<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              reinhard@wearetraction.io
 * @since             1.0.0
 * @package           Teetch_App
 *
 * @wordpress-plugin
 * Plugin Name:       Teetch App
 * Plugin URI:        wearetraction.io
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Rein Torres
 * Author URI:        reinhard@wearetraction.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       teetch-app
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TEETCH_APP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-teetch-app-activator.php
 */
function activate_teetch_app() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-teetch-app-activator.php';
	Teetch_App_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-teetch-app-deactivator.php
 */
function deactivate_teetch_app() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-teetch-app-deactivator.php';
	Teetch_App_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_teetch_app' );
register_deactivation_hook( __FILE__, 'deactivate_teetch_app' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-teetch-app.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_teetch_app() {

	$plugin = new Teetch_App();
	$plugin->run();

}
run_teetch_app();
