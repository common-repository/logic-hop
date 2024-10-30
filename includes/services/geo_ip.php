<?php

if (!defined('ABSPATH')) die;

/**
 * Geolocation from IP Address.
 *
 * Provides geolocation.
 *
 * @since      1.1.0
 * @package    LogicHop
 * @subpackage LogicHop/includes/services
 */

class LogicHop_Geo_IP {

	/**
	 * Core functionality & logic class
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      LogicHop_Core    $logic    Core functionality & logic.
	 */
	private $logic;

	/**
	 * EU Countries
	 *
	 * @since    3.0.0
	 * @access   public
	 * @var      array    $eu    European Union country codes
	 */
	public $eu = array ( 'AT', 'BE', 'HR', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB' );

	/**
	 * GDPR Countries
	 *
	 * @since    3.1.6
	 * @access   public
	 * @var      array    $gdpr    GDPR country codes
	 */
	public $gdpr = array ( 'AT', 'BE', 'HR', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB', 'GP', 'GF', 'MQ', 'YT', 'RE', 'MF' );

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    	1.1.0
	 * @param       object    $logic	LogicHop_Core functionality & logic.
	 */
	public function __construct( $logic ) {
		$this->logic = $logic;
	}

	/**
	 * Retrieve Geolocation Data
	 *
	 * @since    	1.1.0
	 * @param      	string     	$ip       IP Address
	 * @return      object    	Geolocation object
	 */
	public function geolocate ($ip = '0.0.0.0') {

		$geo = $this->geo_object();
		$data = array ( 'ip' => $ip );

		$data = $this->logic->api_post('geolocate', $data);

		if (isset($data['geolocation'])) {
			$geo->Active = true;
			if (isset($data['geolocation']['IP']))				$geo->IP 			= $data['geolocation']['IP'];
			if (isset($data['geolocation']['CountryCode'])) 	$geo->CountryCode 	= $data['geolocation']['CountryCode'];
			if (isset($data['geolocation']['CountryName'])) 	$geo->CountryName 	= $data['geolocation']['CountryName'];
			if (isset($data['geolocation']['RegionCode'])) 		$geo->RegionCode 	= $data['geolocation']['RegionCode'];
			if (isset($data['geolocation']['RegionName'])) 		$geo->RegionName 	= $data['geolocation']['RegionName'];
			if (isset($data['geolocation']['City'])) 			$geo->City 			= $data['geolocation']['City'];
			if (isset($data['geolocation']['ZIPCode'])) 		$geo->ZIPCode 		= $data['geolocation']['ZIPCode'];
			if (isset($data['geolocation']['TimeZone'])) 		$geo->TimeZone 		= $data['geolocation']['TimeZone'];
			if (isset($data['geolocation']['Latitude']))		$geo->Latitude 		= $data['geolocation']['Latitude'];
			if (isset($data['geolocation']['Longitude'])) 		$geo->Longitude 	= $data['geolocation']['Longitude'];
			if (isset($data['geolocation']['MetroCode'])) 		$geo->MetroCode 	= $data['geolocation']['MetroCode'];
			if (isset($data['geolocation']['CountryCode'])) 	$geo->EU 			= $this->is_EU( $data['geolocation']['CountryCode'] );
			if (isset($data['geolocation']['CountryCode'])) 	$geo->GDPR 			= $this->is_GDPR( $data['geolocation']['CountryCode'] );
		}

		return $geo;
	}

	/**
	 * Generate Geolocation Object
	 *
	 * @since    	1.1.0
	 * @return      object    	Geolocation object skeleton
	 */
	public function geo_object () {
		$geo = new stdclass;

		$geo->Active		= false;
		$geo->IP 			= '0.0.0.0';
		$geo->CountryCode 	= '';
		$geo->CountryName 	= '';
		$geo->RegionCode 	= '';
		$geo->RegionName 	= '';
		$geo->City 			= '';
		$geo->ZIPCode 		= '';
		$geo->TimeZone 		= '';
		$geo->Latitude 		= 0;
		$geo->Longitude 	= 0;
		$geo->MetroCode 	= 0;
		$geo->EU 			= false;
		$geo->GDPR 			= false;

		return $geo;
	}

	/**
	 * Check if country is part of the EU
	 *
	 * @since    	3.0.0
	 * @param      	string     	$country_code      Two-character country code
	 * @return      boolean    	Country is part of EU
	 */
	public function is_EU ( $country_code ) {
		if ( in_array( $country_code, $this->eu ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check user country for GDPR
	 *
	 * @since    	3.0.0
	 * @return      boolean    	Country is part of GDPR
	 */
	public function is_GDPR ( $country_code ) {
		if ( in_array( $country_code, $this->gdpr ) ) {
			return true;
		}
		return false;
	}
}
