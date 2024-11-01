<?php

/**
 * Contains action hooks and functions for contract entries.
 *
 * @class ZTSA_Entries_table
 * @package smart-agreements\includes
 * @version 1.0.0
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if ( !class_exists( 'ZTSA_Entries_table' ) ) {
	class ZTSA_Entries_table extends WP_List_Table
	{
		private $customer_info;
		/**
		 * Get cutomer information data from database.
		 *
		 * @return results 
		 */
		function ztsa_get_data( $search = "" )
		{
			global $wpdb, $table_prefix;
			$table_name = $table_prefix . 'ztsa_customer_info';
			if ( !empty( $search ) ) {
				$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE form_id = '%s' ", sanitize_text_field( $search ) ), ARRAY_A );
			} else {
				$results = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
			}
			return $results;
		}

		/**
		 * Define table columns
		 *
		 * @return void
		 */
		function get_columns()
		{
			$columns = array( 
				'id' => __( 'ID', "smart-agreements" ),
				'form_id' => __( 'Form Title [ID]', "smart-agreements" ),
				'customer_detail' => __( 'Customer Detail', "smart-agreements" ),
				'customer_sign' => __( 'Sign Status', "smart-agreements" ),
				'date' => __( 'Date', "smart-agreements" )
			 );
			return $columns;
		}

		/**
		 * Bind table with columns, data and all
		 *
		 * @return void
		 */
		function ztsa_prepare_items()
		{
			if ( isset( $_GET['form_id'] ) ) {
				$this->customer_info = $this->ztsa_get_data( sanitize_text_field( $_GET['form_id'] ) );
			} else {
				$this->customer_info = $this->ztsa_get_data();
			}
			$columns = $this->get_columns();
			$hidden =  array();
			$sortable = $this->ztsa_get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$per_page = 10;
			$current_page = $this->get_pagenum();
			$total_items = count( $this->customer_info );
			$this->customer_info = array_slice( $this->customer_info, ( ( $current_page - 1 ) * $per_page ), $per_page );
			$this->set_pagination_args( array( 
				'total_items' => $total_items,
				'per_page'    => $per_page
			 ) );
			usort( $this->customer_info, array( &$this, 'ztsa_usort_reorder' ) );
			$this->items = $this->customer_info;
		}

		/**
		 * function for printing velue in table

		 *
		 * @param [type] $items
		 * @param [type] $column_name
		 * @return void
		 */
		function column_default( $items, $column_name )
		{
			switch ( $column_name ) {
				case 'id':
					return esc_attr( $items['id'] );
				case 'form_id':
					$result = "<a href=" . esc_url( admin_url( "post.php?post=" . esc_attr( $items['form_id'] ) . "&action=edit" ) ) . ">" . esc_attr( $items['form_title'] ) . "  [" . esc_attr( $items['form_id'] ) . "]</a>";
					return $result;
				case 'customer_detail':
					return "<button class='show-d' id='myBtn' data-nonce='" . esc_attr( wp_create_nonce( 'ztsa_show_entry_details' ) ) . "' data-id='" . esc_attr( $items['id'] ) . "'>Show Details</button>";
				case 'customer_sign':
					$id = sanitize_text_field( $items['id'] );
					global $wpdb, $table_prefix;

					$customer_info_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "ztsa_customer_info WHERE id=%d", $id ) );

					$Additional_cust_no = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( * ) FROM " . $wpdb->prefix . "ztsa_extra_customer_info WHERE entry_id='%d'", $id ) );
					$Additional_cust_sign = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( customer_sign ) FROM " . $wpdb->prefix . "ztsa_extra_customer_info WHERE entry_id='%d'", $id ) );

					$customer_sign = ( !empty( $items['customer_sign'] ) ) ? 'true' : 'false';
					$additional_customer_sign = ( $Additional_cust_no == $Additional_cust_sign ) ? 'true' : 'false';
					$owner_sign = $items['owner_sign'];
					$pdf_url = content_url( 'uploads/ztsa_Agreement/ztsa-agreement-' . $id . '.pdf' );

					if ( $customer_sign == 'true' && $additional_customer_sign == 'true' && empty( $owner_sign ) ) :
						return "<label >" . __( 'Pending for owner sign', 'smart-agreements' ) . "</label><br><button  class='show_agreement' customer-id='" . esc_attr( $id ) . "'>" . __( 'CLICK HERE', 'smart-agreements' ) . "</button><label >" . __( ' for owner sign.', 'smart-agreements' ) . "</label>";
					elseif ( !empty( $owner_sign ) ) :
						return '<button> <a href="' . esc_url( $pdf_url ) . '" target="_blank">' . __( "SHOW AGREEMENT", "smart-agreements" ) . '</a></button>';
					elseif ( !empty( $customer_info_data->owner_status ) && $customer_info_data->owner_status == "reject" ) :
						return esc_html__( "Rejected by the owner.", "smart-agreements" );
					elseif ( !empty( $customer_info_data->owner_status ) && $customer_info_data->owner_status == "accept" && $customer_info_data->customer_status != "reject" ) :
						return esc_html__( "Pending for customer sign.", "smart-agreements" );
					elseif ( !empty( $customer_info_data->customer_status ) && $customer_info_data->customer_status == "reject" ) :
						return esc_html__( "Rejected by the customer.", "smart-agreements" );
					else :
						return esc_html__( "Pending for owner approval.", "smart-agreements" );
					endif;

				case
				'date':
					return esc_html( $items['date'] );
				default:
					return print_r( $items, true ); //Show the whole array for troubleshooting purposes
			}
		}

		/**
		 * Add sorting to columns.
		 *
		 * @return array sortable_columns 
		 */
		protected function ztsa_get_sortable_columns()
		{
			$sortable_columns = array( 
				'id'  => array( 'id', false ),
				'form_id'   => array( 'form_id', false ),
				'date' => array( 'date', true )
			 );
			return $sortable_columns;
		}

		// Sorting function
		/**
		 * Sorting function for entry table
		 *
		 * @return array result 
		 */
		function ztsa_usort_reorder( $value1, $value2 )
		{
			$orderby = ( !empty( $_GET['orderby'] ) ) ? sanitize_text_field( $_GET['orderby'] ) : 'id'; // If no sort, default to post_title
			$order = ( !empty( $_GET['order'] ) ) ? sanitize_text_field( $_GET['order'] ) : 'desc'; // If no order, default to asc
			$result = strcmp( $value1[$orderby], $value2[$orderby] ); // Determine sort order
			return ( $order === 'asc' ) ? $result : -$result; // Send final sort direction to usort

			$orderby = ( !empty( $_GET['orderby'] ) ) ? sanitize_text_field( $_GET['orderby'] ) : 'form_id';
			$order = ( !empty( $_GET['order'] ) ) ? sanitize_text_field( $_GET['order'] ) : 'asc';
			$result = strcmp( $value1[$orderby], $value2[$orderby] );
			return ( $order === 'asc' ) ? $result : -$result;

			$orderby = ( !empty( $_GET['orderby'] ) ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
			$order = !empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'asc';
			$result = strcmp( $value1[$orderby], $value2[$orderby] );
			return ( $order === 'asc' ) ? $result : -$result;
		}
	}
}
