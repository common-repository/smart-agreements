<?php

/**
 * Contains action hooks and functions for generate pdf.
 *
 * @class ZTSA_Setting
 * @package smart-agreements\includes
 * @version 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'ZTSA_Setting' ) ) {
	/**
	 * ZTC Setting.
	 */
	class ZTSA_Setting
	{
		/**
		 * construct funcation of ZTSA_Setting to load hooks
		 */
		function __construct()
		{
			add_action( 'admin_menu', [$this, 'ztsa_setting_sub_menu'] );
			add_action( 'admin_post_save_smtp_setting', [$this, 'ztsa_save_smtp_setting'] );
			add_action( 'phpmailer_init', [$this, 'ztsa_phpmailer_setting'] );
			add_action( 'wp_ajax_test_mail_check', [$this, 'ztsa_test_mail_check'] );
			add_action( 'admin_post_ztsa_notification_setting_tab', [$this, 'ztsa_notification_setting_tab'] );
		}

		/**
		 * Adding Submenu Page of Setting menu.
		 *
		 * @return void 
		 */
		function ztsa_setting_sub_menu_page()
		{
			if ( isset( $_GET['ztsa_page'] ) ) {
				require_once( ZTSA_UI_ADMIN_DIR . '/' . esc_attr( sanitize_text_field( $_GET['ztsa_page'] ) ) . '.php' );
			} else {
				require_once( ZTSA_UI_ADMIN_DIR . '/setting-page.php' );
			}
		}

		/**
		 * Adding Submenu for Settings
		 *
		 * @return void
		 */
		function ztsa_setting_sub_menu()
		{
			$my_slug = sanitize_key( ZTSA_SETTING_PAGE_SLUG );
			add_submenu_page( 
				'edit.php?post_type=' . ZTSA_POST_TYPE_SLUG,
				__( "Contract Form Setting", "smart-agreements" ),
				'Settings',
				'manage_options',
				$my_slug,
				[$this, 'ztsa_setting_sub_menu_page']
			 );
		}

		/**
		 * Save Smtp setting into option table
		 *
		 * @return void
		 */
		function ztsa_save_smtp_setting()
		{
			if ( isset( $_POST['smtp_generate_nonce'] ) && !wp_verify_nonce( sanitize_text_field( $_POST['smtp_generate_nonce'] ), 'save_smtp_setting' ) ) {
				wp_die( 'SMTP From is protected!!' );
			}
			$smtpData = array( 
				'smtp_email' => isset( $_POST['smtp_email'] ) ? sanitize_email( $_POST['smtp_email'] ) : '',
				'smtp_name' => isset( $_POST['smtp_name'] ) ? sanitize_text_field( $_POST['smtp_name'] ) : '',
				'smtp_host' => isset( $_POST['smtp_host'] ) ? sanitize_text_field( $_POST['smtp_host'] ) : '',
				'ecription_type' => isset( $_POST['ecription_type'] ) ? sanitize_text_field( $_POST['ecription_type'] ) : '',
				'smtp_port' => isset( $_POST['smtp_port'] ) ? sanitize_text_field( $_POST['smtp_port'] ) : '',
				'authentication_type' => isset( $_POST['authentication_type'] ) ? sanitize_text_field( $_POST['authentication_type'] ) : '',
				'smtp_user' => isset( $_POST['smtp_user'] ) ? sanitize_text_field( $_POST['smtp_user'] ) : '',
				'smtp_password' => isset( $_POST['smtp_password'] ) ? sanitize_text_field( $_POST['smtp_password'] ) : '',
			 );
			update_option( sanitize_key( 'ztsa_SMTP_Setting' ), array_map( 'sanitize_text_field', $smtpData ) );
			$link = wp_get_referer();
			$link = parse_url( $link );
			if ( isset( $link['query'] ) ) {
				$link = remove_query_arg( 'success', wp_get_referer(  ) );
				$page_url = $link . '&success=true';
			} else {
				$page_url = $link['path'] . '&success=true';
			}
			wp_safe_redirect( $page_url );
			exit;
		}

		/**
		 * Set SMTP setting value in phpmailer 
		 *
		 * @param $phpmailer phpmailer object
		 * @return void
		 */
		function ztsa_phpmailer_setting( $phpmailer )
		{

			$smtpData = get_option( sanitize_key( 'ztsa_SMTP_Setting' ) );
			$phpmailer->isSMTP(  );
			$phpmailer->Host = $smtpData['smtp_host'];
			$phpmailer->SMTPAuth = $smtpData['authentication_type'];
			$phpmailer->Port = $smtpData['smtp_port'];
			$phpmailer->Username = $smtpData['smtp_user'];
			$phpmailer->Password = $smtpData['smtp_password'];
			$phpmailer->SMTPSecure = $smtpData['ecription_type'];
			$phpmailer->From = $smtpData['smtp_email'];
			$phpmailer->FromName = $smtpData['smtp_name'];
		}

		/**
		 * Testing mail
		 *
		 * @return void
		 */
		function ztsa_test_mail_check()
		{
			if ( isset( $_REQUEST ) ) {
				$smtpData = get_option( sanitize_key( 'ztsa_SMTP_Setting' ) );

				$to = isset( $_REQUEST["testMailData"]['to'] ) ? sanitize_email( $_REQUEST["testMailData"]['to'] ) : '';
				$subject = isset( $_REQUEST["testMailData"]['subject'] ) ? sanitize_text_field( $_REQUEST["testMailData"]['subject'] ) : '';
				$message = isset( $_REQUEST["testMailData"]['msg_body'] ) ? wp_kses_post( $_REQUEST["testMailData"]['msg_body'] ) : '';
				$headers[] = 'From: ' . esc_attr( $smtpData['smtp_name'] ) . ' <' . esc_attr( $smtpData['smtp_email'] ) . '>';
				$sent = wp_mail( $to, $subject, $message, $headers );

				if ( $sent ) {
					echo "success";
				} else {
					echo "error";
				}
			}
			wp_die(  );
		}
		/**
		 * data saved in option table for notification setting 
		 *
		 * @return void
		 */
		function ztsa_notification_setting_tab()
		{
			if ( isset( $_REQUEST ) ) {
				if ( isset( $_POST['ztsa_notification_setting_tab'] ) && !wp_verify_nonce( sanitize_text_field( $_POST['ztsa_notification_setting_tab'] ), 'ztsa_notification_setting_tab' ) ) {
					wp_die( 'Notification tab setting is protected!!' );
				}

				$ztsa_ques_crtd_mail_to_owner = array( 
					'checkbox' => isset( $_POST['ztsa_ques_crtd_mail_to_owner_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_ques_crtd_mail_to_owner_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_ques_crtd_mail_to_owner_to'] ) ? sanitize_text_field( $_POST['ztsa_ques_crtd_mail_to_owner_to'] ) : '',
					'cc' => isset( $_POST['ztsa_ques_crtd_mail_to_owner_cc'] ) ? sanitize_text_field( $_POST['ztsa_ques_crtd_mail_to_owner_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_ques_crtd_mail_to_owner_subject'] ) ? sanitize_text_field( $_POST['ztsa_ques_crtd_mail_to_owner_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_ques_crtd_mail_to_owner_msg_header'] ) ? wpautop( wp_kses_post( $_POST['ztsa_ques_crtd_mail_to_owner_msg_header'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_ques_crtd_mail_to_owner_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_ques_crtd_mail_to_owner_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_ques_crtd_mail_to_owner_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_ques_crtd_mail_to_owner_msg_footer'] ) ) : ''
				 );
				$ztsa_ques_crtd_mail_to_admin = array( 
					'checkbox' => isset( $_POST['ztsa_ques_crtd_mail_to_admin_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_ques_crtd_mail_to_admin_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_ques_crtd_mail_to_admin_to'] ) ? sanitize_text_field( $_POST['ztsa_ques_crtd_mail_to_admin_to'] ) : '',
					'cc' => isset( $_POST['ztsa_ques_crtd_mail_to_admin_cc'] ) ? sanitize_text_field( $_POST['ztsa_ques_crtd_mail_to_admin_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_ques_crtd_mail_to_admin_subject'] ) ? sanitize_text_field( $_POST['ztsa_ques_crtd_mail_to_admin_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_ques_crtd_mail_to_admin_msg_header'] ) ? wpautop( wp_kses_post( $_POST['ztsa_ques_crtd_mail_to_admin_msg_header'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_ques_crtd_mail_to_admin_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_ques_crtd_mail_to_admin_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_ques_crtd_mail_to_admin_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_ques_crtd_mail_to_admin_msg_footer'] ) ) : ''
				 );
				$ztsa_form_mail_to_tenant = array( 
					'checkbox' => isset( $_POST['ztsa_form_mail_to_tenant_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_form_mail_to_tenant_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_form_mail_to_tenant_to'] ) ? sanitize_text_field( $_POST['ztsa_form_mail_to_tenant_to'] ) : '',
					'cc' => isset( $_POST['ztsa_form_mail_to_tenant_cc'] ) ? sanitize_text_field( $_POST['ztsa_form_mail_to_tenant_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_form_mail_to_tenant_subject'] ) ? sanitize_text_field( $_POST['ztsa_form_mail_to_tenant_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_form_mail_to_tenant_msg_heading'] ) ? wpautop( wp_kses_post( $_POST['ztsa_form_mail_to_tenant_msg_heading'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_form_mail_to_tenant_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_form_mail_to_tenant_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_form_mail_to_tenant_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_form_mail_to_tenant_msg_footer'] ) ) : ''
				 );
				$ztsa_form_Detail_mailed_to_owner = array( 
					'checkbox' => isset( $_POST['ztsa_form_Detail_mailed_to_owner_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_form_Detail_mailed_to_owner_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_form_Detail_mailed_to_owner_to'] ) ? sanitize_text_field( $_POST['ztsa_form_Detail_mailed_to_owner_to'] ) : '',
					'cc' => isset( $_POST['ztsa_form_Detail_mailed_to_owner_cc'] ) ? sanitize_text_field( $_POST['ztsa_form_Detail_mailed_to_owner_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_form_Detail_mailed_to_owner_subject'] ) ? sanitize_text_field( $_POST['ztsa_form_Detail_mailed_to_owner_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_form_Detail_mailed_to_owner_msg_header'] ) ? wpautop( wp_kses_post( $_POST['ztsa_form_Detail_mailed_to_owner_msg_header'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_form_Detail_mailed_to_owner_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_form_Detail_mailed_to_owner_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_form_Detail_mailed_to_owner_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_form_Detail_mailed_to_owner_msg_footer'] ) ) : ''
				 );
				$ztsa_agmt_acpt_mail_tenant = array( 
					'checkbox' => isset( $_POST['ztsa_agmt_acpt_mail_tenant_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_tenant_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_agmt_acpt_mail_tenant_to'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_tenant_to'] ) : '',
					'cc' => isset( $_POST['ztsa_agmt_acpt_mail_tenant_cc'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_tenant_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_agmt_acpt_mail_tenant_subject'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_tenant_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_agmt_acpt_mail_tenant_msg_header'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_acpt_mail_tenant_msg_header'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_agmt_acpt_mail_tenant_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_acpt_mail_tenant_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_agmt_acpt_mail_tenant_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_acpt_mail_tenant_msg_footer'] ) ) : ''
				 );
				$ztsa_agmt_acpt_mail_multi_tenant = array( 
					'checkbox' => isset( $_POST['ztsa_agmt_acpt_mail_multi_tenant_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_multi_tenant_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_agmt_acpt_mail_multi_tenant_to'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_multi_tenant_to'] ) : '',
					'cc' => isset( $_POST['ztsa_agmt_acpt_mail_multi_tenant_cc'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_multi_tenant_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_agmt_acpt_mail_multi_tenant_subject'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_multi_tenant_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_agmt_acpt_mail_multi_tenant_msg_header'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_acpt_mail_multi_tenant_msg_header'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_agmt_acpt_mail_multi_tenant_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_acpt_mail_multi_tenant_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_agmt_acpt_mail_multi_tenant_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_acpt_mail_multi_tenant_msg_footer'] ) ) : ''
				 );
				$ztsa_rejection_mail_tenant = array( 
					'checkbox' => isset( $_POST['ztsa_rejection_mail_tenant_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_rejection_mail_tenant_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_rejection_mail_tenant_to'] ) ? sanitize_text_field( $_POST['ztsa_rejection_mail_tenant_to'] ) : '',
					'cc' => isset( $_POST['ztsa_rejection_mail_tenant_cc'] ) ? sanitize_text_field( $_POST['ztsa_rejection_mail_tenant_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_rejection_mail_tenant_subject'] ) ? sanitize_text_field( $_POST['ztsa_rejection_mail_tenant_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_rejection_mail_tenant_msg_header'] ) ? wpautop( wp_kses_post( $_POST['ztsa_rejection_mail_tenant_msg_header'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_rejection_mail_tenant_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_rejection_mail_tenant_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_rejection_mail_tenant_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_rejection_mail_tenant_msg_footer'] ) ) : ''
				 );
				$ztsa_agmt_acpt_mail_owner = array( 
					'checkbox' => isset( $_POST['ztsa_agmt_acpt_mail_owner_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_owner_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_agmt_acpt_mail_owner_to'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_owner_to'] ) : '',
					'cc' => isset( $_POST['ztsa_agmt_acpt_mail_owner_cc'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_owner_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_agmt_acpt_mail_owner_subject'] ) ? sanitize_text_field( $_POST['ztsa_agmt_acpt_mail_owner_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_agmt_acpt_mail_owner_msg_header'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_acpt_mail_owner_msg_header'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_agmt_acpt_mail_owner_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_acpt_mail_owner_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_agmt_acpt_mail_owner_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_acpt_mail_owner_msg_footer'] ) ) : ''
				 );
				$ztsa_agmt_rej_mail_owner = array( 
					'checkbox' => isset( $_POST['ztsa_agmt_rej_mail_owner_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_agmt_rej_mail_owner_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_agmt_rej_mail_owner_to'] ) ? sanitize_text_field( $_POST['ztsa_agmt_rej_mail_owner_to'] ) : '',
					'cc' => isset( $_POST['ztsa_agmt_rej_mail_owner_cc'] ) ? sanitize_text_field( $_POST['ztsa_agmt_rej_mail_owner_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_agmt_rej_mail_owner_subject'] ) ? sanitize_text_field( $_POST['ztsa_agmt_rej_mail_owner_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_agmt_rej_mail_owner_msg_header'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_rej_mail_owner_msg_header'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_agmt_rej_mail_owner_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_rej_mail_owner_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_agmt_rej_mail_owner_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_agmt_rej_mail_owner_msg_footer'] ) ) : ''
				 );
				$ztsa_final_agreement = array( 
					'checkbox' => isset( $_POST['ztsa_final_agreement_checkbox'] ) ? sanitize_text_field( $_POST['ztsa_final_agreement_checkbox'] ) : '',
					'to' => isset( $_POST['ztsa_final_agreement_to'] ) ? sanitize_text_field( $_POST['ztsa_final_agreement_to'] ) : '',
					'cc' => isset( $_POST['ztsa_final_agreement_cc'] ) ? sanitize_text_field( $_POST['ztsa_final_agreement_cc'] ) : '',
					'subject' => isset( $_POST['ztsa_final_agreement_subject'] ) ? sanitize_text_field( $_POST['ztsa_final_agreement_subject'] ) : '',
					'msg_header' => isset( $_POST['ztsa_final_agreement_msg_header'] ) ? wpautop( wp_kses_post( $_POST['ztsa_final_agreement_msg_header'] ) ) : '',
					'msg_body' => isset( $_POST['ztsa_final_agreement_msg_body'] ) ? wpautop( wp_kses_post( $_POST['ztsa_final_agreement_msg_body'] ) ) : '',
					'msg_footer' => isset( $_POST['ztsa_final_agreement_msg_footer'] ) ? wpautop( wp_kses_post( $_POST['ztsa_final_agreement_msg_footer'] ) ) : ''
				 );
				update_option( sanitize_key( 'ztsa_ques_crtd_mail_to_owner' ), array_map( 'wp_kses_post', $ztsa_ques_crtd_mail_to_owner ) );
				update_option( sanitize_key( 'ztsa_ques_crtd_mail_to_admin' ), array_map( 'wp_kses_post', $ztsa_ques_crtd_mail_to_admin ) );
				update_option( sanitize_key( 'ztsa_form_mail_to_tenant' ), array_map( 'wp_kses_post', $ztsa_form_mail_to_tenant ) );
				update_option( sanitize_key( 'ztsa_form_Detail_mailed_to_owner' ), array_map( 'wp_kses_post', $ztsa_form_Detail_mailed_to_owner ) );
				update_option( sanitize_key( 'ztsa_agmt_acpt_mail_tenant' ), array_map( 'wp_kses_post', $ztsa_agmt_acpt_mail_tenant ) );
				update_option( sanitize_key( 'ztsa_agmt_acpt_mail_multi_tenant' ), array_map( 'wp_kses_post', $ztsa_agmt_acpt_mail_multi_tenant ) );
				update_option( sanitize_key( 'ztsa_rejection_mail_tenant' ), array_map( 'wp_kses_post', $ztsa_rejection_mail_tenant ) );
				update_option( sanitize_key( 'ztsa_agmt_acpt_mail_owner' ), array_map( 'wp_kses_post', $ztsa_agmt_acpt_mail_owner ) );
				update_option( sanitize_key( 'ztsa_agmt_rej_mail_owner' ), array_map( 'wp_kses_post', $ztsa_agmt_rej_mail_owner ) );
				update_option( sanitize_key( 'ztsa_final_agreement' ), array_map( 'wp_kses_post', $ztsa_final_agreement ) );

				$link = wp_get_referer();
				$link = parse_url( $link );
				if ( isset( $link['query'] ) ) {
					$link = remove_query_arg( 'success', wp_get_referer(  ) );
					$page_url = $link . '&success=true';
				} else {
					$page_url = $link['path'] . '&success=true';
				}
				wp_safe_redirect( $page_url );
				exit;
			}
		}
	}
}
new ZTSA_Setting();
