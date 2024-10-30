<?php

/**
 * Public-specific functionality.
 *
 * @since      1.0.0
 * @package    LogicHop
 * @subpackage LogicHop/includes
 */
class LogicHop_Public {

	/**
	 * The class that's responsible for core functionality & logic
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      LogicHop_Test    $logic    Core functionality & logic.
	 */
	private $logic;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Array of active conditional CSS classes
	 *
	 * @since    2.1.0
	 * @access   public
	 * @var      array    $css    Array of active conditional CSS classes
	 */
	public $css;

	/**
	 * Array of conditional CSS classes
	 *
	 * @since    3.0.0
	 * @access   public
	 * @var      array    $css_preview    Array of conditional CSS classes
	 */
	public $css_preview;
    /**
     * @var LogicHop_Parse
     */
    private $parse;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    		The version of this plugin.
	 */
	public function __construct( $logic, $plugin_name, $version ) {
		$this->logic = $logic;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->css = array();
		$this->css_preview = array();
		$this->parse = new LogicHop_Parse($this->logic);
	}

	/**
	 * Javascript Goal logging.
	 *
	 * @since    1.0.0
	 */

	public function logichop_goal () {
		if ($this->logic->is_valid_referrer()) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

				$goal = (isset($_POST['goal'])) ? $_POST['goal'] : false;
				$condition = (isset($_POST['condition'])) ? $_POST['condition'] : false;
				$condition_not = (isset($_POST['condition_not'])) ? true : false;
				$delete_goal = (isset($_POST['delete_goal'])) ? true : false;

				if ($goal) {
					if ($condition) {
						$condition_met = $this->logic->condition_get($condition);
						if ($condition_not) $condition_met = !$condition_met;

						if ($condition_met) {
							$this->logic->update_goal($goal, $delete_goal);
						}
					} else {
						$this->logic->update_goal($goal, $delete_goal);
					}
				}
			}
		}
		wp_die();
	}

	/**
	 * Javascript Page tracking.
	 * Available only when js_tracking setting enabled.
	 *
	 * DEPRECATED SINCE 3.0.0
	 *
	 * @since    1.0.0
	 */
	public function logichop_page_view () {
		if ($this->logic->is_valid_referrer()) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$pid = isset($_POST['pid']) ? (int) $_POST['pid'] : false;
				$gid = isset($_POST['gid']) ? (int) $_POST['gid'] : false;
				$la = isset($_POST['la']) ? (int) $_POST['la'] : false;
				$lf = isset($_POST['lf']) ? $_POST['lf'] : 'every';
				$cid = isset($_POST['cid']) ? $_POST['cid'] : false;	// CONDITION
				$gcid = isset($_POST['gcid']) ? (int) $_POST['gcid'] : false;
				$rcid = isset($_POST['rcid']) ? $_POST['rcid'] : false;	// CONDITION
				$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : false;
				$referrer = isset($_POST['referrer']) ? $_POST['referrer'] : false;

				$response = new stdclass;
				$response->success = true;
				$response->redirect = false;
				if ($pid) $this->logic->update_data($pid, $referrer); // UPDATE PAGE
				if ($gid) $this->logic->update_goal($gid); // UPDATE GOAL
				if ($pid && $la && $lf) $this->logic->validate_lead_score($pid, $la, $lf); // UPDATE LEAD SCORE
				if ($cid && $gcid && $this->logic->condition_get($cid)) $this->logic->update_goal($gcid); // UPDATE CONDITIONAL GOAL
				if ($rcid && $redirect && $this->logic->condition_get($rcid) && apply_filters( 'logichop_do_redirect', true, $pid, $cid )) {
					$response->redirect = $redirect; // RETURN REDIRECT
				}

				if ( $pid !== false ) {
					if ( $logichop_redirect = $this->is_redirected( $pid ) ) {
						$response->redirect = $logichop_redirect->url;
					}
				}

				echo json_encode($response);
			}
		}
		wp_die();
	}

	/**
	 * Javascript Condition evaluation.
	 *
	 * @since    1.0.4
	 */
	public function logichop_condition () {
		if ($this->logic->is_valid_referrer()) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$cid = isset($_POST['cid']) ? $_POST['cid'] : false;

				$response = new stdclass;
				$response->success = true;
				$response->cid = $cid;
				$response->condition = false;

				if ($cid && $this->logic->condition_get($cid)) {
					$response->condition = true;
				}
				echo json_encode($response);
			}
		}
		wp_die();
	}

	/**
	 * Javascript data variable return.
	 *
	 * @since    1.0.4
	 */
	public function logichop_data () {
		if ($this->logic->is_valid_referrer()) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$data_var = isset($_POST['data_var']) ? $_POST['data_var'] : false;

				$response 			= new stdclass;
				$response->success 	= false;

				if ($this->logic->js_variable_display()) {
					if (!$this->logic->is_variable_disabled($data_var)) {
						$response->success 	= true;
						$response->data_var = $data_var;
						$response->value 	= $this->logic->data_return($data_var);
					}
				}
				echo json_encode($response);
			}
		}
		wp_die();
	}

	/**
	 * Javascript data variable return.
	 * Returns all Logic Hop data.
	 * Only when debug mode enabled.
	 *
	 * @since    3.1.9
	 */
	public function logichop_data_debug () {
		if ($this->logic->is_valid_referrer()) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$response = false;
				if ( $this->logic->get_option( 'debug_mode', false ) ) {
					$response = $this->logic->data_factory->get_data();
				}
				echo json_encode( $response );
			}
		}
		wp_die();
	}

	/**
	 * WordPress user logged in
	 *
	 * @since    2.1.5
	 * @param    string		$user_login		$user->user_login
	 * @param    objedt		$user			WP_User object
	 */
	public function wp_user_login ($user_login, $user) {
		$this->logic->wp_user_data_set($user);
	}

	/**
	 * WordPress user logged out
	 *
	 * @since    2.1.5
	 */
	public function wp_user_logout () {
		$this->logic->wp_user_data_set(false, true);
	}

	/**
	 * Javascript parse Logic Hop data
	 *
	 * @since    1.5.0
	 */
	public function logichop_parse_logic () {
		if ($this->logic->is_valid_referrer()) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$data = isset($_POST['data']) ? $_POST['data'] : array();

				$response = new stdclass;
				$response->success 			= false;
				$response->conditions 		= array();
				$response->conditions_json 	= array();
				$response->variables 		= array();
				$response->redirect 		= false;
				$response->header_css 		= '';
				$response->css 				= '';

				if ( $this->logic->js_tracking() ) {
					$pid = isset($data['pid']) ? (int) $data['pid'] : false;
					$gid = isset($data['gid']) ? (int) $data['gid'] : false;
					$la = isset($data['la']) ? (int) $data['la'] : false;
					$lf = isset($data['lf']) ? $data['lf'] : 'every';
					$cid = isset($data['cid']) ? $data['cid'] : false;	// CONDITION
					$gcid = isset($data['gcid']) ? (int) $data['gcid'] : false;
					$rcid = isset($data['rcid']) ? $data['rcid'] : false;	// CONDITION
					$redirect = isset($data['redirect']) ? $data['redirect'] : false;
					$referrer = isset($data['referrer']) ? $data['referrer'] : false;

					if ( $pid !== false ) {
						$this->logic->update_data( $pid, $referrer); // UPDATE PAGE
						if ( isset( $_POST['URL'] ) ) {
							$this->logic->update_stored_url( $_POST['URL'] );
						}
						if ( isset( $_POST['payload'] ) ) {
							do_action( 'logichop_parse_payload', $_POST['payload'] );
						}
					}

					if ($gid) $this->logic->update_goal($gid); // UPDATE GOAL
					if ($pid && $la !== false && $lf) $this->logic->validate_lead_score($pid, $la, $lf); // UPDATE LEAD SCORE
					if ($cid && $gcid && $this->logic->condition_get($cid)) $this->logic->update_goal($gcid); // UPDATE CONDITIONAL GOAL
					if ($rcid && $redirect && $this->logic->condition_get($rcid) && apply_filters( 'logichop_do_redirect', true, $pid, $rcid )) $response->redirect = $redirect; // RETURN REDIRECT
					$response->success = true;

					$response->cookie = $this->logic->cookie_retrieve();

					if ( $pid ) {
						if ( $logichop_redirect = $this->is_redirected( $pid ) ) {
							$response->redirect = $logichop_redirect->url;
							// REDIRECTING NO NEED TO DO ANY MORE WORK
							echo json_encode($response);
							wp_die();
						}
					}
				}

				$condition_met = false;

				if ( isset($data['conditions_json']) ) { // EVALUATE JSON CONDITIONS
					$response->success = true;
					foreach ($data['conditions_json'] as $json) {
						$json = stripslashes($json);
						if ($json != 'false') {
							$tmp = new stdclass();
							$tmp->hash 		= md5($json);
							$tmp->condition	= $this->logic->logic_apply(json_decode($json), $this->logic->session_get());

							if ($tmp->condition) {
								$response->conditions_json[] = $tmp;
								$condition_met = true;
							}
						}
					}
				}

				if ( isset($data['conditions']) ) { // EVALUATE CONDITIONS
					$response->success = true;
					foreach ($data['conditions'] as $cid) {
						if ($cid != 'no_conditions' & $cid != 'has_conditions') {
							$tmp = new stdclass();
							$tmp->cid 				= $cid;
							$tmp->condition 		= ($this->logic->condition_get($cid)) ? true : false;
							$response->conditions[] = $tmp;
							if ($tmp->condition) $condition_met = true;
						}
					}
				}

				if ($condition_met) {
					$tmp = new stdclass();
					$tmp->cid 				= 'has_conditions';
					$tmp->condition 		= true;
					$response->conditions[] = $tmp;
					$tmp = new stdclass();
					$tmp->cid 				= 'no_conditions';
					$tmp->condition 		= false;
					$response->conditions[] = $tmp;
				} else {
					$tmp = new stdclass();
					$tmp->cid 				= 'has_conditions';
					$tmp->condition 		= false;
					$response->conditions[] = $tmp;
					$tmp = new stdclass();
					$tmp->cid 				= 'no_conditions';
					$tmp->condition 		= true;
					$response->conditions[] = $tmp;
				}

				if (!$this->logic->disable_conditional_css()) {
					$_preview = ( isset($data['css_preview']) ) ? true : false;
					$response->header_css = $this->generate_conditional_css( $_preview );
					$response->css = implode(' ', $this->css);

					if ( $_preview ) $response->css_preview = $this->css_preview;
				}

				if ( $this->logic->js_variable_display() ) { // DATA RETURN ENABLED?
					if ( isset($data['variables']) ) { // RETRIEVE VARIABLE DATA
						foreach ($data['variables'] as $data_var) {
							if (!$this->logic->is_variable_disabled($data_var)) {
								$tmp = new stdclass();
								$tmp->data_var 			= $data_var;
								$tmp->value 			= $this->logic->data_return($data_var);
								$response->variables[] 	= $tmp;
								$response->success 		= true;
							}
						}
					}
				}

				echo json_encode($response);
			}
		}
		wp_die();
	}

	/**
	 * Catch widget and push through widget_redirected_callback
	 *
	 * @since    1.0.0
	 * @param    array		$params		Widget parameters
	 * @return    array		processed widget parameters
	 */
	public function widget_display_callback ($params) {
		global $wp_registered_widgets;

		$id = $params[0]['widget_id'];
		$wp_registered_widgets[$id]['callback_wl_redirect'] = $wp_registered_widgets[$id]['callback'];
		$wp_registered_widgets[$id]['callback'] = array($this, 'widget_redirected_callback');
		return $params;
	}

	/**
	 * Filter widget content
	 *
	 * QUESTION: IS 'widget_' PREFIX A CONSTANT WHEN SETTING $widget_name
	 *
	 * @since    	1.0.0
	 * @return		false or string		Echos widget content
	 */
	public function widget_redirected_callback () {
		global $wp_registered_widgets, $wp_reset_query_is_done;

		$params = func_get_args();
		$id = $params[0]['widget_id'];

		$widget_name = 'widget_' . substr($id, 0, strrpos($id, '-', -1));

		$widget_settings = get_option($widget_name);

		$condition_id = (isset($widget_settings[$params[1]['number']]['logichop_widget'])) ? $widget_settings[$params[1]['number']]['logichop_widget'] : 0;
		$condition_not = (isset($widget_settings[$params[1]['number']]['logichop_widget_not'])) ? (boolean) $widget_settings[$params[1]['number']]['logichop_widget_not'] : false;

		if (!$this->logic->js_tracking()) {
			// JAVASCRIPT TRACKING IS DISABLED
			if ($condition_id) {
				$display_widget = $this->logic->condition_get($condition_id);
				if ($condition_not) $display_widget = !$display_widget;

				if (!$display_widget) return false;
			}

			$callback = $wp_registered_widgets[$id]['callback_wl_redirect'];
			$wp_registered_widgets[$id]['callback'] = $callback;

			if (is_callable($callback)) {
				ob_start();
					call_user_func_array($callback, $params);
					$widget_content = ob_get_contents();
				ob_end_clean();
				echo $widget_content;
			}
		} else {
			 // JAVASCRIPT TRACKING IS ENABLED --> JS POST-LOAD EVALUATE WIDGETS IF CONDITION SET
			$callback = $wp_registered_widgets[$id]['callback_wl_redirect'];
			$wp_registered_widgets[$id]['callback'] = $callback;

			if (is_callable($callback)) {
				ob_start();
					if ($condition_id) {
						printf('<span class="logichop-widget logichop-js" data-cid="%d" %s data-event="%s">',
									$condition_id,
									$condition_not ? 'data-not="true"' : '',
									'fadeIn'
								);
					}
						call_user_func_array($callback, $params);
					if ($condition_id) echo '</span>';
					$widget_content = ob_get_contents();
				ob_end_clean();
				echo $widget_content;
			}
		}
	}

	/**
	 * Register shortcodes
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes () {
		add_shortcode( 'logichop_block', array($this, 'shortcode_logic_block') );
		add_shortcode( 'logichop_condition', array($this, 'shortcode_conditional_display') );
		add_shortcode( 'logichop_condition_not', array($this, 'shortcode_conditional_not_display') );
		add_shortcode( 'logichop_goal', array($this, 'shortcode_goal_embed') );
		add_shortcode( 'logichop_conditional_goal', array($this, 'shortcode_conditional_goal_embed') );
		add_shortcode( 'logichop_conditional_redirect', array($this, 'shortcode_conditional_redirect') );
		add_shortcode( 'logichop_data', array($this, 'shortcode_logichop_data_display') );
		add_shortcode( 'logichop_data_input', array($this, 'shortcode_logichop_data_input_display') );
		add_shortcode( 'logichop_embed', array($this, 'shortcode_logichop_embed') );

		do_action('logichop_register_shortcodes', $this);
	}

	/**
	 * Render Smart Content
	 *
	 * @since    3.1.0
	 * @param  		array	$atts		Shortcode attributes
	 * @return  	null or content_filter()		Formatted shortcode content
	 */
	public function shortcode_logic_block ( $atts = null ) {
		$id = ( isset( $atts['id'] ) ) ? $atts['id'] : null;
		if ( ! $id ) return;

		$block = get_page_by_path( $id, 'OBJECT', 'logichop-logicblocks' );
		if ( ! $block ) return;

		$content = $this->content_filter( do_shortcode( $block->post_excerpt ) );

		$values = get_post_custom( $block->ID );
		$javascript = ( isset( $values ) && isset( $values['logichop_logicblock_javascript'][0] ) ) ? $values['logichop_logicblock_javascript'][0] : '';
		if ( $javascript ) {
			$content .= sprintf( '<script>%s</script>', $javascript );
		}

		return $content;
	}

	/**
	 * Conditional content display shortcode - If condition met
	 *
	 * @since    1.0.0
	 * @param  		array	$atts		Shortcode attributes
	 * @param  		string	$content	Shortcode content
	 * @return  	null or do_shortcode()		Formatted shortcode content
	 */
	public function shortcode_conditional_display ($atts = null, $content = null) {
		$condition_id = (isset($atts['id'])) ? $atts['id'] : null;
		if ($this->logic->condition_get($condition_id)) return do_shortcode($content);
		return;
	}

	/**
	 * Conditional content display shortcode - If condition not met
	 *
	 * @since    1.0.0
	 * @param  		array	$atts		Shortcode attributes
	 * @param  		string	$content	Shortcode content
	 * @return  	null or do_shortcode()		Formatted shortcode content
	 */
	public function shortcode_conditional_not_display ($atts = null, $content = null) {
		$condition_id = (isset($atts['id'])) ? $atts['id'] : null;
		if (!$this->logic->condition_get($condition_id)) return do_shortcode($content);
		return;
	}

	/**
	 * Goal shortcode
	 * Embeds Goal Javascript
	 * Javascript option instead of using page-level logic
	 *
	 * @since    1.0.0
	 * @param  		array	$atts		Shortcode attributes
	 * @param  		string	$content	Shortcode content
	 * @return   null
	 */
	public function shortcode_goal_embed ($atts = null, $content = null) {

		$goal_id = (isset($atts['goal'])) ? (int) $atts['goal'] : null;
		if ($goal_id) {
			printf('<script>logichop_goal(%d);</script>', $goal_id);
		}
		return;
	}

	/**
	 * Conditional Goal shortcode
	 * Embeds Goal Javascript based on the outcome of a condition
	 * Javascript option instead of using page-level logic
	 *
	 * @since    	1.0.0
	 * @param  		array	$atts		Shortcode attributes
	 * @param  		string	$content	Shortcode content
	 * @return    	null
	 */
	public function shortcode_conditional_goal_embed ($atts = null, $content = null) {

		$goal_id = (isset($atts['goal'])) ? (int) $atts['goal'] : null;
		$condition_id = (isset($atts['id'])) ? $atts['id'] : null;

		if ($goal_id && $this->logic->condition_get($condition_id)) {
			printf('<script>logichop_goal(%d);</script>', $goal_id);
		}
		return;
	}

	/**
	 * Conditional redirect shortcode
	 *
	 * @since    	1.0.0
	 * @param  		array	$atts		Shortcode attributes
	 * @param  		string	$content	Shortcode content
	 * @return    	null or redirect	Based on outcome of conditional logic
	 */
	public function shortcode_conditional_redirect ($atts = null, $content = null) {

		$condition_id = (isset($atts['id'])) ? $atts['id'] : null;
		$redirect = (isset($atts['redirect'])) ? $atts['redirect'] : null;

		if ($redirect && $this->logic->condition_get($condition_id) && apply_filters( 'logichop_do_redirect_via_shortcode', true, $condition_id, $redirect )) {
			wp_redirect($redirect, 302);
			exit;
		}
		return;
	}

	/**
	 * Logic Hop data display shortcode
	 *
	 * Accepts [logichop_data vars=""]
	 * Parameter 'vars' accepts '.' delimited object elements and ':' delimited array elements
	 * Example: Date.DateTime OR QueryStore:ref
	 *
	 * @since    	1.0.9
	 * @param  		array	$atts		Shortcode attributes
	 * @return  	null or content		Formatted shortcode content
	 */
	public function shortcode_logichop_data_display ($atts = null) {
		$var = (isset($atts['var'])) ? $atts['var'] : null;
		$case = (isset($atts['case'])) ? $atts['case'] : '';
		$spaces = (isset($atts['spaces'])) ? $atts['spaces'] : false;
		if ($var) {
			$data = $this->logic->data_return($var);
			if ($case == 'lower') $data = strtolower($data);
			if ($case == 'upper') $data = strtoupper($data);
			if ($spaces) $data = str_replace(' ', $spaces, $data);
			return $data;
		}
		return;
	}

	/**
	 * Logic Hop data display <input> shortcode
	 *
	 * Accepts [logichop_data_input vars="" type="" id="" class=""]
	 * Parameter 'vars' accepts '.' delimited object elements and ':' delimited array elements
	 * Example: Date.DateTime OR QueryStore:ref
	 *
	 * @since    	1.5.0
	 * @param  		array	$atts		Shortcode attributes
	 * @return  	null or content		Formatted shortcode content
	 */
	public function shortcode_logichop_data_input_display ($atts = null) {
		$var 	= (isset($atts['var'])) ? $atts['var'] : null;
		$type 	= (isset($atts['type'])) ? $atts['type'] : 'text';
		$id 	= (isset($atts['id'])) ? $atts['id'] : '';
		$name 	= (isset($atts['name'])) ? $atts['name'] : '';
		$class 	= (isset($atts['class'])) ? $atts['class'] : '';

		if ($var) {
			$input = sprintf('<input id="%s" name="%s" class="%s" type="%s" value="%s">',
								$id,
								$name,
								$class,
								$type,
								$this->logic->data_return($var)
							);
			return $input;
		}
		return;
	}

	/**
	 * Process embed or shortcode content as content – Useful for dynamic embeds & shortcodes
	 *
	 * @since    3.2.4
	 * @param  		array	$a		Shortcode attributes
	 * @param  		string	$content	Shortcode content
	 * @return  	null content processed by the_content filter
	 */
	public function shortcode_logichop_embed ( $a = null, $content = null ) {
		$process = $content;
		if ( isset( $a['shortcode'] ) ) {
			$process = '[' . $a['shortcode'];
			foreach ( $a as $k => $v ) {
				if ( $k != 'shortcode' ) {
					$process .= sprintf( ' %s="%s"', $k, $v );
				}
			}
			$process .= ']';
			if ( $content ) {
				$process .= sprintf( '%s[/%s]', $content, $a['shortcode'] );
			}
		}
		return apply_filters( 'the_content', sprintf( '%s', $process ) );
	}

	/**
	 * Parse Conditions, Goals & redirects prior to template load
	 *
	 * @since    1.0.0
	 */
	public function template_level_parsing () {

		$post_id = get_the_id();

		if ( $this->logic->js_tracking() ) { // ONLY CHECK FOR JS DISABLE IF JS TRACKING IS SET
			$disable_js = ( boolean ) get_post_meta( $post_id, '_logichop_disable_js_mode', true );

			if ( $disable_js ) {
				$this->logic->js_mode_disable();
			}
		}

		if ( ! $this->logic->js_tracking() ) { // ONLY PARSE IF JAVASCRIPT TRACKING IS DISABLED
			//$post_id = get_the_id();

			// UPDATE USER DATA
			$this->logic->update_data();

			// LEAD SCORE
			$lead_adjust = (int) get_post_meta($post_id, '_logichop_page_leadscore', true);
			$lead_freq = get_post_meta($post_id, '_logichop_page_lead_freq', true);
			if ( is_int( $lead_adjust ) && $lead_freq ) $this->logic->validate_lead_score( $post_id, $lead_adjust, $lead_freq );

			// GOAL
			$goal_id = (int) get_post_meta($post_id, '_logichop_page_goal', true);
			if ($goal_id) $this->logic->update_goal($goal_id);

			// CONDITIONAL GOAL
			$condition_id 	= get_post_meta($post_id, '_logichop_page_goal_condition', true);
			$goal_id 		= (int) get_post_meta($post_id, '_logichop_page_goal_on_condition', true);
			$condition_not	= (boolean) get_post_meta($post_id, '_logichop_page_goal_condition_not', true);

			if ($goal_id && $condition_id) {

				$do_goal = $this->logic->condition_get($condition_id);
				if ($condition_not) $do_goal = !$do_goal;

				if ($do_goal) $this->logic->update_goal($goal_id);
			}

			// LOGIC HOP REDIRECTS -- PREFERRED
			$redirect = $this->is_redirected( $post_id );
			if ( $redirect ) {
				wp_redirect( $redirect->url, $redirect->type );
				exit();
			}

			// PAGE TOOL REDIRECT -- DEPRECATED
			$condition_id 	= get_post_meta($post_id, '_logichop_page_condition', true);
			$condition_not	= (boolean) get_post_meta($post_id, '_logichop_page_condition_not', true);

			if ($condition_id) {
				$do_redirect = $this->logic->condition_get($condition_id);
				if ($condition_not) $do_redirect = !$do_redirect;

				$do_redirect = apply_filters( 'logichop_do_redirect', $do_redirect, $post_id, $condition_id );

				if ($do_redirect) {
					$redirect = get_post_meta($post_id, '_logichop_page_redirect', true);
					if ($redirect) {
						wp_redirect($redirect);
						exit();
					}
				}
			}
		}
  }

	/**
	 * Parse redirects
	 *
	 * @since    3.1.7
	 * @param		int		$id post id
	 * @return		object 	$redirect
	 */
	public function is_redirected ( $id ) {
		$redirects = $this->logic->redirects_get( $id );
		if ( $redirects ) {
			foreach ( $redirects as $redirect ) {
				$values = get_post_custom($redirect->ID);
				$condition = isset( $values['logichop_redirect_condition'] ) ? esc_attr( $values['logichop_redirect_condition'][0] ) : false;
				$condition_not = isset( $values['logichop_redirect_condition_not'] ) ? esc_attr( $values['logichop_redirect_condition_not'][0] ) : 'met';
				$url = isset( $values['logichop_redirect_url'] ) ? esc_attr( $values['logichop_redirect_url'][0] ) : false;
				$type	= isset( $values['logichop_redirect_type'] ) ? esc_attr( $values['logichop_redirect_type'][0] ) : 307;

				if ( $condition && $url ) {
					$do_redirect = $this->logic->condition_get( $condition );
					if ( $condition_not == 'not_met' ) $do_redirect = !$do_redirect;

					$do_redirect = apply_filters( 'logichop_do_redirect', $do_redirect, $id );

					if ( $do_redirect ) {
						$response = new stdclass();
						$response->url = $this->parse->parseUrlVariables( $url );
						$response->type = $type;
						return $response;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Process template tags in WordPress content
	 *
	 * @since    3.0.0
	 */
	function content_filter ( $content ) {

		$_content = apply_filters( 'logichop_content_filter', $content );

		$_content = $this->parse->conditions( $_content );

		return $_content;
	}

	/**
	 * Add CSS classes to <body>
	 * .logichop-page-views-#
	 * And Conditional CSS
	 *
	 * @since    1.0.8
	 */
	public function body_class_insertion ($classes) {
		if ( ! $this->logic->js_tracking() ) { // ONLY PARSE IF JAVASCRIPT TRACKING IS DISABLED
			$views 		= ($this->logic->session_get_var('Views')) ? $this->logic->session_get_var('Views') : 0;
			$classes[] 	= sprintf('logichop-views-%d', $views);

			if ($this->css) {
				foreach ($this->css as $css) {
					$classes[] = $css;
				}
			}
		} else {
			if ( $this->logic->js_anti_flicker ) {
				$bypass = apply_filters( 'logichop_anti_flicker_css', false );
				if ( ! $bypass ) {
					$classes[] = 'logichop-render-hide';
				}
			}
		}
		return $classes;
	}

	/**
	 * Conditional CSS Preview
	 *
	 * Build conditional CSS preview objects and add to $css_preview
	 *
	 * @since    3.0.0
	 * @param	$class	string	Class name
	 * @param	$active	boolean	Class active or inactive
	 */
	public function logichop_add_preview_css ($class, $active = false) {
		$css = new stdclass();
		$css->class = $class;
		$css->active = $active;

		$this->css_preview[] = $css;
	}

	/**
	 * Generate CSS based on Conditions
	 *
	 * @since    1.5.0
	 */
	public function generate_conditional_css ($preview = false) {

		$this->logic->get_referrer_query_string( false );

		$args = array(
			'numberposts' => -1,
			'post_type' => $this->plugin_name . '-conditions',
			'meta_query' => array(
					array(
            			'key' => 'logichop_css_condition',
            			'value' => true,
            			'compare' => 'LIKE'
        			)
        		)
			);
		$posts = get_posts($args);

		$css_conditions = '';

		if ($posts) {
			foreach ($posts as $p) {
				$rule 	= json_decode($p->post_excerpt, true);
				$result = $this->logic->logic_apply($rule, $this->logic->session_get());
				$tmp 	= new stdclass();

				if ($result) { // ACTIVE CONDITION
					$this->css[] = sprintf('lh-%s', $p->post_name);
					$css_conditions .= sprintf('.logichop-%s { display: block !important; } ', $p->post_name);
					$css_conditions .= sprintf('.logichop-not-%s { display: none !important; } ', $p->post_name);

					if ( $preview ) {
						$this->logichop_add_preview_css( sprintf('body.lh-%s', 	$p->post_name), true );
						$this->logichop_add_preview_css( sprintf('logichop-%s', $p->post_name), true );
						$this->logichop_add_preview_css( sprintf('logichop-not-%s', $p->post_name), false );
					}
				} else { // INACTIVE CONDITION
					$css_conditions .= sprintf('.logichop-%s { display: none !important; } ', $p->post_name);
					$css_conditions .= sprintf('.logichop-not-%s { display: block !important; } ', $p->post_name);

					if ( $preview ) {
						$this->logichop_add_preview_css( sprintf('body.lh-%s', 	$p->post_name), false );
						$this->logichop_add_preview_css( sprintf('logichop-%s', $p->post_name), false );
						$this->logichop_add_preview_css( sprintf('logichop-not-%s', $p->post_name), true );
					}
				}
			}
		}

		if ( $this->logic->data_factory->get_value( 'Condition' ) === true ) {
			$this->css[] = 'logichop-css-conditions-met';
			$this->logic->data_factory->set_value( 'Condition', false );

			if ( $preview ) {
				$this->logichop_add_preview_css( 'body.logichop-css-conditions-met', true );
				$this->logichop_add_preview_css( 'body.logichop-css-conditions-not-met', false );
			}
		} else {
			$this->css[] = 'logichop-css-conditions-not-met';

			if ( $preview ) {
				$this->logichop_add_preview_css( 'body.logichop-css-conditions-met', false );
				$this->logichop_add_preview_css( 'body.logichop-css-conditions-not-met', true );
			}
		}

		return $css_conditions;
	}

	/**
	 * Output Conditional CSS via WP Admin AJAX Call
	 *
	 * @since    1.0.1
	 */
	public function logichop_conditional_css () {
		if (!defined('DOING_AJAX') || !DOING_AJAX) wp_die();
		header('Content-type: text/css; charset: UTF-8');
		echo $this->generate_conditional_css();
		wp_die();
	}

	/**
	 * Display public CSS.
	 *
	 * If JS Tracking is NOT enabled, display via wp_head()
	 *
	 * @since    1.5.0
	 */
	public function output_header_css () {
		if (!$this->logic->js_tracking() && !$this->logic->disable_conditional_css()) {
			printf("<style type=\"text/css\">\n%s\n</style>\n", $this->generate_conditional_css());
		} else if ($this->logic->js_tracking() && !$this->logic->disable_conditional_css()) {
			print('<style id="logichop_header_css"></style>');
		}
	}

	/**
	 * Register public CSS.
	 *
	 * If JS Tracking is enabled, display via WP Admin AJAX to bypass caching
	 *
	 * @since    1.0.1
	 */
	public function enqueue_styles () {
		/*if ($this->logic->js_tracking() && !$this->logic->disable_conditional_css()) {
			wp_enqueue_style('logichop-conditions', admin_url('admin-ajax.php').'?action=logichop_conditional_css', array(), $this->version, 'all');
		}*/

		if ( $this->logic->is_preview_mode() ) {
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( $this->plugin_name . '-preview', plugin_dir_url( __FILE__ ) . 'css/preview.min.css', array(), $this->version, 'all' );
		}

		if ( $this->logic->get_option( 'debug_mode', false ) ) {
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( $this->plugin_name . '-generate_preview_data', plugin_dir_url( __FILE__ ) . 'css/generate_preview_data.css', array(), $this->version, 'all' );
		}

		wp_enqueue_style( $this->plugin_name . '-display', plugin_dir_url( __FILE__ ) . 'css/display.min.css', array(), $this->version, 'all' );

		$hook = '';
		do_action('logichop_public_enqueue_styles', $hook);
	}

	/**
	 * Register public JavaScript.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts () {

		if ( $this->logic->ie11_polyfills() ) {
            wp_enqueue_script( $this->plugin_name . '-ie11-polyfill', 'https://cdnjs.cloudflare.com/polyfill/v3/polyfill.min.js?version=4.8.0&features=Array.prototype.forEach%2CElement.prototype.classList%2CNodeList.prototype.forEach' );
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url(__FILE__) . 'js/ajax-methods.js', array('jquery'), $this->version, false );

		$id = get_the_id();
		$pid = $la = $lf = $gid = $cid = $gcid = $rcid = $redirect = null;

		$goal_ev	= get_post_meta($id, '_logichop_page_goal_js_event', true);
		$goal_el	= get_post_meta($id, '_logichop_page_goal_js_element', true);
		$goal_js	= (int) get_post_meta($id, '_logichop_page_goal_js', true);

		$views = ($this->logic->session_get_var('Views')) ? $this->logic->session_get_var('Views') : 0;

		if ($this->logic->js_tracking()) {
			$pid = $this->logic->wordpress_post_get();
			$la = (int) get_post_meta($id, '_logichop_page_leadscore', true);
			$lf = get_post_meta($id, '_logichop_page_lead_freq', true);
			$gid = (int) get_post_meta($id, '_logichop_page_goal', true);
			$cid = get_post_meta($id, '_logichop_page_goal_condition', true);
			$gcid =	(int) get_post_meta($id, '_logichop_page_goal_on_condition', true);
			$rcid = get_post_meta($id, '_logichop_page_condition', true);
			$redirect = get_post_meta($id, '_logichop_page_redirect', true);
		}

		$js_params = array(
						'ajaxurl' 	=> admin_url('admin-ajax.php'),
						'pid' 		=> $pid,		// PAGE
						'la' 		=> $la,			// LEAD SCORE ADJUSTER
						'lf' 		=> $lf,			// LEAD SCORE FREQUENCY
						'gid'		=> $gid,		// GOAL
						'cid'		=> $cid,		// CONDITIONAL GOAL CONDITION
						'gcid'		=> $gcid,		// CONDITIONAL GOAL
						'rcid'		=> $rcid,		// REDIRECT CONDITIONAL
						'redirect'	=> $redirect,	// REDIRECT URL
						'goal_ev'	=> $goal_ev,	// JAVASCRIPT GOAL EVENT
						'goal_el'	=> $goal_el,	// JAVASCRIPT GOAL ELEMENT
						'goal_js'	=> $goal_js,	// JAVASCRIPT GOAL
						'views'		=> $views,		// PAGE VIEWS
						'js_track' 	=> $this->logic->js_tracking(),			// JS TRACKING
						'anti_flicker' => $this->logic->js_anti_flicker,
						'anti_flicker_timeout' => $this->logic->js_anti_flicker_timeout,
						'js_vars' 	=> $this->logic->js_variable_display(),	// JS VARIABLES
						'vars'	=> null,
						'loaded' => false,
						'cookie'			=> $this->logic->cookie_retrieve(),
						'cookie_name'	=> $this->logic->cookie_name()
						);
        $logichop_data =null;
		if ( $this->logic->js_variable_display() ) {
			$_vars = new stdclass;

			$logichop_data = $this->logic->data_factory->get_data();

			if ( ! is_null( $logichop_data ) ) {
				foreach ( $logichop_data as $k => $v ) {
					if ( ! $this->logic-> is_variable_disabled( $k ) ) {
						$_vars->{$k} = $v;
					}
				}
			}
			$js_params['vars'] = $_vars;
		}

		if ( $this->logic->is_preview_mode() ) {
			$js_params['css_preview'] = true;
		}

		if ($this->logic->get_option('debug_mode', false)) {
            if ( ! is_null( $logichop_data ) ) {
                $js_params['debug'] = $logichop_data;
            }
		}

 		wp_localize_script( $this->plugin_name, 'logichop', $js_params);

 		if ( $this->logic->is_preview_mode() ) {
 			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( $this->plugin_name . '-preview', plugin_dir_url( __FILE__ ) . 'js/preview.min.js', array( 'jquery' ), $this->version, false );
		}

		if ( $this->logic->get_option( 'debug_mode', false ) ) {
			wp_enqueue_script( 'jquery-ui-draggable' );
 			wp_enqueue_script( $this->plugin_name . '-generate_preview_data', plugin_dir_url( __FILE__ ) . 'js/generate_preview_data.js', array( 'jquery' ), $this->version, false );
		}

 		$hook = $post_type = '';
 		do_action('logichop_public_enqueue_scripts', $hook, $post_type);
	}

	/**
	 * Render Logic Bars
	 *
	 * @since    3.1.0
	 */
	public function render_logic_bars () {

		$logicbars = get_posts(array(
  		'post_type' => 'logichop-logic-bars',
  		'post_status' => 'publish',
  		'numberposts' => -1
		));

		if ( $logicbars ) {
			foreach ( $logicbars as $lb ) {
				$values					= get_post_custom($lb->ID);
				$condition			= isset( $values['logichop_logicbar_condition'] ) ? esc_attr( $values['logichop_logicbar_condition'][0] ) : '';
				$condition_not	= isset( $values['logichop_logicbar_condition_not'] ) ? esc_attr( $values['logichop_logicbar_condition_not'][0] ) : '';
				$editor_styles  = isset( $values['logichop_logicbar_editor_styles'] ) ? esc_attr( $values['logichop_logicbar_editor_styles'][0] ) : '';
				$css_styles   	= isset( $values['logichop_logicbar_css_styles'] ) ? esc_attr( $values['logichop_logicbar_css_styles'][0] ) : '';
				$sticky_header	= isset( $values['logichop_logicbar_sticky'] ) ? esc_attr( $values['logichop_logicbar_sticky'][0] ) : '';
				$shadow	= isset( $values['logichop_logicbar_shadow'] ) ? esc_attr( $values['logichop_logicbar_shadow'][0] ) : '';
				$type	= isset( $values['logichop_logicbar_type'] ) ? esc_attr( $values['logichop_logicbar_type'][0] ) : '';
				$display	= isset( $values['logichop_logicbar_display'] ) ? esc_attr( $values['logichop_logicbar_display'][0] ) : '';
				$block = '';

				$hash = md5( $lb->post_name );

				$screenblock = '';
				if ( $type == 'popup' ) {
					$block = sprintf( '<div id="block-%s" class="logichop-logic-bar-screen-block %s"></div>',
																	$hash,
																	( $display ) ? 'logic-bar-hide' : ''
																);
				}

				$logicbar = sprintf( '{%% if condition: %s%s %%}<div id="bar-%s" data-hash="%s" data-delay="%s" class="logichop-logic-bar %s %s %s logic-bar-%s %s">%s</div>%s<style>%s%s</style>{%% endif %%}',
													( $condition_not == 'not_met' ) ? '!' : '',
													$condition,
													$hash, $hash,
													( $display ) ? $display : 'none',
													( $type == 'popup' ) ? 'logichop-logic-bar-popup' : '',
													( $sticky_header == 'disabled' ) ? 'logic-bar-not-sticky' : '',
													( $shadow == 'disabled' ) ? 'logic-bar-no-shadow' : '',
													$lb->post_name,
													( $display ) ? 'logic-bar-hide' : '',
													$lb->post_excerpt,
													$block,
													$editor_styles,
													$css_styles
												);

				echo $this->content_filter( $logicbar );
			}
		}
	}
}
