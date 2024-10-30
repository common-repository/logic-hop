<?php

/**
 * Core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    LogicHop
 * @subpackage LogicHop/includes
 * @author     LogicHop <info@logichop.com>
 */
class LogicHop {
    private static $instance;
	/**
	 * The class that's responsible for core functionality & logic
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      LogicHop_Core    $logic    Core functionality & logic.
	 */
	public $logic;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      LogicHop_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Plugin basename
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_basename    Plugin basename.
	 */
	protected $plugin_basename;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Time modifier for cookie expiration.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cookie_ttl    strtotime modifier.
	 */
	protected $cookie_ttl;

	/**
	 * Maximum number of pages in path history.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      integer    $path_max    Maximum number of pages in path history.
	 */
	protected $path_max;

	/**
	 * API URL
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $api_url    API URL.
	 */
	protected $api_url;

	/**
	 * Debug mode enabled
	 *
	 * @since    1.1.0
	 * @access   public
	 * @var      boolean    $debug
	 */
	public $debug;

	/**
	 * LogicHop core functionality.
	 *
	 * @since    1.0.0
	 */
    public function __construct ($plugin_basename) {
		$this->plugin_basename = $plugin_basename;
		$this->plugin_name 	= 'logichop';
		$this->version 		= Version;
		$this->cookie_ttl  	= '+ 1 year';
		$this->path_max 	= 5;
		$this->api_url 		= 'https://spf.logichop.com/v4.3/data';
		$this->debug		= false;

		$this->load_dependencies();
		$this->set_locale();

        $applyHook= $this->logic->license_handler();
        $this->define_core_hooks();
		$this->define_admin_hooks();
        if ($applyHook) {
            $this->define_public_hooks();
            $this->define_blocks_hooks();
        }

	}

	/**
	 * Load dependencies.
	 *
	 * - Loader: Orchestrates the hooks of the plugin.
	 * - Core: Defines all hooks for core functionality.
	 * - i18n: Defines internationalization functionality.
	 * - Admin: Defines all hooks & filters for the admin area.
	 * - Public: Defines all hooks & filters for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies () {

		/**
		 * Core Functionality
		 * Required classes & libraries
		 */
		// DATA FACTORY
 		if ( ! class_exists( 'LogicHop\DataFactory' ) ) {
 			require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/DataFactory.php';
 		}
		// JSON LOGIC
		if ( ! class_exists( 'LogicHop\JsonLogic' ) ) {
			require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/classes/JsonLogic/JsonLogic.php';
		}
		// MOBILE DETECTION
		if ( ! class_exists( 'Mobile_Detect' ) ) {
			require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/classes/Mobile_Detect/Mobile_Detect.php';
		}
		// CRAWLER DETECTION
		if ( ! class_exists( 'Jaybizzle\CrawlerDetect\CrawlerDetect' ) ) {
			require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/classes/CrawlerDetect/CrawlerDetect.php';
			require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/classes/CrawlerDetect/Fixtures/AbstractProvider.php';
			require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/classes/CrawlerDetect/Fixtures/Crawlers.php';
			require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/classes/CrawlerDetect/Fixtures/Exclusions.php';
			require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/classes/CrawlerDetect/Fixtures/Headers.php';
		}
		// OS DETECTION
		if ( ! class_exists( 'OS_Detect' ) ) {
			require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/classes/OS_Detect/OS_Detect.php';
		}
		/**
		 * Core
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/core.php';

		/**
		 * Services
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/services/geo_ip.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/services/VisualComposer/VisualComposer.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/services/SiteOriginPageBuilder/SiteOriginPageBuilder.php';

		/**
		 * Actions & Filters
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/loader.php';

		/**
		 * Template Parser Functionality
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/parse.php';

		/**
		 * Internationalization
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/i18n.php';

		/**
		 * Admin Functionality
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'admin/admin.php';

		/**
		 * Public Functionality
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'public/public.php';

		/**
		 * Gutenberg Functionality
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'blocks/blocks.php';

		$this->loader = new LogicHop_Loader();
		$this->logic = new LogicHop_Core(   $this->plugin_basename,
											$this->get_plugin_name(),
											$this->get_version(),
											$this->cookie_ttl,
											$this->path_max,
											$this->api_url,
											$this->debug
										);
	}
	/**
	 * Define locale for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale () {
		$i18n = new LogicHop_i18n($this->get_plugin_name());
		$this->loader->add_action( 'plugins_loaded', $i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the core functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_core_hooks () {
		$this->loader->add_action( 'init', $this->logic, 'initialize_core', 5 );
		$this->loader->add_action( 'upgrader_process_complete', $this->logic, 'upgrade_completed', 10, 2 );
		$this->loader->add_action( 'logichop_purge_transients', $this->logic, 'purge_transients', 10, 2 );
		$this->loader->add_filter( 'logichop_before_cookie_create', $this->logic, 'consent_check', 11 );
		$this->loader->add_filter( 'logichop_before_data_remote_put', $this->logic, 'consent_check', 11 );
        do_action( 'logichop_after_core_hooks', $this->logic );
	}

	/**
	 * Register all of the hooks & filters related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks () {
		$plugin_admin = new LogicHop_Admin( $this->logic,
											$this->plugin_basename,
											$this->get_plugin_name(),
											$this->get_version()
										);

		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'dashboard_widget' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_register' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'dismiss_admin_notice' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_admin_notice' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'menu_pages' );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'editor_shortcode_modal' );
		$this->loader->add_action( 'updated_option', $plugin_admin, 'settings_updated', 10, 3 );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'configure_metaboxes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'condition_builder_save' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'primary_metabox_save' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'goal_detail_save' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'logicblock_settings_save' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'logicbar_settings_save' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'redirects_settings_save' );
		$this->loader->add_action( 'wp_ajax_post_lookup', $plugin_admin, 'post_lookup' );
		$this->loader->add_action( 'wp_ajax_post_title_lookup', $plugin_admin, 'post_title_lookup' );
		$this->loader->add_action( 'wp_ajax_logichop_add_recipe', $plugin_admin, 'add_recipe' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'privacy_policy_content' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'toolbar_preview_button', 100 );
		$this->loader->add_action( 'admin_action_logichop_duplicate_post', $plugin_admin, 'logichop_duplicate_post' );
		$this->loader->add_action( 'wp_ajax_logichop_trial',$plugin_admin, 'logichop_trial'  );

		$this->loader->add_filter( 'plugin_action_links_' . $this->plugin_basename, $plugin_admin, 'display_settings_link' );
		$this->loader->add_filter( 'custom_menu_order', $plugin_admin, 'set_menu_page_order' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'custom_post_messages' );
		$this->loader->add_filter( 'in_widget_form', $plugin_admin, 'widget_form_override', 10, 3 );
		$this->loader->add_filter( 'widget_update_callback', $plugin_admin, 'widget_save_override', 10, 2 );
		$this->loader->add_filter( 'media_buttons', $plugin_admin, 'editor_buttons' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'mce_styles' );
		$this->loader->add_filter( 'tiny_mce_before_init', $plugin_admin, 'tiny_mce_before_init' );
		$this->loader->add_filter( 'mce_external_plugins', $plugin_admin, 'mce_external_plugins' );
		$this->loader->add_filter( 'page_row_actions', $plugin_admin, 'duplicate_post_link', 10, 2 );
		$this->loader->add_filter( 'wp_insert_post_data', $plugin_admin, 'update_slug_from_title', 10, 2 );

		new LogicHop_SiteOriginPageBuilder($this->logic);
		new LogicHop_VisualComposer($this->logic);

		do_action( 'logichop_after_admin_hooks', $plugin_admin );
	}

	/**
	 * Register all of the hooks & filters related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks () {

		$plugin_public = new LogicHop_Public( $this->logic, $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );

		$this->loader->add_action( 'wp_ajax_logichop_parse_logic', $plugin_public, 'logichop_parse_logic' );
		$this->loader->add_action( 'wp_ajax_nopriv_logichop_parse_logic', $plugin_public, 'logichop_parse_logic' );

		$this->loader->add_action( 'wp_ajax_logichop_goal', $plugin_public, 'logichop_goal' );
		$this->loader->add_action( 'wp_ajax_nopriv_logichop_goal', $plugin_public, 'logichop_goal' );
		$this->loader->add_action( 'wp_ajax_logichop_page_view', $plugin_public, 'logichop_page_view' );
		$this->loader->add_action( 'wp_ajax_nopriv_logichop_page_view', $plugin_public, 'logichop_page_view' );
		$this->loader->add_action( 'wp_ajax_logichop_condition', $plugin_public, 'logichop_condition' );
		$this->loader->add_action( 'wp_ajax_nopriv_logichop_condition', $plugin_public, 'logichop_condition' );
		$this->loader->add_action( 'wp_ajax_logichop_conditional_css', $plugin_public, 'logichop_conditional_css' );
		$this->loader->add_action( 'wp_ajax_nopriv_logichop_conditional_css', $plugin_public, 'logichop_conditional_css' );
		$this->loader->add_action( 'wp_ajax_logichop_data', $plugin_public, 'logichop_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_logichop_data', $plugin_public, 'logichop_data' );
		$this->loader->add_action( 'wp_ajax_logichop_data_debug', $plugin_public, 'logichop_data_debug' );
		$this->loader->add_action( 'wp_ajax_nopriv_logichop_data_debug', $plugin_public, 'logichop_data_debug' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'template_level_parsing' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'output_header_css' );
		$this->loader->add_action( 'wp_login', $plugin_public, 'wp_user_login', 10, 2 );
		$this->loader->add_action( 'wp_logout', $plugin_public, 'wp_user_logout' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_filter( 'the_title', $plugin_public, 'content_filter' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'content_filter' );
		$this->loader->add_filter( 'the_excerpt', $plugin_public, 'content_filter' );
		$this->loader->add_filter( 'widget_title', $plugin_public, 'content_filter' );
		$this->loader->add_filter( 'widget_text', $plugin_public, 'content_filter' );

		$this->loader->add_filter( 'dynamic_sidebar_params', $plugin_public, 'widget_display_callback' );
		$this->loader->add_filter( 'body_class', $plugin_public, 'body_class_insertion' );

		$this->loader->add_action( 'wp_footer', $plugin_public, 'render_logic_bars' );

		do_action( 'logichop_after_public_hooks', $plugin_public );
	}

	/**
	 * Register all of the hooks & filters related to the Gutenberg functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_blocks_hooks () {
		$plugin_blocks = new LogicHop_Blocks( $this->logic,
											$this->plugin_basename,
											$this->get_plugin_name(),
											$this->get_version()
									);

		$this->loader->add_action( 'init', $plugin_blocks, 'register_dynamic_blocks' );
		$this->loader->add_action( 'init', $plugin_blocks, 'register_meta_fields' );
		$this->loader->add_action( 'logichop_content_filter', $plugin_blocks, 'parse_blocks' );
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_blocks, 'enqueue_block_editor_assets' );

		$this->loader->add_filter( 'is_protected_meta', $plugin_blocks, 'protected_meta_fields', 10, 2 );
	}

	/**
	 * Initialize LogicHop to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function init () {
		$this->loader->run();
	}

	/**
	 * @since     1.0.0
	 * @return    string    Plugin name
	 */
	public function get_plugin_name () {
		return $this->plugin_name;
	}

	/**
	 * Reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader () {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    Plugin version number
	 */
	public function get_version () {
		return $this->version;
	}

	/**
	 * Log Activation
	 *
	 * Updates client meta on plugin activation during plugin upgrade.
	 * Will not log on initial install without API Key added.
	 *
	 * @since    	1.5.0
	 */
	public function log_activation () {
		$this->logic->update_client_meta();
	}

	/**
	 * Set Logic Hop data
	 *
	 * Data added to Data Factory $data.Custom.##$key##
	 *
	 * @since    	3.1.5
	 * @param  		string	$key		Data key - valid PHP variable names only
	 * @param  		object	$data		Data value
	 * @return  	boolean	Data added
	 */
	public function set_data ( $key, $value ) {
		return $this->logic->data_set( $key, $value );
	}

	/**
	 * Update Logic Hop data
	 *
	 * Update existing data
	 *
	 * @since    	3.1.5
	 * @param  		string	$key		Data key - valid PHP variable names only
	 * @param  		object	$data		Data value
	 * @return  	boolean	Data added
	 */
	public function set_value ( $key, $value ) {
		return $this->logic->set_value( $key, $value );
	}

	/**
	 * Return Logic Hop data
	 *
	 * Data extracted from Data Factory $data
	 * Accepts [logichop_data vars=""]
	 * Parameter 'vars' accepts '.' delimited object elements and ':' delimited array elements
	 * Example: Date.DateTime OR QueryStore:ref
	 *
	 * @since    	1.0.9
	 * @param  		string	$var		Data path
	 * @param  		boolean	$echo		Switch to echo or retrun data
	 * @return  	null or content		Data as a string
	 */
	public function get_data ($var, $echo = true) {
		$data = $this->logic->data_return($var);
		return $data;
	}

	/**
	 * Echo Logic Hop data
	 *
	 * Data extracted from Data Factory $data
	 * Accepts [logichop_data vars=""]
	 * Parameter 'vars' accepts '.' delimited object elements and ':' delimited array elements
	 * Example: Date.DateTime OR QueryStore:ref
	 *
	 * @since    	1.0.9
	 * @param  		string	$var		Data path
	 * @return  	null or content		Data as a string
	 */
	public function echo_data ($var) {
		$data = $this->logic->data_return($var);
		echo $data;
	}

	/**
	 * Update geolocation
	 *
	 * @since    	3.6.0
	 * @param      	string    $ip_address
	 */
	public function update_geolocation ( $ip_address ) {
		return $this->logic->update_geolocation( $ip_address );
	}

	/**
	 * Get Condition
	 *
	 * Accepts Condition ID or String and evaluates Condition
	 * Returns true or false
	 *
	 * @since    	1.5.0
	 * @param  		string	$condition	Condition ID or string
	 * @return  	boolean				Condition met
	 */
	public function get_condition ($condition) {
		return $this->logic->condition_get($condition);
	}

	/**
	 * Check Logic
	 *
	 * Accepts raw JSON Logic and evaluates against session
	 * Returns true or false
	 *
	 * @since    	1.5.0
	 * @param  		string	$logic	JSON Logic string
	 * @return  	boolean			Logic result
	 */
	public function check_logic ($logic) {
		return $this->logic->logic_apply_to_session($logic);
	}

	/**
	 * Set Goal
	 *
	 * Accepts Goal ID to updates Goal if Goal ID exists
	 * Returns true or false
	 *
	 * @since    	1.5.0
	 * @param  		integer	$goal_id	Goal ID
	 * @return  	boolean				Goal update status
	 */
	public function set_goal ($goal_id) {
		return $this->logic->update_goal($goal_id);
	}

	/**
	 * Get Goal
	 *
	 * Accepts Goal ID or Slug
	 * Returns goal count
	 *
	 * @since    	3.5.6
	 * @param  		integer	$goal_id	Goal ID
	 * @return  	boolean				Goal update status
	 */
	public function get_goal ( $goal_id ) {
		return $this->logic->goal_status( $goal_id );
	}
}
