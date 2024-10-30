<?php

	if (!defined('ABSPATH')) { header('location: /'); die; }
    if ( ! class_exists( 'LogicHop\LogicHop_Measurement' ) ) {
	    $dir= dirname( __FILE__ ) . '/' . '../../includes/Measurement.php';
	    require_once($dir);
    }
	$tab = 'settings';
	if (isset($_GET['tab'])) {
		$tab = $_GET['tab'];
	}

	$status = false;
    $label="no licence";
    $this->logic->reset_checkcount();
    $status=$this->logic->validate_api("settings Page");
    $label =$status->status;

    LogicHop_Measurement::getInstance()->measure( "activation", $label,true);

    if ($status->valid==true) {
			$api_message_css = 'success';
			$api_message = '';
		} else {
			if ($this->logic->get_option('api_key', false)) {
					$api_message_css = 'error';
					$api_message = sprintf('<p><strong>%s</strong></p><p>%s %s</p>',
								__('You have entered an invalid API Key and/or Domain Name.', 'logichop'),
								__('Logic Hop requires a valid License or API Key.', 'logichop'),
								__('<a target="_blank" href="https://spf.logichop.com/checkout?utm_source=plugin" target="_blank">Learn more</a> about Logic Hop plans.', 'logichop'),
							);
		} else {
			$api_message_css = 'info';
			$api_message = sprintf('<p>%s</p><p>%s</p>',
							__('Logic Hop requires a valid License or API Key.', 'logichop'),
							__('<a target="_blank" href="https://spf.logichop.com/checkout?utm_source=plugin" target="_blank">Learn more</a> about Logic Hop plans.', 'logichop'),
						);
		}
	}

	print('<div class="wrap">');

	printf('<h2>%s</h2>', __('Logic Hop Settings', 'logichop') );

    if ($status->valid) {
        printf('<div id="valid-notice" style="background-color: #f2f2f2; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">');
        printf('<h2 style="color: #333; text-align: left;">Your License informations</h2>');
        if ($status->valid==true) {
            if (($this->logic->check_validity())){
                printf('<p style="font-size: 24px; color: #666; text-align: left; margin-top: 10px;">Your License Key is ACTIVE <i>(%s)</i>.</p>', $status->status);
            }
            if (isset($status->expiration_date) ) {
                printf('<p>Expiration : <strong>%s</strong></p>', $status->expiration_date);
            }
            if (isset($status->domain) ) {
                printf('<p>Domain : <strong>%s</strong></p>', $status->domain);
            }
            if (isset($status->version) ) {
                printf('<p>Version: <strong>%s</strong></p>', $status->version);
            }
	    print('</div>');
        }
    }
    else {
			printf('<div id="valid-notice" style="background-color: #f2f2f2; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">');
			if ( isset( $status->message)  && $status->message <> "" ) {
				    printf( '<p>Message  : [ <strong>%s</strong> ] </p>',
					    $status->message
				    );
			}
		  if ( isset( $status->get_link )) {
            printf(
                '<div style="background-color: #f2f2f2; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #333; text-align: center;">Experience Personalized Content Like Never Before!</h2>
        <p style="font-size: 18px; color: #666; text-align: center; margin-top: 10px;">Unlock the potential of dynamic content personalization and elevate your WordPress site to new heights. Use Coupon <b>IN10</b> for 10%% reduction</p>
        <a target="_blank" class="get-license button" style="display: block; width: fit-content; margin: 20px auto; padding: 10px 15px; font-size: 18px; background-color: #3498db; color: white; border-radius: 5px; text-decoration: none; text-align: center;" href="%s">
            Get Your License Key Now! 
        </a>
     </div>',
                'https://spf.logichop.com/checkout'
      );

        }
		    print('</div>');

    }

	settings_errors();
?>

<style>
	.logichop-yellow { background-color: yellow !important; }
    .logic-warning {
        background-color: lightcoral !important;
    }
    /* Absolute Center Spinner */
    .loading {
        position: fixed;
        z-index: 999;
        height: 2em;
        width: 2em;
        overflow: inherit;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    /* Transparent Overlay */
    .loading:before {
        content: '';
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(rgba(20, 20, 20, .8), rgba(0, 0, 0, .8));

        background: -webkit-radial-gradient(rgba(20, 20, 20, .8), rgba(0, 0, 0, .8));
    }

    /* :not(:required) hides these rules from IE9 and below */
    .loading:not(:required) {
        /* hide "loading..." text */
        font: 0/0 a;
        color: transparent;
        text-shadow: none;
        background-color: transparent;
        border: 0;
    }

    .loading:not(:required):after {
        content: '';
        display: block;
        font-size: 10px;
        width: 1em;
        height: 1em;
        margin-top: -0.5em;
        -webkit-animation: spinner 150ms infinite linear;
        -moz-animation: spinner 150ms infinite linear;
        -ms-animation: spinner 150ms infinite linear;
        -o-animation: spinner 150ms infinite linear;
        animation: spinner 150ms infinite linear;
        border-radius: 0.5em;
        -webkit-box-shadow: rgba(255, 255, 255, 0.75) 1.5em 0 0 0, rgba(255, 255, 255, 0.75) 1.1em 1.1em 0 0, rgba(255, 255, 255, 0.75) 0 1.5em 0 0, rgba(255, 255, 255, 0.75) -1.1em 1.1em 0 0, rgba(255, 255, 255, 0.75) -1.5em 0 0 0, rgba(255, 255, 255, 0.75) -1.1em -1.1em 0 0, rgba(255, 255, 255, 0.75) 0 -1.5em 0 0, rgba(255, 255, 255, 0.75) 1.1em -1.1em 0 0;
        box-shadow: rgba(255, 255, 255, 0.75) 1.5em 0 0 0, rgba(255, 255, 255, 0.75) 1.1em 1.1em 0 0, rgba(255, 255, 255, 0.75) 0 1.5em 0 0, rgba(255, 255, 255, 0.75) -1.1em 1.1em 0 0, rgba(255, 255, 255, 0.75) -1.5em 0 0 0, rgba(255, 255, 255, 0.75) -1.1em -1.1em 0 0, rgba(255, 255, 255, 0.75) 0 -1.5em 0 0, rgba(255, 255, 255, 0.75) 1.1em -1.1em 0 0;
    }

    /* Animation */

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    @-moz-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    @-o-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    @keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    .get-license {
        background: #8766f6;
        /* background: linear-gradient(135deg, #8766f6 0%,#4c8ffa 100%); */
        color: #fff;
        padding: 10px 10px;
        margin-top: 20px;
        text-align: left;
        font-size: 1.05em;
        display: block;
    }
</style>
</style>
<br>
<div style="background-color: #f2f2f2; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
	<h2 style="margin-bottom: 0;"><?php _e( 'Quick Settings', 'logichop' ); ?></h2>
	<p>
			<?php _e( 'Use the buttons below to apply one or more of the following presets to your Logic Hop Settings.', 'logichop' ); ?>
			<br>
			<?php _e( 'Click the <strong>Save Changes</strong> button at the bottom of the page to save your settings.', 'logichop' ); ?>
	</p>
	<p>
		<button id="quick_recommended" class="button" style="min-width: 200px; margin-right: 10px;"><?php _e( 'Logic Hop Recommended', 'logichop' ); ?></button>
		<span style="background: white; padding: 5px;"><?php _e( 'Standard Logic Hop settings.', 'logichop' ); ?></span>
		<script>
			jQuery( '#quick_recommended' ).click( function () {
				jQuery( '#logichop-settings\\[cookie_ttl\\]' ).val( '+ 1 month' ).addClass( 'logichop-yellow' );
				jQuery( '#logichop-settings\\[js_variables\\]' ).prop( 'checked', true );
				jQuery( 'label[for="js_variables"]' ).addClass( 'logichop-yellow' );
				jQuery( '#logichop-settings\\[js_anti_flicker\\]' ).prop( 'checked', true );
				jQuery( 'label[for="js_anti_flicker"]' ).addClass( 'logichop-yellow' );
				jQuery( '#logichop-settings\\[logichop_local_storage_delete\\]' ).val( '30 DAY' ).addClass( 'logichop-yellow' );
				alert( '<?php _e( 'Settings updated & highlighted in yellow. Please review then click Save Changes to save your settings.', 'logichop' ); ?>' );
			} );
		</script>
	</p>
	<p>
		<button id="quick_cached" class="button" style="min-width: 200px; margin-right: 10px;"><?php _e( 'Cached Website Settings', 'logichop' ); ?></button>
		<span style="background: white; padding: 5px;"><?php _e( 'For sites using caching plugins, object cache or Cloudflare.', 'logichop' ); ?></span>
		<script>
			jQuery( '#quick_cached' ).click( function () {
				jQuery( '#logichop-settings\\[js_tracking\\]' ).prop( 'checked', true );
				jQuery( 'label[for="js_tracking"]' ).addClass( 'logichop-yellow' );
				jQuery( '#logichop-settings\\[js_variables\\]' ).prop( 'checked', true );
				jQuery( 'label[for="js_variables"]' ).addClass( 'logichop-yellow' );
				jQuery( '#logichop-settings\\[js_anti_flicker\\]' ).prop( 'checked', true );
				jQuery( 'label[for="js_anti_flicker"]' ).addClass( 'logichop-yellow' );
				var domain = jQuery( '#logichop-settings\\[domain\\]' ).val();
				if ( domain ) {
					jQuery( '#logichop-settings\\[ajax_referrer\\]' ).val( domain ).addClass( 'logichop-yellow' );
				}
				jQuery( '#logichop-settings\\[js_anti_flicker_timeout\\]' ).val( '3000' ).addClass( 'logichop-yellow' );
				alert( '<?php _e( 'Settings updated & highlighted in yellow. Please review then click Save Changes to save your settings.', 'logichop' ); ?>' );
			} );
		</script>
	</p>
	<p>
		<button id="quick_gdpr" class="button" style="min-width: 200px; margin-right: 10px;"><?php _e( 'GDPR Require Consent', 'logichop' ); ?></button>
		<span style="background: white; padding: 5px;"><?php _e( 'Recommended for GDPR compliance.', 'logichop' ); ?></span>
		<script>
			jQuery( '#quick_gdpr' ).click( function () {
				jQuery( '#logichop-settings\\[cookie_ttl\\]' ).val( '+ 1 week' ).addClass( 'logichop-yellow' );
				jQuery( '#logichop-settings\\[logichop_local_storage_delete\\]' ).val( '7 DAY' ).addClass( 'logichop-yellow' );
				jQuery( '#logichop-settings\\[consent_require\\]' ).val( 'gdpr' ).addClass( 'logichop-yellow' );
				if ( ! jQuery( '#logichop-settings\\[consent_cookie\\]' ).val() ) {
					jQuery( '#logichop-settings\\[consent_cookie\\]' ).val( '<?php _e( 'ENTER YOUR CONSENT COOKIE NAME', 'logichop' ); ?>' ).addClass( 'logichop-yellow' );
				}
				alert( '<?php _e( 'Settings updated & highlighted in yellow. Please review then click Save Changes to save your settings.', 'logichop' ); ?>' );
			} );
		</script>
	</p>
	<p>
		<button id="quick_debug" class="button" style="min-width: 200px; margin-right: 10px;"><?php _e( 'Toggle Test / Debug Mode', 'logichop' ); ?></button>
		<span style="background: white; padding: 5px;"><?php _e( 'Enable/disable data output display.', 'logichop' ); ?></span>
		<script>
			jQuery( '#quick_debug' ).click( function () {
				if ( jQuery( '#logichop-settings\\[debug_mode\\]' ).prop( 'checked' ) ) {
					jQuery( '#logichop-settings\\[debug_mode\\]' ).prop( 'checked', false );
				} else {
					jQuery( '#logichop-settings\\[debug_mode\\]' ).prop( 'checked', true );
				}
				jQuery( 'label[for="debug_mode"]' ).addClass( 'logichop-yellow' );
				alert( '<?php _e( 'Settings updated & highlighted in yellow. Please review then click Save Changes to save your settings.', 'logichop' ); ?>' );
			} );
		</script>
	</p>
</div>

<?php
	$integration_tabs = '';
	$integration_tabs = apply_filters('logichop_admin_settings_tabs', $integration_tabs, $tab);


	printf('<h2 class="nav-tab-wrapper">
            	<a href="%s" class="nav-tab %s">%s</a>
            	%s
            	<a href="%s" class="nav-tab %s">%s</a>
        	</h2>',
        	'?page=logichop-settings',
        	($tab == 'settings') ? 'nav-tab-active' : '',
        	__('Settings', 'logichop'),
        	$integration_tabs,
        	'?page=logichop-settings&tab=instructions',
        	($tab == 'instructions') ? 'nav-tab-active' : '',
        	__('Instructions', 'logichop')
        );

	if ($tab == 'settings') {
		print('<form method="post" action="options.php">');
            submit_button();
			settings_fields( 'logichop-settings' );
			do_settings_sections( 'logichop-settings' );
			submit_button();
		print('</form>');
	} else if ($tab == 'instructions') {
		include_once('instructions.php');
	} else if ($tab == 'addons') {
		include_once('addons.php');
	}

	do_action('logichop_admin_settings_page', $tab);

	print('</div>');
