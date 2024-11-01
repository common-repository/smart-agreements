<?php
/**
 * Agreement template.
 *
 * @package smart-agreements\ui-admin
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : '';
$room_agrmnt_template_header = "<div><div><p style='text-align: center;color:#993300;'><strong>Room Rental Agreement</strong></p>
</div>";
$room_agrmnt_template = "<div id='room_rental_agrmt'>
<p>This is a legally binding document agreed upon by the homeowner, henceforth known as</p> 
<p>“Landlord”, and the renter, henceforth known as “Tenant”.</p>
<br>
<p>Rental property located at  in the State of.<p>
<p>Landlord Name: [Owner Name]<p>
<p>Tenant Name: [Customer Name]<p> 
<br>
<p>The undersigned agree to a  year lease of the above-mentioned property.On the  day, in</p>
<p>the month of , in the year , the Tenant agrees to pay $ as the security deposit, $ for the first</p>
<p>month's rent, and $ for the last month's rent. Monthly rent shall be made by the  day of</p>
<p>every month. Rent shall be $ and made payable to . At the termination of the lease, the </p>
<p>Tenant shall have their security deposit returned to them in full if the property has been</p>
<p>maintained properly.<p>
<br>
<p>The undersigned Tenant has inspected the room located at the above-mentioned property,</p>
<p>and is satisfied with the conditions of the room.</p>
<br>
<p>The Landlord has agreed to provide the following utilities:</p>
    <ul>
        <li>Water - Tenant pays % of monthly bill.</li>
        <li>Electricity - Tenant pays % of monthly bill.</li>
        <li>Trash - Tenant pays % of monthly bill.</li>
        <li>Internet - Tenant pays % of monthly bill.</li>
        <li>Gas - Tenant pays % of monthly bill.</li>
    </ul>
<br>
<p>The Landlord agrees to the following conditions: The main rooms of the house are permissible.</p>
<p>The rental room shall be off limits to anyone living within the home that is not the Tenant.</p>
<p>The yard and outdoor furniture shall be maintained by the Landlord. The Tenant is allowed</p>
<p>set schedules of “quiet” within the property.<p>
<br>
<p>If the Tenant or Landlord wishes to terminate this agreement, 60 days' notice in writing</p>
<p>and is satisfied with the conditions of the room.</p>
<br>
<p>The undersigned agrees to all of the conditions of this.</p>
<br>
<p>I, <strong>[Owner Name]</strong>, agree to honor all of the terms of this agreement, dated on this day</p> 
<p>in the month of, in the year .</p>
<p>[Owner Signature]</p>
<br>
<p>I, <strong>[Customer Name]</strong>, agree to honor all of the terms of this agreement, dated on this day </p>
<p>in the month of , the year .</p>
<p>[Customer Signature]</p></div>
<div><button id='select_room_agrmt' class='select_template' >Select</button></div></div>";
$loan_agrmt_templat = 0;

?>

<form id='save_agrmt_template' action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <?php wp_nonce_field('ztsa_save_template', 'ztsa_save_template'); ?>
    <input type="hidden" name="action" value="save_template">
    <input type="hidden" id="template_post_id" name="post_id" value="<?php echo esc_attr($post_id); ?>">
    <input type="hidden" id="template_header_data" name="template_header_data" value="">
    <input type="hidden" id="template_data" name="template_data" value="">
</form>