<?php

if (!defined('ABSPATH')) die;

/**
 * Geolocate
 *
 * @since      1.0.0
 * @package    LogicHop
 */

CONST root ="https://geo.logichop.com";

 class LogicHop_LS_Geolocate {

	/**
 	* Get
 	*
 	* @since    	1.0.0
 	*/
 	public static function get ( $ip = '0.0.0.0' , $license= null): stdclass
    {
        if ($license==null ||  $license=="" ) {
            return     self::model();
        }

        if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
            $ip = '0.0.0.0';
        }
        $geo = self::model();

        $url =root . "/geolocation?license=".urlencode($license)."&ip=" . urlencode($ip) . "&action=get";
        $response = wp_remote_get( $url ,array('timeout' => 2) );
        if( is_array($response) ) {
            $location =json_decode( $response['body'], true );
            if ( $location ) {
                $geo->IP 			= $ip;
                $geo->Active		= $location["active"];
                $geo->CountryCode   = $location["cc"];
                $geo->CountryName   = $location["cc_name"];
                $geo->RegionCode 	= $location["rg_code"];
                $geo->RegionName 	= $location["rg_name"];
                $geo->City 			= $location["city"];
                $geo->ZIPCode 		= $location["zip"];
                $geo->TimeZone	 	= $location["tz"];
                $geo->Latitude 		= $location["lat"];
                $geo->Longitude		= $location["lon"];
                $geo->MetroCode 	= $location["dma"];
            }
        }
        return $geo;
 	}

  /**
 	* Geolocation class
 	*
 	* @since    	1.0.0
 	*/
  public static function model (): stdclass
  {
    $geo = new stdclass;
    $geo->IP 				  = '0.0.0.0';
    $geo->Active			= false;
    $geo->CountryCode = '';
    $geo->CountryName = '';
    $geo->RegionCode  = '';
    $geo->RegionName 	= '';
    $geo->City 				= '';
    $geo->ZIPCode 		= '';
    $geo->TimeZone 		= '';
    $geo->Latitude 		= 0.0;
    $geo->Longitude		= 0.0;
    $geo->MetroCode 	= 0;
    return $geo;
  }

}
