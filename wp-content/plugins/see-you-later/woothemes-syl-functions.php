<?php
/**
 * syl_header function.
 *
 * @description header function for themes to use
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_header () {
	do_action( 'before_syl_header' );
	wp_print_styles();
	wp_print_scripts();
	do_action( 'after_syl_header' );
}

/**
 * syl_footer function.
 *
 * @description footer function for themes to use
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_footer () {
	do_action( 'syl_footer' );
	do_action( 'syl_footer_scripts' );
}

/**
 * syl_title function.
 *
 * @description display see you later page title for <title> tag
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_title ( $echo = true ) {
	global $woothemes_syl;

	$title = (string) apply_filters( 'woothemes_syl_page_title', $woothemes_syl->settings->settings['page_title'] );
	$title = empty( $title ) ? get_bloginfo( 'name' ) : $title;

	if( $echo ) echo esc_html( $title );
	else return esc_html( $title );
}

/**
 * syl_text_heading function.
 *
 * @description display text heading to user
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_text_heading ( $echo = true ) {
	global $woothemes_syl;

	$title = (string) apply_filters( 'woothemes_syl_the_title', $woothemes_syl->settings->settings['title'] );
	$title = empty( $title ) ? get_bloginfo( 'name' ) : $title;

	if( $echo ) echo esc_html( $title );
	else return esc_html( $title );
}

/**
 * syl_message function.
 *
 * @description display see you later message to user
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_message ( $echo = true ) {
	global $woothemes_syl;
	$message = (string) apply_filters( 'woothemes_syl_message', $woothemes_syl->settings->settings['message'] );
	if( $echo ) echo do_shortcode( html_entity_decode( $message ) );
	else return do_shortcode( html_entity_decode( $message ) );
}

/**
 * syl_get_template_url function.
 *
 * @description easily get the currently selected templates URL
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_get_template_url ( $uri = '', $echo = true ) {
	global $woothemes_syl;

	$theme = $woothemes_syl->settings->settings['theme'];

	if ( ! isset( $woothemes_syl->themes[ $theme ]->theme_root ) )
		return null;

	$theme_dir = trailingslashit( $woothemes_syl->themes[ $theme ]->theme_root ) . $theme;

	if ( ! is_dir( $theme_dir ) )
		return null;

	$url = trailingslashit( str_replace( WP_CONTENT_DIR, content_url(), $theme_dir  ) ) . $uri;
	if ( $echo ) echo $url;
	else return $url;
}

/**
 * syl_logo function.
 *
 * @description load site logo or title/description if logo is not set
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_logo ( $show_title_instead = true ) {
	global $woothemes_syl;
	$logo = $woothemes_syl->settings->settings['logo_image'];
	if ( ! empty( $logo ) ) {
	?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
			<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
		</a>
	<?php
	}
}// End syl_logo()

/**
 * syl_custom_footer function.
 *
 * @description display custom footer text for launch pad templates
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_custom_footer () {
	global $woothemes_syl;
	$custom_footer = $woothemes_syl->settings->settings['custom_footer'];
	if ( ! empty( $custom_footer ) ) {
		echo wpautop( stripslashes( esc_html( $custom_footer ) ) );
	}
}// End syl_custom_footer()

/**
 * syl_custom_css function.
 *
 * @description display custom footer text for launch pad templates
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_custom_css () {
	global $woothemes_syl;

	$css = '';

	$body_img = $woothemes_syl->settings->settings['background_image'];
	$body_repeat = $woothemes_syl->settings->settings['background_image_repeat'];
	$body_position = $woothemes_syl->settings->settings['background_image_position'];

	if ( ! empty( $body_img ) ) {
		$css .= 'body { background: url( ' . $body_img . ' ) ' . $body_repeat . ' ' . $body_position . '; }' . "\n";
	}

	$custom_css = html_entity_decode( strip_tags( $woothemes_syl->settings->settings['custom_css'] ) );
	if ( ! empty( $custom_css ) )
		$css .= $custom_css;

	if ( ! empty( $css ) ) {
	?>
		<style type='text/css'>
		<?php echo $css; ?>
		</style>
	<?php
	}
}// End syl_custom_css()


/**
 * Display the progress bar if enabled
 * @since  1.1.0
 * @return void
 */
function syl_progressbar () {
	global $woothemes_syl;

	$launch = strtotime( $woothemes_syl->settings->settings['progressbar_end'] );
	if ( ! $woothemes_syl->settings->settings['enable_progressbar'] || ( $launch < current_time( 'timestamp' ) && empty( $woothemes_syl->settings->settings['progressbar_percentage'] ) ) )
		return;

	if ( ! empty( $woothemes_syl->settings->settings['progressbar_percentage'] ) ) {
		$percentage = str_replace( '%', '', $woothemes_syl->settings->settings['progressbar_percentage'] );
	} else {
		$diff = $launch - strtotime( $woothemes_syl->settings->settings['progressbar_start'] );
		$current_diff = $launch - current_time( 'timestamp' );
		$percentage = ( $current_diff / $diff ) * 100;
	}

	$class = 'meter';
	if ( ! isset( $woothemes_syl->settings->settings['progressbar_stripes'] ) || ! $woothemes_syl->settings->settings['progressbar_stripes'] )
		$class .= ' nostripes';

	echo '<div id="progressbar" class="block">';
	if ( ! empty( $woothemes_syl->settings->settings['progressbar_heading'] ) ) {
	?>
		<h2><span><?php echo stripslashes( $woothemes_syl->settings->settings['progressbar_heading'] ); ?></span></h2>
	<?php
	}
	if ( $percentage > 100 )
		$percentage = 100;

	$class = apply_filters( 'syl_progressbar_class', $class );
	?>
	<div class="<?php echo $class; ?>">
		<span style="width: <?php echo $percentage; ?>%"></span>
	</div>
	</div>
	<?php
} // End syl_progressbar()

/**
 * syl_countdown_enqueue function.
 *
 * Output css to power progressbar
 * @access public
 * @since 1.1.0
 * @return void
 */
function syl_progressbar_enqueue () {
	global $woothemes_syl;
	$launch = strtotime( $woothemes_syl->settings->settings['progressbar_end'] );
	if ( ! $woothemes_syl->settings->settings['enable_progressbar'] || ( $launch < current_time( 'timestamp' ) && empty( $woothemes_syl->settings->settings['progressbar_percentage'] ) ) )
		return;
	wp_enqueue_style( 'progressbar', $woothemes_syl->assets_url . 'css/progressbar.css' );
	wp_enqueue_script( 'progressbar', $woothemes_syl->assets_url . 'js/progressbar.js' , array( 'jquery') );
} // End syl_progressbar_enqueue()

/**
 * countdown_markup function.
 *
 * @description display countdown markup
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_countdown () {
	global $woothemes_syl;
	$launch = strtotime( $woothemes_syl->settings->settings['countdown_launch'] );
	$heading = $woothemes_syl->settings->settings['countdown_heading'];
	if ( ! $woothemes_syl->settings->settings['enable_countdown'] || $launch < current_time( 'timestamp' ) )
		return;
	?>
	<div id="countdown" class="block">
		<h2><span><?php echo stripslashes( $heading ); ?></span></h2>
		<div id="timer"></div>
	</div>
	<?php
} // End countdown_markup()

/**
 * syl_countdown_js function.
 *
 * @description output javascript to power countdown timer
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_countdown_enqueue () {
	global $woothemes_syl;
	$launch = strtotime( $woothemes_syl->settings->settings['countdown_launch'] );
	if ( ! $woothemes_syl->settings->settings['enable_countdown'] || $launch < current_time( 'timestamp' ) )
		return;

	wp_enqueue_script( 'countdown', $woothemes_syl->assets_url . 'js/jquery.countdown.min.js', array( 'jquery' ) );
} // End syl_countdown_js()

/**
 * syl_countdown_js function.
 *
 * @description output javascript to power countdown timer
 * @access public
 * @since 1.0.0
 * @return void
 */
function syl_countdown_js () {
	global $woothemes_syl;
	$launch = strtotime( $woothemes_syl->settings->settings['countdown_launch'] );
	if ( ! $woothemes_syl->settings->settings['enable_countdown'] || $launch < current_time( 'timestamp' ) )
		return;

	$launch = strtotime( $woothemes_syl->settings->settings['countdown_launch'] );
	$gmt_offset = get_option( 'gmt_offset' );
	if ( $gmt_offset > 0 )
		$timezone = "+" . $gmt_offset;
	else $timezone = $gmt_offset;
	?>
	<script type="text/javascript">
	jQuery(function () {
		jQuery( '#timer' ).countdown(
			{
				until: new Date(<?php echo $launch * 1000 ?>), format: 'DHMS', timezone: <?php echo $timezone; ?>
			});
	});
	</script>
	<?php
} // End syl_countdown_js()

/**
 * syl_supersized_enqueue function.
 *
 * @description output javascript to power background slideshow
 * @access public
 * @since 1.1.0
 * @return void
 */
function syl_supersized_enqueue () {
	global $woothemes_syl;
	if ( ! $woothemes_syl->settings->settings['background_slideshow'] )
		return;

	wp_enqueue_style( 'supersized-css', $woothemes_syl->assets_url . 'lib/supersized/css/supersized.css' );
	wp_enqueue_script( 'supersized', $woothemes_syl->assets_url . 'lib/supersized/js/supersized.3.2.7.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'jquery-easing', $woothemes_syl->assets_url . 'lib/supersized/js/jquery.easing.min.js', array( 'jquery' ) );
}

/**
 * syl_supersized_js function.
 *
 * @description output javascript to power background slideshow
 * @access public
 * @since 1.1.0
 * @return void
 */
function syl_supersized_js () {
	global $woothemes_syl;
	if ( ! $woothemes_syl->settings->settings['background_slideshow'] )
		return;
	$images = $woothemes_syl->settings->settings['background_slideshow_images'];
	$images_array = explode( "\n", $images );
	$image_string = '';
	foreach( $images_array as $image ) {
		$image_string .= "{image:'" . trim( $image ) . "'},";
	}
	$image_string = '[' . rtrim( $image_string, ',' ) . ']';
	$random_slides = ( $woothemes_syl->settings->settings['background_slideshow_randomize'] ) ? 1 : 0;
	?>
	<script type="text/javascript">
	jQuery( function($) {
		$.supersized({
			slide_interval: 3000,
			transition: '<?php echo $woothemes_syl->settings->settings['background_slideshow_transition']; ?>',
			transition_speed: 700,
			random: <?php echo $random_slides; ?>,
			fit_landscape: 0,
			slides: <?php echo $image_string; ?>
		});
	});
	</script>
	<?php
}

function syl_newsletter () {
	global $woothemes_syl;
	if ( ! $woothemes_syl->settings->settings['enable_newsletter'] )
		return;

	$service = $woothemes_syl->settings->settings['newsletter_service'];
	?>
	<div id="newsletter">
		<p><?php echo esc_html( $woothemes_syl->settings->settings['newsletter_heading'] ); ?></p>

		<?php
		switch( $woothemes_syl->settings->settings['newsletter_service'] ) {
			case 'feedburner':
				?>
				<form class="newsletter-form" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $woothemes_syl->settings->settings['newsletter_service_id']; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
					<?php do_action( 'woothemes_syl_feedburner_fields' ); ?>
					<input class="email" type="email" name="email" value="<?php _e('E-mail', 'woothemes-syl' ); ?>" onfocus="if (this.value == '<?php _e('E-mail', 'woothemes-syl' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail', 'woothemes-syl' ); ?>';}" />
					<input type="hidden" value="<?php echo $woothemes_syl->settings->settings['newsletter_service_id']; ?>" name="uri"/>
					<input type="hidden" value="<?php bloginfo('name'); ?>" name="title"/>
					<input type="hidden" name="loc" value="en_US"/>
					<input type="submit" name="submit" value="<?php echo stripslashes( $woothemes_syl->settings->settings['newsletter_subscribe_button'] ); ?>" class="btn submit button" />
				</form>
				<?php
				break;
			case 'campaignmonitor':
				$cm_array = explode( '/', $woothemes_syl->settings->settings['newsletter_service_form_action'] );
				array_pop( $cm_array );
				$cm_id = end( $cm_array );
				?>
				<form name="campaignmonitorform" class="newsletter-form" action="<?php echo $woothemes_syl->settings->settings['newsletter_service_form_action']; ?>" method="post">
					<?php do_action( 'woothemes_syl_campaignmonitor_fields' ); ?>
					<input type="text" class="name" value="<?php _e('Name', 'woothemes-syl' ); ?>" name="cm-name" onfocus="if (this.value == '<?php _e('Name', 'woothemes-syl' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Name', 'woothemes-syl' ); ?>';}" />
					<input type="email" class="email" name="cm-<?php echo $cm_id . '-' . $cm_id; ?>"  value="<?php _e('E-mail', 'woothemes-syl' ); ?>" onfocus="if (this.value == '<?php _e('E-mail', 'woothemes-syl' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail', 'woothemes-syl' ); ?>';}" />
					<input type="submit" name="submit" value="<?php echo stripslashes( $woothemes_syl->settings->settings['newsletter_subscribe_button'] ); ?>" class="btn submit button" />
				</form>
				<?php
				break;
			case 'mailchimp':
				?>
				<form class="newsletter-form" action="<?php echo $woothemes_syl->settings->settings['newsletter_mail_chimp_list_subscription_url']; ?>" method="post" target="popupwindow" onsubmit="window.open('<?php echo $woothemes_syl->settings->settings['newsletter_mail_chimp_list_subscription_url']; ?>', 'popupwindow', 'scrollbars=yes,width=650,height=520');return true">
					<?php do_action( 'woothemes_syl_mailchimp_fields' ); ?>
					<input type="email" name="EMAIL" class="required email" value="<?php _e('E-mail', 'woothemes-syl' ); ?>"  id="mce-EMAIL" onfocus="if (this.value == '<?php _e('E-mail', 'woothemes-syl' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail', 'woothemes-syl' ); ?>';}" />
					<input type="submit" value="<?php echo stripslashes( $woothemes_syl->settings->settings['newsletter_subscribe_button'] ); ?>" name="subscribe" id="mc-embedded-subscribe" class="btn submit button" />
				</form>
				<?php
				break;
			case 'aweber':
				?>
				<form method="post" class="newsletter-form" action="http://www.aweber.com/scripts/addlead.pl"  >
				    <div style="display: none;">
				        <input type="hidden" name="meta_web_form_id" value="1687488389" />
				        <input type="hidden" name="meta_split_id" value="" />
				        <input type="hidden" name="listname" value="<?php echo $woothemes_syl->settings->settings['newsletter_aweber_list_id']; ?>" />
				        <input type="hidden" name="redirect" value="<?php apply_filters( 'woothemes_syl_aweber_redirect', 'http://www.aweber.com/thankyou-coi.htm?m=text' ); ?>" />
				        <input type="hidden" name="meta_adtracking" value="" />
				        <input type="hidden" name="meta_message" value="1" />
				        <input type="hidden" name="meta_required" value="name,email" />
				        <input type="hidden" name="meta_tooltip" value="" />
				    </div>
	        		<!-- Start Visible Input Fields -->
	        		<?php do_action( 'woothemes_syl_aweber_fields' ); ?>
					<input type="text" class="name" value="<?php _e('Name', 'woothemes-syl' ); ?>" name="name" onfocus="if (this.value == '<?php _e('Name', 'woothemes-syl' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Name', 'woothemes-syl' ); ?>';}" />
					<input type="email" class="email" value="<?php _e('E-mail', 'woothemes-syl' ); ?>" name="email" onfocus="if (this.value == '<?php _e('E-mail', 'woothemes-syl' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail', 'woothemes-syl' ); ?>';}" />
					<input type="submit" name="submit" value="<?php echo stripslashes( $woothemes_syl->settings->settings['newsletter_subscribe_button'] ); ?>" class="btn submit button" />

	        		<!-- End Visible Input Fields -->
				</form>
				<?php
				break;
			case 'madmimi':
				?>
				<div id="mc_embed_signup">
					<form class="newsletter-form" action="<?php echo $woothemes_syl->settings->settings['newsletter_mad_mimi_subscription_url']; ?>" method="post">
						<?php do_action( 'woothemes_syl_madmimi_fields' ); ?>
						<input type="email" name="signup[email]" class="email" value="<?php _e('E-mail', 'woothemes-syl' ); ?>" onfocus="if (this.value == '<?php _e('E-mail', 'woothemes-syl' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail', 'woothemes-syl' ); ?>';}" />
						<input type="submit" value="<?php echo stripslashes( $woothemes_syl->settings->settings['newsletter_subscribe_button'] ); ?>" name="commit" class="btn submit button" />
					</form>
				</div>
				<?php
				break;
			case 'wysija':
				do_action( 'woothemes_syl_wysija' );
				?>
				<form class="newsletter-form" method="post">
					<?php do_action( 'woothemes_syl_wysija_fields' ); ?>
					<input type="text" name="syl_wysija_name" class="name" value="<?php _e('Name', 'woothemes-syl' ); ?>" onfocus="if (this.value == '<?php _e('Name', 'woothemes-syl' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Name', 'woothemes-syl' ); ?>';}" />
					<input type="email" name="syl_wysija_email" class="email" value="<?php _e('E-mail', 'woothemes-syl' ); ?>" onfocus="if (this.value == '<?php _e('E-mail', 'woothemes-syl' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail', 'woothemes-syl' ); ?>';}" />
					<input type="submit" value="<?php echo stripslashes( $woothemes_syl->settings->settings['newsletter_subscribe_button'] ); ?>" name="wysija_submit" class="btn submit button" />
					</form>
				<?php
				break;
		}
		?>
		<div class="fix"></div>
	</div><!-- /#newsletter -->
	<?php
} // End syl_newsletter()

function syl_social_links () {
	global $woothemes_syl;
	if ( ! $woothemes_syl->settings->settings['enable_social'] )
		return;
	?>
	<div id="social" class="block">
		<h2><?php echo esc_html( $woothemes_syl->settings->settings['social_heading'] ); ?></h2>
		<ul class="social-links">
			<?php if ( '' != $woothemes_syl->settings->settings['twitter_account'] ) { ?>
			<li>
				<a href="https://twitter.com/<?php echo $woothemes_syl->settings->settings['twitter_account']; ?>" class="twitter" target="_blank"><span><?php _e( 'Twitter', 'woothemes-syl' ); ?></span></a>
			</li>
			<?php  } ?>
			<?php if (  '' != $woothemes_syl->settings->settings['facebook_account'] ) { ?>
			<li>
				<a href="http://www.facebook.com/<?php echo $woothemes_syl->settings->settings['facebook_account']; ?>" class="facebook" target="_blank"><span><?php _e( 'Facebook', 'woothemes-syl' ); ?></span></a>
			</li>
			<?php } ?>
			<?php if (  '' != $woothemes_syl->settings->settings['google_account'] ) { ?>
			<li>
				<a href="https://plus.google.com/<?php echo $woothemes_syl->settings->settings['google_account']; ?>" class="googleplus" target="_blank"><span><?php _e( 'Google+', 'woothemes-syl' ); ?></span></a>
			</li>
			<?php } ?>
			<?php if (  '' != $woothemes_syl->settings->settings['pinterest_account'] ) { ?>
			<li>
				<a href="http://pinterest.com/<?php echo $woothemes_syl->settings->settings['pinterest_account']; ?>/" class="pinterest" target="_blank"><span><?php _e( 'Pinterest', 'woothemes-syl' ); ?></span></a>
			</li>
			<?php } ?>
			<?php if (  '' != $woothemes_syl->settings->settings['youtube_account'] ) { ?>
			<li>
				<a href="http://www.youtube.com/user/<?php echo $woothemes_syl->settings->settings['youtube_account']; ?>" class="youtube" target="_blank"><span><?php _e( 'YouTube', 'woothemes-syl' ); ?></span></a>
			</li>
			<?php } ?>
			<?php if (  '' != $woothemes_syl->settings->settings['flickr_account'] ) { ?>
			<li>
				<a href="http://www.flickr.com/photos/<?php echo $woothemes_syl->settings->settings['flickr_account']; ?>/" class="flickr" target="_blank"><span><?php _e( 'Flickr', 'woothemes-syl' ); ?></span></a>
			</li>
			<?php } ?>
			<?php if (  '' != $woothemes_syl->settings->settings['linkedin_account'] ) { ?>
				<?php if ( strpos( $woothemes_syl->settings->settings['linkedin_account'], 'company/' ) === false ) { ?>
					<li>
						<a href="http://linkedin.com/in/<?php echo $woothemes_syl->settings->settings['linkedin_account']; ?>" class="linkedin" target="_blank"><span><?php _e( 'LinkedIn', 'woothemes-syl' ); ?></span></a>
					</li>
				<?php } else { ?>
					<li>
						<a href="http://linkedin.com/<?php echo $woothemes_syl->settings->settings['linkedin_account']; ?>" class="linkedin" target="_blank"><span><?php _e( 'LinkedIn', 'woothemes-syl' ); ?></span></a>
					</li>
				<?php } ?>
			<?php } ?>
			<?php if ( '' !=  $woothemes_syl->settings->settings['email'] && is_email( $woothemes_syl->settings->settings['email'] ) ) { ?>
			<li>
				<a href="mailto:<?php echo $woothemes_syl->settings->settings['email']; ?>" class="contact" target="_blank"><span><?php _e( 'Contact', 'woothemes-syl' ); ?></span></a>
			</li>
			<?php } ?>
			<?php if ( 1 == $woothemes_syl->settings->settings['show_rss'] ) { ?>
			<li>
				<a href="<?php if ( $woothemes_syl->settings->settings['rss_feed_url'] ) { echo $woothemes_syl->settings->settings['rss_feed_url']; } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe" target="_blank"><span><?php _e( 'Subscribe', 'woothemes-syl' ); ?></span></a>
			</li>
			<?php } ?>
		</ul>
		</div><!-- #social -->

	<?php
} // End syl_social_links()

function syl_custom_code_header () {
	global $woothemes_syl;
	$output = $woothemes_syl->settings->settings['custom-html-code-head'];
	if ( ! empty( $output ) )
		echo stripslashes( $output ). "\n";
}

function syl_custom_code_footer () {
	global $woothemes_syl;
	$output = $woothemes_syl->settings->settings['custom-html-code-footer'];
	if ( ! empty( $output ) )
		echo stripslashes( $output ) . "\n";
}

function syl_footer_woothemes_link () {
	global $woothemes_syl;
	if ( isset( $woothemes_syl->settings->settings['show_woothemes_credit'] ) && $woothemes_syl->settings->settings['show_woothemes_credit'] === true )
		return;
?>
	<div id="credit">
		<p><?php _e( 'Made with See You Later by', 'woothemes-syl' ); ?> <a href="http://wthms.co/17kB74k"><img src="<?php echo esc_url( $woothemes_syl->assets_url . 'images/woothemes.png' ); ?>" width="74" height="19" alt="WooThemes" /></a></p>
	</div>
<?php
}

function syl_google_analytics_code () {
	global $woothemes_syl;
	$output = $woothemes_syl->settings->settings['google_analytics_id'];
	if ( ! empty( $output ) )
		echo "
			<script type=\"text/javascript\">

				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', '" . esc_js( $output ) . "']);
			 	_gaq.push(['_trackPageview']);

			 	(function() {
					var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  	})();

			</script>";
}

function syl_process_wysija_subscription () {
	global $woothemes_syl;

	if ( isset( $_POST['wysija_submit'] ) ) {

		if ( is_email( $_POST['syl_wysija_email'] ) )
			$email = sanitize_email( $_POST['syl_wysija_email'] );

		if ( isset( $_POST['syl_wysija_name'] ) )
			$name = sanitize_text_field( $_POST['syl_wysija_name'] );

	    $data=array(
			'user' => array( 'email' => $email, 'firstname' => $name ),
			'user_list'=>array( 'list_ids'=>array( $woothemes_syl->settings->settings['newsletter_wysija_list_id'] ) )
	    );

	    $userHelper = &WYSIJA::get('user','helper');
	    $userHelper->addSubscriber( $data );
		echo '<div id="syl_wysija_message">' . apply_filters( 'woothemes_syl_wysija_subscribed_message', __( 'Thanks for subscribing to our newsletter.', 'woothemes_syl' ) ) . '</div>';
	}
}
?>