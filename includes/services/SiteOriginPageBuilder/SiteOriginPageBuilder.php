<?php

if (!defined('ABSPATH')) die;

/**
 * Adds support for SiteOrigin Page Builder
 *
 *
 * @since      3.0.0
 * @package    LogicHop
 * @subpackage LogicHop/includes/classes
 */
	
class LogicHop_SiteOriginPageBuilder {
	
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
		
		add_filter( 'siteorigin_panels_widget_object', array( $this, 'logichop_so_widget_filter'), 10, 3 );
		add_filter( 'siteorigin_panels_missing_widget', array( $this, 'logichop_so_widget_render_missing'), 10, 1 );
		add_filter( 'siteorigin_panels_widget_classes', array( $this, 'logichop_so_widget_prepare_css'), 10, 4 );
		add_filter( 'siteorigin_panels_widget_attributes', array( $this, 'logichop_so_widget_update_attributes'), 10, 2 );
	}

	/**
	 * Filter SiteOrigin Widgets
	 * Displays SiteOrigin PageBuilder widgets based on the outcome of a condition 
	 * Only when JS Tracking is DISABLED
	 *
	 * @since    	2.1.3
	 * @param  		object	$the_widget		Wordpress Widget
	 * @param  		string	$widget			Widget name
	 * @param  		array	$instance		Widget details
	 * @return    	object	$the_widget		Wordpress Widget		
	 */
	public function logichop_so_widget_filter ($the_widget, $widget, $instance = null) {
		
		if (is_admin() || $this->logic->js_tracking()) return $the_widget;
		
		$condition_id 		= (isset($instance) && isset($instance['logichop_widget'])) ? $instance['logichop_widget'] : 0;
		$condition_not		= (isset($instance) && isset($instance['logichop_widget_not'])) ? (boolean) $instance['logichop_widget_not'] : false;
		
		if ($condition_id) {
			$display_widget = $this->logic->condition_get($condition_id);
			if ($condition_not) $display_widget = !$display_widget;
			
			if (!$display_widget) {
				$object = new stdclass;
				$object->widget_options = array();
				return $object;
			}
		}
		
		return $the_widget;
	}
	
	/**
	 * Render Missing SiteOrigin Widgets
	 * Catches missing widget fallback display and replaces with empty string to prevent display
	 *
	 * @since    	2.1.3
	 * @param  		string	$widget		Widget content
	 * @return    	string	$widget		Widget content --> EMPTY 
	 */
	public function logichop_so_widget_render_missing ($widget) {
		return '';
	}
	
	/**
	 * Prepares SiteOrigin Widget CSS
	 * Adds temporary CSS for parsing to conditional widgets
	 * Only when JS Tracking is ENABLED
	 *
	 * @since    	2.1.3
	 * @param  		array	$classes		Array of CSS classes
	 * @param  		string	$widget_class	Widget name
	 * @param  		array	$instance		Widget details
	 * @return    	array	$widget_info	Widget information
	 */
	public function logichop_so_widget_prepare_css ($classes, $widget_class, $instance, $widget_info) {
	
		if (!$this->logic->js_tracking()) return $classes;
		
		$condition_id	= (isset($instance) && isset($instance['logichop_widget'])) ? $instance['logichop_widget'] : 0;
		$condition_not	= (isset($instance) && isset($instance['logichop_widget_not'])) ? (boolean) $instance['logichop_widget_not'] : false;
		
		if ($condition_id) {
			$classes[] = sprintf('logichop-js-%d-%d',
									$condition_id,
									$condition_not ? 1 : 0
								);
		}
		
		return $classes;
	}
	
	/**
	 * SiteOrigin Widget UpdateAttributes
	 * Parses temporary CSS and adds appropriate attributes for JS display
	 * Only when JS Tracking is ENABLED
	 *
	 * @since    	2.1.3
	 * @param  		array	$atts			Array of attributes
	 * @return    	array	$widget_info	Widget information
	 */
	public function logichop_so_widget_update_attributes ($atts, $widget_info) {
		
		if (!$this->logic->js_tracking()) return $atts;
		
		$classes = explode(' ', $atts['class']);
		
		if ($classes) {
			for ($i = 0; $i < count($classes); $i++) {
				if (strpos($classes[$i], 'logichop-js-') === 0) {
					
					$logic = explode('-', $classes[$i]);
					
					if (isset($logic[2])) {
						
						$atts['data-cid'] = $logic[2];
						if (isset($logic[3]) && $logic[3] == 1)	$atts['data-not'] = 'true';
						$atts['data-event'] = 'fadeIn';
						$atts['style'] = 'display: none;';
						
						if ( $this->logic->is_preview_mode() ) {
							$_conditon = $this->logic->condition_get_json($logic[2], true);
							if (isset( $_conditon->title )) {
								$atts['data-title'] = $_conditon->title;
								if (isset($logic[3]) && $logic[3] == 1) $atts['data-title'] = '!' . $_conditon->title;
							}
						}
						
						
					}
					
					$classes[$i] = 'logichop-js';
				}
			}
			
			$atts['class'] = implode(' ', $classes);
		}
		
		return $atts;
	}	
	
}
