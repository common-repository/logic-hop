<?php

	if (!defined('ABSPATH')) { header('location: /'); die; }

    $condition_count= $this->logic->getPostCount('logichop-conditions');
    $goal_count= $this->logic->getPostCount('logichop-goals');

	/*$featured = fetch_feed('https://logichop.com/?tag=featured&feed=rss2');
	$featured_count = 0;
    $featured_items = false;
    if (!is_wp_error($featured)) {
    	$featured_count = $featured->get_item_quantity(5);
    	$featured_items = $featured->get_items(0, $featured_count);
    }*/
	$blog = fetch_feed('https://logichop.com/feed/');
	$blog_count = 0;
    $blog_items = false;
    if (!is_wp_error($blog)) {
    	$blog_count = $blog->get_item_quantity(10);
    	$blog_items = $blog->get_items(0, $blog_count);
    }
    $plugin_dir = plugin_dir_url(__FILE__);
?>

<style>
    .welcome-panel-x {
        position: relative;
        overflow: auto;
        margin: 16px 0;
        padding: 23px 10px 0;
        border: 1px solid #c3c4c7;
        box-shadow: 0 1px 1px rgb(0 0 0 / 4%);
        background: #fff;
        font-size: 13px;
        line-height: 1.7;
    }
    .welcome-panel-x-content {
        margin-left: 13px;
        max-width: 1500px;
    }
    .welcome-panel-x .about-description {
        font-size: 16px;
        margin: 0;
    }
    .welcome-panel-x .welcome-panel-x-column-container {
        clear: both;
        position: relative;
    }
    .welcome-panel-x .welcome-panel-x-column:first-child {
        width: 36%;
    }

    .welcome-panel-x .welcome-panel-x-column {
        width: 32%;
        min-width: 200px;
        float: left;
    }
    .welcome-panel-x .welcome-panel-x-column-container {
        clear: both;
        position: relative;
    }
	.logichop-hidden-h2 {
		display: none;
	}

	.logichop-logo {
		float: left;
		margin-right: 20px;
	}

	.welcome-panel-x-column p {
		padding-right: 10px;
	}

	.logichop-metabox-title {
		font-size: 14px;
		padding: 8px 12px;
		margin: 0;
		line-height: 1.4;
		border-bottom: 1px solid #eee;
	}

	#newsletter label {
		display: block;
	}

	.newsletter_inputs {
		padding: 10px 20px 20px;
		text-align: center;
		background: #f8f8f8;
		border: 1px solid #eee;
	}

	#newsletter .mc-field-group {
		margin-bottom: 10px;
	}

	.newsletter_button {
		padding-top: 5px;
	}
	.recipe-title {
		margin-bottom: 0;
	}
	.recipe-list {
		padding-left: 20px;
		margin-top: 0 !important;
	}
	.recipe-list li {
		list-style-type: disc !important;
		padding-bottom: 0 !important;
	}
	@media only screen and (min-width: 1500px) {
		#wpbody-content #dashboard-widgets #postbox-container-1,
		#wpbody-content #dashboard-widgets #postbox-container-2 {
    		width: 50%;
    	}
	}
</style>

<script>
	jQuery(function($) {
		$('#recipe-upload').click(function (e) {
			e.preventDefault();
			$('#recipe-file').trigger('click');
			$('#recipe-msg').html('');
		})
		$('#recipe-file').change(function() {
			var reader = new FileReader();
			reader.onload = function (e) {
				var post_data = {};
				post_data.action = 'logichop_add_recipe';
				post_data.data = '';

				var error_msg = 'There was an error. Please try again.';

				try {
				  post_data.data = JSON.parse(e.target.result);
				} catch (e) {
				  $('#recipe-msg').html(error_msg);
				}

				if (post_data.data) {
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: ajaxurl,
						data: post_data,
						cache: false,
						success: function (data) {
							if (data.result == 'success') {
								$('#recipe-msg').html(data.msg);
							} else {
								$('#recipe-msg').html(error_msg);
							}
						},
						error: function (e) {
							$('#recipe-msg').html(error_msg);
						}
					});
				}
      };
      reader.readAsText(this.files[0]);
		});
	});
</script>

<?php if( !$this->logic->is_onboarding_complete() ) : ?>
<style type="text/css">
	#lh-onboarding {
		max-width: 600px;
		margin: 0 auto;
	}
	#logo-container {
		margin: 0 auto;
		width: 380px;
	}

	#greeting {
		text-align: center;
		margin: 25px 0;
	}

	.step-complete h3 {
		color: green;
	}

	.onboarding-step {
		padding-bottom: 10px;
		border-bottom: 1px solid rgb(195, 196, 199);
	}
	.onboarding-step:last-of-type {
		border-bottom: none;
	}
</style>
<div class="wrap" id="lh-onboarding">
	<h2 class="logichop-hidden-h2"></h2>
	<div id="welcome-panel-x" class="welcome-panel-x">
		<div class="welcome-panel-x-content">
			<div id="logo-container" style="text-align: center; padding: 20px; background-color: #f9f9f9; border-bottom: 2px solid #eee;">
				<img class="logichop-logo" src="<?php echo $this->plugin_url('admin'); ?>images/logic-hop.png" style="max-width: 120px;">
				<h1 style="color: #333; font-size: 24px; margin-top: 10px;">Logic Hop</h1>
				<p class="about-description" style="font-size: 18px; color: #666;">CRM-Powered Personalization for WordPress</p>
			</div>
			<div id="greeting" style="padding: 20px;">
				<h3 style="color: #3498db; font-size: 22px;">Welcome to CRM-Enhanced Personalization!</h3>
				<p style="font-size: 18px; color: #666;">Boost your WordPress site's conversion rates and engagement with Logic Hop. Our tools leverage CRM insights to deliver precisely tailored content, offering a seamless blend of marketing personalization and efficient customer management. Start transforming your digital strategy today.</p>
			</div>
			<?php $first_incomplete_condition = null; foreach( $this->logic->get_onboarding_steps() as $i => $step ) : ?>
			<?php if( $first_incomplete_condition === null && !$step['complete'] ) $first_incomplete_condition = $step['scope']; ?>
			<div class="onboarding-step <?php if( $step['complete'] ) echo 'step-complete'; ?>">
				<h1><?php esc_html_e( $i + 1 ); ?>. <?php esc_html_e( $step['title'] ); ?> <?php if( $step['complete'] ) echo '&nbsp;&#9989;'; ?></h1>
				<?php
					if( $step['scope'] == $first_incomplete_condition ) {
						switch( $step['scope'] ) {
							case 'lh-config':
								?>
								<p>Next, make sure Logic Hop is configured properly for your site by reviewing the options on the settings page.</p>
								<a class="button button-primary"  href="<?php echo $this->plugin_url('settings'); ?>">Go to the settings page</a>
								<?php
								break;
							case 'lh-conditions':
								?>
								<p>Now you're ready to start personalizing! &#127881;</p>
								<p>It's time to create your first <strong>condition</strong>. Conditions are the brains of Logic Hop&mdash;once you've created a condition, you can use it to personalize content anywhere on your site depending on whether the condition is true or false. You can even combine conditions for more powerful personalization.</p>
								<p>Check out our <a class="" href="<?php echo $this->plugin_url('5min'); ?>" target="_blank">Famous Five-Minute Overview</a> to learn the essentials. Then, <a class="" href="<?php echo $this->plugin_url('add-condition'); ?>" target="_blank">create your first condition!</a></p>
								<a class="button button-primary" href="<?php echo $this->plugin_url('add-condition'); ?>">Create your first condition</a>
								<a class="button" target="_blank" href="<?php echo $this->plugin_url('5min'); ?>">Check out our famous five-minute overview</a>
								<?php
								break;
							case 'lh-integrations':
								break;
						}
					}
				?>
			</div>
			<?php endforeach; ?>
			<p>Need help? We're here for you. Just email us at <a href="mailto:info@logichop.com">info@logichop.com</a>.
		</div>
	</div>
</div>

<?php else: ?>

<div class="wrap">

	<h2 class="logichop-hidden-h2"></h2>

	<div id="welcome-panel-x" class="welcome-panel-x">
		<div class="welcome-panel-x-content">

			<img class="logichop-logo" src="<?php echo $this->plugin_url('admin'); ?>images/logic-hop.png">

			<h1>Logic Hop</h1>
			<p class="about-description">Personalized Marketing for WordPress</p>

			<div class="welcome-panel-x-column-container">

				<div class="welcome-panel-x-column">

					<?php if( !$this->logic->is_license_expired() ) : ?>
					<h3>Personalize Your Site</h3>
					<a class="button button-primary button-hero" href="<?php echo $this->plugin_url('add-condition'); ?>">Create a Condition</a>
					<a class="button button-primary button-hero" href="<?php echo $this->plugin_url('add-goal'); ?>">Create a Goal</a>
					<p class="">or, <a href="<?php echo $this->plugin_url('settings'); ?>">update your Logic Hop settings</a>.</p>
					

					<h3>Logic Hop Recipes</h3>
					<p>Recipes are ready-made personalizations which you can easily add & use on your site. <a href="https://logichop.com/recipes/?ref=plugin" target="_blank">Explore Logic Hop Recipes</a></p>
					<a class="button button-primary" id="recipe-upload" href="#">Upload a Recipe</a>
					<input type="file" id="recipe-file" value="Upload a Recipe" style="display: none;">
					<div id="recipe-msg"></div>
					<?php endif; ?>

					<?php if ($condition_count == 0) : ?>
						<h3><span class="dashicons dashicons-star-filled"></span> New to Logic Hop & personalization?</h3>
						<p class="">
							Check out our <a class="" href="<?php echo $this->plugin_url('quickstart'); ?>" target="_blank">5-Minute Quick Start Guide</a>!
						</p>
					<?php endif; ?>
				</div>


				<div class="welcome-panel-x-column">
					<h3>Logic Hop Status</h3>
						<ul>
							<?php if ($this->logic->get_option('debug_mode', false)) : ?>
								<li>
									<span class="dashicons dashicons-flag file-error"></span>
									<strong class="">Debug Mode Enabled</strong>
								</li>
							<?php endif; ?>
							<li>
								<?php
									printf('<a href="%s"><span class="dashicons %s"></span> %s</a>',
										$this->plugin_url('conditions'),
										'dashicons-randomize',
										sprintf(_n('%s Condition', '%s Conditions', $condition_count), $condition_count)
									);
								?>
							</li>
							<li>
								<?php
									printf('<a href="%s"><span class="dashicons %s"></span> %s</a>',
										$this->plugin_url('goals'),
										'dashicons-awards',
										sprintf(_n('%s Goal', '%s Goals', $goal_count), $goal_count)
									);
								?>
							</li>
							<li>
								<span class="dashicons dashicons-admin-settings"></span>
								<?php if ($this->logic->get_option('disable_conditional_css', false)) : ?>
									CSS Conditions Disabled
								<?php else : ?>
									CSS Conditions Enabled
								<?php endif; ?>
							</li>
							<li>
								<span class="dashicons dashicons-admin-settings"></span>
								<?php if ($this->logic->get_option('js_tracking', false)) : ?>
									Javascript Mode Enabled
								<?php else : ?>
									Javascript Mode Disabled
								<?php endif; ?>
							</li>
							<li>
								<a class="button" href="<?php echo $this->plugin_url('settings'); ?>">Logic Hop Settings</a>
							</li>
						</ul>
				</div>


				<div class="welcome-panel-x-column welcome-panel-x-last">
					<h3><span class="dashicons dashicons-welcome-learn-more"></span> Personalization Resources</h3>
						<ul class="">
							<li><a class="" href="<?php echo $this->plugin_url('quickstart'); ?>" target="_blank">Logic Hop Setup Guide</a>
							<li><a class="" href="<?php echo $this->plugin_url('5min'); ?>" target="_blank">Logic Hop's Famous Five Minute Quick Start Guide</a>
							<li><a class="" href="<?php echo $this->plugin_url('docs'); ?>" target="_blank">Logic Hop Documentation</a>
							<?php
								// if ($featured_items && $featured_count > 0) {
								// 	foreach ($featured_items as $item) {
								// 		printf('<li><a href="%s?ref=plugin" target="_blank">%s</a></li>',
								// 			esc_url($item->get_permalink()),
								// 			esc_html($item->get_title())
								// 		);
								// 	}
								// }
							?>
							<li>Extend Logic Hop with <a class="" href="<?php echo $this->plugin_url('integrations'); ?>">Integrations</a>
							<?php if (!$this->logic->data_plan()) : ?>
								<li>Get really personal with a <a class="" href="<?php echo $this->plugin_url('data-plan'); ?>" target="_blank">Logic Hop Data Plan</a>
							<?php endif; ?>
						</ul>
					<?php if (!$this->logic->data_plan()) : ?>
						<h3>Want to learn more about Logic Hop?</h3>
						<p class="">
							Email your questions to <a href="mailto:info@logichop.com" target="_blank">info@logichop.com</a>.
						</p>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>

	<?php if( $condition_count < 5 ) : ?>
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">

			<div id="postbox-container-1" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">

					<div id="logichop-videos" class="postbox">
						<h2 class="logichop-metabox-title">Getting Started With Logic Hop</h2>
						<div class="inside">
							<p>New to Logic Hop, or to content personalization in general? No problem&mdash;we're here to help you get started.</p>
							<p>Check out <strong>our Famous Five Minute Quick Start Guide</strong> to get started.</p>
							<a class="button button-primary button-hero" href="<?php echo $this->plugin_url('5min'); ?>" target="_blank">Read The Guide</a>
						</div>
					</div>

				</div>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<div id="logichop-blog" class="postbox ">
							<h2 class="logichop-metabox-title">Integrations</h2>
							<div class="inside">
								<p>Did you know that Logic Hop works with every major page builder, including Beaver Builder, Divi, and Elementor; marketing platforms like Drip and ConvertKit; and with data platforms like Google Analytics? Just install your integrations and uplevel your personalization.</p>
								<a class="button button-hero" href="<?php echo $this->plugin_url('integrations'); ?>" target="_blank">Add An Integration</a>
							</div>
						</div>
				</div>
			</div>

		</div>
	</div>
	<?php endif; ?>


	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">

			<div id="postbox-container-1" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">

					<div id="logichop-videos" class="postbox">
						<h2 class="logichop-metabox-title">Logic Hop Video Tutorials</h2>
						<div class="inside">
							<div style="position:relative;height:0;padding-bottom:56.25%"><iframe src="https://www.youtube.com/embed/videoseries?list=PLxbGAv2YJgupG8pVa27623D7j3BynDeR-&amp;showinfo=0?ecver=2" width="640" height="360" frameborder="0" style="position:absolute;width:100%;height:100%;left:0;border:1px solid #eee;" allowfullscreen></iframe></div>
						</div>
					</div>

				</div>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">

					<?php if ($blog_items) : ?>
						<div id="logichop-blog" class="postbox ">
							<h2 class="logichop-metabox-title">Logic Hop Tips, Tricks & Tutorials</h2>
							<div class="inside">
								<div class="main">
									<ul class="ul-disc">
										<?php
											if ($blog_count > 0) {
												foreach ($blog_items as $item) {
													printf('<li><a href="%s?ref=plugin" target="_blank">%s</a></li>',
														esc_url($item->get_permalink()),
														esc_html($item->get_title())
													);
												}
											}
										?>
									</ul>
								</div>
							</div>
						</div>
					<?php endif; ?>

				</div>
			</div>

		</div>
	</div>

</div>

<?php endif; ?>