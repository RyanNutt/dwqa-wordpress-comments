<?php
/**
 * Plugin Name:       DWQA Comments
 * Plugin URI:        https://www.nutt.net
 * Description:       Replace WordPress comments with DW Q&A Plugin
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Ryan Nutt
 * Author URI:        https://www.nutt.net/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dwqa-comments
 */

include(__DIR__ . '/_inc/class.dwcomments.php');

if( ! class_exists( 'Smashing_Updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . '_inc/updater.php' );
}
$updater = new Smashing_Updater( __FILE__ );
$updater->set_username( 'ryannutt' );
$updater->set_repository( 'dwqa-wordpress-comments' );
$updater->initialize();
