<?php
/*
Plugin Name: See You Later by WooThemes
Plugin URI: http://woothemes.com/
Description: A simple easy to use Maintenance Mode / Coming Soon plugin for your WordPress site.
Version: 1.1.0
Author: WooThemes
Author URI: http://woothemes.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/*  Copyright 2013  WooThemes  (email : info@woothemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! is_admin() ) {
    require_once 'woothemes-syl-functions.php';
    require_once 'woothemes-syl-hooks.php';
}


require_once 'classes/class-woothemes-syl.php';

if ( ! function_exists( 'woothemes_queue_update' ) )
    require_once( 'woo-includes/woo-functions.php' );

global $woothemes_syl;
$woothemes_syl = new WooThemes_SYL( __FILE__ );

/**
 * Plugin updates
 * @since  1.0.0
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '94296bc4a3b7d82245201b87523c18f0', 225355 );

?>