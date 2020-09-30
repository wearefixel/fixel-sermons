<?php
/**
 * Plugin Name: Fixel Sermons
 * Description: Extends Seriously Simple Podcasting to turn it into a powerful sermon management system.
 * Version: 1.2.0
 * Author: Fixel
 * Author URI: https://wearefixel.com/
 */

define('FXS_VERSION', '1.1.1');
define('FXS_FILE', __FILE__);
define('FXS_PATH', plugin_dir_path(FXS_FILE));
define('FXS_URL', plugin_dir_url(FXS_FILE));
define('FXS_BASENAME', plugin_basename(FXS_FILE));
define('FXS_MIN_PHP', '7.0');
define('FXS_MIN_WP', '4.9');

function fxs_init() {
	if (! version_compare(PHP_VERSION, FXS_MIN_PHP, '>=')) {
		add_action('admin_notices', 'fxs_fail_php_version');
	} elseif (! version_compare(get_bloginfo('version'), FXS_MIN_WP, '>=')) {
		add_action('admin_notices', 'fxs_fail_wp_version');
	} elseif (! defined('SSP_VERSION')) {
		add_action('admin_notices', 'fxs_fail_ssp');
	} else {
		include_once FXS_PATH . 'vendor/autoload.php';

		Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/wearefixel/fixel-sermons/',
			FXS_FILE,
			'fixel-sermons'
		);

		include_once FXS_PATH . 'includes/class-fxs-plugin.php';
		include_once FXS_PATH . 'includes/functions.php';
	}
}
add_action('plugins_loaded', 'fxs_init');

function fxs_fail_php_version() {
	echo '<div class="error"><p>Fixel Sermons requires PHP version ' . FXS_MIN_PHP . ', plugin is currently NOT ACTIVE.</p></div>';
}

function fxs_fail_wp_version() {
	echo '<div class="error"><p>Fixel Sermons requires WordPress version ' . FXS_MIN_WP . ', plugin is currently NOT ACTIVE.</p></div>';
}

function fxs_fail_ssp() {
	echo '<div class="error"><p>Fixel Sermons requires <a href="https://wordpress.org/plugins/seriously-simple-podcasting/" target="_blank">Seriously Simple Podcasting</a>, plugin is currently NOT ACTIVE.</p></div>';
}
