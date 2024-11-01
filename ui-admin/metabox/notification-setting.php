<?php
/**
 * Add notification metabox and email notification form.
 *
 * @package smart-agreements\ui-admin\metabox
 * @version 1.0.0
 */

 if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

 wp_enqueue_script('postbox');
$screen = get_current_screen();

add_meta_box(
    'ztsa_ques_crtd_mail_to_owner_id',
    __('Setting for questionnaire created mail to owner', "smart-agreements"),
    'ztsa_questionnaire_created_mail_to_owner_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_ques_crtd_mail_to_admin_id',
    __('Setting for questionnaire created mail to admin', "smart-agreements"),
    'ztsa_questionnaire_created_mail_to_admin_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_form_mail_to_tenant_id',
    __('Mail setting for form mail to Tenant', "smart-agreements"),
    'ztsa_form_mail_to_tenant_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_form_detail_mailed_to_owner_id',
    __('Mail setting for form detail mailed to Owner', "smart-agreements"),
    'ztsa_form_Detail_mailed_to_owner_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_agreement_form_mailed_to_tenant_id',
    __('Mail setting for Agreement mailed to tenant', "smart-agreements"),
    'ztsa_agreement_acceptance_mail_to_tenant_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_agreement_form_mailed_to_multi_tenant_id',
    __('Mail setting for Agreement mailed to multiple tenant', "smart-agreements"),
    'ztsa_agreement_acceptance_mail_to_multi_tenant_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_rejection_mailed_to_tenant_id',
    __('Mail setting for rejection mailed to tenant', "smart-agreements"),
    'ztsa_agreement_rejection_mail_to_tenant_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_Agreemant_acceptance_mailed_to_owner_id',
    __('Mail setting for agreement acceptance mailed to owner', "smart-agreements"),
    'ztsa_agreement_acceptance_mail_to_owner_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_Agreemant_rejection_mail_to_owner_id',
    __('Mail setting for agreement rejection mail to owner', "smart-agreements"),
    'ztsa_agreement_rejection_mail_to_owner_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_final_agreement_mail_id',
    __('Mail setting for Final agreement mail to both', "smart-agreements"),
    'ztsa_final_agreement_mail_html',
    sanitize_key($screen->id),
    'normal',
);
add_meta_box(
    'ztsa_email_shortcode_id',
    __('List of shortcode using in Email', "smart-agreements"),
    'ztsa_email_shortcode_html',
    sanitize_key($screen->id),
    'side',
);
?>

<div class="wrap">
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
</div>

<?php
/**
 * Responsible for create owner email html
 *  
 * @return void
 */
function ztsa_questionnaire_created_mail_to_owner_html() {

    $ztsa_ques_crtd_mail_to_owner = get_option('ztsa_ques_crtd_mail_to_owner', array('checkbox' => 'on', 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));

    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_ques_crtd_mail_to_owner_checkbox" id="ztsa_ques_crtd_mail_to_owner_checkbox" type="checkbox" data-toggle="toggle" <?php echo (empty($ztsa_ques_crtd_mail_to_owner['checkbox'])) ? " " : "checked"; ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_ques_crtd_mail_to_owner_to" id="ztsa_ques_crtd_mail_to_owner_to" value="[Author_Email]" readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_ques_crtd_mail_to_owner_cc" id="ztsa_ques_crtd_mail_to_owner_cc" value="<?php echo esc_attr($ztsa_ques_crtd_mail_to_owner['cc']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_ques_crtd_mail_to_owner_subject" id="ztsa_ques_crtd_mail_to_owner_subject" value="<?php echo esc_attr($ztsa_ques_crtd_mail_to_owner['subject']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_ques_crtd_mail_to_owner['msg_header']), 'ztsa_ques_crtd_mail_to_owner_msg_header', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_ques_crtd_mail_to_owner['msg_body']), 'ztsa_ques_crtd_mail_to_owner_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_ques_crtd_mail_to_owner['msg_footer']), 'ztsa_ques_crtd_mail_to_owner_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>
                </tbody>

            </table>
    </div>

    <?php
}


/**
 * Responsible for create admin email html
 *  
 * @return void
 */
function ztsa_questionnaire_created_mail_to_admin_html()
{

    $ztsa_ques_crtd_mail_to_admin = get_option('ztsa_ques_crtd_mail_to_admin', array('checkbox' => 'on', 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));

    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_ques_crtd_mail_to_admin_checkbox" id="ztsa_ques_crtd_mail_to_admin_checkbox" type="checkbox" data-toggle="toggle" <?php echo (empty($ztsa_ques_crtd_mail_to_admin['checkbox'])) ? " " : "checked"; ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_ques_crtd_mail_to_admin_to" id="ztsa_ques_crtd_mail_to_admin_to" value="[Admin_Email]" readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_ques_crtd_mail_to_admin_cc" id="ztsa_ques_crtd_mail_to_admin_cc" value="<?php echo esc_attr($ztsa_ques_crtd_mail_to_admin['cc']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_ques_crtd_mail_to_admin_subject" id="ztsa_ques_crtd_mail_to_admin_subject" value="<?php echo esc_attr($ztsa_ques_crtd_mail_to_admin['subject']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_ques_crtd_mail_to_admin['msg_header']), 'ztsa_ques_crtd_mail_to_admin_msg_header', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_ques_crtd_mail_to_admin['msg_body']), 'ztsa_ques_crtd_mail_to_admin_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_ques_crtd_mail_to_admin['msg_footer']), 'ztsa_ques_crtd_mail_to_admin_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>

    <?php
}


/**
 * Responsible for create tenant email html
 *  
 * @return void
 */
function ztsa_form_mail_to_tenant_html()
{
    $ztsa_form_mail_to_tenant = get_option('ztsa_form_mail_to_tenant', array('checkbox' => Null, 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));

    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_form_mail_to_tenant_checkbox" id="ztsa_form_mail_to_tenant_checkbox" type="checkbox" data-toggle="toggle" <?php echo (empty($ztsa_form_mail_to_tenant['checkbox'])) ? " " : "checked"; ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_form_mail_to_tenant_to" id="ztsa_form_mail_to_tenant_to" value="<?php echo esc_attr($ztsa_form_mail_to_tenant['to']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_form_mail_to_tenant_cc" id="ztsa_form_mail_to_tenant_cc" value="<?php echo esc_attr($ztsa_form_mail_to_tenant['cc']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_form_mail_to_tenant_subject" id="ztsa_form_mail_to_tenant_subject" value="<?php echo esc_attr($ztsa_form_mail_to_tenant['subject']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_form_mail_to_tenant['msg_header']), 'ztsa_form_mail_to_tenant_msg_heading', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_form_mail_to_tenant['msg_body']), 'ztsa_form_mail_to_tenant_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_form_mail_to_tenant['msg_footer']), 'ztsa_form_mail_to_tenant_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>

    <?php
}

/**
 * Responsible for Detail mailed to owner html
 *  
 * @return void
 */
function ztsa_form_Detail_mailed_to_owner_html()
{
    $ztsa_form_Detail_mailed_to_owner = get_option('ztsa_form_Detail_mailed_to_owner', array('checkbox' => 'on', 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));

    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_form_Detail_mailed_to_owner_checkbox" id="ztsa_form_Detail_mailed_to_owner_checkbox" type="checkbox" data-toggle="toggle" <?php echo (empty($ztsa_form_Detail_mailed_to_owner['checkbox'])) ? " " : "checked";  ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_form_Detail_mailed_to_owner_to" id="ztsa_form_Detail_mailed_to_owner_to" value="[Author_Email]" readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_form_Detail_mailed_to_owner_cc" id="ztsa_form_Detail_mailed_to_owner_cc" value="<?php echo esc_attr($ztsa_form_Detail_mailed_to_owner['cc']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_form_Detail_mailed_to_owner_subject" id="ztsa_form_Detail_mailed_to_owner_subject" value="<?php echo esc_attr($ztsa_form_Detail_mailed_to_owner['subject']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_form_Detail_mailed_to_owner['msg_header']), 'ztsa_form_Detail_mailed_to_owner_msg_header', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_form_Detail_mailed_to_owner['msg_body']), 'ztsa_form_Detail_mailed_to_owner_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_form_Detail_mailed_to_owner['msg_footer']), 'ztsa_form_Detail_mailed_to_owner_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>

    <?php
}

/**
 * Responsible for agreement acceptance mail to tenant html
 *  
 * @return void
 */
function ztsa_agreement_acceptance_mail_to_tenant_html()
{
    $ztsa_agmt_acpt_mail_tenant = get_option('ztsa_agmt_acpt_mail_tenant', array('checkbox' => 'on', 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));

    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_agmt_acpt_mail_tenant_checkbox" id="ztsa_agmt_acpt_mail_tenant_checkbox" type="checkbox" data-toggle="toggle" <?php if (empty($ztsa_agmt_acpt_mail_tenant['checkbox'])) {
                                                                                                                                                                echo '';
                                                                                                                                                            } else {
                                                                                                                                                                echo 'checked';
                                                                                                                                                            } ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_acpt_mail_tenant_to" id="ztsa_agmt_acpt_mail_tenant_to" value="[Customer_Email]" readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_acpt_mail_tenant_cc" id="ztsa_agmt_acpt_mail_tenant_cc" value="<?php echo esc_attr($ztsa_agmt_acpt_mail_tenant['cc']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_acpt_mail_tenant_subject" id="ztsa_agmt_acpt_mail_tenant_subject" value="<?php echo esc_attr($ztsa_agmt_acpt_mail_tenant['subject']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_acpt_mail_tenant['msg_header']), 'ztsa_agmt_acpt_mail_tenant_msg_header', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_acpt_mail_tenant['msg_body']), 'ztsa_agmt_acpt_mail_tenant_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_acpt_mail_tenant['msg_footer']), 'ztsa_agmt_acpt_mail_tenant_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>
    <?php
}

/**
 * Responsible for agreement acceptance mail to multi tenant html
 *  
 * @return void
 */
function ztsa_agreement_acceptance_mail_to_multi_tenant_html()
{
    $ztsa_agmt_acpt_mail_multi_tenant = get_option('ztsa_agmt_acpt_mail_multi_tenant', array('checkbox' => 'on', 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));

    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_agmt_acpt_mail_multi_tenant_checkbox" id="ztsa_agmt_acpt_mail_multi_tenant_checkbox" type="checkbox" data-toggle="toggle" <?php if (empty($ztsa_agmt_acpt_mail_multi_tenant['checkbox'])) {
                                                                                                                                                                            echo '';
                                                                                                                                                                        } else {
                                                                                                                                                                            echo 'checked';
                                                                                                                                                                        } ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_acpt_mail_multi_tenant_to" id="ztsa_agmt_acpt_mail_multi_tenant_to" value="[Additional_Customer_Email]" readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_acpt_mail_multi_tenant_cc" id="ztsa_agmt_acpt_mail_multi_tenant_cc" value="<?php echo esc_attr($ztsa_agmt_acpt_mail_multi_tenant['cc']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_acpt_mail_multi_tenant_subject" id="ztsa_agmt_acpt_mail_multi_tenant_subject" value="<?php echo esc_attr($ztsa_agmt_acpt_mail_multi_tenant['subject']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_acpt_mail_multi_tenant['msg_header']), 'ztsa_agmt_acpt_mail_multi_tenant_msg_header', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_acpt_mail_multi_tenant['msg_body']), 'ztsa_agmt_acpt_mail_multi_tenant_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_acpt_mail_multi_tenant['msg_footer']), 'ztsa_agmt_acpt_mail_multi_tenant_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>
    <?php
}

/**
 * Responsible for agreement rejection mail to tenant html
 *  
 * @return void
 */
function ztsa_agreement_rejection_mail_to_tenant_html()
{
    $ztsa_rejection_mail_tenant = get_option('ztsa_rejection_mail_tenant', array('checkbox' => 'on', 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));

    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_rejection_mail_tenant_checkbox" id="ztsa_rejection_mail_tenant_checkbox" type="checkbox" data-toggle="toggle" <?php if (empty($ztsa_rejection_mail_tenant['checkbox'])) {
                                                                                                                                                                echo '';
                                                                                                                                                            } else {
                                                                                                                                                                echo 'checked';
                                                                                                                                                            } ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_rejection_mail_tenant_to" id="ztsa_rejection_mail_tenant_to" value="[Customer_Email]" readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_rejection_mail_tenant_cc" id="ztsa_rejection_mail_tenant_cc" value="<?php echo esc_attr($ztsa_rejection_mail_tenant['cc']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_rejection_mail_tenant_subject" id="ztsa_rejection_mail_tenant_subject" value="<?php echo esc_attr($ztsa_rejection_mail_tenant['subject']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_rejection_mail_tenant['msg_header']), 'ztsa_rejection_mail_tenant_msg_header', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_rejection_mail_tenant['msg_body']), 'ztsa_rejection_mail_tenant_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_rejection_mail_tenant['msg_footer']), 'ztsa_rejection_mail_tenant_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>
    <?php
}

/**
 * Responsible for agreement acceptance mail to owner html
 *  
 * @return void
 */
function ztsa_agreement_acceptance_mail_to_owner_html()
{
    $ztsa_agmt_acpt_mail_owner = get_option('ztsa_agmt_acpt_mail_owner', array('checkbox' => 'on', 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));
    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_agmt_acpt_mail_owner_checkbox" id="ztsa_agmt_acpt_mail_owner_checkbox" type="checkbox" data-toggle="toggle" <?php if (empty($ztsa_agmt_acpt_mail_owner['checkbox'])) {
                                                                                                                                                            echo '';
                                                                                                                                                        } else {
                                                                                                                                                            echo 'checked';
                                                                                                                                                        } ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_acpt_mail_owner_to" id="ztsa_agmt_acpt_mail_owner_to" value="[Author_Email]" readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_acpt_mail_owner_cc" id="ztsa_agmt_acpt_mail_owner_cc" value="<?php echo esc_attr($ztsa_agmt_acpt_mail_owner['cc']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_acpt_mail_owner_subject" id="ztsa_agmt_acpt_mail_owner_subject" value="<?php echo esc_attr($ztsa_agmt_acpt_mail_owner['subject']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_acpt_mail_owner['msg_header']), 'ztsa_agmt_acpt_mail_owner_msg_header', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_acpt_mail_owner['msg_body']), 'ztsa_agmt_acpt_mail_owner_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_acpt_mail_owner['msg_footer']), 'ztsa_agmt_acpt_mail_owner_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>

                </tbody>
            </table>
    </div>

    <?php
}

/**
 * Responsible for agreement rejection mail to owner html
 *  
 * @return void
 */
function ztsa_agreement_rejection_mail_to_owner_html()
{
    $ztsa_agmt_rej_mail_owner = get_option('ztsa_agmt_rej_mail_owner', array('checkbox' => 'on', 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));

    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_agmt_rej_mail_owner_checkbox" id="ztsa_agmt_rej_mail_owner_checkbox" type="checkbox" data-toggle="toggle" <?php if (empty($ztsa_agmt_rej_mail_owner['checkbox'])) {
                                                                                                                                                            echo '';
                                                                                                                                                        } else {
                                                                                                                                                            echo 'checked';
                                                                                                                                                        } ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_rej_mail_owner_to" id="ztsa_agmt_rej_mail_owner_to" value="[Author_Email]" readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_rej_mail_owner_cc" id="ztsa_agmt_rej_mail_owner_cc" value="<?php echo esc_attr($ztsa_agmt_rej_mail_owner['cc']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_agmt_rej_mail_owner_subject" id="ztsa_agmt_rej_mail_owner_subject" value="<?php echo esc_attr($ztsa_agmt_rej_mail_owner['subject'])  ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_rej_mail_owner['msg_header']), 'ztsa_agmt_rej_mail_owner_msg_header', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_rej_mail_owner['msg_body']), 'ztsa_agmt_rej_mail_owner_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_agmt_rej_mail_owner['msg_footer']), 'ztsa_agmt_rej_mail_owner_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>

                </tbody>
            </table>
    </div>

    <?php
}

/**
 * Responsible for final agreement mail html
 *  
 * @return void
 */
function ztsa_final_agreement_mail_html()
{
    $ztsa_final_agreement = get_option('ztsa_final_agreement', array('checkbox' => 'on', 'to' => '', 'cc' => '', 'subject' => '', 'msg_header' => '', 'msg_body' => '', 'msg_footer' => ''));

    ?>
    <div class="wrap">
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('ztsa_notification_setting_tab', 'ztsa_notification_setting_tab'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Notification Enable', "smart-agreements"); ?></th>
                        <td>
                            <input name="ztsa_final_agreement_checkbox" id="ztsa_final_agreement_checkbox" type="checkbox" data-toggle="toggle" <?php if (empty($ztsa_final_agreement['checkbox'])) {
                                                                                                                                                    echo '';
                                                                                                                                                } else {
                                                                                                                                                    echo 'checked';
                                                                                                                                                } ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="ztsa_notification_setting_tab"><br>
                            <input style="width: 100%;" type="text" name="ztsa_final_agreement_to" id="ztsa_final_agreement_to" value="<?php echo esc_attr($ztsa_final_agreement['to']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('CC', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_final_agreement_cc" id="ztsa_final_agreement_cc" value="<?php echo esc_attr($ztsa_final_agreement['cc']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="ztsa_final_agreement_subject" id="ztsa_final_agreement_subject" value="<?php echo esc_attr($ztsa_final_agreement['subject']) ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Header', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_final_agreement['msg_header']), 'ztsa_final_agreement_msg_header', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_final_agreement['msg_body']), 'ztsa_final_agreement_msg_body', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Footer', "smart-agreements"); ?></th>
                        <td>
                            <?php
                            $settings = array('editor_height' => 40, 'media_buttons' => FALSE, 'textarea_rows' => 5);
                            wp_editor(wp_kses_post($ztsa_final_agreement['msg_footer']), 'ztsa_final_agreement_msg_footer', $settings)
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td style='float:left;margin-left:-13px;'>
                            <?php
                            $other_attributes = array('id' => 'submit');
                            submit_button(__('Save Settings', "smart-agreements"), 'primary', 'submit', true, $other_attributes);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>

    <?php
}

/**
 * Responsible for email shortcode html
 *  
 * @return void
 */
function ztsa_email_shortcode_html()
{
    ?>
    <span style="font-size:16px" onClick='selectText(this)'>[Admin_Email]</span> <br>
    <span style="font-size:16px" onClick='selectText(this)'>[Author_Name]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Author_Email]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Customer_Name]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Customer_Email]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Additional_Customer_Name]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Additional_Customer_Email]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Form_Title]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Form_Id]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Author_Comment]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Customer_Comment]</span><br>
    <span style="font-size:16px" onClick='selectText(this)'>[Agreement_Link]</span><br>


    <?php
}

?>
<script>
    jQuery(function() {
        postboxes.add_postbox_toggles(pagenow);
    });

    jQuery(document).ready(function() {
        var url = window.location.href;
        url = url.split('?')[0];
        url_query = '?post_type=<?php echo esc_attr(ZTSA_POST_TYPE_SLUG); ?>&page=<?php echo esc_attr(ZTSA_SETTING_PAGE_SLUG); ?>&tab=notification-setting'
        newURL = url + url_query;
        window.history.pushState('object', document.title, newURL);
    });
</script>