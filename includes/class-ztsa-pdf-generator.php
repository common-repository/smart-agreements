<?php

/**
 * Contains action hooks and functions for generate pdf.
 *
 * @class ZTSA_PDF_Generator
 * @package smart-agreements\includes
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
// include dompdf autoload
require_once(ZTSA_PLUGIN_DIR . 'dompdf/autoload.inc.php');

use Dompdf\Dompdf;

/**
 * ZTC PDF Generator class.
 */
if (!class_exists('ZTSA_PDF_Generator')) {
	class ZTSA_PDF_Generator
	{
		/**
		 * construct funcation of ZTSA_PDF_Generator to load hooks
		 */
		function __construct()
		{
			add_action('admin_post_show_pdf', [$this, 'ztsa_show_pdf']);
		}

		/**
		 * Responsible for generate sample pdf of agreements.
		 *
		 * @return void 
		 */
		function ztsa_show_pdf()
		{
			$post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : '';
			$header = get_post_meta($post_id, sanitize_key('ztsa_agreement_header'), true);
			$body = get_post_meta($post_id, sanitize_key('ztsa_agreement_body'), true);
			$footer = get_post_meta($post_id, sanitize_key('ztsa_agreement_footer'), true);
			$src = get_post_meta($post_id, sanitize_key('ztsa_agreement_logo'), true);
			$alignment = get_post_meta($post_id, sanitize_key('ztsa_agreement_logo_alignment'), true);
			$logo = '<span style="float: ' . esc_attr($alignment) . '; width:fit-content; height:fit-content;" id="ztc-logo";"><img src="' . esc_url($src) . '"  height="45"></span>';
			$result = '
           <html>
               <head>
                 <style>
                    @page {
                    margin: 100px 30px;
                      }
   
                     header {
                     position: fixed;
                     top:-80px;
                     text-align: center;
                     width:100%;
                     font-size:20px;
                     font-weight:bold;
                     border-bottom:1.5px double;
                     padding-bottom:0px;
                     height:70px;
                     padding:0px;
                       }
                     footer {
                     position: fixed;
                     bottom: -60px;
                     width:100%;
                     text-align: center  
                    }
                    hr{
                        margin:5px 0px;
                    }
      
                  </style>
                </head>
            <body>
               <header>' . wp_kses_post($logo) . '' .
				wp_kses_post($header) . '
               </header>
   
                <footer><hr />' .
				wp_kses_post($footer) . '
                </footer>
   
                <main>'
				. wp_kses_post($body) . '
                </main>
            </body>
        </html>';

			$dompdf = new Dompdf();
			$options = new \Dompdf\Options();
			$options->set('isRemoteEnabled', true);
			$dompdf->setOptions($options);
			$dompdf->loadHtml($result);
			// $dompdf->loadHtml($html);
			$dompdf->setPaper('A4');
			$dompdf->render();
			$canvas = $dompdf->getcanvas();
			$font = $dompdf->getFontMetrics()->getfont("helvetica");
			$canvas->page_text(
				550,
				820,
				"{PAGE_NUM}/{PAGE_COUNT}",
				$font,
				10,
				array(0, 0, 0)
			);
			$dompdf->stream("ztc-agreement", array("Attachment" => 0));
			exit;
		}

		/**
		 * Responsible for generate final pdf of agreements.
		 *
		 * @return void 
		 */
		function ztsa_final_agreement_pdf($customer_id)
		{
			global $wpdb, $table_prefix;
			$table_name = $table_prefix . 'ztsa_customer_info';
			$customer_entry = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id='%d'", sanitize_text_field($customer_id)), ARRAY_A);
			$additional_user_details = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ztsa_extra_customer_info WHERE entry_id='%d'", sanitize_text_field($customer_id)), ARRAY_A);
			$customer_info = json_decode($customer_entry[0]['customer_info']);
			$customer_name = $customer_info->ztsa_user_name->values;
			$customer_email = $customer_info->ztsa_user_email->values;
			$post_id = $customer_entry[0]['form_id'];
			$author_id = get_post_field(sanitize_key('post_author'), sanitize_text_field($post_id));
			$author_name = get_the_author_meta(sanitize_key('display_name'), sanitize_text_field($author_id));
			$customer_sign = $customer_entry[0]['customer_sign'];
			$owner_sign = $customer_entry[0]['owner_sign'];
			$header = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_header'), true);
			$body = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_body'), true);
			$footer = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_footer'), true);
			$src = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_logo'), true);
			$alignment = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_logo_alignment'), true);
			$logo = '<span style="float: ' . esc_attr($alignment) . '; width:fit-content; height:fit-content;" id="ztc-logo";"><img src="' . esc_url($src) . '"  height="45"></span>';
			$customer_sign_div = '<p>Customer Signature:</p><div  style="width:100%;height:60px;border-bottom:1px solid;">
        <div style="font-weight: bold;font-size:20px;width:34%;padding-top:20px;">' . esc_attr($customer_name) . '</div>
        <div style="width:60%;margin-left:35%;margin-top:-55px;height:60px;"><img class="customer_sign"  id ="main_coustomer_sign" src="' . $customer_sign . '" width="250" height="60" alt="Customer Sign"></div>
        </div>';

			$owner_sign_div = '<p>Owner Signature:</p><div  style="width:100%;height:60px;border-bottom:1px solid;">
        <div style="font-weight: bold;font-size:20px;width:34%;padding-top:20px;">' . esc_attr($author_name) . '</div>
        <div style="width:60%;margin-left:35%;margin-top:-55px;height:60px;"><img class="customer_sign"  id ="main_coustomer_sign" src="' . $owner_sign . '" width="250" height="60" alt="Customer Sign"></div>
        </div>';
			$agreement_final = [$header, $body, $footer];
			foreach ($customer_info as $value) {
				if (is_array($value->values)) {
					$temp_value = implode(",", $value->values);
					$header = str_replace("[" . $value->labels . "]", $temp_value, $header);
					$body = str_replace("[" . $value->labels . "]", $temp_value, $body);
					$footer = str_replace("[" . $value->labels . "]", $temp_value, $footer);
				} else {
					$header = str_replace("[" . $value->labels . "]", $value->values, $header);
					$body = str_replace("[" . $value->labels . "]", $value->values, $body);
					$footer = str_replace("[" . $value->labels . "]", $value->values, $footer);
				}
			}
			if (count($additional_user_details) > 0) {
				foreach ($additional_user_details as $values) {

					$extra_user_name[] = $values['customer_name'];
					$extra_user_email[] = $values['customer_email'];
					$signature_feild[] = '<div  style="width:100%;height:60px;border-bottom:1px solid;">
                <div style="font-weight: bold;font-size:20px;width:34%;padding-top:20px;">' . esc_attr($values["customer_name"]) . '</div>
                <div style="width:60%;margin-left:35%;margin-top:-35px;height:60px;"><img class="customer_sign"  id ="main_coustomer_sign" src="' . $values["customer_sign"] . '" width="250" height="60" alt="Customer Sign"></div>
                </div>';
				}
				$extra_user_name = implode(", ", $extra_user_name);
				$extra_user_email = implode(", ", $extra_user_email);
				$signature_feild = implode("", $signature_feild);
				$signature_feild = "<p>Additional Customer Signature:</p>" . $signature_feild;
				$body = str_replace("[Additional Users Name]", $extra_user_name,   $body);
				$body = str_replace("[Additional Users Email]", $extra_user_email,   $body);
				$body = str_replace("[Additional Customer Signature]", $signature_feild,   $body);
			} else {
				$body = str_replace("[Additional Users Name]", "",   $body);
				$body = str_replace("[Additional Users Email]", "",   $body);
				$body = str_replace("[Additional Customer Signature]", "",   $body);
			}
			$header = str_replace("[Owner Name]",  $author_name, $header);
			$header = str_replace("[Logo]", $logo, $header);
			$body = str_replace("[Customer Signature]", $customer_sign_div, $body);
			$body = str_replace("[Owner Signature]", $owner_sign_div, $body);
			$body = str_replace("[Owner Name]", $author_name, $body);
			$footer = str_replace("[Customer Signature]", $customer_sign_div, $footer);
			$footer = str_replace("[Owner Signature]", $owner_sign_div, $footer);
			$footer = str_replace("[Owner Name]", $author_name, $footer);
			// when we are going to sanitize $body using wp_kses_post() then my funcationality was break down.
			$html = '
				<html>
					<head>
						<style>
							@page {
								margin: 100px 25px;
							}

							header {
								position: fixed;
								top: -60px;
								text-align: center;
								width:100%
								
							}
							footer {
								position: fixed;
								bottom: -60px;
								width:100%;
								text-align: center  
							}
						</style>
					</head>
					<body>
						<header>
						' . wp_kses_post($logo) . '' .
						wp_kses_post($header) . '
						<hr />
						</header>

						<footer>
							<hr />' .
						wp_kses_post($footer) . '
						</footer>

						<main>'
						. $body . '
						</main>
					</body>
				</html>';



			$filename = WP_CONTENT_DIR . '/uploads/ztsa_Agreement/ztsa-agreement-' . $customer_id . '.pdf';
			$dompdf = new Dompdf();
			$options = new \Dompdf\Options();
			$options->set('isRemoteEnabled', true);
			$dompdf->setOptions($options);
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4');
			$dompdf->render();
			file_put_contents($filename, $dompdf->output());
			return ($filename);
		}
	}
}
new ZTSA_PDF_Generator();
