<?PHP

/**
 * Entry Page.
 *
 * @package smart-agreements\ui-admin
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$customerinfo = new ZTSA_Entries_table();
?>
<h1><?php echo esc_html(get_admin_page_title()) ?></h1>
<?php $customerinfo->ztsa_prepare_items();
?>
<div class="alignleft actions bulkactions">
	<?php
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'ztsa_customer_info';
	$form_id = $wpdb->get_results("SELECT DISTINCT form_id,form_title FROM $table_name ");
	$get_url_form_id = isset($_GET['form_id']) ? sanitize_text_field($_GET['form_id']) : 0;
	?>
	<label for="form_title"><?php esc_html_e('Filter by Form Title & Id:', "smart-agreements"); ?></label>
	<select id="form_title" class="ztc-filter-id">
		<option><?php esc_html_e('Select form Title [Id]', "smart-agreements"); ?></option>
		<?php
		foreach ($form_id as $key => $value) {
			$selected = '';
			if ($get_url_form_id == $form_id[$key]->form_id) {

				$selected = ' selected = "selected"';
			}
		?>
			<option value="<?php echo esc_attr($form_id[$key]->form_id); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($form_id[$key]->form_title); ?> [<?php echo esc_html($form_id[$key]->form_id); ?>]</option>
		<?php
		} ?>
	</select>
	<script>
		jQuery(".ztc-filter-id").on("change", function() {
			var $form_id = jQuery(this).val();
			if ($form_id != "Select form Title [Id]") {
				var redirectUrl = 'edit.php?post_type=<?php echo esc_attr(ZTSA_POST_TYPE_SLUG); ?>&page=<?php echo esc_attr(ZTSA_ENTRIES_PAGE_SLUG); ?>&form_id=' + $form_id;
				window.location.href = redirectUrl;
			} else {
				var redirectUrl = 'edit.php?post_type=<?php echo esc_attr(ZTSA_POST_TYPE_SLUG); ?>&page=<?php echo esc_attr(ZTSA_ENTRIES_PAGE_SLUG); ?>';
				window.location.href = redirectUrl;
			}
		});
	</script>
</div>
<?php
$customerinfo->display();
?>
<div id='myModal' class='modal'>
	<div class='modal-content'>
		<span class='close'>&times;</span>
		<div id='show-entries-full'></div>
		<form method="post" id="ztsa_owner_response_form" action="">
			<?php wp_nonce_field('ztsa_owner_response_to_customer', 'ztsa_owner_response_to_customer'); ?>
			<input type="hidden" name="action" value="ztsa_owner_response_to_customer">
			<input type="hidden" name="ztsa_form_detail_link" id="ztsa_form_detail_link">
			<input type="hidden" name="ztsa_entry_id" id="ztsa_entry_id">
			<textarea name="ztsa_form_detail_owner_comment" id="ztsa_form_detail_owner_comment" rows="4" cols="80" placeholder="Write comment here"></textarea>
			<div style="display:flex">
				<button id="ztsa_form_detail_accept_by_owner" name="form_detail_accept_by_owner"><?php esc_html_e('ACCEPT', "smart-agreements"); ?></button>
				<button id="ztsa_form_detail_reject_by_owner" name="form_detail_reject_by_owner"><?php esc_html_e('REJECT', "smart-agreements"); ?></button>
			</div>
		</form>
	</div>
</div>
<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
	<?php wp_nonce_field('ztsa_owner_sign_template', 'ztsa_owner_sign_template'); ?>
	<input type="hidden" name="action" value="ztsa_owner_sign_template">
	<input type="hidden" name="ztsa_owner_sign_id" id="ztsa_owner_sign_id">
	<button id='ztsa_show_agreement_to_owner' name='ztsa_show_agreement_to_owner'><?php esc_html_e('SHOW AGREEMENT', "smart-agreements"); ?></button>
</form>
<script>
	jQuery(document).ready(function() {
		jQuery("body").on("click", ".show-d", function(e) {
			e.preventDefault();
			var id = jQuery(this).attr("data-id");
			var wp_nonce = jQuery(this).attr("data-nonce");
			jQuery.ajax({
				url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
				dataType: "json",
				data: {
					id: id,
					wp_nonce: wp_nonce,
					action: "show_entries_full"
				}
			}).done(function(res) {
				jQuery(".modal").css("display", "block");
				jQuery("#show-entries-full").html(res.entries_html);
				var form_url = jQuery("#ztsa_owner_response_form").attr("action");
				jQuery("#ztsa_entry_id").val(id);
				var encode_id = window.btoa(id);
				var sub_string = "==";
				if (encode_id.includes(sub_string, encode_id.length - 2)) {
					encode_id = encode_id.substring(0, encode_id.length - 2);
				}

				if (res.hide_owner_response_form === true) {
					jQuery("#ztsa_owner_response_form").hide();
				} else {
					jQuery("#ztsa_owner_response_form").show();
				}
				var agreement_link = "<?php echo esc_url_raw(admin_url('admin-post.php?action=ztsa_owner_response_to_customer&entry_id=')); ?>" + encode_id;
				jQuery("#ztsa_owner_response_form").attr("action", agreement_link);
				jQuery("#ztsa_form_detail_link").val(agreement_link);

			});

		});
		jQuery("body").on("click", "span", function() {
			jQuery(".modal").css("display", "none");

		});
		jQuery("body").on("click", ".show_agreement", function() {
			var id = jQuery(this).attr("customer-id")
			jQuery("#ztsa_owner_sign_id").val(id);
			jQuery('#ztsa_show_agreement_to_owner').trigger('click');
		});
		jQuery("#ztsa_show_agreement_to_owner").hide();
	});
</script>