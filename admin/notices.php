<?php

  /*
    notice-error
    notice-warning
    notice-success
    notice-info
  */

  $notices = array (
      '3.8.1' => array (
          'type' => 'notice-warning',
          'dismissable' => true,
          'remember' => true,
          'require_active' => false,
          'content' => '<h3>Logic Hop 3.8.1 Update Notice</h3><p>Logic Hop 3.8.1 remove the need of using the Logic Hop Storage plugin. and allow a 14 days trial key generation from the setting Page.The update also adds as some minor functional improvements.</p><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947722-logic-hop-update-notices/?ref=plugin#3.8.1" target="_blank">Update Notes</a> for complete details.</p>'
      ),
      '3.6.0' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>Logic Hop 3.6.0 Update Notice</h3><p>Logic Hop 3.6.0 is a major update which removes PHP Sessions and fixes the WordPress <em>Site Health</em> critical issue. The update also adds as some minor functional improvements.</p><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947722-logic-hop-update-notices/?ref=plugin#3.6.0" target="_blank">Update Notes</a> for complete details.</p>'
    ),
    '3.5.0' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>Logic Hop 3.5.0 Update Notice</h3><p>Logic Hop 3.5.0 is a minor update with a setting to disable geolocation, new logic types, and a filter for overriding conditions as well as some minor functional improvements.</p><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947722-logic-hop-update-notices/?ref=plugin#3.5.0" target="_blank">Update Notes</a> for complete details.</p>'
    ),
    '3.4.1' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>Logic Hop 3.4.1 Update Notice</h3><p>Logic Hop 3.4.1 is a minor update with support for setting and deleting goals in anchor links as well as some minor functional improvements.</p><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947722-logic-hop-update-notices/?ref=plugin#3.4.1" target="_blank">Update Notes</a> for complete details.</p>'
    ),
    '3.3.5' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>Logic Hop 3.3.5 Update Notice</h3><p>Logic Hop 3.3.5 is a minor update with support for redirect variables, operating system conditions, and some functional improvements.</p><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947722-logic-hop-update-notices/?ref=plugin#3.3.5" target="_blank">Update Notes</a> for complete details.</p>'
    ),
    '3.3.4' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>Logic Hop 3.3.4 Update Notice</h3><p>Logic Hop 3.3.4 is a significant update with Quick Start settings, anti-flicker Javascript rendering, new condition types, condition duplication, and advanced Logic Bar functionality.</p><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947722-logic-hop-update-notices/?ref=plugin#3.3.4" target="_blank">Update Notes</a> for complete details.</p>'
    ),
    '3.2.5' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>Logic Hop 3.2.5 Update Notice</h3><p>Logic Hop 3.2.5 is a minor update which includes a new data preview tool for previewing logic and conditions.</p><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947722-logic-hop-update-notices/?ref=plugin#3.2.5" target="_blank">Update Notes</a> for complete details.</p>'
    ),
    '3.2.4' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>Logic Hop 3.2.4 Update Notice</h3><p>Logic Hop 3.2.4 is a minor update which includes support for dynamic content embeds and  utility code updates.</p><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947722-logic-hop-update-notices/?ref=plugin#3.2.4" target="_blank">Update Notes</a> for complete details.</p>'
    ),
    '3.2.3' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>Logic Hop 3.2.3 Update Notice</h3><p>Logic Hop 3.2.3 is a significant update which makes Logic Hop easier to configure on hosting environments which use Varnish and Object Caching. As the update changes how Logic Hop handles cookies, it is important to test your website after updating.</p><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947722-logic-hop-update-notices/?ref=plugin#3.2.3" target="_blank">Update Notes</a> for complete details.</p>'
    ),
    '3.2.2' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => sprintf( '<h3>Logic Hop 3.2.2 Upgrade Notice</h3>
      <h4 style="margin-bottom: -5px;">Nested Logic Tags</h4>
      <p>Logic Hop 3.2.2 includes a new experimental feature, <strong>Nested Logic Tags</strong>, which allow for conditions within conditions.</p>
      <p><strong>Nested Logic Tags are disabled by default.</strong> To enable visit the <a href="%s">Logic Hop Settings</a>, check the <strong>Enable Nested Logic Tags</strong> option and save your settings.<br>Once enabled please take a momement to review your pages which contain Logic Hop conditions and confirm everything appears as expected.<br>If you experience any issues <a href="https://logichop.com/contact/?ref=nested-logic-tags" target="_blank">please let us know</a> and we\'ll work to resolve them immediately.<p><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947534-logic-tags/?ref=plugin-3.2.2#nested-logic-tags" target="_blank">Learn more about Nested Logic Tags.</a></p>
      <h4 style="margin-bottom: -5px;">Goal Groups</h4>
      <p>Logic Hop 3.2.2 also introduces <strong>Goal Groups</strong> which allows for new ways to easily segment and profile site visitors.</p>
      <p><a href="https://logichop.freshdesk.com/support/solutions/articles/80000947532-logic-hop-goals/?ref=plugin-3.2.2#goal-groups" target="_blank">Learn more about Goal Groups.</a></p>', admin_url('admin.php?page=logichop-settings') )
    ),
    'wpengine' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>WPEngine Hosting Notice</h3><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947527-logic-hop-caching/?ref=plugin#wpengine" target="_blank">Logic Hop docs</a> for complete details on how to configure Logic Hop to work with WPEngine hosting and caching.</p>'
    ),
    'pantheon' => array (
      'type' => 'notice-warning',
      'dismissable' => true,
      'remember' => true,
      'require_active' => false,
      'content' => '<h3>Pantheon Hosting Notice</h3><p>Please view the <a href="https://logichop.freshdesk.com/support/solutions/articles/80000947527-logic-hop-caching/?ref=plugin#pantheon" target="_blank">Logic Hop docs</a> for complete details on how to configure Logic Hop to work with Pantheon hosting and caching.</p>'
    ),
  );
