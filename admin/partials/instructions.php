<?php

	printf('<div class="logichop_settings_container">
			<h2>%s</h2>
			<ul class="logichop-ul">
				<li>%s</li>
			</ul>

			<h2>%s</h2>
			<ul class="logichop-ul">
				<li><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947521-how-to-configure-logic-hop/?ref=plugin" target="_blank">%s</a></li>
				<li><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947530-logic-hop-conditions/?ref=plugin" target="_blank">%s</a></li>
				<li><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947532-logic-hop-goals/?ref=plugin" target="_blank">%s</a></li>
				<li><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947557-logic-hop-for-pages-posts/?ref=plugin" target="_blank">%s</a></li>
				<li><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947534-logic-tags/?ref=plugin" target="_blank">%s</a></li>
				<li><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947696-conditional-widgets/?ref=plugin" target="_blank">%s</a></li>
				<li><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947700-logic-hop-conditional-css/?ref=plugin" target="_blank">%s</a></li>
				<li><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947527-logic-hop-caching/?ref=plugin" target="_blank">%s</a></li>
				<li><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947718-condition-type-operator-reference/?ref=plugin" target="_blank">%s</a></li>
			</ul>
			</div>',

			__('Quick Start Instructions', 'logichop'),
			__('<a href="https://logichop.freshdesk.com/support/solutions/articles/80000947519-famous-five-minute-quick-start-guide/?ref=plugin" target="_blank">View the Logic Hop 5-Minute Quick Start Guide.</a>', 'logichop'),

			__('Logic Hop Documentation', 'logichop'),
			__('How to Install & Configure Logic Hop', 'logichop'),
			__('How to Create Logic Hop Conditions', 'logichop'),
			__('How to Create Logic Hop Goals', 'logichop'),
			__('Using Logic Hop with Pages & Posts', 'logichop'),
			__('Working with Logic Tags', 'logichop'),
			__('Using Logic Hop with Widgets', 'logichop'),
			__('Using Logic Hop as Conditional CSS', 'logichop'),
			__('Working with Logic Hop Insights', 'logichop'),
			__('Using Logic Hop with Cache Plugins', 'logichop'),
			__('Condition Type & Operator Reference', 'logichop')
		);

	$options 	= get_option('logichop-settings');
	$theme 		= wp_get_theme();
  $hosting = '';

	if ( $this->is_pantheon() ) {
		$hosting = '<li><strong>Hosting Provider:</strong> Pantheon</li>';
	}
	if ( $this->is_wpengine() ) {
		$hosting = '<li><strong>Hosting Provider:</strong> WPEngine</li>';
	}

	printf('<div class="logichop_settings_container">
			<h2>%s</h2>
			<ul class="logichop-ul-blank">
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				<li><strong style="color: rgb(255,0,0);">%s</strong></li>
				<li><strong>%s</strong> %s</li>
				<li><strong>%s</strong> %s</li>
				%s
			</ul>
			</div>',
			__('Configuration', 'logichop'),
			__('Wordpress Domain:', 'logichop'),
			$_SERVER['SERVER_NAME'],
			__('Domain Name:', 'logichop'),
			(isset($options['domain']) && $options['domain']) ? $options['domain'] : __('Not Set', 'logichop'),
			__('Wordpress Version:', 'logichop'),
			$wp_version,
			__('PHP Version:', 'logichop'),
			PHP_VERSION,
			__('Logic Hop Version:', 'logichop'),
			$this->version,
			__('Logic Hop License/API Key', 'logichop'),
			(isset($options['api_key']) && $options['api_key']) ? $options['api_key'] : __('Disabled', 'logichop'),
			__('Cookie TTL:', 'logichop'),
			isset($options['cookie_ttl']) ? $options['cookie_ttl'] : __('Not Set', 'logichop'),
			__('Javscript Referrer:', 'logichop'),
			isset($options['ajax_referrer']) ? $options['ajax_referrer'] : __('Not Set', 'logichop'),
			__('Cache Enabled:', 'logichop'),
			(defined('WP_CACHE') && WP_CACHE) ? __('Enabled', 'logichop') : __('Disabled', 'logichop'),
			__('Javscript Mode:', 'logichop'),
			($this->logic->js_tracking()) ? __('Enabled', 'logichop') : __('Disabled', 'logichop'),
			(defined('WP_CACHE') && WP_CACHE && !$this->logic->js_tracking()) ? __('Cache Enabled: Javascript Tracking is recommended.', 'logichop') : '',
			__('Theme:', 'logichop'),
			sprintf('%s, %s', $theme->Name, $theme->Version),
			__('Plugins:', 'logichop'),
			$this->get_active_plugins(true),
			$hosting
		);
