<?php

/**
 * Contains action hooks and functions for contract entries.
 *
 * @class ZTSA_Entries
 * @package smart-agreements\includes
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('ZTSA_Entries')) {
	/**
	 * ZTC Entries class.
	 */
	class ZTSA_Entries
	{
		/**
		 * construct funcation of ZTSA_Entries to load hooks
		 */
		function __construct()
		{
			add_action('admin_menu', [$this, 'ztsa_entries_sub_menu']);
			add_action('wp_ajax_show_entries_full', [$this, 'ztsa_show_entries_full']);
			add_action('wp_ajax_nopriv_show_entries_full', [$this, 'ztsa_show_entries_full']);
			add_action('admin_post_ztsa_owner_response_to_customer', [$this, 'ztsa_owner_response_to_customer']);
			add_action('admin_post_nopriv_ztsa_owner_response_to_customer', [$this, 'ztsa_owner_response_to_customer']);
			add_action('admin_post_customer_response', [$this, 'ztsa_customer_response']);
			add_action('admin_post_nopriv_customer_response', [$this, 'ztsa_customer_response']);
			add_action('admin_post_ztsa_owner_signeture', [$this, 'ztsa_owner_signeture']);
			add_action('admin_post_ztsa_owner_sign_template', [$this, 'ztsa_owner_sign_template']);
		}

		/**
		 * Get contract entry details .
		 *
		 * @return void 
		 */
		function ztsa_show_entries_full()
		{
			if (!wp_verify_nonce(sanitize_text_field($_GET['wp_nonce']), 'ztsa_show_entry_details')) {
				die("WP nonce not verified");
			}
			global $wpdb, $table_prefix;
			$table_name = $table_prefix . 'ztsa_customer_info';
			$entry_id = isset($_GET['id']) ? intval(sanitize_text_field($_GET['id'])) : '';
			$customer_info_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id=%d", $entry_id));

			$user_info = json_decode($wpdb->get_var($wpdb->prepare("SELECT customer_info FROM $table_name WHERE id='%d';", $entry_id)));
			$addition_user_info = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ztsa_extra_customer_info WHERE entry_id='%d'", $entry_id));
			if (count($addition_user_info) > 0) {
				foreach ($addition_user_info as $count => $res) {
					foreach ($res as $key => $value) {
						if ($key == "customer_name") {
							$temp_additional_customer_details["ztsa_customer_name_$count"] = (object)array(
								'labels' => "Additional Customer Name " . ($count + 1),
								'values' => $value
							);
						} else if ($key == "customer_email") {
							$temp_additional_customer_details["ztsa_customer_email_$count"] =  (object)array(
								'labels' => "Additional Customer Email " . ($count + 1),
								'values' => $value
							);
						}
					}
				}
				$results = (object) array_merge($temp_additional_customer_details, (array) $user_info);
			} else {
				$results = $user_info;
			}

			$entries_html = "<table class='cust_info_table'>
                    <tbody><tr><th class='cust_info_th'>Key</th><th class='cust_info_th'>Value</th></tr>";
			$entries_html .= "<tr><td class='cust_info_th'><code>Form Title [ID]</code></td><td class='cust_info_th'><code>" . esc_attr($customer_info_data->form_title) . " [" . esc_attr($customer_info_data->form_id) . "]</code></td></tr>";
			foreach ($results as $value) {
				$entries_html .= "<tr>
                    <td class='cust_info_td'> " . esc_attr($value->labels) . "</td>";
				if (is_array($value->values)) {
					$entries_html .= "<td class='cust_info_td'>";
					$value = implode(', ', $value->values);
					$entries_html .= esc_attr($value);
					$entries_html .= " </td>";
				} else {
					$entries_html .= "<td class='cust_info_td'>" . esc_attr($value->values) . " </td>";
				}
			}
			$entries_html .= "</tr></tbody>
               </table>";
			$hide_owner_response_form = (!empty($customer_info_data->customer_sign) && !empty($customer_info_data->owner_sign)) ? true : false;
			echo wp_json_encode(array('entries_html' => wp_kses_post($entries_html), 'hide_owner_response_form' => sanitize_text_field($hide_owner_response_form)));
			wp_die();
		}

		/**
		 * Adding Submenu Page of Setting menu .
		 *
		 * @return void 
		 */
		function ztsa_entries_sub_menu_page()
		{

			require_once(ZTSA_UI_ADMIN_DIR . '/entries-page.php');
		}

		/**
		 * Adding Submenu for entries
		 *
		 * @return void
		 */
		function ztsa_entries_sub_menu()
		{
			$my_slugs = sanitize_key(ZTSA_ENTRIES_PAGE_SLUG);
			add_submenu_page(
				'edit.php?post_type=' . ZTSA_POST_TYPE_SLUG,
				__("Contract Form Entries", "smart-agreements"),
				'Entries',
				'manage_options',
				$my_slugs,
				[$this, 'ztsa_entries_sub_menu_page']
			);
		}

		/**
		 * owner response to multiple customer .
		 *
		 * @return void 
		 */
		function ztsa_owner_response_to_multiple_customer($entry_id)
		{
			global $wpdb, $table_prefix;
			$table_name = $table_prefix . 'ztsa_customer_info';
			$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id='%d'", sanitize_text_field($entry_id)), ARRAY_A);
			$customer_info = json_decode($results[0]['customer_info']);
			$customer_name = $customer_info->ztsa_user_name->values;
			$customer_email = $customer_info->ztsa_user_email->values;
			$post_id = $results[0]['form_id'];
			$author_id = get_post_field(sanitize_key('post_author'), sanitize_text_field($post_id));
			$author_name = get_the_author_meta(sanitize_key('display_name'), sanitize_text_field($author_id));
			$author_email = get_the_author_meta(sanitize_key('email'), sanitize_text_field($author_id));
			$form_name = !empty(get_the_title($post_id)) ? get_the_title(sanitize_text_field($post_id)) : '';
			$admin_email = get_option(sanitize_key('admin_email'));
			$acceptance_mail_to_multiple_tenant_data = get_option(sanitize_key('ztsa_agmt_acpt_mail_multi_tenant'));
			$array = array(
				"Form_Id" => "$post_id", "Form_Title" => "$form_name",
				"Admin_Email" => "$admin_email", "Author_Name" => "$author_name", "Author_Email" => "$author_email",
				"Customer_Email" => " $customer_email", "Customer_Name" => " $customer_name", "Author_Comment" => "",
			);
			foreach ($array as $key => $value) {
				$acceptance_mail_to_multiple_tenant_data = str_replace("[$key]",  $value, $acceptance_mail_to_multiple_tenant_data);
			}

			if (isset($acceptance_mail_to_multiple_tenant_data['checkbox']) && $acceptance_mail_to_multiple_tenant_data['checkbox'] == 'on') {
				$additional_user_details = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ztsa_extra_customer_info WHERE entry_id='%d'", sanitize_text_field($entry_id)));
				if (count($additional_user_details) > 0) {
					foreach ($additional_user_details as $value) {
						$Additional_customer_email = $value->customer_email;
						$Additional_customer_name =  $value->customer_name;
						$agreement_link = esc_url(admin_url("admin-post.php?action=ztsa_owner_response_to_customer&id=" . base64_encode("$value->id") . "&entry_id=" . base64_encode("$value->entry_id") . ""));
						$agreement_link_temp = "<a href='" . esc_url($agreement_link) . "'> " . esc_url($agreement_link) . "</a>";
						$addition_customer = array(
							"Additional_Customer_Email" => "$Additional_customer_email",
							"Additional_Customer_Name" => "$Additional_customer_name", "Agreement_Link" => "$agreement_link_temp"
						);
						$acceptance_mail_to_multiple_tenant_data_temp = $acceptance_mail_to_multiple_tenant_data;
						foreach ($addition_customer as $key => $value) {
							$acceptance_mail_to_multiple_tenant_data_temp = str_replace("[$key]",  $value, $acceptance_mail_to_multiple_tenant_data_temp);
						}

						$mail_subject = $acceptance_mail_to_multiple_tenant_data_temp['subject'];
						$header = $acceptance_mail_to_multiple_tenant_data_temp['msg_header'];
						$body = $acceptance_mail_to_multiple_tenant_data_temp['msg_body'];
						$footer = $acceptance_mail_to_multiple_tenant_data_temp['msg_footer'];
						ob_start();
						include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
						$mail_message = ob_get_contents();
						ob_end_clean();
						$mail_headers[] = 'Content-Type: text/html; charset=UTF-8';
						$smtpData = get_option(sanitize_key('ztsa_SMTP_Setting'));
						$mail_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';
						$mail_headers[] = 'cc:' .  $acceptance_mail_to_multiple_tenant_data_temp['cc'];
						wp_mail($Additional_customer_email, $mail_subject, $mail_message, $mail_headers);
					}
				}
			}
		}

		/**
		 * owner response to customer .
		 *
		 * @return void 
		 */
		function ztsa_owner_response_to_customer()
		{
			if (isset($_REQUEST)) {
				if (!isset($_POST['ztsa_owner_response_to_customer'])) {
					$customer_id = intval(base64_decode(sanitize_text_field($_GET['entry_id'])));
					if ($customer_id != 0) {
						global $wpdb, $table_prefix;
						$table_name = $table_prefix . 'ztsa_customer_info';
						$entry_id = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM $table_name WHERE id='%d'", $customer_id));
						if ($entry_id == $customer_id) {
							$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id='%d'", $customer_id), ARRAY_A);
							$post_id = $results[0]['form_id'];
							$customer_sign = $results[0]['customer_sign'];
							$customer_signeture_button = ' 
                                <textarea  name="ztsa_customer_comment" id = "ztsa_customer_comment" placeholder="Write comment here"></textarea> 
                                    <br>
                                <button class="btn btn-danger" id="ztsa_agrmnt_reject" name="ztsa_agrmnt_reject" >REJECT </button><button class="btn btn-success" id="ztsa_agrmnt_accept" name="ztsa_agrmnt_accept" disabled >ACCEPT </button>';

							include(ZTSA_UI_FRONT_DIR . 'agreement-page.php');
						} else {
							echo ("Entry id not Exist");
						}
					} else {
						echo ("Please give valid Entry id");
					}
				} else {
					if (!wp_verify_nonce(sanitize_text_field($_POST['ztsa_owner_response_to_customer']), 'ztsa_owner_response_to_customer')) {
						wp_die(__('This Page is Protected.'));
					}

					$customer_id = intval(base64_decode(sanitize_text_field($_GET['entry_id'])));
					$agreement_link = isset($_REQUEST['ztsa_form_detail_link']) ? sanitize_text_field($_REQUEST['ztsa_form_detail_link']) : '';
					$author_comment = wp_kses_post($_REQUEST['ztsa_form_detail_owner_comment']);
					global $wpdb, $table_prefix;
					$table_name = $table_prefix . 'ztsa_customer_info';
					$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id='%d'", $customer_id), ARRAY_A);
					$customer_info = json_decode($results[0]['customer_info']);
					$customer_name = $customer_info->ztsa_user_name->values;
					$customer_email = $customer_info->ztsa_user_email->values;
					$post_id = $results[0]['form_id'];
					$author_id = get_post_field(sanitize_key('post_author'), sanitize_text_field($post_id));
					$author_name = get_the_author_meta(sanitize_key('display_name'), sanitize_text_field($author_id));
					$author_email = get_the_author_meta(sanitize_key('email'), sanitize_text_field($author_id));
					$form_name = get_the_title(sanitize_text_field($post_id));
					$admin_email = get_option(sanitize_key('admin_email'));
					$array = array(
						"Form_Id" => "$post_id", "Form_Title" => "$form_name",
						"Admin_Email" => "$admin_email", "Author_Name" => "$author_name", "Author_Email" => "$author_email",
						"Customer_Email" => " $customer_email", "Customer_Name" => " $customer_name", "Author_Comment" => "$author_comment", "Agreement_Link" => $agreement_link
					);

					if (isset($_REQUEST['form_detail_accept_by_owner'])) {
						$acceptance_mail_to_tenant_data = get_option(sanitize_key('ztsa_agmt_acpt_mail_tenant'));

						$sql = $wpdb->prepare("UPDATE " . $wpdb->prefix . "ztsa_customer_info  SET owner_comment = %s,owner_status= %s WHERE id = %s", $author_comment, "accept", $customer_id);
						$wpdb->query($sql);

						if (isset($acceptance_mail_to_tenant_data['checkbox']) && !empty($acceptance_mail_to_tenant_data['checkbox'])) {
							foreach ($array as $key => $value) {
								$acceptance_mail_to_tenant_data = str_replace("[$key]",  $value, $acceptance_mail_to_tenant_data);
							}
							$acceptance_mail_to_tenant_checkbox =  $acceptance_mail_to_tenant_data['checkbox'];
							$acceptance_mail_to_tenant_to = $acceptance_mail_to_tenant_data['to'];
							$acceptance_mail_to_tenant_subject = $acceptance_mail_to_tenant_data['subject'];
							$header = $acceptance_mail_to_tenant_data['msg_header'];
							$body = $acceptance_mail_to_tenant_data['msg_body'];
							$footer = $acceptance_mail_to_tenant_data['msg_footer'];
							ob_start();
							include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
							$acceptance_mail_to_tenant_message = ob_get_contents();
							ob_end_clean();
							$acceptance_mail_to_tenant_headers[] = 'Content-Type: text/html; charset=UTF-8';
							$smtpData = get_option('ztsa_SMTP_Setting');
							if (is_array($smtpData)) {
								$acceptance_mail_to_tenant_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';
							}
							$acceptance_mail_to_tenant_headers[] = 'cc:' . esc_attr($acceptance_mail_to_tenant_data['cc']);

							if ($acceptance_mail_to_tenant_checkbox == 'on' && is_array($smtpData)) {
								$sent = wp_mail($acceptance_mail_to_tenant_to, $acceptance_mail_to_tenant_subject, $acceptance_mail_to_tenant_message, $acceptance_mail_to_tenant_headers);
							}
						}
						$this->ztsa_owner_response_to_multiple_customer($customer_id);
					}

					if (isset($_REQUEST['form_detail_reject_by_owner'])) {
						$rejection_mail_to_tenant_data = get_option(sanitize_key('ztsa_rejection_mail_tenant'));

						$sql = $wpdb->prepare("UPDATE " . $wpdb->prefix . "ztsa_customer_info  SET owner_comment = %s,owner_status= %s WHERE id = %s", $author_comment, "reject", $customer_id);
						$wpdb->query($sql);

						$additional_user_details = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ztsa_extra_customer_info WHERE entry_id='%d';", $customer_id));
						if (isset($rejection_mail_to_tenant_data['checkbox']) && !empty($rejection_mail_to_tenant_data['checkbox'])) {
							foreach ($array as $key => $value) {
								$rejection_mail_to_tenant_data = str_replace("[$key]",  $value, $rejection_mail_to_tenant_data);
							}

							if (count($additional_user_details) > 0) {
								foreach ($additional_user_details as $key) {
									$rejection_mail_to_tenant_headers[] = 'cc:' . $key->customer_email;
									$Additional_customer_name .=  strval($key->customer_name);
									$Additional_customer_name .= ', ';
									$Additional_customer_email .=  strval($key->customer_email);
									$Additional_customer_email .= ', ';
								}
								$rejection_mail_to_tenant_headers[] = 'cc:' . $rejection_mail_to_tenant_data['cc'];
								$Additional_customer_email = rtrim($Additional_customer_email, ", ");
								$Additional_customer_name = rtrim($Additional_customer_name, ", ");

								$addition_customer = array(
									"Additional_Customer_Email" => "$Additional_customer_email", "Additional_Customer_Name" => "$Additional_customer_name"
								);
								foreach ($addition_customer as $key => $value) {
									$rejection_mail_to_tenant_data = str_replace("[$key]",  $value,  $rejection_mail_to_tenant_data);
								}
							} else {
								$rejection_mail_to_tenant_headers[] = 'cc:' . $rejection_mail_to_tenant_data['cc'];
								$addition_customer = array(
									"Additional_Customer_Email" => " ", "Additional_Customer_Name" => " "
								);
								foreach ($addition_customer as $key => $value) {
									$rejection_mail_to_tenant_data = str_replace("[$key]",  $value,  $rejection_mail_to_tenant_data);
								}
							}
							$rejection_mail_to_tenant_checkbox =  $rejection_mail_to_tenant_data['checkbox'];
							$rejection_mail_to_tenant_to = $rejection_mail_to_tenant_data['to'];
							$rejection_mail_to_tenant_subject = $rejection_mail_to_tenant_data['subject'];
							$header = $rejection_mail_to_tenant_data['msg_header'];
							$body = $rejection_mail_to_tenant_data['msg_body'];
							$footer = $rejection_mail_to_tenant_data['msg_footer'];
							ob_start();
							include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
							$rejection_mail_to_tenant_message = ob_get_contents();
							ob_end_clean();
							$rejection_mail_to_tenant_headers[] = 'Content-Type: text/html; charset=UTF-8';
							$smtpData = get_option(sanitize_key('ztsa_SMTP_Setting'));
							if (is_array($smtpData)) {
								$rejection_mail_to_tenant_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';
							}

							if ($rejection_mail_to_tenant_checkbox == 'on') {
								$sent = wp_mail($rejection_mail_to_tenant_to, $rejection_mail_to_tenant_subject, $rejection_mail_to_tenant_message, $rejection_mail_to_tenant_headers);
							}
						}
					}
					wp_safe_redirect(wp_get_referer());
					exit;
				}
			}
		}

		/**
		 * customer response for contract singnature .
		 *
		 * @return void 
		 */
		function ztsa_customer_response()
		{
			if (!wp_verify_nonce(sanitize_text_field($_POST['customer_response']), 'customer_response')) {
				wp_die(__('This Page is Protected.'));
			}

			if (isset($_REQUEST)) {
				$customer_id = isset($_REQUEST['ztsa_customer_response_id']) ? sanitize_text_field($_REQUEST['ztsa_customer_response_id']) : '';
				$customer_comment = wp_kses_post($_REQUEST['ztsa_customer_comment']);
				$customer_sign_url = isset($_REQUEST['signature-customer']) ? sanitize_text_field($_REQUEST['signature-customer']) : '';
				$additional_customer_sign = isset($_POST['additional_customer_id']) ? sanitize_text_field($_POST['additional_customer_id']) : '';
				global $wpdb, $table_prefix;
				$table_name = $table_prefix . 'ztsa_customer_info';
				$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id='%d';", $customer_id), ARRAY_A);
				$customer_info = json_decode($results[0]['customer_info']);
				$customer_name = $customer_info->ztsa_user_name->values;
				$customer_email = $customer_info->ztsa_user_email->values;
				$post_id = $results[0]['form_id'];
				$author_id = get_post_field(sanitize_key('post_author'), sanitize_key($post_id));
				$author_name = get_the_author_meta(sanitize_key('display_name'), sanitize_key($author_id));
				$author_email = get_the_author_meta(sanitize_key('email'), sanitize_key($author_id));
				$form_name = get_the_title(sanitize_key($post_id));
				$admin_email = get_option(sanitize_key('admin_email'));
				$array = array(
					"Form_Id" => "$post_id", "Form_Title" => "$form_name",
					"Admin_Email" => "$admin_email", "Author_Name" => "$author_name", "Author_Email" => "$author_email",
					"Customer_Email" => " $customer_email", "Customer_Name" => " $customer_name",
					"Customer_Comment" => "$customer_comment"
				);

				if (isset($_REQUEST['ztsa_agrmnt_accept'])) {
					if ($_POST['additional_customer_id'] > 0) {
						$sql = $wpdb->prepare("UPDATE " . $table_prefix . "ztsa_extra_customer_info  SET customer_sign = %s,customer_comment = %s,customer_status= %s WHERE id = %s", $customer_sign_url, $customer_comment, "accept", sanitize_text_field($_POST['additional_customer_id']));
						$wpdb->query($sql);
					} else {
						$sql = $wpdb->prepare("UPDATE $table_name  SET customer_sign = %s,customer_comment = %s,customer_status= %s WHERE id = %s", $customer_sign_url, $customer_comment, "accept", $customer_id);
						$wpdb->query($sql);
					}
					$acceptance_mail_to_owner_data = get_option(sanitize_key('ztsa_agmt_acpt_mail_owner'));
					if (isset($acceptance_mail_to_owner_data['checkbox']) && !empty($acceptance_mail_to_owner_data['checkbox'])) {
						foreach ($array as $key => $value) {
							$acceptance_mail_to_owner_data = str_replace("[$key]",  $value, $acceptance_mail_to_owner_data);
						}
						$acceptance_mail_to_owner_checkbox =  $acceptance_mail_to_owner_data['checkbox'];
						$acceptance_mail_to_owner_to = $acceptance_mail_to_owner_data['to'];
						$acceptance_mail_to_owner_subject = sanitize_text_field($acceptance_mail_to_owner_data['subject']);
						$header = wp_kses_post($acceptance_mail_to_owner_data['msg_header']);
						$body = wp_kses_post($acceptance_mail_to_owner_data['msg_body']);
						$footer = wp_kses_post($acceptance_mail_to_owner_data['msg_footer']);
						ob_start();
						include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
						$acceptance_mail_to_owner_message = ob_get_contents();
						ob_end_clean();
						$acceptance_mail_to_owner_headers[] = 'Content-Type: text/html; charset=UTF-8';
						$smtpData = get_option(sanitize_key('ztsa_SMTP_Setting'));
						$acceptance_mail_to_owner_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';

						$acceptance_mail_to_owner_headers[] = 'cc:' . esc_attr($acceptance_mail_to_owner_data['cc']);
						if ($acceptance_mail_to_owner_checkbox == 'on') {
							$sent = wp_mail($acceptance_mail_to_owner_to, $acceptance_mail_to_owner_subject, $acceptance_mail_to_owner_message, $acceptance_mail_to_owner_headers);
						}
					}
				}

				if (isset($_REQUEST['ztsa_agrmnt_reject'])) {
					$rejection_mail_to_owner_data = get_option(sanitize_key('ztsa_agmt_rej_mail_owner'));

					$sql = $wpdb->prepare("UPDATE " . $wpdb->prefix . "ztsa_customer_info  SET customer_comment = %s,customer_status= %s WHERE id = %s", $customer_comment, "reject", $customer_id);
					$wpdb->query($sql);

					if (isset($rejection_mail_to_owner_data['checkbox']) && !empty($rejection_mail_to_owner_data['checkbox'])) {
						foreach ($array as $key => $value) {
							$rejection_mail_to_owner_data = str_replace("[$key]",  $value, $rejection_mail_to_owner_data);
						}
						$rejection_mail_to_owner_checkbox =  $rejection_mail_to_owner_data['checkbox'];
						$rejection_mail_to_owner_to = $rejection_mail_to_owner_data['to'];
						$rejection_mail_to_owner_subject = $rejection_mail_to_owner_data['subject'];
						$header =  wp_kses_post($rejection_mail_to_owner_data['msg_header']);
						$body =  wp_kses_post($rejection_mail_to_owner_data['msg_body']);
						$footer =  wp_kses_post($rejection_mail_to_owner_data['msg_footer']);
						ob_start();
						include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
						$rejection_mail_to_owner_message = ob_get_contents();
						ob_end_clean();
						$rejection_mail_to_owner_headers[] = 'Content-Type: text/html; charset=UTF-8';
						$smtpData = get_option(sanitize_key('ztsa_SMTP_Setting'));
						$rejection_mail_to_owner_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';
						$rejection_mail_to_owner_headers[] = 'cc:' . esc_attr($rejection_mail_to_owner_data['cc']);
						if ($rejection_mail_to_owner_checkbox == 'on') {
							$sent = wp_mail($rejection_mail_to_owner_to, $rejection_mail_to_owner_subject, $rejection_mail_to_owner_message, $rejection_mail_to_owner_headers);
						}
					}
				}
				wp_safe_redirect(wp_get_referer());
				exit;
			}
		}

		/**
		 * Owner response for contract singnature .
		 *
		 * @return void 
		 */
		function ztsa_owner_signeture()
		{
			if (!wp_verify_nonce(sanitize_text_field($_POST['ztsa_owner_signeture']), 'ztsa_owner_signeture')) {
				wp_die(__('This Page is Protected.'));
			}

			if (isset($_REQUEST['sig-submitBtn-owner'])) {
				$customer_id = isset($_REQUEST['ztsa_owner_signeture_id']) ? sanitize_text_field($_REQUEST['ztsa_owner_signeture_id']) : '';
				$owner_sign_url = isset($_REQUEST['signature-owner']) ? sanitize_text_field($_REQUEST['signature-owner']) : '';
				global $wpdb, $table_prefix;
				$table_name = $table_prefix . 'ztsa_customer_info';
				$sql = $wpdb->prepare("UPDATE $table_name  SET owner_sign = %s WHERE id = %s", $owner_sign_url, $customer_id);
				$wpdb->query($sql);

				require_once(ZTSA_PLUGIN_INCLUDES_DIR . 'class-ztsa-pdf-generator.php');
				$generate_pdf = new ZTSA_PDF_Generator();
				$link = $generate_pdf->ztsa_final_agreement_pdf($customer_id);
				$attachments = array($link);
				$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id='%d'", $customer_id), ARRAY_A);
				$customer_info = json_decode($results[0]['customer_info']);
				$customer_name = $customer_info->ztsa_user_name->values;
				$customer_email = $customer_info->ztsa_user_email->values;
				$post_id = $results[0]['form_id'];
				$author_id = get_post_field(sanitize_key('post_author'), sanitize_key($post_id));
				$author_name = get_the_author_meta(sanitize_key('display_name'), sanitize_key($author_id));
				$author_email = get_the_author_meta(sanitize_key('email'), sanitize_key($author_id));
				$form_name = get_the_title(sanitize_key($post_id));
				$admin_email = get_option(sanitize_key('admin_email'));
				$array = array(
					"Form_Id" => "$post_id", "Form_Title" => "$form_name",
					"Admin_Email" => "$admin_email", "Author_Name" => "$author_name", "Author_Email" => "$author_email",
					"Customer_Email" => " $customer_email", "Customer_Name" => " $customer_name",
				);
				$final_agreement_mail_data = get_option(sanitize_key('ztsa_final_agreement'));
				if (isset($final_agreement_mail_data['checkbox']) && !empty($final_agreement_mail_data['checkbox'])) {
					foreach ($array as $key => $value) {
						$final_agreement_mail_data = str_replace("[$key]",  $value, $final_agreement_mail_data);
					}
					$additional_user_details = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ztsa_extra_customer_info WHERE entry_id='%d'", $customer_id));
					if (count($additional_user_details) > 0) {
						$Additional_customer_name = '';
						$Additional_customer_email = '';
						foreach ($additional_user_details as $key) {
							$final_agreement_mail_headers[] = 'cc:' . $key->customer_email;
							$Additional_customer_name .=  strval($key->customer_name);
							$Additional_customer_name .= ', ';
							$Additional_customer_email .=  strval($key->customer_email);
							$Additional_customer_email .= ', ';
						}
						$final_agreement_mail_headers[] = 'cc:' . $final_agreement_mail_data['cc'];
						$Additional_customer_email = rtrim($Additional_customer_email, ", ");
						$Additional_customer_name = rtrim($Additional_customer_name, ", ");

						$addition_customer = array(
							"Additional_Customer_Email" => "$Additional_customer_email", "Additional_Customer_Name" => "$Additional_customer_name"
						);
						foreach ($addition_customer as $key => $value) {
							$final_agreement_mail_data = str_replace("[$key]",  $value,  $final_agreement_mail_data);
						}
					} else {
						$addition_customer = array(
							"Additional_Customer_Email" => " ", "Additional_Customer_Name" => " "
						);
						foreach ($addition_customer as $key => $value) {
							$final_agreement_mail_data = str_replace("[$key]",  $value,  $final_agreement_mail_data);
						}
						$final_agreement_mail_headers[] = 'cc:' . $final_agreement_mail_data['cc'];
					}

					$final_agreement_mail_checkbox =  $final_agreement_mail_data['checkbox'];
					$final_agreement_mail_to = $final_agreement_mail_data['to'];
					$final_agreement_mail_subject = $final_agreement_mail_data['subject'];
					$header = wp_kses_post($final_agreement_mail_data['msg_header']);
					$body = wp_kses_post($final_agreement_mail_data['msg_body']);
					$footer = wp_kses_post($final_agreement_mail_data['msg_footer']);
					ob_start();
					include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
					$final_agreement_mail_message = ob_get_contents();
					ob_end_clean();
					$final_agreement_mail_headers[] = 'Content-Type: text/html; charset=UTF-8';
					$smtpData = get_option(sanitize_key('ztsa_SMTP_Setting'));
					if (is_array($smtpData)) {
						$final_agreement_mail_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';
					}
					if ($final_agreement_mail_checkbox == 'on') {
						$sent = wp_mail($final_agreement_mail_to, $final_agreement_mail_subject, $final_agreement_mail_message, $final_agreement_mail_headers, $attachments);
					}
				}
			}
			wp_safe_redirect(admin_url('/edit.php?post_type=' . esc_attr(ZTSA_POST_TYPE_SLUG) . '&page=' . esc_attr(ZTSA_ENTRIES_PAGE_SLUG)));
			exit;
		}

		/**
		 * Owner singnature template .
		 *
		 * @return void 
		 */
		function ztsa_owner_sign_template()
		{
			if (!wp_verify_nonce(sanitize_text_field($_POST['ztsa_owner_sign_template']), 'ztsa_owner_sign_template')) {
				wp_die(__('This Page is Protected.'));
			}
			if (isset($_REQUEST)) {
				$customer_id = isset($_REQUEST['ztsa_owner_sign_id']) ? sanitize_text_field($_REQUEST['ztsa_owner_sign_id']) : '';
				$owner_signeture_button = '<button class="btn btn-success" id="sig-submitBtn-owner"  name="sig-submitBtn-owner" >Submit Signature owner</button>';
				include(ZTSA_UI_FRONT_DIR . 'agreement-page.php');
			}
		}
	}
	new ZTSA_Entries();
}
