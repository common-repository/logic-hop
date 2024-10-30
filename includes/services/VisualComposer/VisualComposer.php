<?php

if (!defined('ABSPATH')) die;

/**
 * Adds support for Visual Composer
 *
 *
 * @since      3.0.0
 * @package    LogicHop
 * @subpackage LogicHop/includes/services
 */
	
class LogicHop_VisualComposer {
	
	/**
	 * Core functionality & logic class
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      LogicHop_Core    $logic    Core functionality & logic.
	 */
	private $logic;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    	3.0.0
	 * @param       object    $logic	LogicHop_Core functionality & logic.
	 */
	public function __construct( $logic ) {
		$this->logic = $logic;
		$this->init();
	}
	
	/**
	 * Add Actions and Shortcodes
	 *
	 * @since    	3.0.0
	 * @return      void
	 */
	public function init() {
		if (!defined('WPB_VC_VERSION')) return;
		add_action( 'vc_before_init', array( $this, 'setup_vc_shortcodes' ) );
		add_shortcode( 'vc_logic_hop', array( $this, 'render_logichop' ) );
	}
	
	/**
	 * Set up Visual Composer Shortcodes
	 *
	 * @since    	3.0.0
	 * @return      void
	 */
	public function setup_vc_shortcodes () {
		
		$condition_options = array (
									'Always Display'  	=> '',
								);
								
		if ( $conditions = $this->logic->conditions_get(true) ) {
			foreach ($conditions as $c) {
				$condition_options[$c['name']] = $c['slug'];
			}
		}
		
		vc_map( array(
					'name' => __('Logic Hop'),
					'description' => __('Logic Hop Conditional Container'),
					'base' => "vc_logic_hop",
					'icon' => plugin_dir_url( __FILE__ ) . '/img/vc-icon.svg',
					'category' => __('Content'),
					'class' => '',
					'show_settings_on_create' => true,
					'js_view' => 'VcColumnView',
					'content_element' => true,
					'is_container' => true,
					'allowed_container_element' => true,
					'as_parent' => array('except' => 'vc_logic_hop'),
					'as_child' => array('except' => 'vc_logic_hop'),
					
					'params' => array(
						array(
							'type'			=> 'dropdown',
							'class'      	=> '',
							'heading'    	=> 'Condition',
							'param_name' 	=> 'logichop_condition',
							'value'      	=> $condition_options,
							'description' => __('')
						),
						array(
							'type'			=> 'dropdown',
							'class'      	=> '',
							'heading'    	=> 'Display When',
							'param_name' 	=> 'logichop_not',
							'value'      	=> array(
									'Condition is Met'  	=> '',
									'Condition is Not Met'	=> '!'
							),
							'description' => __('')
						),
						array(
							'type'			=> 'dropdown',
							'class'      	=> '',
							'heading'    	=> 'Display Event',
							'param_name' 	=> 'logichop_event',
							'value'      	=> array(
									'Show'  		=> '',
									'Fade In'  		=> 'fadein',
									'Slide Down'  	=> 'slidedown',
							),
							'description' => __('')
						),
						array(
							'type'			=> 'textfield',
							'class'      	=> '',
							'heading'    	=> 'CSS Classes',
							'param_name' 	=> 'logichop_css',
							'description' => __('Optional CSS classes.')
						),
				   )
				)
			);
	}
	
	/**
	 * Render Logic Hop Shortcode
	 *
	 * @since    	3.0.0
	 * @return      string    	Rendered shortcode HTML
	 */
	public function render_logichop ( $atts, $content ) {
		$atts = shortcode_atts(
			array(
				'logichop_condition' 	=> '',
				'logichop_not' 			=> '',
				'logichop_event' 		=> 'show',
				'logichop_css' 			=> '',
			), $atts, 'vc_logic_hop'
		);
		
		$html 		= '';
		$element 	= 'div';
		$preview 	= $this->logic->is_preview_mode();
		
		if ($atts['logichop_condition'] == '') {
			return sprintf('<%s class="%s">%s</%s>',
							$element,
							$atts['logichop_css'],
							do_shortcode($content),
							$element
						);
		}
		
		if ( $this->logic->caching_enabled() || $preview ) {
			
			$_condition 	= $this->logic->condition_get_json($atts['logichop_condition'], true);
			$condition 		= json_encode($_condition->rule);
			$title			= $_condition->title;
			
			if ($atts['logichop_not'] == '!') {
				$title = '!' . $title;
				$condition = sprintf('{"!=":[%s,true]}', $condition);
			}
						
			$html = sprintf('<%s class="logichop-js %s %s" style="display: none;" %s data-hash="%s" data-condition=\'%s\' data-event="%s">%s</%s>',
							$element,
							($preview) ? 'logichop-preview' : '',
							$atts['logichop_css'],
							($title && $preview) ? sprintf('data-title="%s"', htmlentities($title, ENT_QUOTES, 'UTF-8')) : '', // ONLY DISPLAY TITLES IN PREVIEW MODE
							
							md5($condition),
							$condition,
							
							$atts['logichop_event'],
							do_shortcode($content),
							$element
						);
		} else {
		
			$statement = $this->logic->condition_get($atts['logichop_condition']);
			if ($atts['logichop_not'] == '!') {
				$statement = !$statement;
			}
			
			if ($statement) {
				$html = sprintf('<%s class="%s">%s</%s>',
								$element,
								$atts['logichop_css'],
								do_shortcode($content),
								$element
							);
			}
		}
		
		return $html;
	}
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Vc_Logic_Hop extends WPBakeryShortCodesContainer {
		// ENABLES FUNCTIONALITY
	}
}
