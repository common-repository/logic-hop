<?php

if (!defined('ABSPATH')) die;

/**
 * Data
 *
 * @since      1.0.0
 * @package    LogicHop
 */

class LogicHop_LS_Data {

	/**
	 * Get
	 *
	 * @since    	1.0.0
	 */
	public static function get ( $uid, $location ) {
		global $wpdb;
		$table = sprintf( '%slogichop_local_storage', $wpdb->prefix );
		$response = $wpdb->get_row( "SELECT * FROM $table WHERE UID = '$uid'" );
		if ( $response ) {
			$data = json_decode( $response->Data );
			$data->Location = $location;
			return $data;
		}
		return false;
	}
	/**
	 * Save
	 *
	 * @since    	1.0.0
	 */
	public static function save ( $logichop_data ) {
		global $wpdb;
		if ( ! isset( $logichop_data->UID ) ) {
			return false;
		}
		$table = sprintf( '%slogichop_local_storage', $wpdb->prefix );
		$data = SELF::prepare( $logichop_data );
		$wpdb->query(
			$wpdb->prepare(
				"
				INSERT INTO $table
				( UID, Data, Updated, Created )
				VALUES ( %s, %s, NOW(), NOW() )
				ON DUPLICATE KEY UPDATE
				Data = %s, Updated = NOW()
				",
        array(
					$logichop_data->UID,
					$data,
					$data
				)
			)
		);

	}

	/**
	 * Prepare data
	 *
	 * @since    	1.0.0
	 */
	public static function prepare ( $logichop_data ) {

		$data = new stdclass();
		$data->Source 			= $logichop_data->Source;
		$data->TotalVisits	= $logichop_data->TotalVisits;
		$data->LandingPage 	= $logichop_data->LandingPage;
		$data->LeadScore 		= $logichop_data->LeadScore;
		$data->Pages 				= $logichop_data->Pages;
		$data->Goals 				= $logichop_data->Goals;
		$data->category 		= $logichop_data->Categories;
		$data->tag 					= $logichop_data->Tags;

		if ( isset( $logichop_data->Timestamp ) ) {

			if ( isset( $logichop_data->Timestamp->FirstDate ) ) {
				$data->FirstDate = $logichop_data->Timestamp->FirstDate;
			}

			if ( isset( $logichop_data->Timestamp->LastDate ) ) {
				$data->LastDate = $logichop_data->Timestamp->LastDate;
			}
		}

		if ( isset( $logichop_data->DripID ) ) {
			$data->drip = $logichop_data->DripID;
		}

		if ( isset( $logichop_data->ConvertKitID ) ) {
			$data->convertkit = $logichop_data->ConvertKitID;
		}

		if ( isset( $logichop_data->WooCommerce->Products ) ) {
			$data->wc_product = $logichop_data->WooCommerce->Products;
		}
		if ( isset( $logichop_data->WooCommerce->Categories ) ) {
			$data->wc_category = $logichop_data->WooCommerce->Categories;
		}
		if ( isset( $logichop_data->WooCommerce->Tags ) ) {
			$data->wc_tag = $logichop_data->WooCommerce->Tags;
		}

		if ( isset( $logichop_data->GravityForms ) ) {
			$gf_form = array();
			foreach ( $logichop_data->GravityForms as $k => $v ) {
				$key = sprintf( '%s:%s', $k, $v );
				$gf_form[$key] = 1;
			}
			$data->gf_form = $gf_form;
		}

		return json_encode( $data );
	}

	/**
	* Delete expired data
	*
	* @since    1.0.0
	*/
  public static function delete () {
        global $wpdb;
		$interval = '7 DAY';
		$options = get_option( 'logichop-settings' );
		if ( isset( $options['logichop_local_storage_delete'] ) ) {
			$interval = $options['logichop_local_storage_delete'];
		}
		$table = sprintf( '%slogichop_local_storage', $wpdb->prefix );
		$wpdb->query(
			"
			DELETE FROM $table
			WHERE Updated < DATE_SUB( NOW(), INTERVAL $interval )
			"
		);
  }
}
