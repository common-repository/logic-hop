<?php

if (!defined('ABSPATH')) die;

/**
 * Validate
 *
 * @since      1.0.0
 * @package    LogicHop
 */

 class LogicHop_LS_Validate {

	/**
  * Array of add-ons to add to condition builder
  *
  * @since    	1.0.0
  */
	static $addons = array(
		'gravity-forms',
		'google-analytics',
		'facebook-pixel',
		'woocommerce',
		'convertkit',
		'drip',
		'hubspot',
		'jabmo'
	);

	/**
 	* Get
 	*
 	* @since    	1.0.0
 	*/
 	public static function get ( $data ) {
        $response = new stdclass();
		$response->Client = true;
		$response->AddOns = json_encode( self::$addons );
		$response->Conditions = self::conditions( $data );
		return $response;
 	}

	/**
 	* Return conditions for condition builder
 	*
 	* @since    	1.0.0
 	*/
 	private static function conditions ( $data ) {
		require_once('conditions.php');


	    if (isset($logic['default'])) {
		    $regex      = '/\s+/S';
		    $conditions = trim( preg_replace( $regex, ' ', $logic['default'] ) );
	    }

		if ( isset( $data['addons'] ) ) {
			$installed_addons = explode( ',', $data['addons'] );
			foreach ( self::$addons as $addon ) {
				if ( isset( $logic[ $addon ] ) && in_array( $addon, $installed_addons ) ) {
					$conditions .= ',' . trim( preg_replace( $regex, ' ', $logic[ $addon ] ) );
				}
			}
		}
		if (isset($conditions)) {
			return sprintf( '{%s}', $conditions );
		}else {
			return "";
		}
	}
 }
