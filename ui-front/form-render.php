<?php
/**
 * Contract form render Page.
 *
 * @package smart-agreements\ui-front
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$long_discription_enable = get_post_meta(sanitize_text_field($post_id), sanitize_key('long_descr_enable'), true);
$short_discription_enable = get_post_meta(sanitize_text_field($post_id), sanitize_key('short_descr_enable'), true);
$short_descrip = wpautop(get_post_meta(sanitize_text_field($post_id), sanitize_key('short_descr'), true));
$post = get_post(sanitize_text_field($post_id));
$long_descrip = wpautop($post->post_content);
?>
<div class="form-render">
    <?php
    if ($long_discription_enable == 'on') { ?>
        <div class="long_discr_div"><?php echo wp_kses_post($long_descrip); ?></div><hr>
    <?php }
    if ($short_discription_enable == 'on') { ?>
        <div class="long_discr_div"><?php echo wp_kses_post($short_descrip); ?></div><hr>
    <?php }  ?>
    <form id="form-render-<?php echo esc_attr($post_id); ?>"
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
        <div id="generated_by_builder_<?php echo esc_attr($post_id); ?>"> </div>
        <input type="hidden" name="action" value="save_ques_request">
        <input type="hidden" name="form_id" value="<?php echo esc_attr($post_id); ?>">
        <input type="hidden" name="post_title" value="<?php echo esc_attr($post_title); ?>">
        <?php wp_nonce_field("save_ques_request", "ques_form_handler_$post_id"); ?>
        <div id="add_extra_customer_<?php echo esc_attr($post_id); ?>">
            <label for="check_extra_customer">
                <?php esc_html_e('Please check if you are more than one customers', "smart-agreements"); ?>
            </label>
            <input type="checkbox" name="check_extra_customer"
                id="check_extra_customer_<?php echo esc_attr($post_id); ?>">
            <div id="add_customer_div_<?php echo esc_attr($post_id); ?>"></div>
            <div id="button_add_remove_<?php echo esc_attr($post_id); ?>" style="display:none">
                <input type="button" class="ztsa_add_new" id="ztsa_add_new_<?php echo esc_attr($post_id); ?>"
                    value="Add New">
            </div>
        </div>
        <div style="display:flex">
            <button class="btn btn-primary" id="submit_<?php echo esc_attr($post_id) ?>"
                onclick="set_message_id(<?php echo esc_attr($post_id); ?>)"
                name="submit_<?php echo esc_attr($post_id); ?>" type="submit"><?php esc_html_e('Submit Request', "smart-agreements"); ?></button>
            <div class="ztsa_message" id='ztsa_message_<?php echo esc_attr($post_id) ?>'></div>
            <?php
            ?>
        </div>
    </form>
</div>

<script>
    var count = 0;
    jQuery(function ($) {
        var fbRender = document.getElementById("generated_by_builder_<?php echo esc_attr($post_id); ?>");
        $('#form-render-<?php echo esc_attr($post_id); ?>').validate();
        formData = '<?php echo wp_kses_post($formData); ?>';
        var formRenderOpts = {
            formData,
            dataType: "json"
        };
        $(fbRender).formRender(formRenderOpts);
    });
    jQuery("#check_extra_customer_<?php echo esc_attr($post_id); ?>").click(function () {
        if (jQuery(this).is(":checked")) {
            count = 0;
            count++;
            var newTextBoxDiv = jQuery(document.createElement('div'))
                .attr("id", 'add_customer_<?php echo esc_attr($post_id); ?>_' + count);
            newTextBoxDiv.after().html('<div style="width:48%"> <label for="ztsa_customer_name_' + count + '"class="formbuilder-text-label">Customer Name' + count + '<span class="error">*</span></label><br><input type="text" class="ztc-form-control" name="ztsa_customer_name_' + count + '" id="ztsa_customer_name_' + count + '" required area-required="true"><br></div>' +
                '<div style="width:48%"> <label for="ztsa_customer_email_' + count + '"class="formbuilder-text-label">Customer Email' + count + '<span class="error">*</span></label><br><input type="email" class="ztc-form-control" name="ztsa_customer_email_' + count + '" id="ztsa_customer_email_' + count + '" required area-required="true"><br></div></div>' +
                '<div><br><input type="button" id=' + count + ' class="ztsa_delete" onclick="ztsa_delete_div(this,this.id)" value="Delete"></div>');
            newTextBoxDiv.appendTo("#add_customer_div_<?php echo esc_attr($post_id); ?>");
            jQuery('#add_customer_<?php echo esc_attr($post_id); ?>_' + count).css("display", "flex");
            jQuery("#button_add_remove_<?php echo esc_attr($post_id); ?>").css("display", "block");
            jQuery("#check_extra_customer_<?php echo esc_attr($post_id); ?>").val(count);
        }
    });
    jQuery("#check_extra_customer_<?php echo esc_attr($post_id); ?>").click(function () {
        if (!jQuery(this).is(":checked")) {
            count = 0;
            jQuery('#add_customer_div_<?php echo esc_attr($post_id); ?>').empty();
            jQuery("#button_add_remove_<?php echo esc_attr($post_id); ?>").css("display", "none");
            jQuery("#check_extra_customer_<?php echo esc_attr($post_id); ?>").val(count);

        }
    });

    jQuery("#ztsa_add_new_<?php echo esc_attr($post_id); ?>").click(function () {
        count = document.getElementById("add_customer_div_<?php echo esc_attr($post_id); ?>").children.length;
        count++;
        var newTextBoxDiv = jQuery(document.createElement('div'))
            .attr("id", 'add_customer_<?php echo esc_attr($post_id); ?>_' + count);
        newTextBoxDiv.after().html('<div style="width:48%"> <label for="ztsa_customer_name_' + count + '"class="formbuilder-text-label">Customer Name' + count + '<span class="error">*</span></label><br><input type="text" class="ztc-form-control" name="ztsa_customer_name_' + count + '" id="ztsa_customer_name_' + count + '" required area-required="true"><br></div>' +
            '<div style="width:48%"> <label for="ztsa_customer_email_' + count + '"class="formbuilder-text-label">Customer Email' + count + '<span class="error">*</span></label><br><input type="email" class="ztc-form-control" name="ztsa_customer_email_' + count + '" id="ztsa_customer_email_' + count + '" required area-required="true"><br></div></div>' +
            '<div><br><input type="button" id=' + count + ' class="ztsa_delete" onclick="ztsa_delete_div(this,this.id)" value="Delete"></div>');
        newTextBoxDiv.appendTo("#add_customer_div_<?php echo esc_attr($post_id); ?>");
        jQuery('#add_customer_<?php echo esc_attr($post_id); ?>_' + count).css("display", "flex");
        jQuery("#check_extra_customer_<?php echo esc_attr($post_id); ?>").val(count);
    });

    function ztsa_delete_div($this, $this_id) {
        count = $this_id;
        let nextSibling = $this.parentNode.parentNode.nextSibling;
        while (nextSibling) {
            nextSibling.setAttribute("id", 'add_customer_<?php echo esc_attr($post_id); ?>_' + count);
            nextSibling.children[0].children[0].setAttribute("for", 'ztsa_customer_name_' + count);
            nextSibling.children[0].children[0].innerHTML = 'Customer Name' + count + "<span class='error'>*</span>";
            nextSibling.children[0].children[2].setAttribute("name", "ztsa_customer_name_" + count);
            nextSibling.children[0].children[2].setAttribute("id", "ztsa_customer_name_" + count);
            nextSibling.children[1].children[0].setAttribute("for", 'ztsa_customer_email_' + count);
            nextSibling.children[1].children[0].innerHTML = 'Customer Email' + count + "<span class='error'>*</span>";
            nextSibling.children[1].children[2].setAttribute("name", "ztsa_customer_email_" + count);
            nextSibling.children[1].children[2].setAttribute("id", "ztsa_customer_email_" + count);
            nextSibling.children[2].children[1].setAttribute("id", count);
            nextSibling = nextSibling.nextSibling;
            ++count;
        }
        $this.parentNode.parentNode.remove();
        let numb = document.getElementById("add_customer_div_<?php echo esc_attr($post_id); ?>").children.length;

        if (count > numb) {
            count--;
            jQuery("#check_extra_customer_<?php echo esc_attr($post_id); ?>").val(count);
        }
        if (numb == 0) {
            count = 0;
        }

    }

    searchParams = new URLSearchParams(window.location.search);
    get_success_from_url = searchParams.get('success');
    get_form_id_from_url = searchParams.get('form_id');
    if (get_success_from_url == true) {
        jQuery("#ztsa_message_" + get_form_id_from_url).html('Form Submitted.');
        jQuery("#ztsa_message_" + get_form_id_from_url).css('display', 'block');
        jQuery("#ztsa_message_" + get_form_id_from_url).delay(3500).fadeOut(200);
    } else {
        jQuery("#ztsa_message_" + get_form_id_from_url).html('Form not Submitted! Please submit after sometime.');
        jQuery("#ztsa_message_" + get_form_id_from_url).css('color', 'red');
        jQuery("#ztsa_message_" + get_form_id_from_url).css('display', 'block');
        jQuery("#ztsa_message_" + get_form_id_from_url).delay(3500).fadeOut(200);
    }
    jQuery(document).ready(function () {
        var url = window.location.href;
        url = url.split('?')[0];
        window.history.pushState('object', document.title, url);
    });
</script>