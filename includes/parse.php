<?php

/**
 * @since      3.0.0
 * @package    LogicHop
 * @subpackage LogicHop/includes
 */
class LogicHop_Parse {

	/**
	 * @since    3.0.0
	 * @access   protected
	 * @var      object    $logic    Logic object.
	 */
	protected $logic;

	/**
	 * @since    3.0.0
	 * @access   protected
	 * @var      array    $blocks    Conditional blocks.
	 */
	protected $blocks;

	/**
	 * @since    3.2.2
	 * @access   protected
	 * @var      array    $tags    Logic Tags
	 */
	protected $tags;

	/**
	 * Initialize the plugin name.
	 *
	 * @since    3.0.0
	 */
	public function __construct ($logic) {
		$this->logic = $logic;
		$this->blocks = array();
	}

	/**
	 * Parse conditions within {% if condition: ##ID|SLUG|JSON## %} and {% endif %}
	 * Parse query strings within {% if query: ##ID|SLUG|JSON## %} and {% endif %}
	 *
	 * != conditions prefixed with !
	 * Else If conditions with {% else if condition: %} || {% else if query: %}
	 * Else conditions with {% else %}
	 *
	 * @since    3.0.0
	 */
	public function conditions ($content) {

		if ( $this->logic->nested_tags_enabled() ) {
			return $this->processNestedTags( $content );
		}

		$this->extractBlocks($content); // GET RAW BLOCKS

		if ($this->blocks) {

			$preview = $this->logic->is_preview_mode();

			if ( $this->logic->caching_enabled() || $preview ) {
				// CACHED CONTENT? USE JAVASCRIPT --> REBUILD CONDITIONS AS <div>
				foreach ($this->blocks as $block) {
					$html = '';
					$else = array();
					foreach ($block['statements'] as $s) {

						if (!$s['condition']) break;

						$title = '';
						if ($s['type'] == 'id' || $s['type'] == 'slug') {
							$_condition 	= $this->logic->condition_get_json($s['condition'], true);
							$condition 		= json_encode($_condition->rule);
							$title			= $_condition->title;

							if ($preview) {
								if (isset($s['params']['default']) && substr($s['params']['default'], 0, 1) == '!') {
									$title = '!' . $title;
								}
							}

							if (isset($s['operator']) && $s['operator'] === false) {
								$condition = sprintf('{"!=":[%s,true]}', $condition);
							}
							$else[] = $condition;
						} else if ($s['type'] == 'json') {
							$_condition = json_encode($s['condition']);
							$condition 	= sprintf('{"and":[{"!=":[{"and":[%s]},true]},%s]}', implode(',', $else), $_condition);
							$title		= $_condition;
							$else[] 	= $_condition;
						} else if ($s['type'] == 'else') {
							$condition 	= sprintf('{"!=":[{"or":[%s]},true]}', implode(',', $else));
							$title		= 'Else';
						}

						if ($s['type'] != 'endif') {
							$params = '';
							if ($s['params']) {
								foreach ($s['params'] as $k => $v) {
									if ($k != 'default') {
										$params .= sprintf(' data-%s="%s"', $k, $v);
									}
								}
							}

							$html .= sprintf('<%s class="logichop-condition logichop-js %s" %s data-hash="%s" data-condition=\'%s\' %s>%s</%s>',
											'div',
											($preview) ? 'logichop-preview' : '',
											($title && $preview) ? sprintf('data-title="%s"', htmlentities($title, ENT_QUOTES, 'UTF-8')) : '', // ONLY DISPLAY TITLES IN PREVIEW MODE
											md5($condition),
											$condition,
											$params,
											$s['content'],
											'div'
										);
						}
					}

					$content = str_replace( $block['block'], $html, $content);
				}
			} else {
				// UN-CACHED CONTENT? PARSE CONTENT
				foreach ($this->blocks as $block) {
					foreach ($block['statements'] as $s) {
						$statement = false;
						if ($s['type'] == 'id' || $s['type'] == 'slug') {
							$statement = $this->logic->condition_get($s['condition']);
							if (isset($s['operator']) && $s['operator'] === false) {
								$statement = !$statement;
							}
						} else if ($s['type'] == 'json') {
							$statement = $this->logic->logic_apply($s['condition'], $this->logic->session_get());
						} else if ($s['type'] == 'else') {
							$statement = true;
						}

						if ($statement == true) {
							$content = str_replace( $block['block'], $s['content'], $content);
							break;
						} else if ($s['type'] == 'endif') {
							$content = str_replace( $block['block'], '', $content);
						}
					}
				}
			}
		}

		$content = $this->parseVariables($content);

		return $content;
	}

	/**
	 * Process nested tags
	 *
	 * @since    3.2.2
	 */
	public function processNestedTags ( $content ) {

		$preview = $this->logic->is_preview_mode();
		$content = $this->tokenizeTags( $content );

		if ( $this->logic->caching_enabled() || $preview ) {
			$content = $this->replaceNestedTags( $content ); // PARSE WITH JAVASCRIPT
		} else {
			$content = $this->traverseNestedTags( $content ); // PARSE WITH PHP
		}

		$content = $this->parseVariables( $content );

		return $content;
	}

	/**
	 * Add numeric token to each tag for nested parsing
	 *
	 * @since    3.2.2
	 */
	public function tokenizeTags ( $content ) {
		$this->tags = array();
		$count = $block = $level = 0;
		$tokenized_content = preg_replace_callback( '/{%[^}]*%}/', function ( $match ) use ( &$count, &$block, &$level ) {
			if ( strpos( $match[0], '{% if' ) !== false ) {
				$level++; // IF STATEMENT STARTS NEW LEVEL
			}
			$_tag = substr( $match[0], 0, -2 ); // TRIM %}
			$tag = sprintf( '%s%d%%}', $_tag, $count ); // RE-BUILD TAG;
			$this->tags[$block][$level][] = $tag;
			$count++;
			if ( strpos( $match[0], '{% endif' ) !== false ) {
				if ( $level > 1 ) {
					$level--;
				} else {
					$level = 0;
					$block++;
				}
			}
			return $tag;
		}, $content );

		return $tokenized_content;
	}

	/**
	 * Replace nested tags with HTML for Javascript mode
	 *
	 * @since    3.2.2
	 */
	private function replaceNestedTags ( $content ) {

		$preview = $this->logic->is_preview_mode();

  	for ( $b = 0; $b < count( $this->tags ); $b++ ) { // BLOCKS
    	for ( $l = 1; $l <= count( $this->tags[$b] ); $l++ ) { // LEVELS
            $else = array();
            if (is_array($this->tags[$b][$l])) {
                for ($g = 0; $g < count($this->tags[$b][$l]); $g++) { // GROUPS

                    $html = '';

                    $tag = $this->tags[$b][$l][$g];

                    $params = $this->extractReplacementTag($tag);

                    if (!$params || $params['type'] == 'else') {

                        $condition = sprintf('{"!=":[{"or":[%s]},true]}', implode(',', $else));
                        $title = 'Else';

                    } else if ($params['type'] == 'id' || $params['type'] == 'slug') {

                        $_condition = $this->logic->condition_get_json($params['condition'], true);
                        $condition = json_encode($_condition->rule);
                        $title = $_condition->title;

                        if ($preview) {
                            if (isset($params['params']['default']) && substr($params['params']['default'], 0, 1) == '!') {
                                $title = '!' . $title;
                            }
                        }

                        if (isset($params['operator']) && $params['operator'] === false) {
                            $condition = sprintf('{"!=":[%s,true]}', $condition);
                        }

                        $else[] = $condition;

                    } else if ($params['type'] == 'json') {

                        $_condition = json_encode($params['condition']);
                        $condition = sprintf('{"and":[{"!=":[{"and":[%s]},true]},%s]}', implode(',', $else), $_condition);
                        $title = $_condition;
                        $else[] = $_condition;

                    }

                    if (strpos($tag, '{% endif ') !== false) { // ELSE CONDITION

                        $html = '</div>';

                    } else {

                        $data = '';
                        if (is_array($params) && $params['params']) {
                            foreach ($params['params'] as $k => $v) {
                                if ($k != 'default') {
                                    $data .= sprintf(' data-%s="%s"', $k, $v);
                                }
                            }
                        }

                        $html = sprintf('<div class="logichop-condition logichop-js %s" %s data-hash="%s" data-condition=\'%s\' %s>',
                            ($preview) ? 'logichop-preview' : '',
                            ($title && $preview) ? sprintf('data-title="%s"', htmlentities($title, ENT_QUOTES, 'UTF-8')) : '',
                            md5($condition),
                            $condition,
                            $data
                        );

                        if (strpos($tag, '{% if ') === false) {
                            $html = sprintf('</div>%s', $html);
                        }
                    }

                    $content = str_replace($tag, $html, $content);
                }
            }
      }
    }

    return $content;
  }

	/**
	 * Extract Sub-Blocks
	 *
	 * @since    3.0.0
	 */
	public function extractReplacementTag ( $tag ) {

		preg_match( '#{% (if condition:|elseif condition:|if query:|elseif query:|else|if var:|elseif var:) (.+?) (\d+)%}#is', $tag, $control );

		$params = false;

		if ( $control ) {
			if ( strpos( $control[1], 'query:' ) !== false ) {
				$params = $this->extractQuery( $control[2] );
			} else if ( strpos( $control[1], 'var:' ) !== false ) {
				$params = $this->extractVarCondition( $control[2] );
			} else {
				$params = $this->extractCondition( $control[2] );
			}
		}

		return $params;
	}

	/**
	 * Traverse nested tags
	 *
	 * @since    3.2.2
	 */
	public function traverseNestedTags ( $content ) {
		$tags = $this->tags;
		for ( $b = 0; $b < count( $tags ); $b++ ) { // BLOCKS
      for ( $l = 1; $l <= count( $tags[$b] ); $l++ ) { // LEVELS
        $groups = array();
        $_content = '';
        $met = false;
        if (is_array($this->tags[$b][$l])) {
              for ($g = 0; $g < count($tags[$b][$l]); $g++) { // GROUPS
                  $tag = $tags[$b][$l][$g];
                  if (strpos($tag, '{% endif') === false) {
                      $end = $tags[$b][$l][$g + 1];
                      $raw = $this->extractNestedContent($tag, $end, $content);
                      $groups[] = sprintf('%s%s', $tag, $raw);

                      $condition_met = $this->evaluateNestedTag($tag);

                      if (!$met && $condition_met) { // IF CONDITION IS TRUE
                          $met = true;
                          $_content = $raw;
                      }
                      if (!$met && strpos($tag, '{% else ') !== false) { // ELSE CONDITION
                          $_content = $raw;
                      }
                  } else {
                      $groups[] = $tag;
                      $full_block = implode('', $groups);
                      $content = str_replace($full_block, $_content, $content);
                      $groups = array();
                      $_content = '';
                      $switch = true;
                  }
              }
        }
      }
    }
    return $content;
	}

	/**
	 * Extract nested content
	 *
	 * @since    3.2.2
	 */
	public function extractNestedContent ( $start, $stop, $_content ) {
    // if ( strpos( $_content, $start ) === false) { // DOES THIS SAVE TIME??
    //   return '';
    // }
		$extract = explode( $start, $_content );
		if ( isset( $extract[1] ) ) {
			$extract = explode( $stop, $extract[1] );
			return $extract[0];
		}
		return '';
	}

	/**
	 * Extract Sub-Blocks
	 *
	 * @since    3.0.0
	 */
	public function evaluateNestedTag ( $tag ) {

		preg_match( '#{% (if condition:|elseif condition:|if query:|elseif query:|else|if var:|elseif var:) (.+?) (\d+)%}#is', $tag, $control );

		$result = false;

		if ( $control ) {

			if ( strpos( $control[1], 'query:' ) !== false ) {

				$params = $this->extractQuery( $control[2] );
				$result = $this->logic->logic_apply( $params['condition'], $this->logic->session_get() );
			} else if ( strpos( $control[1], 'var:' ) !== false ) {

				$params = $this->extractVarCondition( $control[2] );
				$result = $this->logic->logic_apply( $params['condition'], $this->logic->session_get() );
			} else {

				$params = $this->extractCondition( $control[2] );
				$result = $this->logic->condition_get( $params['condition'] );
				if ( isset( $params['operator'] ) && $params['operator'] === false ) {
					$result = !$result;
				}
			}
			do_action( 'logichop_php_condition_evaluated', $params['condition'], $result );
		}

		return $result;
	}

	/**
	 * Extract Blocks
	 *
	 * @since    3.0.0
	 */
	public function extractBlocks ($the_content) {

		preg_match_all('#{% if (.+?) %}(.+?){% endif %}#is', $the_content, $blocks, PREG_SET_ORDER);

		if ($blocks) {
			foreach ($blocks as $block) {
				if ($statements = $this->extractSubBlocks($block[0])) {

					for ($i = 0; $i < count($statements); $i++) {
						if (isset($statements[$i + 1])) {
							$_content = $this->extractContent($statements[$i]['raw'], $statements[$i + 1]['raw'], $block[0]);
							$statements[$i]['content'] = trim(preg_replace('[^(<br( \/)?>)*|(<br( \/)?>)*$]', '', $_content));
						}
					}

					$this->blocks[] = array (
						'block' => $block[0],
						'statements' => $statements,
					);
				}
			}
		}
	}

	/**
	 * Extract Sub-Blocks
	 *
	 * @since    3.0.0
	 */
	public function extractSubBlocks ($raw_block) {

		preg_match_all('#{% (if condition:|elseif condition:|if query:|elseif query:|else|if var:|elseif var:|else) (.+?) %}#is', $raw_block, $controls, PREG_SET_ORDER);

		if ($controls) {

			$statements = array();

			foreach ($controls as $control) {
				if ($control[1] != 'else') {
					$raw = $control[0];
					if (strstr($control[1], 'query:')) {
						$condition = $this->extractQuery( $control[2] );
					} else if (strstr($control[1], 'var:')) {
						$condition = $this->extractVarCondition( $control[2] );
					} else {
						$condition = $this->extractCondition( $control[2] );
					}
				} else {
					$raw = '{% else %}'; // MANUALLY BUILD else STATEMENT
					$condition = array ( 'condition' => 'else', 'operator' => true, 'type' => 'else' );
				}

				$statements[] = array (
					'raw' => $raw,
					'condition' => $condition['condition'],
					'operator' => $condition['operator'],
					'type' => $condition['type'],
					'params' => (isset($condition['params'])) ? $condition['params'] : array(),
				);
			}

			$statements[] = array ( // MANUALLY ADD endif STATEMENT
					'raw' => '{% endif %}',
					'condition' => 'endif',
					'operator' => true,
					'type' =>  'endif',
					'params' => array(),
				);

			return $statements;
		}

		return false;
	}

	/**
	 * Extract Variable Condition
	 *
	 * @since    3.0.1
	 */
	public function extractVarCondition ($raw_query) {

		$params = $this->extractParams($raw_query);
		if (!isset($params['default'])) return false;
		$delimiter = (strstr($params['default'], '==')) ? '==' : '!=';
		if (strstr($params['default'], ' in ')) $delimiter = 'in';
		if (strstr($params['default'], ' <= ')) $delimiter = '<=';
		if (strstr($params['default'], ' < ')) $delimiter = '<';
		if (strstr($params['default'], ' >= ')) $delimiter = '>=';
		if (strstr($params['default'], ' > ')) $delimiter = '>';

		$query = explode($delimiter, $params['default']);

		$operator = 'eq_i';
		if ($delimiter == '!=') $operator = 'ne_i';
		if ($delimiter == 'in') $operator = 'in_i';
		if ($delimiter == '<') $operator = '<';
		if ($delimiter == '<=') $operator = '<=';
		if ($delimiter == '>') $operator = '>';
		if ($delimiter == '>=') $operator = '>=';

		if (isset($query[0]) && isset($query[1])) {
			$condition = sprintf('{"%s": [ {"var": "%s" }, "%s" ] }',
						$operator,
						trim($query[0]),
						trim($query[1])
					);
		} else {
			$condition = null;
		}

		return array (
			'condition' => json_decode($condition),
			'operator' => true,
			'type' => 'json',
			'params' => $params
		);
	}

	/**
	 * Extract Query
	 *
	 * @since    3.0.0
	 */
	public function extractQuery ($raw_query) {

		$params = $this->extractParams($raw_query);
		if (!isset($params['default'])) return false;

		$delimiter = (strstr($params['default'], '==')) ? '==' : '!=';
		if (strstr($params['default'], ' in ')) $delimiter = 'in';

		$query = explode($delimiter, $params['default']);

		$operator = 'eq_i';
		if ($delimiter == '!=') $operator = 'ne_i';
		if ($delimiter == 'in') $operator = 'in_i';

		if (isset($query[0]) && isset($query[1])) {
			$condition = sprintf('{"%s": [ {"var": "Query.%s" }, "%s" ] }',
						$operator,
						trim($query[0]),
						trim($query[1])
					);
		} else {
			$condition = null;
		}

		return array (
			'condition' => json_decode($condition),
			'operator' => true,
			'type' => 'json',
			'params' => $params
		);
	}

	/**
	 * Extract Conditions
	 *
	 * @since    3.0.0
	 */
	public function extractCondition ($raw_condition) {

		$params = $this->extractParams($raw_condition);
		if (!isset($params['default'])) return false;

		$operator = (substr($params['default'], 0, 1) == '!') ? false : true;
		$condition = ($operator) ? $params['default'] : substr($params['default'], 1);

		if ((int) $condition > 0) {
			$type = 'id';
		} else if (strstr($condition, '{')) {
			$type = 'json';
			$condition = str_replace(array('&#8220;','&#8221;'), '"', $condition);
			$condition = json_decode($condition);
		} else {
			$type = 'slug';
			$condition = strtolower($condition);
		}

		return array (
			'condition' => $condition,
			'operator' => $operator,
			'type' => $type,
			'params' => $params
		);
	}

	/**
	 * Extract Parameters
	 *
	 * @since    3.0.0
	 */
	public function extractParams ($condition) {

		$raw = explode('|', trim($condition));
		$params = array();

		if (isset($raw[0])) {
			$params['default'] = $raw[0];

			foreach ($raw as $r) {
				$tmp = explode(': ', $r);
				if (count($tmp) == 2) {
					$params[trim(strtolower($tmp[0]))] = trim($tmp[1]);
				}
			}
		}

		return $params;
	}

	/**
	 * Extract Content
	 *
	 * @since    3.0.0
	 */
	public function extractContent ($start, $stop, $_content) {
		$extract = explode($start, $_content);
		if (isset($extract[1])){
			$extract = explode($stop, $extract[1]);
			return $extract[0];
		}
		return '';
	}

	/**
	 * Parse Variables
	 *
	 * Parse variables within {{ var: ##VAR## }} and {{ goal: ##GOAL ID## }}
	 *
	 * Options separated by |
	 * text: lower|upper|words|first
	 * element: any HTML element – default <div></div>
	 * condition: ID or Slug - For goal option
	 * default: ##VALUE##
	 *
	 * @since    3.0.0
	 */
	public function parseVariables ($content) {

		preg_match_all('#{{([^}}]*)}}#is', $content, $matches);

		if (count($matches[0]) > 0) {

			$preview = $this->logic->is_preview_mode();

			for ($i = 0; $i < count($matches[0]); $i++) {

				$tag = $matches[0][$i];
				$raw = explode('|', trim($matches[1][$i]));
				$data = array();

				$condition = false;
				$condition_not = false;
				$delete = false;

				foreach ($raw as $r) {
					$tmp = explode(': ', $r);
					if (count($tmp) == 2) {
						$data[trim(strtolower($tmp[0]))] = trim($tmp[1]);
						if (key_exists('query', $data)) {
							$data['var'] = 'Query:' . $data['query'];
						}

						if (key_exists('condition', $data)) {
							$condition = $this->extractCondition($data['condition']);
						}

						if (key_exists('delete', $data)) {
							$delete = true;
						}
					}
				}

				if (isset($data['var'])) {

					if ($this->logic->caching_enabled() || $preview) {
						$params = '';

						if ($condition) {
							$json = json_encode($this->logic->condition_get_json($condition['condition']));
							if (isset($condition['operator']) && $condition['operator'] === false) {
								$json = sprintf('{"!=":[%s,true]}', $json);
							}

							$params = sprintf('style="display: none;" data-hash="%s" data-condition=\'%s\'',
												md5($json),
												$json
											);
						}

						$element = (key_exists('element', $data)) ? $data['element'] : 'span';
						$replace =  sprintf('<%s class="logichop-variable logichop-js %s" data-var="%s" %s %s %s %s %s %s %s>%s</%s>',
												$element,
												(key_exists('class', $data)) ? $data['class'] : '',
												$data['var'],
												$params,
												(key_exists('case', $data)) ? sprintf('data-case="%s"', $data['case']) : '',
												(key_exists('type', $data)) ? sprintf('data-type="%s"', $data['type']) : '',
												(key_exists('event', $data)) ? sprintf('data-event="%s"', $data['event']) : '',
												(key_exists('spaces', $data)) ? sprintf('data-spaces="%s"', $data['spaces']) : '',
												(key_exists('prepend', $data)) ? sprintf('data-prepend="%s"', $data['prepend']) : '',
												(key_exists('append', $data)) ? sprintf('data-append="%s"', $data['append']) : '',
												(key_exists('default', $data)) ? $data['default'] : '',
												$element
											);

					} else {

						$display = true;
						$replace = '';

						if ($condition) {
							$display = $this->logic->condition_get($condition['condition']);
							if ($condition['operator'] == false) $display = !$display;
						}

						if ($display) {
							$val = $this->logic->data_return($data['var']);
							if ( $val !== false && $val !== null ) {
								$prepend = (key_exists('prepend', $data)) ? $data['prepend'] : '';
								$append = (key_exists('append', $data)) ? $data['append'] : '';

								if (key_exists('append', $data)) {
									$replace .= $val . $data['case'];
								}

								if (key_exists('case', $data)) {
									$replace = $this->textf($val, $data['case']);
								} else {
									$replace = $val;
								}

								$replace = sprintf('%s%s%s',
												$prepend,
												$replace,
												$append
											);

							} else {
								if (key_exists('default', $data)) {
									$replace = $data['default'];
								}
							}
						}
					}
					$content = str_replace($tag, $replace, $content);
				}

				if (isset($data['goal'])) {
					if ($this->logic->caching_enabled()) {
						$replace = sprintf('<input type="hidden" class="logichop-goal" value="%s" %s %s %s>',
											$data['goal'],
											($condition) ? sprintf('data-condition="%s"', $condition['condition']) : '',
											($condition && $condition['operator'] == false) ? 'data-not="true"' : '',
											($delete) ? 'data-delete="true"' : ''
										);
					} else {
						$replace = sprintf("<script>logichop_goal%s('%s'%s%s);</script>",
											($delete) ? '_delete' : '',
											$data['goal'],
											($condition) ? sprintf(",'%s'", $condition['condition']) : '',
											($condition && $condition['operator'] == false) ? ',true' : ''
										);
					}
					$content = str_replace($tag, $replace, $content);
				}
			}
		}

		return $content;
	}

	/**
	 * Parse URL Variables
	 *
	 * Parse variables within {{ var: ##VAR## }} for URL use
	 *
	 * Options separated by |
	 * text: lower|upper|words|first
	 * default: ##VALUE##
	 *
	 * @since    3.3.5
	 */
	public function parseUrlVariables ($content) {

		preg_match_all('#{{([^}}]*)}}#is', $content, $matches);

		if (count($matches[0]) > 0) {

			for ($i = 0; $i < count($matches[0]); $i++) {

				$tag = $matches[0][$i];
				$raw = explode('|', trim($matches[1][$i]));
				$data = array();

				foreach ($raw as $r) {
					$tmp = explode(': ', $r);
					if (count($tmp) == 2) {
						$data[trim(strtolower($tmp[0]))] = trim($tmp[1]);
					}
				}

				if (isset($data['var'])) {
					$replace = '';

					$val = $this->logic->data_return($data['var']);
					if ( $val !== false && $val !== null ) {
						$prepend = (key_exists('prepend', $data)) ? $data['prepend'] : '';
						$append = (key_exists('append', $data)) ? $data['append'] : '';

						if (key_exists('append', $data)) {
							$replace .= $val . $data['case'];
						}

						if (key_exists('case', $data)) {
							$replace = $this->textf($val, $data['case']);
						} else {
							$replace = $val;
						}

						$replace = sprintf('%s%s%s',
										$prepend,
										$replace,
										$append
									);

					} else {
						if (key_exists('default', $data)) {
							$replace = $data['default'];
						}
					}

					$content = str_replace($tag, $replace, $content);
				}
			}
		}

		return str_replace( '&amp;', '&', $content );
	}

	/**
	 * Format text
	 *
	 * @since    3.0.0
	 */
	public function textf ($text, $case) {
		if ($case == 'lower') return strtolower($text);
		if ($case == 'upper') return strtoupper($text);
		if ($case == 'words') return ucwords($text);
		if ($case == 'first') return ucfirst($text);
		return $text;
	}
}
