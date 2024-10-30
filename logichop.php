<?php
/**
 * Plugin Name: Logic Hop - Content Personalization for WordPress- CMP Connector
 * Plugin URI:	https://logichop.com
 * Description: Dynamic content personalization for WordPress. Easily create powerful personalized experiences in minutes. Create dynamic content for Elementor, Divi, Beaver Builder or Gutenberg. match segments you already built with your favorite CMS.
 * Version:		3.9.0
 * Author:		Logic Hop
 * Author URI:	https://logichop.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: logichop
 * Domain Path: languages
 *
 * Logic Hop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Logic Hop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */
CONST Version ="3.9.0";

if (!defined('ABSPATH')) { header('location: /'); die; }
/*
if ( ! function_exists('write_log')) {
    function write_log ( $log )  {
        //error_log(wp_debug_backtrace_summary( ));
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}*/
function logichop_activate () {
	update_option('logichop_activated', true);
	require_once plugin_dir_path(__FILE__) . 'includes/activate.php';
	LogicHop_Activate::activate();
}

function logichop_deactivate () {
	require_once plugin_dir_path(__FILE__) . 'includes/deactivate.php';
	LogicHop_Deactivate::deactivate();

    if ( ! class_exists( 'LogicHop_LS_Setup' ) ) {
        require_once('includes/setup.php');
    }

	LogicHop_LS_Setup::deactivate();

	require_once plugin_dir_path(__FILE__) . 'includes/Measurement.php';
	LogicHop_Measurement::getInstance()->measure( "installation", "deactivate",false);

}
function logichop_uninstall() {

	require_once plugin_dir_path(__FILE__) . 'includes/Measurement.php';
	LogicHop_Measurement::getInstance()->measure( "installation", "uninstall",false);

    if (function_exists('is_multisite') && is_multisite()) {
        if (is_super_admin() == false) return;
        $blogs = get_sites();
        foreach ($blogs as $blog) {
            switch_to_blog( $blog['blog_id'] );
            logichop_delete_plugin();
            restore_current_blog();
        }
    } else {
        if (!current_user_can( 'activate_plugins' )) return;
        logichop_delete_plugin();
    }
}

function logichop_delete_plugin () {
	delete_transient( 'logichop' );
	$options = array(LOGICHOP_SETTINGS
    , LOGICHOP_TRIAL
    , LOGICHOP_NB_CHECKCOUNT
    ,LOGICHOP_INSTANCE
    ,'logichop-settings'
    ,'logichop_expiration_lastseen'
    ,'logichop_data'
    ,'logichop_local_storage'
    ,'logichop_version'
    ,'logichop_checkcount');
	if ($options) foreach ($options as $option)  {
		delete_option($option);
	}
	$postTypes = array(
	'logichop-conditions'   => array( 'type' => 'logichop-conditions' ),
	'logichop-logicblocks'  => array( 'type'=>  'logichop-logicblocks'),
	'logichop-logic-bars'   => array( 'type'=>  'logichop-logic-bars' ),
	'logichop-goals'        => array( 'type'=>  'logichop-goals' ,'tax'=>'logichop_goal_group'),
    'logichop-redirects'    => array( 'type'=>  'logichop-redirects' ),
	);
	foreach ($postTypes as $postType) {
		$pts = get_posts(array('numberposts' => -1, 'post_type' => $postType['type']));
		if ($pts)foreach ($pts as $post) {
			wp_delete_post($post->ID, true);
		}
		unregister_post_type( $postType['type'] );
		if ( isset($postType['tax'])  ) {
				unregister_taxonomy_for_object_type( $postType['tax'], $postType['type'] );
		}
	}
	global $wpdb;
	$sql = sprintf( 'DROP TABLE IF EXISTS %slogichop_local_storage', $wpdb->prefix );
	$wpdb->query( $sql );
	delete_option( 'logichop_local_storage' );
	if ( wp_next_scheduled( 'logichop_local_delete_storage' ) ) {
		wp_clear_scheduled_hook( 'logichop_local_delete_storage' );
	}
}

register_activation_hook(__FILE__, 'logichop_activate');
register_deactivation_hook(__FILE__, 'logichop_deactivate');
register_uninstall_hook(__FILE__, 'logichop_uninstall');

function check_version() {
	$version = get_option('logichop_version');
	if (!$version) {
		update_option('logichop_version',Version);
		// No Version it might be an update or an install
		require_once plugin_dir_path(__FILE__) . 'includes/Measurement.php';
		LogicHop_Measurement::getInstance()->measure( "installation", "new", true);
	}else {
		if ( Version !== get_option( 'logichop_version') ) {
			update_option('logichop_version',Version);
			require_once plugin_dir_path(__FILE__) . 'includes/Measurement.php';
			LogicHop_Measurement::getInstance()->measure( "installation", "update", true);
		}
	}
}

add_action('plugins_loaded', 'check_version');

require plugin_dir_path(__FILE__) . 'includes/LogicHop.php';

function logichop_load () {
	$load = true;

	$core_actions_get = array(
		'fetch-list',
		// 'ajax-tag-search',
		'wp-compression-test',
		'imgedit-preview',
		'oembed-cache',
		'autocomplete-user',
		'dashboard-widgets',
		'logged-in',
		'rest-nonce',
	);
	$core_actions_post = array(
		'oembed-cache',
		'image-editor',
		'delete-comment',
		'delete-tag',
		'delete-link',
		'delete-meta',
		'delete-post',
		'trash-post',
		'untrash-post',
		'delete-page',
		'dim-comment',
		'add-link-category',
		'add-tag',
		'get-tagcloud',
		'get-comments',
		'replyto-comment',
		'edit-comment',
		'add-menu-item',
		'add-meta',
		'add-user',
		'closed-postboxes',
		'hidden-columns',
		'update-welcome-panel',
		'menu-get-metabox',
		'wp-link-ajax',
		'menu-locations-save',
		'menu-quick-search',
		'meta-box-order',
		'get-permalink',
		'sample-permalink',
		'inline-save',
		'inline-save-tax',
		'find_posts',
		'widgets-order',
		// 'save-widget',
		'delete-inactive-widgets',
		'set-post-thumbnail',
		'date_format',
		'time_format',
		'wp-remove-post-lock',
		'dismiss-wp-pointer',
		'upload-attachment',
		'get-attachment',
		'query-attachments',
		'save-attachment',
		'save-attachment-compat',
		'send-link-to-editor',
		'send-attachment-to-editor',
		'save-attachment-order',
		'media-create-image-subsizes',
		'heartbeat',
		'get-revision-diffs',
		'save-user-color-scheme',
		'update-widget',
		'query-themes',
		'parse-embed',
		'set-attachment-thumbnail',
		'parse-media-shortcode',
		'destroy-sessions',
		'install-plugin',
		'update-plugin',
		'crop-image',
		'generate-password',
		'save-wporg-username',
		'delete-plugin',
		'search-plugins',
		'search-install-plugins',
		'activate-plugin',
		'update-theme',
		'delete-theme',
		'install-theme',
		'get-post-thumbnail-html',
		'get-community-events',
		'edit-theme-plugin-file',
		'wp-privacy-export-personal-data',
		'wp-privacy-erase-personal-data',
		'health-check-site-status-result',
		'health-check-dotorg-communication',
		'health-check-is-in-debug-mode',
		'health-check-background-updates',
		'health-check-loopback-requests',
		'health-check-get-sizes',
	);

	if ( isset( $_GET ) && isset( $_GET['action'] ) ) {
		if ( in_array( $_GET['action'], $core_actions_get ) ) {
			$load = false;	// WP ADMIN AJAX CORE REQUEST
		}
	}

	if ( isset( $_POST ) && isset( $_POST['action'] ) ) {
		if ( in_array( $_POST['action'], $core_actions_post ) ) {
			$load = false;	// WP ADMIN AJAX CORE REQUEST
		}
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		$load = false;	// WP REST API REQUEST
	}

	if ( strpos( $_SERVER['REQUEST_URI'], 'wp-json') !== false ) {
		$load = false;	// WP REST API REQUEST
	}

	if ( strpos( $_SERVER['REQUEST_URI'], 'wp-cron.php') !== false ) {
		$load = false;	// WP CRON JOB
	}

	if ( isset( $_GET ) && isset( $_GET['doing_wp_cron'] ) ) {
		$load = false;	// WP CRON JOB
	}

	return $load;
}
function logichop_init () {

	$logichop = new LogicHop(plugin_basename(__FILE__));

	$logichop->init();
    do_action('logichop_after_plugin_init', $logichop);

    if (is_admin() && get_option('logichop_activated')) {
        delete_option('logichop_activated');
	    require_once plugin_dir_path(__FILE__) . 'includes/Measurement.php';
	    LogicHop_Measurement::getInstance()->measure( "installation", "active" ,true);
    }

    if (!class_exists('LogicHop_LS_Setup')) {
        require_once('includes/setup.php');
    }

    LogicHop_LS_Setup::disable();

    if ($logichop->logic->data_plan()) {
        return $logichop;
    }else {

        LogicHop_LS_Setup::activate($logichop->get_version());
        if (!class_exists('LogicHop_LocalStorage')) {
            require_once('includes/local_storage.php');
        }
        $localstorage = new LogicHop_LocalStorage($logichop->get_version());
    }
    return $logichop;
}

if ( logichop_load() === true ) {
	$logichop = logichop_init();
}

