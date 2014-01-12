<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WooThemes_SYL {

	const TEXT_DOMAIN = 'woothemes-syl';
	public $version;
	private $file;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->version = '1.1.0';

		$this->plugin_path = trailingslashit( dirname( $file ) );
		$this->template_path = trailingslashit( dirname( $file ) ) . 'templates/';
		$this->screens_path = trailingslashit( $this->plugin_path ) . 'screens/';

		$this->plugin_url = trailingslashit( plugins_url( '', $plugin = $file ) );
		$this->assets_url = trailingslashit( $this->plugin_url ) . 'assets/';

		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		$this->themes = $this->load_themes();

		$this->load_settings();

		// Load the frontend
		if ( ! is_admin() ) {
			require_once( 'class-woothemes-syl-frontend.php' );
			$this->frontend = new WooThemes_SYL_FrontEnd( $this->file );
		}

		add_action( 'admin_bar_menu', array( $this, 'add_toolbar_button' ), 10 );
		add_action( 'admin_print_styles', array( $this, 'load_toolbar_button_css' ), 10 );
		add_action( 'wp_print_styles', array( $this, 'load_toolbar_button_css' ), 10 );
	} // End __construct()

	/**
	 * Load Localization function.
	 *
	 * @description Load plugin textdomains
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation() {
		load_plugin_textdomain( self::TEXT_DOMAIN, false, $this->plugin_path . 'languages/' );
	} // End load_localisation()

	/**
	 * load_settings function
	 *
	 * Initialize the settings and load it via the settings API
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_settings() {
		require_once( 'class-woothemes-syl-settings-api.php' );
		require_once( 'class-woothemes-syl-settings.php' );

		$this->settings = new WooThemes_SYL_Settings();
		$this->settings->token = 'woothemes-see-you-later';
		$this->settings->settings_version = $this->version;

		// Setup Admin Settings data
		$this->settings->has_tabs 	= true;
		$this->settings->name 		= __( 'See You Later Settings', 'woothemes-syl' );
		$this->settings->menu_label	= __( 'See You Later', 'woothemes-syl' );
		$this->settings->page_slug	= 'see-you-later';
		$this->settings->setup_settings();
		$this->settings->get_settings();
	}

	/**
	 * load_themes function.
	 *
	 * Locate all themes and load their data
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_themes() {
		$themes = array();
		$themes_data = array();

		// Add current WP Theme's 503 page as an option also
		$themes['503'] = array( 'Name' => __( 'Your Theme\'s 503 Page', 'woothemes-syl' ) );

		// see you later template directories
		foreach( (array) apply_filters( 'woothemes_syl_template_roots', array( $this->template_path ) ) as $path ) {
			if( !is_dir( $path ) )
				continue;

			$theme_dirs[ $path ] = array_diff( scandir( $path ), array( '..', '.' ) );
			foreach( $theme_dirs[ $path ] as $theme ) {
				if( file_exists( trailingslashit( $path ) . $theme . '/style.css' ) )
					$themes[ $theme ] = wp_get_theme( $theme, $path );
			}
		}

		foreach( (array) apply_filters( 'woothemes_syl_theme_roots', array() ) as $theme ) {
			$themes[ $theme['slug'] ] = wp_get_theme( $theme['slug'], $theme['path'] );
		}

		return $themes;
	}

	/**
	 * add_toolbar_button function
	 *
	 * Add a button to the WordPress Toolbar.
	 * @since  1.0.2
	 * @return  void
	 */
	public function add_toolbar_button() {
		global $wp_admin_bar, $current_user;

		if ( ! is_admin_bar_showing() || ! is_object( $wp_admin_bar ) )
			return false;

		$title = __( 'See You Later Mode', 'woothemes-syl' );
		$id = 'woothemes-syl';
		if ( true == $this->settings->settings['enable'] ) {
			$title = __( 'See You Later is Active', 'woothemes-syl' );
			$id = 'woothemes-syl-active';
		}

		if ( 'woothemes-syl' == $id && ! current_user_can( 'manage_options' ) ) {
			// Silence is golden...
		} else {
			$wp_admin_bar->add_menu( array( 'parent' => 'top-secondary', 'id' => esc_attr( $id ), 'title' => $title, 'href' => esc_url( add_query_arg( 'page', $this->settings->page_slug, admin_url( 'options-general.php' ) ) ) ) );
		}
	} // End add_toolbar_button()

	/**
	 * load_toolbar_button_css function
	 *
	 * Load custom CSS for the new button on the WordPress Toolbar.
	 * @since  1.0.2
	 * @return  void
	 */
	public function load_toolbar_button_css() {
		global $wp_admin_bar;

		if ( ! is_admin_bar_showing() || ! is_object( $wp_admin_bar ) )
			return false;

		$html = '<style type="text/css">' . "\n";
		$html .= '#wpadminbar #wp-admin-bar-woothemes-syl-active { background-color: #d00;
	background-image: -moz-linear-gradient(bottom, #f44, #d00 );
	background-image: -webkit-gradient(linear, left bottom, left top, from(#f44), to(#d00)); }';
		$html .= '#wp-admin-bar-woothemes-syl-active .ab-item { color: #EBEBEB; }';
		$html .= '#wp-admin-bar-woothemes-syl-active .ab-item:hover, #wp-admin-bar-woothemes-syl-active .ab-item:focus, #wp-admin-bar-woothemes-syl-active .ab-item:active { background: none !important; color: #FFFFFF; }';
		$html .= '</style>' . "\n";

		echo $html;
	} // End load_toolbar_button_css()

} // End Class