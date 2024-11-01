<?php
/**
* Plugin Name: Smart Agreements
* Description:The smart agreements plugin helps to create a agreement/contract and digital signature.
* Version: 1.0.3
* Author: Zehntech Technologies Pvt. Ltd.
* Author URI: https://www.zehntech.com/
* License: GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: smart-agreements
*
* @package smart-agreements
*/

defined( 'ABSPATH' ) || die( 'you do not have access to this page!' );

defined( 'ZTSA_PLUGIN_DIR' ) ? '' : define( 'ZTSA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'ZTSA_PLUGIN_URL' ) ? '' : define( 'ZTSA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
defined( 'ZTSA_ASSETS_URL' ) ? '' : define( 'ZTSA_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
defined( 'ZTSA_PLUGIN_INCLUDES_DIR' ) ? '' : define( 'ZTSA_PLUGIN_INCLUDES_DIR', ZTSA_PLUGIN_DIR . 'includes/' );
defined( 'ZTSA_UI_FRONT_DIR' ) ? '' : define( 'ZTSA_UI_FRONT_DIR', ZTSA_PLUGIN_DIR . 'ui-front/' );
defined( 'ZTSA_UI_ADMIN_DIR' ) ? '' : define( 'ZTSA_UI_ADMIN_DIR', ZTSA_PLUGIN_DIR . 'ui-admin/' );
defined( 'ZTSA_POST_TYPE_SLUG' ) ? '' : define( 'ZTSA_POST_TYPE_SLUG', 'ztsa-smart-agreement' );
defined( 'ZTSA_SETTING_PAGE_SLUG' ) ? '' : define( 'ZTSA_SETTING_PAGE_SLUG', 'ztsa-setting' );
defined( 'ZTSA_ENTRIES_PAGE_SLUG' ) ? '' : define( 'ZTSA_ENTRIES_PAGE_SLUG', 'ztsa-entries' );
defined( 'ZTSA_AGREEMENT_PAGE_SLUG' ) ? '' : define( 'ZTSA_AGREEMENT_PAGE_SLUG', 'agreements' );

/**
 * Create table on activation of hook.
 */
function ztsa_on_activation() {
	global $wpdb;
	$table_name_1 = $wpdb->prefix . 'ztsa_customer_info';
	$table_name_2 = $wpdb->prefix . 'ztsa_extra_customer_info';

	$charset_collate = $wpdb->get_charset_collate();

	$sql_1 = "CREATE TABLE IF NOT EXISTS $table_name_1 (
		id BIGINT NOT NULL AUTO_INCREMENT,
		form_title TEXT NOT NULL,
		form_id BIGINT NOT NULL,
		customer_info LONGTEXT NOT NULL,
		customer_sign LONGTEXT,
		owner_sign LONGTEXT,
		owner_comment LONGTEXT,
		owner_status varchar(50),
		customer_comment LONGTEXT,
		customer_status varchar(50),
		`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	) $charset_collate;";

	$sql_2 = "CREATE TABLE IF NOT EXISTS $table_name_2(
		id BIGINT NOT NULL AUTO_INCREMENT,
		customer_name TEXT NOT NULL,
		customer_email TEXT NOT NULL,
		entry_id BIGINT NOT NULL,
		form_id BIGINT NOT NULL,
		customer_sign LONGTEXT NULL,
		customer_comment LONGTEXT,
		customer_status varchar(50),
		`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql_1 );
	dbDelta( $sql_2 );
}

register_activation_hook( __FILE__, 'ztsa_on_activation' );

// Create folder in uploads for aggreement.
$folder_path = WP_CONTENT_DIR . '/uploads/ztsa_Agreement';
if ( ! file_exists( $folder_path ) ) {
	wp_mkdir_p( $folder_path, 0777, false );
}


// Include the contracts class.
require_once ZTSA_PLUGIN_INCLUDES_DIR . 'class-ztsa-contracts.php';
require_once ZTSA_PLUGIN_INCLUDES_DIR . 'class-ztsa-setting.php';
require_once ZTSA_PLUGIN_INCLUDES_DIR . 'class-ztsa-entries.php';
require_once ZTSA_PLUGIN_INCLUDES_DIR . 'class-ztsa-entries-table.php';
require_once ZTSA_PLUGIN_INCLUDES_DIR . 'class-ztsa-pdf-generator.php';
