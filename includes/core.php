<?php

if (!defined('ABSPATH')) die;

/**
 * Core functionality.
 *
 * Provides core functionality.
 *
 * @since      1.0.0
 * @package    LogicHop
 * @subpackage LogicHop/includes
 */

use LogicHop\JsonLogic as JsonLogic;
use Mobile_Detect as Mobile_Detect;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use OS_Detect as OS_Detect;

CONST LOGICHOP_LICENSE_DATA ='logichop_data';
CONST LOGICHOP_TRIAL ='logichop_trial';
CONST LOGICHOP_VALIDATION_ERROR ='logichop_validation_error';
CONST LOGICHOP_EXPIRATION_LASTSEEN = 'logichop_expiration_lastseen';
CONST LOGICHOP_NB_CHECKCOUNT='logichop_checkcount';
CONST LOGICHOP_SETTINGS ='logichop-settings';
CONST LOGICHOP_INSTANCE ='logichop_instance';
CONST API_ROOT ='https://license.logichop.com';
class LogicHop_Core {

    /**
	 * The basename of this plugin.
	 *
	 * @since    3.0.9
	 * @access   private
	 * @var      string    $plugin_basename    The plugin basename
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
	public $version;

	/**
	 * Data Factory object
	 *
	 * @since    3.5.6
	 * @access   public
	 * @var      object    $data    Data Factory
	 */
	public $data_factory;

	/**
	 * Wordpress Option Settings
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      array    $options    Array of WP get_options('logichop-settings')
	 */
	private $options;

	/**
	 * Cookie name
	 *
	 * @since    3.1.8
	 * @access   private
	 * @var      string    $cookie_name    cookie name
	 */
	public $cookie_name;

	/**
	 * Time modifier for cookie expiration.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cookie_ttl    strtotime modifier.
	 */
	private $cookie_ttl;

	/**
	 * Require Consent
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $consent_require    Level of consent required
	 */
	private $consent_require;

	/**
	 * Consent cookie name
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $consent_cookie    Cookie name.
	 */
	private $consent_cookie;

	/**
	 * Cookie expiration setting.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cookie_expires    Cookie expiration setting.
	 */
	private $cookie_expires;

	/**
	 * Cookie path.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cookie_path    Cookie path.
	 */
	private $cookie_path = '/';

	/**
	 * Hash identifying user.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $hash    Hash identifying user.
	 */
	public $hash;

	/**
	 * API URL.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_url   	API URL.
	 */
	private $api_url;

	/**
	 * API KEY.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_key    API Key.
	 */
	public $api_key;

	/**
	 * Website domain name.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $domain    Website domain name.
	 */
	public $domain;

	/**
	 * Maximum number of pages in Path array.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      integer    $path_max    Maximum number of pages in Path array.
	 */
	public $path_max;

	/**
	 * Enable or Disable Javascript-based tracking
	 * Use when Wordpress content is cached
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      boolean    $js_tracking
	 */
	private $js_tracking;

	/**
	 * Enable or Disable Javascript Anti-Flicker
	 *
	 * @since    3.3.4
	 * @access   public
	 * @var      boolean    $js_anti_flicker
	 */
	public $js_anti_flicker;

	/**
	 * Anti-Flicker Timeout
	 *
	 * @since    3.3.4
	 * @access   public
	 * @var      integer    $js_anti_flicker_timeout
	 */
	public $js_anti_flicker_timeout = 3000;

	/**
	 * Disable Conditional CSS
	 *
	 * @since    1.5.0
	 * @access   private
	 * @var      boolean    $disable_conditional_css
	 */
	private $disable_conditional_css;

	/**
	 * Disable Visual Editor STyles
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      boolean    $disable_editor_styles
	 */
	private $disable_editor_styles;

	/**
	 * Enable nested Logic Tags
	 *
	 * @since    3.2.2
	 * @access   private
	 * @var      boolean    $enable_nested_tags
	 */
	private $enable_nested_tags;

	/**
	 * Enable or disable geolocation
	 *
	 * @since    3.5.0
	 * @access   private
	 * @var      boolean    $geolocation_disable
	 */
	public $geolocation_disable;

	/**
	 * Enable or disable IE11 Polyfills
	 *
	 * @since    3.5.0
	 * @access   private
	 * @var      boolean    $ie11_polyfills
	 */
	private $ie11_polyfills;

	/**
	 * Enable or Disable Javascript variable display
	 *
	 * @since    1.4.2
	 * @access   private
	 * @var      boolean    $js_variables
	 */
	private $js_variables;

	/**
	 * Variable names to disable via Javascript display
	 *
	 * @since    1.4.2
	 * @access   private
	 * @var      array    $js_disabled_vars
	 */
	private $js_disabled_vars;

	/**
	 * Referrer to check during AJAX requests or Javascript-based tracking
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ajax_referrer
	 */
	private $ajax_referrer;

	/**
	 * Use transients
	 *
	 * @since    3.2.3
	 * @access   public
	 * @var      boolean    $use_transients
	 */
	public $use_transients;

	/**
	 * Transients class
	 *
	 * @since    3.2.3
	 * @access   public
	 * @var      object    $transients
	 */
	public $transients;

	/**
	 * Transient prefix
	 *
	 * @since    3.2.3
	 * @access   public
	 * @var      string    $transient_prefix
	 */
	public $transient_prefix = 'logichop_';

	/**
	 * Cookie payload - Set cookie via JS
	 *
	 * @since    3.2.3
	 * @access   public
	 * @var      object    $cookie    Cookie payload.
	 */
	public $cookie = null;

	/**
	 * Debug mode enabled
	 *
	 * @since    1.1.0
	 * @access   public
	 * @var      boolean    $debug
	 */
	public $debug;
    public $license_data;
    public $data_plan;
	public $instanceId ;
    /**
	 * Provides core functionality.
	 *
	 * @param      	string    $plugin_basename The basename of this plugin.
	 * @param string $plugin_name   	The name of this plugin.
	 * @param      	string    $version    		The version of this plugin.
	 * @param      	string    $cookie_ttl    	Time modifier for cookie expiration.
	 * @param      	string    $path_max    		Maximum number of pages in path history
	 * @param      	string    $api_url    		The version of this plugin.
	 * @param      	boolean   $debug    		Enable debugging
	 *@since    	1.0.0
	 */
	public function __construct (string $plugin_basename, string $plugin_name, $version, $cookie_ttl, $path_max, $api_url, $debug = false ) {
        $this->plugin_basename = $plugin_basename;
		$this->plugin_name	= $plugin_name;
		$this->version 		= $version;
		$this->cookie_ttl 	= $cookie_ttl;
		$this->path_max 	= $path_max;
		$this->api_url 		= $api_url;
		$this->debug 		= $debug;
		$this->transients = false;
        $this->license_data=null;
        $this->data_plan=null;
		$this->getInstanceUuid();
    }

    function check_validity() {
        $lastseen= get_option(LOGICHOP_EXPIRATION_LASTSEEN);
        if (!$lastseen) {
            $lastseen =new DateTime();;
            add_option(LOGICHOP_EXPIRATION_LASTSEEN,$lastseen);
        }

        $date_now = new DateTime();
        $expiration = date_add($lastseen, date_interval_create_from_date_string('7 days'));
        $isExpired = $date_now->format("Y-m-d")  > $expiration->format("Y-m-d");
        $interval = date_diff( $date_now,$expiration );

        if ($isExpired) {
            return false;
        }
        return $interval->d;

    }
    function apply_validation_error_notice() {

        $err =get_option(LOGICHOP_VALIDATION_ERROR);
        if ($err===false) {
        }else {
            echo '<div class="notice notice-error is-dismissible">
				<p style="font-size: large;color: #e21b24">				
				 At this moment , it\'s impossible to check the Licence status
                </p>																
			    </div>';

        }
    }
	function  apply_expire_notice()
    {

        if ($this->is_license_expired()) {
            // Set Last Seen and counter
            $interval = $this->check_validity();
            if (!$interval) {
                echo '<div class="notice notice-error is-dismissible">';
			    echo '<p style="font-size: large;color: #e21b24">Your LogicHop license isn\'t active anymore.</p>';
                echo '<p>Please renew your license asap via this <a href="https://logichop.com/get-started/?utm_source=Plugin">link</a>.</p>';
                echo '</div>';

            } else {

                echo '<div class="notice notice-error is-dismissible">';
				echo '<p style="font-size: large;color: #e21b24">Your LogicHop license has expired.</p>';
                echo '<p>At this stage, you can\'t set up and/or modify Logic Hop rules anymore (conditions, blocks, goals, etc.)</p>';
                echo '<p style="color: #e21b24">In ' .$interval .' days, the execution of those rules on your site will stop working,which might break the UX on your site.</p>';
                echo '<p>In order to avoid the deactivation of all your Logic Hop settings</p>';
                echo '<p>Please <a class ="button" href="https://logichop.com/get-started/?utm_source=Plugin"> renew your license asap</a></p>';
                echo '</div>';
            }


        }
    }
	/**
	 * Initialize core functionality.
	 * Update user data
	 *
	 * @since    1.0.0
	 */
	public function initialize_core () {

        if ( $this->options = get_option('logichop-settings') ) {
			$this->api_key 					= $this->get_option('api_key');
			$this->domain 					= $this->get_option('domain');
			$this->geolocation_disable  = ( $this->get_option( 'geolocation_disable' ) == 1 ) ? true : false;
			$this->ie11_polyfills  = ( $this->get_option( 'ie11_polyfills' ) == 1 ) ? true : false;
			$this->js_tracking  		= ($this->get_option('js_tracking') == 1) ? true : false;
			$this->js_anti_flicker = ($this->get_option('js_anti_flicker') == 1) ? true : false;
			$this->js_anti_flicker_timeout = ( $this->get_option( 'js_anti_flicker_timeout' ) ) ? $this->get_option( 'js_anti_flicker_timeout' ) : $this->js_anti_flicker_timeout;
			$this->disable_conditional_css 	= ($this->get_option('disable_conditional_css') == 1) ? true : false;
			$this->disable_editor_styles 	= ($this->get_option('disable_editor_styles') == 1) ? true : false;
			$this->enable_nested_tags 	= ( $this->get_option( 'disable_nested_tags' ) == 1 ) ? false : true;
			$this->js_variables				= ($this->get_option('js_variables') == 1) ? true : false;
			$this->js_disabled_vars 		= array_map('trim', explode(',', $this->get_option('js_disabled_vars')));
			$this->ajax_referrer 			= $this->get_option('ajax_referrer');
			$this->cookie_ttl				= $this->get_option('cookie_ttl', $this->cookie_ttl);
			$this->consent_require  		= $this->get_option('consent_require');
			$this->consent_cookie  			= $this->get_option('consent_cookie');
			$this->cookie_name			= ( $this->get_option( 'cookie_name' ) ) ? $this->get_option( 'cookie_name' ) : 'logichop';
			$this->use_transients		= ( $this->get_option('use_transients' ) == 1 ) ? true : false;

			if ( $this->get_option( 'disable_wpautop' ) == 1 ) {
				remove_filter( 'the_content', 'wpautop' );
				remove_filter( 'the_excerpt', 'wpautop' );
			}
		}


        if ($this->active()) {
            $this->custom_post_types();
        }

		$this->cookie_expires = strtotime( $this->cookie_ttl );
        $this->data_factory = new LogicHop\DataFactory( $this );
		do_action( 'logichop_integration_init', $this );

		$bypass = false;
		$bypass = apply_filters( 'logichop_initialize_core', $bypass );

		if ( ! $bypass ) {
			$cookie = false;
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				if ( ! isset( $_COOKIE[$this->cookie_name] ) ) {
					if ( isset( $_POST['logichop_cookie'] ) ) {
						$_cookie = sanitize_text_field( $_POST['logichop_cookie'] );
						if ( $this->is_hash( $_cookie ) ) {
							$cookie = $_cookie;
						}
					}
				}
			}

			if ( ! isset( $_COOKIE[$this->cookie_name] ) && $cookie === false && $this->data_factory->get_value( 'UID' ) ) {
				$cookie = $this->data_factory->get_value( 'UID' );
			}

			if ( ! isset( $_COOKIE[$this->cookie_name] ) && $cookie === false ) { // NO COOKIE -> CREATE COOKIE & SESSION
				$this->hash = $this->generate_hash(); // GENERATE UID :: HASH
				$this->data_factory->data_object_create(); // CREATE THE DATA OBJECT :: NEW USER
			} else { // COOKIE EXISTS -> LOAD UID -> TRY TO RETRIEVE TRANSIENT
				$this->hash = ( $cookie ) ? $cookie : $_COOKIE[$this->cookie_name]; // LOAD UID :: HASH
				$this->data_factory->transient_retrieve( $this->hash );
			}

			if ( is_null( $this->data_factory->get_value( 'UID' ) ) ) { // COOKIE EXISTS -> EXISTING USER -> LOAD DATA
				$this->data_factory->data_object_create(); // CREATE THE DATA OBJECT :: EXISTING USER
				$this->data_retrieve(); // LOAD USER DATA
			}
		}
		do_action('logichop_initialize_core_data_check');
	}

    /**
     * Update core variable.
     *
     * @param string $prop Property name.
     * @param null $value Value to assign to property.
     * @return mixed $value    Value to assigned to property
     * @since        1.0.0
     */
	public function config_set ($prop, $value = null) {
    	$this->{$prop} = $value;
    	return $this->{$prop};
    }
    public function reset_checkcount () {
        update_option( LOGICHOP_NB_CHECKCOUNT, 0 );
    }
    public function license_data_model () {
        $license_data = new stdclass;
        $license_data->status= 'na';
        $license_data->type= 'na';
        $license_data->valid= false;
        $license_data->check_date =time() ;
        $license_data->expiration_date="";
        $license_data->domain ="na";
        $license_data->message ="";
        $license_data->tier="na";
        $license_data->get_link ='https://spf.logichop.com/checkout?utm_source=settings';
        return $license_data;
    }
    public function reset_license_data(){
        delete_option(LOGICHOP_LICENSE_DATA);
        $this->license_data=$this->license_data_model();
    }
    /**
     * Validate API
     *
     * Set logichop transient based on API validation response
     *
     * @since    4.0.0
     */
    public function validate_api ($referer): ?stdclass
    {

        $num_of_checks = get_option( LOGICHOP_NB_CHECKCOUNT );
        if ($num_of_checks ==false) {
            $num_of_checks =0;
            $this->reset_checkcount();
        }

        if ($num_of_checks> 3) {
            $this->license_data=  $this->license_data_model();
            return  $this->license_data;
        }
        $this->options = get_option('logichop-settings');
		$key =$this->get_option('api_key', false);
        if (substr($key,0,8) ==="v2.local") {
            if (isset($this->license_data->key)) {
                if (substr($this->license_data->key,0,8) ==="v2.local") {
                    $key = $this->license_data->key;
                }
            }
        }
        if (strlen($key) >0 ) {
            $installed_addons = array();
            $installed_addons = apply_filters('logichop_client_meta_integrations', $installed_addons);
            $data = array(
                'addons' => implode(',', $installed_addons)
            );
            $this->domain = $this->get_option("domain","");
            if  (strlen($this->domain)==0) {
                $this->license_data=  $this->license_data_model();
                return  $this->license_data;
            }
            $valid = $this->validate_api_license($key,$this->domain, $this->version,$referer);

            if (isset($valid['validation-error']) && $valid['validation-error']){
                if ($this->license_data==null) {
                    $this->license_data=  $this->license_data_model();
                }
                update_option(LOGICHOP_VALIDATION_ERROR,time());
                return $this->license_data;
            }
            delete_option(LOGICHOP_VALIDATION_ERROR);
			delete_option(LOGICHOP_LICENSE_DATA);

            if (isset($valid['active']) && $valid['active']){
                $logichop =    $this->license_data_model();
                $logichop->status = $valid['status']?? "";
                if (!($logichop->status=="expired")) {
                    delete_option(LOGICHOP_EXPIRATION_LASTSEEN);
                }
                if (isset($valid['is_local_storage']) && $valid['is_local_storage']) {
                    $logichop->is_local_storage = true ;
                    add_option( 'logichop_local_storage',$valid['version'] );
                }else {
                    $logichop->is_local_storage = false ;
                    delete_option( 'logichop_local_storage' );
                }
                $logichop->expiration_date = $valid['expires'] ?? "";
                $logichop->type =$valid['type'] ?? "na";
                $logichop->key =$valid['key'] ?? $key;
                $logichop->domain= $valid['domain']  ;
                $logichop->message= $valid['message'] ;
                $logichop->check_date =time() ;
                $logichop->valid = $valid['active'] ;
                $logichop->version =$this->version;
                $logichop->addons = $data;
                $logichop->tier =$valid['tier'] ;
                $logichop->builder = '';
                if (isset($valid['addons']) && $valid['addons']) {
                    $logichop->addons = json_decode($valid['addons']);
                }
                if (isset($valid['conditions']) && $valid['conditions']) {
                    $logichop->builder = $valid['conditions'];
                }
                require_once( __DIR__ . '/api/validate.php' );
                $resp = LogicHop_LS_Validate::get($data);
                if ( isset($resp->Conditions)) {
                    $logichop->builder = $resp->Conditions;
                }
                add_option('logichop_data', $logichop);
                update_option( LOGICHOP_NB_CHECKCOUNT, 0 );


                $this->license_data= $logichop;
                return  $this->license_data;
            }else {
                $logichop= $this->license_data_model();
                $logichop->key=$key;

                $logichop->status= $valid['status']?? "";
                $logichop->message= $valid['message'] ??"";

                add_option(LOGICHOP_LICENSE_DATA, $logichop);
                $this->license_data= $logichop;
                update_option( LOGICHOP_NB_CHECKCOUNT, $num_of_checks+1 );
                return  $this->license_data;
            }
        }else {
            $this->reset_license_data();
            $logichop= $this->license_data_model();
            add_option(LOGICHOP_LICENSE_DATA, $logichop);
            $this->license_data= $logichop;
            return  $this->license_data;
        }
    }
    private function load_license_data () {
        if ($this->license_data==null ) {
            $logichop =get_option(LOGICHOP_LICENSE_DATA);
            if ($logichop === false) {
                $this->license_data=null;
            }else if ($logichop->valid) {
                $diff = time()- $logichop->check_date;
                if ($diff < 24*60*60) {
                    $this->license_data = $logichop;
                }
            }
        }
    }
    /**
     * Is Logic Hop active
     *
     * @return 		boolean    Logic Hop is active
     *
     * @since    3.0.0
     */
    public function license_handler (): ?bool
    {
        $this->load_license_data();
        if (($this->license_data)==null){
            $this->validate_api("handler");
        }

        return $this->active();
    }

    /**
	 * Is Logic Hop active
	 *
	 * @return 		boolean    Logic Hop is active
	 *
	 * @since    3.0.0
	 */
	public function active ( $data = false)
    {
        $this->load_license_data();
        if ($this->is_license_expired()) {
            $interval = $this->check_validity();
            if (!$interval) {
                return false;
            }
        }
		if ($data) {
			return $this->license_data;
		}
        return $this->license_data->valid;
	}
	/**
	 * Data Plan
	 * Is there an active, valid data plan
	 *
	 * @return     	boolean    Valid data plan
	 *
	 * @since    2.1.2
	 */
	public function data_plan () {
        if ($this->data_plan==null) {
            $this->load_license_data();
            if ($this->license_data->type !=null) {
                $this->data_plan = $this->license_data->type == "Data Plan";
            }
        }
        return $this->data_plan;
	}

	/**
	 * Get Logic Hop Add-ons
	 *
	 * @return 		array    Array of Add-ons
	 *
	 * @since    3.0.0
	 */
	public function get_addons () {
		$addons = array();

		if ( $logichop = $this->active( true ) ) {
			$addons = $logichop->addons;
		}

		return $addons;
	}

	/**
	 * Get Logic Hop Add-ons
	 *
	 * @param		string		Add-on
	 * @return 		boolean 	Add-on active
	 *
	 * @since    3.0.0
	 */
	public function addon_active ( $addon ) {
		$go=get_transient('logichop_' .$addon . 'active' );
		if ($go ==false) {
			set_transient('logichop_' .$addon . 'active',1,24*60*60);
			if ( ! class_exists( 'LogicHop\LogicHop_Measurement' ) ) {
				require_once 'Measurement.php';
			}
			LogicHop_Measurement::getInstance()->measure( "addons", $addon,true);
		}
		return true;
	}

	/**
	 * Get Logic Hop Builder Logic
	 *
	 * @return 		string    Builder Logic
	 *
	 * @since    3.0.0
	 */
	public function get_builder () {
		$builder = '';

		if ( $this->license_data!=null) {
			$builder = $this->license_data->builder;
			$builder = apply_filters( 'logichop_admin_condition_builder', $builder );
		}
		return $builder;
	}

	/**
	 * Update Client Meta Data.
	 * Store client meta data for QA - No API Keys or Account information.
	 *
	 * @since    1.1.0
	 */
	public function update_client_meta () {
		if ( $tmp_options = get_option('logichop-settings') ) {
			$key = (isset($tmp_options['api_key'])) ? $tmp_options['api_key'] : '';

			$settings = array();
			if (isset($tmp_options['js_tracking'])) 			$settings[] = 'js_tracking';
			if (isset($tmp_options['disable_nested_tags'])) 			$settings[] = 'disable_nested_tags';
			if (isset($tmp_options['disable_conditional_css'])) $settings[] = 'disable_css';
			if (isset($tmp_options['js_variables'])) 			$settings[] = 'js_variables';

			$consent = ( $tmp_options['consent_require'] ) ? $tmp_options['consent_require'] : 'never';
			if (isset($tmp_options['consent_require'])) 		$settings[] = 'consent_require-' . $consent;

			$integrations = array();
			$integrations = apply_filters('logichop_client_meta_integrations', $integrations);

			$data = array (
					'cookie_ttl'	=> $tmp_options['cookie_ttl'],
					'settings'		=> implode(' ', $settings),
					'integrations'	=> implode(' ', $integrations)
				);
			$meta_log = $this->api_post('meta-log', $data, $key);
		}
	}
  	/**
	 * Register Custom Post Types.
	 *
	 * @since    1.0.0
	 */
	public function custom_post_types () {
        register_post_type(
			'logichop-conditions',
				array(
					'label' => __('Conditions', 'logichop'),
					'labels' => array (
						'name' => __('Logic Hop Conditions', 'logichop'),
						'all_items' => __('Conditions', 'logichop'), // All Conditions
						'add_new_item' => __('Add New Condition', 'logichop'),
						'edit_item' => __('Edit Condition', 'logichop'),
						'not_found' => __('No conditions found', 'logichop'),
						'not_found_in_trash' => __('No conditions found', 'logichop'),
						'search_items' =>  __('Search Conditons', 'logichop')
						),
					'menu_position' => 20,
					'public' => false,
					'show_ui' => true,
					'show_in_menu' => 'logichop-menu',
					'exclude_from_search' => true,
					'hierarchical' => true,
					'capability_type' => 'post',
					'supports' => array(
						'title',
						'excerpt'
					)
				)
		);
		register_post_type(
			'logichop-logicblocks',
				array(
					'label' => __('Logic Blocks', 'logichop'),
					'labels' => array (
						'name' => __('Logic Blocks', 'logichop'),
						'all_items' => __('Logic Blocks', 'logichop'), // All Conditions
						'add_new_item' => __('Add New Logic Block', 'logichop'),
						'edit_item' => __('Edit Logic Block', 'logichop'),
						'not_found' => __('No Logic Blocks found', 'logichop'),
						'not_found_in_trash' => __('No Logic Blocks found', 'logichop'),
						'search_items' =>  __('Search Logic Blocks', 'logichop')
						),
					'menu_position' => 20,
					'public' => false,
					'show_ui' => true,
					'show_in_menu' => 'logichop-menu',
					'exclude_from_search' => true,
					'hierarchical' => true,
					'capability_type' => 'post',
					'supports' => array(
						'title',
						'excerpt'
					)
				)
		);
		register_post_type(
			'logichop-logic-bars',
				array(
					'label' => __('Logic Bars', 'logichop'),
					'labels' => array (
						'name' => __('Logic Bars', 'logichop'),
						'all_items' => __('Logic Bars', 'logichop'), // All Conditions
						'add_new_item' => __('Add New Logic Bar', 'logichop'),
						'edit_item' => __('Edit Logic Bar', 'logichop'),
						'not_found' => __('No Logic Bars found', 'logichop'),
						'not_found_in_trash' => __('No Logic Bars found', 'logichop'),
						'search_items' =>  __('Search Logic Bars', 'logichop')
						),
					'menu_position' => 20,
					'public' => false,
					'show_ui' => true,
					'show_in_menu' => 'logichop-menu',
					'exclude_from_search' => true,
					'hierarchical' => true,
					'capability_type' => 'post',
					'supports' => array(
						'title',
						'excerpt'
					)
				)
		);
		register_post_type(
			'logichop-goals',
				array(
					'label' => __('Goals', 'logichop'),
					'labels' => array (
						'name' => __('Logic Hop Goals', 'logichop'),
						'all_items' => __('Goals', 'logichop'), // All Goals
						'add_new_item' => __('Add New Goal', 'logichop'),
						'edit_item' => __('Edit Goal', 'logichop'),
						'not_found' => __('No goals found', 'logichop'),
						'not_found_in_trash' => __('No goals found', 'logichop'),
						'search_items' =>  __('Search Goals', 'logichop')
						),
					'menu_position' => 20,
					'public' => false,
					'show_ui' => true,
					'show_in_menu' => 'logichop-menu',
					'exclude_from_search' => true,
					'hierarchical' => true,
					'capability_type' => 'post',
					'supports' => array(
						'title',
						'excerpt'
					)
				)
		);

		register_taxonomy(
       'logichop_goal_group',
       'logichop-goals',
       array (
         'hierarchical'	=> false,
         'labels'		=> array(
           'name'                       => _x( 'Goal Group', 'taxonomy general name', 'logichop' ),
           'singular_name'              => _x( 'Group', 'taxonomy singular name', 'logichop' ),
           'search_items'               => __( 'Search Groups', 'logichop' ),
           'popular_items'              => __( 'Popular Groups', 'logichop' ),
           'all_items'                  => __( 'All Groups', 'logichop' ),
           'edit_item'                  => __( 'Edit Group', 'logichop' ),
           'update_item'                => __( 'Update Group', 'logichop' ),
           'add_new_item'               => __( 'Add New Group', 'logichop' ),
           'new_item_name'              => __( 'New Group Name', 'logichop' ),
           'separate_items_with_commas' => __( 'Separate groups with commas', 'logichop' ),
           'add_or_remove_items'        => __( 'Add or remove groups', 'logichop' ),
           'choose_from_most_used'      => __( 'Choose from the most used groups', 'logichop' ),
           'not_found'                  => __( 'No groups found.', 'logichop' ),
           'menu_name'                  => __( 'Groups', 'logichop' ),
         ),
				 'public'								 => false,
         'show_ui'               => true,
				 'show_in_menu'					 => false,
         'show_admin_column'     => true,
				 'show_in_quick_edit'		 => false,
         'query_var'             => false,
       )
     );

		register_post_type(
			'logichop-redirects',
				array(
					'label' => __('Redirects', 'logichop'),
					'labels' => array (
						'name' => __('Redirects', 'logichop'),
						'all_items' => __('Redirects', 'logichop'), // All Conditions
						'add_new_item' => __('Add New Redirect', 'logichop'),
						'edit_item' => __('Edit Redirect', 'logichop'),
						'not_found' => __('No Redirects found', 'logichop'),
						'not_found_in_trash' => __('No Redirects found', 'logichop'),
						'search_items' =>  __('Search Redirects', 'logichop')
						),
					'menu_position' => 20,
					'public' => false,
					'show_ui' => true,
					'show_in_menu' => 'logichop-menu',
					'exclude_from_search' => true,
					'hierarchical' => true,
					'capability_type' => 'post',
					'supports' => array(
						'title',
						'excerpt'
					)
				)
		);
	}

	/**
	 * Get user data from session.
	 *
	 * REPLACED BY $this->data_factory->get_data()
	 *
	 * @since    	1.0.0
	 * @return      object    User data from data_factory.
	 */
	public function session_get () {
		return $this->data_factory->get_data();
	}

	/**
	 * Get single variable from session.
	 *
	 * REPLACED BY $this->data_factory->get_value()
	 *
	 * @since    	1.0.0
	 * @param      	string    	$var	Variable name
	 * @return      variable    Single variable from data factory.
	 */
	public function session_get_var ( $var ) {
		return $this->data_factory->get_value( $var );
	}

	/**
	 * Sets logichop cookie
	 *
	 * @since    1.0.0
	 */
	public function cookie_create () {
		$bypass = false;
		$bypass = apply_filters( 'logichop_before_cookie_create', $bypass );

		if ( $bypass ) {
			return false;
		}
		$cookie_name = ( $this->cookie_name ) ? $this->cookie_name : 'logichop';
		$this->cookie_construct( $cookie_name, $this->hash, $this->cookie_expires, $this->cookie_path, $this->domain );
        return true;
	}

	/**
	 * Builds cookie object
	 *
	 * @since    3.2.3
	 */
	public function cookie_construct ( $name, $value, $expires, $path, $domain ) {
		$cookie = new stdclass;
		$cookie->name = $name;
		$cookie->value = $value;
		$cookie->expires = strftime( '%a, %d %h %Y %H:%M:%S GMT', $expires );
		$cookie->path = $path;
		$cookie->domain = $domain;
		$cookie->secure = $this->is_https();

		$this->cookie = $cookie;
	}

	/**
	 * Get cookie object
	 *
	 * @since    3.2.3
	 */
	public function cookie_retrieve () {
		return $this->cookie;
	}

	/**
	 * Get cookie name
	 *
	 * @since    3.2.3
	 */
	public function cookie_name () {
		return $this->cookie_name;
	}

	/**
	 * Destroys cookie object
	 *
	 * @since    3.2.3
	 */
	public function cookie_destroy () {
		$this->cookie = null;
	}

	/**
	 * Check user country for European Union
	 *
	 * @since    	3.0.0
	 * @return      boolean    	Country is part of European Union
	 */
	public function is_EU () {
		$location = $this->data_factory->get_value( 'Location' );
		return ( isset( $location->EU ) ) ? $location->EU : false;
	}

	/**
	 * Check user country for GDPR
	 *
	 * @since    	3.0.0
	 * @return      boolean    	Country is part of GDPR
	 */
	public function is_GDPR () {
		$location = $this->data_factory->get_value( 'Location' );
		return ( isset( $location->GDPR ) ) ? $location->GDPR : false;
	}

	/**
	 * Check if consent cookie required
	 * Check if data should be collected
	 *
	 * @since    3.0.0
	 * @return	boolean		True if user data should be bypassed
	 */
	function consent_check ( $bypass ) {

		$check_consent = false;

		if ( $this->consent_require == 'all' ) {
			$check_consent = true;
		}

		if ( $this->consent_require == 'eu' && $this->is_EU() ) {
			$check_consent = true;
		}

		if ( $this->consent_require == 'gdpr' && $this->is_GDPR() ) {
			$check_consent = true;
		}

		if ( $check_consent ) {
			if ( ! $this->consent_cookie_check() ) {
				$bypass = true;
			}
		}

		return $bypass;
	}

	/**
	 * Check if consent cookie is set
	 *
	 * @since    3.0.0
	 * @return	boolean		True if consent cookie is set
	 */
	function consent_cookie_check () {

		if ( isset( $_COOKIE[ $this->consent_cookie ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Update UID
	 *
	 * Update Logic Hop UID – Useful for add-ons when loading existing user data
	 *
	 * @since    3.0.0
	 */
	public function update_uid ($uid) {
		$cookie_name = ( $this->cookie_name ) ? $this->cookie_name : 'logichop';
		$this->cookie_construct( $cookie_name, $uid, $this->cookie_expires, $this->cookie_path, $this->domain );
		$this->data_factory->set_value( 'UID', $uid );
	}

	/**
	 * Retrieve remote user data from 'events' API
	 *
	 * @since    	1.0.0
	 * @return		JSON object		User data from api_post response
	 */
	public function data_retrieve () {
		$args = array (
						'uid' => $this->data_factory->get_value( 'UID' ),
						'ip' => $this->data_factory->get_value( 'IP' )
					);
		$data = $this->api_post('events', $args);

		if ( $data ) {
			$this->data_factory->store_retrieved_data( $data );
			if (is_array($data)){
				do_action( 'logichop_data_retrieve', $data );
			}
		}

		return $data;
	}

	/**
	 * Update user data on page load.
	 * Does not update for admin section.
	 *
	 * To access Page/Post Tracking: $track_page = get_post_meta($post_id, '_logichop_track_page', true);
	 *
	 * @since    1.0.0
	 * @param      	string    	$pid		Page ID
	 * @since    1.3.0
	 * @param      	string    	$referrer	Referring URL
	 */
	public function update_data ( $pid = false, $referrer = false ) {
		if ( $pid === false ) { // $pid SET = EXPLICIT CALL VIA AJAX --> REQUEST METHOD CAN VARY - NOT CALLED FROM WP-ADMIN
			if ( is_admin() || $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				return;  // ONLY CHECK FOR ADMIN & POST ON STANDARD SUBMISSION
			}
		}

		$this->data_factory->update_data( $pid, $referrer );
	}

	/**
	 * Logic Hop set data
	 *
	 * @since    	3.1.5
	 * @param  		string		$key	Data key - Must be valid PHP variable name
	 * @return  	object		$value	Data value
	 */
	public function data_set ( $key, $value = null ) {
		return $this->data_factory->set_custom_value( $key, $value );
	}

	/**
	 * Logic Hop update data
	 *
	 * @since    	3.6.0
	 * @param  		string		$key	Data key - Must be valid PHP variable name
	 * @return  	object		$value	Data value
	 */
	public function set_value ( $key, $value = null ) {
		return $this->data_factory->set_value( $key, $value );
	}

	/**
	 * Logic Hop return data
	 *
	 * Data extracted from Data Factory data object
	 * Accepts [logichop_data vars=""]
	 * Parameter 'vars' accepts '.' delimited object elements and ':' delimited array elements
	 * Example: Date.DateTime OR QueryStore:ref
	 *
	 * @since    	1.0.9
	 * @param  		string	$var	Data path
	 * @return  	string			Data as a string
	 */
	public function data_return ($var) {
		$vars = explode('.', $var);
		$object = $this->data_factory->get_data();
		foreach ($vars as $key => $element) {
			$array_check = explode(':', $element);
			if (!isset($array_check[1])) {
				if (isset($object->$element)) {
					$object = $object->$element;
				} else {
					return '';
				}
			} else {
				if (isset($object->{$array_check[0]}[$array_check[1]])) {
					$object = $object->{$array_check[0]}[$array_check[1]];
				} else {
					return  '';
				}
			}
		}
		if (isset($object)) return $object;
		return '';
	}

	/**
	 * Refresh data stored in transients
	 *
	 * @since    	3.5.6
	 */
	public function data_refresh () {
		$this->data_factory->transient_retrieve();
	}

	/**
	 * Return Logic Hop variable from Liquid tag
	 *
	 * Used to lookup variable for sending from Goal
	 * Accepts {{#VARIABLE_NAME#}}
	 * Parameter 'vars' accepts '.' delimited object elements and ':' delimited array elements
	 * Example: {{Date.DateTime}} OR {{QueryStore:ref}}
	 *
	 * @since    	1.0.9
	 * @param  		string	$var	Liquid variable
	 * @return  	string			Data as a string --> Returns default value if value does not exist
	 */
	public function get_liquid_value ($var) {
		if (preg_match('/{{([^}}]*)}}/', $var, $match)) {
			if (isset($match[1])) {
				return $this->data_return(trim($match[1]));
			}
		}
		return $var;
	}

	/**
	 * Update query string from HTTP Referrer when JS Tracking is enabled
	 * Referrer should always be internal via WP/AJAX
	 *
	 * @param	$goals	boolean		Process goals
	 * @since    1.0.5
	 */
	public function get_referrer_query_string ( $goals = true ) {
		if ($this->js_tracking) {
			if (isset($_SERVER['HTTP_REFERER'])) {
				$get_vars = array();
				$referrer = parse_url($_SERVER['HTTP_REFERER']);

				if ( isset( $referrer['query'] ) ) {
					parse_str($referrer['query'], $get_vars);
					if ( $goals ) {
						$this->process_querystring_goals( $get_vars );
					}
				}
				$query_store = $this->data_factory->get_value( 'QueryStore' );
				$this->data_factory->set_value( 'Query', $get_vars, false );
				$this->data_factory->set_value( 'QueryStore', array_merge( $query_store, $get_vars ), false );
				$this->data_factory->transient_save();
			}
		}
	}

	/**
	 * Search query string for goals to set and delete
	 *
	 * @param	$querystring	array		Query string
	 * @since    3.4.3
	 */
	public function process_querystring_goals ( $querystring ) {
		if ( is_array( $querystring ) ) {
			foreach ( $querystring as $k => $v ) {
				if ( $k == 'logichop_goal' ) {
					$this->update_goal( $v );
				}
				if ( $k == 'logichop_goal_delete' ) {
					$this->update_goal( $v, true );
				}
			}
		}
	}

	/**
	 * Update URL when JS Tracking is enabled
	 *
	 * @since    3.3.1
	 */
	public function update_stored_url ( $url ) {
		if ($this->js_tracking) {
			if ( $url ) {
				$this->data_factory->set_value( 'URL', $url );
			}
		}
	}

	/**
	 * Update Goal.
	 *
	 * @since    	1.0.0
	 * @param      	integer/string    Goal ID or Slug
	 * @param      	boolean    Delete goal
	 * @return      boolean    Goal stored state.
	 */
	public function update_goal ( $goal_id = null, $delete_goal = false ) {
		if ( is_numeric( $goal_id ) ) {
			$goal = get_post( $goal_id );
		} else {
			$goal_slug = filter_var( $goal_id, FILTER_SANITIZE_STRING );
			$goal = get_page_by_path( $goal_slug, OBJECT, 'logichop-goals' );
		}

		if ( isset( $goal ) && $goal->post_type == 'logichop-goals' ) {
			return $this->data_factory->update_goal( $goal, $delete_goal );
		}

		return false;
	}

	/**
	 * Update geolocation
	 *
	 * @since    	3.6.0
	 * @param      	string    $ip_address
	 */
	public function update_geolocation ( $ip_address ) {
		return $this->data_factory->update_geolocation( $ip_address );
	}

	/**
	 * Lead Score Validator
	 *
	 * @since    	2.0.7
	 * @param      	integer    $post_id			Post ID
	 * @param      	integer    $lead_adjust		Lead Score increment
	 * @param      	string     $lead_freq		Lead Score frequency
	 * @return      boolean    Lead Score stored state.
	 */
	public function validate_lead_score ( $post_id, $lead_adjust, $lead_freq ) {

		return $this->data_factory->validate_lead_score( $post_id, $lead_adjust, $lead_freq );
	}

	/**
	 * Get Wordpress Post ID or Post Data.
	 *
	 * @since    	1.0.0
	 * @param      	integer   	$pid 			Post ID.
	 * @param      	boolean   	$return_post 	Switch to determin return parameter
	 * @return      string    		Post ID - Defailt.
	 * @return      Post Object    	Wordpress Post Object - If $return_post is true.
	 */
	public function wordpress_post_get ($pid = false, $return_post = false) {
		$post_id = ($pid) ? $pid : url_to_postid(sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']));

		if ($post_id == 0) {
			if (is_404()) $post_id = -1;
			if (is_home() && !is_front_page()) $post_id = (int) get_option('page_for_posts');
		}

		if (!$return_post) return $post_id;

		return get_post($post_id);
	}

	/**
	 * Get Condition JSON
	 *
	 * @since   	1.0.0
	 * @param      	mixed		$condition_id   	Integer for Condition ID – String for default conditions.
	 * @param      	boolean		$return_title   	Return Condition title
	 * @return     	JSON    	Condition logic as JSON
	 * @return     	object    	Condition logic as JSON and Condition title as string
	 */
	public function condition_get_json ($condition_id = false, $return_title = false) {

		$rule 	= false;
		$title 	= '';

		if (is_numeric($condition_id)) {
			// NUMERIC CONDITION
			$condition_id = (int) $condition_id;
			$condition = get_post($condition_id);
			if ($condition) {
				$rule 	= json_decode($condition->post_excerpt, true);
				$title 	= $condition->post_title;
			}
		} else if ($default_condition = $this->condition_default_get(true, $condition_id, false)) {
			// DEFAULT CONDITION
			$rule 	= json_decode($default_condition['rule']);
			$title 	= $default_condition['title'];
		} else {
			// CONDITION SLUG
			$condition = get_page_by_path( $condition_id, OBJECT, 'logichop-conditions' );
			if ($condition) {
				$rule 	= json_decode($condition->post_excerpt, true);
				$title 	= $condition->post_title;
			}
		}

		if ($return_title) {
			$data = new stdClass();
			$data->rule = $rule;
			$data->title = $title;
			return $data;
		}

		return $rule;
	}

	/**
	 * Get Condition result
	 *
	 * @since   	1.0.0
	 * @param      	mixed		$condition_id   	Integer for Condition ID – String for default conditions.
	 * @return     	boolean    	Condition result
	 */
	public function condition_get ($condition_id = false) {

		if (!$this->session_get()) return false;

		$rule = $this->condition_get_json($condition_id);
		if ($rule) {
			$result = $this->logic_apply($rule, $this->session_get());
			if ($result) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get Default Condition result
	 *
	 * @since   	1.1.0
	 * @param      	boolean		$single   			Return single condition rule or array of all conditions
	 * @param      	string		$condition_id   	Default condition key
	 * @param      	boolean		$rule_only   		Option to return rule only or complete single condition data
	 * @return     	mixed    	Array of all condtions or single condition rule
	 */
	public function condition_default_get ($single = false, $condition_id = '', $rule_only = true) {

		$conditions = array (
			'no_conditions' => array (
								'title' => "No Conditions Met",
								'rule'	=> '{"==":[{"var":"Condition"},false]}',
								'info'	=> "No conditions have been met. Must be last condition when used in Shortcode. View docs for full details."
							),
			'has_conditions' => array (
								'title' => "Condition(s) Met",
								'rule'	=> '{"==": [ {"var": "Condition" }, true ] }',
								'info'	=> "One or more conditions have been met. Must not be first condition when used in Shortcode. View docs for full details."
							),
			'first_visit' 	=> array (
								'title' => "User's First Visit",
								'rule'	=> '{"==": [ {"var": "FirstVisit" }, true ] }',
								'info'	=> "This is the user's first visit to the site."
							),
			'repeat_visit' 	=> array (
								'title' => "User Repeat Visit",
								'rule'	=> '{"==": [ {"var": "FirstVisit" }, false ] }',
								'info'	=> "This is not the user's first visit to the site."
							),
			'pages_gt_one' 	=> array (
								'title' => "User Has Viewed More Than 1 Page",
								'rule'	=> '{">": [ {"add_array": {"var": "Pages"}}, 1 ] }',
								'info'	=> "The user has viewed more than one page."
							),
			'direct_visit' 	=> array (
								'title' => "User Visiting Site Directly - No Referrer",
								'rule'	=> '{"==": [{"var": "Source" }, "direct"]}',
								'info'	=> "The user visited the site directly."
							),
			'is_desktop' 	=> array (
								'title' => "User on a Desktop or Laptop Computer",
								'rule'	=> '{"==": [ {"var": "Mobile" }, false ] }',
								'info'	=> "The user is on a desktop or laptop."
							),
			'is_mobile' 	=> array (
								'title' => "User on a Mobile Device",
								'rule'	=> '{"==": [ {"var": "Mobile" }, true ] }',
								'info'	=> "The user is on a mobile device."
							),
			'is_tablet' 	=> array (
								'title' => "User on a Tablet",
								'rule'	=> '{"==": [ {"var": "Tablet" }, true ] }',
								'info'	=> "The user is on a tablet."
							),
			'logged_in' 	=> array (
								'title' => "User is Logged In",
								'rule'	=> '{"==": [ {"var": "LoggedIn" }, true ] }',
								'info'	=> "The user is logged in to WordPress."
							),
			'from_google' 	=> array (
								'title' => "User from Google search",
								'rule'	=> '{"in": ["google.com", {"var": "Referrer" }]}',
								'info'	=> "The user was referred by Google."
							),
      'always_display' 	=> array (
              'title' => "Always Display",
              'rule'	=> '{"==": [true, true]}',
              'info'	=> "Always display.",
              'omit'  => true
            ),
			);

		if (!$this->data_plan()) {
			$conditions['repeat_visit']['title'] = 'User Repeat Visit (Data Plan Requried)';
		}

		$conditions = apply_filters('logichop_condition_default_get', $conditions);

		if ($single) {
			if (array_key_exists($condition_id, $conditions)) {
				if ($rule_only) {
					return $conditions[$condition_id]['rule']; // DEFAULT: Return the condition rule
				}
				return $conditions[$condition_id];
			}
			return false;
		}
		return $conditions;
	}

	/**
	 * Goal Status
	 *
	 * @since   	1.0.0
	 * @param      	mixed		$goal_id   	Integer for Goal ID – String for Goal slug
	 * @return     	boolean    	Condition result
	 */
	public function goal_status ( $goal_id ) {
		$goals = $this->session_get_var( 'Goals' );

		if ( is_int( $goal_id ) ) {
			if ( isset( $goals[$goal_id] ) ) {
				return $goals[$goal_id];
			}
		} else if ( is_string( $goal_id ) ) {
			$goal = get_page_by_path( $goal_id, OBJECT, 'logichop-goals' );
			if ( $goal ) {
				if ( isset( $goals[$goal->ID] ) ) {
					return $goals[$goal->ID];
				}
			}
		}
		return 0;
	}

	/**
	 * Apply conditional logic
	 *
	 * @since    1.0.0
	 * @param      object     $rule       Condition rule
	 * @param      object     $data       Condition data
	 * @return     boolean    Conditional logic result
	 *
	 * TO DO: Better solution for 'no_conditions' lookup
	 *
	 */
	public function logic_apply ($rule, $data = false) {
		$jsonlogic = new JsonLogic();

		$condition_met = false;
		if ($data) {
			$_data = new stdclass();
			$_data = clone( $data );	// CLONE LOGIC HOP SESSION DATA
			$_data->Cookie = ( isset( $_COOKIE ) ) ? $_COOKIE : array(); // APPEND COOKIE DATA TO CLONE
			$_data->Server = ( isset( $_SERVER ) ) ? $_SERVER : array();	// APPEND SERVER DATA TO CLONE
			$condition_met = $jsonlogic->apply($rule, $_data);
		} else {
			$condition_met = $jsonlogic->apply($rule);
		}

		if ($condition_met) {
			if (json_encode($rule) != $this->condition_default_get( true, 'no_conditions' ) ) {
				if ( ! $this->data_factory->get_value( 'Condition' ) ) {
					$this->data_factory->set_value( 'Condition', true );
				}
			}
		}
		return $condition_met;
	}

	/**
	 * Apply conditional logic to session
	 *
	 * @since    1.0.0
	 * @param      object	$rule	Condition rule
	 * @return     boolean			logic_apply() result
	 */
	public function logic_apply_to_session ($rule) {
		return $this->logic_apply(json_decode($rule), $this->session_get());
	}

	/**
	 * Check if previewing content and user is an editor
	 *
	 * @since    	1.0.0
	 * @return      boolean     If preview mode is active
	 */
	public function is_preview_mode () {
		if ( is_preview() && current_user_can('edit_posts') ) {
			return true;
		}
		return false;
	}

	/**
	 * Get Logic Hop Redirects for a post
	 *
	 * @since    3.1.7
	 */
	public function redirects_get ( $id ) {
		$id = (int) $id;
		if ( $id > 0 ) {
			return get_posts(
				array(
					'post_type' => 'logichop-redirects',
					'post_status' => 'publish',
					'meta_query' => array(
        		array(
            	'key' => 'logichop_redirect_id',
            	'compare' => '==',
							'value' => $id,
						)
        	),
	    		'posts_per_page' => -1,
				)
			);
		}
		return false;
	}

	/**
	 * Check if Javascript Tracking has been enabled
	 *
	 * @since    	1.0.0
	 * @return      boolean     If $this->js_tracking is set
	 */
	public function js_tracking () {
		if ($this->js_tracking) return true;
		return false;
	}

	/**
	 * Check if Caching || Javascript Tracking has been enabled
	 *
	 * @since    	1.0.0
	 * @return      boolean     If $this->js_tracking is set
	 */
	public function caching_enabled () {
		return $this->js_tracking();
	}

	/**
	 * Check if nested Logic Tags are enabled.
	 *
	 * @since    	3.2.2
	 * @return      boolean     If $this->enable_nested_tags is true || false
	 */
	public function nested_tags_enabled () {
		if ( $this->enable_nested_tags ) {
			return true;
		}
		return false;
	}

	/**
	 * Disable Javascript Mode
	 *
	 * @since    	3.1.9
	 */
	public function js_mode_disable () {
		$this->js_mode_set( false );
	}

	/**
	 * Enable Javascript Mode
	 *
	 * @since    	3.1.9
	 */
	public function js_mode_enable () {
		$this->js_mode_set( true );
	}

	/**
	 * Set Javascript Mode
	 *
	 * @since    	3.1.9
	 * @param 	boolean		$mode
	 */
	private function js_mode_set ( $mode ) {
		$this->js_tracking = $mode;
	}

	/**
	 * Check if IE11 Polyfills have been enabled
	 *
	 * @since    	3.5.2
	 * @return      boolean     If $this->ie11_polyfills is true
	 */
	public function ie11_polyfills () {
		if ( $this->ie11_polyfills ) {
			return true;
		}
		return false;
	}

	/**
	 * Check if Conditional CSS has been disabled
	 *
	 * @since    	1.5.0
	 * @return      boolean     If $this->disable_conditional_css is set
	 */
	public function disable_conditional_css () {
		if ($this->disable_conditional_css) return true;
		return false;
	}

	/**
	 * Check if Visual Editor Styles have been disabled
	 *
	 * @since    	1.5.0
	 * @return      boolean     If $this->disable_editor_styles is set
	 */
	public function disable_editor_styles () {
		if ($this->disable_editor_styles) return true;
		return false;
	}

	/**
	 * Check if Javascript Variable Display has been enabled
	 *
	 * @since    	1.4.2
	 * @return      boolean     If $this->js_variables is set
	 */
	public function js_variable_display () {
		if ($this->js_variables) return true;
		return false;
	}

	/**
	 * Check if Variable name is in array of disabled variables
	 *
	 * @since    	1.4.2
	 * @return      boolean     True if variable is on disabled list
	 */
	public function is_variable_disabled ($var_name) {
		if (in_array($var_name, $this->js_disabled_vars)) return true;
		return false;
	}

	/**
	 * Check if event should be tracked by external APIs
	 *
	 * @since    	1.0.0
	 * @param		integer     $id       Wordpress Post ID
	 * @param		array     	$values   Wordpress Post get_post_custom() ARRAY
	 */
	public function check_track_event ($id, $values = array()) {
		do_action( 'logichop_check_track_event', $id, $values );
	}

	/**
	 * Set User Data
	 *
	 * @since    	2.1.5
	 * @param      	object		$user    	User object.
	 * @param      	boolean		$logout    	Logout user
	 */
	public function wp_user_data_set ( $user = false, $logout = false ) {
		$this->data_factory->set_user_data( $user = false, $logout = false );
	}

	/**
	 * Store user data through API.
	 *
	 * @since    	1.0.0
	 * @return 		json object    API response.
	 */
	public function data_remote_put ( $type, $value, $endpoint = 'event', $json = false ) {
		$bypass = false;
		$bypass = apply_filters( 'logichop_before_data_remote_put', $bypass );

		if ( $bypass ) {
			return false;
		}

		$data = array (
					'uid'		=> $this->data_factory->get_value( 'UID' ),
					'type'	=> $type,
					'value'	=> $value,
					'ip'		=> $this->get_client_IP()
				);
		return $this->api_post( $endpoint, $data, false, $json );
	}
	public function getInstanceUuid () {
		if ($this->instanceId ==null) {
			$this->instanceId= get_option("logichop_instance");
			if ($this->instanceId==null){
				$bytes = random_bytes(20);
				$this->instanceId =bin2hex($bytes);
				add_option("logichop_instance", $this->instanceId);
			}
		}
		return $this->instanceId;
	}


    /**
     * Check License
     *
     * @return      array object    API response.
     * @since        1.0.0
     */
    public function validate_api_license ($key,$domain, $version,$referer) {
        $lk = ($key) ? $key : $this->api_key;
        $wpDomain =isset($_SERVER['SERVER_NAME']) ? strtolower($_SERVER['SERVER_NAME']) : '';
        $url =API_ROOT ."/license?action=validate&license_key=".$lk  ."&domain=" .$domain ."&version=".$version. "&wp_domain=".$wpDomain."&ref=".$referer."&wp-version=" .get_bloginfo('version') ;
        $response = wp_remote_get( $url ,array('timeout' => 5) );
        if (!is_wp_error($response)) {
            if (isset($response['body'])) {
                return json_decode($response['body'], true);
            }
        }else {
            $error["validation-error"]=true;
            $error['message']= $response->get_error_message();
            $error["active"]=false;
            return $error;
        }
    }
    public function get_plugin_api_root () {
        return  API_ROOT;
    }
	/**
	 * Post data to API.
	 *
	 * @since		1.0.0
	 * @param		string		$endpoint		API endpoint
	 * @param		array		$data			Post data
	 * @param		string		$key			Override API Key
	 * @return      JSON object    API response.
	 */
	public function api_post ( $endpoint, $data = array(), $key = false, $json = false ) {
		$logichop_data = ( isset( $this->data_factory ) ) ? $this->data_factory->get_data() :  null;
		$bypass = false;
		$bypass = apply_filters( 'logichop_api_post', $bypass, $endpoint, $data, $key, $json, $logichop_data );
		if ( $bypass ) {
			return $bypass;
		}

		if (!in_array($endpoint, array('validate', 'status', 'integrations'))) {
			if (!$this->data_plan()) {
				return false;
			}
		}

		if (in_array($endpoint, array('event'))) {
			if ( $this->data_factory->get_value( 'IsBot' ) ) {
				return false;	// PREVENT BOTS FROM STORING DATA
			}
		}

		$data_default = array (
					'domain' 	=> $this->domain,
					'version' 	=> $this->version,
					'wp_domain' => isset($_SERVER['SERVER_NAME']) ? strtolower($_SERVER['SERVER_NAME']) : ''
				);
		$data_source = array_merge($data_default, $data);

		$url = sprintf('%s/%s.php', $this->api_url, $endpoint);

		$post_args = array (
						'headers' => array (
							'LOGIC-HOP-API-KEY' => ($key) ? $key : $this->api_key
							),
						'body' => $data_source
					);

		if ( $json ) {
			$post_args['data_format'] = 'body';
			$post_args['body'] = json_encode( $data_source );
		}

		$response = wp_remote_post($url, $post_args);

		if (!is_wp_error($response)) {
			if (isset($response['body'])) return json_decode($response['body'], true);
		} else {
			$error['Client']['Message'] = sprintf('<h4 style="color: #08ff00;">ERROR: %s</h4>', $response->get_error_message());
			return $error;
		}
		return false;
	}

	/**
	 * Get Javascript events as options for select input
	 *
	 * @since    1.0.0
	 * @param		string		$id		Selected option value
	 * @return      string    	Javascript event options
	 */
	public function javascript_get_events ($event = false) {
		$events = array (
						'click' 		=> __('On Click', 'logichop'),
						'dblclick' 		=> __('On Double Click', 'logichop'),
						'focus' 		=> __('On Focus', 'logichop'),
						'blur' 			=> __('On Blur', 'logichop'),
						'scroll' 		=> __('On Scroll', 'logichop'),
						'mousedown' 	=> __('On Mouse Down', 'logichop'),
						'mouseup' 		=> __('On Mouse Up', 'logichop'),
						'mouseover' 	=> __('On Mouse Over', 'logichop'),
						'mouseout' 		=> __('On Mouse Out', 'logichop'),
						'mouseenter' 	=> __('On Mouse Enter', 'logichop'),
						'mouseleave' 	=> __('On Mouse Leave', 'logichop'),
						'change' 		=> __('On Change', 'logichop'),
						'select' 		=> __('On Select', 'logichop'),
						'submit' 		=> __('On Submit', 'logichop'),
						'keydown' 		=> __('On Key Down', 'logichop'),
						'keypress' 		=> __('On Key Press', 'logichop'),
						'keyup' 		=> __('On Key Up', 'logichop'),
						'load' 			=> __('On Load', 'logichop'),
						'unload' 		=> __('On Unload', 'logichop'),
						'beforeunload' 	=> __('On Before Unload', 'logichop')
					);

		$options = '';

		foreach ($events as $value => $name) {
					$options .= sprintf('<option value="%s" %s>%s</option>',
											$value,
											($event == $value) ? 'selected' : '',
											$name
										);
		}
		return $options;
	}

	/**
	 * Get Conditions
	 *
	 * @since    1.1.0
	 * @return      array    	Array of Conditions
	 */
	public function conditions_get ( $slug = false, $key_slug = 'slug', $key_name = 'name' ) {
		$conditions = array();

		$query = new WP_Query(array(
						'post_type' => $this->plugin_name . '-conditions',
						'post_status' => 'publish',
						'posts_per_page' => -1
					));

		if ($query) {
			foreach ($query->posts as $p) {
				$tmp = array (
							'id' => $p->ID,
							$key_name => $p->post_title
						);
				if ($slug) $tmp[$key_slug] = $p->post_name;

				$conditions[] = $tmp;
			}
		}
		wp_reset_postdata();

		$defaults = $this->condition_default_get();
		if ($defaults) {
			foreach ($defaults as $key => $c) {
				$tmp = array (
							'id' => $key,
							$key_name => $c['title']
						);
				if ($slug) $tmp[$key_slug] = $key;

        if ( !isset( $c['omit'] ) ) {
          $conditions[] = $tmp;
        }
			}
		}

		return $conditions;
	}

	/**
	 * Get Condition as options for select input
	 *
	 * @since    1.0.0
	 * @param		string		$id		Selected option value
	 * @param		boolean		$slug_value		Set option value as slug instead of ID -- Logic Bar & Blocks for Recipe support
	 * @return      string    	Condtion options
	 */
	public function conditions_get_options ( $id = false, $slug_value = false ) {
		$options = '';

		$query = new WP_Query(array(
						'post_type' => $this->plugin_name . '-conditions',
						'post_status' => 'publish',
						'posts_per_page' => -1
					));

		if ($query) {
			foreach ($query->posts as $p) {
				$value = ( ! $slug_value ) ? $p->ID : $p->post_name;
				$options .= sprintf('<option value="%s" data-slug="%s" %s>%s</option>',
									$value,
									$p->post_name,
									($value == $id) ? 'selected' : '',
									$p->post_title
								);
			}
		}
		wp_reset_postdata();

		$conditions = $this->condition_default_get();
		if ($conditions) {
			$options .= '<option value="" data-slug=""> >> Default Conditions</option>';
			foreach ($conditions as $key => $c) {
				$options .= sprintf('<option value="%s" data-slug="%s" %s> > %s</option>',
									$key,
									$key,
									($key == $id) ? 'selected' : '',
									$c['title']
								);
			}
		}

		return $options;
	}

  /**
   * Get Goals as an Array
   *
   * @since    	3.0.7
   * @return      array    	Array of Goals
   */
  public function goals_get ( $slug = false, $key_slug = 'slug', $key_name = 'name' ) {
    $goals = array();

    $query = new WP_Query(array(
            'post_type' => $this->plugin_name . '-goals',
            'post_status' => 'publish',
            'posts_per_page' => -1
          ));

    if ($query) {
			foreach ($query->posts as $p) {
				$tmp = array (
							'id' => $p->ID,
							$key_name => $p->post_title
						);
				if ($slug) $tmp[$key_slug] = $p->post_name;

				$goals[] = $tmp;
			}
		}
		wp_reset_postdata();

    return $goals;
  }

	/**
	 * Get Goals as options for select input
	 *
	 * @since    	1.0.0
	 * @param		string		$id		Selected option value
	 * @return      string		Goal options
	 */
	public function goals_get_options ($id = false) {
		$options = '';
		$query = new WP_Query(array(
						'post_type' => $this->plugin_name . '-goals',
						'post_status' => 'publish',
						'posts_per_page' => -1
					));

		if ($query) {
			foreach ($query->posts as $p) {
				$options .= sprintf('<option value="%s" data-slug="%s" %s>%s</option>',
									$p->ID,
									$p->post_name,
									($p->ID == $id) ? 'selected' : '',
									$p->post_title
								);
			}
		}
		wp_reset_postdata();

		return $options;
	}

	/**
	 * Get Goals as JSON object
	 *
	 * @since    	1.0.0
	 * @return      json object    JSON encoded goals
	 */
	public function goals_get_json () {
		$goals = new stdclass;

		$query = new WP_Query(array(
						'post_type' => $this->plugin_name . '-goals',
						'post_status' => 'publish',
						'posts_per_page' => -1
					));

		if ($query) {
			foreach ($query->posts as $p) {
				$goals->{$p->ID} = $p->post_title;
			}
		}
		wp_reset_postdata();

		return json_encode($goals);
	}

	/**
	 * Get Logic Blocks as options for select input
	 *
	 * @since    	3.1.0
	 * @param		string		$id		Selected option value
	 * @return      string		Goal options
	 */
	public function blocks_get_options ($id = false) {
		$options = '';
		$query = new WP_Query(array(
						'post_type' => $this->plugin_name . '-logicblocks',
						'post_status' => 'publish',
						'posts_per_page' => -1
					));

		if ($query) {
			foreach ($query->posts as $p) {
				$options .= sprintf('<option value="%s" %s>%s</option>',
									$p->post_name,
									($p->ID == $id) ? 'selected' : '',
									$p->post_title
								);
			}
		}
		wp_reset_postdata();

		return $options;
	}

	/**
	 * Get Wordpress Pages & Posts as JSON object
	 *
	 * Only returns pages/posts with _logichop_track_page = true
	 *
	 * @since    	1.0.0
	 * @return      json object    JSON encoded pages & posts
	 */
	public function pages_get_json () {
		$pages = new stdclass;

		$query = new WP_Query(array(
						'post_type' => 'page',
						'post_status' => 'publish',
						'posts_per_page' => -1,
						'order' => 'ASC',
						'orderby' > 'ID',
						'meta_query' => array(
							array(
								'key' => '_logichop_track_page',
								'value' => true,
							   	'compare' => '	='
							)
						)
					));

		if ($query)
			foreach ($query->posts as $p)
				$pages->{$p->ID} = $p->post_title;
		wp_reset_postdata();

		$query = new WP_Query(array(
						'post_type' => 'post',
						'post_status' => 'publish',
						'posts_per_page' => -1,
						'order' => 'ASC',
						'orderby' > 'ID',
						'meta_query' => array(
							array(
								'key' => '_logichop_track_page',
								'value' => true,
							   	'compare' => '='
							)
						)
					));

		if ($query)
			foreach ($query->posts as $p)
				$pages->{$p->ID} = $p->post_title;
		wp_reset_postdata();

		$pages = apply_filters('logichop_condition_pages_get_json', $pages);

		return json_encode($pages);
	}

	/**
	 * Returns host from current Referrer
	 * Example: domain.com
	 *
	 * @since    	1.0.0
	 * @return      string    Domain name
	 */
	public function get_referrer_host () {
		$referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : '';
		return isset($referrer['host']) ? $referrer['host'] : '';
	}

	/**
	 * Determines if the current referrer is valid based on the value of $ajax_referrer
	 * Always true if $ajax_referrer is NOT set
	 *
	 * @since    	1.0.0
	 * @return      boolean    If current referrer matches $ajax_referrer
	 */
	public function is_valid_referrer () {
		if ($this->ajax_referrer) {
			if ($this->ajax_referrer == $this->get_referrer_host()) {
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * Generates hash
	 *
	 * @since    	1.0.0
	 * @param      	string    $salt			Optional salt for md5 hash
	 * @return      string    md5 hash.
	 */
	public function generate_hash ($salt = '') {
		return md5($this->get_client_IP() . time() . $salt);
	}

	/**
	 * Is hash valid
	 *
	 * @since    	3.0.0
	 * @param      	string    $hash			Hash to check
	 * @return     	boolean		Valid hash
	 */
	public function is_hash ($hash = '') {
		$hash = filter_var( $hash, FILTER_SANITIZE_STRING );
		if ( preg_match('/^[a-f0-9]{32}$/', $hash) ) {
			return $hash;
		}
		return false;
	}

	/**
	 * Detect client IP address
	 *
	 * @since		1.0.6
	 * @return     	string		IP Address
	 */
	public function get_client_IP () {
		$client_ip = '';

		if (array_key_exists('HTTP_X_REAL_IP', $_SERVER) && !empty($_SERVER['HTTP_X_REAL_IP'])) {
			$client_ip = $_SERVER['HTTP_X_REAL_IP'];
		} else if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
			$client_ip = $ip[0];
		} else if (array_key_exists('REMOTE_ADDR', $_SERVER) && !empty($_SERVER['REMOTE_ADDR'])) {
      		$client_ip = $_SERVER['REMOTE_ADDR'];
    	} else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
    		$client_ip = $_SERVER['HTTP_CLIENT_IP'];
    	}

		if (array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER) && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$client_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}

		if (array_key_exists('HTTP_TRUE_CLIENT_IP', $_SERVER) && !empty($_SERVER['HTTP_TRUE_CLIENT_IP'])) {
			$client_ip = $_SERVER['HTTP_TRUE_CLIENT_IP'];
		}

		$client_ip = apply_filters( 'logichop_get_client_ip', $client_ip );

		if (filter_var($client_ip, FILTER_VALIDATE_IP)) {
			return $client_ip;
		}

    	return '0.0.0.0';
	}

	/**
	 * Utility to get Wordpress option from $options
	 *
	 * @since		1.1.0
	 * @param      	string   	$option   			Option Name
	 * @param      	var			$default_return		Default return value - Optional
	 */
	public function get_option ($option, $default_return = '') {
		if (isset($this->options[$option])) return $this->options[$option];
		return $default_return;
	}

	/**
	 * Utility to handle events on plugin update
	 *
	 * @since		3.0.9
	 * @param      	object    			$upgrader_object       	Plugin_Upgrader instance
	 * @param      	array    			$options       	Type of action / process
	 */
	public function upgrade_completed ( $upgrader_object, $options ) {
		if ( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
			foreach ( $options['plugins'] as $plugin ) {
   			if ( strrpos( $plugin, 'logichop' ) !== false || strrpos( $plugin, 'logic-hop' ) !== false ) {
					// Any Logic Hop plugin updated
					delete_transient( 'logichop' );
   			    }
  		    }
 		}
	}

	/**
	 * Purge Logic Hop Transients
	 *
	 * @since    3.2.3
	 */
	public function purge_transients () {
		if ( ! class_exists( 'LogicHop\DataFactory' ) ) {
			require_once 'DataFactory.php';
		}
		$transients = new LogicHop\DataFactory( $this );
		$transients->delete_expired_transients();
	}

	/**
	 * Utility to HTTPS
	 *
	 * @return		boolean
	*/
	public function is_https () {
		$is_secure = false;
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on') {
    	$is_secure = true;
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ||
								! empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on' ) {
    	$is_secure = true;
		}
		return $is_secure;
	}

	/**
	 * Utility to echo and log data
	 *
	 * @since		1.0.0
	 * @param      	variable, string    $data       Variable to display
	 * @param      	boolean    			$pad       	Switch for padding to accommodate for WP Dashboard nav
	 * @param      	boolean    			$log       	Switch to log data to error log
	 */
	public function d ($data, $pad = false, $log = false) {
		echo '<pre style="color: red;">';
		if ($pad) echo '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ';
		var_dump($data);
		echo '</pre>';
		if ($log) error_log($data);
	}
    
    public function getPostCount($postType ) {
        $q =wp_count_posts($postType);
        if (isset($q->publish)){
            return $q->publish;
        }
        return 0;
    }
    public function canDisplayToolbar() :bool{
        //return ($this->data_plan() || $this->is_local_storage_active() )  ;
        return true;

    }

    /**
     * Determine if the Logic Hop Local license expired.
     *
     * @since		3.7.2
     */
    public function is_license_expired() {
        $this->load_license_data();
		return $this->license_data->status=="expired";
    }
	/**
	 * Fetch a list of onboarding steps for display in wp-admin.
	 * 
	 * @since		3.7.0
	 */
	public function get_onboarding_steps() {
            $steps = [
                [
                    'scope'		=> 'lh-config',
                    'title'		=> 'Set your license Key and configure Logic Hop',
                    'complete'	=> $this->active(),
                ],
                [
                    'scope'		=> 'lh-conditions',
                    'title'		=> 'Create a Condition',
                    'complete'	=> $this->getPostCount('logichop-conditions') ,
                ]
            ];
		return $steps;
	}

	/**
	 * Determine if onboarding is complete.
	 * 
	 * @since		3.7.0
	 */
	public function is_onboarding_complete() {
		$steps = $this->get_onboarding_steps();
		foreach( $steps as $step ) {
			if( !$step['complete'] ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Determine if a given Logic Hop integration is 'active', 'installed', or 'unavailable'.
	 * 
	 * @since 		3.7.0
	 * @return 		{string}
	 */
	private function get_integration_status( $integration ) {
		// Check to see if the integration is active
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		foreach( $active_plugins as $plugin ) {
			if( strpos( $plugin, $integration ) !== false ) {
				return 'active';
			}
		}
        if ( !function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if ( function_exists( 'get_plugins' ) ) {
            // Check to see if the integration is installed
            foreach (get_plugins() as $plugin => $plugin_data) {
                if (strpos($plugin, $integration) !== false) {
                    return 'installed';
                }
            }
        }
		// If we got this far, the integration isn't installed or active.
		return 'unavailable';
	}

	/**
	 * Get all available integrations, including their statuses.
	 * 
	 * @since		3.7.0
	 */
	public function get_integrations() {
		$available_integrations = [
			'logic-hop-beaver-builder/logichop_beaver_builder.php' => [
				'name'	=> 'Beaver Builder',
				'desc'	=> 'Add content personalization support to the Beaver Builder editor.'
			],
			'logic-hop-convertkit-add-on/logichop_convertkit.php' => [
				'name'	=> 'ConvertKit',
				'desc'	=> 'Create personalization conditions based on information stored in ConvertKit.'
			],
			'logic-hop-drip-add-on/logichop_drip.php' => [
				'name'	=> 'Drip',
				'desc'	=> 'Create personalization conditions based on information stored in Drip.'
			],
			/* 'logic-hop-facebook-pixel/logichop_facebook_pixel.php' => [
				'name'	=> 'Facebook Pixel',
				'desc'	=> 'Create personalization conditions based on information tracked by the Facebook Pixel.'
			], */
			'logic-hop-google-analytics-add-on/logichop_google_analytics.php' => [
				'name'	=> 'Google Analytics',
				'desc'	=> 'Create personalization conditions based on behavior tracked by Google Analytics.'
			],
			'logic-hop-hubspot-add-on/logichop_hubspot.php' => [
				'name'	=> 'Hubspot',
				'desc'	=> 'Create personalization conditions based on information stored in Hubspot.'
			],
			'logic-hop-personalization-for-divi-add-on/logichop_divi.php' => [
				'name'	=> 'Divi',
				'desc'	=> 'Add content personalization support to the Divi editor.'
			],
			'logic-hop-personalization-for-elementor-add-on/logichop_elementor.php' => [
				'name'	=> 'Elementor',
				'desc'	=> 'Add content personalization support to the Elementor editor.'
			],
			'logic-hop-personalization-for-gravity-forms-add-on/logichop_gravity_forms.php' => [
				'name'	=> 'Gravity Forms',
				'desc'	=> 'Create personalizaion conditions based on Gravity Form submissions.'
			],
			'logic-hop-woocommerce-add-on/logichop_woocommerce.php' => [
				'name'	=> 'WooCommerce',
				'desc'	=> 'Create personalization conditions based on customer behavior and purchase history via WooCommerce.'
			]
		];

		// Sort the conditions alphabetically
		$names = array_column( $available_integrations, 'name' );
		array_multisort( $names, SORT_ASC, $available_integrations );

		// Determine the integration's status
		foreach( $available_integrations as $integration => $details ) {
			$available_integrations[$integration]['status'] = $this->get_integration_status( $integration );
		}

		return $available_integrations;
	}
}