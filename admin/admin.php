<?php

 /**
 * Admin-specific functionality.
 *
* @since      1.0.0
 * @package    LogicHop
 * @subpackage LogicHop/includes
 */
class LogicHop_Admin {

	/**
	 * Core functionality & logic class
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      LogicHop_Core    $logic    Core functionality & logic.
	 */
	private $logic;

	/**
	 * Plugin basename - Plugin file path
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_basename    Plugin basename.
	 */
	private $plugin_basename;

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
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

	/**
	 * Returns plugin URL.
	 *
	 * @since    1.0.0
	 * @param	string	$which_url		Array key
	 * @return	string	URL
	 */
	public function plugin_url ($which_url) {
		$urls = array (
			'website' 		=> 'https://logichop.com/?utm_source=plugin',
			'contact' 		=> 'https://logichop.com/contact/?utm_source=plugin',
			'referrer' 		=> '?ref=plugin',
			'data-plan' 	=> 'https://logichop.com/get-started/?utm_source=plugin',
			'lh-account'	=> 'https://logichop.com/my-account/?utm_source=plugin',
			'learn' 		=> 'https://logichop.com/get-started/?utm_source=plugin',
			'docs' 			=> 'https://logichop.freshdesk.com/support/home/?ref=plugin',
			'quickstart' 	=> 'https://logichop.freshdesk.com/support/solutions/articles/80000947514-installation-guide/?utm_source=plugin',
			'5min'			=> 'https://logichop.freshdesk.com/support/solutions/articles/80000947519-famous-five-minute-quick-start-guide/?utm_source=plugin',
			'cond-oview'	=> 'https://logichop.freshdesk.com/support/solutions/articles/80000947530-logic-hop-conditions/?utm_source=plugin',
			'admin' 		=> plugin_dir_url(__FILE__),
			'dashboard' 	=> admin_url('admin.php?page=logichop-dashboard'),
			'settings' 		=> admin_url('admin.php?page=logichop-settings'),
			'integrations' 	=> admin_url('admin.php?page=logichop-integrations'),
			'conditions'	=> admin_url('edit.php?post_type=logichop-conditions'),
			'add-condition' => admin_url('post-new.php?post_type=logichop-conditions'),
			'goals'			=> admin_url('edit.php?post_type=logichop-goals'),
			'add-goal' 		=> admin_url('post-new.php?post_type=logichop-goals')
		);

		if (key_exists($which_url, $urls)) {
			return $urls[$which_url];
		}
		return '';
	}

	/**
	 *  Validate a trial generation
	 */
	public function logichop_trial () {
		check_ajax_referer( 'logichop_nonce' );
		$args = $_POST['data'];
		update_option(LOGICHOP_TRIAL,$args['status']);
		wp_send_json( [ 'status'=>'ok'] );
	}
	/**
	 * Determines if using WPEngine hosting
	 *
	 * WPEngine --> https://wpengine.com/support/determining-wp-engine-environment/
	 *
	 * @since    3.0.0
	 * @return 		boolean
	 */
	public function is_wpengine () {
		$wpengine = false;
		if ( function_exists('is_wpe') ) $wpengine = true;	// WPEngine Production
		if ( function_exists('is_wpe_snapshot') ) $wpengine = true;	// WPEngine Staging
		return $wpengine;
	}

	/**
	 * Determines if using Pantheon hosting & no session plugin
	 *
	 * PANTHEON SESSIONS --> https://pantheon.io/docs/caching-advanced-topics/
	 * PANTHEON COOKIES --> https://pantheon.io/docs/cookies/
	 *
	 * @since    3.1.7
	 * @param 	$no_sessions	boolean  Just check for Pantheon --> Override session plugin check
	 * @return 		boolean
	 */
	public function is_pantheon ( $no_sessions = false ) {
		$pantheon = false;
		if ( defined( 'PANTHEON_SITE' ) ) {
			if ( ! defined( 'PANTHEON_SESSIONS_ENABLED' ) ) {
				$pantheon = true;
			} else {
				if ( PANTHEON_SESSIONS_ENABLED == false ) {
					$pantheon = true;
				}
			}
			if ( $no_sessions ) {
				$pantheon = true;
			}
		}
		return $pantheon;
	}

	/**
	 * Register Plugin Settings.
	 *
	 * @since    1.0.0
	 */
	public function settings_register () {

		if (get_option('logichop-settings') == false) add_option('logichop-settings');

		add_settings_section(
			'logichop_settings_section',
        	'Settings',
        	array($this, 'section_callback'),
        	'logichop-settings'
    	);

		$settings = array (
						'domain' 		=> array (
												'name' 	=> __('Domain Name', 'logichop'),
												'meta' 	=> sprintf(__('Recommended: %s', 'logichop'), isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : ''),
												'type' 	=> 'text',
												'label' => 'Domain Name',
												'opts'  => null
											),
						'api_key' 		=> array (
												'name' 	=> __('License/API Key', 'logichop'),
												'meta' 	=> __('License/API Key provided with Logic Hop subscription. <a href="https://logichop.com/get-started/?utm_source=plugin" target="_blank">Learn more</a>.', 'logichop'),
												'type' 	=> 'text',
												'label' => 'License/API Key',
												'opts'  => null
											),
						'cookie_name' 		=> array (
												'name' 	=> __('Cookie Name', 'logichop'),
												'meta' 	=> __('Logic Hop cookie name. Must be a valid cookie name. <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947521-how-to-configure-logic-hop/?utm_source=plugin" target="_blank">Learn more</a>.<br>Recommended value: <em>logichop</em> ', 'logichop'),
												'type' 	=> 'cookie_name',
												'label' => 'Cookie Name',
												'opts'  => null
											),
						'cookie_ttl' 	=> array (
												'name' 	=> __('Cookie Duration', 'logichop'),
												'meta' 	=> __('How long until user data expires. <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947521-how-to-configure-logic-hop/?utm_source=plugin" target="_blank">Learn more</a>.', 'logichop'),
												'type' 	=> 'select',
												'label' => '',
												'opts'  => array (
																0 => array (
																		'name' => __('Never Expire', 'logichop'),
																		'value' => '+ 20 year'
																	),
																1 => array (
																		'name' => __('One Year', 'logichop'),
																		'value' => '+ 1 year'
																	),
																2 => array (
																		'name' => __('One Month', 'logichop'),
																		'value' => '+ 1 month'
																	),
																3 => array (
																		'name' => __('One Week', 'logichop'),
																		'value' => '+ 1 week'
																	),
																4 => array (
																		'name' => __('One Day', 'logichop'),
																		'value' => '+ 1 day'
																	),
																5 => array (
																		'name' => __('One Hour', 'logichop'),
																		'value' => '+ 1 hour'
																	),
																)
											),
						'consent_require' 	=> array (
												'name' 	=> __('Require Consent', 'logichop'),
												'meta' 	=> __('Require consent? <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947523-data-storage-gdpr-settings/?utm_source=plugin" target="_blank">Learn more</a>.', 'logichop'),
												'type' 	=> 'select',
												'label' => '',
												'opts'  => array (
																0 => array (
																		'name' => __('Never', 'logichop'),
																		'value' => ''
																	),
																1 => array (
																		'name' => __('Visitors from GDPR Countries', 'logichop'),
																		'value' => 'gdpr'
																	),
																2 => array (
																		'name' => __('Visitors from European Union', 'logichop'),
																		'value' => 'eu'
																	),
																3 => array (
																		'name' => __('All Visitors', 'logichop'),
																		'value' => 'all'
																	),
																)
											),
						'consent_cookie' => array (
												'name' 	=> __('Consent Cookie', 'logichop'),
												'meta' 	=> __('Data consent confirmation. <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947523-data-storage-gdpr-settings/?utm_source=plugin" target="_blank">Learn more</a>.', 'logichop'),
												'type' 	=> 'text',
												'label' => 'Cookie Name (Optional)',
												'opts'  => null
											),
						'geolocation_disable' => array (
												'name' 	=> __('Geolocation', 'logichop'),
												'meta' 	=> __('Increases performance & reduces processing time when disabled.', 'logichop'),
												'type' 	=> 'checkbox',
												'label' => __('Disable Geolocation', 'logichop'),
												'opts'  => null
											),
						'js_tracking' => array (
												'name' 	=> __('Javascript Mode', 'logichop'),
												'meta' 	=> __('Recommended when using Cache Plugins. <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947701-logic-hop-javascript/?utm_source=plugin" target="_blank">Learn More</a>.', 'logichop'),
												'type' 	=> 'tracking',
												'label' => __('Enable Javascript Mode', 'logichop'),
												'opts'  => null
											),
						'js_anti_flicker' => array (
												'name' 	=> __('Anti-Flicker Mode', 'logichop'),
												'meta' 	=> __('Hides the page until the container is ready, ensuring that users don\'t see the initial page content prior to it being modified by Logic Hop.', 'logichop'),
												'type' 	=> 'checkbox',
												'label' => __('Enable Anti-Flicker Mode', 'logichop'),
												'opts'  => null
											),
						'js_anti_flicker_timeout' => array (
												'name' 	=> __('Anti-Flicker Timeout', 'logichop'),
												'meta' 	=> __('Time in milliseconds until page is automatically displayed (in the unlikely event Logic Hop takes too long to load). <br>Example: 3000 would be 3 seconds.', 'logichop'),
												'type' 	=> 'text',
												'label' => __('Milliseconds until page is displayed', 'logichop'),
												'opts'  => array (
													'default' => $this->logic->js_anti_flicker_timeout
												)
											),
						'ajax_referrer' => array (
												'name' 	=> __('Javascript Referrer', 'logichop'),
												'meta' 	=> __('Restrict AJAX requests to specific domain. Leave blank to allow from all.', 'logichop'),
												'type' 	=> 'referrer',
												'label' => __('Enable Referrer', 'logichop'),
												'opts'  => null
											),
						'js_variables' => array (
												'name' 	=> __('Javascript Variables', 'logichop'),
												'meta' 	=> __('Check to display data via Javascript. <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947701-logic-hop-javascript/?ref=plugin" target="_blank">Learn More</a>.', 'logichop'),
												'type' 	=> 'checkbox',
												'label' => __('Enable Javascript Variable Display', 'logichop'),
												'opts'  => null
											),
						'js_disabled_vars' => array (
												'name' 	=> __('Disable Variables ', 'logichop'),
												'meta' 	=> __('Prevents specifc variables from being accessed via Javsascript. <br>Comma-separated list of variable names. <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947701-logic-hop-javascript/?ref=plugin" target="_blank">Learn More</a>.', 'logichop'),
												'type' 	=> 'textarea',
												'label' => __('Disable Javascript Variables', 'logichop'),
												'opts'  => null
											),
						'ie11_polyfills' => array (
												'name' 	=> __('IE11 Polyfills', 'logichop'),
												'meta' 	=> __('Include Javascript polyfills from safe CDN "cdnjs.cloudflare.com" for IE11 support.', 'logichop'),
												'type' 	=> 'checkbox',
												'label' => __('Internet Explorer 11 Polyfill', 'logichop'),
												'opts'  => null
											),
						'disable_wpautop' => array (
												'name' 	=> __('Disable wpautop()', 'logichop'),
												'meta' 	=> __('Check to disable WordPress auto-paragraph tags in the content and excerpt.', 'logichop'),
												'type' 	=> 'checkbox',
												'label' => __('Disable WordPress Auto-Paragraph Tags', 'logichop'),
												'opts'  => null
											),
						'disable_conditional_css' => array (
												'name' 	=> __('Conditional CSS', 'logichop'),
												'meta' 	=> __('Check to disable Conditional CSS output. <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947700-logic-hop-conditional-css/?ref=plugin" target="_blank">Learn More</a>.', 'logichop'),
												'type' 	=> 'checkbox',
												'label' => __('Disable Conditional CSS', 'logichop'),
												'opts'  => null
											),
						'disable_editor_styles' => array (
												'name' 	=> __('Visual Editor Styling', 'logichop'),
												'meta' 	=> __('Check to disable Visual Editor Styling.', 'logichop'),
												'type' 	=> 'checkbox',
												'label' => __('Disable Visual Editor Styling', 'logichop'),
												'opts'  => null
											),
						 'disable_nested_tags' => array (
												'name' 	=> __('Logic Tags', 'logichop'),
												'meta' 	=> __('Check to disable nested Logic Tags.', 'logichop'),
												'type' 	=> 'checkbox',
												'label' => __('Disable Nested Logic Tags', 'logichop'),
												'opts'  => null
											),
                        'expire_transients'	=> array (
													'name' 	=> __('Delete Transients', 'logichop'),
													'meta' 	=> __('Automatically delete expired Logic Hop Transients. <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947523-data-storage-gdpr-settings/?ref=plugin#transients" target="_blank">Learn More.</a></strong>', 'logichop'),
													'type' 	=> 'select',
													'label' => '',
													'opts'  => array (
																	0 => array (
																			'name' => __('Never', 'logichop'),
																			'value' => 0
																		),
																	1 => array (
																			'name' => __('Every Hour', 'logichop'),
																			'value' => 'hourly'
																		),
																	2 => array (
																			'name' => __('Every 12 Hours', 'logichop'),
																			'value' => 'twicedaily'
																		),
																	3 => array (
																			'name' => __('Every 24 Hours', 'logichop'),
																			'value' => 'daily'
																		),
																	)
												),
					);

		$settings = apply_filters('logichop_settings_register', $settings);

		$settings['debug_mode'] = array (
												'name' 	=> __('Debug Mode', 'logichop'),
												'meta' 	=> __('Output Logic Hop data for testing & debugging.<br>Append to URL: <em>?debug=display</em> - Disabled when Javascript Mode enabled<br>Javascript Mode Output: <em>logichop_debug();</em><br><strong>FOR TESTING ONLY - Disable on production websites.</strong>', 'logichop'),
												'type' 	=> 'checkbox',
												'label' => __('Enable Debug Mode', 'logichop'),
												'opts'  => null
											);

		foreach ($settings as $var => $params) {
			add_settings_field(
				$var,
				$params['name'],
				array($this, 'render_setting_input'),
				'logichop-settings',
				'logichop_settings_section',
				array($var, $params['type'], $params['meta'], $params['label'], $params['opts'])
			);
		}

		register_setting(
			'logichop-settings',
			'logichop-settings',
			array($this, 'setting_validation')
		);

		do_action('logichop_settings_registered');
	}

	/**
	 * Plugin section callback.
	 *
	 * @since	1.0.0
	 * @return  null
	 */
	public function section_callback () {
		//$this->logic->validate_api(null,"section_callback");
		return;
	}

	/**
	 * Validate Plugin Settings.
	 *
	 * @since    1.0.0
	 * @param	array	$input		Plugin settings
	 * @return	array	Plugin settings
	 */
	public function setting_validation ($input) {
		$output = array();
    	$error = false;
    	$error_msg = '';

    	foreach ($input as $key => $value) {
            $this->setting_action( $key, $value );
        	if (isset($input[$key])) {
         		$output[$key] = strip_tags(stripslashes($input[$key]));
         		if ($input[$key] != '') {
                     $validation = new stdclass;
                     $validation->error = false;
	    			 $validation = apply_filters('logichop_settings_validate', $validation, $key, $input);
	         		if ( isset( $validation->error ) && $validation->error ) {
	         			$error = true;
                            if ( isset( $validation->error_msg ) ) {
         					    $error_msg .= $validation->error_msg;
                            }
         				$output[$key] = '';
         			}
         		}
        	}
    	}

    	if ($error) {
    		add_settings_error(
        		'logichop_settings_error',
				'settings_updated',
        		sprintf('<h2>Settings Error</h2><ul>%s</ul>', $error_msg),
        		'error'
    		);
    	}
		return $output;
	}

	/**
	 * Perform action based on setting
	 *
	 * @since    3.2.3
	 * @param	string	$key		Setting key
	 * @param	mixed	$value		Setting value
	 */
	public function setting_action ( $key, $value ) {

		if ( $key == 'expire_transients' ) {
			if ( wp_next_scheduled( 'logichop_purge_transients' ) ) {
				wp_clear_scheduled_hook( 'logichop_purge_transients' );
			}
			if ( $value != '0' ) {
				wp_schedule_event( time(), $value, 'logichop_purge_transients' );
			}
		}

	}

	/**
	 * Settings updated callback.
	 *
	 * @since    1.1.0
	 * @param	string	$updated		Settings Updated
	 */
	public function settings_updated ($updated, $old_value, $new_value) {
		if ($updated == 'logichop-settings') {
            $this->logic->update_client_meta();
        }
	}

	/**
	 * Render Settings Form Inputs.
	 *
	 * @param	array	$args		Setting arguments
	 * @since    1.0.0
	 */
	public function render_setting_input ($args) {

		$var 	= isset($args[0]) ? $args[0] : '';
		$type 	= isset($args[1]) ? $args[1] : 'text';
		$meta 	= isset($args[2]) ? $args[2] : '';
		$label 	= isset($args[3]) ? $args[3] : '';
		$opts 	= isset($args[4]) ? $args[4] : array();

		$options = get_option('logichop-settings');

		if ($type == 'text') {
			$value = isset($options[$var]) ? sanitize_text_field($options[$var]) : '';
			if ( ! $value ) {
				if ( array_key_exists( 'default', $opts ) ) {
					$value = $opts['default'];
				}
			}
			printf('<input type="text" id="logichop-settings[%s]" name="logichop-settings[%s]" value="%s" style="width: 400px; height: 30px;" placeholder="%s">
					<p><small>%s</small></p>',
					$var,
					$var,
					$value,
					$label,
					$meta
				);
		}

		if ($type == 'textarea') {
			printf('<textarea id="logichop-settings[%s]" name="logichop-settings[%s]" style="width: 400px; height: 60px;">%s</textarea>
					<p><small>%s</small></p>',
					$var,
					$var,
					isset($options[$var]) ? sanitize_text_field($options[$var]) : '',
					$meta
				);
		}

		if ($type == 'select') {
			$value = isset($options[$var]) ? sanitize_text_field($options[$var]) : '';

			$option_items = '';
			foreach ($opts as $o) {
				$option_items .= sprintf('<option value="%s" %s>%s</option>',
											$o['value'],
											($value == $o['value']) ? 'selected' : '',
											$o['name']
										);
			}

			printf('<select id="logichop-settings[%s]" name="logichop-settings[%s]" style="width: 400px; height: 30px;">
						%s
					</select>
					<p><small>%s</small></p>',
					$var,
					$var,
					$option_items,
					$meta
				);
		}

		if ($type == 'checkbox') {
			$value = isset($options[$var]) ? $options[$var] : '';
			printf('<input type="checkbox" id="logichop-settings[%s]" name="logichop-settings[%s]" value="1" %s />
					<label for="%s"><strong>%s</strong></label>
					<p><small>%s</small></p>',
					$var,
					$var,
					checked(1, $value, false),
					$var,
					$label,
					$meta
				);
		}

		if ($type == 'referrer') {
			$default = $this->logic->get_referrer_host();

			if ($default) {
				$messsage = sprintf(__('Recommended: %s', 'logichop'), $default);
			} else {
				$messsage = __('Referrer not found in $_SERVER[\'HTTP_REFERER\']. Please check with your hosting company.', 'logichop');
			}

			$ajax_referrer = isset($options['ajax_referrer']) ? sanitize_text_field($options['ajax_referrer']) : $default;
			$js_tracking = isset($options['js_tracking']) ? $options['js_tracking'] : '';
			printf('<input type="text" id="logichop-settings[%s]" name="logichop-settings[%s]" value="%s" style="width: 400px; height: 30px;">
					<p><small>%s<br>%s</small></p>',
					$var,
					$var,
					$ajax_referrer,
					$meta,
					$messsage
				);
		}

		if ($type == 'tracking') {
			$value = isset($options['js_tracking']) ? $options['js_tracking'] : '';
			printf('<input type="checkbox" id="logichop-settings[%s]" name="logichop-settings[%s]" value="1" %s />
					<label for="%s"><strong>%s</strong></label>
					<p><small>%s</small></p>
					%s%s',
					$var,
					$var,
					checked(1, $value, false),
					$var,
					$label,
					$meta,
					(defined('WP_CACHE') && WP_CACHE && !$value) ? '<p><strong style="color: rgb(255,0,0);">Cache Enabled: Javascript Mode is recommended.</strong></p>' : '',
					($this->is_wpengine() && !$value) ? '<p><strong style="color: rgb(255,0,0);">WPEnging Hosting Detected: Javascript Mode is recommended.</strong></p>' : ''
				);
		}

		if ($type == 'domain') {
			printf('<input type="text" id="logichop-settings[%s]" name="logichop-settings[%s]" value="%s" style="width: 400px; height: 30px;" readonly>
					<p><small>%s</small></p>',
					$var,
					$var,
					isset($options[$var]) ? sanitize_text_field($options[$var]) : strtolower($_SERVER['SERVER_NAME']),
					''
				);
		}

		if ($type == 'cookie_name') {
			$value = isset($options[$var]) ? sanitize_text_field($options[$var]) : 'logichop';
			$value = str_replace( array( '=',',',' ',';' ), '', $value );
			if ( $value == '' ) {
				$value = 'logichop';
			}
			printf('<input type="text" id="logichop-settings[%s]" name="logichop-settings[%s]" value="%s" style="width: 400px; height: 30px;" placeholder="%s">
					<p><small>%s</small></p>',
					$var,
					$var,
					$value,
					$label,
					$meta
				);
		}
	}

	/**
	 * Add Dashboard widget
	 *
	 * @since	2.1.2
	 */
	public function dashboard_widget () {
		wp_add_dashboard_widget('logichop_widget', 'Logic Hop Status', array($this, 'dashboard_widget_display'));
	}

	/**
	 * Display Dashboard widget
	 *
	 * @since	2.1.2
	 * @return	string	Wordpress Dashboard widget
	 */
	public function dashboard_widget_display () {
		$conditions = wp_count_posts('logichop-conditions');
		$condition_count =0;
		if (isset($conditions->publish)) {
			$condition_count = $conditions->publish;
		}

		$goal = wp_count_posts('logichop-goals');
		$goal_count =0;
		if (isset($goal->publish)) {
			$goal_count = $goal->publish;
		}

		printf('<ul class="activity-block" style="padding-bottom: 10px;">
					<li style="display: inline-block; width: 33%%; text-align: center; border-right: 1px solid #eee;">
						<a href="%s"><span class="dashicons dashicons-randomize"></span> %s</a>
					</li>
					<li style="display: inline-block; width: 32%%; text-align: center; border-right: 1px solid #eee;">
						<a href="%s"><span class="dashicons dashicons-awards"></span> %s</a>
					</li>
					<li style="display: inline-block; width: 30%%; text-align: center;">
						<a href="%s"><span class="dashicons dashicons-dashboard"></span> Dashboard</a>
					</li>
				</ul>
				<ul class="activity-block">
					<li style="%s">
						<span class="dashicons dashicons-flag file-error"></span>
						<strong class="">Debug Mode Enabled</strong>
					</li>
					<li>
						<span class="dashicons dashicons-location" style="color: #999; margin-right: 5px;"></span> Geolocation %s
					</li>
					<li>
						<span class="dashicons dashicons-admin-settings" style="color: #999; margin-right: 5px;"></span> CSS Conditions %s
					</li>
					<li>
						<span class="dashicons dashicons-admin-settings" style="color: #999; margin-right: 5px;"></span> Javascript Mode %s
					</li>
					<li>
						<span class="dashicons dashicons-admin-generic" style="color: #999; margin-right: 5px;"></span> <a href="%s">Update Settings</a>
					</li>
				</ul>

				<div class="activity-block" style="padding-top: 15px;">
					<a href="%s" class="button button-primary" style="margin-right: 10px;">Create a Condition</a>
					<a href="%s" class="button button-primary">Create a Goal</a>
				</div>
				',
				$this->plugin_url('conditions'),
				sprintf(_n('%s Condition', '%s Conditions', $condition_count), $condition_count),
				$this->plugin_url('goals'),
				sprintf(_n('%s Goal', '%s Goals', $goal_count), $goal_count),
				$this->plugin_url('dashboard'),
				($this->logic->get_option('debug_mode', false)) ? '' : 'display: none;',
				($this->logic->data_plan()) ? 'Enabled' : 'Disabled <em><small>(Data Plan required)</small></em>',
				($this->logic->get_option('disable_conditional_css', false)) ? 'Disabled' : 'Enabled',
				($this->logic->get_option('js_tracking', false)) ? 'Enabled' : 'Disabled',
				$this->plugin_url('settings'),
				$this->plugin_url('add-condition'),
				$this->plugin_url('add-goal')
			);
	}

	/**
	 * Register plugin settings
	 *
	 * @since	1.0.0
	 * @param	array	$links		Wordpress plugin links
	 * @return	array	Wordpress plugin links
	 */
	public function display_settings_link ($links) {
		$new_links = array();
        $new_links['settings'] = sprintf( '<a href="%s"> %s </a>', $this->plugin_url('settings'), __('Settings', 'plugin_domain') );
        if (!$this->logic->data_plan()) {
        	$new_links['data-plan'] = sprintf( '<a href="%s" target="_blank"> %s </a>', $this->plugin_url('data-plan'), __('Get a License or API Key', 'plugin_domain') );
 		}
 		$new_links['deactivate'] = $links['deactivate'];
 		return $new_links;
	}

	/**
	 * Register admin notices
	 *
	 * @since    1.0.0
	 */
	public function display_admin_notice () {
		global $pagenow;
        if ($pagenow == 'post.php') {
            if (get_post_type() == 'logichop-conditions') {
                printf('<div id="logichop-condition-builder-error" class="notice error" style="display: none;">
				    <p>%s</p>
						</div>',
                    __('<strong>Logic Hop Condition Builder Failed to Load</strong>', 'logichop')
                );
            }
        }

		require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/notices.php');

        $this->logic->apply_expire_notice();
        $this->logic->apply_validation_error_notice();
		$notice_lookup = array();
		$notice_lookup[] = $this->version;

		if ( $this->is_wpengine() ) {
			$notice_lookup[] = 'wpengine';
		}
		if ( $this->is_pantheon() ) {
			$notice_lookup[] = 'pantheon';
		}

        foreach ($notices as $version => $notice ) {
			if ( in_array( $version, $notice_lookup ) ) {
				$display = false;
				$dismiss = '';

				if ( $notice['remember'] ) {
					$option = 'logic-hop-dismiss-notice-' . $version;
					if ( empty( get_option( 'logic-hop-dismiss-notice-' . $version ) ) ) {
						$display = true;
						$dismiss = sprintf( '<p><a href="%s">%s</a></p>',
														add_query_arg( 'logic-hop-dismiss-notice', $version ),
														__( 'Permanently dismiss this notice.', 'logichop' )
													);
					}
				} else {
					$display = true;
				}

				if ( $notice['require_active'] ) {
						if ( ! $this->logic->active() ) {
							$display = false;
						}
				}

				if ( $display ) {
					printf('<div class="notice %s %s">%s %s</div>',
							$notice['type'],
							( $notice['dismissable'] ) ? 'is-dismissible' : '',
							$notice['content'],
							$dismiss
						);
				}
			}
		}

		do_action('logichop_admin_notice');
	}

	/**
	 * Dismiss admin notice
	 *
	 * @since    3.2.2
	 */
	public function dismiss_admin_notice () {
		if ( isset( $_GET['logic-hop-dismiss-notice'] ) ) {
			$notice = sanitize_text_field( $_GET['logic-hop-dismiss-notice'] );
			update_option( 'logic-hop-dismiss-notice-' . $notice, 1 );
		}
	}

	/**
	 * Register Custom Post Types Text Filter.
	 *
	 * @since	1.0.0
	 * @param	array	$messages	Wordpress post messages
	 * @return	array	Wordpress post messages
	 */
	public function custom_post_messages ($messages) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages[$this->plugin_name . '-conditions'] = array(
			0  => '',
			1  => __( 'Condition updated.', 'logichop' ),
			2  => __( 'Custom field updated.', 'logichop' ),
			3  => __( 'Custom field deleted.', 'logichop' ),
			4  => __( 'Condition updated.', 'logichop' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Condition restored to revision from %s', 'logichop' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Condition published.', 'logichop' ),
			7  => __( 'Condition saved.', 'logichop' ),
			8  => __( 'Condition submitted.', 'logichop' ),
			9  => sprintf(
				__( 'Condition scheduled for: <strong>%1$s</strong>.', 'logichop' ),
				date_i18n( __( 'M j, Y @ G:i', 'logichop' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Condition draft updated.', 'logichop' )
		);

		$messages[$this->plugin_name . '-goals'] = array(
			0  => '',
			1  => __( 'Goal updated.', 'logichop' ),
			2  => __( 'Custom field updated.', 'logichop' ),
			3  => __( 'Custom field deleted.', 'logichop' ),
			4  => __( 'Goal updated.', 'logichop' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Goal restored to revision from %s', 'logichop' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Goal published.', 'logichop' ),
			7  => __( 'Goal saved.', 'logichop' ),
			8  => __( 'Goal submitted.', 'logichop' ),
			9  => sprintf(
				__( 'Goal scheduled for: <strong>%1$s</strong>.', 'logichop' ),
				date_i18n( __( 'M j, Y @ G:i', 'logichop' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Goal draft updated.', 'logichop' )
		);

		return $messages;
	}

	/**
	 * Register Menu Pages CSS.
	 *
	 * @since    1.0.0
	 */
	public function menu_pages () {
		add_menu_page(
			'Logic Hop',
			'Logic Hop',
			'edit_theme_options',
			$this->plugin_name . '-menu',
			null,
			plugins_url('images/icon-green.png', __FILE__),
			85
		);

		add_submenu_page(
			$this->plugin_name . '-menu',
			__('Dashboard', 'logichop'),
			__('Dashboard', 'logichop'),
			'manage_options',
			$this->plugin_name . '-dashboard',
			array($this, 'dashboard_page')
		);

        if ( $this->logic->data_plan() ) {
			add_submenu_page(
				$this->plugin_name . '-menu',
				__('Insights', 'logichop'),
				__('Insights', 'logichop'),
				'edit_theme_options',
				$this->plugin_name . '-insights',
				array($this, 'insights_page')
			);
		}

		add_submenu_page(
			$this->plugin_name . '-menu',
			__('Integrations', 'logichop'),
			__('Integrations', 'logichop'),
			'install_plugins',
			$this->plugin_name . '-integrations',
			array($this, 'integrations_page')
		);

		add_submenu_page(
			$this->plugin_name . '-menu',
			__('Settings', 'logichop'),
			__('Settings', 'logichop'),
			'manage_options',
			$this->plugin_name . '-settings',
			array($this, 'settings_page')
		);

		do_action('logichop_admin_menu_pages');
	}

	/**
	 * Re-order sub-menu pages
	 *
	 * @since    2.1.2
	 */
	public function set_menu_page_order ($menu_order) {
		global $submenu;

		if (key_exists('logichop-menu', $submenu)) {
			$new_order = array();
			for ($i = 0; $i < count($submenu['logichop-menu']); $i++) {
				if ($submenu['logichop-menu'][$i][0] == 'Dashboard') {
					array_unshift($new_order, $submenu['logichop-menu'][$i]);
				} else if ($submenu['logichop-menu'][$i][2] == 'logichop-menu') {
					continue;
				} else {
					$new_order[] = $submenu['logichop-menu'][$i];
				}
			}
			$submenu['logichop-menu'] = $new_order;
		}
		return $menu_order;
	}

	/**
	 * Display Dashboard Page.
	 * Include partial file dashboard.php
	 *
	 * @since    2.1.2
	 */
	public function dashboard_page () {
		include_once( plugin_dir_path( dirname(__FILE__) ) . 'admin/partials/dashboard.php');
	}

	/**
	 * Display Insights Page.
	 * Include partial file insights.php
	 *
	 * @since    1.0.0
	 */
	public function insights_page () {
		include_once( plugin_dir_path( dirname(__FILE__) ) . 'admin/partials/insights.php');
	}

	/**
	 * Display Settings Page.
	 * Include partial file settings.php
	 *
	 * @since    1.0.0
	 */
	public function settings_page () {
		global $wp_version;
		include_once( plugin_dir_path( dirname(__FILE__) ) . 'admin/partials/settings.php');
	}

	/**
	 * Display Integrations Page.
	 * Include partial file addons.php
	 *
	 * @since    1.0.0
	 */
	public function integrations_page () {
		global $wp_version;
		include_once( plugin_dir_path( dirname(__FILE__) ) . 'admin/partials/addons.php');
	}

	/**
	 * Override Widget Form
	 *
	 *
	 * @since    1.0.0
	 * @param	 object		$widget			Wordpress widget object
	 * @param	 object		$return			Wordpress widget object
	 * @param	 object		$instance		Wordpress widget objects
	 * @return	 object		Wordpress widget object
	 */
	public function widget_form_override ($widget, $return, $instance) {
		$logichop_widget = isset($instance['logichop_widget']) ? $instance['logichop_widget'] : '';
		$logichop_widget_not = isset($instance['logichop_widget_not']) ? $instance['logichop_widget_not'] : false;

		printf('<p style="margin-bottom: 10px;">
					<label for="%s" style="margin-bottom: 4px;">%s:
					</label>
					<select class="widefat" id="%s" name="%s">
						<option value="">%s</option>
						%s
					</select>
				</p>
				<p>
					<input id="%s" name="%s" type="checkbox" %s>&nbsp;<label for="%s">%s</label>
				</p>',
				$widget->get_field_id('logichop_widget'),
				__('Display if Logic Hop Condition is', 'logichop'),
				$widget->get_field_id('logichop_widget'),
				$widget->get_field_name('logichop_widget'),
				__('Always Display', 'logichop'),
				$this->logic->conditions_get_options($logichop_widget),

				$widget->get_field_id('logichop_widget_not'),
				$widget->get_field_name('logichop_widget_not'),
				($logichop_widget_not) ? 'checked' : '',
				$widget->get_field_name('logichop_widget_not'),
				__('Logic Hop Condition Not Met', 'logichop')
			);
	}

	/**
	 * Override Widget Form
	 *
	 * @since    1.0.0
	 * @param	 object		$instance		Wordpress widget object
	 * @param	 object		$new_instance	Wordpress widget object
	 * @return	 object		Wordpress widget object
	 */
	public function widget_save_override ($instance, $new_instance) {
		if (isset($new_instance['logichop_widget'])) {
			$instance['logichop_widget'] = $new_instance['logichop_widget'];
		}
		if (isset($new_instance['logichop_widget_not'])) {
			$instance['logichop_widget_not'] = true;
		} else {
			$instance['logichop_widget_not'] = false;
		}
		return $instance;
	}

	/**
	 * Add Toolbar Preview Button
	 *
	 * @since    1.0.0
	 * @param	 object		$wp_admin_bar		WordPress Toolbar object
	 */
	public function toolbar_preview_button ( $wp_admin_bar ) {

		$args = array(
				'id' 	=> 'logichop-toolbar',
				'title' => 'Logic Hop',
				'href' 	=> '#',
				'meta' 	=> array(
					'class' 	=> 'logichop-toolbar'
				)
			);
		$wp_admin_bar->add_node( $args );

		if( $this->logic->canDisplayToolbar()) {
			$args = array(
					'parent'	=> 'logichop-toolbar',
					'id' 		=> 'logichop-conditions-button',
					'title' 	=> 'Conditions',
					'href' 		=> admin_url('edit.php?post_type=logichop-conditions')
				);
			$wp_admin_bar->add_node( $args );

			$args = array(
					'parent'	=> 'logichop-toolbar',
					'id' 		=> 'logichop-blocks-button',
					'title' 	=> 'Logic Blocks',
					'href' 		=> admin_url('edit.php?post_type=logichop-logicblocks'),
				);
			$wp_admin_bar->add_node( $args );

			$args = array(
					'parent'	=> 'logichop-toolbar',
					'id' 		=> 'logichop-bars-button',
					'title' 	=> 'Logic Bars',
					'href' 		=> admin_url('edit.php?post_type=logichop-logic-bars'),
				);
			$wp_admin_bar->add_node( $args );

			$args = array(
					'parent'	=> 'logichop-toolbar',
					'id' 		=> 'logichop-goals-button',
					'title' 	=> 'Goals',
					'href' 		=> admin_url('edit.php?post_type=logichop-goals')
				);
			$wp_admin_bar->add_node( $args );

			$args = array(
					'parent'	=> 'logichop-toolbar',
					'id' 		=> 'logichop-redirects-button',
					'title' 	=> 'Redirects',
					'href' 		=> admin_url('edit.php?post_type=logichop-redirects')
				);
			$wp_admin_bar->add_node( $args );

			if ( $this->logic->data_plan()) {
				$args = array(
					'parent'	=> 'logichop-toolbar',
					'id' 		=> 'logichop-insights-button',
					'title' 	=> 'Insights',
					'href' 		=> admin_url('admin.php?page=logichop-insights'),
				);
				$wp_admin_bar->add_node( $args );
			}
		}

		$args = array(
				'parent'	=> 'logichop-toolbar',
				'id' 		=> 'logichop-settings-button',
				'title' 	=> 'Settings',
				'href' 		=> admin_url('admin.php?page=logichop-settings'),
			);
		$wp_admin_bar->add_node( $args );

		if ( is_preview() ) {
			$args = array(
					'parent'	=> 'logichop-toolbar',
					'id' 		=> 'logichop-preview-button',
					'title' 	=> 'Preview Tool',
					'href' 		=> '#',
					'meta' 		=> array(
						'class' 	=> 'logichop-preview-button'
					)
				);
			$wp_admin_bar->add_node( $args );
		}
	}

	/**
	 * Add editor shortcode button
	 * Send directly to HTML via printf()
	 *
	 * @since    1.0.0
	 * @param	 object		$context	Wordpress Editor context
	 * @return	 object		Wordpress Editor context
	 */
	public function editor_buttons ($context) {
		printf('<a href="#" class="button logichop-button logichop-editor" onclick="logichop_modal_open(); return false;"  title="Logic Hop"><img src="%s"> %s</a>',
				plugins_url('images/icon-green.png', __FILE__),
				'L<span>o</span>gic H<span>o</span>p'
			);

		return $context;
    }

	/**
	 * Add editor modal window
	 *
	 * @since    1.0.0
	 * @return		string		Echos admin HTML & Javascript
	 */
	public function editor_shortcode_modal ( $force_display = false ) {
		global $pagenow;

		if ( $force_display || in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php', 'widgets.php' ) ) ) {

			$conditions = $this->logic->conditions_get_options();
			$goals = $this->logic->goals_get_options();
			$blocks = $this->logic->blocks_get_options();

			$tab_conditions = sprintf('<h4>%s</h4>
					<select id="logichop_condition_logic">
						<option value="if">IF</option>
						<option value="if not">IF NOT</option>
						<option value="if else">IF / ELSE</option>
						<option value="if not else">IF NOT / ELSE</option>
						<option value="else if">ELSE IF</option>
						<option value="else if not">ELSE IF NOT</option>
						<option value="else">ELSE</option>
					</select>
					<select id="logichop_condition">
						<option value="">%s</option>
						%s
					</select>
					<p>
						<button class="button button-primary logichop_insert_condition">%s</button>
					</p>
					<hr>

					<h4>Query String Logic</h4>
					<input type="text" style="width: 40%%;" id="logichop_query_logic" placeholder="Query String Variable">
					<select id="logichop_query_operator" style="width: 16%%;">
						<option value="==">IS</option>
						<option value="!=">IS NOT</option>
						<option value="in">IN</option>
					</select>
					<input type="text" style="width: 40%%;" id="logichop_query_value" placeholder="Value">
					<p>
						<button data-type="else" data-if="==" class="button button-primary logichop_insert_query_condition">%s</button>
					</p>
					',
					__('Conditional Logic', 'logichop'),
					($conditions) ? __('Select a condition', 'logichop') : __('No conditions have been created', 'logichop'),
					$conditions,
					__('Insert Conditional Logic', 'logichop'),

					__('Insert Query String Logic', 'logichop')
				);

			$vars_datalist = $this->editor_shortcode_modal_get_vars();

			$tab_data = sprintf('<h4>%s</h4>
								<p>
									<input list="logichop_data_var_list" id="logichop_data" placeholder="%s">
									<datalist id="logichop_data_var_list">
										%s
									</datalist>
								</p>

								<h4>%s</h4>
								<input type="text" id="logichop_data_default" placeholder="">

								<h4>%s</h4>
								<select id="logichop_data_event">
									<option value="">Show</option>
									<option value="fadein">Fade In</option>
									<option value="slidedown">Slide Down</option>
									<option value="hide">Hide</option>
									<option value="fadeout">Fade Out</option>
									<option value="slideup">Slide Up</option>
								</select>
								<h4>%s</h4>
								<select id="logichop_data_case">
									<option value=""></option>
									<option value="lower">Lowercase</option>
									<option value="upper">Uppercase</option>
									<option value="words">Capitalize Words</option>
									<option value="first">Capitalize First Letter</option>
								</select>

								<h4>%s</h4>
								<input type="text" id="logichop_data_class" placeholder="">

								<p>
									<button class="button button-primary logichop_insert_data" data-input="#logichop_data">%s</button>
								</p>',
					__('Variable', 'logichop'),
					__('Logic Hop Variable', 'logichop'),
					$vars_datalist,
					__('Default Value', 'logichop'),
					__('Display', 'logichop'),
					__('Text Case', 'logichop'),
					__('CSS Classes', 'logichop'),
					__('Insert Variable', 'logichop')
				);

			$tab_goals = sprintf('<h4>%s</h4>
                <select id="logichop_goal_delete">
                  <option value="">%s</option>
                  <option value="delete">%s</option>
                </select>
								<select id="logichop_goal">
									<option value="">%s</option>
									%s
								</select>
								<p>
									<button id="logichop_insert_goal" class="button button-primary">%s</button>
								</p>
								<hr>

								<h4>%s</h4>
                <select id="logichop_conditional">
									<option value="">%s</option>
									%s
								</select>
								<select id="logichop_conditional_goal_not">
									<option value="">When Met Trigger Goal</option>
									<option value="!">When NOT Met Trigger Goal</option>
								</select>
                <select id="logichop_conditional_goal_delete">
                  <option value="">%s</option>
                  <option value="delete">%s</option>
                </select>
                <select id="logichop_conditional_goal">
									<option value="">%s</option>
									%s
								</select>
								<p>
									<button id="logichop_insert_conditional_goal" class="button button-primary">%s</button>
								</p>',
          __('Goal', 'logichop'),
          __('Set Goal', 'logichop'), __('Delete Goal', 'logichop'),
					($goals) ? __('Select a goal', 'logichop') : __('No goals have been created', 'logichop'),
					$goals,
					__('Insert Goal', 'logichop'),
					__('Conditional Goal', 'logichop'),
          ($conditions) ? __('Select a condition', 'logichop') : __('No conditions have been created', 'logichop'),
					$conditions,
					__('Set Goal', 'logichop'), __('Delete Goal', 'logichop'),
          ($goals) ? __('Select a goal', 'logichop') : __('No goals have been created', 'logichop'),
					$goals,
					__('Insert Conditional Goal', 'logichop')
				);

			$tab_blocks = sprintf('<h4>%s</h4>
                <select id="logichop_logicblock">
                  <option value="">%s</option>
									%s
                </select>
								<p>
									<button id="logichop_insert_logicblock" class="button button-primary">%s</button>
								</p>',
          __('Logic Blocks', 'logichop'),
          __('Select a Block', 'logichop'),
					$blocks,
					__('Insert Logic Block', 'logichop')
				);

			$tab_navigation = $tab_panel = '';
			$tab_navigation = apply_filters('logichop_editor_modal_nav', $tab_navigation);
			$tab_panel = apply_filters('logichop_editor_modal_panel', $tab_panel);

			printf('<div id="logichop-modal-backdrop"></div>
					<div id="logichop-modal-wrap" class="wp-core-ui has-text-field" role="dialog" aria-labelledby="link-modal-title" style="display: none;" >
						<form class="logichop-modal-form" tabindex="-1">

							<h1 class="logichop-modal-title"><img src="%s"> %s</h1>

							<div class="logichop-modal-close"><span class="screen-reader-text">Close</span></div>

							<div class="logichop-modal-content">
								<h2 class="nav-tab-wrapper">
									<a href="#" id="logichop-modal-conditions-tab" class="nav-tab nav-tab-active" data-tab="logichop-modal-conditions">Conditions</a>
									<a href="#" id="logichop-modal-blocks-tab" class="nav-tab" data-tab="logichop-modal-blocks">Blocks</a>
									<a href="#" id="logichop-modal-goals-tab" class="nav-tab" data-tab="logichop-modal-goals">Goals</a>
									<a href="#" id="logichop-modal-data-tab" class="nav-tab" data-tab="logichop-modal-data">Data</a>
									%s
								</h2>
								<div id="logichop-modal-conditions" class="nav-tab-display logichop-modal-conditions nav-tab-display-active">%s</div>
								<div id="logichop-modal-blocks" class="nav-tab-display logichop-modal-blocks">%s</div>
								<div id="logichop-modal-goals" class="nav-tab-display logichop-modal-goals">%s</div>
								<div id="logichop-modal-data" class="nav-tab-display logichop-modal-data">%s</div>
								%s
							</div>

							<div class="logichop-modal-footer">
								<a href="%s" class="logichop-hide">Add Conditions</a>
								<a href="%s" class="logichop-hide">Add Goals</a>
								<button type="button" class="button logichop-modal-cancel">%s</button>
							</div>
						</form>
					</div>',
					plugins_url('images/icon-green.png', __FILE__),
					__('Logic Hop', 'logichop'),
					$tab_navigation,
					$tab_conditions,
					$tab_blocks,
					$tab_goals,
					$tab_data,
					$tab_panel,
					admin_url('edit.php?post_type=logichop-conditions'),
					admin_url('edit.php?post_type=logichop-goals'),
					__('Cancel', 'logichop')
				);
		}
  }

	/**
	 * Get editor modal window variables
	 *
	 * @since    1.0.0
	 * @return		string		Datalist Options
	 */
	public function editor_shortcode_modal_get_vars () {
		if ($this->logic->data_plan()) {
			$datalist = '<option value=""></option>
					<option value="UserData.user_firstname">User First Name</option>
					<option value="UserData.user_lastname">User Last Name</option>
					<option value="UserData.display_name">User Display Name</option>
					<option value="UserData.user_nicename">User Nice Name</option>
					<option value="UserData.user_email">User Email Address</option>
					<option value="UserData.role">User Role</option>
					<option value="UserData.ID">User ID</option>
					<option value="Query:#var#">Query String</option>
					<option value="QueryStore:#var#">Query String - Stored</option>
					<option value="Location.CountryCode">Country Code (US, CA)</option>
					<option value="Location.CountryName">Country Name</option>
					<option value="Location.RegionCode">Region Code (CA, NY)</option>
					<option value="Location.RegionName">Region Name (California, New York)</option>
					<option value="Location.City">City</option>
					<option value="Location.ZIPCode">ZIP Code</option>
					<option value="Location.TimeZone">Time Zone</option>
					<option value="Location.Latitude">Latitude</option>
					<option value="Location.Longitude">Longitude</option>
					<option value="Location.IP">IP Address</option>
					<option value="UserDate.DayName">User\'s Date: Day</option>
					<option value="UserDate.Day">User\'s Date: Day - Numeric</option>
					<option value="UserDate.MonthName">User\'s Date: Month</option>
					<option value="UserDate.Month">User\'s Date: Month - Numeric</option>
					<option value="UserDate.Year">User\'s Date: Year</option>
					<option value="Date.DayName">Website Date: Day</option>
					<option value="Date.Day">Website Date: Day - Numeric</option>
					<option value="Date.MonthName">Website Date: Month</option>
					<option value="Date.Month">Website Date: Month - Numeric</option>
					<option value="Date.Year">Website Date: Year</option>
					<option value="LandingPage">Landing Page - First Visit</option>
					<option value="LandingPageSession">Landing Page - Current Session</option>
					<option value="LeadScore">Lead Score</option>
					<option value="Source">Source / Referral</option>
					<option value="TotalVisits">Total Visits</option>
					<option value="Language">Language (en, fr)</option>
					<option value="UserAgent">User Agent</option>';
		} else {
			$datalist = '<option value=""></option>
					<option value="UserData.user_firstname">User First Name</option>
					<option value="UserData.user_lastname">User Last Name</option>
					<option value="UserData.display_name">User Display Name</option>
					<option value="UserData.user_nicename">User Nice Name</option>
					<option value="UserData.user_email">User Email Address</option>
					<option value="UserData.role">User Role</option>
					<option value="UserData.ID">User ID</option>
					<option value="Query:#var#">Query String</option>
					<option value="QueryStore:#var#">Query String - Stored</option>
					<option value="UserDate.DayName">User\'s Date: Day</option>
					<option value="UserDate.Day">User\'s Date: Day - Numeric</option>
					<option value="UserDate.MonthName">User\'s Date: Month</option>
					<option value="UserDate.Month">User\'s Date: Month - Numeric</option>
					<option value="UserDate.Year">User\'s Date: Year</option>
					<option value="Date.DayName">Website Date: Day</option>
					<option value="Date.Day">Website Date: Day - Numeric</option>
					<option value="Date.MonthName">Website Date: Month</option>
					<option value="Date.Month">Website Date: Month - Numeric</option>
					<option value="Date.Year">Website Date: Year</option>
					<option value="LandingPage">Landing Page - First Visit</option>
					<option value="LandingPageSession">Landing Page - Current Session</option>
					<option value="LeadScore">Lead Score</option>
					<option value="Source">Source / Referral</option>
					<option value="TotalVisits">Total Visits</option>
					<option value="Language">Language (en, fr)</option>
					<option value="UserAgent">User Agent</option>';
		}
		return apply_filters('logichop_editor_shortcode_variables', $datalist);
	}

	/**
	 * Add custom HTML tags to TinyMCE
	 *
	 * @since    3.0.0
	 * @return	array	TinyMCE initializing variables
	 */
	public function tiny_mce_before_init ( $init ) {

		if ( ! $this->logic->disable_editor_styles() ) {
			$elements = array (
					'logichop-var[*]',
					'logichop-if[*]',
					'logichop-else[*]',
					'logichop-elseif[*]',
					'logichop-endif[*]',
				);
			$init['extended_valid_elements'] = implode( ',', $elements );
		}

		return $init;
	}

	/**
	 * Add plugins to TinyMCE
	 *
	 * @since    3.0.0
	 * @param	array	TinyMCE plugins
	 * @return	array	TinyMCE plugins
	 */
	public function mce_external_plugins ( $plugin_array ) {

    	if ( ! $this->logic->disable_editor_styles() ) {
    		$plugin_array['logichop'] = plugin_dir_url( __FILE__ ) . 'js/tinymce/tinymce.js';
    	}

    	return $plugin_array;
	}

	/**
	 * Add stylesheet for TinyMCE
	 *
	 * @since    3.0.0
	 */
	public function mce_styles () {

		if ( ! $this->logic->disable_editor_styles() ) {
			add_editor_style( plugin_dir_url( __FILE__ ) . 'css/tinymce/tinymce.css' );
		}
	}

	/**
	 * Adds & removes metaboxes
	 *
	 * @since    1.0.0
	 */
	public function configure_metaboxes () {
		
		$remove_excerpts = array( 'logichop-conditions', 'logichop-goals', 'logichop-logic-bars', 'logichop-logicblocks', 'logichop-redirects' );
		remove_meta_box( 'postexcerpt', $remove_excerpts, 'normal' );

		add_meta_box(
			'logichop_metabox',
			sprintf('<img src="%s"> Logic Hop', plugins_url('images/icon-green.png', __FILE__)),
			array($this, 'primary_metabox_display'),
			apply_filters('logichop_metabox_post_types', array('post', 'page')),
			'normal',
			'high',
			array(
        '__back_compat_meta_box' => true,
    		)
			);
		add_meta_box(
			'logichop_condition_builder',
			__('Logic Builder', 'logichop'),
			array($this, 'condition_builder_display'),
			array('logichop-conditions'),
			'advanced',
			'high'
			);
		add_meta_box(
			'logichop_goal_detail',
			__('Goal Description', 'logichop'),
			array($this, 'goal_detail_display'),
			array('logichop-goals'),
			'normal',
			'high'
			);
		add_meta_box(
			'logichop_logicbar_settings',
			__('Logic Bar Settings', 'logichop'),
			array($this, 'logicbar_settings_display'),
			array('logichop-logic-bars'),
			'normal',
			'high'
			);
			add_meta_box(
				'logichop_redirect_settings',
				__('Redirect Settings', 'logichop'),
				array($this, 'redirects_settings_display'),
				array('logichop-redirects'),
				'normal',
				'high'
				);
		add_meta_box(
			'logichop_logicblock_settings',
			__('Logic Block Settings', 'logichop'),
			array($this, 'logicblock_settings_display'),
			array('logichop-logicblocks'),
			'normal',
			'high'
			);

		if (!$this->logic->data_plan()) {
			add_meta_box(
				'logichop_data_plan',
				__('Want to get really personal?', 'logichop'),
				array($this, 'logichop_data_plan_display'),
				array('logichop-conditions', 'logichop-goals'),
				'side'
				);
		}

		do_action('logichop_configure_metaboxes');
	}

	/**
	 * Displays primary metabox on Page & Post editor
	 *
	 * @since    1.0.0
	 * @param		object		$post		Wordpress Post object
	 * @return		string		Echos metabox form
	 *
	 * @since	3.0.0
	 * Deprecated: _logichop_track_page
	 */
	public function primary_metabox_display ($post) {

		$hide_tracking = apply_filters( 'logichop_primary_metabox_tracking', false, $post );
		$hide_tracking = true; // _logichop_track_page Deprecated since 3.0.0

		$values = get_post_custom($post->ID);

		$disable_js_mode 	= isset( $values['_logichop_disable_js_mode'] ) ? esc_attr( $values['_logichop_disable_js_mode'][0] ) : '';

		$lead_score		= isset($values['_logichop_page_leadscore']) 		? esc_attr($values['_logichop_page_leadscore'][0]) 	: 0;
		$lead_freq		= isset($values['_logichop_page_lead_freq']) 		? esc_attr($values['_logichop_page_lead_freq'][0]) 	: 'every';
		$track_page 	= isset($values['_logichop_track_page']) 			? esc_attr($values['_logichop_track_page'][0]) 		: '';

		$condition		= isset($values['_logichop_page_condition'])		? esc_attr($values['_logichop_page_condition'][0])	: '';
		$redirect		= isset($values['_logichop_page_redirect']) 		? esc_attr($values['_logichop_page_redirect'][0]) 			: '';
		$condition_not	= isset($values['_logichop_page_condition_not']) 	? esc_attr($values['_logichop_page_condition_not'][0])		: '';

		$goal			= isset($values['_logichop_page_goal']) 			? esc_attr($values['_logichop_page_goal'][0])				: '';

		$goal_condition		= isset($values['_logichop_page_goal_condition'])		? esc_attr($values['_logichop_page_goal_condition'][0]) 	: '';
		$goal_on_condition	= isset($values['_logichop_page_goal_on_condition'])	? (int) esc_attr($values['_logichop_page_goal_on_condition'][0])	: '';
		$goal_condition_not	= isset($values['_logichop_page_goal_condition_not']) 	? esc_attr($values['_logichop_page_goal_condition_not'][0])	: '';

		$goal_js			= isset($values['_logichop_page_goal_js'])			? (int) esc_attr($values['_logichop_page_goal_js'][0]) 	: '';
		$goal_js_event		= isset($values['_logichop_page_goal_js_event'])	? esc_attr($values['_logichop_page_goal_js_event'][0]) 	: '';
		$goal_js_element	= isset($values['_logichop_page_goal_js_element'])	? esc_attr($values['_logichop_page_goal_js_element'][0])	: '';

		$goals_js		= $this->logic->goals_get_options($goal_js);
		$goal_js_events = $this->logic->javascript_get_events($goal_js_event);
		$conditions_on	= $this->logic->conditions_get_options($goal_condition);
		$goals_on		= $this->logic->goals_get_options($goal_on_condition);
		$conditions		= $this->logic->conditions_get_options($condition);
		$goals			= $this->logic->goals_get_options($goal);

		$goal_on_css = '';
		if ($goal_condition || $goal_on_condition) $goal_on_css = 'half-set';
		if ($goal_condition && $goal_on_condition) $goal_on_css = 'set';

		$goal_js_css = '';
		if ($goal_js || $goal_js_event || $goal_js_element) $goal_js_css = 'half-set';
		if ($goal_js && $goal_js_event && $goal_js_element) $goal_js_css = 'set';

		$redirect_css = '';
		if ($condition || $redirect) $redirect_css = 'half-set';
		if ($condition && $redirect) $redirect_css = 'set';

		wp_nonce_field('_logichop_metabox_nonce', 'meta_box_nonce');

		$disable_js_mode_input = sprintf( '<div class="logichop-meta %s">
										<label><strong>%s</strong></label><a href="#" class="logichop-meta-clear">%s</a><br>
										<select id="_logichop_disable_js_mode" name="_logichop_disable_js_mode" style="width: 100%%;">
											%s
										</select>
										<p><span id="_disable_meta" style="%s">%s</span></p>
									</div>
									<hr>',
									( $disable_js_mode ) ? 'set' : '',
									__( 'Page Render Settings', 'logichop' ), __('Clear', 'logichop'),
									sprintf( '<option value="" %s>%s</option>
													<option value="true" %s>%s</option>',
													( $disable_js_mode ) ? '' : 'selected', __( 'Logic Tags rendered after page load with Javascript', 'logichop' ),
													( $disable_js_mode ) ? 'selected' : '', __( 'Logic Tags rendered before page load with PHP', 'logichop' )
										),
									( $disable_js_mode ) ? '' : 'display: none;', __( 'Logic Tag output will be cached if caching is not disabled for this page.', 'logichop' )
								);

		$tracking_input = sprintf('<div class="logichop-meta %s">
										<label><strong>%s</strong></label><br>
										<select id="_logichop_track_page" name="_logichop_track_page" style="width: 100%%;">
											%s
										</select>
										<p></p>
									</div>
									<hr>',
									($track_page) ? 'set' : '',
									__('Logic Hop Tracking', 'logichop'),
									sprintf('<option value="disabled" %s>%s</option>
											<option value="enabled" %s>%s</option>',
											($track_page) ? '' : 'selected',
											__('Tracking Disabled', 'logichop'),
											($track_page) ? 'selected' : '',
											__('Tracking Enabled', 'logichop')
									)
								);

		printf('<div>

					%s

					<div class="logichop-meta %s">
						<label><strong>%s</strong></label><a href="#" class="logichop-meta-clear">%s</a><br>
						<input type="number" value="%s" id="_logichop_page_leadscore" name="_logichop_page_leadscore" style="">
						<select id="_logichop_page_lead_freq" name="_logichop_page_lead_freq" style="margin-top: -4px;">
		 					<option value="every" %s>%s</option>
		 					<option value="first" %s>%s</option>
		 					<option value="session" %s>%s</option>
		 					<option value="set" %s>%s</option>
		 				</select>
		 				<p></p>
		 			</div>
		 			<hr>

					%s

		 			<div class="logichop-meta %s">
						<label><strong>%s</strong></label><a href="#" class="logichop-meta-clear">%s</a><br>
						<label><strong>%s</strong></label><br>
						<select id="_logichop_page_goal" name="_logichop_page_goal" style="width: 100%%;">
		 					<option value="">%s</option>
		 					%s
		 				</select>
		 				<p></p>
		 			</div>
		 			<hr>

		 			<div class="logichop-meta %s">
						<label><strong>%s</strong></label><a href="#" class="logichop-meta-clear">%s</a><br>
						<label><strong>%s</strong></label><br>
						<select id="_logichop_page_goal_condition" name="_logichop_page_goal_condition" style="width: 100%%;">
							<option value="">%s</option>
							%s
						</select>
						<p></p>
						<label><strong>%s</strong></label><br>
						<select id="_logichop_page_goal_on_condition" name="_logichop_page_goal_on_condition" style="width: 100%%;">
							<option value="">%s</option>
							%s
						</select>
						<p></p>
						<label for="_logichop_page_goal_condition_not" class="selectit">
		 					<input type="checkbox" id="_logichop_page_goal_condition_not" name="_logichop_page_goal_condition_not" %s>
		 					%s
		 				</label>
		 				<p></p>
					</div>
		 			<hr>

		 			<div class="logichop-meta %s">
						<label><strong>%s</strong></label><a href="#" class="logichop-meta-clear">%s</a><br>
						<label><strong>%s</strong></label><br>
						<select id="_logichop_page_goal_js_event" name="_logichop_page_goal_js_event" style="width: 100%%;">
							<option value="">%s</option>
							%s
						</select>
						<p></p>
						<label><strong>%s</strong></label><br>
						<input type="text" value="%s" id="_logichop_page_goal_js_element" name="_logichop_page_goal_js_element" placeholder="%s" style="width: 100%%;">
						<p></p>
						<label><strong>%s</strong></label><br>
						<select id="_logichop_page_goal_js" name="_logichop_page_goal_js" style="width: 100%%;">
							<option value="">%s</option>
							%s
						</select>
						<p></p>
					</div>
		 			<hr>

		 			<div class="logichop-meta %s">
						<label><strong>%s</strong></label><a href="#" class="logichop-meta-clear">%s</a><br>
						<label><strong>%s</strong></label><br>
						<select id="_logichop_page_condition" name="_logichop_page_condition" style="width: 100%%;">
							<option value="">%s</option>
							%s
						</select>
						<p></p>
						<label><strong>%s</strong></label><br>
						<input type="text" value="%s" id="_logichop_page_redirect" name="_logichop_page_redirect" placeholder="%s" style="width: 100%%;">
						<p></p>
						<label for="_logichop_page_condition_not" class="selectit">
		 					<input type="checkbox" id="_logichop_page_condition_not" name="_logichop_page_condition_not" %s>
		 					%s
		 				</label>
		 				<p></p>
					</div>
				</div>',
				( $this->logic->js_tracking() ) ? $disable_js_mode_input : '',
				($lead_score != 0) ? 'set' : '',
				__('Logic Hop Lead Score', 'logichop'),
				__('Clear', 'logichop'),
				$lead_score,
				($lead_freq == 'every') ? 'selected' : '',
				__('Increment on every view', 'logichop'),
				($lead_freq == 'first') ? 'selected' : '',
				__('Increment on first view only', 'logichop'),
				($lead_freq == 'session') ? 'selected' : '',
				__('Increment on first view each session', 'logichop'),
				($lead_freq == 'set') ? 'selected' : '',
				__('Set as Lead Score', 'logichop'),

				($hide_tracking) ? '' : $tracking_input,

				($goal) ? 'set' : '',
				__('Set Goal on Load', 'logichop'),
				__('Clear', 'logichop'),
				__('Set Goal', 'logichop'),
				__('No Goal', 'logichop'),
				$goals,

				$goal_on_css,
				__('Set Goal on Condition', 'logichop'),
				__('Clear', 'logichop'),
				__('On Condition', 'logichop'),
				__('No Condition', 'logichop'),
				$conditions_on,
				__('Set Goal', 'logichop'),
				__('No Goal', 'logichop'),
				$goals_on,
				($goal_condition_not) ? 'checked' : '',
				__('Condition Not Met', 'logichop'),

				$goal_js_css,
				__('Set Goal on Javascript Event', 'logichop'),
				__('Clear', 'logichop'),
				__('Event', 'logichop'),
				__('No Event', 'logichop'),
				$goal_js_events,
				__('Element', 'logichop'),
				$goal_js_element,
				__('Class or ID. Example: .class-name or #id-name', 'logichop'),
				__('Set Goal', 'logichop'),
				__('No Goal', 'logichop'),
				$goals_js,

				$redirect_css,
				__('Redirect on Condition', 'logichop'),
				__('Clear', 'logichop'),
				__('On Condition', 'logichop'),
				__('No Redirect', 'logichop'),
				$conditions,
				__('Redirect to Page/URL', 'logichop'),
				$redirect,
				__('Path or URL', 'logichop'),
				($condition_not) ? 'checked' : '',
				__('Condition Not Met', 'logichop')
			);
	}

	/**
	 * Saves primary metabox data
	 *
	 * @since    1.0.0
	 * @param		integer		$post_id	Post ID
	 */
	public function primary_metabox_save ($post_id) {

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], '_logichop_metabox_nonce')) return;
		if (!current_user_can('edit_post', $post_id)) return;

		$track_page = false;
		if (isset($_POST['_logichop_track_page'])) {
			if ($_POST['_logichop_track_page'] == 'enabled') $track_page = true;
		}
		update_post_meta($post_id, '_logichop_track_page', wp_kses($track_page,''));

		if (isset($_POST['_logichop_disable_js_mode']))	update_post_meta( $post_id, '_logichop_disable_js_mode', wp_kses($_POST['_logichop_disable_js_mode'],''));

		if (isset($_POST['_logichop_page_leadscore'])) {
			$lead_score = (is_numeric($_POST['_logichop_page_leadscore'])) ? $_POST['_logichop_page_leadscore'] : 0;
			update_post_meta($post_id, '_logichop_page_leadscore', $lead_score);
		}
		if (isset($_POST['_logichop_page_lead_freq']))	update_post_meta( $post_id, '_logichop_page_lead_freq', wp_kses($_POST['_logichop_page_lead_freq'],''));

		if (isset($_POST['_logichop_page_goal']))	update_post_meta( $post_id, '_logichop_page_goal', wp_kses($_POST['_logichop_page_goal'],''));

		if (isset($_POST['_logichop_page_goal_condition'])) 	update_post_meta( $post_id, '_logichop_page_goal_condition', wp_kses($_POST['_logichop_page_goal_condition'],''));
		if (isset($_POST['_logichop_page_goal_on_condition'])) 	update_post_meta( $post_id, '_logichop_page_goal_on_condition', wp_kses($_POST['_logichop_page_goal_on_condition'],''));
		$checkbox = (isset($_POST['_logichop_page_goal_condition_not'])) ? true : false;
		update_post_meta($post_id, '_logichop_page_goal_condition_not', wp_kses($checkbox,''));

		if (isset($_POST['_logichop_page_goal_js'])) 			update_post_meta( $post_id, '_logichop_page_goal_js', wp_kses($_POST['_logichop_page_goal_js'],''));
		if (isset($_POST['_logichop_page_goal_js_event'])) 		update_post_meta( $post_id, '_logichop_page_goal_js_event', wp_kses($_POST['_logichop_page_goal_js_event'],''));
		if (isset($_POST['_logichop_page_goal_js_element']))	update_post_meta( $post_id, '_logichop_page_goal_js_element', wp_kses($_POST['_logichop_page_goal_js_element'],''));

		if (isset($_POST['_logichop_page_condition'])) 	update_post_meta( $post_id, '_logichop_page_condition', wp_kses($_POST['_logichop_page_condition'],''));
		if (isset($_POST['_logichop_page_redirect'])) 	update_post_meta( $post_id, '_logichop_page_redirect', wp_kses($_POST['_logichop_page_redirect'],''));
		$checkbox = (isset($_POST['_logichop_page_condition_not'])) ? true : false;
		update_post_meta($post_id, '_logichop_page_condition_not', wp_kses($checkbox,''));
	}

	/**
	 * Displays condition builder metabox on Condition editor
	 *
	 * @since    1.0.0
	 * @param		object		$post		Wordpress Post object
	 * @return		string		Echos metabox form
	 */
	public function condition_builder_display ($post) {

        global $conditions_text;
        require_once( plugin_dir_path( dirname(__FILE__) ) . 'admin/partials/conditions.php');

		$data = ($post->post_excerpt) ? $post->post_excerpt : 'false';

		$condition_vars = '';
		$condition_vars = apply_filters('logichop_condition_builder_vars', $condition_vars);

		$values			= get_post_custom($post->ID);
		$description	= isset($values['logichop_condition_description']) ? esc_attr($values['logichop_condition_description'][0]) : '';
		$css_condition	= isset($values['logichop_css_condition']) ? esc_attr($values['logichop_css_condition'][0]) : '';

		wp_nonce_field('logichop_metabox_description_nonce', 'meta_box_nonce');

		printf('
				<label><strong>%s</strong></label>
				<textarea rows="3" cols="40" id="logichop_condition_description" name="logichop_condition_description" style="width: 100%%; margin-top: 5px;">%s</textarea>
				<p></p>

				<label><strong>%s</strong></label>
				<div class="col-xs-12 col-sm-3 logichop-conditions"></div>
				<p></p>

				<label><strong>%s</strong></label>
				<input type="text" onfocus="this.select();" readonly="readonly" style="width: 100%%; margin-top: 5px;" class="" value="%s">
				<p></p>

				<label><strong>%s</strong></label>
				<input type="text" onfocus="this.select();" readonly="readonly" style="width: 100%%; margin-top: 5px;" class="" value="%s">
				<p></p>

				<hr>

				<p>
					<label for="logichop_css_condition" class="selectit"><input type="checkbox" id="logichop_css_condition" name="logichop_css_condition" %s>
						<strong>%s</strong>
					</label>
				</p>

				<div class="logichop-css %s">
					<label><strong>%s</strong></label>
					<input type="text" onfocus="this.select();" readonly="readonly" style="width: 100%%; margin-top: 5px;" class="" value="%s">
					<p></p>

					<label><strong>%s</strong></label>
					<input type="text" onfocus="this.select();" readonly="readonly" style="width: 100%%; margin-top: 5px;" class="" value="%s">
					<p></p>

					<label><strong>%s</strong></label>
					<input type="text" onfocus="this.select();" readonly="readonly" style="width: 100%%; margin-top: 5px;" class="" value="%s">
					<p></p>
				</div>

				%s
				<textarea rows="3" cols="40" id="excerpt" name="excerpt" style="width: 100%%; margin-top: 5px;" class="logichop-condition-excerpt logichop-condition-excerpt-hide">%s</textarea>

				<script>
					var logichop_data = %s;
					var logichop_text = %s;
					%s
				</script>
				',
				__('Condition Description', 'logichop'),
				$description,
				__('Conditional Statements...', 'logichop'),

				__('Logic Tags', 'logichop'),
				( $post->post_name != '' ) ? sprintf( '{%% if condition: %s %%}{%% endif %%}', $post->post_name ) : __( 'Available after condition is published.', 'logichop' ),

				__('Condition Slug', 'logichop'),
				( $post->post_name != '' ) ? $post->post_name : __( 'Available after condition is published.', 'logichop' ),

				($css_condition) ? 'checked' : '',
				__('Enable Conditional CSS', 'logichop'),

				($css_condition) ? '' : 'logichop-condition-excerpt-hide',
				__('CSS Body Class', 'logichop'),
				( $post->post_name != '' ) ? sprintf( 'lh-%s', $post->post_name ) : __( 'Available after condition is published.', 'logichop' ),
				__('CSS Class', 'logichop'),
				( $post->post_name != '' ) ? sprintf( 'logichop-%s', $post->post_name ) : __( 'Available after condition is published.', 'logichop' ),
				__('CSS Not Class', 'logichop'),
				( $post->post_name != '' ) ? sprintf( 'logichop-not-%s', $post->post_name ) : __( 'Available after condition is published.', 'logichop' ),

				sprintf('<div class="logichop-condition-logic-label"><small><a href="#" class="logichop-condition-logic">%s</a></small></div>', __('Show Conditional Logic', 'logichop')),
				$post->post_excerpt,
				htmlspecialchars_decode($data),
				json_encode($conditions_text),
				$condition_vars
			);
	}

	/**
	 * Saves condition builder metabox data
	 *
	 * @since    1.0.0
	 * @param		integer		$post_id	Post ID
	 */
	public function condition_builder_save ($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'logichop_metabox_description_nonce')) return;
		if (!current_user_can('edit_post', $post_id)) return;

		$checkbox = (isset($_POST['logichop_css_condition'])) ? true : false;
		update_post_meta($post_id, 'logichop_css_condition', wp_kses($checkbox,''));

		if (isset($_POST['logichop_condition_description'])) {
			update_post_meta($post_id, 'logichop_condition_description', wp_kses($_POST['logichop_condition_description'],''));
		}
	}

	/**
	 * Displays goal tracker metabox on Goal editor
	 *
	 * @since    1.0.0
	 * @param		object		$post		Wordpress Post object
	 * @return		string		Echos metabox form
	 */
	public function goal_detail_display ($post) {

		$values			= get_post_custom($post->ID);
		$lead_adjust	= isset($values['logichop_goal_lead_score_adjust']) ? esc_attr($values['logichop_goal_lead_score_adjust'][0]) : 0;
		$lead_freq		= isset($values['logichop_goal_lead_freq']) ? esc_attr($values['logichop_goal_lead_freq'][0]) : 'every';

		printf('
				<textarea rows="3" cols="40" id="excerpt" name="excerpt" style="width: 100%%; margin-top: 5px;" class="">%s</textarea>
				<p></p>

				<label><strong>%s </strong></label>
				<input type="number" style="margin-top: 5px;" class="" id="logichop_goal_lead_score_adjust" name="logichop_goal_lead_score_adjust" value="%s">
				<select id="logichop_goal_lead_freq" name="logichop_goal_lead_freq" style="margin-top: -3px;">
		 			<option value="every" %s>%s</option>
		 			<option value="first" %s>%s</option>
		 			<option value="session" %s>%s</option>
		 			<option value="set" %s>%s</option>
		 		</select>
				<p></p>

				<label><strong>%s</strong></label>
				<input type="text" onfocus="this.select();" readonly="readonly" style="width: 100%%; margin-top: 5px;" class="" value="{{ goal: %s }}">
				<p></p>

				<label><strong>%s</strong></label>
				<input type="text" onfocus="this.select();" readonly="readonly" style="width: 100%%; margin-top: 5px;" class="" value="%s">
				',
				$post->post_excerpt,
				__('Logic Hop Lead Score', 'logichop'),
				$lead_adjust,
				($lead_freq == 'every') ? 'selected' : '',
				__('Increment on every complete', 'logichop'),
				($lead_freq == 'first') ? 'selected' : '',
				__('Increment on first complete only', 'logichop'),
				($lead_freq == 'session') ? 'selected' : '',
				__('Increment on first complete each session', 'logichop'),
				($lead_freq == 'set') ? 'selected' : '',
				__('Set as Lead Score', 'logichop'),
				__('Logic Tag', 'logichop'),
				$post->post_name,
				__('Goal Slug', 'logichop'),
				$post->post_name
			);

		wp_nonce_field('logichop_goal_event_nonce', 'meta_box_nonce');
	}

	/**
	 * Saves goal metabox data
	 *
	 * @since   	2.0.0
	 * @param		integer		$post_id	Post ID
	 */
	public function goal_detail_save ($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'logichop_goal_event_nonce')) return;
		if (!current_user_can('edit_post', $post_id)) return;

		if (isset($_POST['logichop_goal_lead_score_adjust'])) update_post_meta($post_id, 'logichop_goal_lead_score_adjust', wp_kses($_POST['logichop_goal_lead_score_adjust'],''));
		if (isset($_POST['logichop_goal_lead_freq'])) update_post_meta($post_id, 'logichop_goal_lead_freq', wp_kses($_POST['logichop_goal_lead_freq'],''));

		do_action('logichop_event_save', $post_id);
	}

	/**
	 * Displays logic bar metabox on  editor
	 *
	 * @since    3.1.0
	 * @param		object		$post		Wordpress Post object
	 * @return		string		Echos metabox form
	 */
	public function logicblock_settings_display ($post) {

		$values = get_post_custom($post->ID);
		$json_data = $this->meta_get('logichop_logicblock_json', '[]', $values);

		$javascript = $this->meta_get('logichop_logicblock_javascript', '', $values);
		$conditions = $this->logic->conditions_get_options();

		wp_enqueue_media();

		printf('
				<label><strong>%s</strong></label>
				<input type="text" onfocus="this.select();" readonly="readonly" style="width: 100%%; margin-top: 5px;" value=\'[logichop_block id="%s"]\'>
				<p></p>
				<div id="logicblocks">
					<div class="logicblock clearfix" data-block="0">
						<select class="logic" style="width: 100%%; margin-top: 5px;" class="">
							<option value="if">If</option>
						</select>
						<select class="condition" style="width: 100%%; margin-top: 5px;" class="">
							<option value="">Select a Condition</option>
							%s
						</select>
						<select class="condition_not" style="width: 100%%; margin-top: 5px;" class="">
							<option value="met">Condition Met</option>
							<option value="not_met">Condition Not Met</option>
						</select>
						<div class="spacer"></div>
						<label class="content-label">%s</label>
						<textarea class="content content-0" rows="3" cols="40" style="width: 100%%; margin-top: 5px;"></textarea>
						<a href="#" class="button logic-block-media-btn" title="Add Data Variable">Add Media</a>
						<a href="#" class="button logic-block-var-btn" title="Add Data Variable">Add Data Variable</a>
					</div>
					<div class="logicblock clearfix" data-block="1">
						<select class="logic" style="width: 100%%; margin-top: 5px;" class="">
							<option value="endif">End If</option>
							<option value="else">Else</option>
							<option value="elseif">Else If</option>
						</select>
						<select class="condition hidden" style="width: 100%%; margin-top: 5px;" class="">
							<option value="">Select a Condition</option>
							%s
						</select>
						<select class="condition_not hidden" style="width: 100%%; margin-top: 5px;" class="">
							<option value="met">Condition Met</option>
							<option value="not_met">Condition Not Met</option>
						</select>
						<div class="spacer"></div>
						<label class="content-label hidden">%s</label>
						<textarea id="logichop_block" class="content content-1 hidden" rows="3" cols="40" style="width: 100%%; margin-top: 5px;"></textarea>
						<a href="#" class="button logic-block-media-btn hidden" title="Add Data Variable">Add Media</a>
						<a href="#" class="button logic-block-var-btn hidden" title="Add Data Variable">Add Data Variable</a>
					</div>
				</div>

				<div class="logicblock-template hidden clearfix" data-block="">
					<select class="logic" style="width: 100%%; margin-top: 5px;" class="">
						<option value="endif">End If</option>
						<option value="else">Else</option>
						<option value="elseif">Else If</option>
					</select>
					<select class="condition hidden" style="width: 100%%; margin-top: 5px;" class="">
						<option value="">Select a Condition</option>
						%s
					</select>
					<select class="condition_not hidden" style="width: 100%%; margin-top: 5px;" class="">
						<option value="met">Condition Met</option>
						<option value="not_met">Condition Not Met</option>
					</select>
					<div class="spacer"></div>
					<label class="content-label hidden">%s</label>
					<textarea id="logichop_block" class="content hidden" rows="3" cols="40" style="width: 100%%; margin-top: 5px;"></textarea>
					<a href="#" class="button logic-block-media-btn hidden" title="Add Data Variable">Add Media</a>
					<a href="#" class="button logic-block-var-btn hidden" title="Add Data Variable">Add Data Variable</a>
				</div>

				<div class="logicblock">
					<label class="content-label">%s</label>
					<textarea id="logichop_logicblock_javascript" name="logichop_logicblock_javascript" rows="3" cols="40" style="width: 100%%; margin-top: 5px;">%s</textarea>
				</div>

				<input type="hidden" id="logichop_logicblock_json" name="logichop_logicblock_json" value="%s">
				<input type="hidden" id="excerpt" name="excerpt" value="%s">

				<script>
					var logichop_block_data = %s;
					var logichop_post_id = %d;
					// var editor = CodeMirror.fromTextArea( document.getElementById("logichop_block"), { lineNumbers: true, mode: "xml" });
				</script>
				',
				__('Shortcode', 'logichop'),
				$post->post_name,
				$conditions,
				__('Show Content', 'logichop'),
				$conditions,
				__('Show Content', 'logichop'),
				$conditions,
				__('Show Content', 'logichop'),
				__('Javascript', 'logichop'),
				$javascript,
				$json_data,
				$post->post_excerpt,
				html_entity_decode( $json_data, ENT_QUOTES ),
				$post->ID
			);

		wp_nonce_field('logichop_logicblock_nonce', 'meta_box_nonce');
	}

	/**
	 * Saves logic bar metabox data
	 *
	 * @since   	3.1.0
	 * @param		integer		$post_id	Post ID
	 */
	public function logicblock_settings_save ($post_id) {

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'logichop_logicblock_nonce')) return;
		if (!current_user_can('edit_post', $post_id)) return;

		$this->meta_set( 'logichop_logicblock_javascript', $post_id );

		if ( isset( $_POST['logichop_logicblock_json'] ) ) {
			$json = $_POST['logichop_logicblock_json'];
			update_post_meta( $post_id, 'logichop_logicblock_json', $json );
		}

		do_action('logichop_event_save', $post_id);
	}

	/**
	 * Displays logic bar metabox on  editor
	 *
	 * @since    3.1.0
	 * @param		object		$post		Wordpress Post object
	 * @return		string		Echos metabox form
	 */
	public function logicbar_settings_display ($post) {

		$values			= get_post_custom($post->ID);
		$condition	= $this->meta_get('logichop_logicbar_condition', '', $values);
		$condition_not	= $this->meta_get('logichop_logicbar_condition_not', 'met', $values);
		$bg_color		= $this->meta_get('logichop_logicbar_bg_color', '#2fd6e2', $values);
		$font_color = $this->meta_get('logichop_logicbar_font_color', '#000000', $values);
		$link_color = $this->meta_get('logichop_logicbar_link_color', '#ffffff', $values);
		$button_color   = $this->meta_get('logichop_logicbar_button_color', '#026087', $values);
		$sticky   = $this->meta_get('logichop_logicbar_sticky', '', $values);
		$shadow		= $this->meta_get('logichop_logicbar_shadow', '', $values);
		$editor_styles   = $this->meta_get('logichop_logicbar_editor_styles', '', $values);
		$css_styles   = $this->meta_get('logichop_logicbar_css_styles', '', $values);
		$type   = $this->meta_get('logichop_logicbar_type', '', $values);
		$display   = $this->meta_get('logichop_logicbar_display', '', $values);

		if ( ! $css_styles ) {
			$css_styles = sprintf( ".logic-bar-%s {\r\t/* CUSTOM CSS HERE */\r}", $post->post_name );
		}

		$conditions = $this->logic->conditions_get_options( $condition );

		printf('
			<label><strong>%s</strong></label><br>
			<select id="logichop_logicbar_condition" name="logichop_logicbar_condition" style="width: 100%%; margin-top: 5px;" class="">
				<option value="">Select a Condition</option>
				%s
			</select>
			<select id="logichop_logicbar_condition_not" name="logichop_logicbar_condition_not" style="width: 100%%; margin-top: 5px;" class="">
				<option value="met">Condition Met</option>
				<option value="not_met" %s>Condition Not Met</option>
			</select>
			<p></p>

			<label><strong>%s</strong></label><br>
			<div id="logic-bar" class="logichop-logic-bar logic-bar-%s"></div>

				<label><strong>%s</strong></label><br>
				<textarea rows="3" cols="40" id="excerpt" name="excerpt" style="width: 100%%; margin-top: 5px;" class="logic-bar-setting">%s</textarea>
				<p></p>
				<a href="#" class="button" onclick="logichop_modal_open(\'#excerpt\'); return false;" title="Add Data Variable">Add Data Variable</a>
				<p></p>

				<div class="logichop-logicbar-input">
					<label><strong>%s</strong></label><br>
					<input type="text" style="width: 100%%; margin-top: 5px;" class="jscolor logic-bar-setting" id="logichop_logicbar_bg_color" name="logichop_logicbar_bg_color" value="%s">
				</div>

				<div class="logichop-logicbar-input">
					<label><strong>%s</strong></label><br>
					<input type="text" style="width: 100%%; margin-top: 5px;" class="jscolor logic-bar-setting" id="logichop_logicbar_font_color" name="logichop_logicbar_font_color" value="%s">
				</div>

				<div class="logichop-logicbar-input">
					<label><strong>%s</strong></label><br>
					<input type="text" style="width: 100%%; margin-top: 5px;" class="jscolor logic-bar-setting" id="logichop_logicbar_link_color" name="logichop_logicbar_link_color" value="%s">
				</div>

				<div class="logichop-logicbar-input">
					<label><strong>%s</strong></label><br>
					<input type="text" style="width: 100%%; margin-top: 5px;" class="jscolor logic-bar-setting" id="logichop_logicbar_button_color" name="logichop_logicbar_button_color" value="%s">
				</div>

				<div class="logichop-logicbar-input">
					<select style="width: 100%%; margin-top: 5px;" class="logic-bar-setting" id="logichop_logicbar_sticky" name="logichop_logicbar_sticky">
						<option value="">%s</option>
						<option value="disabled" %s>%s</option>
					</select>
				</div>

				<div class="logichop-logicbar-input">
					<select style="width: 100%%; margin-top: 5px;" class="logic-bar-setting" id="logichop_logicbar_shadow" name="logichop_logicbar_shadow">
						<option value="">%s</option>
						<option value="disabled" %s>%s</option>
					</select>
				</div>

				<div class="logichop-logicbar-input !no-float">
					<select style="width: 100%%; margin-top: 5px;" class="logic-bar-setting" id="logichop_logicbar_display" name="logichop_logicbar_display">
						<option value="">%s</option>
						<option value="scroll" %s>%s</option>
						<option value="click" %s>%s</option>
						<option value="exit" %s>%s</option>
						<option value="5000" %s>%s</option>
						<option value="10000" %s>%s</option>
						<option value="15000" %s>%s</option>
						<option value="30000" %s>%s</option>
					</select>
				</div>

				<div class="logichop-logicbar-input !no-float">
					<select style="width: 100%%; margin-top: 5px;" class="logic-bar-setting" id="logichop_logicbar_type" name="logichop_logicbar_type">
						<option value="">%s</option>
						<option value="popup" %s>%s</option>
					</select>
				</div>

				<label><strong>%s</strong></label>
				<input type="text" onfocus="this.select();" readonly="readonly" style="width: 100%%; margin-top: 5px;" class="" value="%s">
				<p></p>

				<label><strong>%s</strong></label><br>
				<textarea rows="3" cols="40" id="logichop_logicbar_css_styles" name="logichop_logicbar_css_styles" style="width: 100%%; margin-top: 5px;" class="logic-bar-setting">%s</textarea>

				<input type="hidden" id="logichop_logicbar_editor_styles" name="logichop_logicbar_editor_styles" value="%s">
				<input type="hidden" id="logichop_logicbar_slug" value="%s">

				<style id="logic-bar-editor-styles"></style>
				<style id="logic-bar-user-styles"></style>

				<script>
					var editor = CodeMirror.fromTextArea( document.getElementById("logichop_logicbar_css_styles"), {
    				lineNumbers: true,
						matchBrackets: true,
						mode: "css"
  				});
				</script>
				',
				__('Display When', 'logichop'), $conditions, ($condition_not == 'not_met') ? 'selected' : '',
				__('Logic Bar Preview', 'logichop'),$post->post_name,
				__('Logic Bar Content', 'logichop'), ( $post->post_excerpt == '' ) ? 'Logic Bar Text <a href="#" class="logic-bar-btn">Click Here</a><button class="logic-bar-close">X</button>' : $post->post_excerpt,
				__('Background Color', 'logichop'), $bg_color,
				__('Font Color', 'logichop'), $font_color,
				__('Link Color', 'logichop'), $link_color,
				__('Button Color', 'logichop'), $button_color,
				__('Sticky Header Enabled', 'logichop'), ( $sticky == 'disabled' ) ? 'selected' : '', __('Sticky Header Disabled', 'logichop'),
				__('Drop Shadow Enabled', 'logichop'), ( $shadow == 'disabled' ) ? 'selected' : '', __('Drop Shadow Disabled', 'logichop'),

				__('Display Immediately', 'logichop'),
					( $display == 'scroll' ) ? 'selected' : '', __('Display On Scroll', 'logichop'),
					( $display == 'click' ) ? 'selected' : '', __('Display On Click', 'logichop'),
					( $display == 'exit' ) ? 'selected' : '', __('Display On Exit Intent', 'logichop'),
					( $display == '5000' ) ? 'selected' : '', __('Display After 5 Seconds', 'logichop'),
					( $display == '10000' ) ? 'selected' : '', __('Display After 10 Seconds', 'logichop'),
					( $display == '15000' ) ? 'selected' : '', __('Display After 15 Seconds', 'logichop'),
					( $display == '30000' ) ? 'selected' : '', __('Display After 30 Seconds', 'logichop'),

				__('Display As Header Bar', 'logichop'), ( $type == 'popup' ) ? 'selected' : '', __('Display As Pop Up', 'logichop'),
				__('CSS Class Name', 'logichop'), ( $post->post_name ) ? sprintf( '.logic-bar-%s', $post->post_name ) : __('Available after publish', 'logichop'),
				__('Additional CSS Styles', 'logichop'), ( $post->post_name ) ? $css_styles : '',
				$editor_styles,
				$post->post_name
			);

		wp_nonce_field('logichop_logicbar_nonce', 'meta_box_nonce');
	}

	/**
	 * Saves logic bar metabox data
	 *
	 * @since   	3.1.0
	 * @param		integer		$post_id	Post ID
	 */
	public function logicbar_settings_save ($post_id) {

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'logichop_logicbar_nonce')) return;
		if (!current_user_can('edit_post', $post_id)) return;

		$this->meta_set( 'logichop_logicbar_condition', $post_id );
		$this->meta_set( 'logichop_logicbar_condition_not', $post_id );
		$this->meta_set( 'logichop_logicbar_bg_color', $post_id );
		$this->meta_set( 'logichop_logicbar_font_color', $post_id );
		$this->meta_set( 'logichop_logicbar_link_color', $post_id );
		$this->meta_set( 'logichop_logicbar_button_color', $post_id );
		$this->meta_set( 'logichop_logicbar_sticky', $post_id );
		$this->meta_set( 'logichop_logicbar_shadow', $post_id );
		$this->meta_set( 'logichop_logicbar_css_styles', $post_id );
		$this->meta_set( 'logichop_logicbar_editor_styles', $post_id );
		$this->meta_set( 'logichop_logicbar_type', $post_id );
		$this->meta_set( 'logichop_logicbar_display', $post_id );

		do_action('logichop_event_save', $post_id);
	}

	/**
	 * Displays redirect metabox on  editor
	 *
	 * @since    3.1.7
	 * @param		object		$post		Wordpress Post object
	 * @return		string		Echos metabox form
	 */
	public function redirects_settings_display ( $post ) {

		$values = get_post_custom($post->ID);
		$id	= $this->meta_get('logichop_redirect_id', '', $values);
		$condition = $this->meta_get('logichop_redirect_condition', '', $values);
		$condition_not = $this->meta_get('logichop_redirect_condition_not', 'met', $values);
		$url = $this->meta_get('logichop_redirect_url', '', $values);
		$type	= $this->meta_get('logichop_redirect_type', '', $values);

		$conditions = $this->logic->conditions_get_options( $condition );

		printf('
			<label><strong>Description</strong></label><br>
			<textarea rows="3" cols="40" id="excerpt" name="excerpt" style="width: 100%%; margin-top: 5px;" class="">%s</textarea>
			<p></p>

			<label><strong>Redirect From</strong></label><br>
			<input class="form-control page_lookup logichop-ajax" type="text" placeholder="Page or Post Title" autocomplete="off" style="width: 100%%; margin-top: 5px;">
			<input name="logichop_redirect_id" id="logichop_redirect_id" class="page logichop-ajax-data" type="hidden" value="%s">
			<p></p>

			<label><strong>Redirect When</strong></label><br>
			<select id="logichop_redirect_condition" name="logichop_redirect_condition" style="width: 100%%; margin-top: 5px;" class="">
				<option value="">Select a Condition</option>
				%s
			</select>
			<select id="logichop_redirect_condition_not" name="logichop_redirect_condition_not" style="width: 100%%; margin-top: 5px;" class="">
				<option value="met">Condition Met</option>
				<option value="not_met" %s>Condition Not Met</option>
			</select>
			<p></p>

			<label><strong>Redirect To</strong></label><br>
			<input type="text" style="width: 100%%; margin-top: 5px;" id="logichop_redirect_url" name="logichop_redirect_url" placeholder="URL or Path" value="%s">
			<div style="text-align: right"><a href="#" class="logic-block-var-btn" title="Add Data Variable">Add Data Variable</a></div>
			<p></p>

			<label><strong>Redirect Status Code</strong></label><br>
			<select id="logichop_redirect_type" name="logichop_redirect_type" style="width: 100%%; margin-top: 5px;" class="">
				<option value="307" %s>Temporary (307)</option>
				<option value="301" %s>Permanent (301)</option>
				<option value="302" %s>Found (302)</option>
				<option value="303" %s>See Other (303)</option>
				<option value="308" %s>Permanent (308)</option>
			</select>
			<p></p>
			',
			$post->post_excerpt,
			$id,
			$conditions, ($condition_not == 'not_met') ? 'selected' : '',
			$url,
			($type == 307) ? 'selected' : '',($type == 301) ? 'selected' : '',($type == 302) ? 'selected' : '',($type == 303) ? 'selected' : '',($type == 308) ? 'selected' : ''
		);

		wp_nonce_field('logichop_redirect_nonce', 'meta_box_nonce');
	}

	/**
	 * Saves redirect metabox data
	 *
	 * @since   	3.1.7
	 * @param		integer		$post_id	Post ID
	 */
	public function redirects_settings_save ( $post_id ) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'logichop_redirect_nonce')) return;
		if (!current_user_can('edit_post', $post_id)) return;

		$this->meta_set( 'logichop_redirect_id', $post_id );
		$this->meta_set( 'logichop_redirect_condition', $post_id );
		$this->meta_set( 'logichop_redirect_condition_not', $post_id );
		$this->meta_set( 'logichop_redirect_url', $post_id );
		$this->meta_set( 'logichop_redirect_type', $post_id );

		do_action('logichop_redirect_save', $post_id);
	}

	/**
	 * Displays Data Plan promotion in sidebar
	 *
	 * @since    	2.1.2
	 * @param		object		$post		Wordpress Post object
	 * @return		string		Echos metabox form
	 */
	public function logichop_data_plan_display ($post) {
		printf('<p>
					Add geolocation & store user data so you can personalize content for returning visitors with a Logic Hop Data Plan.</p>
				<p>
					<a class="button" href="%s" target="_blank">Get your Logic Hop Data Plan</a>
				</p>',
				$this->plugin_url('data-plan')
			);
	}

	/**
	 * Get Active Plugins.
	 *
	 * @since    1.0.0
	 * @return		array		Active Plugin Name/Version
	 */
	public function get_active_plugins ($list = false) {
		$active		= array();

        if ( !function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $output = '<ul class="logichop-ul-blank">';
        if (function_exists('get_plugins')) {
            $plugins = get_plugins();

            foreach ($plugins as $k => $p) {
                if (is_plugin_active($k)) {
                    $active[] = sprintf('%s, %s', $p['Name'], $p['Version']);
                    $output .= sprintf('<li>%s, %s</li>', $p['Name'], $p['Version']);
                }
            }

            if (!$list) return $active;


        }
        $output .= '</ul>';
        return $output;
	}

	/**
	 * Post Lookup
	 * Conditional Builder
	 *
	 * @since    3.0.0
	 * @param		array		Post type slugs
	 * @return		array		Objects with IDs and titles
	 */
	public function post_lookup () {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			$data 		= array();
			$lookup 	= (isset($_POST['lookup'])) ? $_POST['lookup'] : '';
			$post_type 	= (isset($_POST['type'])) ? $_POST['type'] : array( 'post' );
			$query_type = (isset($_POST['query'])) ? $_POST['query'] : 'posts';

			$data_override = false;
			$data_override = apply_filters( 'logichop_admin_post_lookup', $data_override, $lookup, $post_type, $query_type );

			if ( ! $data_override ) {
				if ($query_type == 'posts') {
					$query = new WP_Query( array (
								's' => $lookup,
								'post_type' => $post_type,
								'post_status' => 'publish',
								'posts_per_page' => 10
							)
						);

					if ( $query->have_posts() ) {
						while( $query->have_posts() ) {
							$query->the_post();

							$tmp 		= new stdclass();
							$tmp->id 	= get_the_ID();
							$tmp->title = sprintf( '%s%s',
													get_the_title(),
													( is_array( $post_type ) ) ? sprintf(' (%s)', get_post_type()) : ''
													//(count($post_type) > 1) ? sprintf(' (%s)', get_post_type()) : ''
												);
							$data[] 	= $tmp;
						}
					}
					wp_reset_postdata();
				} else if ($query_type == 'terms') {
					if ( isset( $post_type[0] ) ) {
						if ( $post_type[0] == 'post_tag' ) {
							$post_type = apply_filters( 'logichop_update_data_tags', $post_type );
						}
						if ( $post_type[0] == 'category' ) {
							$post_type = apply_filters( 'logichop_update_data_categories', $post_type );
						}
					}
					$term_query = new WP_Term_Query( array(
										'taxonomy' => $post_type,
										'name__like' => $lookup,
										'hide_empty' => false,
								)
							);

					if (!empty( $term_query->terms ) ) {
	    				foreach ( $term_query->terms as $term ) {
									$tax = ( $term->taxonomy == 'post_tag' ) ? 'tag' : $term->taxonomy;
	        				$tmp 		= new stdclass();
									$tmp->id 	= $term->term_id;
									$tmp->title = sprintf( '%s%s',
															$term->name,
															sprintf(' (%s)', $tax )
											);
									$data[] 	= $tmp;
	    				}
					}
				}
			} else {
				$data = $data_override;
			}
			echo json_encode( $data );
		}
		wp_die();
	}

	/**
	 * Post Title Lookup
	 * Conditional Builder
	 *
	 * @since    3.0.0
	 * @return		object		Post Title
	 */
	public function post_title_lookup () {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			$id 		= (isset($_POST['id'])) ? filter_var( $_POST['id'], FILTER_SANITIZE_NUMBER_INT ) : 0;
			$post_type 	= (isset($_POST['type'])) ? $_POST['type'] : array( 'post' );
			$query_type = (isset($_POST['query'])) ? $_POST['query'] : 'posts';

			$data 			= new stdclass();
			$data->title 	= false;

			$data_override = false;
			$data_override = apply_filters( 'logichop_admin_post_title_lookup', $data_override, $id, $post_type, $query_type );

			if ( ! $data_override ) {
				if ($query_type == 'posts') {
					if ( $post = get_post( $id ) ) {
						$data->title = sprintf( '%s%s',
											$post->post_title,
											(count($post_type) > 1) ? sprintf(' (%s)', $post->post_type) : ''
										);
					}
				} else if ($query_type == 'terms') {
					if ( $term = get_term( $id ) ) {
						$tax = ( $term->taxonomy == 'post_tag' ) ? 'tag' : $term->taxonomy;
						$data->title = sprintf( '%s%s',
											$term->name,
											sprintf(' (%s)', $tax )
										);
					}
				}
			} else {
				$data = $data_override;
			}
			echo json_encode($data);
		}
		wp_die();
	}

	/**
	 * Dashboard Add Recipe Tool
	 *
	 * @since    3.1.3
	 * @return		object		JSON response object
	 */
	public function add_recipe () {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			$types = array( 'condition', 'goal', 'bar', 'block' );
			$statuses = array( 'publish', 'pending', 'draft' );
			$all_goals = $all_conditions = array();
			$goals = $conditions = $blocks = $bars = 0;
			$goals_err = $conditions_err = $blocks_err = $bars_err = 0;
			$response = new stdclass;
			$response->error = false;
			$response->result = 'success';
			$recipe_title = '';
			$recipe_details = '';

			$recipes = ( isset( $_POST['data'] ) ) ? $_POST['data'] : null;

			if ( is_array( $recipes ) ) {
				foreach ( $recipes as $recipe ) {

					$type 	= isset( $recipe['type'] ) ? sanitize_text_field( $recipe['type'] ) : false;
					$slug 	= isset( $recipe['slug'] ) ? sanitize_text_field( $recipe['slug'] ) : false;
					$url 		= isset( $recipe['url'] ) ? sanitize_text_field( $recipe['url'] ) : false;
					$status = isset( $recipe['status'] ) ? sanitize_text_field( $recipe['status'] ) : false;
					$title 	= isset( $recipe['title'] ) ? sanitize_text_field( $recipe['title'] ) : false;
					$desc 	= isset( $recipe['description'] ) ? sanitize_text_field( $recipe['description'] ) : false;
					$data 	= isset( $recipe['data'] ) ? stripslashes( $recipe['data'] ) : false;

					if ( $type == 'recipe' && $title ) {
						$link = ( $url ) ? sprintf( '&ndash; <a href="%s" target="_blank">Full Details</a>', $url ) : '';
						$recipe_title = sprintf( '%s %s', $title, $link );
						$recipe_details = ( $desc ) ? $recipe['description'] : '';
					}

					if ( in_array( $type, $types ) && in_array( $status, $statuses ) && $title && $desc && $slug ) {

						if ( $type == 'goal' ) {
							$goal = get_page_by_path( $slug, OBJECT, 'logichop-goals' );
							$id = ( $goal ) ? $goal->ID : 0;
							$params = array (
								'post_type' => 'logichop-goals',
								'ID' => $id,
								'post_name' => $slug,
								'post_title' => $title,
								'post_status' => $status,
								'post_excerpt' => $desc
							);
							$post = wp_insert_post( $params );
							if ( $post ) {
								$goals++;
								$all_goals[$slug] = $post;
							} else {
								$goals_err++;
							}
						}

						if ( $type == 'condition' && $data && $slug ) {
							$condition = get_page_by_path( $slug, OBJECT, 'logichop-conditions' );
							$id = ( $condition ) ? $condition->ID : 0;
							$params = array (
								'post_type' => 'logichop-conditions',
								'ID' => $id,
								'post_name' => $slug,
								'post_title' => $title,
								'post_status' => $status,
								'post_excerpt' => $this->clean_recipe_data( $data, $all_goals ),
								'meta_input' => array (
									'logichop_condition_description' => $desc
								)
							);
							$post = wp_insert_post( $params );
							if ( $post ) {
								$conditions++;
								$all_conditions[$slug] = $post;
							} else {
								$conditions_err++;
							}
						}

						if ( $type == 'block' && $data && $slug ) {
							$javascript	= isset( $recipe['javascript'] ) ? sanitize_text_field( $recipe['javascript'] ) : '';
							$block = get_page_by_path( $slug, OBJECT, 'logichop-logicblocks' );
							$id = ( $block ) ? $block->ID : 0;
							$params = array (
								'post_type' => 'logichop-logicblocks',
								'ID' => $id,
								'post_name' => $slug,
								'post_title' => $title,
								'post_status' => $status,
								'post_excerpt' => $this->clean_recipe_data( $desc, $all_conditions ),
								'meta_input' => array (
									'logichop_logicblock_json' => $this->clean_recipe_data( $data, $all_conditions ),
									'logichop_logicblock_javascript' => $javascript
								)
							);
							$post = wp_insert_post( $params );
							if ( $post ) {
								$blocks++;
							} else {
								$blocks_err++;
							}
						}

						if ( $type == 'bar' && $data && $slug ) {
							$meta_input = json_decode( $this->clean_recipe_data( $data, $all_conditions ), true );
							if ( is_array( $meta_input ) ) {
								$bar = get_page_by_path( $slug, OBJECT, 'logichop-logic-bars' );
								$id = ( $bar ) ? $bar->ID : 0;
								$params = array (
									'post_type' => 'logichop-logic-bars',
									'ID' => $id,
									'post_name' => $slug,
									'post_title' => $title,
									'post_status' => $status,
									'post_excerpt' => $recipe['description'],
									'meta_input' => $meta_input
								);
								$post = wp_insert_post( $params );
								if ( $post ) {
									$bars++;
								} else {
									$bars_err++;
								}
							}
						}
					}

					$response->msg = sprintf( '<h4 class="recipe-title">Recipe Added: %s</h4>%s<ul class="recipe-list">', $recipe_title, $recipe_details );
					if ( $goals > 0 ) $response->msg .= $this->format_recipe_response( 'Goal', $goals, 'logichop-goals' );
					if ( $goals_err > 0 ) $response->msg .= $this->format_recipe_response( 'Goal', $goals_err, 'logichop-goals', true );
					if ( $conditions > 0 ) $response->msg .= $this->format_recipe_response( 'Condition', $conditions, 'logichop-conditions' );
					if ( $conditions_err > 0 ) $response->msg .= $this->format_recipe_response( 'Condition', $conditions_err, 'logichop-conditions', true );
					if ( $blocks > 0 ) $response->msg .= $this->format_recipe_response( 'Logic Block', $blocks, 'logichop-logicblocks' );
					if ( $blocks_err > 0 ) $response->msg .= $this->format_recipe_response( 'Logic Block', $blocks_err, 'logichop-logicblocks', true );
					if ( $bars > 0 ) $response->msg .= $this->format_recipe_response( 'Logic Bar', $bars, 'logichop-logic-bars' );
					if ( $bars_err > 0 ) $response->msg .= $this->format_recipe_response( 'Logic Bar', $bars_err, 'logichop-logic-bars', true );
					$response->msg .= '</ul>';
				}
			}

			if ( $response->error ) {
				$response->msg = 'There was an error adding this recipe.';
			}
			echo json_encode( $response );
		}
		wp_die();
	}

	/**
	 * Recipe condition slug-to-ID find and replace
	 *
	 * @since    3.1.3
	 * @param			string 	$name
	 * @param			integer 	$count
	 * @param			string 	$post_type
	 * @param			boolean 	$failed
	 * @return		string		Return message
	 */
	public function format_recipe_response ( $name, $count, $post_type, $failed = false ) {
		if ( ! $failed ) {
			if ( $count == 1 ) {
				$msg = sprintf( '%s %s was added.', $count, $name );
			} else {
				$msg = sprintf( '%s %ss were added.', $count, $name );
			}
			$response = sprintf( '<a href="%s">%s</a>', get_admin_url( null, 'edit.php?post_type=' . $post_type ), $msg );
		} else {
			if ( $count == 1 ) {
				$response = sprintf( '%s %s failed to be added.', $count, $name );
			} else {
				$response = sprintf( '%s %ss failed to be added.', $count, $name );
			}
		}
		return sprintf( '<li>%s</li>', $response );
	}

	/**
	 * Recipe condition slug-to-ID find and replace
	 *
	 * @since    3.1.3
	 * @return		object		JSON response object
	 */
	public function clean_recipe_data ( $data, $lookup ) {
		foreach ( $lookup as $slug => $id ) {
			$data = str_replace( $slug, $id, $data );
		}
		return $data;
	}

	/**
	 * Register Admin CSS.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles ($hook) {
		global $post_type;

		if (in_array($hook, array('post.php', 'post-new.php', 'logic-hop_page_logichop-insights', 'logic-hop_page_logichop-settings', 'widgets.php'))) {
			wp_enqueue_style('thickbox');
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.min.css', array(), $this->version, 'all' );
		}

		if ($post_type == 'logichop-conditions' && in_array($hook, array('post.php', 'post-new.php'))) {
			wp_enqueue_style( $this->plugin_name . '-cb', plugin_dir_url( __FILE__ ) . 'css/builder.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'jquery-auto-complete', plugin_dir_url( __FILE__ ) . 'css/jquery.auto-complete.css', array(), $this->version, 'all' );
		}

		if ($post_type == 'logichop-redirects' && in_array($hook, array('post.php', 'post-new.php'))) {
			wp_enqueue_style( $this->plugin_name . '-cb', plugin_dir_url( __FILE__ ) . 'css/builder.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'jquery-auto-complete', plugin_dir_url( __FILE__ ) . 'css/jquery.auto-complete.css', array(), $this->version, 'all' );
		}

		if (in_array($post_type, array('logichop-logic-bars', 'logichop-logicblocks')) && in_array($hook, array('post.php', 'post-new.php'))) {
			wp_enqueue_style( 'codemirror', plugin_dir_url( __FILE__ ) . 'css/codemirror/codemirror.css', array(), $this->version, 'all' );
		}

		do_action('logichop_admin_enqueue_styles', $hook);
	}

	/**
	 * Register Admin JavaScript.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts ($hook) {
		global $post_type;

		if ( $post_type == 'logichop-conditions' && in_array($hook, array('post.php', 'post-new.php')) ) {
			// CONDITION BUILDER
			wp_enqueue_script( 'jquery-auto-complete', plugin_dir_url( __FILE__ ) . 'js/jquery.auto-complete.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/condition-builder.min.js', array( 'jquery' ), $this->version, false );

	   		$js_params = array(
					'ajaxurl'		=> admin_url('admin-ajax.php'),
					'conditions'	=> $this->logic->get_builder(),
				);
			wp_localize_script( $this->plugin_name, 'logichop', $js_params);
			wp_dequeue_script('autosave');

		} else if ( in_array($hook, array('post.php', 'post-new.php', 'widgets.php')) ) {
			// LOGIC HOP EDITOR MODAL
			wp_enqueue_script('thickbox');
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/editor.min.js', array( 'jquery' ), $this->version, false );

		} else if ($hook == 'logic-hop_page_logichop-insights') {
			// INSIGHTS
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/Chart.min.js', array( 'jquery' ), $this->version, false );
		}

		if ( in_array($post_type, array('logichop-logic-bars', 'logichop-logicblocks')) && in_array($hook, array('post.php', 'post-new.php')) ) {
			// LOGIC BARS
			wp_enqueue_script( 'jscolor', plugin_dir_url( __FILE__ ) . 'js/jscolor.js', array(), $this->version, false );
			wp_enqueue_script( 'codemirror', plugin_dir_url( __FILE__ ) . 'js/codemirror/codemirror.js', array(), $this->version, false );
			wp_enqueue_script( 'codemirror-css', plugin_dir_url( __FILE__ ) . 'js/codemirror/css.js', array(), $this->version, false );
			wp_enqueue_script( 'codemirror-xml', plugin_dir_url( __FILE__ ) . 'js/codemirror/xml.js', array(), $this->version, false );
			wp_enqueue_script( 'codemirror-matchbrackets', plugin_dir_url( __FILE__ ) . 'js/codemirror/matchbrackets.js', array(), $this->version, false );
			if ( $post_type == 'logichop-logic-bars' ) {
				wp_enqueue_script( 'logic-bar', plugin_dir_url( __FILE__ ) . 'js/logic-bar.js', array( 'jquery' ), $this->version, false );
			}
			if ( $post_type == 'logichop-logicblocks' ) {
				wp_enqueue_script( 'logic-block', plugin_dir_url( __FILE__ ) . 'js/logic-block.js', array( 'jquery' ), $this->version, false );
			}
		}

		if ( $post_type == 'logichop-redirects' && in_array($hook, array('post.php', 'post-new.php')) ) {
			wp_enqueue_script( 'logichop-redirects', plugin_dir_url( __FILE__ ) . 'js/redirects.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'jquery-auto-complete', plugin_dir_url( __FILE__ ) . 'js/jquery.auto-complete.min.js', array( 'jquery' ), $this->version, false );

	   	$js_params = array(
				'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
				'post_types' => array( 'post', 'page ' ),
			);
			wp_localize_script( $this->plugin_name, 'logichop', $js_params);
			wp_dequeue_script('autosave');
		}

		do_action('logichop_admin_enqueue_scripts', $hook, $post_type);
	}

	/**
	 * Get meta value utility
	 *
	 * @since    3.1.0
	 */
	public function meta_get ( $var, $default, $values ) {
		return isset( $values[$var] ) ? esc_attr( $values[$var][0] ) : $default;
	}

	/**
	 * Set meta value utility
	 *
	 * @since    3.1.0
	 */
	public function meta_set ( $var, $post_id ) {
		if ( isset( $_POST[$var] ) ) {
			update_post_meta( $post_id, $var, wp_kses( $_POST[$var], '' ) );
		}
	}

	/**
	 * Get Conditions
	 *
	 * @since    3.1.9
	 * @return      array    	Array of Conditions
	 */
	public function conditions_get ( $slug = false, $key_slug = 'slug', $key_name = 'name' ) {
		return $this->logic->conditions_get( $slug, $key_slug, $key_name );
	}

	/**
	 * Add duplicate post link
	 *
	 * @since    3.3.4
	 * @param				$actions			Array of page actions
	 * @param				$post					Object of post data
	 * @return      $actions    	Array of page actions
	 */
	public function duplicate_post_link ( $actions, $post ) {
		if ( strrpos( $post->post_type, 'logichop' ) !== false ) {
			if ( current_user_can( 'edit_posts' ) ) {
				$actions['duplicate'] = sprintf( '<a href="%s">%s</a>',
																					wp_nonce_url( 'admin.php?action=logichop_duplicate_post&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ),
																					__( 'Duplicate', 'logichop' )
																			);
			}
    }
    return $actions;
	}

	/**
	 * Duplicate post link
	 *
	 * @since    3.3.4
	 */
	public function logichop_duplicate_post () {
		global $wpdb;

		if ( ! ( isset( $_GET['post']) || isset( $_POST['post'] )  || ( isset( $_REQUEST['action'] ) && 'logichop_duplicate_post' == $_REQUEST['action'] ) ) ) {
			wp_die( 'Duplication Error' );
		}

		if ( ! isset( $_GET['duplicate_nonce'] ) || ! wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) ) {
			wp_die( 'Duplication Error' );
		}

		$post_id = ( isset($_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
		$post = get_post( $post_id );

		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;

		if ( isset( $post ) && $post != null ) {
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => sprintf( '%s-duplicate', $post->post_name ),
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => sprintf( '%s DUPLICATE', $post->post_title ),
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);

			$new_post_id = wp_insert_post( $args );

			$taxonomies = get_object_taxonomies( $post->post_type );
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}

			$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = $post_id" );
			if ( count( $post_meta_infos ) != 0 ) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

				foreach ( $post_meta_infos as $meta_info ) {
					$meta_key = $meta_info->meta_key;
					if ( $meta_key == '_wp_old_slug' ) {
						continue;
					}
					$meta_value = addslashes( $meta_info->meta_value );
					$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}
				$sql_query .= implode( " UNION ALL ", $sql_query_sel );
				$wpdb->query( $sql_query );
			}

			wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
			exit;
		} else {
			wp_die( 'Duplication Error' );
		}
	}

	/**
	 * Update slug from title
	 *
	 * @since    3.3.4
	 * @param				$data			Array of post data
	 * @param				$postarr	Array of un-formatted post data
	 * @return      $data    	Array of post data
	 */
	public function update_slug_from_title ( $data , $postarr ) {

		if ( strrpos( $data['post_type'], 'logichop' ) !== false ) {
			$slug_parts = explode( '-', $data['post_name'] );
			if ( end( $slug_parts ) == 'duplicate' ) {
				$new_slug = sanitize_title_with_dashes( $data['post_title'] );
				$data['post_name'] = $new_slug;
			}
		}

		return $data;
	}

	/**
	 * Add privacy policy content for GDPR
	 *
	 * @since    3.0.0
	 */
	public function privacy_policy_content () {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
        	return;
    	}

		$content = sprintf(
			__( '
<h2>Logic Hop Data Protection & Privacy</h2>
Logic Hop strives to be fully GDPR compliant and it is our commitment to provide the highest standard of user privacy, data protection and transparency.

Logic Hop provides content personalization services to WordPress-powered websites. Our service gives websites the tools to personalize their content based on user activity. Logic Hop can generate the following data for each user:

<ul>
	<li>Geolocation, using IP address (geolocation is never stored)</li>
	<li>Referring URL (the URL a visitor came from)</li>
	<li>Landing page (the URL a visitor entered the website on)</li>
	<li>Page(s) viewed</li>
	<li>Goal(s) completed</li>
	<li>Lead scores</li>
	<li>User agent (browser type)</li>
</ul>

<strong>All Logic Hop data is fully anonymized and no personally identifiable data is ever stored by Logic Hop.</strong> While we do provide Geolocation based on visitors’ IP Addresses, <strong>location information is provided in real time and never stored.</strong> Additionally, the last octet of IPv4 addresses and the last 80 bits of IPv6 addresses are set to zero to anonymize all IP Addresses – This is the same technique Google uses.

Websites using Logic Hop have the option to set a cookie to associate the anonymized data with returning visitors. Cookie consent and storage duration is up to the individual websites. Cookies are set by the website, and never by Logic Hop.

<strong>Logic Hop provides tools for each website to:</strong>

<ul>
	<li>Disable data storage for all users
	<li>Require cookie consent for users located in EU countries before storing data
	<li>Require cookie consent for all users before storing data
</ul>

<h3>Accessing Your Data</h3>
If you have visited a website that uses Logic Hop and would like to view and/or delete your data, please visit <a href="%s">%s</a>.
			', 'logichop' ),
        	'https://logichop.com/data-protection-and-privacy/',
        	'https://logichop.com/data-protection-and-privacy/'
    	);

		wp_add_privacy_policy_content(
        	'Logic Hop',
        	wp_kses_post( wpautop( $content, false ) )
    	);
	}
}
