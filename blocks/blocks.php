<?php

 /**
 * Gutenberg-specific functionality.
 *
* @since      3.0.7
 * @package    LogicHop
 * @subpackage LogicHop/includes
 */
class LogicHop_Blocks {

	/**
	 * Core functionality & logic class
	 *
	 * @since    3.0.7
	 * @access   private
	 * @var      LogicHop_Core    $logic    Core functionality & logic.
	 */
	private $logic;

	/**
	 * Plugin basename - Plugin file path
	 *
	 * @since    3.0.7
	 * @access   private
	 * @var      string    $plugin_basename    Plugin basename.
	 */
	private $plugin_basename;

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.7
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.7
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.7
	 * @param      object    $logic    			LogicHop_Core functionality & logic.
	 * @param      string    $plugin_basename   Plugin file path
	 * @param      string    $plugin_name   	The name of this plugin.
	 * @param      string    $version    		The version of this plugin.
	 */
	public function __construct( $logic, $plugin_basename, $plugin_name, $version ) {
		$this->logic = $logic;
		$this->plugin_basename = $plugin_basename;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

  public function gutenberg () {
    if ( ! function_exists( 'register_block_type' ) ) {
      return false;
    }
    return true;
  }

  public function register_meta_fields () {
    if ( ! $this->gutenberg() ) return;
    register_meta(
			'post',
			'_logichop_page_leadscore',
			[
				'type'         => 'integer',
				'single'       => true,
				'show_in_rest' => true,
				'description'  => 'Logic Hop Gutenberg page/post Lead Score value',
			]
    );
		register_meta(
		  'post',
			'_logichop_page_lead_freq',
			[
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
				'description'  => 'Logic Hop Gutenberg page/post Lead Score frequency',
			]
    );
		register_meta(
		  'post',
			'_logichop_disable_js_mode',
			[
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
				'description'  => 'Logic Hop page/post redner setting',
			]
    );
  }

  public function protected_meta_fields ( $protected, $meta_key ) {
    if ( ! $this->gutenberg() ) return;
    $_keys = array( '_logichop_page_leadscore', '_logichop_page_lead_freq', '_logichop_disable_js_mode' );

    /*if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
      if ( in_array( $meta_key, $_keys ) ) {
        $protected = false;
      }
    }*/

    if ( in_array( $meta_key, $_keys ) ) {
      $protected = false;
    }

    return $protected;
  }

  public function parse_blocks ( $content ) {
  	preg_match_all( '#<logichop (.+?)>#is', $content, $blocks, PREG_SET_ORDER );
  	if ( $blocks ) {
  		$parsed_content = $content;
  		foreach ( $blocks as $block ) {
  			$source = sprintf( '<logichop %s>', $block[1] );
  			preg_match_all( '#condition="(.+?)"#is', $block[1], $conditions );
  			$condition = ( isset( $conditions ) && isset( $conditions[1] ) ) ? $conditions[1][0] : 'always_display';
  			$logic_tag = sprintf( '{%% if condition: %s %%}', $condition );
  			$parsed_content = str_replace( $source, $logic_tag, $parsed_content );
  		}
  		$parsed_content = str_replace( '</logichop>', '{% endif %}', $parsed_content );
  		return $parsed_content;
  	}
  	return $content;
  }

  public function register_dynamic_blocks () {
    if ( ! $this->gutenberg() ) return;

  	register_block_type(
  		'logic-hop/goal',
  		array (
      	'render_callback' => array($this, 'render_logichop_goal'),
  		)
  	);

  	register_block_type(
  		'logic-hop/conditional-goal',
  		array (
      	'render_callback' => array($this, 'render_logichop_conditional_goal'),
  		)
  	);
  }

  /**
	 * Get editor modal window variables
	 *
	 * @since    3.0.7
	 * @return		string		Datalist Options
	 */
	public function get_data_options () {
		$options = [
      [ 'value' => 'UserData.user_firstname',
      'label' => 'User First Name' ],
      [ 'value' => 'UserData.user_lastname',
      'label' => 'User Last Name' ],
      [ 'value' => 'UserData.display_name',
      'label' => 'User Display Name' ],
      [ 'value' => 'UserData.user_nicename',
      'label' => 'User Nice Name' ],
      [ 'value' => 'UserData.user_email',
      'label' => 'User Email Address' ],
      [ 'value' => 'UserData.role',
      'label' => 'User Role' ],
      [ 'value' => 'UserData.ID',
      'label' => 'User ID' ],
      [ 'value' => 'Query:#var#',
      'label' => 'Query String' ],
      [ 'value' => 'QueryStore:#var#',
      'label' => 'Query String - Stored' ],
      [ 'value' => 'Location.CountryCode',
      'label' => 'Country Code (US, CA)' ],
      [ 'value' => 'Location.CountryName',
      'label' => 'Country Name' ],
      [ 'value' => 'Location.RegionCode',
      'label' => 'Region Code (CA, NY)' ],
      [ 'value' => 'Location.RegionName',
      'label' => 'Region Name (California, New York)' ],
      [ 'value' => 'Location.City',
      'label' => 'City' ],
      [ 'value' => 'Location.ZIPCode',
      'label' => 'ZIP Code' ],
      [ 'value' => 'Location.TimeZone',
      'label' => 'Time Zone' ],
      [ 'value' => 'Location.Latitude',
      'label' => 'Latitude' ],
      [ 'value' => 'Location.Longitude',
      'label' => 'Longitude' ],
      [ 'value' => 'Location.IP',
      'label' => 'IP Address' ],
			[ 'value' => 'UserDate.DayName',
      'label' => 'User\'s Date: Day' ],
			[ 'value' => 'UserDate.Day',
      'label' => 'User\'s Date: Day - Numeric' ],
			[ 'value' => 'UserDate.MonthName',
      'label' => 'User\'s Date: Month' ],
			[ 'value' => 'UserDate.Month',
      'label' => 'User\'s Date: Month - Numeric' ],
			[ 'value' => 'UserDate.Year',
      'label' => 'User\'s Date: Year' ],
			[ 'value' => 'Date.DayName',
			'label' => 'Website Date: Day' ],
			[ 'value' => 'Date.Day',
			'label' => 'Website Date: Day Numeric' ],
			[ 'value' => 'Date.MonthName',
			'label' => 'Website Date: Month' ],
			[ 'value' => 'Date.Month',
			'label' => 'Website Date: Month Numeric' ],
			[ 'value' => 'Date.Year',
			'label' => 'Website Date: Year' ],
      [ 'value' => 'LandingPage',
      'label' => 'Landing Page' ],
      [ 'value' => 'LeadScore',
      'label' => 'Lead Score' ],
      [ 'value' => 'Source',
      'label' => 'Source / Referral' ],
      [ 'value' => 'UserAgent',
      'label' => 'User Agent' ],
    ];

    return apply_filters('logichop_gutenberg_variables', $options);
	}

  public function render_logichop_goal ( $atts ) {
  	if ( isset( $atts['goal'] ) ) {
			$deleteGoal = ( isset( $atts['deleteGoal'] )) ? ' | delete: true' : '';
			return sprintf( '{{ goal: %s%s }}', $atts['goal'], $deleteGoal );
		}
		return '';
  }

  public function render_logichop_conditional_goal ( $atts ) {
  	if ( isset( $atts['goal'] ) && isset( $atts['condition'] ) ) {
				$deleteGoal = ( isset( $atts['deleteGoal'] )) ? ' | delete: true' : '';
  			$condition = ( isset( $atts['conditionNot'] )) ? sprintf( '!%s', $atts['condition'] ) : $atts['condition'];
  			return sprintf( '{{ goal: %s | condition: %s%s }}', $atts['goal'], $condition, $deleteGoal );
  	}
		return '';
  }

  public function enqueue_block_editor_assets () {
    if ( ! $this->gutenberg() ) return;

    wp_enqueue_style( $this->plugin_name . '-blocks', plugin_dir_url( __FILE__ ) . 'css/editor.css', array(), $this->version, 'all' );

    wp_enqueue_script( $this->plugin_name . '-blocks', plugin_dir_url( __FILE__ ) . 'js/editor.js', [ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-edit-post', 'wp-plugins' ], $this->version, false );

    $conditions = $this->logic->conditions_get( true, 'value', 'label' );
    $goals = $this->logic->goals_get( true, 'value', 'label' );

    $js_params = array(
			'caching' 		=> $this->logic->caching_enabled(),
			'disable_js'	=> ( get_post_meta( get_the_ID(), '_logichop_disable_js_mode', true ) ) ? 'true' : '',
  		'conditions'	=> $conditions,
  		'goals'	      => $goals,
      'variables'   => $this->get_data_options()
  	);
  	wp_localize_script( $this->plugin_name . '-blocks', 'logichop_block_data', $js_params );
  }

}
