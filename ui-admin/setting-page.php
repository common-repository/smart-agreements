<?php
/**
 * Setting Page.
 *
 * @package smart-agreements\ui-admin
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

 if (!current_user_can('manage_options')) {
    return;
}

$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'smtp-setting';
$setting_tabs = array(
    // 'general-setting' => 'General Setting',
    'smtp-setting' => 'SMTP Settings',
    // 'form-setting' => 'Form Setting',
    'notification-setting' => 'Notification Settings',
);
?>

<div class="wrap">
    <!-- Print the page title -->
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <?php
    if (isset($_GET['success']) == 'true') {
    ?>
        <div class="ztsa_notice notice notice-success is-dismissible">
            <p><?php esc_html_e('Settings saved.', "smart-agreements"); ?></p>
        </div>
    <?php
    } ?>
    <h2 class="nav-tab-wrapper" style="margin-bottom:20px">
    <?php foreach ($setting_tabs as $tab => $title) {
        $current_active_tab = ($tab === $current_tab) ? 'nav-tab-active' : '';
        printf(
            '<a class="nav-tab %s" href="%s" >%s</a>',
            esc_attr($current_active_tab),
            esc_url(admin_url("edit.php?post_type=".ZTSA_POST_TYPE_SLUG."&page=".ZTSA_SETTING_PAGE_SLUG."&tab={$tab}")),
            esc_attr($title)
        );
    } ?>
    </h2>

    <div class="tab-content">
        <?php
        if (isset($current_tab)) {
            require_once ZTSA_UI_ADMIN_DIR . 'metabox/' . $current_tab . '.php';
        }
        ?>
    </div>
</div>