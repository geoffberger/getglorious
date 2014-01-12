<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * See You Layet Settings Class
 *
 * All functionality pertaining to the settings in See You Later.
 *
 * @package WordPress
 * @subpackage See You Later
 * @category Core
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - init_sections()
 * - init_fields()
 * - get_duration_options()
 * - add_contextual_help()
 * - pages_array()
 */
class WooThemes_SYL_Settings extends WooThemes_SYL_Settings_API {

	/**
	 * Constructor.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct () {
		parent::__construct(); // Required in extended classes.
		add_action( 'woothemes_syl_admin_inside_title', array( $this, 'live_preview_button' ) );
	} // End __construct()

	/**
	 * Add settings sections.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function init_sections () {
		global $woothemes_syl;
		$sections = array();

		$sections['general'] = array(
			'name' 			=> __( 'General Settings', 'woothemes-syl' ),
			'description'	=> __( 'Activate See You Later, set the page title, message and access rights.', 'woothemes-syl'  )
		);

		$sections['lookfeel'] = array(
			'name' 			=> __( 'Look & Feel', 'woothemes-syl' ),
			'description'	=> __( 'Customize the look and feel of your See You Later page.', 'woothemes-syl' )
		);

		$sections['progressbar'] = array(
			'name'			=> __( 'Progress Bar', 'woothemes-syl' ),
			'description'	=> __( 'Activate and setup a progress bar to indicate how close to launching you are.', 'woothemes-syl' )
		);

		$sections['countdown'] = array(
			'name'			=> __( 'Countdown Clock', 'woothemes-syl' ),
			'description'	=> __( 'Activate and setup a countdown clock for when your site will be launching.', 'woothemes-syl' )
		);

		$sections['newsletter'] = array(
			'name' 			=> __( 'Newsletter', 'woothemes-syl' ),
			'description'	=> __( 'Activate and setup a newsletter subscription form.', 'woothemes-syl' )
		);

		$sections['social'] = array(
			'name'			=> __( 'Social Links', 'woothemes-syl' ),
			'description'	=> __( 'The links to social profiles. You must include the full url.', 'woothemes-syl' )
		);

		$sections['custom-html'] = array(
			'name' 			=> __( 'Custom Code', 'woothemes-syl' ),
			'description'	=> __( 'Add custom code to your See You Later page. This is ideal for tracking code.', 'woothemes-syl' )
		);

		$sections['services'] = array(
			'name' 			=> __( 'Toggle WP Services', 'woothemes-syl' ),
			'description'	=> __( 'Enable/Disable certain WordPress features when See You Later is enabled.', 'woothemes-syl' )
		);

		$sections['access'] = array(
			'name' 			=> __( 'Special Access', 'woothemes-syl' ),
			'description'	=> __( 'Allow bypassing the See You Later page by giving access to your site with these special rules.', 'woothemes-syl' )
		);

		$this->sections = apply_filters( 'woothemes_syl_settings_tabs', $sections );
	} // End init_sections()

	/**
	 * Add settings fields.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function init_fields () {
		global $pagenow, $woothemes_syl;

		$background_image_repeat_options = array( 'no-repeat' => __( 'No Repeat', 'woothemes-syl' ), 'repeat-x' => __( 'Repeat Horizontally Only', 'woothemes-syl' ), 'repeat-y' => __( 'Repeat Vertically Only', 'woothemes-syl' ), 'repeat' => __( 'Repeat', 'woothemes-syl' ) );


		$background_image_position_options = array(
			'left top' => __( 'Top Left', 'woothemes-syl' ),
			'right top' => __( 'Top Right', 'woothemes-syl' ),
			'center top' => __( 'Top Center', 'woothemes-syl' ),
			'left center' => __( 'Center Left', 'woothemes-syl' ),
			'center center' => __( 'Center Center', 'woothemes-syl' ),
			'right center' => __( 'Center Right', 'woothemes-syl' ),
			'left bottom' => __( 'Bottom Left', 'woothemes-syl' ),
			'center bottom' => __( 'Bottom Center', 'woothemes-syl' ),
			'right bottom' => __( 'Bottom Right', 'woothemes-syl' )
		);

		$newsletter_service_options = array(
			'aweber' => 'Aweber',
			'campaignmonitor' => 'Campaign Monitor',
			'feedburner' => 'FeedBurner',
			'madmimi' => 'Mad Mimi',
			'mailchimp' => 'MailChimp'
		);

		// Check if Wysija is installed and add as option
		$wysija_lists = array();
		if ( class_exists( 'WYSIJA' ) ) {
			$newsletter_service_options['wysija'] = 'Wysija Newsletters';
			$model_list = WYSIJA::get( 'list','model' );
			$wysija_lists_temp = $model_list->get( array( 'name','list_id' ), array( 'is_enabled' => 1 ) );
			foreach( $wysija_lists_temp as $list ) {
				$wysija_lists[$list['list_id']] = $list['name'];
			}
		}

		$themes = array();

		foreach( $woothemes_syl->themes as $theme => $data ) {
			$themes[ $theme ] = $data['Name'];
		}

		$fields = array();

		$fields['enable'] = array(
			'name' => __( 'Enable', 'woothemes-syl' ),
			'description' => __( 'Turn on See You Later.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'general'
		);

		$fields['page_title'] = array(
			'name' => __( 'Page Title', 'woothemes-syl' ),
			'description' => __( 'This is the page title for the See You Later page. Leave blank to use your website\'s title.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'general'
		);

		$fields['title'] = array(
			'name' => __( 'Introduction Title', 'woothemes-syl' ),
			'description' => __( 'This is the HTML title of the See You Later page. Leave blank to use your website\'s title.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'general'
		);

		$fields['message'] = array(
			'name' => __( 'Introduction Message', 'woothemes-syl' ),
			'description' => __( 'A brief message that will be included in the See You Later page.', 'woothemes-syl' ),
			'type' => 'textarea',
			'default' => '',
			'section' => 'general'
		);

		$fields['custom_footer'] = array(
			'name' => __( 'Footer Text', 'woothemes-syl' ),
			'description' => __( 'Display a line of text in the footer area of the See You Later page.', 'woothemes-syl' ),
			'type' => 'textarea',
			'default' => '',
			'section' => 'general'
		);

		$fields['role'] = array(
			'name' => __( 'Minimum User Rights', 'woothemes-syl' ),
			'description' => __( 'Determine which level of users can see the website when See You Later is enabled.', 'woothemes-syl' ),
			'type' => 'select',
			'default' => 'manage_options',
			'options'	=> array(
				'manage_options'	=> __( 'Administrator', 'woothemes-syl' ),
				'publish_pages'		=> __( 'Editor', 'woothemes-syl' ),
				'publish_posts'		=> __( 'Author', 'woothemes-syl' ),
				'edit_posts'		=> __( 'Contributor', 'woothemes-syl' ),
				'read'				=> __( 'Subscriber', 'woothemes-syl' )
			),
			'section' => 'general'
		);

		$fields['theme'] = array(
			'name' => __( 'Theme', 'woothemes-syl' ),
			'description' => __( 'Choose the design to be used for your See You Later page.', 'woothemes-syl' ),
			'type' => 'select',
			'default' => 'vanilla',
			'options'	=> $themes,
			'section' => 'lookfeel'
		);

		$fields['logo_image'] = array(
			'name' => __( 'Your logo', 'woothemes-syl' ),
			'description' => sprintf( __('Add your logo to the See You Later page. Ensure you enter the full URL (eg: %s).', 'woothemes-syl' ), site_url( '/images/logo.png' ) ),
			'type' => 'upload',
			'default' => '',
			'section' => 'lookfeel'
		);

		$fields['background_image'] = array(
			'name' => __( 'Background Image', 'woothemes-syl' ),
			'description' => sprintf( __('Add a custom background to the See You Later page. Ensure you enter the full URL (eg: %s).', 'woothemes-syl' ), site_url( '/images/background.png' ) ),
			'type' => 'upload',
			'default' => '',
			'section' => 'lookfeel'
		);

		$fields['background_image_repeat'] = array(
			'name' => __( 'Background Image Repeat', 'woothemes-syl' ),
			'description' => __( 'Select how you would like to repeat the background-image.', 'woothemes-syl' ),
			'type' => 'select',
			'default' => '',
			'options' => $background_image_repeat_options ,
			'section' => 'lookfeel'
		);

		$fields['background_image_position'] = array(
			'name' => __( 'Background Image Position', 'woothemes-syl' ),
			'description' => __( 'Select how you would like to position the background.', 'woothemes-syl' ),
			'type' => 'select',
			'default' => '',
			'options' => $background_image_position_options ,
			'section' => 'lookfeel'
		);

		$fields['background_slideshow'] = array(
			'name' => __( 'Background Slideshow', 'woothemes-syl' ),
			'description' => __( 'Override the background image above and display a slideshow of images.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'lookfeel'
		);

		$fields['background_slideshow_images'] = array(
			'name' => __( 'Slideshow Images', 'woothemes-syl' ),
			'description' => __( 'Images to use for the slideshow, each image on a new line.', 'woothemes-syl' ),
			'type' => 'multiupload',
			'default' => '',
			'section' => 'lookfeel'
		);

		$fields['background_slideshow_transition'] = array(
			'name' => __( 'Slideshow Transition', 'woothemes-syl' ),
			'description' => __( 'The effect used to transition between slides.', 'woothemes-syl' ),
			'type' => 'select',
			'default' => 'fade',
			'options' => array(
				'none' => __( 'No transition effect', 'woothemes-syl' ),
				'fade' => __( 'Fade effect', 'woothemes-syl' ),
				'slideTop' => __( 'Slide in from top', 'woothemes-syl' ),
				'slideRight' => __( 'Slide in from right', 'woothemes-syl' ),
				'slideBottom' => __( 'Slide in from bottom', 'woothemes-syl' ),
				'slideLeft' => __( 'Slide in from left', 'woothemes-syl' ),
				'carouselRight' => __( 'Carousel from right to left', 'woothemes-syl' ),
				'carouselLeft' => __( 'Carousel from left to right', 'woothemes-syl' )
			),
			'section' => 'lookfeel'
		);

		$fields['background_slideshow_randomize'] = array(
			'name' => __( 'Randomize Slideshow', 'woothemes-syl' ),
			'description' => __( 'Display images randomly instead of in order as defined.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'lookfeel'
		);

		$fields['show_woothemes_credit'] = array(
			'name' => __( 'Hide WooThemes Credit Link', 'woothemes-syl' ),
			'description' => __( 'Enable to hide the Powered by WooThemes logo in the footer.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'lookfeel'
		);

		$fields['enable_feeds'] = array(
			'name' => __( 'Allow Feeds', 'woothemes-syl' ),
			'description' => __( 'Allow feeds when See You Later is enabled.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => true,
			'section' => 'services'
		);

		$fields['enable_trackbacks'] = array(
			'name' => __( 'Allow Trackbacks', 'woothemes-syl' ),
			'description' => __( 'Allow trackbacks when See You Later is enabled.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => true,
			'section' => 'services'
		);

		$fields['enable_xmlrpc'] = array(
			'name' => __( 'Allow XML-RPC', 'woothemes-syl' ),
			'description' => __( 'Allow XML-RPC when See You Later is enabled.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => true,
			'section' => 'services'
		);

		$fields['enable_progressbar'] = array(
			'name' => __( 'Enable Progress Bar', 'woothemes-syl' ),
			'description' => __( 'Enabled the Progress Bar on your See You Later page.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'progressbar'
		);

		$fields['progressbar_heading'] = array(
			'name' => __( 'Progress Bar Heading', 'woothemes-syl' ),
			'description' => __( 'Enter the progress bar heading.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => false,
			'section' => 'progressbar'
		);

		$fields['progressbar_start'] = array(
			'name' => __( 'Start Date', 'woothemes-syl' ),
			'description' => __( 'Enter the start date and time to use for calculating the progress.', 'woothemes-syl' ),
			'type' => 'datetime_backward',
			'default' => false,
			'section' => 'progressbar'
		);

		$fields['progressbar_end'] = array(
			'name' => __( 'End Date', 'woothemes-syl' ),
			'description' => __( 'Enter the end date and time to use for calculating the progress.', 'woothemes-syl' ),
			'type' => 'datetime',
			'default' => false,
			'section' => 'progressbar'
		);

		$fields['progressbar_percentage'] = array(
			'name' => __( 'Percentage Override', 'woothemes-syl' ),
			'description' => __( 'Enter the percentage you want the bar to display at, this will override the start and end date calculation.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'progressbar'
		);

		$fields['progressbar_stripes'] = array(
			'name' => __( 'Enable Animated Stripes', 'woothemes-syl' ),
			'description' => __( 'Enabled animated stripes on the progress bar.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'progressbar'
		);

		$fields['enable_countdown'] = array(
			'name' => __( 'Enable Countdown Clock', 'woothemes-syl' ),
			'description' => __( 'Enabled the Countdown Clock on your See You Later page.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'countdown'
		);

		$fields['countdown_heading'] = array(
			'name' => __( 'Countdown Clock Heading', 'woothemes-syl' ),
			'description' => __( 'Enter the countdown heading.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => false,
			'section' => 'countdown'
		);

		$fields['countdown_launch'] = array(
			'name' => __( 'Site Launch Date', 'woothemes-syl' ),
			'description' => __( 'Enter the date and time you\'ll be launching.', 'woothemes-syl' ),
			'type' => 'datetime',
			'default' => false,
			'section' => 'countdown'
		);

		$fields['enable_newsletter'] = array(
			'name' => __( 'Enable Newsletter Area', 'woothemes-syl' ),
			'description' => __( 'Enable the newsletter area on the See You Later page.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'newsletter'
		);

		$fields['newsletter_heading'] = array(
			'name' => __( 'Newsletter Text', 'woothemes-syl' ),
			'description' => __( 'Enter the newsletter heading.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => false,
			'section' => 'newsletter'
		);

		$fields['newsletter_subscribe_button'] = array(
			'name' => __( 'Submit Button Text', 'woothemes-syl' ),
			'description' => __( 'Enter the submit button text.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => false,
			'section' => 'newsletter'
		);

		$fields['newsletter_service'] = array(
			'name' => __( 'Newsletter Service', 'woothemes-syl' ),
			'description' => __( 'Select which Newsletter service you are using.', 'woothemes-syl' ),
			'type' => 'select',
			'options' => $newsletter_service_options ,
			'default' => '' ,
			'section' => 'newsletter'
		);

		$fields['newsletter_service_id'] = array(
			'name' => __( 'Feedburner Feed ID', 'woothemes-syl' ),
			'description' => sprintf( __( 'Enter the your Feedburner Feed ID %s(?)%s.', 'woothemes-syl' ), '<a href="' . esc_url( 'http://support.google.com/feedburner/bin/answer.py?hl=en&answer=78982' ) . '" target="_blank">', '</a>' ),
			'type' => 'text',
			'default' => '' ,
			'section' => 'newsletter'
		);

		$fields['newsletter_service_form_action'] = array(
			'name' => __( 'Campaign Monitor Form Action', 'woothemes-syl' ),
			'description' => __( 'Enter the the form action URL of your Campaign Monitor form.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => '' ,
			'section' => 'newsletter'
		);

		$fields['newsletter_mail_chimp_list_subscription_url'] = array(
			'name' => __( 'MailChimp List Subscription URL', 'woothemes-syl' ),
			'description' => sprintf( __( 'If you have a MailChimp account you can enter the %sMailChimp List Subscribe URL%s to allow your users to subscribe to a MailChimp List.', 'woothemes-syl' ), '<a href="' . esc_url( 'http://woochimp.heroku.com/' ) . '" target="_blank">', '</a>' ),
			'type' => 'text',
			'default' => '' ,
			'section' => 'newsletter'
		);
		$fields['newsletter_mad_mimi_subscription_url'] = array(
			'name' => __( 'Mad Mimi Webform URL', 'woothemes-syl' ),
			'description' => __( 'Enter your Mad Mimi Webform URL, eg. https://madmimi.com/signups/subscribe/####', 'woothemes-syl' ),
			'type' => 'text',
			'default' => '' ,
			'section' => 'newsletter'
		);

		$fields['newsletter_aweber_list_id'] = array(
			'name' => __( 'Aweber List Name', 'woothemes-syl' ),
			'description' => __( 'Enter the name of your Aweber list to subscribe users to.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => '' ,
			'section' => 'newsletter'
		);

		$fields['newsletter_wysija_list_id'] = array(
			'name' => __( 'Wysija List', 'woothemes-syl' ),
			'description' => __( 'Select the name of the Wysija list to subscribe users to.', 'woothemes-syl' ),
			'type' => 'select',
			'default' => '' ,
			'section' => 'newsletter',
			'options' => $wysija_lists
		);

		$fields['enable_social'] = array(
			'name' => __( 'Enable Social Media Area' , 'woothemes-syl' ),
			'description' => __( 'Enable the Social Media Area on the See You Later page.' , 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'social'
		);

		$fields['social_heading'] = array(
			'name' => __( 'Social Heading' , 'woothemes-syl' ),
			'description' => __( 'The heading to display in the Social Media Area' , 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		$fields['twitter_account'] = array(
			'name' => __( 'Twitter' , 'woothemes-syl' ),
			'description' => __( 'Your Twitter username eg. WooThemes for @WooThemes' , 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		$fields['facebook_account'] = array(
			'name' => __( 'Facebook' , 'woothemes-syl' ),
			'description' => __( 'Your Facebook vanity URL username eg. WooThemes for http://www.facebook.com/WooThemes' , 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		$fields['google_account'] = array(
			'name' => __( 'Google+' , 'woothemes-syl' ),
			'description' => __( 'Your Google+ page ID eg. 112186000520658104704 for https://plus.google.com/112186000520658104704/' , 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		$fields['pinterest_account'] = array(
			'name' => __( 'Pinterest' , 'woothemes-syl' ),
			'description' => __( 'Your Pinterest username eg. WooThemes for http://pinterest.com/woothemes/' , 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		$fields['youtube_account'] = array(
			'name' => __( 'YouTube' , 'woothemes-syl' ),
			'description' => __( 'Your YouTube profile username eg. WooThemes for http://www.youtube.com/user/woothemes/' , 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		$fields['flickr_account'] = array(
			'name' => __( 'Flickr' , 'woothemes-syl' ),
			'description' => __( 'Your Flickr account username eg. WooThemes for http://www.flickr.com/photos/woothemes/' , 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		$fields['linkedin_account'] = array(
			'name' => __( 'LinkedIn' , 'woothemes-syl' ),
			'description' => __( 'Your LinkedIn Profile URL username eg. WooThemes for http://linkedin.com/in/WooThemes, if it is a company page prefix with company/ eg. company/WooThemes' , 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		$fields['show_rss'] = array(
			'name' => __( 'RSS Feed' , 'woothemes-syl' ),
			'description' => __( 'Show RSS Feed link.', 'woothemes-syl' ),
			'type' => 'checkbox',
			'default' => '',
			'section' => 'social'
		);

		$fields['rss_feed_url'] = array(
			'name' => __( 'RSS Feed URL' , 'woothemes-syl' ),
			'description' => __( 'The full URL to a RSS Feed link, leave empty to use the current site RSS Feed.', 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		$fields['email'] = array(
			'name' => __( 'Email Address', 'woothemes-syl' ),
			'description' => '',
			'type' => 'text',
			'default' => '',
			'section' => 'social'
		);

		if ( current_user_can( 'unfiltered_html' ) ) {
			$fields['custom-html-code-head'] = array(
				'name' => __( 'Inside the &lt;head&gt; Tags', 'woothemes-syl' ),
				'description' => __( 'Output custom HTML code inside the &lt;head&gt; tags of your See You Later page.', 'woothemes-syl' ),
				'type' => 'html',
				'default' => '',
				'section' => 'custom-html',
				'required' => 0,
				'form' => 'form_field_textarea',
				'validate' => 'validate_field_html'
			);

			$fields['custom-html-code-footer'] = array(
				'name' => __( 'Before the closing &lt;/body&gt; Tag', 'woothemes-syl' ),
				'description' => __( 'Output custom HTML code before the closing &lt;/body&gt; tag of your See You Later page.', 'woothemes-syl' ),
				'type' => 'html',
				'default' => '',
				'section' => 'custom-html',
				'required' => 0,
				'form' => 'form_field_textarea',
				'validate' => 'validate_field_html'
			);
		}

		$fields['google_analytics_id'] = array(
			'name' => __( 'Google Analytics ID', 'woothemes-syl' ),
			'description' => __( 'Log into your google analytics account to find your ID. e.g. UA-XXXXX-X', 'woothemes-syl' ),
			'type' => 'text',
			'default' => '',
			'section' => 'custom-html'
		);

		$fields['custom_css'] = array(
			'name' => __( 'Custom CSS', 'woothemes-syl' ),
			'description' => __('Add custom CSS to your See You Later page.', 'woothemes-syl' ),
			'type' => 'textarea',
			'default' => '',
			'section' => 'custom-html'
		);

		$fields['access_url'] = array(
			'name' => __( 'Bypass URL', 'woothemes-syl' ),
			'description' => sprintf( __( 'Add a phrase that will be appended to your site URL you can give a client to bypass the See You Later page and give access to your site. Eg. <strong>%s<span id="site-url-example"></span></strong>', 'woothemes-syl' ), site_url('/') ),
			'type' => 'text',
			'default' => '',
			'section' => 'access',
			'prepend' => '<strong>' . site_url('/') . '</strong>'
		);

		$fields['access_ip'] = array(
			'name' => __( 'Access Through IP', 'woothemes-syl' ),
			'description' => __( 'A list of IP addresses that will be granted access to the site. Enter each new IP address on a separate line.', 'woothemes-syl' ),
			'type' => 'textarea',
			'default' => '',
			'section' => 'access'
		);

		$fields['exclude_url_patern'] = array(
			'name' => __( 'Exclude URL Pattern', 'woothemes-syl' ),
			'description' => sprintf( __( 'Exclude URLs from displaying the See You Later page using <a href="%s">regular expression</a>. Eg. Enter \'shop\' to exclude %s', 'woothemes-syl' ), 'http://www.regular-expressions.info/', site_url('shop') ),
			'type' => 'text',
			'default' => '',
			'section' => 'access'
		);

		$this->fields = apply_filters( 'woothemes_syl_settings_fields', $fields );

	} // End init_fields()

	/**
	 * Live preview button HTML markup.
	 * @access  public
	 * @since   1.1.0
	 * @return  void
	 */
	public function live_preview_button () {
		global $woothemes_syl;
		if ( isset( $woothemes_syl->settings->settings['enable'] ) && ! $woothemes_syl->settings->settings['enable'] ) return;

		$url = wp_nonce_url( add_query_arg( array( 'syl_preview' => 'true' ), get_site_url() ) );
		echo '<a href="' . esc_url( $url ) . '" class="button live-preview-button" target="_blank">' . __( 'Preview', 'woothemes-syl' ) . '</a>';
	} // End live_preview_button()
} // End Class
?>