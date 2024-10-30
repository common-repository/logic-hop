<?php

if (!defined('ABSPATH')) die;

/**
 * Status
 *
 * @since      1.0.0
 * @package    LogicHop
 */

class LogicHop_LS_Status {
	/**
	 * Get
	 *
	 * @since    	1.0.0
	 */
	public static function get () {
		$response = new stdclass();
		$Client = new stdclass();
		$Client->Active = true;
		$response->Client = $Client;
		return $response;
	}
}
