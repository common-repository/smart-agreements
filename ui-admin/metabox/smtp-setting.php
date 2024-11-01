<?php
/**
 * Add smtp setting metabox and form.
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
    'SMTP_setting_id',
    __('SMTP Configuration Setting', "smart-agreements"),
    'ztsa_smtp_setting_box_html',
    sanitize_key($screen->id),
    'normal',
);
do_meta_boxes(sanitize_key($screen->id), 'normal', '');

/**
 * Responsible for smtp setting box html
 *  
 * @return void
 */
function ztsa_smtp_setting_box_html()
{

    $smtpData = get_option('ztsa_SMTP_Setting', array('smtp_email' => '', 'smtp_name' => '', 'smtp_host' => '', 'ecription_type' => '', 'smtp_port' => '', 'authentication_type' => '', 'smtp_user' => '', 'smtp_password' => ''));
    ?>
    <div class="wrap">
        <table class="form-table">
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <?php wp_nonce_field('save_smtp_setting', 'smtp_generate_nonce'); ?>
                <tbody>
                    <tr>
                        <th><?php esc_html_e('From Email Address', "smart-agreements"); ?></th>
                        <td>
                            <input type="hidden" name="action" value="save_smtp_setting">
                            <input style="width: 100%;" type="email" name="smtp_email" id="smtp_email" autocomplete="off" value="<?php echo  esc_attr($smtpData['smtp_email']) ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('From Name', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="smtp_name" id="smtp_name" autocomplete="off" value="<?php echo  esc_attr($smtpData['smtp_name']) ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('SMTP Host', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="smtp_host" id="smtp_host" value="<?php echo  esc_attr($smtpData['smtp_host']) ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Type of Encription', "smart-agreements"); ?></th>
                        <td>
                            <input type="radio" name="ecription_type" id="none_type" value='None' <?php echo ($smtpData['ecription_type'] == 'None') ? 'checked' : '' ?>>
                            <label for="none_type"><?php esc_html_e('None', "smart-agreements"); ?></label>
                            <input type="radio" name="ecription_type" id="ssl_type" value='ssl' <?php echo ($smtpData['ecription_type'] == 'ssl') ? 'checked' : '' ?>>
                            <label for="ssl_type"><?php esc_html_e('SSL', "smart-agreements"); ?></label>
                            <input type="radio" name="ecription_type" id="tls_type" value='tls' <?php echo ($smtpData['ecription_type'] == 'tls') ? 'checked' : '' ?>>
                            <label for="tls_type"><?php esc_html_e('TLS', "smart-agreements"); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('SMTP Port', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="smtp_port" id="smtp_port" value="<?php echo  esc_attr($smtpData['smtp_port']) ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('SMTP Authentication', "smart-agreements"); ?></th>
                        <td>
                            <input type="radio" name="authentication_type" id="no_type" value='false' <?php echo ($smtpData['authentication_type'] == 'false') ? 'checked' : '' ?>>
                            <label for="no_type"><?php esc_html_e('No', "smart-agreements"); ?></label>
                            <input type="radio" name="authentication_type" id="yes_type" value='true' <?php echo ($smtpData['authentication_type'] == 'true') ? 'checked' : '' ?>>
                            <label for="yes_type"><?php esc_html_e('Yes', "smart-agreements"); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('SMTP User', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="smtp_user" id="smtp_user" value="<?php echo  esc_attr($smtpData['smtp_user']) ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('SMTP Password', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="password" name="smtp_password" id="smtp_password" value="<?php echo  esc_attr($smtpData['smtp_password']) ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <input style="width: 100%;" type="submit" class="button button-primary" style="height:30px; width:100%" name="smtp_setting" value="Save Changes">
                        </th>
            </form>
            <td>
                <button class="button" style="width:25%;margin-left:75%;" id="test_mail_btn"><?php esc_html_e('Test Mail', "smart-agreements"); ?></button>
            </td>
            </tr>
            </tbody>
        </table>
        <div class="wrap" id="test_mail_div">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('To', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="email" name="test_email" id="test_email" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Subject', "smart-agreements"); ?></th>
                        <td>
                            <input style="width: 100%;" type="text" name="test_subject" id="test_subject" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Message Body', "smart-agreements"); ?></th>
                        <td>
                            <textarea style="height :250px; width: 100%;" id="test_msg_body" name="test_msg_body"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <input style="width: 100%;" type="submit" data-action-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" class="button button-primary" style="height:30px; width:100%" id="test_mail" value="Send Mail">
                        </th>
                        <td>
                            <div id="test_message"></div>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
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
        url_query = '?post_type=<?php echo esc_attr(ZTSA_POST_TYPE_SLUG); ?>&page=<?php echo esc_attr(ZTSA_SETTING_PAGE_SLUG); ?>&tab=smtp-setting'
        newURL = url + url_query;
        window.history.pushState('object', document.title, newURL);
    });
</script>