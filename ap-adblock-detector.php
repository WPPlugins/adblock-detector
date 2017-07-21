<?php
/*
Plugin Name: AdBlock Detector
Plugin URI: https://wordpress.org/plugins/adpushup/
Description: Find out how many of your visitors are using ad blocking software.
Version: 1.0.0
Author: AdPushup
Author URI: http://adpushup.com
License: GPL2+
Text Domain: adblock-detector
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ap-adblock-detector-activator.php
 */
function ap_adb_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ap-adblock-detector-activator.php';
	AP_Adblock_Detector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ap-adblock-detector-deactivator.php
 */
function ap_adb_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ap-adblock-detector-deactivator.php';
	AP_Adblock_Detector_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'ap_adb_activate' );
register_deactivation_hook( __FILE__, 'ap_adb_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ap-adblock-detector.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function ap_adb_run() {

	$plugin = new AP_Adblock_Detector();
	$plugin->run();

}
ap_adb_run();
