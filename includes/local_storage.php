<?php

 if ( ! defined( 'ABSPATH' ) ) die;

 /**
 * Provides Logic Hop local storage functionality.
 *
 * @since      1.0.0
 * @package    LogicHop
 */

class LogicHop_LocalStorage {
    /**
     * active
     *
     * @since    1.0.0
     * @access   public
     * @var      string    $active    Plugin version
     */
    public $active;

	/**
	 * Plugin version
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    Plugin version
	 */
	public $version;

  /**
	 * License
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $license    License
	 */
	public $license = '';

	/**
	 * Put endpoints
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    Plugin version
	 */
	private $post = array(
			'event',
			'event-update',
			'events-update',
		);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    	1.0.0
	 * @param       object    $version	LogicHop_Core functionality & logic.
	 */
	public function __construct ( $version ) {
		$this->version = $version;
        $this->active=false;
        $options = get_option( 'logichop-settings' );
		if ( isset( $options['api_key'] ) ) {
		   $this->license = $options['api_key'];
		}

        $local=get_option('logichop_local_storage');
        if ($local ==false) {
            $this->active=false;
        }else {
            $this->active = true;
        }
        if ($this->active) {
            $this->required_classes();
            $this->add_hooks_filters();
        }
	}

	/**
	 * Include required classes
	 *
	 * @since    	1.0.0
	 */
	public function required_classes () {
		require_once(__DIR__ . '/api/status.php');
		require_once(__DIR__ . '/api/validate.php');
		require_once(__DIR__ . '/api/data.php');
		require_once(__DIR__ . '/api/geolocate.php');
	}

	/**
	 * Add actions
	 *
	 * @since    	1.0.0
	 */
	public function add_hooks_filters () {
		add_filter( 'logichop_api_post', array( $this, 'api_post' ), 10, 6 );
		add_filter( 'logichop_settings_register', array( $this, 'register_settings' ) );
		add_action( 'logichop_local_delete_storage', array( $this, 'delete_storage' ) );
		add_action( 'after_setup_theme', array( $this, 'update' ) );
	}

	/**
	 * Filter api_post requests
	 * Bypasses all API calls
	 *
	 * @since    	1.0.0
	 */
	public function api_post ( $bypass, $endpoint, $data, $key, $json, $logichop_data ) {

		$response = false;

        $ip = ( isset( $logichop_data->IP ) ) ? $logichop_data->IP : '0.0.0.0';
        $uid = ( isset( $logichop_data->UID ) ) ? $logichop_data->UID : '';

		if ( in_array( $endpoint, $this->post ) ) {
			$response = LogicHop_LS_Data::save( $logichop_data );
		}

		if ( $endpoint == 'events' ) {
			$location = LogicHop_LS_Geolocate::get( $ip ,$this->license );
			$response = LogicHop_LS_Data::get( $uid, $location );
		}

		if ( $endpoint == 'status' ) {
			$response = LogicHop_LS_Status::get();
		}

		if ( $endpoint == 'geolocate' ) {
			$location = LogicHop_LS_Geolocate::get( $data['ip'],$this->license );
			$response = new stdclass();
			$response->geolocation = $location;
		}

		if ( $response ) {
			return $this->response( $response );
		}

		return true;
	}

	/**
	 * Format api response
	 *
	 * @since    	1.0.0
	 */
	public function response ( $response ) {
		$response = json_encode( $response );
		return json_decode( $response, true );
	}

	/**
	 * Delete storage data
	 *
	 * @since    	1.0.0
	 */
	public function delete_storage () {
		LogicHop_LS_Data::delete();
	}

	/**
	 * Update plugin
	 *
	 * @since    	1.0.0
	 */
	public function update () {
		//new LogicHop_LS_Update( $this->version, $this->license );
	}

  /**
	 * Add settings
	 *
	 * @param    array		$settings Settings parameters
	 * @return   array    $settings	Settings parameters
	 *@since    1.0.0
	 */
	public function register_settings ( array $settings ): array
    {
        $settings['logichop_local_storage_delete'] = array (
								'name' 	=> __( 'Delete Local Storage', 'logichop') ,
								'meta' 	=> __( 'Delete local storage data. <a target="_blank" href="https://logichop.com/docs/data-storage-gdpr-settings/" target="_blank">Learn More</a>.', 'logichop' ),
								'type' 	=> 'select',
								'label' => '',
								'opts'  => array (
									0 => array (
										'name' => __( 'That hasn\'t been updated within 1 day', 'logichop' ),
										'value' => '1 DAY'
									),
									1 => array (
										'name' => __( 'That hasn\'t been updated within 3 days', 'logichop' ),
										'value' => '3 DAY'
									),
									2=> array (
										'name' => __( 'That hasn\'t been updated within 5 days', 'logichop' ),
										'value' => '5 DAY'
									),
									3 => array (
										'name' => __( 'That hasn\'t been updated within 7 days', 'logichop' ),
										'value' => '7 DAY'
									),
									4 => array (
										'name' => __( 'That hasn\'t been updated within 14 days', 'logichop' ),
										'value' => '14 DAY'
									),
									5 => array (
										'name' => __( 'That hasn\'t been updated within 30 days', 'logichop' ),
										'value' => '30 DAY'
									),
									6 => array (
										'name' => __( 'That hasn\'t been updated within 45 days', 'logichop' ),
										'value' => '45 DAY'
									),
									7 => array (
										'name' => __( 'That hasn\'t been updated within 60 days', 'logichop' ),
										'value' => '60 DAY'
									),
									8 => array (
										'name' => __( 'That hasn\'t been updated within 90 days', 'logichop' ),
										'value' => '90 DAY'
									),
									9 => array (
										'name' => __( 'That hasn\'t been updated within 180 days', 'logichop' ),
										'value' => '180 DAY'
									),
								)
							);

		return $settings;
	}
}
