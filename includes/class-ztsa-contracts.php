<?php

/**
 * Contains action hooks and functions for Contracts.
 *
 * @class ZTSA_Contracts
 * @package smart-agreements\includes
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	die();
}

if (!class_exists('ZTSA_Contracts')) {
	/**
	 * ZTC Contract class.
	 */
	class ZTSA_Contracts
	{
		/**
		 * ZTSA Contracts class Constructor
		 */
		public function __construct()
		{
			add_action('admin_enqueue_scripts', array($this, 'ztsa_admin_css_and_js'));
			add_action('wp_enqueue_scripts', array($this, 'ztsa_frontend_css_and_js'));
			add_action('init', array($this, 'ztsa_post_type'));
			add_action('add_meta_boxes', array($this, 'ztsa_form_builder_meta_box'));
			add_action('save_post', array($this, 'ztsa_save_metabox_formData'), 10, 2);
			add_filter('manage_' . ZTSA_POST_TYPE_SLUG . '_posts_columns', array($this, 'ztsa_custom_column_name'));
			add_action('manage_' . ZTSA_POST_TYPE_SLUG . '_posts_custom_column', array($this, 'ztsa_custom_post_type_columns_value'), 10, 2);
			add_shortcode('ztsa_Contract_Form', array($this, 'ztsa_form_render'));
			add_action('admin_post_save_ques_request', array($this, 'ztsa_save_ques_request'));
			add_action('admin_post_nopriv_save_ques_request', array($this, 'ztsa_save_ques_request'));
			add_action('admin_post_ztsa_agreement_form', array($this, 'ztsa_agreement_form'));
			add_action('admin_post_save_template', array($this, 'ztsa_save_template'));
			add_action('wp_ajax_show_preview', array($this, 'ztsa_agreement_preview'));
		}

		/**
		 * Register post type for Contract Form
		 *
		 * @return void
		 */
		public function ztsa_post_type()
		{
			$labels = array(
				'name'          => __('Smart Agreements', 'smart-agreements'),
				'singular_name' => __('Smart Agreements', 'smart-agreements'),
				'all_items'     => __('All Forms', 'smart-agreements'),
				'add_new_item'  => __('Add New Contract Form', 'smart-agreements'),
				'add_new'       => __('Add New', 'smart-agreements'),
			);
			$args   = array(
				'public'             => true,
				'has_archive'        => true,
				'publicly_queryable' => false,
				'rewrite'            => array('slug' => ZTSA_POST_TYPE_SLUG), // my custom slug.
				'menu_position'      => 25,
				'menu_icon'          => 'dashicons-forms',
				'labels'             => $labels,
			);

			register_post_type(ZTSA_POST_TYPE_SLUG, $args);
			remove_post_type_support(ZTSA_POST_TYPE_SLUG, 'editor');
		}

		/**
		 * Enqueue all CSS and Script File on Admin Panel
		 *
		 * @return void
		 */
		public function ztsa_admin_css_and_js()
		{
			wp_register_style('ztsa_form_style', ZTSA_ASSETS_URL . 'css/style.css', false, 'all');
			wp_enqueue_style('ztsa_form_style');
			wp_enqueue_script('ztsa_form_builder_js', ZTSA_ASSETS_URL . 'js/form-builder.min.js', '', true, true);
			wp_enqueue_script('ztsa_form_render_js', ZTSA_ASSETS_URL . 'js/form-render.min.js', '', true, true);
			wp_enqueue_script('ztsa_custom_js', ZTSA_ASSETS_URL . 'js/custom.js', '', true, true);
		}

		/**
		 * Enqueue all CSS and Script File on Frontend.
		 *
		 * @return void
		 */
		public function ztsa_frontend_css_and_js()
		{
			wp_register_style('ztsa_form_style', ZTSA_ASSETS_URL . 'css/style.css', false, 'all');
			wp_enqueue_style('ztsa_form_style');
			wp_enqueue_script('ztsa_validate_js', ZTSA_ASSETS_URL . 'js/jquery.validate.min.js', array('jquery'), true, true);
			wp_enqueue_script('ztsa_form_render_js', ZTSA_ASSETS_URL . 'js/form-render.min.js', array('jquery'), true, true);
			wp_enqueue_script('ztsa_custom_js', ZTSA_ASSETS_URL . 'js/custom.js', array('jquery'), true, true);
		}

		/**
		 * Adding new column 'Shortcode' in default Post column
		 *
		 * @param array $columns table column array.
		 *
		 * @return $columns
		 */
		public function ztsa_custom_column_name($columns)
		{
			unset($columns['date']);
			$columns['form_entries']     = __('Entries', 'smart-agreements');
			$columns['form_shortcode']   = __('Shortcode', 'smart-agreements');
			$columns['create_agreement'] = __('Create Agreement', 'smart-agreements');
			$columns['date']             = __('Date', 'smart-agreements');

			return $columns;
		}

		/**
		 * Set value in column 'Shortcode'
		 *
		 * @param array   $column table column array.
		 * @param integer $post_id post id.
		 */
		public function ztsa_custom_post_type_columns_value($column, $post_id)
		{
			switch ($column) {
				case 'form_shortcode': ?>
					<code onClick="selectText( this );">[ztsa_Contract_Form id="<?php echo esc_attr(get_the_ID()); ?>" title="<?php echo esc_attr(get_the_title()); ?>"]</code>
				<?php break;
				case 'form_entries': ?>
					<a href="edit.php?post_type=<?php echo esc_attr(ZTSA_POST_TYPE_SLUG); ?>&page=<?php echo esc_attr(ZTSA_ENTRIES_PAGE_SLUG); ?>&form_id=<?php echo esc_attr(get_the_ID()); ?>"><?php esc_html_e('Show Entries', 'smart-agreements') ?></a>
				<?php break;
				case 'create_agreement': ?>
					<h4 id="agreement_link"><a href="<?php echo esc_url(admin_url('edit.php?post_type=' . ZTSA_POST_TYPE_SLUG . '&page=' . ZTSA_SETTING_PAGE_SLUG . '&ztsa_page=' . ZTSA_AGREEMENT_PAGE_SLUG . '&post_id=' . esc_attr(get_the_ID()))) ?>"><?php esc_html_e('Create/Update Agreement', 'smart-agreements') ?></a></h4>
			<?php }
		}

		/**
		 * Metabox html for Create Form
		 *
		 * @param object $post post array.
		 */
		public function ztsa_form_builder_meta_box_html($post)
		{
			$form_data = get_post_meta(sanitize_text_field($post->ID), 'formData', true);
			?>
			<div id="imp_note">
				<h4><?php esc_html_e('Important Note:', 'smart-agreements'); ?></h4>
				<p><?php esc_html_e("We will provide field for Name and Email. You don't need to add for it.", 'smart-agreements'); ?></p>
			</div>
			<div id="build-wrap"></div>
			<input type="hidden" name="formData" id="formData">
			<script>
				jQuery(function($) {

					var fbEditor = document.getElementById('build-wrap');
					options = {
						formData: '<?php echo wp_kses_post($form_data); ?>',
						dataType: 'json',
					};
					var formBuilder = $(fbEditor).formBuilder(options);
					document.getElementById('publish').addEventListener('click', function(e) {
						//e.preventDefault(  );                   
						var formdata = (formBuilder.actions.getData('json'));
						formdata = JSON.parse(formdata);
						var new_formdata = new Array();

						var user_name_field = {
							"type": "text",
							"required": true,
							"label": "Customer Name",
							"className": "form-control",
							"name": "ztsa_user_name",
							"access": false,
							"subtype": "text"
						};
						var user_email_field = {
							"type": "text",
							"required": true,
							"label": "Customer Email",
							"className": "form-control",
							"name": "ztsa_user_email",
							"access": false,
							"subtype": "email"
						};
						if (formdata.length == 0) {
							formdata.splice(0, 0, user_name_field);
							formdata.splice(1, 0, user_email_field);
						} else {
							for (var i = 0; i < formdata.length; i++) {
								console.log(formdata[i]['name'] + i);
								if (formdata[i].name === "ztsa_user_name" || formdata[i].name === "ztsa_user_email") {
									// var spliced = formdata.splice( i, 1 );
								} else {
									new_formdata.push(formdata[i]);

								}
							}

							if (new_formdata[0]['type'] == "header") {
								new_formdata.splice(1, 0, user_name_field);
								new_formdata.splice(2, 0, user_email_field);
							} else {
								new_formdata.splice(0, 0, user_name_field);
								new_formdata.splice(1, 0, user_email_field);
							}

						}
						console.log(formdata);
						if (new_formdata.length > 0) {
							formdata = JSON.stringify(new_formdata);
						} else {
							formdata = JSON.stringify(formdata);
						}

						document.getElementById("formData").setAttribute('value', formdata);
					});
				});
			</script>

			<?php
		}

		/**
		 * Metabox html for Shortcode
		 *
		 * @param object $post post array.
		 */
		public function ztsa_form_shortcode_meta_box_html($post)
		{
			if (isset($_GET['post'])) {
			?>
				<code id="form_builder_shortcode" class="wrap" onClick="selectText(  this  );">[ztsa_Contract_Form id="<?php echo esc_attr(get_the_ID()); ?>" title="<?php echo esc_attr(get_the_title($post)); ?>"]</code><br>
				<h3 id="agreement_link"><a href="<?php echo esc_url(admin_url('edit.php?post_type=' . ZTSA_POST_TYPE_SLUG . '&page=' . ZTSA_SETTING_PAGE_SLUG . '&ztsa_page=' . ZTSA_AGREEMENT_PAGE_SLUG . '&post_id=' . esc_attr(get_the_ID()))); ?>"><?php esc_html_e('Create/Update Agreement', 'smart-agreements'); ?></a></h3>
			<?php
			}
		}

		/**
		 * Add editor for Long Description.
		 *
		 * @param object $post post array.
		 */
		public function ztsa_long_description_meta_box_html($post)
		{
			$enable  = get_post_meta(sanitize_text_field($post->ID), 'long_descr_enable', true);
			$checked = ('on' === $enable) ? 'checked' : null;
			?>
			<div class="ztsa_long_discription"><?php esc_html_e('Show Long Discription in frontend.', 'smart-agreements'); ?>
				<input type="checkbox" name="long_discription" <?php echo esc_attr($checked); ?>>
			</div>
			<hr>

		<?php
			$post      = get_post(sanitize_text_field($post->ID), OBJECT, 'edit');
			$content   = wp_kses_post($post->post_content);
			$editor_id = 'post_content';
			wp_editor($content, $editor_id);
		}

		/**
		 * Add editor for Short Description.
		 *
		 * @param object $post post array.
		 */
		public function ztsa_short_description_meta_box_html($post)
		{
			$enable  = get_post_meta(sanitize_text_field($post->ID), sanitize_key('short_descr_enable'), true);
			$checked = ('on' === $enable) ? 'checked' : null;
		?>
			<div class="ztsa_long_discription"><?php esc_html_e('Show Short Discription in frontend.', 'smart-agreements'); ?>
				<input type="checkbox" name="short_discription" <?php echo esc_attr($checked); ?>>
			</div>
			<hr>

		<?php
			$post      = get_post(sanitize_text_field($post->ID), OBJECT, 'edit');
			$content   = html_entity_decode(wp_kses_post($post->short_descr));
			$editor_id = 'short_descr';
			wp_editor($content, $editor_id);
		}

		/**
		 * Add metaboxes which is related to contrats form
		 *
		 * @return void
		 */
		public function ztsa_form_builder_meta_box()
		{
			add_meta_box(
				'ztsa_form_builder_id',
				__('Create your Agreement Form', 'smart-agreements'),
				array($this, 'ztsa_form_builder_meta_box_html'),
				ZTSA_POST_TYPE_SLUG,
				'normal',
				'high'
			);
			add_meta_box(
				'ztsa_shortcode_id',
				__('Shortcode For Form', 'smart-agreements'),
				array($this, 'ztsa_form_shortcode_meta_box_html'),
				ZTSA_POST_TYPE_SLUG,
				'side',
				'high'
			);
			add_meta_box(
				'ztsa_long_description_id',
				__('Long Description', 'smart-agreements'),
				array($this, 'ztsa_long_description_meta_box_html'),
				ZTSA_POST_TYPE_SLUG,
				'normal'
			);
			add_meta_box(
				'ztsa_short_description_id',
				__('Short Description', 'smart-agreements'),
				array($this, 'ztsa_short_description_meta_box_html'),
				ZTSA_POST_TYPE_SLUG,
				'normal'
			);
		}

		/**
		 * Form Render in Frontend.
		 *
		 * @param array $atts shartcode attribute.
		 *
		 * @return render contact form
		 */
		public function ztsa_form_render($atts)
		{
			$value = shortcode_atts(
				array(
					'id'    => '',
					'title' => '',
				),
				$atts
			);
			if (!empty($value['id'])) {
				$post_id    = $value['id'];
				$post_title = $value['title'];
				$formData = get_post_meta(sanitize_text_field($post_id), 'formData', true);
				if (!empty($formData) && get_post_type(sanitize_text_field($post_id)) == ZTSA_POST_TYPE_SLUG) {
					ob_start();
					include(ZTSA_UI_FRONT_DIR . '/form-render.php');
					$res = ob_get_clean();
					return $res;
				} else {
					return "<div>" . esc_html_e('Form not exist.', "smart-agreements") . "</div>";
				}
			} else {
				return "<div>" . esc_html_e('Please give valid ID.', "smart-agreements") . "</div>";
			}
		}

		/**
		 * set contetn type for mail
		 *
		 * @return void
		 */
		function ztsa_set_content_type_for_mail()
		{
			return "text/html";
		}

		/**
		 *  Saving FormData and Short Description into postmeta table
		 *
		 * @param  $post_id
		 */
		function ztsa_save_metabox_formData($post_id)
		{

			if (isset($_POST['formData'])) {
				$smtpData = ""; 
				$formdata_exist = get_post_meta($post_id, 'formData', true);
				$formdata = isset($_POST['formData']) ? sanitize_text_field($_POST['formData']) : '';
				$author_id = get_post_field(sanitize_key('post_author'), sanitize_text_field($post_id));
				$author_name = get_the_author_meta(sanitize_key('display_name'), sanitize_text_field($author_id));
				$author_email = get_the_author_meta(sanitize_key('email'), sanitize_text_field($author_id));
				$form_name = get_the_title(sanitize_text_field($post_id));
				$admin_email = get_option(sanitize_key('admin_email'));
				$array = array(
					"Form_Id" => "$post_id", "Form_Title" => "$form_name",
					"Admin_Email" => "$admin_email", "Author_Name" => "$author_name", "Author_Email" => "$author_email"
				);

				$owner_mail_data = get_option(sanitize_key('ztsa_ques_crtd_mail_to_owner'));
				if (isset($owner_mail_data['checkbox']) && !empty($owner_mail_data['checkbox'])) {
					foreach ($array as $key => $value) {
						$owner_mail_data = str_replace("[$key]",  $value, $owner_mail_data);
					}
		
					$owner_mail_checkbox =  $owner_mail_data['checkbox'];
					$owner_mail_to = $owner_mail_data['to'];
					$owner_mail_subject = $owner_mail_data['subject'];
					$header = $owner_mail_data['msg_header'];
					$body = $owner_mail_data['msg_body'];
					$footer = $owner_mail_data['msg_footer'];
					ob_start();
					include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
					$owner_mail_message = ob_get_contents();
					ob_end_clean();
					$owner_mail_headers[] = 'Content-Type: text/html; charset=UTF-8';
					$smtpData = get_option('ztsa_SMTP_Setting');
					if (is_array($smtpData)) {
						$owner_mail_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';
					}
					$owner_mail_headers[] = 'cc:' . esc_attr($owner_mail_data['cc']);
				}

				$admin_mail_data = get_option(sanitize_key('ztsa_ques_crtd_mail_to_admin'));
				if (isset($admin_mail_data['checkbox']) && !empty($admin_mail_data['checkbox'])) {
					foreach ($array as $key => $value) {
						$admin_mail_data = str_replace("[$key]", $value, $admin_mail_data);
					}
				
					$admin_mail_checkbox =  $admin_mail_data['checkbox'];
					$admin_mail_to = $admin_mail_data['to'];
					$admin_mail_subject = $admin_mail_data['subject'];
					$header = $admin_mail_data['msg_header'];
					$body = $admin_mail_data['msg_body'];
					$footer = $admin_mail_data['msg_footer'];
					ob_start();
					include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
					$admin_mail_message = ob_get_contents();
					ob_end_clean();
					$admin_mail_headers[] = 'Content-Type: text/html; charset=UTF-8';
					$smtpData = get_option(sanitize_key('ztsa_SMTP_Setting'));
					if (is_array($smtpData)) {
						$admin_mail_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';
					}
					$admin_mail_headers[] = 'cc:' . esc_attr($admin_mail_data['cc']);
				}

				if (empty($formdata_exist) && is_array($smtpData)) {
					if ($admin_mail_checkbox == 'on' && $owner_mail_checkbox == 'on' && $admin_mail_to == $owner_mail_to) {
						$sent = wp_mail($admin_mail_to, $admin_mail_subject, $admin_mail_message, $admin_mail_headers);
						
					} elseif ($admin_mail_checkbox == 'on' && $owner_mail_checkbox == 'on' && $admin_mail_to != $owner_mail_to) {
						$sent = wp_mail($owner_mail_to, $owner_mail_subject, $owner_mail_message, $owner_mail_headers);
						$sentd = wp_mail($admin_mail_to, $admin_mail_subject, $admin_mail_message, $admin_mail_headers);
						
					} elseif ($admin_mail_checkbox == 'on' && $owner_mail_checkbox == null) {
						$sent = wp_mail($admin_mail_to, $admin_mail_subject, $admin_mail_message, $admin_mail_headers);
						
					} elseif ($admin_mail_checkbox == null && $owner_mail_checkbox == 'on') {
						$sent = wp_mail($owner_mail_to, $owner_mail_subject, $owner_mail_message, $owner_mail_headers);
						
					}

				}
				update_post_meta($post_id, 'formData', sanitize_text_field($formdata));
			}
			$long_discription = isset($_POST['long_discription']) ? wp_kses_post($_POST['long_discription']) : 'off';
			update_post_meta($post_id, sanitize_key('long_descr_enable'),  wp_kses_post($long_discription));
			$short_discription = isset($_POST['short_discription']) ? wp_kses_post($_POST['short_discription']) : 'off';
			update_post_meta($post_id, sanitize_key('short_descr_enable'),  wp_kses_post($short_discription));
			if (isset($_POST['short_descr'])) {
				$short_descr = wp_kses_post($_POST['short_descr']);
				update_post_meta(sanitize_text_field($post_id), sanitize_key('short_descr'), wp_kses_post($_POST['short_descr']));
			}
		}
		function ztsa_agreement_form()
		{
			if (isset($_REQUEST)) {
				if (!wp_verify_nonce(sanitize_text_field($_POST['ztsa_agreement_form']), 'ztsa_agreement_form')) {
					wp_die('Agreement form is protected!!');
				}
				$post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : '';
				// we have sanitized file url rather then $_FILES['ztsa_agreement_logo']
				$logo_alignment = isset($_POST['logo_alignment']) ? sanitize_text_field($_POST['logo_alignment']) : '';
				define('ALLOW_UNFILTERED_UPLOADS', true);
				if ($_FILES['ztsa_agreement_logo']['size'] > 0) {
					if (!function_exists('wp_handle_upload')) {
						require_once(ABSPATH . 'wp-admin/includes/file.php');
					}
					$upload_overrides = array('test_form' => false);
					$movefile = wp_handle_upload($_FILES['ztsa_agreement_logo'], $upload_overrides);
					update_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_logo'), sanitize_url($movefile['url']));
				}



				$ztsa_agreement_header = !empty($_POST['ztsa_agreement_header']) ? wpautop(wp_kses_post($_POST['ztsa_agreement_header'])) : '';
				$ztsa_agreement_body = wpautop(wp_kses_post($_POST['ztsa_agreement_body']));
				$ztsa_agreement_footer = wpautop(wp_kses_post($_POST['ztsa_agreement_footer']));

				update_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_logo_alignment'), sanitize_text_field($logo_alignment));
				update_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_header'), wp_kses_post($ztsa_agreement_header));
				update_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_body'), wp_kses_post($ztsa_agreement_body));
				update_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_footer'), wp_kses_post($ztsa_agreement_footer));
				wp_safe_redirect(wp_get_referer());
				exit;
			}
		}

		/**
		 * Save Question Request Details in backend
		 *
		 * @return void
		 */
		function ztsa_save_ques_request()
		{
			$form_id = isset($_POST['form_id']) ? sanitize_text_field($_POST['form_id']) : '';
			if (!wp_verify_nonce(sanitize_text_field($_POST["ques_form_handler_{$form_id}"]), 'save_ques_request')) {
				wp_safe_redirect(wp_get_referer());
				exit;
			}
			$extra_user_request_data = [];
			$check_extra_customer = isset($_POST['check_extra_customer']) ? sanitize_text_field($_POST['check_extra_customer']) : 0;
			$user_request_data = [];
			$label = [];
			$formData = json_decode(get_post_meta(sanitize_text_field($_POST['form_id']), 'formData', true));
			foreach ($formData as $labels) {
				if ($labels->type == 'hidden') {
					$label[$labels->name] = 'Hidden';
				} else {
					$label[$labels->name] = $labels->label;
				}
			}
			$extra_customer_data = array_map('sanitize_text_field', $_POST);
			foreach ($extra_customer_data as $form_name => $value) {
				if ($form_name == 'action') {
					break;
				} else if ($form_name == "check_extra_customer") {
					continue;
				} else {
					$user_request_data[$form_name] = array(
						'labels' => isset($label[$form_name]) ? $label[$form_name] : 0,
						'values' => $value
					);
				}
			}

			do_action('ztsa_questionnaires_request');
			global $wpdb;
			$table_name = $wpdb->prefix . 'ztsa_customer_info';

			$sql = $wpdb->prepare("INSERT INTO " . $table_name . " ( form_title,form_id, customer_info  ) VALUES (  %s,%d, %s )", sanitize_text_field($_POST['post_title']), sanitize_text_field($_POST['form_id']), sanitize_text_field(json_encode($user_request_data)));
			$sql_success = $wpdb->query($sql);
			$last_entry_id = $wpdb->insert_id;
			for ($i = 1; $i <= $check_extra_customer; $i++) {
				$db_sql = $wpdb->prepare("INSERT INTO  " . $wpdb->prefix . "ztsa_extra_customer_info(  `customer_name`, `customer_email`, `entry_id`, `form_id` ) VALUES (  %s,%s,%d,%d )", sanitize_text_field($_POST["ztsa_customer_name_$i"]), sanitize_text_field($_POST["ztsa_customer_email_$i"]), $last_entry_id, sanitize_text_field($_POST['form_id']));
				$wpdb->query($db_sql);
			}
			$post_id = isset($_POST['form_id']) ? sanitize_text_field($_POST['form_id']) : '';
			$author_id = get_post_field(sanitize_key('post_author'), sanitize_text_field($post_id));
			$author_name = get_the_author_meta(sanitize_key('display_name'), sanitize_text_field($author_id));
			$author_email = get_the_author_meta(sanitize_key('email'), sanitize_text_field($author_id));
			$form_name = get_the_title(sanitize_text_field($post_id));
			$admin_email = get_option(sanitize_key('admin_email'));
			$array = array(
				"Form_Id" => "$post_id", "Form_Title" => "$form_name",
				"Admin_Email" => "$admin_email", "Author_Name" => "$author_name", "Author_Email" => "$author_email"
			);

			$form_detail_owner_mail_data = get_option(sanitize_key('ztsa_form_Detail_mailed_to_owner'));
			if (isset($form_detail_owner_mail_data['checkbox']) && !empty($form_detail_owner_mail_data['checkbox'])) {
				foreach ($array as $key => $value) {
					$form_detail_owner_mail_data = str_replace("[$key]",  $value, $form_detail_owner_mail_data);
				}
				$form_detail_owner_mail_checkbox =  $form_detail_owner_mail_data['checkbox'];
				$form_detail_owner_mail_to = $form_detail_owner_mail_data['to'];
				$form_detail_owner_mail_subject = $form_detail_owner_mail_data['subject'];
				$header = $form_detail_owner_mail_data['msg_header'];
				$body = $form_detail_owner_mail_data['msg_body'];
				$footer = $form_detail_owner_mail_data['msg_footer'];
				ob_start();
				include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
				$form_detail_owner_mail_message = ob_get_contents();
				ob_end_clean();
				$form_detail_owner_mail_headers[] = 'Content-Type: text/html; charset=UTF-8';
				$smtpData = get_option('ztsa_SMTP_Setting');
				if (is_array($smtpData)) {
					$form_detail_owner_mail_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';
				}
				$form_detail_owner_mail_headers[] = 'cc:' . esc_attr($form_detail_owner_mail_data['cc']);

				if ($form_detail_owner_mail_checkbox == 'on'  && is_array($smtpData)) {
					$sent = wp_mail($form_detail_owner_mail_to, $form_detail_owner_mail_subject, $form_detail_owner_mail_message, $form_detail_owner_mail_headers);
				}
			}


		

			$tenant_mail_data = get_option(sanitize_key('ztsa_form_mail_to_tenant'));
			if (isset($tenant_mail_data['checkbox']) && !empty($tenant_mail_data['checkbox'])) {

                    global $wpdb, $table_prefix;
					$table_name = $table_prefix . 'ztsa_customer_info';
					$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id='%d'", $last_entry_id), ARRAY_A);
					$customer_info = json_decode($results[0]['customer_info']);
					$customer_name = $customer_info->ztsa_user_name->values;
					$customer_email = $customer_info->ztsa_user_email->values;
					$post_id = $results[0]['form_id'];
					$author_id = get_post_field(sanitize_key('post_author'), sanitize_text_field($post_id));
					$author_name = get_the_author_meta(sanitize_key('display_name'), sanitize_text_field($author_id));
					$author_email = get_the_author_meta(sanitize_key('email'), sanitize_text_field($author_id));
					$form_name = get_the_title(sanitize_text_field($post_id));
					$admin_email = get_option(sanitize_key('admin_email'));

					$array1 = array(
						"Form_Id" => "$post_id", "Form_Title" => "$form_name",
						"Admin_Email" => "$admin_email", "Author_Name" => "$author_name", "Author_Email" => "$author_email",
						"Customer_Email" => " $customer_email", "Customer_Name" => " $customer_name"
					);

					$additional_user_details = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ztsa_extra_customer_info WHERE entry_id='%d'", $last_entry_id));


					$array2 = array();

					if (count($additional_user_details) > 0) {
						$Additional_customer_name = '';
						$Additional_customer_email = '';
						foreach ($additional_user_details as $key) {
							$Additional_customer_name .=  strval($key->customer_name);
							$Additional_customer_name .= ', ';
							$Additional_customer_email .=  strval($key->customer_email);
							$Additional_customer_email .= ', ';
						}
						$Additional_customer_email = rtrim($Additional_customer_email, ", ");
						$Additional_customer_name = rtrim($Additional_customer_name, ", ");
	
						
						$array2 = array(
							"Additional_Customer_Email" => "$Additional_customer_email", "Additional_Customer_Name" => "$Additional_customer_name"
						);
					
						
					}
					$array = $array1 + $array2;
				
	
				foreach ($array as $key => $value) {
					$tenant_mail_data = str_replace("[$key]", $value, $tenant_mail_data);
				}

				$tenant_mail_checkbox =  $tenant_mail_data['checkbox'];
				$tenant_mail_to = $tenant_mail_data['to'];
			
				$tenant_mail_subject = $tenant_mail_data['subject'];
				$header = $tenant_mail_data['msg_header'];
				$body = $tenant_mail_data['msg_body'];
				$footer = $tenant_mail_data['msg_footer'];
				ob_start();
				include(ZTSA_UI_ADMIN_DIR . '/email-template.php');
				$tenant_mail_message = ob_get_contents();
				ob_end_clean();

				$tenant_mail_headers[] = 'Content-Type: text/html; charset=UTF-8';
				$smtpData = get_option('ztsa_SMTP_Setting');
				if (is_array($smtpData)) {
					$tenant_mail_headers[] = 'From: ' . esc_attr($smtpData['smtp_name']) . ' <' . esc_attr($smtpData['smtp_email']) . '>';
				}
				$tenant_mail_headers[] = 'cc:' . esc_attr($tenant_mail_data['cc']);

				if ($tenant_mail_checkbox == 'on' && !empty($tenant_mail_to)) {
					$sent = wp_mail($tenant_mail_to, $tenant_mail_subject, $tenant_mail_message, $tenant_mail_headers);
					
				}
			}
		

            

			if ($sql_success) {
				$success = true;
			} else {
				$success = false;
			}
			$link = wp_get_referer();
			$link = wp_parse_url($link);
			if (isset($link['query'])) {
				$arr_params = array('form_id', 'success');
				$link = remove_query_arg($arr_params, wp_get_referer());
				$page_url = $link . '?form_id=' . $post_id . '&success=' . $success;
			} else {
				$page_url = wp_get_referer() . '?form_id=' . $post_id . '&success=' . $success;
			}

			wp_safe_redirect($page_url);
			exit();
		}

		/**
		 * agreement preview
		 *
		 * @return void
		 */
		function ztsa_agreement_preview()
		{
			$post_id =  isset($_REQUEST['id']) ? sanitize_text_field($_REQUEST['id']) : '';

			$header = get_post_meta($post_id, sanitize_key('ztsa_agreement_header'), true);
			$body = get_post_meta($post_id, sanitize_key('ztsa_agreement_body'), true);
			$footer = get_post_meta($post_id, sanitize_key('ztsa_agreement_footer'), true);
			echo $response = wp_json_encode(array("header" => wp_kses_post($header), "body" => wp_kses_post($body), "footer" => wp_kses_post($footer)));
		
			exit;
		}

		/**
		 * Save template
		 *
		 * @return void
		 */
		function ztsa_save_template()
		{
			if (!wp_verify_nonce(sanitize_text_field($_POST['ztsa_save_template']), 'ztsa_save_template')) {
				wp_die(__('This Page is Protected.', "smart-agreements"));
			}
			$template_header_data = wp_kses_post($_POST['template_header_data']);
			$template_data = wp_kses_post($_POST['template_data']);
			$post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : '';
			update_post_meta($post_id, sanitize_key('ztsa_agreement_header'), wp_kses_post($template_header_data));
			update_post_meta($post_id, sanitize_key('ztsa_agreement_body'), wp_kses_post($template_data));
			wp_safe_redirect(wp_get_referer());
			exit;
		}

		/**
		 * add attribute in wp editor
		 *
		 * @return void
		 */
		function add_required_attribute_to_wp_editor($editor)
		{
			$editor = str_replace('<textarea', '<textarea required="required"', $editor);
			return $editor;
		}
	}
}

new ZTSA_Contracts();
