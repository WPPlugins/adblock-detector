<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    AP_Adblock_Detector
 * @subpackage AP_Adblock_Detector/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    AP_Adblock_Detector
 * @subpackage AP_Adblock_Detector/includes
 * @author     Your Name <email@example.com>
 */
class AP_Adblock_Detector_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		global $wpdb;
		$db_table_name = $wpdb->prefix . 'ap_adblock_detector' . '_log';
		if( $wpdb->get_var( "SHOW TABLES LIKE '$db_table_name'" ) != $db_table_name ) {
			if ( ! empty( $wpdb->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty( $wpdb->collate ) )
			$charset_collate .= " COLLATE $wpdb->collate";

			$sql = "CREATE TABLE " . $db_table_name . " (
			`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			KEY `timestamp` (`timestamp`)
			) $charset_collate;";
			dbDelta( $sql );

			update_option( 'adblockmon_settings', array('ap_adblock_detector_enabled' => "1") );
		}
	}

}
