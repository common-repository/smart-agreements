<?php
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}
global $wpdb;
 $table_name_1= $wpdb->prefix.'ztc_customer_info';
 $table_name_2 = $wpdb->prefix . 'ztc_extra_customer_info';
 $sql_1= "DROP TABLE IF EXISTS `$table_name_1`; ";
 $sql_2= "DROP TABLE IF EXISTS `$table_name_2`; ";
 $wpdb->query($sql_1);
 $wpdb->query($sql_2);
  