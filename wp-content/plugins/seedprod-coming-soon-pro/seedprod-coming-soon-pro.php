<?php
/*
Plugin Name: SeedProd Coming Soon Pro
Plugin URI: http://www.seedprod.com
Description: The Ultimate Coming Soon & Maintenance Mode Plugin
Version:  3.10.0
Author: SeedProd
Author URI: http://www.seedprod.com
TextDomain: seedprod 
License: GPLv2
Copyright 2012  John Turner (email : john@seedprod.com, twitter : @johnturner)
*/

/**
 * Init
 *
 * @package WordPress
 * @subpackage seedprod-coming-soon-pro
 * @since 0.1.0
 */

/**
 * Plugin Data
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$plugin_data = get_plugin_data( __FILE__, false, false );

/**
 * Default Constants
 */
define( 'SEED_CSP3_SHORTNAME', 'seed_csp3' ); // Used to reference namespace functions.
define( 'SEED_CSP3_FILE', 'seedprod-coming-soon-pro/seedprod-coming-soon-pro.php' ); // Used for settings link.
define( 'SEED_CSP3_TEXTDOMAIN', 'seedprod' ); // Your textdomain
define( 'SEED_CSP3_PLUGIN_NAME', __( 'Coming Soon Pro', 'seedprod' ) ); // Plugin Name shows up on the admin settings screen.
define( 'SEED_CSP3_VERSION', $plugin_data['Version'] ); // Plugin Version Number. Recommend you use Semantic Versioning http://semver.org/
define( 'SEED_CSP3_REQUIRED_WP_VERSION', '3.3' ); // Required Version of WordPress
define( 'SEED_CSP3_PLUGIN_PATH', plugin_dir_path( __FILE__ ) ); // Example output: /Applications/MAMP/htdocs/wordpress/wp-content/plugins/seed_csp3/
define( 'SEED_CSP3_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); // Example output: http://localhost:8888/wordpress/wp-content/plugins/seed_csp3/
define( 'SEED_CSP3_TABLENAME', 'csp3_subscribers' );
define( 'SEED_CSP3_API_URL', 'http://app.seedprod.com/api/get-update-v2/' );


/**
 * Load Translation
 */
function seed_csp3_load_textdomain() {
    load_plugin_textdomain( 'seedprod', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'seed_csp3_load_textdomain');

/**
 * Upon activation of the plugin, see if we are running the required version and deploy theme in defined.
 *
 * @since 0.1.0
 */
function seed_csp3_activation(){
    if ( version_compare( get_bloginfo( 'version' ), SEED_CSP3_REQUIRED_WP_VERSION, '<' ) ) {
        deactivate_plugins( __FILE__ );
        wp_die( sprintf( __( "WordPress %s and higher required. The plugin has now disabled itself. On a side note why are you running an old version :( Upgrade!", 'seedprod' ), SEED_CSP3_REQUIRED_WP_VERSION ) );
    }

    // Redirect to Settings
    wp_redirect(admin_url('options-general.php?page=seed_csp3'));

}
register_activation_hook( __FILE__, 'seed_csp3_activation' );


/**
 * Load Required Files
 */
require_once( 'framework/framework.php' );
require_once( 'inc/class-plugin.php' );
@include( 'extensions/pro.php' );
require_once( 'inc/config.php' );


/**
* API Updates
*/
$seed_csp3_settings_1 = get_option('seed_csp3_settings_1');
$seed_csp3_api_key = '';
if(isset($seed_csp3_settings_1['api_key'])){
    $seed_csp3_api_key = $seed_csp3_settings_1['api_key'];
}
if(defined('SEED_CSP_API_KEY')){
    $seed_csp3_api_key = SEED_CSP_API_KEY;
}
if(!empty($seed_csp3_api_key) && strlen($seed_csp3_api_key) === 16){
    add_action('init', 'seed_csp3_auto_update');
}
function seed_csp3_auto_update()
{
    global $seed_csp3_api_key;
    require_once 'framework/seedprod-auto-update.php';
    $seed_csp3_plugin_domain = home_url();
    $seed_csp3_plugin_api_key = trim($seed_csp3_api_key);
    $seed_csp3_plugin_current_version = SEED_CSP3_VERSION;
    $seed_csp3_plugin_remote_path = SEED_CSP3_API_URL;
    #TODO Add new slug format in
    $seed_csp3_plugin_slug = plugin_basename(__FILE__);
    new seedprod_auto_update($seed_csp3_plugin_current_version, $seed_csp3_plugin_remote_path, $seed_csp3_plugin_slug, $seed_csp3_plugin_api_key,$seed_csp3_plugin_domain);
}
