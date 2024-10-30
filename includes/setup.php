<?php

if (!defined('ABSPATH')) die;
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
/**
 * Setup
 *
 * @since      1.0.0
 * @package    LogicHop
 */

class LogicHop_LS_Setup {
    /**
     *
     */
    public static function disable (  ) {
        $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
        foreach( $active_plugins as $plugin ) {
            if( strpos( $plugin, 'logic-hop-local-storage' ) !== false || strpos( $plugin, 'logichop_local_storage' ) !== false ) {
                deactivate_plugins($plugin,true);
            }
        }
    }
	/**
	 * Activate
	 *
	 * @since    	1.0.0
	 */
	public static function activate ( $version ) {
		global $wpdb;
		$installed_version = get_option( 'logichop_local_storage' );
        if ( $installed_version ) {
			SELF::update( $installed_version, $version );
			return;
		}
		$sql = sprintf( "
				CREATE TABLE IF NOT EXISTS %slogichop_local_storage (
				ID bigint(32) unsigned NOT NULL AUTO_INCREMENT,
				UID varchar(128) NOT NULL DEFAULT '',
				Data text NOT NULL,
				Updated datetime NOT NULL,
				Created datetime NOT NULL,
				PRIMARY KEY (ID),
				UNIQUE (UID)
			) %s;
			",
			$wpdb->prefix,
			$wpdb->get_charset_collate()
		);
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql );
		update_option( 'logichop_local_storage', $version );
		wp_schedule_event( time(), 'twicedaily', 'logichop_local_delete_storage' );
		SELF::utility();
	}

	/**
	 * Update
	 *
	 * @since    	1.0.0
	 */
	public static function update ( $installed_version, $version ) {
		if ( $installed_version != $version ) {
			// Update DB table when necessary
			update_option( 'logichop_local_storage', $version );
		}
		
		SELF::utility();
	}

	/**
	 * Deactivate
	 *
	 * @since    	1.0.0
	 */
	public static function deactivate () {
		global $wpdb;
        $sql = sprintf( 'DROP TABLE IF EXISTS %slogichop_local_storage', $wpdb->prefix );
        $wpdb->query( $sql );
		delete_option( 'logichop_local_storage' );
		if ( wp_next_scheduled( 'logichop_local_delete_storage' ) ) {
			wp_clear_scheduled_hook( 'logichop_local_delete_storage' );
		}

		SELF::utility();
	}

	/**
	 * Global tasks
	 *
	 * @since    	1.0.0
	 */
	public static function utility () {
		delete_transient( 'logichop' );
	}

}
