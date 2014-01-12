<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WooThemes_SYL_FrontEnd {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( $file ) {
		add_action( 'init', array( $this, 'maintenance_mode' ) );
		remove_all_actions( 'wp_print_scripts' );
	}

	/**
	 * maintenance_mode function.
	 *
	 * Function that makes it all happen
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function maintenance_mode() {
		global $woothemes_syl;
		if ( isset( $woothemes_syl->settings->settings['enable'] ) && $woothemes_syl->settings->settings['enable'] ) {
			if ( $this->can_page_bypass() || $this->can_user_bypass() )
				return;
			nocache_headers();
			$this->load_template( $woothemes_syl->settings->settings['theme'] );
		}
	}

	/**
	 * load_template function.
	 *
	 * @description load see you later page theme
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_template( $template ) {
		global $woothemes_syl;
		if ( '503' == $template ) {
			include( get_stylesheet_directory() . '/503.php' );
			exit;
		}
		$template_path = trailingslashit( $woothemes_syl->themes[ $template ]->theme_root ) . $template;
		if ( file_exists( $template_path . '/functions.php' ) )
			require_once $template_path . '/functions.php';

		if (  isset( $woothemes_syl->themes[ $template ]->theme_root ) && file_exists( $template_path . '/index.php' ) )
			require_once $template_path . '/index.php';
		else {
			wp_die( syl_message( false ), syl_title( false ) );
		}
		exit();
	}

	/**
	 * add_toolbar_button function.
	 *
	 * @description add see you later mode button/notice to admin toolbar
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function add_toolbar_button () {
		global $wp_admin_bar, $current_user, $woothemes_syl;

		if ( ! is_admin_bar_showing() || ! is_object( $wp_admin_bar ) )
			return false;

		$title = __( 'See You Later Mode', 'woothemes-syl' );
		$id = 'woothemes-syl';

		if ( 'woothemes-syl' == $id && ! current_user_can( 'manage_options' ) ) {
			// Silence is golden...
		} else {
			$wp_admin_bar->add_menu( array( 'parent' => 'top-secondary', 'id' => esc_attr( $id ), 'title' => $title, 'href' => esc_url( add_query_arg( 'page', $this->settings->page_slug, admin_url( 'options-general.php' ) ) ) ) );
		}
	} // End add_toolbar_button()

	/**
	 * load_toolbar_button_css function.
	 *
	 * @description load CSS for admin toolbar button to be styled
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_toolbar_button_css () {
		global $wp_admin_bar;

		if ( ! is_admin_bar_showing() || ! is_object( $wp_admin_bar ) )
			return false;

		$html = '<style type="text/css">' . "\n";
		$html .= '#wpadminbar #wp-admin-bar-woothemes-syl { background-color: #d00;
	background-image: -moz-linear-gradient(bottom, #f44, #d00 );
	background-image: -webkit-gradient(linear, left bottom, left top, from(#f44), to(#d00)); }';
		$html .= '#wp-admin-bar-woothemes-syl .ab-item { color: #EBEBEB; }';
		$html .= '#wp-admin-bar-woothemes-syl.ab-item:hover, #wp-admin-bar-woothemes-syl.ab-item:focus, #wp-admin-bar-woothemes-syl .ab-item:active { background: none !important; color: #FFFFFF; }';
		$html .= '</style>' . "\n";

		echo $html;
	} // End load_toolbar_button_css()

	/**
	 * can_user_bypass function.
	 *
	 * @description check if current user can bypass
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function can_user_bypass() {
		global $woothemes_syl;

		if ( ( isset( $_GET['syl_preview'] ) && 'true' == $_GET['syl_preview'] ) && ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'] ) ) )
			return false;

		return (bool) apply_filters( 'woothemes_syl_user_bypass', current_user_can( $woothemes_syl->settings->settings['role'] ) );
	}

	/**
	 * can_page_bypass function.
	 *
	 * Credit: Michael Wöhrer : http://sw-guide.de/
	 * See You Later page should not display in certain situations
	 *
	 * @description check if current page can be bypassed
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function can_page_bypass() {
		global $woothemes_syl;

		if ( ( isset( $_GET['syl_preview'] ) && 'true' == $_GET['syl_preview'] ) && ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'] ) ) )
			return false;

		foreach( (array) apply_filters( 'woothemes_syl_bypass_endpoints', array( 'wp-login.php', 'async-upload.php', 'upgrade.php' ), 'woothemes_syl_endpoint_bypass' ) as $file ) {
			if( strstr( $_SERVER['PHP_SELF'], $file ) )
				return true;
		}

		// check for url pattern to allow access
		if ( ! empty( $woothemes_syl->settings->settings['exclude_url_patern'] ) ) {
			if ( preg_match( '/' . $woothemes_syl->settings->settings['exclude_url_patern'] .'/', $_SERVER['REQUEST_URI'] ) == 1 )
				return true;
		}

		// check for access cookie and update expire time if found
		if ( isset( $_COOKIE['woothemes-see-you-later-has-access'] ) ) {
			setcookie( 'woothemes-see-you-later-has-access', 'yes', apply_filters( 'woothemes_syl_cookie_time', time() + 60 * 12 ) );
			return true;
		}

		// check if accessed via special url and set cookie for 12 hours
		if ( ! empty( $woothemes_syl->settings->settings['access_url'] ) ) {
			if ( strstr( htmlspecialchars( $_SERVER['REQUEST_URI'] ), '/' . $woothemes_syl->settings->settings['access_url'] ) ) {
				setcookie( 'woothemes-see-you-later-has-access', 'yes', apply_filters( 'woothemes_syl_cookie_time', time() + 60 * 12 ) );
				wp_redirect( site_url() );
				exit;
			}
		}

		// check for access via IP whitelist
		if ( ! empty( $woothemes_syl->settings->settings['access_ip'] ) ) {
			$user_ip = $this->get_user_ip();
			$ip_addresses = explode( "\n", $woothemes_syl->settings->settings['access_ip'] );
			if ( in_array( $user_ip, $ip_addresses ) )
				return true;
		}

		if( strstr( htmlspecialchars( $_SERVER['REQUEST_URI'] ), '/feed' ) || strstr( htmlspecialchars( $_SERVER['REQUEST_URI'] ), 'feed=' ) ) {
			if ( $woothemes_syl->settings->settings['enable_feeds'] )
				return true;
			else {
				nocache_headers();
				$this->http_header_unavailable();
				exit();
			}
		}

		if( strstr( htmlspecialchars( $_SERVER['REQUEST_URI'] ), '/trackback' ) || strstr( $_SERVER['PHP_SELF'], 'wp-trackback.php' ) ) {
			if ( $woothemes_syl->settings->settings['enable_trackbacks'] )
				return true;
			else {
				nocache_headers();
				$this->http_header_unavailable();
				exit();
			}
		}

		if( strstr( $_SERVER['PHP_SELF'], 'xmlrpc.php' ) ) {
			if ( $woothemes_syl->settings->settings['enable_xmlrpc'] )
				return true;
			else {
				$this->http_header_unavailable(); 
				exit();
			}
		}

		return (bool) apply_filters( 'woothemes_syl_page_bypass', false );
	}

	/**
	 * http_header_unavailable function.
	 *
	 * @description throw 503 error
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function http_header_unavailable () {
	   	header( 'HTTP/1.0 503 Service Unavailable' );
		header( 'Retry-After: ' . 60 * 60 ); // 1 hour.
	} // End http_header_unavailable()

	/**
	 * get_user_ip function.
	 *
	 * @description get the user ip address
	 * @access private
	 * @since 1.0.0
	 * @return string
	 */
	private function get_user_ip() {
		$ip = '';
		if ( isset( $_SERVER['HTTP_X_FORWARD_FOR'] ) )
			if ( ! empty( $_SERVER['HTTP_X_FORWARD_FOR'] ) )
				$ip = $_SERVER['HTTP_X_FORWARD_FOR'];
			else $ip = $_SERVER['REMOTE_ADDR'];
		else $ip = $_SERVER['REMOTE_ADDR'];

		return $ip;
	}
}