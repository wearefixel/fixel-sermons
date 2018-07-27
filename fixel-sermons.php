<?php
/**
 * Plugin Name: Fixel Sermons
 * Description: Extends Seriously Simple Podcasting to turn it into a powerful sermon management system.
 * Version: 1.0.0
 * Author: Fixel
 * Author URI: https://wearefixel.com/
 */

define( 'FS_VERSION', '1.0.0' );
define( 'FS_FILE', __FILE__ );
define( 'FS_PATH', plugin_dir_path( FS_FILE ) );
define( 'FS_URL', plugin_dir_url( FS_FILE ) );
define( 'FS_BASENAME', plugin_basename( FS_FILE ) );
define( 'FS_MIN_PHP', '7.0' );
define( 'FS_MIN_WP', '4.9' );

function fs_init() {
	if ( ! version_compare( PHP_VERSION, FS_MIN_PHP, '>=' ) ) {
		add_action( 'admin_notices', 'fs_fail_php_version' );
	} elseif ( ! version_compare( get_bloginfo( 'version' ), FS_MIN_WP, '>=' ) ) {
		add_action( 'admin_notices', 'fs_fail_wp_version' );
	} elseif ( ! defined( 'SSP_VERSION' ) ) {
		add_action( 'admin_notices', 'fs_fail_ssp' );
	} else {
		include_once FS_PATH . 'includes/class-fs-plugin.php';
	}
}
add_action( 'plugins_loaded', 'fs_init' );

function fs_fail_php_version() {
	echo '<div class="error"><p>Fixel Sermons requires PHP version ' . FS_MIN_PHP . ', plugin is currently NOT ACTIVE.</p></div>';
}

function fs_fail_wp_version() {
	echo '<div class="error"><p>Fixel Sermons requires WordPress version ' . FS_MIN_WP . ', plugin is currently NOT ACTIVE.</p></div>';
}

function fs_fail_ssp() {
	echo '<div class="error"><p>Fixel Sermons requires <a href="https://wordpress.org/plugins/seriously-simple-podcasting/" target="_blank">Seriously Simple Podcasting</a>, plugin is currently NOT ACTIVE.</p></div>';
}
