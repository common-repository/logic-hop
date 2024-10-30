<?php

if (!defined('ABSPATH')) die;

/**
 * Setup
 *
 * @since      1.0.0
 * @package    LogicHop
 */

class LogicHop_Measurement {


	private static $instances = [];

	protected function __construct() { }
	protected function __clone() { }
	public function __wakeup()
	{
		throw new \Exception("Cannot unserialize a singleton.");
	}

	/**
	 * @return LogicHop_Measurement
	 */
	public static function getInstance(): LogicHop_Measurement
	{
		$cls = static::class;
		if (!isset(self::$instances[$cls])) {
			self::$instances[$cls] = new static();
		}

		return self::$instances[$cls];
	}

	/**
	 * Measurement API
	 *
	 * @param $category
	 * @param $action
	 *
	 * @return void
	 * @since        4.0.0
	 */
	public function measure ( $category, $action,$pluginlist =false ) {
		$payload        = array();
		if ($pluginlist) {
            $all_plugins =array();
            if ( !function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            if (function_exists('get_plugins')){
                $all_plugins = get_plugins();
            }
            $active_plugins = get_option( 'active_plugins' );
			foreach ( $active_plugins as $index => $plugin ) {
				if ( array_key_exists( $plugin, $all_plugins ) ) {
					array_push( $payload, $all_plugins[ $plugin ] );
				}
			}
		}

		$options = get_option( 'logichop-settings' );
		$api_key ="na";
		if ( isset( $options['api_key'] ) ) {
			$api_key= $options['api_key'];
		}
		$instanceId=get_option('logichop_instance',"na");
		$server =isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';

		$url ="https://plugin.logichop.com/measure?a=".$action ."&lk=".$api_key ."&cat=" .$category . "&cid=". $instanceId ."&ver=".Version ."&domain=".$server ;
		$post_args = array (
				'timeout' => 2,
				'headers' => array ( 'Content-Type'=>'application/json' ),
				'body' => json_encode($payload)
		);
		$response = wp_remote_post( $url ,$post_args );
	}
}
