<?php
/**
 * Plugin Name: Monitor.chat
 * Version: 1.1.1
 * Plugin URI: https://monitor.chat
 * Description: Monitor Your Wordpress Website with XMPP Instant Messages!
 * Author: Edward Stoever
 * Author URI: https://e2e.ee/en/blog/
 * Requires at least WP: 4.0
 * Tested on WP: 5.7
 *
 * Text Domain: monitor-chat
 *
 * This plugin was modelled on the template created by Hugh Lashbrooke.
 * https://github.com/hlashbrooke/WordPress-Plugin-Template
 *
 * @package WordPress
 * @author Edward Stoever
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MONITORCHAT_VERSION', '1.1.1' );
define( 'MONITORCHAT__MINIMUM_WP_VERSION', '4.0' );
define( 'MONITORCHAT__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Load plugin class files.
require_once (MONITORCHAT__PLUGIN_DIR . 'includes/class-monitorchat.php');
require_once (MONITORCHAT__PLUGIN_DIR . 'includes/class-monitorchat-settings.php');

// Load plugin libraries.
require_once (MONITORCHAT__PLUGIN_DIR . 'includes/lib/class-monitorchat-admin-api.php');
require_once (MONITORCHAT__PLUGIN_DIR . 'includes/lib/monitorchat_shortcodes_table.php');

// Load functions
require_once (MONITORCHAT__PLUGIN_DIR . 'functions/monitorchat_general.php');
require_once (MONITORCHAT__PLUGIN_DIR . 'functions/monitorchat_wordpress.php');

// Load functions that depend on shell_exec and OS not being Windows
if((strtoupper(substr(PHP_OS,0,3))!='WIN')&&(monitorchat_shell_exec_enabled())){
    require_once (MONITORCHAT__PLUGIN_DIR . 'functions/monitorchat_linux.php');}

// Load functions that depend on other plugins
if (is_plugin_active('woocommerce/woocommerce.php')) {
     require_once (MONITORCHAT__PLUGIN_DIR . 'functions/monitorchat_woocommerce.php');}
if (is_plugin_active('updraftplus/updraftplus.php')){
     require_once (MONITORCHAT__PLUGIN_DIR . 'functions/monitorchat_updraft.php');}
if (is_plugin_active('gwolle-gb/gwolle-gb.php')){
     require_once (MONITORCHAT__PLUGIN_DIR . 'functions/monitorchat_gwolle_guestbook.php');}
if (is_plugin_active('wp-statistics/wp-statistics.php')){
     require_once (MONITORCHAT__PLUGIN_DIR . 'functions/monitorchat_wp_statistics.php');}
if (is_plugin_active('akismet/akismet.php')){
    require_once (MONITORCHAT__PLUGIN_DIR . 'functions/monitorchat_akismet.php');}

/**
 * Returns the main instance of monitorchat to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object monitorchat
 */
function monitorchat() {
	$instance = monitorchat::instance( __FILE__, MONITORCHAT_VERSION );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = monitorchat_Settings::instance( $instance );
	}

	return $instance;
}

monitorchat();


// INITITALIZE
require_once (MONITORCHAT__PLUGIN_DIR . 'initialize.php');

?>
