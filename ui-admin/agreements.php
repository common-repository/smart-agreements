<?php

/**
 * Admin agreement Page.
 *
 * @package smart-agreements\ui-admin
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $plugin_page;

$screen = get_current_screen();
wp_enqueue_script('postbox');

add_meta_box(
	'ztsa_agreement_header_id',
	__("Agreement's Header", "smart-agreements"),
	'ztsa_agreement_header',
	esc_attr($screen->id),
	'normal'
);
add_meta_box(
	'ztsa_agreement_body_id',
	__("Agreement's Body", "smart-agreements"),
	'ztsa_agreement_body',
	esc_attr($screen->id),
	'normal'
);
add_meta_box(
	'ztsa_agreement_footer_id',
	__("Agreement's Footer", "smart-agreements"),
	'ztsa_agreement_footer',
	esc_attr($screen->id),
	'normal'
);

add_meta_box(
	'ztsa_Agreement_shortcode_id',
	__('Agreement list of Shortcodes', "smart-agreements"),
	'ztsa_agreement_shortcode',
	esc_attr($screen->id),
	'side'
);
add_meta_box(
	'ztsa_Agreement_template_id',
	__('Choose Template', "smart-agreements"),
	'ztsa_agreement_template',
	esc_attr($screen->id),
	'side'
);
?>


<div class="wrap">
	<h1><?php esc_html_e('Agreement form', "smart-agreements"); ?></h1>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">

			<div id="postbox-container-1" class="postbox-container">

				<?php do_meta_boxes('', 'side', null); ?>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<?php do_meta_boxes('', 'normal', null); ?>
				<?php do_meta_boxes('', 'advanced', null); ?>
			</div>

		</div>
	</div>
	</form>
</div>

<script>
	jQuery(function($) {
		postboxes.add_postbox_toggles(pagenow);
	});
</script>

<?php
/**
 * Responsible for agreement header
 *  
 * @return void
 */
function ztsa_agreement_header()
{
	$post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : '';
	$logo = get_post_meta($post_id, sanitize_key('ztsa_agreement_logo'), true);
?>
	<div class="wrap">
		<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field('ztsa_agreement_form', 'ztsa_agreement_form'); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<div style="width: fit-content;margin-top: 33px;"><Label><b><?php esc_html_e('LOGO:', "smart-agreements"); ?></b></Label></div>
						<div style='margin-left:8%;margin-top:-40px'><img id="imgPreview" src="<?php echo esc_url($logo); ?>" alt="Logo Preview" height="70px"></div>
					</tr>
					<tr>
						<input type="hidden" name="action" value="ztsa_agreement_form"><br>
						<input type="hidden" name="post_id" id="post_id" value="<?php echo  esc_attr($post_id); ?>"><br>
						<label for="ztsa_agreement_logo"><b><?php esc_html_e('Choose the logo:', "smart-agreements"); ?></b></label>(upload png, jpeg file)
						<input type="file" name="ztsa_agreement_logo" id="ztsa_agreement_logo" accept="image/jpeg, image/png">
					</tr>

					<tr>
						<label for="logo_alignment"><b><?php esc_html_e('Choose the Alignment of logo:', "smart-agreements"); ?></b></label>
						<select name="logo_alignment" id='logo_alignment'>
							<option value="left"><?php esc_html_e('Left', "smart-agreements"); ?></option>
							<option value="right"><?php esc_html_e('Right', "smart-agreements"); ?></option>
							<!-- <option value="middle">Centre</option> -->
						</select>
					</tr>
					<tr>
						<!-- <td style="width:100%;"> -->
						<input type="hidden" name="action" value="ztsa_agreement_form"><br>
						<input type="hidden" name="post_id" id="post_id" value="<?php echo  esc_attr($post_id); ?>"><br>
						<?php
						$settings = array('editor_height' => 30, 'textarea_rows' => 1, 'media_buttons' => FALSE);
						$post      = get_post($post_id, OBJECT, 'edit');
						$content   = html_entity_decode($post->ztsa_agreement_header);
						$editor_id = 'ztsa_agreement_header';
						// wp_editor($content, $editor_id, $settings);
						$args = array(
							'textarea_name' => 'ztsa_agreement_header',
							'media_buttons' => false,
							'quicktags' => false,
							'editor_height' => 30,
							'textarea_rows' => 5,
							'tinymce'       => array(
								'toolbar1'      => 'forecolor,bold,italic,paragraph,underline,alignleft,aligncenter,alignright,undo,redo',
								'toolbar2' => '',
							),
						);
						wp_editor($content, $editor_id, $args);
						?>
					</tr>
					<tr>
						<th>
							<?php
							$other_attributes = array('id' => 'submit');
							submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
							?>
						</th>
					</tr>
				</tbody>
			</table>
	</div>
<?php
}

/**
 * Responsible for agreement body
 *  
 * @return void
 */
function ztsa_agreement_body()
{
	$post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : '';
?>
	<div class="wrap">
		<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
			<?php wp_nonce_field('ztsa_agreement_form', 'ztsa_agreement_form'); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<input type="hidden" name="action" value="ztsa_agreement_form"><br>
						<?php
						$settings = array('editor_height' => 500, 'textarea_rows' => 400);
						$post = get_post($post_id, OBJECT, 'edit');
						$content   = html_entity_decode(wp_kses_post($post->ztsa_agreement_body));
						$editor_id = 'ztsa_agreement_body';
						wp_editor($content, $editor_id, $settings)
						?>
					</tr>
					<tr>
						<th>
							<?php
							$other_attributes = array('id' => 'submit');
							submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
							?>
						</th>
					</tr>
				</tbody>
			</table>
	</div>
<?php
}

/**
 * Responsible for agreement body
 *  
 * @return void
 */
function ztsa_agreement_footer()
{
	$post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : '';
?>
	<div class="wrap">
		<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
			<?php wp_nonce_field('ztsa_agreement_form', 'ztsa_agreement_form'); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<input type="hidden" name="action" value="ztsa_agreement_form"><br>
						<?php
						$args = array(
							'textarea_name' => 'ztsa_agreement_footer',
							'media_buttons' => false,
							'quicktags' => false,
							'editor_height' => 30,
							'textarea_rows' => 5,
							'tinymce'       => array(
								'toolbar1'      => 'forecolor,bold,italic,paragraph,underline,alignleft,aligncenter,alignright,undo,redo',
								'toolbar2' => '',
							),
						);
						$post      = get_post($post_id, OBJECT, 'edit');
						$content   = html_entity_decode(wp_kses_post($post->ztsa_agreement_footer));
						$editor_id = 'ztsa_agreement_footer';
						wp_editor($content, $editor_id,  $args);
						?>
					</tr>
					<tr>
						<th>
							<?php
							$other_attributes = array('id' => 'submit');
							submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
							?>
						</th>
					</tr>
				</tbody>
			</table>
	</div>
	<?php
}

/**
 * Responsible for agreement shortcode
 *  
 * @return void
 */
function ztsa_agreement_shortcode()
{
	$post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : '';
	$label = [];
	$formData = json_decode(get_post_meta($post_id, 'formData', true));
	$logo = get_post_meta($post_id, sanitize_key('ztsa_agreement_logo'), true);
	$logo_alignment = get_post_meta($post_id, sanitize_key('ztsa_agreement_logo_alignment'), true);

	if (isset($formData)) {
		foreach ($formData as $labels) {
			if (isset($labels->name) && isset($labels->label)) {
				$label[$labels->name] = $labels->label;
			}
		} ?>
		<span style='font-size:16px' onClick='selectText(this)'>[Owner Name]</span><br>
		<span style='font-size:16px' onClick='selectText(this)'>[Additional Users Name]</span><br>
		<span style='font-size:16px' onClick='selectText(this)'>[Additional Users Email]</span><br>
		<?php foreach ($label as $key => $value) {
			if (isset($value)) { ?>
				<span style='font-size:16px' onClick='selectText(this)'>["<?php echo esc_html($value) ?> "]</span><br>
		<?php }
		} ?>
		<span style='font-size:16px' onClick='selectText(this)'>[Owner Signature]</span><br>
		<span style='font-size:16px' onClick='selectText(this)'>[Customer Signature]</span><br>
		<span style='font-size:16px' onClick='selectText(this)'>[Additional Customer Signature]</span><br>
	<?php }
	?>
	<div id='myModal' class='modal'>

		<div id='show-preview-modal' class='modal-content'>
			<span class='close'>&times;</span>
			<div id='show-preview'>
				<div id='header_preview'>
					<?php
					if ($logo_alignment == 'right') { ?>
						<div id='header_text'></div>
						<div id='header_logo'></div>
					<?php } else { ?>
						<div id='header_logo'></div>
						<div id='header_text'></div>
					<?php }  ?>
				</div>
				<hr>
				<div id='body_preview'></div>
				<hr>
				<div id='footer_preview'></div>
			</div>
		</div>
	</div>
	<div style="display:flex; margin-top:15px;">
		<button id="ztsa_preview" style="margin-right:20px"><?php esc_html_e('Show Preview', "smart-agreements"); ?></button>
		<button id="ztsa_generate_pdf"> <a href="<?php echo esc_url(admin_url('admin-post.php?action=show_pdf&post_id=' . $post_id . '')) ?>" target="_blank"><?php esc_html_e('Generate PDF', "smart-agreements"); ?></a></button>

	</div>
	<script>
		jQuery(document).ready(function($) {
			$('#ztsa_preview').click(function() {
				var id = <?php echo esc_attr($post_id) ?>;
				jQuery("#show-preview p:first-child").find('#agrmnt_logo').remove();
				var ajaxurl = "<?php echo esc_url(admin_url('admin-ajax.php')) ?>";
				jQuery.ajax({
					url: ajaxurl,
					method: 'post',
					data: {
						id: id,
						action: "show_preview"
					},
					success: function(data) {
						var response = JSON.parse(data);
						jQuery("#myModal").css("display", "block");
						jQuery("#wp-ztsa_agreement_header-wrap").css("z-index", "0");
						jQuery("#wp-ztsa_agreement_body-wrap").css("z-index", "0");
						jQuery("#wp-ztsa_agreement_footer-wrap").css("z-index", "0");
						var logo = '<?php echo esc_url($logo); ?>';
						var logo_align = '<?php echo esc_attr($logo_alignment) ?>';
						var logo_img = '<img src="' + logo + '" align="' + logo_align + '" height="45"></div>';
						if (logo == "") {
							jQuery('#header_logo').css('width', '0%');
							jQuery('#header_text').css('width', '100%');
						}
						// jQuery("#show-preview p:fi").html(text + logo_img);
						jQuery('#header_text').html(response['header']);
						jQuery('#header_logo').html(logo_img);
						jQuery('#body_preview').html(response['body']);
						jQuery('#footer_preview').html(response['footer']);

					},
				});

				jQuery("body").on("click", "span", function() {
					jQuery("#myModal").css("display", "none");
					jQuery("#wp-ztsa_agreement_header-wrap").css("z-index", "1");
					jQuery("#wp-ztsa_agreement_body-wrap").css("z-index", "1");
					jQuery("#wp-ztsa_agreement_footer-wrap").css("z-index", "1");
				});
			});
		});
	</script>
<?php
}

/**
 * Responsible for agreement template
 *  
 * @return void
 */
function ztsa_agreement_template()
{
	echo "<input type='button' id='rental_agrmt' value='ROOM RENTAL AGREEMENT'>";
?>
	<div id='mytemplateModal' class='modal'>

		<div class='modal-content'>
			<span class='close'>&times;</span>
			<div id='show-template'>
				<?php
				require_once ZTSA_UI_ADMIN_DIR . 'agreement-template.php';
				echo wp_kses_post($room_agrmnt_template_header);
				echo "<hr>";
				echo wp_kses_post($room_agrmnt_template);
				// echo $select_room_argrmt;
				?>
			</div>
		</div>
	</div>
	<script>
		jQuery('#rental_agrmt').click(function() {
			jQuery("#mytemplateModal").css("display", "block");
			jQuery("#wp-ztsa_agreement_header-wrap").css("z-index", "0");
			jQuery("#wp-ztsa_agreement_body-wrap").css("z-index", "0");
			jQuery("#wp-ztsa_agreement_footer-wrap").css("z-index", "0");
		});
		jQuery("body").on("click", "span", function() {
			jQuery("#mytemplateModal").css("display", "none");
			jQuery("#wp-ztsa_agreement_header-wrap").css("z-index", "1");
			jQuery("#wp-ztsa_agreement_body-wrap").css("z-index", "1");
			jQuery("#wp-ztsa_agreement_footer-wrap").css("z-index", "1");
		});
	</script>
<?php
}
?>

<script>
	/*   function select_template($this_id) {
    var template_header_data = jQuery('#' + $this_id).parent().siblings().html();
    var template_data = jQuery('#' + $this_id).parent().prev().html();
    jQuery("#template_header_data").val(template_header_data);
    jQuery("#template_data").val(template_data);
    jQuery("#save_agrmt_template").submit();
  } */

	jQuery(function() {
		jQuery('#select_room_agrmt').on('click', function(event) {
			var template_header_data = jQuery('#select_room_agrmt').parent().siblings().html();
			var template_data = jQuery('#select_room_agrmt').parent().prev().html();
			jQuery("#template_header_data").val(template_header_data);
			jQuery("#template_data").val(template_data);
			jQuery("#save_agrmt_template").submit();
		});
	});

	jQuery(function() {
		jQuery('#logo_alignment').on('change', function(event) {
			var select_option = jQuery('#logo_alignment').find(":selected").attr('value');
			localStorage.setItem('select_option', select_option);
		});
	});
	var select = localStorage.getItem('select_option');
	jQuery('#logo_alignment option[value=' + select + ']').attr('selected', 'selected');

	jQuery('#ztsa_agreement_logo').change(function() {
		const file = this.files[0];
		if (file) {
			let reader = new FileReader();
			reader.onload = function(event) {
				console.log(event.target.result);
				jQuery('#imgPreview').attr('src', event.target.result);
			}
			reader.readAsDataURL(file);
		}
	});
</script>