<?php
/**
 * agreement Page.
 *
 * @package smart-agreements\ui-front
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

$additional_entry_id = isset($_GET['id']) ? base64_decode(sanitize_text_field($_GET['id'])) : '0';

global $wpdb, $table_prefix;
$table_name = $table_prefix . 'ztsa_customer_info';
$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE id='%d'", sanitize_text_field( $customer_id ) ), ARRAY_A);
$customer_info = json_decode($results[0]['customer_info']);
$customer_name = $customer_info->ztsa_user_name->values;
$customer_email = $customer_info->ztsa_user_email->values;
$post_id = $results[0]['form_id'];
$author_id = get_post_field(sanitize_key('post_author'), sanitize_text_field($post_id));
$author_name = get_the_author_meta(sanitize_key('display_name'), $author_id);
$customer_sign = $results[0]['customer_sign'];
$owner_sign = $results[0]['owner_sign'];
$additional_user_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix . "ztsa_extra_customer_info WHERE entry_id='%d'", sanitize_text_field( $customer_id ) ), ARRAY_A);
$header = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_header'), true);
$body = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_body'), true);
$footer = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_footer'), true);
$logo = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_logo'), true);
$logo_alignment = get_post_meta(sanitize_text_field($post_id), sanitize_key('ztsa_agreement_logo_alignment'), true);
$agreement = array(
  'header' => $header,
  'body' => $body,
  'footer' => $footer
);

if (count($additional_user_details) > 0) {
  $additional_user_sign = $wpdb->get_var( $wpdb->prepare( "SELECT customer_sign FROM ".$wpdb->prefix . "ztsa_extra_customer_info WHERE id='%d'", sanitize_text_field( $additional_entry_id ) ) );
  foreach ($additional_user_details as $values) {
    $extra_user_name[] = $values['customer_name'];
    $extra_user_email[] = $values['customer_email'];
//we can't user esc_url in $values["customer_sign"] because image in base64 encrypt format
    $signature_feild[] = '<div class="additional_sign_field">
     <div class="additional_cust_name">' . esc_html($values["customer_name"]) . '</div>
     <div class="additional_cust_sign"><img class="customer_sign" id ="coustomer_sign_' . esc_attr($values["id"]) . '" src="' . esc_attr( $values["customer_sign"] ) . '" alt="Customer Sign"></div>
    </div>';
  }
  $extra_user_name = implode(", ", $extra_user_name);
  $extra_user_email = implode(", ", $extra_user_email);
  $signature_feild = implode("", $signature_feild);
  $signature_feild = "<p>" . __('Additional Customer Signature:', 'smart-agreements') . "</p>" . $signature_feild;

  $agreement = str_replace("[Additional Users Name]", $extra_user_name, $agreement);
  $agreement = str_replace("[Additional Users Email]", $extra_user_email, $agreement);
  $agreement = str_replace("[Additional Customer Signature]", $signature_feild, $agreement);
} else {
  $agreement = str_replace("[Additional Users Name]", "", $agreement);
  $agreement = str_replace("[Additional Users Email]", "", $agreement);
  $agreement = str_replace("[Additional Customer Signature]", "", $agreement);
}
$customer_sign_div = '<p>' . __("Customer Signature:", "smart-agreements") . '</p><div class="additional_sign_field">
<div class="additional_cust_name">' . esc_html($customer_name) . '</div>
<div class="additional_cust_sign"><img class="customer_sign" id ="main_customer_sign" src="' . esc_attr( $customer_sign ) . '" alt="Customer Sign"></div>
</div>';
$agreement = str_replace("[Customer Signature]", $customer_sign_div, $agreement);

$owner_sign_div = '<p>' . __("Owner Signature:", "smart-agreements") . '</p><div class="additional_sign_field">
<div class="additional_cust_name">' . esc_html($author_name) . '</div>
<div class="additional_cust_sign"><img class="customer_sign" id ="owner_sign" src="' . esc_attr( $owner_sign ). '" alt="Customer Sign"></div>
</div>';
$agreement = str_replace("[Owner Signature]", $owner_sign_div, $agreement);
$agreement = str_replace("[Owner Name]", $author_name, $agreement);
foreach ($customer_info as $value) {
  if (is_array($value->values)) {
    $temp_value = implode(",", $value->values);
    $agreement = str_replace("[" . $value->labels . "]", $temp_value, $agreement);
  } else {
    $agreement = str_replace("[$value->labels]", $value->values, $agreement);
  }
}
?>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<style>

    :root,
    [data-bs-theme="light"] {
        --bs-blue: #0d6efd;
        --bs-indigo: #6610f2;
        --bs-purple: #6f42c1;
        --bs-pink: #d63384;
        --bs-red: #dc3545;
        --bs-orange: #fd7e14;
        --bs-yellow: #ffc107;
        --bs-green: #198754;
        --bs-teal: #20c997;
        --bs-cyan: #0dcaf0;
        --bs-black: #000;
        --bs-white: #fff;
        --bs-gray: #6c757d;
        --bs-gray-dark: #343a40;
        --bs-gray-100: #f8f9fa;
        --bs-gray-200: #e9ecef;
        --bs-gray-300: #dee2e6;
        --bs-gray-400: #ced4da;
        --bs-gray-500: #adb5bd;
        --bs-gray-600: #6c757d;
        --bs-gray-700: #495057;
        --bs-gray-800: #343a40;
        --bs-gray-900: #212529;
        --bs-primary: #0d6efd;
        --bs-secondary: #6c757d;
        --bs-success: #198754;
        --bs-info: #0dcaf0;
        --bs-warning: #ffc107;
        --bs-danger: #dc3545;
        --bs-light: #f8f9fa;
        --bs-dark: #212529;
        --bs-primary-rgb: 13, 110, 253;
        --bs-secondary-rgb: 108, 117, 125;
        --bs-success-rgb: 25, 135, 84;
        --bs-info-rgb: 13, 202, 240;
        --bs-warning-rgb: 255, 193, 7;
        --bs-danger-rgb: 220, 53, 69;
        --bs-light-rgb: 248, 249, 250;
        --bs-dark-rgb: 33, 37, 41;
        --bs-primary-text-emphasis: #052c65;
        --bs-secondary-text-emphasis: #2b2f32;
        --bs-success-text-emphasis: #0a3622;
        --bs-info-text-emphasis: #055160;
        --bs-warning-text-emphasis: #664d03;
        --bs-danger-text-emphasis: #58151c;
        --bs-light-text-emphasis: #495057;
        --bs-dark-text-emphasis: #495057;
        --bs-primary-bg-subtle: #cfe2ff;
        --bs-secondary-bg-subtle: #e2e3e5;
        --bs-success-bg-subtle: #d1e7dd;
        --bs-info-bg-subtle: #cff4fc;
        --bs-warning-bg-subtle: #fff3cd;
        --bs-danger-bg-subtle: #f8d7da;
        --bs-light-bg-subtle: #fcfcfd;
        --bs-dark-bg-subtle: #ced4da;
        --bs-primary-border-subtle: #9ec5fe;
        --bs-secondary-border-subtle: #c4c8cb;
        --bs-success-border-subtle: #a3cfbb;
        --bs-info-border-subtle: #9eeaf9;
        --bs-warning-border-subtle: #ffe69c;
        --bs-danger-border-subtle: #f1aeb5;
        --bs-light-border-subtle: #e9ecef;
        --bs-dark-border-subtle: #adb5bd;
        --bs-white-rgb: 255, 255, 255;
        --bs-black-rgb: 0, 0, 0;
        --bs-font-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        --bs-font-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
        --bs-body-font-family: var(--bs-font-sans-serif);
        --bs-body-font-size: 1rem;
        --bs-body-font-weight: 400;
        --bs-body-line-height: 1.5;
        --bs-body-color: #212529;
        --bs-body-color-rgb: 33, 37, 41;
        --bs-body-bg: #fff;
        --bs-body-bg-rgb: 255, 255, 255;
        --bs-emphasis-color: #000;
        --bs-emphasis-color-rgb: 0, 0, 0;
        --bs-secondary-color: rgba(33, 37, 41, 0.75);
        --bs-secondary-color-rgb: 33, 37, 41;
        --bs-secondary-bg: #e9ecef;
        --bs-secondary-bg-rgb: 233, 236, 239;
        --bs-tertiary-color: rgba(33, 37, 41, 0.5);
        --bs-tertiary-color-rgb: 33, 37, 41;
        --bs-tertiary-bg: #f8f9fa;
        --bs-tertiary-bg-rgb: 248, 249, 250;
        --bs-link-color: #0d6efd;
        --bs-link-color-rgb: 13, 110, 253;
        --bs-link-decoration: underline;
        --bs-link-hover-color: #0a58ca;
        --bs-link-hover-color-rgb: 10, 88, 202;
        --bs-code-color: #d63384;
        --bs-highlight-bg: #fff3cd;
        --bs-border-width: 1px;
        --bs-border-style: solid;
        --bs-border-color: #dee2e6;
        --bs-border-color-translucent: rgba(0, 0, 0, 0.175);
        --bs-border-radius: 0.375rem;
        --bs-border-radius-sm: 0.25rem;
        --bs-border-radius-lg: 0.5rem;
        --bs-border-radius-xl: 1rem;
        --bs-border-radius-xxl: 2rem;
        --bs-border-radius-2xl: var(--bs-border-radius-xxl);
        --bs-border-radius-pill: 50rem;
        --bs-box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        --bs-box-shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --bs-box-shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        --bs-box-shadow-inset: inset 0 1px 2px rgba(0, 0, 0, 0.075);
        --bs-focus-ring-width: 0.25rem;
        --bs-focus-ring-opacity: 0.25;
        --bs-focus-ring-color: rgba(13, 110, 253, 0.25);
        --bs-form-valid-color: #198754;
        --bs-form-valid-border-color: #198754;
        --bs-form-invalid-color: #dc3545;
        --bs-form-invalid-border-color: #dc3545;
    }
    [data-bs-theme="dark"] {
        color-scheme: dark;
        --bs-body-color: #adb5bd;
        --bs-body-color-rgb: 173, 181, 189;
        --bs-body-bg: #212529;
        --bs-body-bg-rgb: 33, 37, 41;
        --bs-emphasis-color: #fff;
        --bs-emphasis-color-rgb: 255, 255, 255;
        --bs-secondary-color: rgba(173, 181, 189, 0.75);
        --bs-secondary-color-rgb: 173, 181, 189;
        --bs-secondary-bg: #343a40;
        --bs-secondary-bg-rgb: 52, 58, 64;
        --bs-tertiary-color: rgba(173, 181, 189, 0.5);
        --bs-tertiary-color-rgb: 173, 181, 189;
        --bs-tertiary-bg: #2b3035;
        --bs-tertiary-bg-rgb: 43, 48, 53;
        --bs-primary-text-emphasis: #6ea8fe;
        --bs-secondary-text-emphasis: #a7acb1;
        --bs-success-text-emphasis: #75b798;
        --bs-info-text-emphasis: #6edff6;
        --bs-warning-text-emphasis: #ffda6a;
        --bs-danger-text-emphasis: #ea868f;
        --bs-light-text-emphasis: #f8f9fa;
        --bs-dark-text-emphasis: #dee2e6;
        --bs-primary-bg-subtle: #031633;
        --bs-secondary-bg-subtle: #161719;
        --bs-success-bg-subtle: #051b11;
        --bs-info-bg-subtle: #032830;
        --bs-warning-bg-subtle: #332701;
        --bs-danger-bg-subtle: #2c0b0e;
        --bs-light-bg-subtle: #343a40;
        --bs-dark-bg-subtle: #1a1d20;
        --bs-primary-border-subtle: #084298;
        --bs-secondary-border-subtle: #41464b;
        --bs-success-border-subtle: #0f5132;
        --bs-info-border-subtle: #087990;
        --bs-warning-border-subtle: #997404;
        --bs-danger-border-subtle: #842029;
        --bs-light-border-subtle: #495057;
        --bs-dark-border-subtle: #343a40;
        --bs-link-color: #6ea8fe;
        --bs-link-hover-color: #8bb9fe;
        --bs-link-color-rgb: 110, 168, 254;
        --bs-link-hover-color-rgb: 139, 185, 254;
        --bs-code-color: #e685b5;
        --bs-border-color: #495057;
        --bs-border-color-translucent: rgba(255, 255, 255, 0.15);
        --bs-form-valid-color: #75b798;
        --bs-form-valid-border-color: #75b798;
        --bs-form-invalid-color: #ea868f;
        --bs-form-invalid-border-color: #ea868f;
    }
    *,
    ::after,
    ::before {
        box-sizing: border-box;
    }
    @media (prefers-reduced-motion: no-preference) {
        :root {
            scroll-behavior: smooth;
        }
    }
    body {
        margin: 0;
        font-family: var(--bs-body-font-family);
        font-size: var(--bs-body-font-size);
        font-weight: var(--bs-body-font-weight);
        line-height: var(--bs-body-line-height);
        color: var(--bs-body-color);
        text-align: var(--bs-body-text-align);
        background-color: var(--bs-body-bg);
        -webkit-text-size-adjust: 100%;
        -webkit-tap-highlight-color: transparent;
    }
    hr {
        margin: 1rem 0;
        color: inherit;
        border: 0;
        border-top: var(--bs-border-width) solid;
        opacity: 0.25;
    }
    .h1,
    .h2,
    .h3,
    .h4,
    .h5,
    .h6,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        margin-top: 0;
        margin-bottom: 0.5rem;
        font-weight: 500;
        line-height: 1.2;
        color: var(--bs-heading-color, inherit);
    }
    .h1,
    h1 {
        font-size: calc(1.375rem + 1.5vw);
    }
    @media (min-width: 1200px) {
        .h1,
        h1 {
            font-size: 2.5rem;
        }
    }
    .h2,
    h2 {
        font-size: calc(1.325rem + 0.9vw);
    }
    @media (min-width: 1200px) {
        .h2,
        h2 {
            font-size: 2rem;
        }
    }
    .h3,
    h3 {
        font-size: calc(1.3rem + 0.6vw);
    }
    @media (min-width: 1200px) {
        .h3,
        h3 {
            font-size: 1.75rem;
        }
    }
    .h4,
    h4 {
        font-size: calc(1.275rem + 0.3vw);
    }
    @media (min-width: 1200px) {
        .h4,
        h4 {
            font-size: 1.5rem;
        }
    }
    .h5,
    h5 {
        font-size: 1.25rem;
    }
    .h6,
    h6 {
        font-size: 1rem;
    }
    p {
        margin-top: 0;
        margin-bottom: 1rem;
    }
    abbr[title] {
        -webkit-text-decoration: underline dotted;
        text-decoration: underline dotted;
        cursor: help;
        -webkit-text-decoration-skip-ink: none;
        text-decoration-skip-ink: none;
    }
    address {
        margin-bottom: 1rem;
        font-style: normal;
        line-height: inherit;
    }
    ol,
    ul {
        padding-left: 2rem;
    }
    dl,
    ol,
    ul {
        margin-top: 0;
        margin-bottom: 1rem;
    }
    ol ol,
    ol ul,
    ul ol,
    ul ul {
        margin-bottom: 0;
    }
    dt {
        font-weight: 700;
    }
    dd {
        margin-bottom: 0.5rem;
        margin-left: 0;
    }
    blockquote {
        margin: 0 0 1rem;
    }
    b,
    strong {
        font-weight: bolder;
    }
    .small,
    small {
        font-size: 0.875em;
    }
    .mark,
    mark {
        padding: 0.1875em;
        background-color: var(--bs-highlight-bg);
    }
    sub,
    sup {
        position: relative;
        font-size: 0.75em;
        line-height: 0;
        vertical-align: baseline;
    }
    sub {
        bottom: -0.25em;
    }
    sup {
        top: -0.5em;
    }
    a {
        color: rgba(var(--bs-link-color-rgb), var(--bs-link-opacity, 1));
        text-decoration: underline;
    }
    a:hover {
        --bs-link-color-rgb: var(--bs-link-hover-color-rgb);
    }
    a:not([href]):not([class]),
    a:not([href]):not([class]):hover {
        color: inherit;
        text-decoration: none;
    }
    code,
    kbd,
    pre,
    samp {
        font-family: var(--bs-font-monospace);
        font-size: 1em;
    }
    pre {
        display: block;
        margin-top: 0;
        margin-bottom: 1rem;
        overflow: auto;
        font-size: 0.875em;
    }
    pre code {
        font-size: inherit;
        color: inherit;
        word-break: normal;
    }
    code {
        font-size: 0.875em;
        color: var(--bs-code-color);
        word-wrap: break-word;
    }
    a > code {
        color: inherit;
    }
    kbd {
        padding: 0.1875rem 0.375rem;
        font-size: 0.875em;
        color: var(--bs-body-bg);
        background-color: var(--bs-body-color);
        border-radius: 0.25rem;
    }
    kbd kbd {
        padding: 0;
        font-size: 1em;
    }
    figure {
        margin: 0 0 1rem;
    }
    img,
    svg {
        vertical-align: middle;
    }
    table {
        caption-side: bottom;
        border-collapse: collapse;
    }
    caption {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        color: var(--bs-secondary-color);
        text-align: left;
    }
    th {
        text-align: inherit;
        text-align: -webkit-match-parent;
    }
    tbody,
    td,
    tfoot,
    th,
    thead,
    tr {
        border-color: inherit;
        border-style: solid;
        border-width: 0;
    }
    label {
        display: inline-block;
    }
    button {
        border-radius: 0;
    }
    button:focus:not(:focus-visible) {
        outline: 0;
    }
    button,
    input,
    optgroup,
    select,
    textarea {
        margin: 0;
        font-family: inherit;
        font-size: inherit;
        line-height: inherit;
    }
    button,
    select {
        text-transform: none;
    }
    [role="button"] {
        cursor: pointer;
    }
    select {
        word-wrap: normal;
    }
    select:disabled {
        opacity: 1;
    }
    [list]:not([type="date"]):not([type="datetime-local"]):not([type="month"]):not([type="week"]):not([type="time"])::-webkit-calendar-picker-indicator {
        display: none !important;
    }
    [type="button"],
    [type="reset"],
    [type="submit"],
    button {
        -webkit-appearance: button;
    }
    [type="button"]:not(:disabled),
    [type="reset"]:not(:disabled),
    [type="submit"]:not(:disabled),
    button:not(:disabled) {
        cursor: pointer;
    }
    ::-moz-focus-inner {
        padding: 0;
        border-style: none;
    }
    textarea {
        resize: vertical;
    }
    fieldset {
        min-width: 0;
        padding: 0;
        margin: 0;
        border: 0;
    }
    legend {
        float: left;
        width: 100%;
        padding: 0;
        margin-bottom: 0.5rem;
        font-size: calc(1.275rem + 0.3vw);
        line-height: inherit;
    }
    @media (min-width: 1200px) {
        legend {
            font-size: 1.5rem;
        }
    }
    legend + * {
        clear: left;
    }
    ::-webkit-datetime-edit-day-field,
    ::-webkit-datetime-edit-fields-wrapper,
    ::-webkit-datetime-edit-hour-field,
    ::-webkit-datetime-edit-minute,
    ::-webkit-datetime-edit-month-field,
    ::-webkit-datetime-edit-text,
    ::-webkit-datetime-edit-year-field {
        padding: 0;
    }
    ::-webkit-inner-spin-button {
        height: auto;
    }
    [type="search"] {
        outline-offset: -2px;
        -webkit-appearance: textfield;
    }
    ::-webkit-search-decoration {
        -webkit-appearance: none;
    }
    ::-webkit-color-swatch-wrapper {
        padding: 0;
    }
    ::-webkit-file-upload-button {
        font: inherit;
        -webkit-appearance: button;
    }
    ::file-selector-button {
        font: inherit;
        -webkit-appearance: button;
    }
    output {
        display: inline-block;
    }
    iframe {
        border: 0;
    }
    summary {
        display: list-item;
        cursor: pointer;
    }
    progress {
        vertical-align: baseline;
    }
    [hidden] {
        display: none !important;
    }
    .lead {
        font-size: 1.25rem;
        font-weight: 300;
    }
    .display-1 {
        font-size: calc(1.625rem + 4.5vw);
        font-weight: 300;
        line-height: 1.2;
    }
    @media (min-width: 1200px) {
        .display-1 {
            font-size: 5rem;
        }
    }
    .display-2 {
        font-size: calc(1.575rem + 3.9vw);
        font-weight: 300;
        line-height: 1.2;
    }
    @media (min-width: 1200px) {
        .display-2 {
            font-size: 4.5rem;
        }
    }
    .display-3 {
        font-size: calc(1.525rem + 3.3vw);
        font-weight: 300;
        line-height: 1.2;
    }
    @media (min-width: 1200px) {
        .display-3 {
            font-size: 4rem;
        }
    }
    .display-4 {
        font-size: calc(1.475rem + 2.7vw);
        font-weight: 300;
        line-height: 1.2;
    }
    @media (min-width: 1200px) {
        .display-4 {
            font-size: 3.5rem;
        }
    }
    .display-5 {
        font-size: calc(1.425rem + 2.1vw);
        font-weight: 300;
        line-height: 1.2;
    }
    @media (min-width: 1200px) {
        .display-5 {
            font-size: 3rem;
        }
    }
    .display-6 {
        font-size: calc(1.375rem + 1.5vw);
        font-weight: 300;
        line-height: 1.2;
    }
    @media (min-width: 1200px) {
        .display-6 {
            font-size: 2.5rem;
        }
    }
    .list-unstyled {
        padding-left: 0;
        list-style: none;
    }
    .list-inline {
        padding-left: 0;
        list-style: none;
    }
    .list-inline-item {
        display: inline-block;
    }
    .list-inline-item:not(:last-child) {
        margin-right: 0.5rem;
    }
    .initialism {
        font-size: 0.875em;
        text-transform: uppercase;
    }
    .blockquote {
        margin-bottom: 1rem;
        font-size: 1.25rem;
    }
    .blockquote > :last-child {
        margin-bottom: 0;
    }
    .blockquote-footer {
        margin-top: -1rem;
        margin-bottom: 1rem;
        font-size: 0.875em;
        color: #6c757d;
    }
    .blockquote-footer::before {
        content: "— ";
    }
    .img-fluid {
        max-width: 100%;
        height: auto;
    }
    .img-thumbnail {
        padding: 0.25rem;
        background-color: var(--bs-body-bg);
        border: var(--bs-border-width) solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
        max-width: 100%;
        height: auto;
    }
    .figure {
        display: inline-block;
    }
    .figure-img {
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    .figure-caption {
        font-size: 0.875em;
        color: var(--bs-secondary-color);
    }
    .container,
    .container-fluid,
    .container-lg,
    .container-md,
    .container-sm,
    .container-xl,
    .container-xxl {
        --bs-gutter-x: 1.5rem;
        --bs-gutter-y: 0;
        width: 100%;
        padding-right: calc(var(--bs-gutter-x) * 0.5);
        padding-left: calc(var(--bs-gutter-x) * 0.5);
        margin-right: auto;
        margin-left: auto;
    }
    @media (min-width: 576px) {
        .container,
        .container-sm {
            max-width: 540px;
        }
    }
    @media (min-width: 768px) {
        .container,
        .container-md,
        .container-sm {
            max-width: 720px;
        }
    }
    @media (min-width: 992px) {
        .container,
        .container-lg,
        .container-md,
        .container-sm {
            max-width: 960px;
        }
    }
    @media (min-width: 1200px) {
        .container,
        .container-lg,
        .container-md,
        .container-sm,
        .container-xl {
            max-width: 1140px;
        }
    }
    @media (min-width: 1400px) {
        .container,
        .container-lg,
        .container-md,
        .container-sm,
        .container-xl,
        .container-xxl {
            max-width: 1320px;
        }
    }
    :root {
        --bs-breakpoint-xs: 0;
        --bs-breakpoint-sm: 576px;
        --bs-breakpoint-md: 768px;
        --bs-breakpoint-lg: 992px;
        --bs-breakpoint-xl: 1200px;
        --bs-breakpoint-xxl: 1400px;
    }
    .row {
        --bs-gutter-x: 1.5rem;
        --bs-gutter-y: 0;
        display: flex;
        flex-wrap: wrap;
        margin-top: calc(-1 * var(--bs-gutter-y));
        margin-right: calc(-0.5 * var(--bs-gutter-x));
        margin-left: calc(-0.5 * var(--bs-gutter-x));
    }
    .row > * {
        flex-shrink: 0;
        width: 100%;
        max-width: 100%;
        padding-right: calc(var(--bs-gutter-x) * 0.5);
        padding-left: calc(var(--bs-gutter-x) * 0.5);
        margin-top: var(--bs-gutter-y);
    }
    .col {
        flex: 1 0 0%;
    }
    .row-cols-auto > * {
        flex: 0 0 auto;
        width: auto;
    }
    .row-cols-1 > * {
        flex: 0 0 auto;
        width: 100%;
    }
    .row-cols-2 > * {
        flex: 0 0 auto;
        width: 50%;
    }
    .row-cols-3 > * {
        flex: 0 0 auto;
        width: 33.3333333333%;
    }
    .row-cols-4 > * {
        flex: 0 0 auto;
        width: 25%;
    }
    .row-cols-5 > * {
        flex: 0 0 auto;
        width: 20%;
    }
    .row-cols-6 > * {
        flex: 0 0 auto;
        width: 16.6666666667%;
    }
    .col-auto {
        flex: 0 0 auto;
        width: auto;
    }
    .col-1 {
        flex: 0 0 auto;
        width: 8.33333333%;
    }
    .col-2 {
        flex: 0 0 auto;
        width: 16.66666667%;
    }
    .col-3 {
        flex: 0 0 auto;
        width: 25%;
    }
    .col-4 {
        flex: 0 0 auto;
        width: 33.33333333%;
    }
    .col-5 {
        flex: 0 0 auto;
        width: 41.66666667%;
    }
    .col-6 {
        flex: 0 0 auto;
        width: 50%;
    }
    .col-7 {
        flex: 0 0 auto;
        width: 58.33333333%;
    }
    .col-8 {
        flex: 0 0 auto;
        width: 66.66666667%;
    }
    .col-9 {
        flex: 0 0 auto;
        width: 75%;
    }
    .col-10 {
        flex: 0 0 auto;
        width: 83.33333333%;
    }
    .col-11 {
        flex: 0 0 auto;
        width: 91.66666667%;
    }
    .col-12 {
        flex: 0 0 auto;
        width: 100%;
    }
    .offset-1 {
        margin-left: 8.33333333%;
    }
    .offset-2 {
        margin-left: 16.66666667%;
    }
    .offset-3 {
        margin-left: 25%;
    }
    .offset-4 {
        margin-left: 33.33333333%;
    }
    .offset-5 {
        margin-left: 41.66666667%;
    }
    .offset-6 {
        margin-left: 50%;
    }
    .offset-7 {
        margin-left: 58.33333333%;
    }
    .offset-8 {
        margin-left: 66.66666667%;
    }
    .offset-9 {
        margin-left: 75%;
    }
    .offset-10 {
        margin-left: 83.33333333%;
    }
    .offset-11 {
        margin-left: 91.66666667%;
    }
    .g-0,
    .gx-0 {
        --bs-gutter-x: 0;
    }
    .g-0,
    .gy-0 {
        --bs-gutter-y: 0;
    }
    .g-1,
    .gx-1 {
        --bs-gutter-x: 0.25rem;
    }
    .g-1,
    .gy-1 {
        --bs-gutter-y: 0.25rem;
    }
    .g-2,
    .gx-2 {
        --bs-gutter-x: 0.5rem;
    }
    .g-2,
    .gy-2 {
        --bs-gutter-y: 0.5rem;
    }
    .g-3,
    .gx-3 {
        --bs-gutter-x: 1rem;
    }
    .g-3,
    .gy-3 {
        --bs-gutter-y: 1rem;
    }
    .g-4,
    .gx-4 {
        --bs-gutter-x: 1.5rem;
    }
    .g-4,
    .gy-4 {
        --bs-gutter-y: 1.5rem;
    }
    .g-5,
    .gx-5 {
        --bs-gutter-x: 3rem;
    }
    .g-5,
    .gy-5 {
        --bs-gutter-y: 3rem;
    }
    @media (min-width: 576px) {
        .col-sm {
            flex: 1 0 0%;
        }
        .row-cols-sm-auto > * {
            flex: 0 0 auto;
            width: auto;
        }
        .row-cols-sm-1 > * {
            flex: 0 0 auto;
            width: 100%;
        }
        .row-cols-sm-2 > * {
            flex: 0 0 auto;
            width: 50%;
        }
        .row-cols-sm-3 > * {
            flex: 0 0 auto;
            width: 33.3333333333%;
        }
        .row-cols-sm-4 > * {
            flex: 0 0 auto;
            width: 25%;
        }
        .row-cols-sm-5 > * {
            flex: 0 0 auto;
            width: 20%;
        }
        .row-cols-sm-6 > * {
            flex: 0 0 auto;
            width: 16.6666666667%;
        }
        .col-sm-auto {
            flex: 0 0 auto;
            width: auto;
        }
        .col-sm-1 {
            flex: 0 0 auto;
            width: 8.33333333%;
        }
        .col-sm-2 {
            flex: 0 0 auto;
            width: 16.66666667%;
        }
        .col-sm-3 {
            flex: 0 0 auto;
            width: 25%;
        }
        .col-sm-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
        }
        .col-sm-5 {
            flex: 0 0 auto;
            width: 41.66666667%;
        }
        .col-sm-6 {
            flex: 0 0 auto;
            width: 50%;
        }
        .col-sm-7 {
            flex: 0 0 auto;
            width: 58.33333333%;
        }
        .col-sm-8 {
            flex: 0 0 auto;
            width: 66.66666667%;
        }
        .col-sm-9 {
            flex: 0 0 auto;
            width: 75%;
        }
        .col-sm-10 {
            flex: 0 0 auto;
            width: 83.33333333%;
        }
        .col-sm-11 {
            flex: 0 0 auto;
            width: 91.66666667%;
        }
        .col-sm-12 {
            flex: 0 0 auto;
            width: 100%;
        }
        .offset-sm-0 {
            margin-left: 0;
        }
        .offset-sm-1 {
            margin-left: 8.33333333%;
        }
        .offset-sm-2 {
            margin-left: 16.66666667%;
        }
        .offset-sm-3 {
            margin-left: 25%;
        }
        .offset-sm-4 {
            margin-left: 33.33333333%;
        }
        .offset-sm-5 {
            margin-left: 41.66666667%;
        }
        .offset-sm-6 {
            margin-left: 50%;
        }
        .offset-sm-7 {
            margin-left: 58.33333333%;
        }
        .offset-sm-8 {
            margin-left: 66.66666667%;
        }
        .offset-sm-9 {
            margin-left: 75%;
        }
        .offset-sm-10 {
            margin-left: 83.33333333%;
        }
        .offset-sm-11 {
            margin-left: 91.66666667%;
        }
        .g-sm-0,
        .gx-sm-0 {
            --bs-gutter-x: 0;
        }
        .g-sm-0,
        .gy-sm-0 {
            --bs-gutter-y: 0;
        }
        .g-sm-1,
        .gx-sm-1 {
            --bs-gutter-x: 0.25rem;
        }
        .g-sm-1,
        .gy-sm-1 {
            --bs-gutter-y: 0.25rem;
        }
        .g-sm-2,
        .gx-sm-2 {
            --bs-gutter-x: 0.5rem;
        }
        .g-sm-2,
        .gy-sm-2 {
            --bs-gutter-y: 0.5rem;
        }
        .g-sm-3,
        .gx-sm-3 {
            --bs-gutter-x: 1rem;
        }
        .g-sm-3,
        .gy-sm-3 {
            --bs-gutter-y: 1rem;
        }
        .g-sm-4,
        .gx-sm-4 {
            --bs-gutter-x: 1.5rem;
        }
        .g-sm-4,
        .gy-sm-4 {
            --bs-gutter-y: 1.5rem;
        }
        .g-sm-5,
        .gx-sm-5 {
            --bs-gutter-x: 3rem;
        }
        .g-sm-5,
        .gy-sm-5 {
            --bs-gutter-y: 3rem;
        }
    }
    @media (min-width: 768px) {
        .col-md {
            flex: 1 0 0%;
        }
        .row-cols-md-auto > * {
            flex: 0 0 auto;
            width: auto;
        }
        .row-cols-md-1 > * {
            flex: 0 0 auto;
            width: 100%;
        }
        .row-cols-md-2 > * {
            flex: 0 0 auto;
            width: 50%;
        }
        .row-cols-md-3 > * {
            flex: 0 0 auto;
            width: 33.3333333333%;
        }
        .row-cols-md-4 > * {
            flex: 0 0 auto;
            width: 25%;
        }
        .row-cols-md-5 > * {
            flex: 0 0 auto;
            width: 20%;
        }
        .row-cols-md-6 > * {
            flex: 0 0 auto;
            width: 16.6666666667%;
        }
        .col-md-auto {
            flex: 0 0 auto;
            width: auto;
        }
        .col-md-1 {
            flex: 0 0 auto;
            width: 8.33333333%;
        }
        .col-md-2 {
            flex: 0 0 auto;
            width: 16.66666667%;
        }
        .col-md-3 {
            flex: 0 0 auto;
            width: 25%;
        }
        .col-md-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
        }
        .col-md-5 {
            flex: 0 0 auto;
            width: 41.66666667%;
        }
        .col-md-6 {
            flex: 0 0 auto;
            width: 50%;
        }
        .col-md-7 {
            flex: 0 0 auto;
            width: 58.33333333%;
        }
        .col-md-8 {
            flex: 0 0 auto;
            width: 66.66666667%;
        }
        .col-md-9 {
            flex: 0 0 auto;
            width: 75%;
        }
        .col-md-10 {
            flex: 0 0 auto;
            width: 83.33333333%;
        }
        .col-md-11 {
            flex: 0 0 auto;
            width: 91.66666667%;
        }
        .col-md-12 {
            flex: 0 0 auto;
            width: 100%;
        }
        .offset-md-0 {
            margin-left: 0;
        }
        .offset-md-1 {
            margin-left: 8.33333333%;
        }
        .offset-md-2 {
            margin-left: 16.66666667%;
        }
        .offset-md-3 {
            margin-left: 25%;
        }
        .offset-md-4 {
            margin-left: 33.33333333%;
        }
        .offset-md-5 {
            margin-left: 41.66666667%;
        }
        .offset-md-6 {
            margin-left: 50%;
        }
        .offset-md-7 {
            margin-left: 58.33333333%;
        }
        .offset-md-8 {
            margin-left: 66.66666667%;
        }
        .offset-md-9 {
            margin-left: 75%;
        }
        .offset-md-10 {
            margin-left: 83.33333333%;
        }
        .offset-md-11 {
            margin-left: 91.66666667%;
        }
        .g-md-0,
        .gx-md-0 {
            --bs-gutter-x: 0;
        }
        .g-md-0,
        .gy-md-0 {
            --bs-gutter-y: 0;
        }
        .g-md-1,
        .gx-md-1 {
            --bs-gutter-x: 0.25rem;
        }
        .g-md-1,
        .gy-md-1 {
            --bs-gutter-y: 0.25rem;
        }
        .g-md-2,
        .gx-md-2 {
            --bs-gutter-x: 0.5rem;
        }
        .g-md-2,
        .gy-md-2 {
            --bs-gutter-y: 0.5rem;
        }
        .g-md-3,
        .gx-md-3 {
            --bs-gutter-x: 1rem;
        }
        .g-md-3,
        .gy-md-3 {
            --bs-gutter-y: 1rem;
        }
        .g-md-4,
        .gx-md-4 {
            --bs-gutter-x: 1.5rem;
        }
        .g-md-4,
        .gy-md-4 {
            --bs-gutter-y: 1.5rem;
        }
        .g-md-5,
        .gx-md-5 {
            --bs-gutter-x: 3rem;
        }
        .g-md-5,
        .gy-md-5 {
            --bs-gutter-y: 3rem;
        }
    }
    @media (min-width: 992px) {
        .col-lg {
            flex: 1 0 0%;
        }
        .row-cols-lg-auto > * {
            flex: 0 0 auto;
            width: auto;
        }
        .row-cols-lg-1 > * {
            flex: 0 0 auto;
            width: 100%;
        }
        .row-cols-lg-2 > * {
            flex: 0 0 auto;
            width: 50%;
        }
        .row-cols-lg-3 > * {
            flex: 0 0 auto;
            width: 33.3333333333%;
        }
        .row-cols-lg-4 > * {
            flex: 0 0 auto;
            width: 25%;
        }
        .row-cols-lg-5 > * {
            flex: 0 0 auto;
            width: 20%;
        }
        .row-cols-lg-6 > * {
            flex: 0 0 auto;
            width: 16.6666666667%;
        }
        .col-lg-auto {
            flex: 0 0 auto;
            width: auto;
        }
        .col-lg-1 {
            flex: 0 0 auto;
            width: 8.33333333%;
        }
        .col-lg-2 {
            flex: 0 0 auto;
            width: 16.66666667%;
        }
        .col-lg-3 {
            flex: 0 0 auto;
            width: 25%;
        }
        .col-lg-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
        }
        .col-lg-5 {
            flex: 0 0 auto;
            width: 41.66666667%;
        }
        .col-lg-6 {
            flex: 0 0 auto;
            width: 50%;
        }
        .col-lg-7 {
            flex: 0 0 auto;
            width: 58.33333333%;
        }
        .col-lg-8 {
            flex: 0 0 auto;
            width: 66.66666667%;
        }
        .col-lg-9 {
            flex: 0 0 auto;
            width: 75%;
        }
        .col-lg-10 {
            flex: 0 0 auto;
            width: 83.33333333%;
        }
        .col-lg-11 {
            flex: 0 0 auto;
            width: 91.66666667%;
        }
        .col-lg-12 {
            flex: 0 0 auto;
            width: 100%;
        }
        .offset-lg-0 {
            margin-left: 0;
        }
        .offset-lg-1 {
            margin-left: 8.33333333%;
        }
        .offset-lg-2 {
            margin-left: 16.66666667%;
        }
        .offset-lg-3 {
            margin-left: 25%;
        }
        .offset-lg-4 {
            margin-left: 33.33333333%;
        }
        .offset-lg-5 {
            margin-left: 41.66666667%;
        }
        .offset-lg-6 {
            margin-left: 50%;
        }
        .offset-lg-7 {
            margin-left: 58.33333333%;
        }
        .offset-lg-8 {
            margin-left: 66.66666667%;
        }
        .offset-lg-9 {
            margin-left: 75%;
        }
        .offset-lg-10 {
            margin-left: 83.33333333%;
        }
        .offset-lg-11 {
            margin-left: 91.66666667%;
        }
        .g-lg-0,
        .gx-lg-0 {
            --bs-gutter-x: 0;
        }
        .g-lg-0,
        .gy-lg-0 {
            --bs-gutter-y: 0;
        }
        .g-lg-1,
        .gx-lg-1 {
            --bs-gutter-x: 0.25rem;
        }
        .g-lg-1,
        .gy-lg-1 {
            --bs-gutter-y: 0.25rem;
        }
        .g-lg-2,
        .gx-lg-2 {
            --bs-gutter-x: 0.5rem;
        }
        .g-lg-2,
        .gy-lg-2 {
            --bs-gutter-y: 0.5rem;
        }
        .g-lg-3,
        .gx-lg-3 {
            --bs-gutter-x: 1rem;
        }
        .g-lg-3,
        .gy-lg-3 {
            --bs-gutter-y: 1rem;
        }
        .g-lg-4,
        .gx-lg-4 {
            --bs-gutter-x: 1.5rem;
        }
        .g-lg-4,
        .gy-lg-4 {
            --bs-gutter-y: 1.5rem;
        }
        .g-lg-5,
        .gx-lg-5 {
            --bs-gutter-x: 3rem;
        }
        .g-lg-5,
        .gy-lg-5 {
            --bs-gutter-y: 3rem;
        }
    }
    @media (min-width: 1200px) {
        .col-xl {
            flex: 1 0 0%;
        }
        .row-cols-xl-auto > * {
            flex: 0 0 auto;
            width: auto;
        }
        .row-cols-xl-1 > * {
            flex: 0 0 auto;
            width: 100%;
        }
        .row-cols-xl-2 > * {
            flex: 0 0 auto;
            width: 50%;
        }
        .row-cols-xl-3 > * {
            flex: 0 0 auto;
            width: 33.3333333333%;
        }
        .row-cols-xl-4 > * {
            flex: 0 0 auto;
            width: 25%;
        }
        .row-cols-xl-5 > * {
            flex: 0 0 auto;
            width: 20%;
        }
        .row-cols-xl-6 > * {
            flex: 0 0 auto;
            width: 16.6666666667%;
        }
        .col-xl-auto {
            flex: 0 0 auto;
            width: auto;
        }
        .col-xl-1 {
            flex: 0 0 auto;
            width: 8.33333333%;
        }
        .col-xl-2 {
            flex: 0 0 auto;
            width: 16.66666667%;
        }
        .col-xl-3 {
            flex: 0 0 auto;
            width: 25%;
        }
        .col-xl-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
        }
        .col-xl-5 {
            flex: 0 0 auto;
            width: 41.66666667%;
        }
        .col-xl-6 {
            flex: 0 0 auto;
            width: 50%;
        }
        .col-xl-7 {
            flex: 0 0 auto;
            width: 58.33333333%;
        }
        .col-xl-8 {
            flex: 0 0 auto;
            width: 66.66666667%;
        }
        .col-xl-9 {
            flex: 0 0 auto;
            width: 75%;
        }
        .col-xl-10 {
            flex: 0 0 auto;
            width: 83.33333333%;
        }
        .col-xl-11 {
            flex: 0 0 auto;
            width: 91.66666667%;
        }
        .col-xl-12 {
            flex: 0 0 auto;
            width: 100%;
        }
        .offset-xl-0 {
            margin-left: 0;
        }
        .offset-xl-1 {
            margin-left: 8.33333333%;
        }
        .offset-xl-2 {
            margin-left: 16.66666667%;
        }
        .offset-xl-3 {
            margin-left: 25%;
        }
        .offset-xl-4 {
            margin-left: 33.33333333%;
        }
        .offset-xl-5 {
            margin-left: 41.66666667%;
        }
        .offset-xl-6 {
            margin-left: 50%;
        }
        .offset-xl-7 {
            margin-left: 58.33333333%;
        }
        .offset-xl-8 {
            margin-left: 66.66666667%;
        }
        .offset-xl-9 {
            margin-left: 75%;
        }
        .offset-xl-10 {
            margin-left: 83.33333333%;
        }
        .offset-xl-11 {
            margin-left: 91.66666667%;
        }
        .g-xl-0,
        .gx-xl-0 {
            --bs-gutter-x: 0;
        }
        .g-xl-0,
        .gy-xl-0 {
            --bs-gutter-y: 0;
        }
        .g-xl-1,
        .gx-xl-1 {
            --bs-gutter-x: 0.25rem;
        }
        .g-xl-1,
        .gy-xl-1 {
            --bs-gutter-y: 0.25rem;
        }
        .g-xl-2,
        .gx-xl-2 {
            --bs-gutter-x: 0.5rem;
        }
        .g-xl-2,
        .gy-xl-2 {
            --bs-gutter-y: 0.5rem;
        }
        .g-xl-3,
        .gx-xl-3 {
            --bs-gutter-x: 1rem;
        }
        .g-xl-3,
        .gy-xl-3 {
            --bs-gutter-y: 1rem;
        }
        .g-xl-4,
        .gx-xl-4 {
            --bs-gutter-x: 1.5rem;
        }
        .g-xl-4,
        .gy-xl-4 {
            --bs-gutter-y: 1.5rem;
        }
        .g-xl-5,
        .gx-xl-5 {
            --bs-gutter-x: 3rem;
        }
        .g-xl-5,
        .gy-xl-5 {
            --bs-gutter-y: 3rem;
        }
    }
    @media (min-width: 1400px) {
        .col-xxl {
            flex: 1 0 0%;
        }
        .row-cols-xxl-auto > * {
            flex: 0 0 auto;
            width: auto;
        }
        .row-cols-xxl-1 > * {
            flex: 0 0 auto;
            width: 100%;
        }
        .row-cols-xxl-2 > * {
            flex: 0 0 auto;
            width: 50%;
        }
        .row-cols-xxl-3 > * {
            flex: 0 0 auto;
            width: 33.3333333333%;
        }
        .row-cols-xxl-4 > * {
            flex: 0 0 auto;
            width: 25%;
        }
        .row-cols-xxl-5 > * {
            flex: 0 0 auto;
            width: 20%;
        }
        .row-cols-xxl-6 > * {
            flex: 0 0 auto;
            width: 16.6666666667%;
        }
        .col-xxl-auto {
            flex: 0 0 auto;
            width: auto;
        }
        .col-xxl-1 {
            flex: 0 0 auto;
            width: 8.33333333%;
        }
        .col-xxl-2 {
            flex: 0 0 auto;
            width: 16.66666667%;
        }
        .col-xxl-3 {
            flex: 0 0 auto;
            width: 25%;
        }
        .col-xxl-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
        }
        .col-xxl-5 {
            flex: 0 0 auto;
            width: 41.66666667%;
        }
        .col-xxl-6 {
            flex: 0 0 auto;
            width: 50%;
        }
        .col-xxl-7 {
            flex: 0 0 auto;
            width: 58.33333333%;
        }
        .col-xxl-8 {
            flex: 0 0 auto;
            width: 66.66666667%;
        }
        .col-xxl-9 {
            flex: 0 0 auto;
            width: 75%;
        }
        .col-xxl-10 {
            flex: 0 0 auto;
            width: 83.33333333%;
        }
        .col-xxl-11 {
            flex: 0 0 auto;
            width: 91.66666667%;
        }
        .col-xxl-12 {
            flex: 0 0 auto;
            width: 100%;
        }
        .offset-xxl-0 {
            margin-left: 0;
        }
        .offset-xxl-1 {
            margin-left: 8.33333333%;
        }
        .offset-xxl-2 {
            margin-left: 16.66666667%;
        }
        .offset-xxl-3 {
            margin-left: 25%;
        }
        .offset-xxl-4 {
            margin-left: 33.33333333%;
        }
        .offset-xxl-5 {
            margin-left: 41.66666667%;
        }
        .offset-xxl-6 {
            margin-left: 50%;
        }
        .offset-xxl-7 {
            margin-left: 58.33333333%;
        }
        .offset-xxl-8 {
            margin-left: 66.66666667%;
        }
        .offset-xxl-9 {
            margin-left: 75%;
        }
        .offset-xxl-10 {
            margin-left: 83.33333333%;
        }
        .offset-xxl-11 {
            margin-left: 91.66666667%;
        }
        .g-xxl-0,
        .gx-xxl-0 {
            --bs-gutter-x: 0;
        }
        .g-xxl-0,
        .gy-xxl-0 {
            --bs-gutter-y: 0;
        }
        .g-xxl-1,
        .gx-xxl-1 {
            --bs-gutter-x: 0.25rem;
        }
        .g-xxl-1,
        .gy-xxl-1 {
            --bs-gutter-y: 0.25rem;
        }
        .g-xxl-2,
        .gx-xxl-2 {
            --bs-gutter-x: 0.5rem;
        }
        .g-xxl-2,
        .gy-xxl-2 {
            --bs-gutter-y: 0.5rem;
        }
        .g-xxl-3,
        .gx-xxl-3 {
            --bs-gutter-x: 1rem;
        }
        .g-xxl-3,
        .gy-xxl-3 {
            --bs-gutter-y: 1rem;
        }
        .g-xxl-4,
        .gx-xxl-4 {
            --bs-gutter-x: 1.5rem;
        }
        .g-xxl-4,
        .gy-xxl-4 {
            --bs-gutter-y: 1.5rem;
        }
        .g-xxl-5,
        .gx-xxl-5 {
            --bs-gutter-x: 3rem;
        }
        .g-xxl-5,
        .gy-xxl-5 {
            --bs-gutter-y: 3rem;
        }
    }
    .table {
        --bs-table-color: var(--bs-body-color);
        --bs-table-bg: transparent;
        --bs-table-border-color: var(--bs-border-color);
        --bs-table-accent-bg: transparent;
        --bs-table-striped-color: var(--bs-body-color);
        --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
        --bs-table-active-color: var(--bs-body-color);
        --bs-table-active-bg: rgba(0, 0, 0, 0.1);
        --bs-table-hover-color: var(--bs-body-color);
        --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
        width: 100%;
        margin-bottom: 1rem;
        color: var(--bs-table-color);
        vertical-align: top;
        border-color: var(--bs-table-border-color);
    }
    .table > :not(caption) > * > * {
        padding: 0.5rem 0.5rem;
        background-color: var(--bs-table-bg);
        border-bottom-width: var(--bs-border-width);
        box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
    }
    .table > tbody {
        vertical-align: inherit;
    }
    .table > thead {
        vertical-align: bottom;
    }
    .table-group-divider {
        border-top: calc(var(--bs-border-width) * 2) solid currentcolor;
    }
    .caption-top {
        caption-side: top;
    }
    .table-sm > :not(caption) > * > * {
        padding: 0.25rem 0.25rem;
    }
    .table-bordered > :not(caption) > * {
        border-width: var(--bs-border-width) 0;
    }
    .table-bordered > :not(caption) > * > * {
        border-width: 0 var(--bs-border-width);
    }
    .table-borderless > :not(caption) > * > * {
        border-bottom-width: 0;
    }
    .table-borderless > :not(:first-child) {
        border-top-width: 0;
    }
    .table-striped > tbody > tr:nth-of-type(odd) > * {
        --bs-table-accent-bg: var(--bs-table-striped-bg);
        color: var(--bs-table-striped-color);
    }
    .table-striped-columns > :not(caption) > tr > :nth-child(2n) {
        --bs-table-accent-bg: var(--bs-table-striped-bg);
        color: var(--bs-table-striped-color);
    }
    .table-active {
        --bs-table-accent-bg: var(--bs-table-active-bg);
        color: var(--bs-table-active-color);
    }
    .table-hover > tbody > tr:hover > * {
        --bs-table-accent-bg: var(--bs-table-hover-bg);
        color: var(--bs-table-hover-color);
    }
    .table-primary {
        --bs-table-color: #000;
        --bs-table-bg: #cfe2ff;
        --bs-table-border-color: #bacbe6;
        --bs-table-striped-bg: #c5d7f2;
        --bs-table-striped-color: #000;
        --bs-table-active-bg: #bacbe6;
        --bs-table-active-color: #000;
        --bs-table-hover-bg: #bfd1ec;
        --bs-table-hover-color: #000;
        color: var(--bs-table-color);
        border-color: var(--bs-table-border-color);
    }
    .table-secondary {
        --bs-table-color: #000;
        --bs-table-bg: #e2e3e5;
        --bs-table-border-color: #cbccce;
        --bs-table-striped-bg: #d7d8da;
        --bs-table-striped-color: #000;
        --bs-table-active-bg: #cbccce;
        --bs-table-active-color: #000;
        --bs-table-hover-bg: #d1d2d4;
        --bs-table-hover-color: #000;
        color: var(--bs-table-color);
        border-color: var(--bs-table-border-color);
    }
    .table-success {
        --bs-table-color: #000;
        --bs-table-bg: #d1e7dd;
        --bs-table-border-color: #bcd0c7;
        --bs-table-striped-bg: #c7dbd2;
        --bs-table-striped-color: #000;
        --bs-table-active-bg: #bcd0c7;
        --bs-table-active-color: #000;
        --bs-table-hover-bg: #c1d6cc;
        --bs-table-hover-color: #000;
        color: var(--bs-table-color);
        border-color: var(--bs-table-border-color);
    }
    .table-info {
        --bs-table-color: #000;
        --bs-table-bg: #cff4fc;
        --bs-table-border-color: #badce3;
        --bs-table-striped-bg: #c5e8ef;
        --bs-table-striped-color: #000;
        --bs-table-active-bg: #badce3;
        --bs-table-active-color: #000;
        --bs-table-hover-bg: #bfe2e9;
        --bs-table-hover-color: #000;
        color: var(--bs-table-color);
        border-color: var(--bs-table-border-color);
    }
    .table-warning {
        --bs-table-color: #000;
        --bs-table-bg: #fff3cd;
        --bs-table-border-color: #e6dbb9;
        --bs-table-striped-bg: #f2e7c3;
        --bs-table-striped-color: #000;
        --bs-table-active-bg: #e6dbb9;
        --bs-table-active-color: #000;
        --bs-table-hover-bg: #ece1be;
        --bs-table-hover-color: #000;
        color: var(--bs-table-color);
        border-color: var(--bs-table-border-color);
    }
    .table-danger {
        --bs-table-color: #000;
        --bs-table-bg: #f8d7da;
        --bs-table-border-color: #dfc2c4;
        --bs-table-striped-bg: #eccccf;
        --bs-table-striped-color: #000;
        --bs-table-active-bg: #dfc2c4;
        --bs-table-active-color: #000;
        --bs-table-hover-bg: #e5c7ca;
        --bs-table-hover-color: #000;
        color: var(--bs-table-color);
        border-color: var(--bs-table-border-color);
    }
    .table-light {
        --bs-table-color: #000;
        --bs-table-bg: #f8f9fa;
        --bs-table-border-color: #dfe0e1;
        --bs-table-striped-bg: #ecedee;
        --bs-table-striped-color: #000;
        --bs-table-active-bg: #dfe0e1;
        --bs-table-active-color: #000;
        --bs-table-hover-bg: #e5e6e7;
        --bs-table-hover-color: #000;
        color: var(--bs-table-color);
        border-color: var(--bs-table-border-color);
    }
    .table-dark {
        --bs-table-color: #fff;
        --bs-table-bg: #212529;
        --bs-table-border-color: #373b3e;
        --bs-table-striped-bg: #2c3034;
        --bs-table-striped-color: #fff;
        --bs-table-active-bg: #373b3e;
        --bs-table-active-color: #fff;
        --bs-table-hover-bg: #323539;
        --bs-table-hover-color: #fff;
        color: var(--bs-table-color);
        border-color: var(--bs-table-border-color);
    }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    @media (max-width: 575.98px) {
        .table-responsive-sm {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
    @media (max-width: 767.98px) {
        .table-responsive-md {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
    @media (max-width: 991.98px) {
        .table-responsive-lg {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
    @media (max-width: 1199.98px) {
        .table-responsive-xl {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
    @media (max-width: 1399.98px) {
        .table-responsive-xxl {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
    .form-label {
        margin-bottom: 0.5rem;
    }
    .col-form-label {
        padding-top: calc(0.375rem + var(--bs-border-width));
        padding-bottom: calc(0.375rem + var(--bs-border-width));
        margin-bottom: 0;
        font-size: inherit;
        line-height: 1.5;
    }
    .col-form-label-lg {
        padding-top: calc(0.5rem + var(--bs-border-width));
        padding-bottom: calc(0.5rem + var(--bs-border-width));
        font-size: 1.25rem;
    }
    .col-form-label-sm {
        padding-top: calc(0.25rem + var(--bs-border-width));
        padding-bottom: calc(0.25rem + var(--bs-border-width));
        font-size: 0.875rem;
    }
    .form-text {
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: var(--bs-secondary-color);
    }
    .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--bs-body-color);
        background-color: var(--bs-body-bg);
        background-clip: padding-box;
        border: var(--bs-border-width) solid var(--bs-border-color);
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-radius: var(--bs-border-radius);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    @media (prefers-reduced-motion: reduce) {
        .form-control {
            transition: none;
        }
    }
    .form-control[type="file"] {
        overflow: hidden;
    }
    .form-control[type="file"]:not(:disabled):not([readonly]) {
        cursor: pointer;
    }
    .form-control:focus {
        color: var(--bs-body-color);
        background-color: var(--bs-body-bg);
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-control::-webkit-date-and-time-value {
        min-width: 85px;
        height: 1.5em;
        margin: 0;
    }
    .form-control::-webkit-datetime-edit {
        display: block;
        padding: 0;
    }
    .form-control::-moz-placeholder {
        color: var(--bs-secondary-color);
        opacity: 1;
    }
    .form-control::placeholder {
        color: var(--bs-secondary-color);
        opacity: 1;
    }
    .form-control:disabled {
        background-color: var(--bs-secondary-bg);
        opacity: 1;
    }
    .form-control::-webkit-file-upload-button {
        padding: 0.375rem 0.75rem;
        margin: -0.375rem -0.75rem;
        -webkit-margin-end: 0.75rem;
        margin-inline-end: 0.75rem;
        color: var(--bs-body-color);
        background-color: var(--bs-tertiary-bg);
        pointer-events: none;
        border-color: inherit;
        border-style: solid;
        border-width: 0;
        border-inline-end-width: var(--bs-border-width);
        border-radius: 0;
        -webkit-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control::file-selector-button {
        padding: 0.375rem 0.75rem;
        margin: -0.375rem -0.75rem;
        -webkit-margin-end: 0.75rem;
        margin-inline-end: 0.75rem;
        color: var(--bs-body-color);
        background-color: var(--bs-tertiary-bg);
        pointer-events: none;
        border-color: inherit;
        border-style: solid;
        border-width: 0;
        border-inline-end-width: var(--bs-border-width);
        border-radius: 0;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    @media (prefers-reduced-motion: reduce) {
        .form-control::-webkit-file-upload-button {
            -webkit-transition: none;
            transition: none;
        }
        .form-control::file-selector-button {
            transition: none;
        }
    }
    .form-control:hover:not(:disabled):not([readonly])::-webkit-file-upload-button {
        background-color: var(--bs-secondary-bg);
    }
    .form-control:hover:not(:disabled):not([readonly])::file-selector-button {
        background-color: var(--bs-secondary-bg);
    }
    .form-control-plaintext {
        display: block;
        width: 100%;
        padding: 0.375rem 0;
        margin-bottom: 0;
        line-height: 1.5;
        color: var(--bs-body-color);
        background-color: transparent;
        border: solid transparent;
        border-width: var(--bs-border-width) 0;
    }
    .form-control-plaintext:focus {
        outline: 0;
    }
    .form-control-plaintext.form-control-lg,
    .form-control-plaintext.form-control-sm {
        padding-right: 0;
        padding-left: 0;
    }
    .form-control-sm {
        min-height: calc(1.5em + 0.5rem + calc(var(--bs-border-width) * 2));
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: var(--bs-border-radius-sm);
    }
    .form-control-sm::-webkit-file-upload-button {
        padding: 0.25rem 0.5rem;
        margin: -0.25rem -0.5rem;
        -webkit-margin-end: 0.5rem;
        margin-inline-end: 0.5rem;
    }
    .form-control-sm::file-selector-button {
        padding: 0.25rem 0.5rem;
        margin: -0.25rem -0.5rem;
        -webkit-margin-end: 0.5rem;
        margin-inline-end: 0.5rem;
    }
    .form-control-lg {
        min-height: calc(1.5em + 1rem + calc(var(--bs-border-width) * 2));
        padding: 0.5rem 1rem;
        font-size: 1.25rem;
        border-radius: var(--bs-border-radius-lg);
    }
    .form-control-lg::-webkit-file-upload-button {
        padding: 0.5rem 1rem;
        margin: -0.5rem -1rem;
        -webkit-margin-end: 1rem;
        margin-inline-end: 1rem;
    }
    .form-control-lg::file-selector-button {
        padding: 0.5rem 1rem;
        margin: -0.5rem -1rem;
        -webkit-margin-end: 1rem;
        margin-inline-end: 1rem;
    }
    textarea.form-control {
        min-height: calc(1.5em + 0.75rem + calc(var(--bs-border-width) * 2));
    }
    textarea.form-control-sm {
        min-height: calc(1.5em + 0.5rem + calc(var(--bs-border-width) * 2));
    }
    textarea.form-control-lg {
        min-height: calc(1.5em + 1rem + calc(var(--bs-border-width) * 2));
    }
    .form-control-color {
        width: 3rem;
        height: calc(1.5em + 0.75rem + calc(var(--bs-border-width) * 2));
        padding: 0.375rem;
    }
    .form-control-color:not(:disabled):not([readonly]) {
        cursor: pointer;
    }
    .form-control-color::-moz-color-swatch {
        border: 0 !important;
        border-radius: var(--bs-border-radius);
    }
    .form-control-color::-webkit-color-swatch {
        border: 0 !important;
        border-radius: var(--bs-border-radius);
    }
    .form-control-color.form-control-sm {
        height: calc(1.5em + 0.5rem + calc(var(--bs-border-width) * 2));
    }
    .form-control-color.form-control-lg {
        height: calc(1.5em + 1rem + calc(var(--bs-border-width) * 2));
    }
    .form-select {
        --bs-form-select-bg-img: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        display: block;
        width: 100%;
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--bs-body-color);
        background-color: var(--bs-body-bg);
        background-image: var(--bs-form-select-bg-img), var(--bs-form-select-bg-icon, none);
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
        border: var(--bs-border-width) solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    @media (prefers-reduced-motion: reduce) {
        .form-select {
            transition: none;
        }
    }
    .form-select:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-select[multiple],
    .form-select[size]:not([size="1"]) {
        padding-right: 0.75rem;
        background-image: none;
    }
    .form-select:disabled {
        background-color: var(--bs-secondary-bg);
    }
    .form-select:-moz-focusring {
        color: transparent;
        text-shadow: 0 0 0 var(--bs-body-color);
    }
    .form-select-sm {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        padding-left: 0.5rem;
        font-size: 0.875rem;
        border-radius: var(--bs-border-radius-sm);
    }
    .form-select-lg {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        padding-left: 1rem;
        font-size: 1.25rem;
        border-radius: var(--bs-border-radius-lg);
    }
    [data-bs-theme="dark"] .form-select {
        --bs-form-select-bg-img: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23adb5bd' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    }
    .form-check {
        display: block;
        min-height: 1.5rem;
        padding-left: 1.5em;
        margin-bottom: 0.125rem;
    }
    .form-check .form-check-input {
        float: left;
        margin-left: -1.5em;
    }
    .form-check-reverse {
        padding-right: 1.5em;
        padding-left: 0;
        text-align: right;
    }
    .form-check-reverse .form-check-input {
        float: right;
        margin-right: -1.5em;
        margin-left: 0;
    }
    .form-check-input {
        --bs-form-check-bg: var(--bs-body-bg);
        width: 1em;
        height: 1em;
        margin-top: 0.25em;
        vertical-align: top;
        background-color: var(--bs-form-check-bg);
        background-image: var(--bs-form-check-bg-image);
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        border: var(--bs-border-width) solid var(--bs-border-color);
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
        print-color-adjust: exact;
    }
    .form-check-input[type="checkbox"] {
        border-radius: 0.25em;
    }
    .form-check-input[type="radio"] {
        border-radius: 50%;
    }
    .form-check-input:active {
        filter: brightness(90%);
    }
    .form-check-input:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .form-check-input:checked[type="checkbox"] {
        --bs-form-check-bg-image: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e");
    }
    .form-check-input:checked[type="radio"] {
        --bs-form-check-bg-image: url("data:image/svg+xml,%3csvg xmlns='' viewBox='-4 -4 8 8'%3e%3ccircle r='2' fill='%23fff'/%3e%3c/svg%3e");
    }
    .form-check-input[type="checkbox"]:indeterminate {
        background-color: #0d6efd;
        border-color: #0d6efd;
        --bs-form-check-bg-image: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10h8'/%3e%3c/svg%3e");
    }
    .form-check-input:disabled {
        pointer-events: none;
        filter: none;
        opacity: 0.5;
    }
    .form-check-input:disabled ~ .form-check-label,
    .form-check-input[disabled] ~ .form-check-label {
        cursor: default;
        opacity: 0.5;
    }
    .form-switch {
        padding-left: 2.5em;
    }
    .form-switch .form-check-input {
        --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
        width: 2em;
        margin-left: -2.5em;
        background-image: var(--bs-form-switch-bg);
        background-position: left center;
        border-radius: 2em;
        transition: background-position 0.15s ease-in-out;
    }
    @media (prefers-reduced-motion: reduce) {
        .form-switch .form-check-input {
            transition: none;
        }
    }
    .form-switch .form-check-input:focus {
        --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%2386b7fe'/%3e%3c/svg%3e");
    }
    .form-switch .form-check-input:checked {
        background-position: right center;
        --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
    .form-switch.form-check-reverse {
        padding-right: 2.5em;
        padding-left: 0;
    }
    .form-switch.form-check-reverse .form-check-input {
        margin-right: -2.5em;
        margin-left: 0;
    }
    .form-check-inline {
        display: inline-block;
        margin-right: 1rem;
    }
    .btn-check {
        position: absolute;
        clip: rect(0, 0, 0, 0);
        pointer-events: none;
    }
    .btn-check:disabled + .btn,
    .btn-check[disabled] + .btn {
        pointer-events: none;
        filter: none;
        opacity: 0.65;
    }
    [data-bs-theme="dark"] .form-switch .form-check-input:not(:checked):not(:focus) {
        --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255, 255, 255, 0.25%29'/%3e%3c/svg%3e");
    }
    .form-range {
        width: 100%;
        height: 1.5rem;
        padding: 0;
        background-color: transparent;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .form-range:focus {
        outline: 0;
    }
    .form-range:focus::-webkit-slider-thumb {
        box-shadow: 0 0 0 1px #fff, 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-range:focus::-moz-range-thumb {
        box-shadow: 0 0 0 1px #fff, 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-range::-moz-focus-outer {
        border: 0;
    }
    .form-range::-webkit-slider-thumb {
        width: 1rem;
        height: 1rem;
        margin-top: -0.25rem;
        background-color: #0d6efd;
        border: 0;
        border-radius: 1rem;
        -webkit-transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        -webkit-appearance: none;
        appearance: none;
    }
    @media (prefers-reduced-motion: reduce) {
        .form-range::-webkit-slider-thumb {
            -webkit-transition: none;
            transition: none;
        }
    }
    .form-range::-webkit-slider-thumb:active {
        background-color: #b6d4fe;
    }
    .form-range::-webkit-slider-runnable-track {
        width: 100%;
        height: 0.5rem;
        color: transparent;
        cursor: pointer;
        background-color: var(--bs-tertiary-bg);
        border-color: transparent;
        border-radius: 1rem;
    }
    .form-range::-moz-range-thumb {
        width: 1rem;
        height: 1rem;
        background-color: #0d6efd;
        border: 0;
        border-radius: 1rem;
        -moz-transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        -moz-appearance: none;
        appearance: none;
    }
    @media (prefers-reduced-motion: reduce) {
        .form-range::-moz-range-thumb {
            -moz-transition: none;
            transition: none;
        }
    }
    .form-range::-moz-range-thumb:active {
        background-color: #b6d4fe;
    }
    .form-range::-moz-range-track {
        width: 100%;
        height: 0.5rem;
        color: transparent;
        cursor: pointer;
        background-color: var(--bs-tertiary-bg);
        border-color: transparent;
        border-radius: 1rem;
    }
    .form-range:disabled {
        pointer-events: none;
    }
    .form-range:disabled::-webkit-slider-thumb {
        background-color: var(--bs-secondary-color);
    }
    .form-range:disabled::-moz-range-thumb {
        background-color: var(--bs-secondary-color);
    }
    .form-floating {
        position: relative;
    }
    .form-floating > .form-control,
    .form-floating > .form-control-plaintext,
    .form-floating > .form-select {
        height: calc(3.5rem + calc(var(--bs-border-width) * 2));
        line-height: 1.25;
    }
    .form-floating > label {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 2;
        height: 100%;
        padding: 1rem 0.75rem;
        overflow: hidden;
        text-align: start;
        text-overflow: ellipsis;
        white-space: nowrap;
        pointer-events: none;
        border: var(--bs-border-width) solid transparent;
        transform-origin: 0 0;
        transition: opacity 0.1s ease-in-out, transform 0.1s ease-in-out;
    }
    @media (prefers-reduced-motion: reduce) {
        .form-floating > label {
            transition: none;
        }
    }
    .form-floating > .form-control,
    .form-floating > .form-control-plaintext {
        padding: 1rem 0.75rem;
    }
    .form-floating > .form-control-plaintext::-moz-placeholder,
    .form-floating > .form-control::-moz-placeholder {
        color: transparent;
    }
    .form-floating > .form-control-plaintext::placeholder,
    .form-floating > .form-control::placeholder {
        color: transparent;
    }
    .form-floating > .form-control-plaintext:not(:-moz-placeholder-shown),
    .form-floating > .form-control:not(:-moz-placeholder-shown) {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
    .form-floating > .form-control-plaintext:focus,
    .form-floating > .form-control-plaintext:not(:placeholder-shown),
    .form-floating > .form-control:focus,
    .form-floating > .form-control:not(:placeholder-shown) {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
    .form-floating > .form-control-plaintext:-webkit-autofill,
    .form-floating > .form-control:-webkit-autofill {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
    .form-floating > .form-select {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
    .form-floating > .form-control:not(:-moz-placeholder-shown) ~ label {
        color: rgba(var(--bs-body-color-rgb), 0.65);
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }
    .form-floating > .form-control-plaintext ~ label,
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .form-floating > .form-select ~ label {
        color: rgba(var(--bs-body-color-rgb), 0.65);
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }
    .form-floating > .form-control:not(:-moz-placeholder-shown) ~ label::after {
        position: absolute;
        inset: 1rem 0.375rem;
        z-index: -1;
        height: 1.5em;
        content: "";
        background-color: var(--bs-body-bg);
        border-radius: var(--bs-border-radius);
    }
    .form-floating > .form-control-plaintext ~ label::after,
    .form-floating > .form-control:focus ~ label::after,
    .form-floating > .form-control:not(:placeholder-shown) ~ label::after,
    .form-floating > .form-select ~ label::after {
        position: absolute;
        inset: 1rem 0.375rem;
        z-index: -1;
        height: 1.5em;
        content: "";
        background-color: var(--bs-body-bg);
        border-radius: var(--bs-border-radius);
    }
    .form-floating > .form-control:-webkit-autofill ~ label {
        color: rgba(var(--bs-body-color-rgb), 0.65);
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }
    .form-floating > .form-control-plaintext ~ label {
        border-width: var(--bs-border-width) 0;
    }
    .form-floating > :disabled ~ label {
        color: #6c757d;
    }
    .form-floating > :disabled ~ label::after {
        background-color: var(--bs-secondary-bg);
    }
    .input-group {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        width: 100%;
    }
    .input-group > .form-control,
    .input-group > .form-floating,
    .input-group > .form-select {
        position: relative;
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
    }
    .input-group > .form-control:focus,
    .input-group > .form-floating:focus-within,
    .input-group > .form-select:focus {
        z-index: 5;
    }
    .input-group .btn {
        position: relative;
        z-index: 2;
    }
    .input-group .btn:focus {
        z-index: 5;
    }
    .input-group-text {
        display: flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--bs-body-color);
        text-align: center;
        white-space: nowrap;
        background-color: var(--bs-tertiary-bg);
        border: var(--bs-border-width) solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
    }
    .input-group-lg > .btn,
    .input-group-lg > .form-control,
    .input-group-lg > .form-select,
    .input-group-lg > .input-group-text {
        padding: 0.5rem 1rem;
        font-size: 1.25rem;
        border-radius: var(--bs-border-radius-lg);
    }
    .input-group-sm > .btn,
    .input-group-sm > .form-control,
    .input-group-sm > .form-select,
    .input-group-sm > .input-group-text {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: var(--bs-border-radius-sm);
    }
    .input-group-lg > .form-select,
    .input-group-sm > .form-select {
        padding-right: 3rem;
    }
    .input-group:not(.has-validation) > .dropdown-toggle:nth-last-child(n + 3),
    .input-group:not(.has-validation) > .form-floating:not(:last-child) > .form-control,
    .input-group:not(.has-validation) > .form-floating:not(:last-child) > .form-select,
    .input-group:not(.has-validation) > :not(:last-child):not(.dropdown-toggle):not(.dropdown-menu):not(.form-floating) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .input-group.has-validation > .dropdown-toggle:nth-last-child(n + 4),
    .input-group.has-validation > .form-floating:nth-last-child(n + 3) > .form-control,
    .input-group.has-validation > .form-floating:nth-last-child(n + 3) > .form-select,
    .input-group.has-validation > :nth-last-child(n + 3):not(.dropdown-toggle):not(.dropdown-menu):not(.form-floating) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .input-group > :not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
        margin-left: calc(var(--bs-border-width) * -1);
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    .input-group > .form-floating:not(:first-child) > .form-control,
    .input-group > .form-floating:not(:first-child) > .form-select {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    .valid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: var(--bs-form-valid-color);
    }
    .valid-tooltip {
        position: absolute;
        top: 100%;
        z-index: 5;
        display: none;
        max-width: 100%;
        padding: 0.25rem 0.5rem;
        margin-top: 0.1rem;
        font-size: 0.875rem;
        color: #fff;
        background-color: var(--bs-success);
        border-radius: var(--bs-border-radius);
    }
    .is-valid ~ .valid-feedback,
    .is-valid ~ .valid-tooltip,
    .was-validated :valid ~ .valid-feedback,
    .was-validated :valid ~ .valid-tooltip {
        display: block;
    }
    .form-control.is-valid,
    .was-validated .form-control:valid {
        border-color: var(--bs-form-valid-border-color);
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    .form-control.is-valid:focus,
    .was-validated .form-control:valid:focus {
        border-color: var(--bs-form-valid-border-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-success-rgb), 0.25);
    }
    .was-validated textarea.form-control:valid,
    textarea.form-control.is-valid {
        padding-right: calc(1.5em + 0.75rem);
        background-position: top calc(0.375em + 0.1875rem) right calc(0.375em + 0.1875rem);
    }
    .form-select.is-valid,
    .was-validated .form-select:valid {
        border-color: var(--bs-form-valid-border-color);
    }
    .form-select.is-valid:not([multiple]):not([size]),
    .form-select.is-valid:not([multiple])[size="1"],
    .was-validated .form-select:valid:not([multiple]):not([size]),
    .was-validated .form-select:valid:not([multiple])[size="1"] {
        --bs-form-select-bg-icon: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        padding-right: 4.125rem;
        background-position: right 0.75rem center, center right 2.25rem;
        background-size: 16px 12px, calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    .form-select.is-valid:focus,
    .was-validated .form-select:valid:focus {
        border-color: var(--bs-form-valid-border-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-success-rgb), 0.25);
    }
    .form-control-color.is-valid,
    .was-validated .form-control-color:valid {
        width: calc(3rem + calc(1.5em + 0.75rem));
    }
    .form-check-input.is-valid,
    .was-validated .form-check-input:valid {
        border-color: var(--bs-form-valid-border-color);
    }
    .form-check-input.is-valid:checked,
    .was-validated .form-check-input:valid:checked {
        background-color: var(--bs-form-valid-color);
    }
    .form-check-input.is-valid:focus,
    .was-validated .form-check-input:valid:focus {
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-success-rgb), 0.25);
    }
    .form-check-input.is-valid ~ .form-check-label,
    .was-validated .form-check-input:valid ~ .form-check-label {
        color: var(--bs-form-valid-color);
    }
    .form-check-inline .form-check-input ~ .valid-feedback {
        margin-left: 0.5em;
    }
    .input-group > .form-control:not(:focus).is-valid,
    .input-group > .form-floating:not(:focus-within).is-valid,
    .input-group > .form-select:not(:focus).is-valid,
    .was-validated .input-group > .form-control:not(:focus):valid,
    .was-validated .input-group > .form-floating:not(:focus-within):valid,
    .was-validated .input-group > .form-select:not(:focus):valid {
        z-index: 3;
    }
    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: var(--bs-form-invalid-color);
    }
    .invalid-tooltip {
        position: absolute;
        top: 100%;
        z-index: 5;
        display: none;
        max-width: 100%;
        padding: 0.25rem 0.5rem;
        margin-top: 0.1rem;
        font-size: 0.875rem;
        color: #fff;
        background-color: var(--bs-danger);
        border-radius: var(--bs-border-radius);
    }
    .is-invalid ~ .invalid-feedback,
    .is-invalid ~ .invalid-tooltip,
    .was-validated :invalid ~ .invalid-feedback,
    .was-validated :invalid ~ .invalid-tooltip {
        display: block;
    }
    .form-control.is-invalid,
    .was-validated .form-control:invalid {
        border-color: var(--bs-form-invalid-border-color);
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    .form-control.is-invalid:focus,
    .was-validated .form-control:invalid:focus {
        border-color: var(--bs-form-invalid-border-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-danger-rgb), 0.25);
    }
    .was-validated textarea.form-control:invalid,
    textarea.form-control.is-invalid {
        padding-right: calc(1.5em + 0.75rem);
        background-position: top calc(0.375em + 0.1875rem) right calc(0.375em + 0.1875rem);
    }
    .form-select.is-invalid,
    .was-validated .form-select:invalid {
        border-color: var(--bs-form-invalid-border-color);
    }
    .form-select.is-invalid:not([multiple]):not([size]),
    .form-select.is-invalid:not([multiple])[size="1"],
    .was-validated .form-select:invalid:not([multiple]):not([size]),
    .was-validated .form-select:invalid:not([multiple])[size="1"] {
        --bs-form-select-bg-icon: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        padding-right: 4.125rem;
        background-position: right 0.75rem center, center right 2.25rem;
        background-size: 16px 12px, calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    .form-select.is-invalid:focus,
    .was-validated .form-select:invalid:focus {
        border-color: var(--bs-form-invalid-border-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-danger-rgb), 0.25);
    }
    .form-control-color.is-invalid,
    .was-validated .form-control-color:invalid {
        width: calc(3rem + calc(1.5em + 0.75rem));
    }
    .form-check-input.is-invalid,
    .was-validated .form-check-input:invalid {
        border-color: var(--bs-form-invalid-border-color);
    }
    .form-check-input.is-invalid:checked,
    .was-validated .form-check-input:invalid:checked {
        background-color: var(--bs-form-invalid-color);
    }
    .form-check-input.is-invalid:focus,
    .was-validated .form-check-input:invalid:focus {
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-danger-rgb), 0.25);
    }
    .form-check-input.is-invalid ~ .form-check-label,
    .was-validated .form-check-input:invalid ~ .form-check-label {
        color: var(--bs-form-invalid-color);
    }
    .form-check-inline .form-check-input ~ .invalid-feedback {
        margin-left: 0.5em;
    }
    .input-group > .form-control:not(:focus).is-invalid,
    .input-group > .form-floating:not(:focus-within).is-invalid,
    .input-group > .form-select:not(:focus).is-invalid,
    .was-validated .input-group > .form-control:not(:focus):invalid,
    .was-validated .input-group > .form-floating:not(:focus-within):invalid,
    .was-validated .input-group > .form-select:not(:focus):invalid {
        z-index: 4;
    }
    .btn {
        --bs-btn-padding-x: 0.75rem;
        --bs-btn-padding-y: 0.375rem;
        --bs-btn-font-family: ;
        --bs-btn-font-size: 1rem;
        --bs-btn-font-weight: 400;
        --bs-btn-line-height: 1.5;
        --bs-btn-color: var(--bs-body-color);
        --bs-btn-bg: transparent;
        --bs-btn-border-width: var(--bs-border-width);
        --bs-btn-border-color: transparent;
        --bs-btn-border-radius: var(--bs-border-radius);
        --bs-btn-hover-border-color: transparent;
        --bs-btn-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
        --bs-btn-disabled-opacity: 0.65;
        --bs-btn-focus-box-shadow: 0 0 0 0.25rem rgba(var(--bs-btn-focus-shadow-rgb), 0.5);
        display: inline-block;
        padding: var(--bs-btn-padding-y) var(--bs-btn-padding-x);
        font-family: var(--bs-btn-font-family);
        font-size: var(--bs-btn-font-size);
        font-weight: var(--bs-btn-font-weight);
        line-height: var(--bs-btn-line-height);
        color: var(--bs-btn-color);
        text-align: center;
        text-decoration: none;
        vertical-align: middle;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        border: var(--bs-btn-border-width) solid var(--bs-btn-border-color);
        border-radius: var(--bs-btn-border-radius);
        background-color: var(--bs-btn-bg);
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    @media (prefers-reduced-motion: reduce) {
        .btn {
            transition: none;
        }
    }
    .btn:hover {
        color: var(--bs-btn-hover-color);
        background-color: var(--bs-btn-hover-bg);
        border-color: var(--bs-btn-hover-border-color);
    }
    .btn-check + .btn:hover {
        color: var(--bs-btn-color);
        background-color: var(--bs-btn-bg);
        border-color: var(--bs-btn-border-color);
    }
    .btn:focus-visible {
        color: var(--bs-btn-hover-color);
        background-color: var(--bs-btn-hover-bg);
        border-color: var(--bs-btn-hover-border-color);
        outline: 0;
        box-shadow: var(--bs-btn-focus-box-shadow);
    }
    .btn-check:focus-visible + .btn {
        border-color: var(--bs-btn-hover-border-color);
        outline: 0;
        box-shadow: var(--bs-btn-focus-box-shadow);
    }
    .btn-check:checked + .btn,
    .btn.active,
    .btn.show,
    .btn:first-child:active,
    :not(.btn-check) + .btn:active {
        color: var(--bs-btn-active-color);
        background-color: var(--bs-btn-active-bg);
        border-color: var(--bs-btn-active-border-color);
    }
    .btn-check:checked + .btn:focus-visible,
    .btn.active:focus-visible,
    .btn.show:focus-visible,
    .btn:first-child:active:focus-visible,
    :not(.btn-check) + .btn:active:focus-visible {
        box-shadow: var(--bs-btn-focus-box-shadow);
    }
    .btn.disabled,
    .btn:disabled,
    fieldset:disabled .btn {
        color: var(--bs-btn-disabled-color);
        pointer-events: none;
        background-color: var(--bs-btn-disabled-bg);
        border-color: var(--bs-btn-disabled-border-color);
        opacity: var(--bs-btn-disabled-opacity);
    }
    .btn-primary {
        --bs-btn-color: #fff;
        --bs-btn-bg: #0d6efd;
        --bs-btn-border-color: #0d6efd;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #0b5ed7;
        --bs-btn-hover-border-color: #0a58ca;
        --bs-btn-focus-shadow-rgb: 49, 132, 253;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #0a58ca;
        --bs-btn-active-border-color: #0a53be;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #fff;
        --bs-btn-disabled-bg: #0d6efd;
        --bs-btn-disabled-border-color: #0d6efd;
    }
    .btn-secondary {
        --bs-btn-color: #fff;
        --bs-btn-bg: #6c757d;
        --bs-btn-border-color: #6c757d;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #5c636a;
        --bs-btn-hover-border-color: #565e64;
        --bs-btn-focus-shadow-rgb: 130, 138, 145;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #565e64;
        --bs-btn-active-border-color: #51585e;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #fff;
        --bs-btn-disabled-bg: #6c757d;
        --bs-btn-disabled-border-color: #6c757d;
    }
    .btn-success {
        --bs-btn-color: #fff;
        --bs-btn-bg: #198754;
        --bs-btn-border-color: #198754;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #157347;
        --bs-btn-hover-border-color: #146c43;
        --bs-btn-focus-shadow-rgb: 60, 153, 110;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #146c43;
        --bs-btn-active-border-color: #13653f;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #fff;
        --bs-btn-disabled-bg: #198754;
        --bs-btn-disabled-border-color: #198754;
    }
    .btn-info {
        --bs-btn-color: #000;
        --bs-btn-bg: #0dcaf0;
        --bs-btn-border-color: #0dcaf0;
        --bs-btn-hover-color: #000;
        --bs-btn-hover-bg: #31d2f2;
        --bs-btn-hover-border-color: #25cff2;
        --bs-btn-focus-shadow-rgb: 11, 172, 204;
        --bs-btn-active-color: #000;
        --bs-btn-active-bg: #3dd5f3;
        --bs-btn-active-border-color: #25cff2;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #000;
        --bs-btn-disabled-bg: #0dcaf0;
        --bs-btn-disabled-border-color: #0dcaf0;
    }
    .btn-warning {
        --bs-btn-color: #000;
        --bs-btn-bg: #ffc107;
        --bs-btn-border-color: #ffc107;
        --bs-btn-hover-color: #000;
        --bs-btn-hover-bg: #ffca2c;
        --bs-btn-hover-border-color: #ffc720;
        --bs-btn-focus-shadow-rgb: 217, 164, 6;
        --bs-btn-active-color: #000;
        --bs-btn-active-bg: #ffcd39;
        --bs-btn-active-border-color: #ffc720;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #000;
        --bs-btn-disabled-bg: #ffc107;
        --bs-btn-disabled-border-color: #ffc107;
    }
    .btn-danger {
        --bs-btn-color: #fff;
        --bs-btn-bg: #dc3545;
        --bs-btn-border-color: #dc3545;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #bb2d3b;
        --bs-btn-hover-border-color: #b02a37;
        --bs-btn-focus-shadow-rgb: 225, 83, 97;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #b02a37;
        --bs-btn-active-border-color: #a52834;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #fff;
        --bs-btn-disabled-bg: #dc3545;
        --bs-btn-disabled-border-color: #dc3545;
    }
    .btn-light {
        --bs-btn-color: #000;
        --bs-btn-bg: #f8f9fa;
        --bs-btn-border-color: #f8f9fa;
        --bs-btn-hover-color: #000;
        --bs-btn-hover-bg: #d3d4d5;
        --bs-btn-hover-border-color: #c6c7c8;
        --bs-btn-focus-shadow-rgb: 211, 212, 213;
        --bs-btn-active-color: #000;
        --bs-btn-active-bg: #c6c7c8;
        --bs-btn-active-border-color: #babbbc;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #000;
        --bs-btn-disabled-bg: #f8f9fa;
        --bs-btn-disabled-border-color: #f8f9fa;
    }
    .btn-dark {
        --bs-btn-color: #fff;
        --bs-btn-bg: #212529;
        --bs-btn-border-color: #212529;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #424649;
        --bs-btn-hover-border-color: #373b3e;
        --bs-btn-focus-shadow-rgb: 66, 70, 73;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #4d5154;
        --bs-btn-active-border-color: #373b3e;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #fff;
        --bs-btn-disabled-bg: #212529;
        --bs-btn-disabled-border-color: #212529;
    }
    .btn-outline-primary {
        --bs-btn-color: #0d6efd;
        --bs-btn-border-color: #0d6efd;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #0d6efd;
        --bs-btn-hover-border-color: #0d6efd;
        --bs-btn-focus-shadow-rgb: 13, 110, 253;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #0d6efd;
        --bs-btn-active-border-color: #0d6efd;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #0d6efd;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #0d6efd;
        --bs-gradient: none;
    }
    .btn-outline-secondary {
        --bs-btn-color: #6c757d;
        --bs-btn-border-color: #6c757d;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #6c757d;
        --bs-btn-hover-border-color: #6c757d;
        --bs-btn-focus-shadow-rgb: 108, 117, 125;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #6c757d;
        --bs-btn-active-border-color: #6c757d;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #6c757d;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #6c757d;
        --bs-gradient: none;
    }
    .btn-outline-success {
        --bs-btn-color: #198754;
        --bs-btn-border-color: #198754;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #198754;
        --bs-btn-hover-border-color: #198754;
        --bs-btn-focus-shadow-rgb: 25, 135, 84;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #198754;
        --bs-btn-active-border-color: #198754;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #198754;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #198754;
        --bs-gradient: none;
    }
    .btn-outline-info {
        --bs-btn-color: #0dcaf0;
        --bs-btn-border-color: #0dcaf0;
        --bs-btn-hover-color: #000;
        --bs-btn-hover-bg: #0dcaf0;
        --bs-btn-hover-border-color: #0dcaf0;
        --bs-btn-focus-shadow-rgb: 13, 202, 240;
        --bs-btn-active-color: #000;
        --bs-btn-active-bg: #0dcaf0;
        --bs-btn-active-border-color: #0dcaf0;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #0dcaf0;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #0dcaf0;
        --bs-gradient: none;
    }
    .btn-outline-warning {
        --bs-btn-color: #ffc107;
        --bs-btn-border-color: #ffc107;
        --bs-btn-hover-color: #000;
        --bs-btn-hover-bg: #ffc107;
        --bs-btn-hover-border-color: #ffc107;
        --bs-btn-focus-shadow-rgb: 255, 193, 7;
        --bs-btn-active-color: #000;
        --bs-btn-active-bg: #ffc107;
        --bs-btn-active-border-color: #ffc107;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #ffc107;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #ffc107;
        --bs-gradient: none;
    }
    .btn-outline-danger {
        --bs-btn-color: #dc3545;
        --bs-btn-border-color: #dc3545;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #dc3545;
        --bs-btn-hover-border-color: #dc3545;
        --bs-btn-focus-shadow-rgb: 220, 53, 69;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #dc3545;
        --bs-btn-active-border-color: #dc3545;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #dc3545;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #dc3545;
        --bs-gradient: none;
    }
    .btn-outline-light {
        --bs-btn-color: #f8f9fa;
        --bs-btn-border-color: #f8f9fa;
        --bs-btn-hover-color: #000;
        --bs-btn-hover-bg: #f8f9fa;
        --bs-btn-hover-border-color: #f8f9fa;
        --bs-btn-focus-shadow-rgb: 248, 249, 250;
        --bs-btn-active-color: #000;
        --bs-btn-active-bg: #f8f9fa;
        --bs-btn-active-border-color: #f8f9fa;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #f8f9fa;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #f8f9fa;
        --bs-gradient: none;
    }
    .btn-outline-dark {
        --bs-btn-color: #212529;
        --bs-btn-border-color: #212529;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #212529;
        --bs-btn-hover-border-color: #212529;
        --bs-btn-focus-shadow-rgb: 33, 37, 41;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #212529;
        --bs-btn-active-border-color: #212529;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #212529;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #212529;
        --bs-gradient: none;
    }
    .btn-link {
        --bs-btn-font-weight: 400;
        --bs-btn-color: var(--bs-link-color);
        --bs-btn-bg: transparent;
        --bs-btn-border-color: transparent;
        --bs-btn-hover-color: var(--bs-link-hover-color);
        --bs-btn-hover-border-color: transparent;
        --bs-btn-active-color: var(--bs-link-hover-color);
        --bs-btn-active-border-color: transparent;
        --bs-btn-disabled-color: #6c757d;
        --bs-btn-disabled-border-color: transparent;
        --bs-btn-box-shadow: 0 0 0 #000;
        --bs-btn-focus-shadow-rgb: 49, 132, 253;
        text-decoration: underline;
    }
    .btn-link:focus-visible {
        color: var(--bs-btn-color);
    }
    .btn-link:hover {
        color: var(--bs-btn-hover-color);
    }
    .btn-group-lg > .btn,
    .btn-lg {
        --bs-btn-padding-y: 0.5rem;
        --bs-btn-padding-x: 1rem;
        --bs-btn-font-size: 1.25rem;
        --bs-btn-border-radius: var(--bs-border-radius-lg);
    }
    .btn-group-sm > .btn,
    .btn-sm {
        --bs-btn-padding-y: 0.25rem;
        --bs-btn-padding-x: 0.5rem;
        --bs-btn-font-size: 0.875rem;
        --bs-btn-border-radius: var(--bs-border-radius-sm);
    }
    .fade {
        transition: opacity 0.15s linear;
    }
    @media (prefers-reduced-motion: reduce) {
        .fade {
            transition: none;
        }
    }
    .fade:not(.show) {
        opacity: 0;
    }
    .collapse:not(.show) {
        display: none;
    }
    .collapsing {
        height: 0;
        overflow: hidden;
        transition: height 0.35s ease;
    }
    @media (prefers-reduced-motion: reduce) {
        .collapsing {
            transition: none;
        }
    }
    .collapsing.collapse-horizontal {
        width: 0;
        height: auto;
        transition: width 0.35s ease;
    }
    @media (prefers-reduced-motion: reduce) {
        .collapsing.collapse-horizontal {
            transition: none;
        }
    }
    .dropdown,
    .dropdown-center,
    .dropend,
    .dropstart,
    .dropup,
    .dropup-center {
        position: relative;
    }
    .dropdown-toggle {
        white-space: nowrap;
    }
    .dropdown-toggle::after {
        display: inline-block;
        margin-left: 0.255em;
        vertical-align: 0.255em;
        content: "";
        border-top: 0.3em solid;
        border-right: 0.3em solid transparent;
        border-bottom: 0;
        border-left: 0.3em solid transparent;
    }
    .dropdown-toggle:empty::after {
        margin-left: 0;
    }
    .dropdown-menu {
        --bs-dropdown-zindex: 1000;
        --bs-dropdown-min-width: 10rem;
        --bs-dropdown-padding-x: 0;
        --bs-dropdown-padding-y: 0.5rem;
        --bs-dropdown-spacer: 0.125rem;
        --bs-dropdown-font-size: 1rem;
        --bs-dropdown-color: var(--bs-body-color);
        --bs-dropdown-bg: var(--bs-body-bg);
        --bs-dropdown-border-color: var(--bs-border-color-translucent);
        --bs-dropdown-border-radius: var(--bs-border-radius);
        --bs-dropdown-border-width: var(--bs-border-width);
        --bs-dropdown-inner-border-radius: calc(var(--bs-border-radius) - var(--bs-border-width));
        --bs-dropdown-divider-bg: var(--bs-border-color-translucent);
        --bs-dropdown-divider-margin-y: 0.5rem;
        --bs-dropdown-box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        --bs-dropdown-link-color: var(--bs-body-color);
        --bs-dropdown-link-hover-color: var(--bs-body-color);
        --bs-dropdown-link-hover-bg: var(--bs-tertiary-bg);
        --bs-dropdown-link-active-color: #fff;
        --bs-dropdown-link-active-bg: #0d6efd;
        --bs-dropdown-link-disabled-color: var(--bs-tertiary-color);
        --bs-dropdown-item-padding-x: 1rem;
        --bs-dropdown-item-padding-y: 0.25rem;
        --bs-dropdown-header-color: #6c757d;
        --bs-dropdown-header-padding-x: 1rem;
        --bs-dropdown-header-padding-y: 0.5rem;
        position: absolute;
        z-index: var(--bs-dropdown-zindex);
        display: none;
        min-width: var(--bs-dropdown-min-width);
        padding: var(--bs-dropdown-padding-y) var(--bs-dropdown-padding-x);
        margin: 0;
        font-size: var(--bs-dropdown-font-size);
        color: var(--bs-dropdown-color);
        text-align: left;
        list-style: none;
        background-color: var(--bs-dropdown-bg);
        background-clip: padding-box;
        border: var(--bs-dropdown-border-width) solid var(--bs-dropdown-border-color);
        border-radius: var(--bs-dropdown-border-radius);
    }
    .dropdown-menu[data-bs-popper] {
        top: 100%;
        left: 0;
        margin-top: var(--bs-dropdown-spacer);
    }
    .dropdown-menu-start {
        --bs-position: start;
    }
    .dropdown-menu-start[data-bs-popper] {
        right: auto;
        left: 0;
    }
    .dropdown-menu-end {
        --bs-position: end;
    }
    .dropdown-menu-end[data-bs-popper] {
        right: 0;
        left: auto;
    }
    @media (min-width: 576px) {
        .dropdown-menu-sm-start {
            --bs-position: start;
        }
        .dropdown-menu-sm-start[data-bs-popper] {
            right: auto;
            left: 0;
        }
        .dropdown-menu-sm-end {
            --bs-position: end;
        }
        .dropdown-menu-sm-end[data-bs-popper] {
            right: 0;
            left: auto;
        }
    }
    @media (min-width: 768px) {
        .dropdown-menu-md-start {
            --bs-position: start;
        }
        .dropdown-menu-md-start[data-bs-popper] {
            right: auto;
            left: 0;
        }
        .dropdown-menu-md-end {
            --bs-position: end;
        }
        .dropdown-menu-md-end[data-bs-popper] {
            right: 0;
            left: auto;
        }
    }
    @media (min-width: 992px) {
        .dropdown-menu-lg-start {
            --bs-position: start;
        }
        .dropdown-menu-lg-start[data-bs-popper] {
            right: auto;
            left: 0;
        }
        .dropdown-menu-lg-end {
            --bs-position: end;
        }
        .dropdown-menu-lg-end[data-bs-popper] {
            right: 0;
            left: auto;
        }
    }
    @media (min-width: 1200px) {
        .dropdown-menu-xl-start {
            --bs-position: start;
        }
        .dropdown-menu-xl-start[data-bs-popper] {
            right: auto;
            left: 0;
        }
        .dropdown-menu-xl-end {
            --bs-position: end;
        }
        .dropdown-menu-xl-end[data-bs-popper] {
            right: 0;
            left: auto;
        }
    }
    @media (min-width: 1400px) {
        .dropdown-menu-xxl-start {
            --bs-position: start;
        }
        .dropdown-menu-xxl-start[data-bs-popper] {
            right: auto;
            left: 0;
        }
        .dropdown-menu-xxl-end {
            --bs-position: end;
        }
        .dropdown-menu-xxl-end[data-bs-popper] {
            right: 0;
            left: auto;
        }
    }
    .dropup .dropdown-menu[data-bs-popper] {
        top: auto;
        bottom: 100%;
        margin-top: 0;
        margin-bottom: var(--bs-dropdown-spacer);
    }
    .dropup .dropdown-toggle::after {
        display: inline-block;
        margin-left: 0.255em;
        vertical-align: 0.255em;
        content: "";
        border-top: 0;
        border-right: 0.3em solid transparent;
        border-bottom: 0.3em solid;
        border-left: 0.3em solid transparent;
    }
    .dropup .dropdown-toggle:empty::after {
        margin-left: 0;
    }
    .dropend .dropdown-menu[data-bs-popper] {
        top: 0;
        right: auto;
        left: 100%;
        margin-top: 0;
        margin-left: var(--bs-dropdown-spacer);
    }
    .dropend .dropdown-toggle::after {
        display: inline-block;
        margin-left: 0.255em;
        vertical-align: 0.255em;
        content: "";
        border-top: 0.3em solid transparent;
        border-right: 0;
        border-bottom: 0.3em solid transparent;
        border-left: 0.3em solid;
    }
    .dropend .dropdown-toggle:empty::after {
        margin-left: 0;
    }
    .dropend .dropdown-toggle::after {
        vertical-align: 0;
    }
    .dropstart .dropdown-menu[data-bs-popper] {
        top: 0;
        right: 100%;
        left: auto;
        margin-top: 0;
        margin-right: var(--bs-dropdown-spacer);
    }
    .dropstart .dropdown-toggle::after {
        display: inline-block;
        margin-left: 0.255em;
        vertical-align: 0.255em;
        content: "";
    }
    .dropstart .dropdown-toggle::after {
        display: none;
    }
    .dropstart .dropdown-toggle::before {
        display: inline-block;
        margin-right: 0.255em;
        vertical-align: 0.255em;
        content: "";
        border-top: 0.3em solid transparent;
        border-right: 0.3em solid;
        border-bottom: 0.3em solid transparent;
    }
    .dropstart .dropdown-toggle:empty::after {
        margin-left: 0;
    }
    .dropstart .dropdown-toggle::before {
        vertical-align: 0;
    }
    .dropdown-divider {
        height: 0;
        margin: var(--bs-dropdown-divider-margin-y) 0;
        overflow: hidden;
        border-top: 1px solid var(--bs-dropdown-divider-bg);
        opacity: 1;
    }
    .dropdown-item {
        display: block;
        width: 100%;
        padding: var(--bs-dropdown-item-padding-y) var(--bs-dropdown-item-padding-x);
        clear: both;
        font-weight: 400;
        color: var(--bs-dropdown-link-color);
        text-align: inherit;
        text-decoration: none;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        border-radius: var(--bs-dropdown-item-border-radius, 0);
    }
    .dropdown-item:focus,
    .dropdown-item:hover {
        color: var(--bs-dropdown-link-hover-color);
        background-color: var(--bs-dropdown-link-hover-bg);
    }
    .dropdown-item.active,
    .dropdown-item:active {
        color: var(--bs-dropdown-link-active-color);
        text-decoration: none;
        background-color: var(--bs-dropdown-link-active-bg);
    }
    .dropdown-item.disabled,
    .dropdown-item:disabled {
        color: var(--bs-dropdown-link-disabled-color);
        pointer-events: none;
        background-color: transparent;
    }
    .dropdown-menu.show {
        display: block;
    }
    .dropdown-header {
        display: block;
        padding: var(--bs-dropdown-header-padding-y) var(--bs-dropdown-header-padding-x);
        margin-bottom: 0;
        font-size: 0.875rem;
        color: var(--bs-dropdown-header-color);
        white-space: nowrap;
    }
    .dropdown-item-text {
        display: block;
        padding: var(--bs-dropdown-item-padding-y) var(--bs-dropdown-item-padding-x);
        color: var(--bs-dropdown-link-color);
    }
    .dropdown-menu-dark {
        --bs-dropdown-color: #dee2e6;
        --bs-dropdown-bg: #343a40;
        --bs-dropdown-border-color: var(--bs-border-color-translucent);
        --bs-dropdown-box-shadow: ;
        --bs-dropdown-link-color: #dee2e6;
        --bs-dropdown-link-hover-color: #fff;
        --bs-dropdown-divider-bg: var(--bs-border-color-translucent);
        --bs-dropdown-link-hover-bg: rgba(255, 255, 255, 0.15);
        --bs-dropdown-link-active-color: #fff;
        --bs-dropdown-link-active-bg: #0d6efd;
        --bs-dropdown-link-disabled-color: #adb5bd;
        --bs-dropdown-header-color: #adb5bd;
    }
    .btn-group,
    .btn-group-vertical {
        position: relative;
        display: inline-flex;
        vertical-align: middle;
    }
    .btn-group-vertical > .btn,
    .btn-group > .btn {
        position: relative;
        flex: 1 1 auto;
    }
    .btn-group-vertical > .btn-check:checked + .btn,
    .btn-group-vertical > .btn-check:focus + .btn,
    .btn-group-vertical > .btn.active,
    .btn-group-vertical > .btn:active,
    .btn-group-vertical > .btn:focus,
    .btn-group-vertical > .btn:hover,
    .btn-group > .btn-check:checked + .btn,
    .btn-group > .btn-check:focus + .btn,
    .btn-group > .btn.active,
    .btn-group > .btn:active,
    .btn-group > .btn:focus,
    .btn-group > .btn:hover {
        z-index: 1;
    }
    .btn-toolbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
    }
    .btn-toolbar .input-group {
        width: auto;
    }
    .btn-group {
        border-radius: var(--bs-border-radius);
    }
    .btn-group > .btn-group:not(:first-child),
    .btn-group > :not(.btn-check:first-child) + .btn {
        margin-left: calc(var(--bs-border-width) * -1);
    }
    .btn-group > .btn-group:not(:last-child) > .btn,
    .btn-group > .btn.dropdown-toggle-split:first-child,
    .btn-group > .btn:not(:last-child):not(.dropdown-toggle) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .btn-group > .btn-group:not(:first-child) > .btn,
    .btn-group > .btn:nth-child(n + 3),
    .btn-group > :not(.btn-check) + .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    .dropdown-toggle-split {
        padding-right: 0.5625rem;
        padding-left: 0.5625rem;
    }
    .dropdown-toggle-split::after,
    .dropend .dropdown-toggle-split::after,
    .dropup .dropdown-toggle-split::after {
        margin-left: 0;
    }
    .dropstart .dropdown-toggle-split::before {
        margin-right: 0;
    }
    .btn-group-sm > .btn + .dropdown-toggle-split,
    .btn-sm + .dropdown-toggle-split {
        padding-right: 0.375rem;
        padding-left: 0.375rem;
    }
    .btn-group-lg > .btn + .dropdown-toggle-split,
    .btn-lg + .dropdown-toggle-split {
        padding-right: 0.75rem;
        padding-left: 0.75rem;
    }
    .btn-group-vertical {
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
    }
    .btn-group-vertical > .btn,
    .btn-group-vertical > .btn-group {
        width: 100%;
    }
    .btn-group-vertical > .btn-group:not(:first-child),
    .btn-group-vertical > .btn:not(:first-child) {
        margin-top: calc(var(--bs-border-width) * -1);
    }
    .btn-group-vertical > .btn-group:not(:last-child) > .btn,
    .btn-group-vertical > .btn:not(:last-child):not(.dropdown-toggle) {
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .btn-group-vertical > .btn-group:not(:first-child) > .btn,
    .btn-group-vertical > .btn ~ .btn {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    .nav {
        --bs-nav-link-padding-x: 1rem;
        --bs-nav-link-padding-y: 0.5rem;
        --bs-nav-link-font-weight: ;
        --bs-nav-link-color: var(--bs-link-color);
        --bs-nav-link-hover-color: var(--bs-link-hover-color);
        --bs-nav-link-disabled-color: var(--bs-secondary-color);
        display: flex;
        flex-wrap: wrap;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
    }
    .nav-link {
        display: block;
        padding: var(--bs-nav-link-padding-y) var(--bs-nav-link-padding-x);
        font-size: var(--bs-nav-link-font-size);
        font-weight: var(--bs-nav-link-font-weight);
        color: var(--bs-nav-link-color);
        text-decoration: none;
        background: 0 0;
        border: 0;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
    }
    @media (prefers-reduced-motion: reduce) {
        .nav-link {
            transition: none;
        }
    }
    .nav-link:focus,
    .nav-link:hover {
        color: var(--bs-nav-link-hover-color);
    }
    .nav-link:focus-visible {
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .nav-link.disabled {
        color: var(--bs-nav-link-disabled-color);
        pointer-events: none;
        cursor: default;
    }
    .nav-tabs {
        --bs-nav-tabs-border-width: var(--bs-border-width);
        --bs-nav-tabs-border-color: var(--bs-border-color);
        --bs-nav-tabs-border-radius: var(--bs-border-radius);
        --bs-nav-tabs-link-hover-border-color: var(--bs-secondary-bg) var(--bs-secondary-bg) var(--bs-border-color);
        --bs-nav-tabs-link-active-color: var(--bs-emphasis-color);
        --bs-nav-tabs-link-active-bg: var(--bs-body-bg);
        --bs-nav-tabs-link-active-border-color: var(--bs-border-color) var(--bs-border-color) var(--bs-body-bg);
        border-bottom: var(--bs-nav-tabs-border-width) solid var(--bs-nav-tabs-border-color);
    }
    .nav-tabs .nav-link {
        margin-bottom: calc(-1 * var(--bs-nav-tabs-border-width));
        border: var(--bs-nav-tabs-border-width) solid transparent;
        border-top-left-radius: var(--bs-nav-tabs-border-radius);
        border-top-right-radius: var(--bs-nav-tabs-border-radius);
    }
    .nav-tabs .nav-link:focus,
    .nav-tabs .nav-link:hover {
        isolation: isolate;
        border-color: var(--bs-nav-tabs-link-hover-border-color);
    }
    .nav-tabs .nav-link.disabled,
    .nav-tabs .nav-link:disabled {
        color: var(--bs-nav-link-disabled-color);
        background-color: transparent;
        border-color: transparent;
    }
    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: var(--bs-nav-tabs-link-active-color);
        background-color: var(--bs-nav-tabs-link-active-bg);
        border-color: var(--bs-nav-tabs-link-active-border-color);
    }
    .nav-tabs .dropdown-menu {
        margin-top: calc(-1 * var(--bs-nav-tabs-border-width));
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    .nav-pills {
        --bs-nav-pills-border-radius: var(--bs-border-radius);
        --bs-nav-pills-link-active-color: #fff;
        --bs-nav-pills-link-active-bg: #0d6efd;
    }
    .nav-pills .nav-link {
        border-radius: var(--bs-nav-pills-border-radius);
    }
    .nav-pills .nav-link:disabled {
        color: var(--bs-nav-link-disabled-color);
        background-color: transparent;
        border-color: transparent;
    }
    .nav-pills .nav-link.active,
    .nav-pills .show > .nav-link {
        color: var(--bs-nav-pills-link-active-color);
        background-color: var(--bs-nav-pills-link-active-bg);
    }
    .nav-underline {
        --bs-nav-underline-gap: 1rem;
        --bs-nav-underline-border-width: 0.125rem;
        --bs-nav-underline-link-active-color: var(--bs-emphasis-color);
        gap: var(--bs-nav-underline-gap);
    }
    .nav-underline .nav-link {
        padding-right: 0;
        padding-left: 0;
        border-bottom: var(--bs-nav-underline-border-width) solid transparent;
    }
    .nav-underline .nav-link:focus,
    .nav-underline .nav-link:hover {
        border-bottom-color: currentcolor;
    }
    .nav-underline .nav-link.active,
    .nav-underline .show > .nav-link {
        font-weight: 700;
        color: var(--bs-nav-underline-link-active-color);
        border-bottom-color: currentcolor;
    }
    .nav-fill .nav-item,
    .nav-fill > .nav-link {
        flex: 1 1 auto;
        text-align: center;
    }
    .nav-justified .nav-item,
    .nav-justified > .nav-link {
        flex-basis: 0;
        flex-grow: 1;
        text-align: center;
    }
    .nav-fill .nav-item .nav-link,
    .nav-justified .nav-item .nav-link {
        width: 100%;
    }
    .tab-content > .tab-pane {
        display: none;
    }
    .tab-content > .active {
        display: block;
    }
    .navbar {
        --bs-navbar-padding-x: 0;
        --bs-navbar-padding-y: 0.5rem;
        --bs-navbar-color: rgba(var(--bs-emphasis-color-rgb), 0.65);
        --bs-navbar-hover-color: rgba(var(--bs-emphasis-color-rgb), 0.8);
        --bs-navbar-disabled-color: rgba(var(--bs-emphasis-color-rgb), 0.3);
        --bs-navbar-active-color: rgba(var(--bs-emphasis-color-rgb), 1);
        --bs-navbar-brand-padding-y: 0.3125rem;
        --bs-navbar-brand-margin-end: 1rem;
        --bs-navbar-brand-font-size: 1.25rem;
        --bs-navbar-brand-color: rgba(var(--bs-emphasis-color-rgb), 1);
        --bs-navbar-brand-hover-color: rgba(var(--bs-emphasis-color-rgb), 1);
        --bs-navbar-nav-link-padding-x: 0.5rem;
        --bs-navbar-toggler-padding-y: 0.25rem;
        --bs-navbar-toggler-padding-x: 0.75rem;
        --bs-navbar-toggler-font-size: 1.25rem;
        --bs-navbar-toggler-icon-bg: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        --bs-navbar-toggler-border-color: rgba(var(--bs-emphasis-color-rgb), 0.15);
        --bs-navbar-toggler-border-radius: var(--bs-border-radius);
        --bs-navbar-toggler-focus-width: 0.25rem;
        --bs-navbar-toggler-transition: box-shadow 0.15s ease-in-out;
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        padding: var(--bs-navbar-padding-y) var(--bs-navbar-padding-x);
    }
    .navbar > .container,
    .navbar > .container-fluid,
    .navbar > .container-lg,
    .navbar > .container-md,
    .navbar > .container-sm,
    .navbar > .container-xl,
    .navbar > .container-xxl {
        display: flex;
        flex-wrap: inherit;
        align-items: center;
        justify-content: space-between;
    }
    .navbar-brand {
        padding-top: var(--bs-navbar-brand-padding-y);
        padding-bottom: var(--bs-navbar-brand-padding-y);
        margin-right: var(--bs-navbar-brand-margin-end);
        font-size: var(--bs-navbar-brand-font-size);
        color: var(--bs-navbar-brand-color);
        text-decoration: none;
        white-space: nowrap;
    }
    .navbar-brand:focus,
    .navbar-brand:hover {
        color: var(--bs-navbar-brand-hover-color);
    }
    .navbar-nav {
        --bs-nav-link-padding-x: 0;
        --bs-nav-link-padding-y: 0.5rem;
        --bs-nav-link-font-weight: ;
        --bs-nav-link-color: var(--bs-navbar-color);
        --bs-nav-link-hover-color: var(--bs-navbar-hover-color);
        --bs-nav-link-disabled-color: var(--bs-navbar-disabled-color);
        display: flex;
        flex-direction: column;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
    }
    .navbar-nav .nav-link.active,
    .navbar-nav .nav-link.show {
        color: var(--bs-navbar-active-color);
    }
    .navbar-nav .dropdown-menu {
        position: static;
    }
    .navbar-text {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        color: var(--bs-navbar-color);
    }
    .navbar-text a,
    .navbar-text a:focus,
    .navbar-text a:hover {
        color: var(--bs-navbar-active-color);
    }
    .navbar-collapse {
        flex-basis: 100%;
        flex-grow: 1;
        align-items: center;
    }
    .navbar-toggler {
        padding: var(--bs-navbar-toggler-padding-y) var(--bs-navbar-toggler-padding-x);
        font-size: var(--bs-navbar-toggler-font-size);
        line-height: 1;
        color: var(--bs-navbar-color);
        background-color: transparent;
        border: var(--bs-border-width) solid var(--bs-navbar-toggler-border-color);
        border-radius: var(--bs-navbar-toggler-border-radius);
        transition: var(--bs-navbar-toggler-transition);
    }
    @media (prefers-reduced-motion: reduce) {
        .navbar-toggler {
            transition: none;
        }
    }
    .navbar-toggler:hover {
        text-decoration: none;
    }
    .navbar-toggler:focus {
        text-decoration: none;
        outline: 0;
        box-shadow: 0 0 0 var(--bs-navbar-toggler-focus-width);
    }
    .navbar-toggler-icon {
        display: inline-block;
        width: 1.5em;
        height: 1.5em;
        vertical-align: middle;
        background-image: var(--bs-navbar-toggler-icon-bg);
        background-repeat: no-repeat;
        background-position: center;
        background-size: 100%;
    }
    .navbar-nav-scroll {
        max-height: var(--bs-scroll-height, 75vh);
        overflow-y: auto;
    }
    @media (min-width: 576px) {
        .navbar-expand-sm {
            flex-wrap: nowrap;
            justify-content: flex-start;
        }
        .navbar-expand-sm .navbar-nav {
            flex-direction: row;
        }
        .navbar-expand-sm .navbar-nav .dropdown-menu {
            position: absolute;
        }
        .navbar-expand-sm .navbar-nav .nav-link {
            padding-right: var(--bs-navbar-nav-link-padding-x);
            padding-left: var(--bs-navbar-nav-link-padding-x);
        }
        .navbar-expand-sm .navbar-nav-scroll {
            overflow: visible;
        }
        .navbar-expand-sm .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }
        .navbar-expand-sm .navbar-toggler {
            display: none;
        }
        .navbar-expand-sm .offcanvas {
            position: static;
            z-index: auto;
            flex-grow: 1;
            width: auto !important;
            height: auto !important;
            visibility: visible !important;
            background-color: transparent !important;
            border: 0 !important;
            transform: none !important;
            transition: none;
        }
        .navbar-expand-sm .offcanvas .offcanvas-header {
            display: none;
        }
        .navbar-expand-sm .offcanvas .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
        }
    }
    @media (min-width: 768px) {
        .navbar-expand-md {
            flex-wrap: nowrap;
            justify-content: flex-start;
        }
        .navbar-expand-md .navbar-nav {
            flex-direction: row;
        }
        .navbar-expand-md .navbar-nav .dropdown-menu {
            position: absolute;
        }
        .navbar-expand-md .navbar-nav .nav-link {
            padding-right: var(--bs-navbar-nav-link-padding-x);
            padding-left: var(--bs-navbar-nav-link-padding-x);
        }
        .navbar-expand-md .navbar-nav-scroll {
            overflow: visible;
        }
        .navbar-expand-md .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }
        .navbar-expand-md .navbar-toggler {
            display: none;
        }
        .navbar-expand-md .offcanvas {
            position: static;
            z-index: auto;
            flex-grow: 1;
            width: auto !important;
            height: auto !important;
            visibility: visible !important;
            background-color: transparent !important;
            border: 0 !important;
            transform: none !important;
            transition: none;
        }
        .navbar-expand-md .offcanvas .offcanvas-header {
            display: none;
        }
        .navbar-expand-md .offcanvas .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
        }
    }
    @media (min-width: 992px) {
        .navbar-expand-lg {
            flex-wrap: nowrap;
            justify-content: flex-start;
        }
        .navbar-expand-lg .navbar-nav {
            flex-direction: row;
        }
        .navbar-expand-lg .navbar-nav .dropdown-menu {
            position: absolute;
        }
        .navbar-expand-lg .navbar-nav .nav-link {
            padding-right: var(--bs-navbar-nav-link-padding-x);
            padding-left: var(--bs-navbar-nav-link-padding-x);
        }
        .navbar-expand-lg .navbar-nav-scroll {
            overflow: visible;
        }
        .navbar-expand-lg .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }
        .navbar-expand-lg .navbar-toggler {
            display: none;
        }
        .navbar-expand-lg .offcanvas {
            position: static;
            z-index: auto;
            flex-grow: 1;
            width: auto !important;
            height: auto !important;
            visibility: visible !important;
            background-color: transparent !important;
            border: 0 !important;
            transform: none !important;
            transition: none;
        }
        .navbar-expand-lg .offcanvas .offcanvas-header {
            display: none;
        }
        .navbar-expand-lg .offcanvas .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
        }
    }
    @media (min-width: 1200px) {
        .navbar-expand-xl {
            flex-wrap: nowrap;
            justify-content: flex-start;
        }
        .navbar-expand-xl .navbar-nav {
            flex-direction: row;
        }
        .navbar-expand-xl .navbar-nav .dropdown-menu {
            position: absolute;
        }
        .navbar-expand-xl .navbar-nav .nav-link {
            padding-right: var(--bs-navbar-nav-link-padding-x);
            padding-left: var(--bs-navbar-nav-link-padding-x);
        }
        .navbar-expand-xl .navbar-nav-scroll {
            overflow: visible;
        }
        .navbar-expand-xl .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }
        .navbar-expand-xl .navbar-toggler {
            display: none;
        }
        .navbar-expand-xl .offcanvas {
            position: static;
            z-index: auto;
            flex-grow: 1;
            width: auto !important;
            height: auto !important;
            visibility: visible !important;
            background-color: transparent !important;
            border: 0 !important;
            transform: none !important;
            transition: none;
        }
        .navbar-expand-xl .offcanvas .offcanvas-header {
            display: none;
        }
        .navbar-expand-xl .offcanvas .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
        }
    }
    @media (min-width: 1400px) {
        .navbar-expand-xxl {
            flex-wrap: nowrap;
            justify-content: flex-start;
        }
        .navbar-expand-xxl .navbar-nav {
            flex-direction: row;
        }
        .navbar-expand-xxl .navbar-nav .dropdown-menu {
            position: absolute;
        }
        .navbar-expand-xxl .navbar-nav .nav-link {
            padding-right: var(--bs-navbar-nav-link-padding-x);
            padding-left: var(--bs-navbar-nav-link-padding-x);
        }
        .navbar-expand-xxl .navbar-nav-scroll {
            overflow: visible;
        }
        .navbar-expand-xxl .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }
        .navbar-expand-xxl .navbar-toggler {
            display: none;
        }
        .navbar-expand-xxl .offcanvas {
            position: static;
            z-index: auto;
            flex-grow: 1;
            width: auto !important;
            height: auto !important;
            visibility: visible !important;
            background-color: transparent !important;
            border: 0 !important;
            transform: none !important;
            transition: none;
        }
        .navbar-expand-xxl .offcanvas .offcanvas-header {
            display: none;
        }
        .navbar-expand-xxl .offcanvas .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
        }
    }
    .navbar-expand {
        flex-wrap: nowrap;
        justify-content: flex-start;
    }
    .navbar-expand .navbar-nav {
        flex-direction: row;
    }
    .navbar-expand .navbar-nav .dropdown-menu {
        position: absolute;
    }
    .navbar-expand .navbar-nav .nav-link {
        padding-right: var(--bs-navbar-nav-link-padding-x);
        padding-left: var(--bs-navbar-nav-link-padding-x);
    }
    .navbar-expand .navbar-nav-scroll {
        overflow: visible;
    }
    .navbar-expand .navbar-collapse {
        display: flex !important;
        flex-basis: auto;
    }
    .navbar-expand .navbar-toggler {
        display: none;
    }
    .navbar-expand .offcanvas {
        position: static;
        z-index: auto;
        flex-grow: 1;
        width: auto !important;
        height: auto !important;
        visibility: visible !important;
        background-color: transparent !important;
        border: 0 !important;
        transform: none !important;
        transition: none;
    }
    .navbar-expand .offcanvas .offcanvas-header {
        display: none;
    }
    .navbar-expand .offcanvas .offcanvas-body {
        display: flex;
        flex-grow: 0;
        padding: 0;
        overflow-y: visible;
    }
    .navbar-dark {
        --bs-navbar-color: rgba(255, 255, 255, 0.55);
        --bs-navbar-hover-color: rgba(255, 255, 255, 0.75);
        --bs-navbar-disabled-color: rgba(255, 255, 255, 0.25);
        --bs-navbar-active-color: #fff;
        --bs-navbar-brand-color: #fff;
        --bs-navbar-brand-hover-color: #fff;
        --bs-navbar-toggler-border-color: rgba(255, 255, 255, 0.1);
        --bs-navbar-toggler-icon-bg: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.55%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    [data-bs-theme="dark"] .navbar-toggler-icon {
        --bs-navbar-toggler-icon-bg: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.55%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    .card {
        --bs-card-spacer-y: 1rem;
        --bs-card-spacer-x: 1rem;
        --bs-card-title-spacer-y: 0.5rem;
        --bs-card-title-color: ;
        --bs-card-subtitle-color: ;
        --bs-card-border-width: var(--bs-border-width);
        --bs-card-border-color: var(--bs-border-color-translucent);
        --bs-card-border-radius: var(--bs-border-radius);
        --bs-card-box-shadow: ;
        --bs-card-inner-border-radius: calc(var(--bs-border-radius) - (var(--bs-border-width)));
        --bs-card-cap-padding-y: 0.5rem;
        --bs-card-cap-padding-x: 1rem;
        --bs-card-cap-bg: rgba(var(--bs-body-color-rgb), 0.03);
        --bs-card-cap-color: ;
        --bs-card-height: ;
        --bs-card-color: ;
        --bs-card-bg: var(--bs-body-bg);
        --bs-card-img-overlay-padding: 1rem;
        --bs-card-group-margin: 0.75rem;
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        height: var(--bs-card-height);
        color: var(--bs-body-color);
        word-wrap: break-word;
        background-color: var(--bs-card-bg);
        background-clip: border-box;
        border: var(--bs-card-border-width) solid var(--bs-card-border-color);
        border-radius: var(--bs-card-border-radius);
    }
    .card > hr {
        margin-right: 0;
        margin-left: 0;
    }
    .card > .list-group {
        border-top: inherit;
        border-bottom: inherit;
    }
    .card > .list-group:first-child {
        border-top-width: 0;
        border-top-left-radius: var(--bs-card-inner-border-radius);
        border-top-right-radius: var(--bs-card-inner-border-radius);
    }
    .card > .list-group:last-child {
        border-bottom-width: 0;
        border-bottom-right-radius: var(--bs-card-inner-border-radius);
        border-bottom-left-radius: var(--bs-card-inner-border-radius);
    }
    .card > .card-header + .list-group,
    .card > .list-group + .card-footer {
        border-top: 0;
    }
    .card-body {
        flex: 1 1 auto;
        padding: var(--bs-card-spacer-y) var(--bs-card-spacer-x);
        color: var(--bs-card-color);
    }
    .card-title {
        margin-bottom: var(--bs-card-title-spacer-y);
        color: var(--bs-card-title-color);
    }
    .card-subtitle {
        margin-top: calc(-0.5 * var(--bs-card-title-spacer-y));
        margin-bottom: 0;
        color: var(--bs-card-subtitle-color);
    }
    .card-text:last-child {
        margin-bottom: 0;
    }
    .card-link + .card-link {
        margin-left: var(--bs-card-spacer-x);
    }
    .card-header {
        padding: var(--bs-card-cap-padding-y) var(--bs-card-cap-padding-x);
        margin-bottom: 0;
        color: var(--bs-card-cap-color);
        background-color: var(--bs-card-cap-bg);
        border-bottom: var(--bs-card-border-width) solid var(--bs-card-border-color);
    }
    .card-header:first-child {
        border-radius: var(--bs-card-inner-border-radius) var(--bs-card-inner-border-radius) 0 0;
    }
    .card-footer {
        padding: var(--bs-card-cap-padding-y) var(--bs-card-cap-padding-x);
        color: var(--bs-card-cap-color);
        background-color: var(--bs-card-cap-bg);
        border-top: var(--bs-card-border-width) solid var(--bs-card-border-color);
    }
    .card-footer:last-child {
        border-radius: 0 0 var(--bs-card-inner-border-radius) var(--bs-card-inner-border-radius);
    }
    .card-header-tabs {
        margin-right: calc(-0.5 * var(--bs-card-cap-padding-x));
        margin-bottom: calc(-1 * var(--bs-card-cap-padding-y));
        margin-left: calc(-0.5 * var(--bs-card-cap-padding-x));
        border-bottom: 0;
    }
    .card-header-tabs .nav-link.active {
        background-color: var(--bs-card-bg);
        border-bottom-color: var(--bs-card-bg);
    }
    .card-header-pills {
        margin-right: calc(-0.5 * var(--bs-card-cap-padding-x));
        margin-left: calc(-0.5 * var(--bs-card-cap-padding-x));
    }
    .card-img-overlay {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        padding: var(--bs-card-img-overlay-padding);
        border-radius: var(--bs-card-inner-border-radius);
    }
    .card-img,
    .card-img-bottom,
    .card-img-top {
        width: 100%;
    }
    .card-img,
    .card-img-top {
        border-top-left-radius: var(--bs-card-inner-border-radius);
        border-top-right-radius: var(--bs-card-inner-border-radius);
    }
    .card-img,
    .card-img-bottom {
        border-bottom-right-radius: var(--bs-card-inner-border-radius);
        border-bottom-left-radius: var(--bs-card-inner-border-radius);
    }
    .card-group > .card {
        margin-bottom: var(--bs-card-group-margin);
    }
    @media (min-width: 576px) {
        .card-group {
            display: flex;
            flex-flow: row wrap;
        }
        .card-group > .card {
            flex: 1 0 0%;
            margin-bottom: 0;
        }
        .card-group > .card + .card {
            margin-left: 0;
            border-left: 0;
        }
        .card-group > .card:not(:last-child) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .card-group > .card:not(:last-child) .card-header,
        .card-group > .card:not(:last-child) .card-img-top {
            border-top-right-radius: 0;
        }
        .card-group > .card:not(:last-child) .card-footer,
        .card-group > .card:not(:last-child) .card-img-bottom {
            border-bottom-right-radius: 0;
        }
        .card-group > .card:not(:first-child) {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        .card-group > .card:not(:first-child) .card-header,
        .card-group > .card:not(:first-child) .card-img-top {
            border-top-left-radius: 0;
        }
        .card-group > .card:not(:first-child) .card-footer,
        .card-group > .card:not(:first-child) .card-img-bottom {
            border-bottom-left-radius: 0;
        }
    }
    .accordion {
        --bs-accordion-color: var(--bs-body-color);
        --bs-accordion-bg: var(--bs-body-bg);
        --bs-accordion-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, border-radius 0.15s ease;
        --bs-accordion-border-color: var(--bs-border-color);
        --bs-accordion-border-width: var(--bs-border-width);
        --bs-accordion-border-radius: var(--bs-border-radius);
        --bs-accordion-inner-border-radius: calc(var(--bs-border-radius) - (var(--bs-border-width)));
        --bs-accordion-btn-padding-x: 1.25rem;
        --bs-accordion-btn-padding-y: 1rem;
        --bs-accordion-btn-color: var(--bs-body-color);
        --bs-accordion-btn-bg: var(--bs-accordion-bg);
        --bs-accordion-btn-icon: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        --bs-accordion-btn-icon-width: 1.25rem;
        --bs-accordion-btn-icon-transform: rotate(-180deg);
        --bs-accordion-btn-icon-transition: transform 0.2s ease-in-out;
        --bs-accordion-btn-active-icon: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 16 16' fill='%23052c65'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        --bs-accordion-btn-focus-border-color: #86b7fe;
        --bs-accordion-btn-focus-box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        --bs-accordion-body-padding-x: 1.25rem;
        --bs-accordion-body-padding-y: 1rem;
        --bs-accordion-active-color: var(--bs-primary-text-emphasis);
        --bs-accordion-active-bg: var(--bs-primary-bg-subtle);
    }
    .accordion-button {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        padding: var(--bs-accordion-btn-padding-y) var(--bs-accordion-btn-padding-x);
        font-size: 1rem;
        color: var(--bs-accordion-btn-color);
        text-align: left;
        background-color: var(--bs-accordion-btn-bg);
        border: 0;
        border-radius: 0;
        overflow-anchor: none;
        transition: var(--bs-accordion-transition);
    }
    @media (prefers-reduced-motion: reduce) {
        .accordion-button {
            transition: none;
        }
    }
    .accordion-button:not(.collapsed) {
        color: var(--bs-accordion-active-color);
        background-color: var(--bs-accordion-active-bg);
        box-shadow: inset 0 calc(-1 * var(--bs-accordion-border-width)) 0 var(--bs-accordion-border-color);
    }
    .accordion-button:not(.collapsed)::after {
        background-image: var(--bs-accordion-btn-active-icon);
        transform: var(--bs-accordion-btn-icon-transform);
    }
    .accordion-button::after {
        flex-shrink: 0;
        width: var(--bs-accordion-btn-icon-width);
        height: var(--bs-accordion-btn-icon-width);
        margin-left: auto;
        content: "";
        background-image: var(--bs-accordion-btn-icon);
        background-repeat: no-repeat;
        background-size: var(--bs-accordion-btn-icon-width);
        transition: var(--bs-accordion-btn-icon-transition);
    }
    @media (prefers-reduced-motion: reduce) {
        .accordion-button::after {
            transition: none;
        }
    }
    .accordion-button:hover {
        z-index: 2;
    }
    .accordion-button:focus {
        z-index: 3;
        border-color: var(--bs-accordion-btn-focus-border-color);
        outline: 0;
        box-shadow: var(--bs-accordion-btn-focus-box-shadow);
    }
    .accordion-header {
        margin-bottom: 0;
    }
    .accordion-item {
        color: var(--bs-accordion-color);
        background-color: var(--bs-accordion-bg);
        border: var(--bs-accordion-border-width) solid var(--bs-accordion-border-color);
    }
    .accordion-item:first-of-type {
        border-top-left-radius: var(--bs-accordion-border-radius);
        border-top-right-radius: var(--bs-accordion-border-radius);
    }
    .accordion-item:first-of-type .accordion-button {
        border-top-left-radius: var(--bs-accordion-inner-border-radius);
        border-top-right-radius: var(--bs-accordion-inner-border-radius);
    }
    .accordion-item:not(:first-of-type) {
        border-top: 0;
    }
    .accordion-item:last-of-type {
        border-bottom-right-radius: var(--bs-accordion-border-radius);
        border-bottom-left-radius: var(--bs-accordion-border-radius);
    }
    .accordion-item:last-of-type .accordion-button.collapsed {
        border-bottom-right-radius: var(--bs-accordion-inner-border-radius);
        border-bottom-left-radius: var(--bs-accordion-inner-border-radius);
    }
    .accordion-item:last-of-type .accordion-collapse {
        border-bottom-right-radius: var(--bs-accordion-border-radius);
        border-bottom-left-radius: var(--bs-accordion-border-radius);
    }
    .accordion-body {
        padding: var(--bs-accordion-body-padding-y) var(--bs-accordion-body-padding-x);
    }
    .accordion-flush .accordion-collapse {
        border-width: 0;
    }
    .accordion-flush .accordion-item {
        border-right: 0;
        border-left: 0;
        border-radius: 0;
    }
    .accordion-flush .accordion-item:first-child {
        border-top: 0;
    }
    .accordion-flush .accordion-item:last-child {
        border-bottom: 0;
    }
    .accordion-flush .accordion-item .accordion-button,
    .accordion-flush .accordion-item .accordion-button.collapsed {
        border-radius: 0;
    }
    [data-bs-theme="dark"] .accordion-button::after {
        --bs-accordion-btn-icon: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 16 16' fill='%236ea8fe'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        --bs-accordion-btn-active-icon: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 16 16' fill='%236ea8fe'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    .breadcrumb {
        --bs-breadcrumb-padding-x: 0;
        --bs-breadcrumb-padding-y: 0;
        --bs-breadcrumb-margin-bottom: 1rem;
        --bs-breadcrumb-bg: ;
        --bs-breadcrumb-border-radius: ;
        --bs-breadcrumb-divider-color: var(--bs-secondary-color);
        --bs-breadcrumb-item-padding-x: 0.5rem;
        --bs-breadcrumb-item-active-color: var(--bs-secondary-color);
        display: flex;
        flex-wrap: wrap;
        padding: var(--bs-breadcrumb-padding-y) var(--bs-breadcrumb-padding-x);
        margin-bottom: var(--bs-breadcrumb-margin-bottom);
        font-size: var(--bs-breadcrumb-font-size);
        list-style: none;
        background-color: var(--bs-breadcrumb-bg);
        border-radius: var(--bs-breadcrumb-border-radius);
    }
    .breadcrumb-item + .breadcrumb-item {
        padding-left: var(--bs-breadcrumb-item-padding-x);
    }
    .breadcrumb-item + .breadcrumb-item::before {
        float: left;
        padding-right: var(--bs-breadcrumb-item-padding-x);
        color: var(--bs-breadcrumb-divider-color);
        content: var(--bs-breadcrumb-divider, "/");
    }
    .breadcrumb-item.active {
        color: var(--bs-breadcrumb-item-active-color);
    }
    .pagination {
        --bs-pagination-padding-x: 0.75rem;
        --bs-pagination-padding-y: 0.375rem;
        --bs-pagination-font-size: 1rem;
        --bs-pagination-color: var(--bs-link-color);
        --bs-pagination-bg: var(--bs-body-bg);
        --bs-pagination-border-width: var(--bs-border-width);
        --bs-pagination-border-color: var(--bs-border-color);
        --bs-pagination-border-radius: var(--bs-border-radius);
        --bs-pagination-hover-color: var(--bs-link-hover-color);
        --bs-pagination-hover-bg: var(--bs-tertiary-bg);
        --bs-pagination-hover-border-color: var(--bs-border-color);
        --bs-pagination-focus-color: var(--bs-link-hover-color);
        --bs-pagination-focus-bg: var(--bs-secondary-bg);
        --bs-pagination-focus-box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        --bs-pagination-active-color: #fff;
        --bs-pagination-active-bg: #0d6efd;
        --bs-pagination-active-border-color: #0d6efd;
        --bs-pagination-disabled-color: var(--bs-secondary-color);
        --bs-pagination-disabled-bg: var(--bs-secondary-bg);
        --bs-pagination-disabled-border-color: var(--bs-border-color);
        display: flex;
        padding-left: 0;
        list-style: none;
    }
    .page-link {
        position: relative;
        display: block;
        padding: var(--bs-pagination-padding-y) var(--bs-pagination-padding-x);
        font-size: var(--bs-pagination-font-size);
        color: var(--bs-pagination-color);
        text-decoration: none;
        background-color: var(--bs-pagination-bg);
        border: var(--bs-pagination-border-width) solid var(--bs-pagination-border-color);
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    @media (prefers-reduced-motion: reduce) {
        .page-link {
            transition: none;
        }
    }
    .page-link:hover {
        z-index: 2;
        color: var(--bs-pagination-hover-color);
        background-color: var(--bs-pagination-hover-bg);
        border-color: var(--bs-pagination-hover-border-color);
    }
    .page-link:focus {
        z-index: 3;
        color: var(--bs-pagination-focus-color);
        background-color: var(--bs-pagination-focus-bg);
        outline: 0;
        box-shadow: var(--bs-pagination-focus-box-shadow);
    }
    .active > .page-link,
    .page-link.active {
        z-index: 3;
        color: var(--bs-pagination-active-color);
        background-color: var(--bs-pagination-active-bg);
        border-color: var(--bs-pagination-active-border-color);
    }
    .disabled > .page-link,
    .page-link.disabled {
        color: var(--bs-pagination-disabled-color);
        pointer-events: none;
        background-color: var(--bs-pagination-disabled-bg);
        border-color: var(--bs-pagination-disabled-border-color);
    }
    .page-item:not(:first-child) .page-link {
        margin-left: calc(var(--bs-border-width) * -1);
    }
    .page-item:first-child .page-link {
        border-top-left-radius: var(--bs-pagination-border-radius);
        border-bottom-left-radius: var(--bs-pagination-border-radius);
    }
    .page-item:last-child .page-link {
        border-top-right-radius: var(--bs-pagination-border-radius);
        border-bottom-right-radius: var(--bs-pagination-border-radius);
    }
    .pagination-lg {
        --bs-pagination-padding-x: 1.5rem;
        --bs-pagination-padding-y: 0.75rem;
        --bs-pagination-font-size: 1.25rem;
        --bs-pagination-border-radius: var(--bs-border-radius-lg);
    }
    .pagination-sm {
        --bs-pagination-padding-x: 0.5rem;
        --bs-pagination-padding-y: 0.25rem;
        --bs-pagination-font-size: 0.875rem;
        --bs-pagination-border-radius: var(--bs-border-radius-sm);
    }
    .badge {
        --bs-badge-padding-x: 0.65em;
        --bs-badge-padding-y: 0.35em;
        --bs-badge-font-size: 0.75em;
        --bs-badge-font-weight: 700;
        --bs-badge-color: #fff;
        --bs-badge-border-radius: var(--bs-border-radius);
        display: inline-block;
        padding: var(--bs-badge-padding-y) var(--bs-badge-padding-x);
        font-size: var(--bs-badge-font-size);
        font-weight: var(--bs-badge-font-weight);
        line-height: 1;
        color: var(--bs-badge-color);
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: var(--bs-badge-border-radius);
    }
    .badge:empty {
        display: none;
    }
    .btn .badge {
        position: relative;
        top: -1px;
    }
    .alert {
        --bs-alert-bg: transparent;
        --bs-alert-padding-x: 1rem;
        --bs-alert-padding-y: 1rem;
        --bs-alert-margin-bottom: 1rem;
        --bs-alert-color: inherit;
        --bs-alert-border-color: transparent;
        --bs-alert-border: var(--bs-border-width) solid var(--bs-alert-border-color);
        --bs-alert-border-radius: var(--bs-border-radius);
        --bs-alert-link-color: inherit;
        position: relative;
        padding: var(--bs-alert-padding-y) var(--bs-alert-padding-x);
        margin-bottom: var(--bs-alert-margin-bottom);
        color: var(--bs-alert-color);
        background-color: var(--bs-alert-bg);
        border: var(--bs-alert-border);
        border-radius: var(--bs-alert-border-radius);
    }
    .alert-heading {
        color: inherit;
    }
    .alert-link {
        font-weight: 700;
        color: var(--bs-alert-link-color);
    }
    .alert-dismissible {
        padding-right: 3rem;
    }
    .alert-dismissible .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 2;
        padding: 1.25rem 1rem;
    }
    .alert-primary {
        --bs-alert-color: var(--bs-primary-text-emphasis);
        --bs-alert-bg: var(--bs-primary-bg-subtle);
        --bs-alert-border-color: var(--bs-primary-border-subtle);
        --bs-alert-link-color: var(--bs-primary-text-emphasis);
    }
    .alert-secondary {
        --bs-alert-color: var(--bs-secondary-text-emphasis);
        --bs-alert-bg: var(--bs-secondary-bg-subtle);
        --bs-alert-border-color: var(--bs-secondary-border-subtle);
        --bs-alert-link-color: var(--bs-secondary-text-emphasis);
    }
    .alert-success {
        --bs-alert-color: var(--bs-success-text-emphasis);
        --bs-alert-bg: var(--bs-success-bg-subtle);
        --bs-alert-border-color: var(--bs-success-border-subtle);
        --bs-alert-link-color: var(--bs-success-text-emphasis);
    }
    .alert-info {
        --bs-alert-color: var(--bs-info-text-emphasis);
        --bs-alert-bg: var(--bs-info-bg-subtle);
        --bs-alert-border-color: var(--bs-info-border-subtle);
        --bs-alert-link-color: var(--bs-info-text-emphasis);
    }
    .alert-warning {
        --bs-alert-color: var(--bs-warning-text-emphasis);
        --bs-alert-bg: var(--bs-warning-bg-subtle);
        --bs-alert-border-color: var(--bs-warning-border-subtle);
        --bs-alert-link-color: var(--bs-warning-text-emphasis);
    }
    .alert-danger {
        --bs-alert-color: var(--bs-danger-text-emphasis);
        --bs-alert-bg: var(--bs-danger-bg-subtle);
        --bs-alert-border-color: var(--bs-danger-border-subtle);
        --bs-alert-link-color: var(--bs-danger-text-emphasis);
    }
    .alert-light {
        --bs-alert-color: var(--bs-light-text-emphasis);
        --bs-alert-bg: var(--bs-light-bg-subtle);
        --bs-alert-border-color: var(--bs-light-border-subtle);
        --bs-alert-link-color: var(--bs-light-text-emphasis);
    }
    .alert-dark {
        --bs-alert-color: var(--bs-dark-text-emphasis);
        --bs-alert-bg: var(--bs-dark-bg-subtle);
        --bs-alert-border-color: var(--bs-dark-border-subtle);
        --bs-alert-link-color: var(--bs-dark-text-emphasis);
    }
    @keyframes progress-bar-stripes {
        0% {
            background-position-x: 1rem;
        }
    }
    .progress,
    .progress-stacked {
        --bs-progress-height: 1rem;
        --bs-progress-font-size: 0.75rem;
        --bs-progress-bg: var(--bs-secondary-bg);
        --bs-progress-border-radius: var(--bs-border-radius);
        --bs-progress-box-shadow: var(--bs-box-shadow-inset);
        --bs-progress-bar-color: #fff;
        --bs-progress-bar-bg: #0d6efd;
        --bs-progress-bar-transition: width 0.6s ease;
        display: flex;
        height: var(--bs-progress-height);
        overflow: hidden;
        font-size: var(--bs-progress-font-size);
        background-color: var(--bs-progress-bg);
        border-radius: var(--bs-progress-border-radius);
    }
    .progress-bar {
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
        color: var(--bs-progress-bar-color);
        text-align: center;
        white-space: nowrap;
        background-color: var(--bs-progress-bar-bg);
        transition: var(--bs-progress-bar-transition);
    }
    @media (prefers-reduced-motion: reduce) {
        .progress-bar {
            transition: none;
        }
    }
    .progress-bar-striped {
        background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
        background-size: var(--bs-progress-height) var(--bs-progress-height);
    }
    .progress-stacked > .progress {
        overflow: visible;
    }
    .progress-stacked > .progress > .progress-bar {
        width: 100%;
    }
    .progress-bar-animated {
        animation: 1s linear infinite progress-bar-stripes;
    }
    @media (prefers-reduced-motion: reduce) {
        .progress-bar-animated {
            animation: none;
        }
    }
    .list-group {
        --bs-list-group-color: var(--bs-body-color);
        --bs-list-group-bg: var(--bs-body-bg);
        --bs-list-group-border-color: var(--bs-border-color);
        --bs-list-group-border-width: var(--bs-border-width);
        --bs-list-group-border-radius: var(--bs-border-radius);
        --bs-list-group-item-padding-x: 1rem;
        --bs-list-group-item-padding-y: 0.5rem;
        --bs-list-group-action-color: var(--bs-secondary-color);
        --bs-list-group-action-hover-color: var(--bs-emphasis-color);
        --bs-list-group-action-hover-bg: var(--bs-tertiary-bg);
        --bs-list-group-action-active-color: var(--bs-body-color);
        --bs-list-group-action-active-bg: var(--bs-secondary-bg);
        --bs-list-group-disabled-color: var(--bs-secondary-color);
        --bs-list-group-disabled-bg: var(--bs-body-bg);
        --bs-list-group-active-color: #fff;
        --bs-list-group-active-bg: #0d6efd;
        --bs-list-group-active-border-color: #0d6efd;
        display: flex;
        flex-direction: column;
        padding-left: 0;
        margin-bottom: 0;
        border-radius: var(--bs-list-group-border-radius);
    }
    .list-group-numbered {
        list-style-type: none;
        counter-reset: section;
    }
    .list-group-numbered > .list-group-item::before {
        content: counters(section, ".") ". ";
        counter-increment: section;
    }
    .list-group-item-action {
        width: 100%;
        color: var(--bs-list-group-action-color);
        text-align: inherit;
    }
    .list-group-item-action:focus,
    .list-group-item-action:hover {
        z-index: 1;
        color: var(--bs-list-group-action-hover-color);
        text-decoration: none;
        background-color: var(--bs-list-group-action-hover-bg);
    }
    .list-group-item-action:active {
        color: var(--bs-list-group-action-active-color);
        background-color: var(--bs-list-group-action-active-bg);
    }
    .list-group-item {
        position: relative;
        display: block;
        padding: var(--bs-list-group-item-padding-y) var(--bs-list-group-item-padding-x);
        color: var(--bs-list-group-color);
        text-decoration: none;
        background-color: var(--bs-list-group-bg);
        border: var(--bs-list-group-border-width) solid var(--bs-list-group-border-color);
    }
    .list-group-item:first-child {
        border-top-left-radius: inherit;
        border-top-right-radius: inherit;
    }
    .list-group-item:last-child {
        border-bottom-right-radius: inherit;
        border-bottom-left-radius: inherit;
    }
    .list-group-item.disabled,
    .list-group-item:disabled {
        color: var(--bs-list-group-disabled-color);
        pointer-events: none;
        background-color: var(--bs-list-group-disabled-bg);
    }
    .list-group-item.active {
        z-index: 2;
        color: var(--bs-list-group-active-color);
        background-color: var(--bs-list-group-active-bg);
        border-color: var(--bs-list-group-active-border-color);
    }
    .list-group-item + .list-group-item {
        border-top-width: 0;
    }
    .list-group-item + .list-group-item.active {
        margin-top: calc(-1 * var(--bs-list-group-border-width));
        border-top-width: var(--bs-list-group-border-width);
    }
    .list-group-horizontal {
        flex-direction: row;
    }
    .list-group-horizontal > .list-group-item:first-child:not(:last-child) {
        border-bottom-left-radius: var(--bs-list-group-border-radius);
        border-top-right-radius: 0;
    }
    .list-group-horizontal > .list-group-item:last-child:not(:first-child) {
        border-top-right-radius: var(--bs-list-group-border-radius);
        border-bottom-left-radius: 0;
    }
    .list-group-horizontal > .list-group-item.active {
        margin-top: 0;
    }
    .list-group-horizontal > .list-group-item + .list-group-item {
        border-top-width: var(--bs-list-group-border-width);
        border-left-width: 0;
    }
    .list-group-horizontal > .list-group-item + .list-group-item.active {
        margin-left: calc(-1 * var(--bs-list-group-border-width));
        border-left-width: var(--bs-list-group-border-width);
    }
    @media (min-width: 576px) {
        .list-group-horizontal-sm {
            flex-direction: row;
        }
        .list-group-horizontal-sm > .list-group-item:first-child:not(:last-child) {
            border-bottom-left-radius: var(--bs-list-group-border-radius);
            border-top-right-radius: 0;
        }
        .list-group-horizontal-sm > .list-group-item:last-child:not(:first-child) {
            border-top-right-radius: var(--bs-list-group-border-radius);
            border-bottom-left-radius: 0;
        }
        .list-group-horizontal-sm > .list-group-item.active {
            margin-top: 0;
        }
        .list-group-horizontal-sm > .list-group-item + .list-group-item {
            border-top-width: var(--bs-list-group-border-width);
            border-left-width: 0;
        }
        .list-group-horizontal-sm > .list-group-item + .list-group-item.active {
            margin-left: calc(-1 * var(--bs-list-group-border-width));
            border-left-width: var(--bs-list-group-border-width);
        }
    }
    @media (min-width: 768px) {
        .list-group-horizontal-md {
            flex-direction: row;
        }
        .list-group-horizontal-md > .list-group-item:first-child:not(:last-child) {
            border-bottom-left-radius: var(--bs-list-group-border-radius);
            border-top-right-radius: 0;
        }
        .list-group-horizontal-md > .list-group-item:last-child:not(:first-child) {
            border-top-right-radius: var(--bs-list-group-border-radius);
            border-bottom-left-radius: 0;
        }
        .list-group-horizontal-md > .list-group-item.active {
            margin-top: 0;
        }
        .list-group-horizontal-md > .list-group-item + .list-group-item {
            border-top-width: var(--bs-list-group-border-width);
            border-left-width: 0;
        }
        .list-group-horizontal-md > .list-group-item + .list-group-item.active {
            margin-left: calc(-1 * var(--bs-list-group-border-width));
            border-left-width: var(--bs-list-group-border-width);
        }
    }
    @media (min-width: 992px) {
        .list-group-horizontal-lg {
            flex-direction: row;
        }
        .list-group-horizontal-lg > .list-group-item:first-child:not(:last-child) {
            border-bottom-left-radius: var(--bs-list-group-border-radius);
            border-top-right-radius: 0;
        }
        .list-group-horizontal-lg > .list-group-item:last-child:not(:first-child) {
            border-top-right-radius: var(--bs-list-group-border-radius);
            border-bottom-left-radius: 0;
        }
        .list-group-horizontal-lg > .list-group-item.active {
            margin-top: 0;
        }
        .list-group-horizontal-lg > .list-group-item + .list-group-item {
            border-top-width: var(--bs-list-group-border-width);
            border-left-width: 0;
        }
        .list-group-horizontal-lg > .list-group-item + .list-group-item.active {
            margin-left: calc(-1 * var(--bs-list-group-border-width));
            border-left-width: var(--bs-list-group-border-width);
        }
    }
    @media (min-width: 1200px) {
        .list-group-horizontal-xl {
            flex-direction: row;
        }
        .list-group-horizontal-xl > .list-group-item:first-child:not(:last-child) {
            border-bottom-left-radius: var(--bs-list-group-border-radius);
            border-top-right-radius: 0;
        }
        .list-group-horizontal-xl > .list-group-item:last-child:not(:first-child) {
            border-top-right-radius: var(--bs-list-group-border-radius);
            border-bottom-left-radius: 0;
        }
        .list-group-horizontal-xl > .list-group-item.active {
            margin-top: 0;
        }
        .list-group-horizontal-xl > .list-group-item + .list-group-item {
            border-top-width: var(--bs-list-group-border-width);
            border-left-width: 0;
        }
        .list-group-horizontal-xl > .list-group-item + .list-group-item.active {
            margin-left: calc(-1 * var(--bs-list-group-border-width));
            border-left-width: var(--bs-list-group-border-width);
        }
    }
    @media (min-width: 1400px) {
        .list-group-horizontal-xxl {
            flex-direction: row;
        }
        .list-group-horizontal-xxl > .list-group-item:first-child:not(:last-child) {
            border-bottom-left-radius: var(--bs-list-group-border-radius);
            border-top-right-radius: 0;
        }
        .list-group-horizontal-xxl > .list-group-item:last-child:not(:first-child) {
            border-top-right-radius: var(--bs-list-group-border-radius);
            border-bottom-left-radius: 0;
        }
        .list-group-horizontal-xxl > .list-group-item.active {
            margin-top: 0;
        }
        .list-group-horizontal-xxl > .list-group-item + .list-group-item {
            border-top-width: var(--bs-list-group-border-width);
            border-left-width: 0;
        }
        .list-group-horizontal-xxl > .list-group-item + .list-group-item.active {
            margin-left: calc(-1 * var(--bs-list-group-border-width));
            border-left-width: var(--bs-list-group-border-width);
        }
    }
    .list-group-flush {
        border-radius: 0;
    }
    .list-group-flush > .list-group-item {
        border-width: 0 0 var(--bs-list-group-border-width);
    }
    .list-group-flush > .list-group-item:last-child {
        border-bottom-width: 0;
    }
    .list-group-item-primary {
        --bs-list-group-color: var(--bs-primary-text-emphasis);
        --bs-list-group-bg: var(--bs-primary-bg-subtle);
        --bs-list-group-border-color: var(--bs-primary-border-subtle);
        --bs-list-group-action-hover-color: var(--bs-emphasis-color);
        --bs-list-group-action-hover-bg: var(--bs-primary-border-subtle);
        --bs-list-group-action-active-color: var(--bs-emphasis-color);
        --bs-list-group-action-active-bg: var(--bs-primary-border-subtle);
        --bs-list-group-active-color: var(--bs-primary-bg-subtle);
        --bs-list-group-active-bg: var(--bs-primary-text-emphasis);
        --bs-list-group-active-border-color: var(--bs-primary-text-emphasis);
    }
    .list-group-item-secondary {
        --bs-list-group-color: var(--bs-secondary-text-emphasis);
        --bs-list-group-bg: var(--bs-secondary-bg-subtle);
        --bs-list-group-border-color: var(--bs-secondary-border-subtle);
        --bs-list-group-action-hover-color: var(--bs-emphasis-color);
        --bs-list-group-action-hover-bg: var(--bs-secondary-border-subtle);
        --bs-list-group-action-active-color: var(--bs-emphasis-color);
        --bs-list-group-action-active-bg: var(--bs-secondary-border-subtle);
        --bs-list-group-active-color: var(--bs-secondary-bg-subtle);
        --bs-list-group-active-bg: var(--bs-secondary-text-emphasis);
        --bs-list-group-active-border-color: var(--bs-secondary-text-emphasis);
    }
    .list-group-item-success {
        --bs-list-group-color: var(--bs-success-text-emphasis);
        --bs-list-group-bg: var(--bs-success-bg-subtle);
        --bs-list-group-border-color: var(--bs-success-border-subtle);
        --bs-list-group-action-hover-color: var(--bs-emphasis-color);
        --bs-list-group-action-hover-bg: var(--bs-success-border-subtle);
        --bs-list-group-action-active-color: var(--bs-emphasis-color);
        --bs-list-group-action-active-bg: var(--bs-success-border-subtle);
        --bs-list-group-active-color: var(--bs-success-bg-subtle);
        --bs-list-group-active-bg: var(--bs-success-text-emphasis);
        --bs-list-group-active-border-color: var(--bs-success-text-emphasis);
    }
    .list-group-item-info {
        --bs-list-group-color: var(--bs-info-text-emphasis);
        --bs-list-group-bg: var(--bs-info-bg-subtle);
        --bs-list-group-border-color: var(--bs-info-border-subtle);
        --bs-list-group-action-hover-color: var(--bs-emphasis-color);
        --bs-list-group-action-hover-bg: var(--bs-info-border-subtle);
        --bs-list-group-action-active-color: var(--bs-emphasis-color);
        --bs-list-group-action-active-bg: var(--bs-info-border-subtle);
        --bs-list-group-active-color: var(--bs-info-bg-subtle);
        --bs-list-group-active-bg: var(--bs-info-text-emphasis);
        --bs-list-group-active-border-color: var(--bs-info-text-emphasis);
    }
    .list-group-item-warning {
        --bs-list-group-color: var(--bs-warning-text-emphasis);
        --bs-list-group-bg: var(--bs-warning-bg-subtle);
        --bs-list-group-border-color: var(--bs-warning-border-subtle);
        --bs-list-group-action-hover-color: var(--bs-emphasis-color);
        --bs-list-group-action-hover-bg: var(--bs-warning-border-subtle);
        --bs-list-group-action-active-color: var(--bs-emphasis-color);
        --bs-list-group-action-active-bg: var(--bs-warning-border-subtle);
        --bs-list-group-active-color: var(--bs-warning-bg-subtle);
        --bs-list-group-active-bg: var(--bs-warning-text-emphasis);
        --bs-list-group-active-border-color: var(--bs-warning-text-emphasis);
    }
    .list-group-item-danger {
        --bs-list-group-color: var(--bs-danger-text-emphasis);
        --bs-list-group-bg: var(--bs-danger-bg-subtle);
        --bs-list-group-border-color: var(--bs-danger-border-subtle);
        --bs-list-group-action-hover-color: var(--bs-emphasis-color);
        --bs-list-group-action-hover-bg: var(--bs-danger-border-subtle);
        --bs-list-group-action-active-color: var(--bs-emphasis-color);
        --bs-list-group-action-active-bg: var(--bs-danger-border-subtle);
        --bs-list-group-active-color: var(--bs-danger-bg-subtle);
        --bs-list-group-active-bg: var(--bs-danger-text-emphasis);
        --bs-list-group-active-border-color: var(--bs-danger-text-emphasis);
    }
    .list-group-item-light {
        --bs-list-group-color: var(--bs-light-text-emphasis);
        --bs-list-group-bg: var(--bs-light-bg-subtle);
        --bs-list-group-border-color: var(--bs-light-border-subtle);
        --bs-list-group-action-hover-color: var(--bs-emphasis-color);
        --bs-list-group-action-hover-bg: var(--bs-light-border-subtle);
        --bs-list-group-action-active-color: var(--bs-emphasis-color);
        --bs-list-group-action-active-bg: var(--bs-light-border-subtle);
        --bs-list-group-active-color: var(--bs-light-bg-subtle);
        --bs-list-group-active-bg: var(--bs-light-text-emphasis);
        --bs-list-group-active-border-color: var(--bs-light-text-emphasis);
    }
    .list-group-item-dark {
        --bs-list-group-color: var(--bs-dark-text-emphasis);
        --bs-list-group-bg: var(--bs-dark-bg-subtle);
        --bs-list-group-border-color: var(--bs-dark-border-subtle);
        --bs-list-group-action-hover-color: var(--bs-emphasis-color);
        --bs-list-group-action-hover-bg: var(--bs-dark-border-subtle);
        --bs-list-group-action-active-color: var(--bs-emphasis-color);
        --bs-list-group-action-active-bg: var(--bs-dark-border-subtle);
        --bs-list-group-active-color: var(--bs-dark-bg-subtle);
        --bs-list-group-active-bg: var(--bs-dark-text-emphasis);
        --bs-list-group-active-border-color: var(--bs-dark-text-emphasis);
    }
    .btn-close {
        --bs-btn-close-color: #000;
        --bs-btn-close-bg: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e");
        --bs-btn-close-opacity: 0.5;
        --bs-btn-close-hover-opacity: 0.75;
        --bs-btn-close-focus-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        --bs-btn-close-focus-opacity: 1;
        --bs-btn-close-disabled-opacity: 0.25;
        --bs-btn-close-white-filter: invert(1) grayscale(100%) brightness(200%);
        box-sizing: content-box;
        width: 1em;
        height: 1em;
        padding: 0.25em 0.25em;
        color: var(--bs-btn-close-color);
        background: transparent var(--bs-btn-close-bg) center/1em auto no-repeat;
        border: 0;
        border-radius: 0.375rem;
        opacity: var(--bs-btn-close-opacity);
    }
    .btn-close:hover {
        color: var(--bs-btn-close-color);
        text-decoration: none;
        opacity: var(--bs-btn-close-hover-opacity);
    }
    .btn-close:focus {
        outline: 0;
        box-shadow: var(--bs-btn-close-focus-shadow);
        opacity: var(--bs-btn-close-focus-opacity);
    }
    .btn-close.disabled,
    .btn-close:disabled {
        pointer-events: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        opacity: var(--bs-btn-close-disabled-opacity);
    }
    .btn-close-white {
        filter: var(--bs-btn-close-white-filter);
    }
    [data-bs-theme="dark"] .btn-close {
        filter: var(--bs-btn-close-white-filter);
    }
    .toast {
        --bs-toast-zindex: 1090;
        --bs-toast-padding-x: 0.75rem;
        --bs-toast-padding-y: 0.5rem;
        --bs-toast-spacing: 1.5rem;
        --bs-toast-max-width: 350px;
        --bs-toast-font-size: 0.875rem;
        --bs-toast-color: ;
        --bs-toast-bg: rgba(var(--bs-body-bg-rgb), 0.85);
        --bs-toast-border-width: var(--bs-border-width);
        --bs-toast-border-color: var(--bs-border-color-translucent);
        --bs-toast-border-radius: var(--bs-border-radius);
        --bs-toast-box-shadow: var(--bs-box-shadow);
        --bs-toast-header-color: var(--bs-secondary-color);
        --bs-toast-header-bg: rgba(var(--bs-body-bg-rgb), 0.85);
        --bs-toast-header-border-color: var(--bs-border-color-translucent);
        width: var(--bs-toast-max-width);
        max-width: 100%;
        font-size: var(--bs-toast-font-size);
        color: var(--bs-toast-color);
        pointer-events: auto;
        background-color: var(--bs-toast-bg);
        background-clip: padding-box;
        border: var(--bs-toast-border-width) solid var(--bs-toast-border-color);
        box-shadow: var(--bs-toast-box-shadow);
        border-radius: var(--bs-toast-border-radius);
    }
    .toast.showing {
        opacity: 0;
    }
    .toast:not(.show) {
        display: none;
    }
    .toast-container {
        --bs-toast-zindex: 1090;
        position: absolute;
        z-index: var(--bs-toast-zindex);
        width: -webkit-max-content;
        width: -moz-max-content;
        width: max-content;
        max-width: 100%;
        pointer-events: none;
    }
    .toast-container > :not(:last-child) {
        margin-bottom: var(--bs-toast-spacing);
    }
    .toast-header {
        display: flex;
        align-items: center;
        padding: var(--bs-toast-padding-y) var(--bs-toast-padding-x);
        color: var(--bs-toast-header-color);
        background-color: var(--bs-toast-header-bg);
        background-clip: padding-box;
        border-bottom: var(--bs-toast-border-width) solid var(--bs-toast-header-border-color);
        border-top-left-radius: calc(var(--bs-toast-border-radius) - var(--bs-toast-border-width));
        border-top-right-radius: calc(var(--bs-toast-border-radius) - var(--bs-toast-border-width));
    }
    .toast-header .btn-close {
        margin-right: calc(-0.5 * var(--bs-toast-padding-x));
        margin-left: var(--bs-toast-padding-x);
    }
    .toast-body {
        padding: var(--bs-toast-padding-x);
        word-wrap: break-word;
    }
    .modal {
        --bs-modal-zindex: 1055;
        --bs-modal-width: 500px;
        --bs-modal-padding: 1rem;
        --bs-modal-margin: 0.5rem;
        --bs-modal-color: ;
        --bs-modal-bg: var(--bs-body-bg);
        --bs-modal-border-color: var(--bs-border-color-translucent);
        --bs-modal-border-width: var(--bs-border-width);
        --bs-modal-border-radius: var(--bs-border-radius-lg);
        --bs-modal-box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --bs-modal-inner-border-radius: calc(var(--bs-border-radius-lg) - (var(--bs-border-width)));
        --bs-modal-header-padding-x: 1rem;
        --bs-modal-header-padding-y: 1rem;
        --bs-modal-header-padding: 1rem 1rem;
        --bs-modal-header-border-color: var(--bs-border-color);
        --bs-modal-header-border-width: var(--bs-border-width);
        --bs-modal-title-line-height: 1.5;
        --bs-modal-footer-gap: 0.5rem;
        --bs-modal-footer-bg: ;
        --bs-modal-footer-border-color: var(--bs-border-color);
        --bs-modal-footer-border-width: var(--bs-border-width);
        position: fixed;
        top: 0;
        left: 0;
        z-index: var(--bs-modal-zindex);
        display: none;
        width: 100%;
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        outline: 0;
    }
    .modal-dialog {
        position: relative;
        width: auto;
        margin: var(--bs-modal-margin);
        pointer-events: none;
    }
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: translate(0, -50px);
    }
    @media (prefers-reduced-motion: reduce) {
        .modal.fade .modal-dialog {
            transition: none;
        }
    }
    .modal.show .modal-dialog {
        transform: none;
    }
    .modal.modal-static .modal-dialog {
        transform: scale(1.02);
    }
    .modal-dialog-scrollable {
        height: calc(100% - var(--bs-modal-margin) * 2);
    }
    .modal-dialog-scrollable .modal-content {
        max-height: 100%;
        overflow: hidden;
    }
    .modal-dialog-scrollable .modal-body {
        overflow-y: auto;
    }
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - var(--bs-modal-margin) * 2);
    }
    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        color: var(--bs-modal-color);
        pointer-events: auto;
        background-color: var(--bs-modal-bg);
        background-clip: padding-box;
        border: var(--bs-modal-border-width) solid var(--bs-modal-border-color);
        border-radius: var(--bs-modal-border-radius);
        outline: 0;
    }
    .modal-backdrop {
        --bs-backdrop-zindex: 1050;
        --bs-backdrop-bg: #000;
        --bs-backdrop-opacity: 0.5;
        position: fixed;
        top: 0;
        left: 0;
        z-index: var(--bs-backdrop-zindex);
        width: 100vw;
        height: 100vh;
        background-color: var(--bs-backdrop-bg);
    }
    .modal-backdrop.fade {
        opacity: 0;
    }
    .modal-backdrop.show {
        opacity: var(--bs-backdrop-opacity);
    }
    .modal-header {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: space-between;
        padding: var(--bs-modal-header-padding);
        border-bottom: var(--bs-modal-header-border-width) solid var(--bs-modal-header-border-color);
        border-top-left-radius: var(--bs-modal-inner-border-radius);
        border-top-right-radius: var(--bs-modal-inner-border-radius);
    }
    .modal-header .btn-close {
        padding: calc(var(--bs-modal-header-padding-y) * 0.5) calc(var(--bs-modal-header-padding-x) * 0.5);
        margin: calc(-0.5 * var(--bs-modal-header-padding-y)) calc(-0.5 * var(--bs-modal-header-padding-x)) calc(-0.5 * var(--bs-modal-header-padding-y)) auto;
    }
    .modal-title {
        margin-bottom: 0;
        line-height: var(--bs-modal-title-line-height);
    }
    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: var(--bs-modal-padding);
    }
    .modal-footer {
        display: flex;
        flex-shrink: 0;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        padding: calc(var(--bs-modal-padding) - var(--bs-modal-footer-gap) * 0.5);
        background-color: var(--bs-modal-footer-bg);
        border-top: var(--bs-modal-footer-border-width) solid var(--bs-modal-footer-border-color);
        border-bottom-right-radius: var(--bs-modal-inner-border-radius);
        border-bottom-left-radius: var(--bs-modal-inner-border-radius);
    }
    .modal-footer > * {
        margin: calc(var(--bs-modal-footer-gap) * 0.5);
    }
    @media (min-width: 576px) {
        .modal {
            --bs-modal-margin: 1.75rem;
            --bs-modal-box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .modal-dialog {
            max-width: var(--bs-modal-width);
            margin-right: auto;
            margin-left: auto;
        }
        .modal-sm {
            --bs-modal-width: 300px;
        }
    }
    @media (min-width: 992px) {
        .modal-lg,
        .modal-xl {
            --bs-modal-width: 800px;
        }
    }
    @media (min-width: 1200px) {
        .modal-xl {
            --bs-modal-width: 1140px;
        }
    }
    .modal-fullscreen {
        width: 100vw;
        max-width: none;
        height: 100%;
        margin: 0;
    }
    .modal-fullscreen .modal-content {
        height: 100%;
        border: 0;
        border-radius: 0;
    }
    .modal-fullscreen .modal-footer,
    .modal-fullscreen .modal-header {
        border-radius: 0;
    }
    .modal-fullscreen .modal-body {
        overflow-y: auto;
    }
    @media (max-width: 575.98px) {
        .modal-fullscreen-sm-down {
            width: 100vw;
            max-width: none;
            height: 100%;
            margin: 0;
        }
        .modal-fullscreen-sm-down .modal-content {
            height: 100%;
            border: 0;
            border-radius: 0;
        }
        .modal-fullscreen-sm-down .modal-footer,
        .modal-fullscreen-sm-down .modal-header {
            border-radius: 0;
        }
        .modal-fullscreen-sm-down .modal-body {
            overflow-y: auto;
        }
    }
    @media (max-width: 767.98px) {
        .modal-fullscreen-md-down {
            width: 100vw;
            max-width: none;
            height: 100%;
            margin: 0;
        }
        .modal-fullscreen-md-down .modal-content {
            height: 100%;
            border: 0;
            border-radius: 0;
        }
        .modal-fullscreen-md-down .modal-footer,
        .modal-fullscreen-md-down .modal-header {
            border-radius: 0;
        }
        .modal-fullscreen-md-down .modal-body {
            overflow-y: auto;
        }
    }
    @media (max-width: 991.98px) {
        .modal-fullscreen-lg-down {
            width: 100vw;
            max-width: none;
            height: 100%;
            margin: 0;
        }
        .modal-fullscreen-lg-down .modal-content {
            height: 100%;
            border: 0;
            border-radius: 0;
        }
        .modal-fullscreen-lg-down .modal-footer,
        .modal-fullscreen-lg-down .modal-header {
            border-radius: 0;
        }
        .modal-fullscreen-lg-down .modal-body {
            overflow-y: auto;
        }
    }
    @media (max-width: 1199.98px) {
        .modal-fullscreen-xl-down {
            width: 100vw;
            max-width: none;
            height: 100%;
            margin: 0;
        }
        .modal-fullscreen-xl-down .modal-content {
            height: 100%;
            border: 0;
            border-radius: 0;
        }
        .modal-fullscreen-xl-down .modal-footer,
        .modal-fullscreen-xl-down .modal-header {
            border-radius: 0;
        }
        .modal-fullscreen-xl-down .modal-body {
            overflow-y: auto;
        }
    }
    @media (max-width: 1399.98px) {
        .modal-fullscreen-xxl-down {
            width: 100vw;
            max-width: none;
            height: 100%;
            margin: 0;
        }
        .modal-fullscreen-xxl-down .modal-content {
            height: 100%;
            border: 0;
            border-radius: 0;
        }
        .modal-fullscreen-xxl-down .modal-footer,
        .modal-fullscreen-xxl-down .modal-header {
            border-radius: 0;
        }
        .modal-fullscreen-xxl-down .modal-body {
            overflow-y: auto;
        }
    }
    .tooltip {
        --bs-tooltip-zindex: 1080;
        --bs-tooltip-max-width: 200px;
        --bs-tooltip-padding-x: 0.5rem;
        --bs-tooltip-padding-y: 0.25rem;
        --bs-tooltip-margin: ;
        --bs-tooltip-font-size: 0.875rem;
        --bs-tooltip-color: var(--bs-body-bg);
        --bs-tooltip-bg: var(--bs-emphasis-color);
        --bs-tooltip-border-radius: var(--bs-border-radius);
        --bs-tooltip-opacity: 0.9;
        --bs-tooltip-arrow-width: 0.8rem;
        --bs-tooltip-arrow-height: 0.4rem;
        z-index: var(--bs-tooltip-zindex);
        display: block;
        margin: var(--bs-tooltip-margin);
        font-family: var(--bs-font-sans-serif);
        font-style: normal;
        font-weight: 400;
        line-height: 1.5;
        text-align: left;
        text-align: start;
        text-decoration: none;
        text-shadow: none;
        text-transform: none;
        letter-spacing: normal;
        word-break: normal;
        white-space: normal;
        word-spacing: normal;
        line-break: auto;
        font-size: var(--bs-tooltip-font-size);
        word-wrap: break-word;
        opacity: 0;
    }
    .tooltip.show {
        opacity: var(--bs-tooltip-opacity);
    }
    .tooltip .tooltip-arrow {
        display: block;
        width: var(--bs-tooltip-arrow-width);
        height: var(--bs-tooltip-arrow-height);
    }
    .tooltip .tooltip-arrow::before {
        position: absolute;
        content: "";
        border-color: transparent;
        border-style: solid;
    }
    .bs-tooltip-auto[data-popper-placement^="top"] .tooltip-arrow,
    .bs-tooltip-top .tooltip-arrow {
        bottom: calc(-1 * var(--bs-tooltip-arrow-height));
    }
    .bs-tooltip-auto[data-popper-placement^="top"] .tooltip-arrow::before,
    .bs-tooltip-top .tooltip-arrow::before {
        top: -1px;
        border-width: var(--bs-tooltip-arrow-height) calc(var(--bs-tooltip-arrow-width) * 0.5) 0;
        border-top-color: var(--bs-tooltip-bg);
    }
    .bs-tooltip-auto[data-popper-placement^="right"] .tooltip-arrow,
    .bs-tooltip-end .tooltip-arrow {
        left: calc(-1 * var(--bs-tooltip-arrow-height));
        width: var(--bs-tooltip-arrow-height);
        height: var(--bs-tooltip-arrow-width);
    }
    .bs-tooltip-auto[data-popper-placement^="right"] .tooltip-arrow::before,
    .bs-tooltip-end .tooltip-arrow::before {
        right: -1px;
        border-width: calc(var(--bs-tooltip-arrow-width) * 0.5) var(--bs-tooltip-arrow-height) calc(var(--bs-tooltip-arrow-width) * 0.5) 0;
        border-right-color: var(--bs-tooltip-bg);
    }
    .bs-tooltip-auto[data-popper-placement^="bottom"] .tooltip-arrow,
    .bs-tooltip-bottom .tooltip-arrow {
        top: calc(-1 * var(--bs-tooltip-arrow-height));
    }
    .bs-tooltip-auto[data-popper-placement^="bottom"] .tooltip-arrow::before,
    .bs-tooltip-bottom .tooltip-arrow::before {
        bottom: -1px;
        border-width: 0 calc(var(--bs-tooltip-arrow-width) * 0.5) var(--bs-tooltip-arrow-height);
        border-bottom-color: var(--bs-tooltip-bg);
    }
    .bs-tooltip-auto[data-popper-placement^="left"] .tooltip-arrow,
    .bs-tooltip-start .tooltip-arrow {
        right: calc(-1 * var(--bs-tooltip-arrow-height));
        width: var(--bs-tooltip-arrow-height);
        height: var(--bs-tooltip-arrow-width);
    }
    .bs-tooltip-auto[data-popper-placement^="left"] .tooltip-arrow::before,
    .bs-tooltip-start .tooltip-arrow::before {
        left: -1px;
        border-width: calc(var(--bs-tooltip-arrow-width) * 0.5) 0 calc(var(--bs-tooltip-arrow-width) * 0.5) var(--bs-tooltip-arrow-height);
        border-left-color: var(--bs-tooltip-bg);
    }
    .tooltip-inner {
        max-width: var(--bs-tooltip-max-width);
        padding: var(--bs-tooltip-padding-y) var(--bs-tooltip-padding-x);
        color: var(--bs-tooltip-color);
        text-align: center;
        background-color: var(--bs-tooltip-bg);
        border-radius: var(--bs-tooltip-border-radius);
    }
    .popover {
        --bs-popover-zindex: 1070;
        --bs-popover-max-width: 276px;
        --bs-popover-font-size: 0.875rem;
        --bs-popover-bg: var(--bs-body-bg);
        --bs-popover-border-width: var(--bs-border-width);
        --bs-popover-border-color: var(--bs-border-color-translucent);
        --bs-popover-border-radius: var(--bs-border-radius-lg);
        --bs-popover-inner-border-radius: calc(var(--bs-border-radius-lg) - var(--bs-border-width));
        --bs-popover-box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        --bs-popover-header-padding-x: 1rem;
        --bs-popover-header-padding-y: 0.5rem;
        --bs-popover-header-font-size: 1rem;
        --bs-popover-header-color: ;
        --bs-popover-header-bg: var(--bs-secondary-bg);
        --bs-popover-body-padding-x: 1rem;
        --bs-popover-body-padding-y: 1rem;
        --bs-popover-body-color: var(--bs-body-color);
        --bs-popover-arrow-width: 1rem;
        --bs-popover-arrow-height: 0.5rem;
        --bs-popover-arrow-border: var(--bs-popover-border-color);
        z-index: var(--bs-popover-zindex);
        display: block;
        max-width: var(--bs-popover-max-width);
        font-family: var(--bs-font-sans-serif);
        font-style: normal;
        font-weight: 400;
        line-height: 1.5;
        text-align: left;
        text-align: start;
        text-decoration: none;
        text-shadow: none;
        text-transform: none;
        letter-spacing: normal;
        word-break: normal;
        white-space: normal;
        word-spacing: normal;
        line-break: auto;
        font-size: var(--bs-popover-font-size);
        word-wrap: break-word;
        background-color: var(--bs-popover-bg);
        background-clip: padding-box;
        border: var(--bs-popover-border-width) solid var(--bs-popover-border-color);
        border-radius: var(--bs-popover-border-radius);
    }
    .popover .popover-arrow {
        display: block;
        width: var(--bs-popover-arrow-width);
        height: var(--bs-popover-arrow-height);
    }
    .popover .popover-arrow::after,
    .popover .popover-arrow::before {
        position: absolute;
        display: block;
        content: "";
        border-color: transparent;
        border-style: solid;
        border-width: 0;
    }
    .bs-popover-auto[data-popper-placement^="top"] > .popover-arrow,
    .bs-popover-top > .popover-arrow {
        bottom: calc(-1 * (var(--bs-popover-arrow-height)) - var(--bs-popover-border-width));
    }
    .bs-popover-auto[data-popper-placement^="top"] > .popover-arrow::after,
    .bs-popover-auto[data-popper-placement^="top"] > .popover-arrow::before,
    .bs-popover-top > .popover-arrow::after,
    .bs-popover-top > .popover-arrow::before {
        border-width: var(--bs-popover-arrow-height) calc(var(--bs-popover-arrow-width) * 0.5) 0;
    }
    .bs-popover-auto[data-popper-placement^="top"] > .popover-arrow::before,
    .bs-popover-top > .popover-arrow::before {
        bottom: 0;
        border-top-color: var(--bs-popover-arrow-border);
    }
    .bs-popover-auto[data-popper-placement^="top"] > .popover-arrow::after,
    .bs-popover-top > .popover-arrow::after {
        bottom: var(--bs-popover-border-width);
        border-top-color: var(--bs-popover-bg);
    }
    .bs-popover-auto[data-popper-placement^="right"] > .popover-arrow,
    .bs-popover-end > .popover-arrow {
        left: calc(-1 * (var(--bs-popover-arrow-height)) - var(--bs-popover-border-width));
        width: var(--bs-popover-arrow-height);
        height: var(--bs-popover-arrow-width);
    }
    .bs-popover-auto[data-popper-placement^="right"] > .popover-arrow::after,
    .bs-popover-auto[data-popper-placement^="right"] > .popover-arrow::before,
    .bs-popover-end > .popover-arrow::after,
    .bs-popover-end > .popover-arrow::before {
        border-width: calc(var(--bs-popover-arrow-width) * 0.5) var(--bs-popover-arrow-height) calc(var(--bs-popover-arrow-width) * 0.5) 0;
    }
    .bs-popover-auto[data-popper-placement^="right"] > .popover-arrow::before,
    .bs-popover-end > .popover-arrow::before {
        left: 0;
        border-right-color: var(--bs-popover-arrow-border);
    }
    .bs-popover-auto[data-popper-placement^="right"] > .popover-arrow::after,
    .bs-popover-end > .popover-arrow::after {
        left: var(--bs-popover-border-width);
        border-right-color: var(--bs-popover-bg);
    }
    .bs-popover-auto[data-popper-placement^="bottom"] > .popover-arrow,
    .bs-popover-bottom > .popover-arrow {
        top: calc(-1 * (var(--bs-popover-arrow-height)) - var(--bs-popover-border-width));
    }
    .bs-popover-auto[data-popper-placement^="bottom"] > .popover-arrow::after,
    .bs-popover-auto[data-popper-placement^="bottom"] > .popover-arrow::before,
    .bs-popover-bottom > .popover-arrow::after,
    .bs-popover-bottom > .popover-arrow::before {
        border-width: 0 calc(var(--bs-popover-arrow-width) * 0.5) var(--bs-popover-arrow-height);
    }
    .bs-popover-auto[data-popper-placement^="bottom"] > .popover-arrow::before,
    .bs-popover-bottom > .popover-arrow::before {
        top: 0;
        border-bottom-color: var(--bs-popover-arrow-border);
    }
    .bs-popover-auto[data-popper-placement^="bottom"] > .popover-arrow::after,
    .bs-popover-bottom > .popover-arrow::after {
        top: var(--bs-popover-border-width);
        border-bottom-color: var(--bs-popover-bg);
    }
    .bs-popover-auto[data-popper-placement^="bottom"] .popover-header::before,
    .bs-popover-bottom .popover-header::before {
        position: absolute;
        top: 0;
        left: 50%;
        display: block;
        width: var(--bs-popover-arrow-width);
        margin-left: calc(-0.5 * var(--bs-popover-arrow-width));
        content: "";
        border-bottom: var(--bs-popover-border-width) solid var(--bs-popover-header-bg);
    }
    .bs-popover-auto[data-popper-placement^="left"] > .popover-arrow,
    .bs-popover-start > .popover-arrow {
        right: calc(-1 * (var(--bs-popover-arrow-height)) - var(--bs-popover-border-width));
        width: var(--bs-popover-arrow-height);
        height: var(--bs-popover-arrow-width);
    }
    .bs-popover-auto[data-popper-placement^="left"] > .popover-arrow::after,
    .bs-popover-auto[data-popper-placement^="left"] > .popover-arrow::before,
    .bs-popover-start > .popover-arrow::after,
    .bs-popover-start > .popover-arrow::before {
        border-width: calc(var(--bs-popover-arrow-width) * 0.5) 0 calc(var(--bs-popover-arrow-width) * 0.5) var(--bs-popover-arrow-height);
    }
    .bs-popover-auto[data-popper-placement^="left"] > .popover-arrow::before,
    .bs-popover-start > .popover-arrow::before {
        right: 0;
        border-left-color: var(--bs-popover-arrow-border);
    }
    .bs-popover-auto[data-popper-placement^="left"] > .popover-arrow::after,
    .bs-popover-start > .popover-arrow::after {
        right: var(--bs-popover-border-width);
        border-left-color: var(--bs-popover-bg);
    }
    .popover-header {
        padding: var(--bs-popover-header-padding-y) var(--bs-popover-header-padding-x);
        margin-bottom: 0;
        font-size: var(--bs-popover-header-font-size);
        color: var(--bs-popover-header-color);
        background-color: var(--bs-popover-header-bg);
        border-bottom: var(--bs-popover-border-width) solid var(--bs-popover-border-color);
        border-top-left-radius: var(--bs-popover-inner-border-radius);
        border-top-right-radius: var(--bs-popover-inner-border-radius);
    }
    .popover-header:empty {
        display: none;
    }
    .popover-body {
        padding: var(--bs-popover-body-padding-y) var(--bs-popover-body-padding-x);
        color: var(--bs-popover-body-color);
    }
    .carousel {
        position: relative;
    }
    .carousel.pointer-event {
        touch-action: pan-y;
    }
    .carousel-inner {
        position: relative;
        width: 100%;
        overflow: hidden;
    }
    .carousel-inner::after {
        display: block;
        clear: both;
        content: "";
    }
    .carousel-item {
        position: relative;
        display: none;
        float: left;
        width: 100%;
        margin-right: -100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        transition: transform 0.6s ease-in-out;
    }
    @media (prefers-reduced-motion: reduce) {
        .carousel-item {
            transition: none;
        }
    }
    .carousel-item-next,
    .carousel-item-prev,
    .carousel-item.active {
        display: block;
    }
    .active.carousel-item-end,
    .carousel-item-next:not(.carousel-item-start) {
        transform: translateX(100%);
    }
    .active.carousel-item-start,
    .carousel-item-prev:not(.carousel-item-end) {
        transform: translateX(-100%);
    }
    .carousel-fade .carousel-item {
        opacity: 0;
        transition-property: opacity;
        transform: none;
    }
    .carousel-fade .carousel-item-next.carousel-item-start,
    .carousel-fade .carousel-item-prev.carousel-item-end,
    .carousel-fade .carousel-item.active {
        z-index: 1;
        opacity: 1;
    }
    .carousel-fade .active.carousel-item-end,
    .carousel-fade .active.carousel-item-start {
        z-index: 0;
        opacity: 0;
        transition: opacity 0s 0.6s;
    }
    @media (prefers-reduced-motion: reduce) {
        .carousel-fade .active.carousel-item-end,
        .carousel-fade .active.carousel-item-start {
            transition: none;
        }
    }
    .carousel-control-next,
    .carousel-control-prev {
        position: absolute;
        top: 0;
        bottom: 0;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 15%;
        padding: 0;
        color: #fff;
        text-align: center;
        background: 0 0;
        border: 0;
        opacity: 0.5;
        transition: opacity 0.15s ease;
    }
    @media (prefers-reduced-motion: reduce) {
        .carousel-control-next,
        .carousel-control-prev {
            transition: none;
        }
    }
    .carousel-control-next:focus,
    .carousel-control-next:hover,
    .carousel-control-prev:focus,
    .carousel-control-prev:hover {
        color: #fff;
        text-decoration: none;
        outline: 0;
        opacity: 0.9;
    }
    .carousel-control-prev {
        left: 0;
    }
    .carousel-control-next {
        right: 0;
    }
    .carousel-control-next-icon,
    .carousel-control-prev-icon {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        background-repeat: no-repeat;
        background-position: 50%;
        background-size: 100% 100%;
    }
    .carousel-control-prev-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z'/%3e%3c/svg%3e");
    }
    .carousel-control-next-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    .carousel-indicators {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 2;
        display: flex;
        justify-content: center;
        padding: 0;
        margin-right: 15%;
        margin-bottom: 1rem;
        margin-left: 15%;
    }
    .carousel-indicators [data-bs-target] {
        box-sizing: content-box;
        flex: 0 1 auto;
        width: 30px;
        height: 3px;
        padding: 0;
        margin-right: 3px;
        margin-left: 3px;
        text-indent: -999px;
        cursor: pointer;
        background-color: #fff;
        background-clip: padding-box;
        border: 0;
        border-top: 10px solid transparent;
        border-bottom: 10px solid transparent;
        opacity: 0.5;
        transition: opacity 0.6s ease;
    }
    @media (prefers-reduced-motion: reduce) {
        .carousel-indicators [data-bs-target] {
            transition: none;
        }
    }
    .carousel-indicators .active {
        opacity: 1;
    }
    .carousel-caption {
        position: absolute;
        right: 15%;
        bottom: 1.25rem;
        left: 15%;
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
        color: #fff;
        text-align: center;
    }
    .carousel-dark .carousel-control-next-icon,
    .carousel-dark .carousel-control-prev-icon {
        filter: invert(1) grayscale(100);
    }
    .carousel-dark .carousel-indicators [data-bs-target] {
        background-color: #000;
    }
    .carousel-dark .carousel-caption {
        color: #000;
    }
    [data-bs-theme="dark"] .carousel .carousel-control-next-icon,
    [data-bs-theme="dark"] .carousel .carousel-control-prev-icon,
    [data-bs-theme="dark"].carousel .carousel-control-next-icon,
    [data-bs-theme="dark"].carousel .carousel-control-prev-icon {
        filter: invert(1) grayscale(100);
    }
    [data-bs-theme="dark"] .carousel .carousel-indicators [data-bs-target],
    [data-bs-theme="dark"].carousel .carousel-indicators [data-bs-target] {
        background-color: #000;
    }
    [data-bs-theme="dark"] .carousel .carousel-caption,
    [data-bs-theme="dark"].carousel .carousel-caption {
        color: #000;
    }
    .spinner-border,
    .spinner-grow {
        display: inline-block;
        width: var(--bs-spinner-width);
        height: var(--bs-spinner-height);
        vertical-align: var(--bs-spinner-vertical-align);
        border-radius: 50%;
        animation: var(--bs-spinner-animation-speed) linear infinite var(--bs-spinner-animation-name);
    }
    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }
    .spinner-border {
        --bs-spinner-width: 2rem;
        --bs-spinner-height: 2rem;
        --bs-spinner-vertical-align: -0.125em;
        --bs-spinner-border-width: 0.25em;
        --bs-spinner-animation-speed: 0.75s;
        --bs-spinner-animation-name: spinner-border;
        border: var(--bs-spinner-border-width) solid currentcolor;
        border-right-color: transparent;
    }
    .spinner-border-sm {
        --bs-spinner-width: 1rem;
        --bs-spinner-height: 1rem;
        --bs-spinner-border-width: 0.2em;
    }
    @keyframes spinner-grow {
        0% {
            transform: scale(0);
        }
        50% {
            opacity: 1;
            transform: none;
        }
    }
    .spinner-grow {
        --bs-spinner-width: 2rem;
        --bs-spinner-height: 2rem;
        --bs-spinner-vertical-align: -0.125em;
        --bs-spinner-animation-speed: 0.75s;
        --bs-spinner-animation-name: spinner-grow;
        background-color: currentcolor;
        opacity: 0;
    }
    .spinner-grow-sm {
        --bs-spinner-width: 1rem;
        --bs-spinner-height: 1rem;
    }
    @media (prefers-reduced-motion: reduce) {
        .spinner-border,
        .spinner-grow {
            --bs-spinner-animation-speed: 1.5s;
        }
    }
    .offcanvas,
    .offcanvas-lg,
    .offcanvas-md,
    .offcanvas-sm,
    .offcanvas-xl,
    .offcanvas-xxl {
        --bs-offcanvas-zindex: 1045;
        --bs-offcanvas-width: 400px;
        --bs-offcanvas-height: 30vh;
        --bs-offcanvas-padding-x: 1rem;
        --bs-offcanvas-padding-y: 1rem;
        --bs-offcanvas-color: var(--bs-body-color);
        --bs-offcanvas-bg: var(--bs-body-bg);
        --bs-offcanvas-border-width: var(--bs-border-width);
        --bs-offcanvas-border-color: var(--bs-border-color-translucent);
        --bs-offcanvas-box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --bs-offcanvas-transition: transform 0.3s ease-in-out;
        --bs-offcanvas-title-line-height: 1.5;
    }
    @media (max-width: 575.98px) {
        .offcanvas-sm {
            position: fixed;
            bottom: 0;
            z-index: var(--bs-offcanvas-zindex);
            display: flex;
            flex-direction: column;
            max-width: 100%;
            color: var(--bs-offcanvas-color);
            visibility: hidden;
            background-color: var(--bs-offcanvas-bg);
            background-clip: padding-box;
            outline: 0;
            transition: var(--bs-offcanvas-transition);
        }
    }
    @media (max-width: 575.98px) and (prefers-reduced-motion: reduce) {
        .offcanvas-sm {
            transition: none;
        }
    }
    @media (max-width: 575.98px) {
        .offcanvas-sm.offcanvas-start {
            top: 0;
            left: 0;
            width: var(--bs-offcanvas-width);
            border-right: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(-100%);
        }
    }
    @media (max-width: 575.98px) {
        .offcanvas-sm.offcanvas-end {
            top: 0;
            right: 0;
            width: var(--bs-offcanvas-width);
            border-left: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(100%);
        }
    }
    @media (max-width: 575.98px) {
        .offcanvas-sm.offcanvas-top {
            top: 0;
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-bottom: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(-100%);
        }
    }
    @media (max-width: 575.98px) {
        .offcanvas-sm.offcanvas-bottom {
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-top: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(100%);
        }
    }
    @media (max-width: 575.98px) {
        .offcanvas-sm.show:not(.hiding),
        .offcanvas-sm.showing {
            transform: none;
        }
    }
    @media (max-width: 575.98px) {
        .offcanvas-sm.hiding,
        .offcanvas-sm.show,
        .offcanvas-sm.showing {
            visibility: visible;
        }
    }
    @media (min-width: 576px) {
        .offcanvas-sm {
            --bs-offcanvas-height: auto;
            --bs-offcanvas-border-width: 0;
            background-color: transparent !important;
        }
        .offcanvas-sm .offcanvas-header {
            display: none;
        }
        .offcanvas-sm .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
            background-color: transparent !important;
        }
    }
    @media (max-width: 767.98px) {
        .offcanvas-md {
            position: fixed;
            bottom: 0;
            z-index: var(--bs-offcanvas-zindex);
            display: flex;
            flex-direction: column;
            max-width: 100%;
            color: var(--bs-offcanvas-color);
            visibility: hidden;
            background-color: var(--bs-offcanvas-bg);
            background-clip: padding-box;
            outline: 0;
            transition: var(--bs-offcanvas-transition);
        }
    }
    @media (max-width: 767.98px) and (prefers-reduced-motion: reduce) {
        .offcanvas-md {
            transition: none;
        }
    }
    @media (max-width: 767.98px) {
        .offcanvas-md.offcanvas-start {
            top: 0;
            left: 0;
            width: var(--bs-offcanvas-width);
            border-right: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(-100%);
        }
    }
    @media (max-width: 767.98px) {
        .offcanvas-md.offcanvas-end {
            top: 0;
            right: 0;
            width: var(--bs-offcanvas-width);
            border-left: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(100%);
        }
    }
    @media (max-width: 767.98px) {
        .offcanvas-md.offcanvas-top {
            top: 0;
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-bottom: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(-100%);
        }
    }
    @media (max-width: 767.98px) {
        .offcanvas-md.offcanvas-bottom {
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-top: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(100%);
        }
    }
    @media (max-width: 767.98px) {
        .offcanvas-md.show:not(.hiding),
        .offcanvas-md.showing {
            transform: none;
        }
    }
    @media (max-width: 767.98px) {
        .offcanvas-md.hiding,
        .offcanvas-md.show,
        .offcanvas-md.showing {
            visibility: visible;
        }
    }
    @media (min-width: 768px) {
        .offcanvas-md {
            --bs-offcanvas-height: auto;
            --bs-offcanvas-border-width: 0;
            background-color: transparent !important;
        }
        .offcanvas-md .offcanvas-header {
            display: none;
        }
        .offcanvas-md .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
            background-color: transparent !important;
        }
    }
    @media (max-width: 991.98px) {
        .offcanvas-lg {
            position: fixed;
            bottom: 0;
            z-index: var(--bs-offcanvas-zindex);
            display: flex;
            flex-direction: column;
            max-width: 100%;
            color: var(--bs-offcanvas-color);
            visibility: hidden;
            background-color: var(--bs-offcanvas-bg);
            background-clip: padding-box;
            outline: 0;
            transition: var(--bs-offcanvas-transition);
        }
    }
    @media (max-width: 991.98px) and (prefers-reduced-motion: reduce) {
        .offcanvas-lg {
            transition: none;
        }
    }
    @media (max-width: 991.98px) {
        .offcanvas-lg.offcanvas-start {
            top: 0;
            left: 0;
            width: var(--bs-offcanvas-width);
            border-right: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(-100%);
        }
    }
    @media (max-width: 991.98px) {
        .offcanvas-lg.offcanvas-end {
            top: 0;
            right: 0;
            width: var(--bs-offcanvas-width);
            border-left: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(100%);
        }
    }
    @media (max-width: 991.98px) {
        .offcanvas-lg.offcanvas-top {
            top: 0;
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-bottom: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(-100%);
        }
    }
    @media (max-width: 991.98px) {
        .offcanvas-lg.offcanvas-bottom {
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-top: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(100%);
        }
    }
    @media (max-width: 991.98px) {
        .offcanvas-lg.show:not(.hiding),
        .offcanvas-lg.showing {
            transform: none;
        }
    }
    @media (max-width: 991.98px) {
        .offcanvas-lg.hiding,
        .offcanvas-lg.show,
        .offcanvas-lg.showing {
            visibility: visible;
        }
    }
    @media (min-width: 992px) {
        .offcanvas-lg {
            --bs-offcanvas-height: auto;
            --bs-offcanvas-border-width: 0;
            background-color: transparent !important;
        }
        .offcanvas-lg .offcanvas-header {
            display: none;
        }
        .offcanvas-lg .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
            background-color: transparent !important;
        }
    }
    @media (max-width: 1199.98px) {
        .offcanvas-xl {
            position: fixed;
            bottom: 0;
            z-index: var(--bs-offcanvas-zindex);
            display: flex;
            flex-direction: column;
            max-width: 100%;
            color: var(--bs-offcanvas-color);
            visibility: hidden;
            background-color: var(--bs-offcanvas-bg);
            background-clip: padding-box;
            outline: 0;
            transition: var(--bs-offcanvas-transition);
        }
    }
    @media (max-width: 1199.98px) and (prefers-reduced-motion: reduce) {
        .offcanvas-xl {
            transition: none;
        }
    }
    @media (max-width: 1199.98px) {
        .offcanvas-xl.offcanvas-start {
            top: 0;
            left: 0;
            width: var(--bs-offcanvas-width);
            border-right: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(-100%);
        }
    }
    @media (max-width: 1199.98px) {
        .offcanvas-xl.offcanvas-end {
            top: 0;
            right: 0;
            width: var(--bs-offcanvas-width);
            border-left: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(100%);
        }
    }
    @media (max-width: 1199.98px) {
        .offcanvas-xl.offcanvas-top {
            top: 0;
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-bottom: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(-100%);
        }
    }
    @media (max-width: 1199.98px) {
        .offcanvas-xl.offcanvas-bottom {
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-top: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(100%);
        }
    }
    @media (max-width: 1199.98px) {
        .offcanvas-xl.show:not(.hiding),
        .offcanvas-xl.showing {
            transform: none;
        }
    }
    @media (max-width: 1199.98px) {
        .offcanvas-xl.hiding,
        .offcanvas-xl.show,
        .offcanvas-xl.showing {
            visibility: visible;
        }
    }
    @media (min-width: 1200px) {
        .offcanvas-xl {
            --bs-offcanvas-height: auto;
            --bs-offcanvas-border-width: 0;
            background-color: transparent !important;
        }
        .offcanvas-xl .offcanvas-header {
            display: none;
        }
        .offcanvas-xl .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
            background-color: transparent !important;
        }
    }
    @media (max-width: 1399.98px) {
        .offcanvas-xxl {
            position: fixed;
            bottom: 0;
            z-index: var(--bs-offcanvas-zindex);
            display: flex;
            flex-direction: column;
            max-width: 100%;
            color: var(--bs-offcanvas-color);
            visibility: hidden;
            background-color: var(--bs-offcanvas-bg);
            background-clip: padding-box;
            outline: 0;
            transition: var(--bs-offcanvas-transition);
        }
    }
    @media (max-width: 1399.98px) and (prefers-reduced-motion: reduce) {
        .offcanvas-xxl {
            transition: none;
        }
    }
    @media (max-width: 1399.98px) {
        .offcanvas-xxl.offcanvas-start {
            top: 0;
            left: 0;
            width: var(--bs-offcanvas-width);
            border-right: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(-100%);
        }
    }
    @media (max-width: 1399.98px) {
        .offcanvas-xxl.offcanvas-end {
            top: 0;
            right: 0;
            width: var(--bs-offcanvas-width);
            border-left: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateX(100%);
        }
    }
    @media (max-width: 1399.98px) {
        .offcanvas-xxl.offcanvas-top {
            top: 0;
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-bottom: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(-100%);
        }
    }
    @media (max-width: 1399.98px) {
        .offcanvas-xxl.offcanvas-bottom {
            right: 0;
            left: 0;
            height: var(--bs-offcanvas-height);
            max-height: 100%;
            border-top: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
            transform: translateY(100%);
        }
    }
    @media (max-width: 1399.98px) {
        .offcanvas-xxl.show:not(.hiding),
        .offcanvas-xxl.showing {
            transform: none;
        }
    }
    @media (max-width: 1399.98px) {
        .offcanvas-xxl.hiding,
        .offcanvas-xxl.show,
        .offcanvas-xxl.showing {
            visibility: visible;
        }
    }
    @media (min-width: 1400px) {
        .offcanvas-xxl {
            --bs-offcanvas-height: auto;
            --bs-offcanvas-border-width: 0;
            background-color: transparent !important;
        }
        .offcanvas-xxl .offcanvas-header {
            display: none;
        }
        .offcanvas-xxl .offcanvas-body {
            display: flex;
            flex-grow: 0;
            padding: 0;
            overflow-y: visible;
            background-color: transparent !important;
        }
    }
    .offcanvas {
        position: fixed;
        bottom: 0;
        z-index: var(--bs-offcanvas-zindex);
        display: flex;
        flex-direction: column;
        max-width: 100%;
        color: var(--bs-offcanvas-color);
        visibility: hidden;
        background-color: var(--bs-offcanvas-bg);
        background-clip: padding-box;
        outline: 0;
        transition: var(--bs-offcanvas-transition);
    }
    @media (prefers-reduced-motion: reduce) {
        .offcanvas {
            transition: none;
        }
    }
    .offcanvas.offcanvas-start {
        top: 0;
        left: 0;
        width: var(--bs-offcanvas-width);
        border-right: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
        transform: translateX(-100%);
    }
    .offcanvas.offcanvas-end {
        top: 0;
        right: 0;
        width: var(--bs-offcanvas-width);
        border-left: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
        transform: translateX(100%);
    }
    .offcanvas.offcanvas-top {
        top: 0;
        right: 0;
        left: 0;
        height: var(--bs-offcanvas-height);
        max-height: 100%;
        border-bottom: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
        transform: translateY(-100%);
    }
    .offcanvas.offcanvas-bottom {
        right: 0;
        left: 0;
        height: var(--bs-offcanvas-height);
        max-height: 100%;
        border-top: var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);
        transform: translateY(100%);
    }
    .offcanvas.show:not(.hiding),
    .offcanvas.showing {
        transform: none;
    }
    .offcanvas.hiding,
    .offcanvas.show,
    .offcanvas.showing {
        visibility: visible;
    }
    .offcanvas-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        width: 100vw;
        height: 100vh;
        background-color: #000;
    }
    .offcanvas-backdrop.fade {
        opacity: 0;
    }
    .offcanvas-backdrop.show {
        opacity: 0.5;
    }
    .offcanvas-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: var(--bs-offcanvas-padding-y) var(--bs-offcanvas-padding-x);
    }
    .offcanvas-header .btn-close {
        padding: calc(var(--bs-offcanvas-padding-y) * 0.5) calc(var(--bs-offcanvas-padding-x) * 0.5);
        margin-top: calc(-0.5 * var(--bs-offcanvas-padding-y));
        margin-right: calc(-0.5 * var(--bs-offcanvas-padding-x));
        margin-bottom: calc(-0.5 * var(--bs-offcanvas-padding-y));
    }
    .offcanvas-title {
        margin-bottom: 0;
        line-height: var(--bs-offcanvas-title-line-height);
    }
    .offcanvas-body {
        flex-grow: 1;
        padding: var(--bs-offcanvas-padding-y) var(--bs-offcanvas-padding-x);
        overflow-y: auto;
    }
    .placeholder {
        display: inline-block;
        min-height: 1em;
        vertical-align: middle;
        cursor: wait;
        background-color: currentcolor;
        opacity: 0.5;
    }
    .placeholder.btn::before {
        display: inline-block;
        content: "";
    }
    .placeholder-xs {
        min-height: 0.6em;
    }
    .placeholder-sm {
        min-height: 0.8em;
    }
    .placeholder-lg {
        min-height: 1.2em;
    }
    .placeholder-glow .placeholder {
        animation: placeholder-glow 2s ease-in-out infinite;
    }
    @keyframes placeholder-glow {
        50% {
            opacity: 0.2;
        }
    }
    .placeholder-wave {
        -webkit-mask-image: linear-gradient(130deg, #000 55%, rgba(0, 0, 0, 0.8) 75%, #000 95%);
        mask-image: linear-gradient(130deg, #000 55%, rgba(0, 0, 0, 0.8) 75%, #000 95%);
        -webkit-mask-size: 200% 100%;
        mask-size: 200% 100%;
        animation: placeholder-wave 2s linear infinite;
    }
    @keyframes placeholder-wave {
        100% {
            -webkit-mask-position: -200% 0%;
            mask-position: -200% 0%;
        }
    }
    .clearfix::after {
        display: block;
        clear: both;
        content: "";
    }
    .text-bg-primary {
        color: #fff !important;
        background-color: RGBA(13, 110, 253, var(--bs-bg-opacity, 1)) !important;
    }
    .text-bg-secondary {
        color: #fff !important;
        background-color: RGBA(108, 117, 125, var(--bs-bg-opacity, 1)) !important;
    }
    .text-bg-success {
        color: #fff !important;
        background-color: RGBA(25, 135, 84, var(--bs-bg-opacity, 1)) !important;
    }
    .text-bg-info {
        color: #000 !important;
        background-color: RGBA(13, 202, 240, var(--bs-bg-opacity, 1)) !important;
    }
    .text-bg-warning {
        color: #000 !important;
        background-color: RGBA(255, 193, 7, var(--bs-bg-opacity, 1)) !important;
    }
    .text-bg-danger {
        color: #fff !important;
        background-color: RGBA(220, 53, 69, var(--bs-bg-opacity, 1)) !important;
    }
    .text-bg-light {
        color: #000 !important;
        background-color: RGBA(248, 249, 250, var(--bs-bg-opacity, 1)) !important;
    }
    .text-bg-dark {
        color: #fff !important;
        background-color: RGBA(33, 37, 41, var(--bs-bg-opacity, 1)) !important;
    }
    .link-primary {
        color: RGBA(var(--bs-primary-rgb, var(--bs-link-opacity, 1)));
        -webkit-text-decoration-color: RGBA(var(--bs-primary-rgb), var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(var(--bs-primary-rgb), var(--bs-link-underline-opacity, 1));
    }
    .link-primary:focus,
    .link-primary:hover {
        color: RGBA(10, 88, 202, var(--bs-link-opacity, 1));
        -webkit-text-decoration-color: RGBA(10, 88, 202, var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(10, 88, 202, var(--bs-link-underline-opacity, 1));
    }
    .link-secondary {
        color: RGBA(var(--bs-secondary-rgb, var(--bs-link-opacity, 1)));
        -webkit-text-decoration-color: RGBA(var(--bs-secondary-rgb), var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(var(--bs-secondary-rgb), var(--bs-link-underline-opacity, 1));
    }
    .link-secondary:focus,
    .link-secondary:hover {
        color: RGBA(86, 94, 100, var(--bs-link-opacity, 1));
        -webkit-text-decoration-color: RGBA(86, 94, 100, var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(86, 94, 100, var(--bs-link-underline-opacity, 1));
    }
    .link-success {
        color: RGBA(var(--bs-success-rgb, var(--bs-link-opacity, 1)));
        -webkit-text-decoration-color: RGBA(var(--bs-success-rgb), var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(var(--bs-success-rgb), var(--bs-link-underline-opacity, 1));
    }
    .link-success:focus,
    .link-success:hover {
        color: RGBA(20, 108, 67, var(--bs-link-opacity, 1));
        -webkit-text-decoration-color: RGBA(20, 108, 67, var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(20, 108, 67, var(--bs-link-underline-opacity, 1));
    }
    .link-info {
        color: RGBA(var(--bs-info-rgb, var(--bs-link-opacity, 1)));
        -webkit-text-decoration-color: RGBA(var(--bs-info-rgb), var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(var(--bs-info-rgb), var(--bs-link-underline-opacity, 1));
    }
    .link-info:focus,
    .link-info:hover {
        color: RGBA(61, 213, 243, var(--bs-link-opacity, 1));
        -webkit-text-decoration-color: RGBA(61, 213, 243, var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(61, 213, 243, var(--bs-link-underline-opacity, 1));
    }
    .link-warning {
        color: RGBA(var(--bs-warning-rgb, var(--bs-link-opacity, 1)));
        -webkit-text-decoration-color: RGBA(var(--bs-warning-rgb), var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(var(--bs-warning-rgb), var(--bs-link-underline-opacity, 1));
    }
    .link-warning:focus,
    .link-warning:hover {
        color: RGBA(255, 205, 57, var(--bs-link-opacity, 1));
        -webkit-text-decoration-color: RGBA(255, 205, 57, var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(255, 205, 57, var(--bs-link-underline-opacity, 1));
    }
    .link-danger {
        color: RGBA(var(--bs-danger-rgb, var(--bs-link-opacity, 1)));
        -webkit-text-decoration-color: RGBA(var(--bs-danger-rgb), var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(var(--bs-danger-rgb), var(--bs-link-underline-opacity, 1));
    }
    .link-danger:focus,
    .link-danger:hover {
        color: RGBA(176, 42, 55, var(--bs-link-opacity, 1));
        -webkit-text-decoration-color: RGBA(176, 42, 55, var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(176, 42, 55, var(--bs-link-underline-opacity, 1));
    }
    .link-light {
        color: RGBA(var(--bs-light-rgb, var(--bs-link-opacity, 1)));
        -webkit-text-decoration-color: RGBA(var(--bs-light-rgb), var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(var(--bs-light-rgb), var(--bs-link-underline-opacity, 1));
    }
    .link-light:focus,
    .link-light:hover {
        color: RGBA(249, 250, 251, var(--bs-link-opacity, 1));
        -webkit-text-decoration-color: RGBA(249, 250, 251, var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(249, 250, 251, var(--bs-link-underline-opacity, 1));
    }
    .link-dark {
        color: RGBA(var(--bs-dark-rgb, var(--bs-link-opacity, 1)));
        -webkit-text-decoration-color: RGBA(var(--bs-dark-rgb), var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(var(--bs-dark-rgb), var(--bs-link-underline-opacity, 1));
    }
    .link-dark:focus,
    .link-dark:hover {
        color: RGBA(26, 30, 33, var(--bs-link-opacity, 1));
        -webkit-text-decoration-color: RGBA(26, 30, 33, var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(26, 30, 33, var(--bs-link-underline-opacity, 1));
    }
    .link-body-emphasis {
        color: RGBA(var(--bs-emphasis-color-rgb), var(--bs-link-opacity, 1));
        -webkit-text-decoration-color: RGBA(var(--bs-emphasis-color-rgb), var(--bs-link-underline-opacity, 1));
        text-decoration-color: RGBA(var(--bs-emphasis-color-rgb), var(--bs-link-underline-opacity, 1));
    }
    .link-body-emphasis:focus,
    .link-body-emphasis:hover {
        color: RGBA(var(--bs-emphasis-color-rgb), var(--bs-link-opacity, 0.75));
        -webkit-text-decoration-color: RGBA(var(--bs-emphasis-color-rgb), var(--bs-link-underline-opacity, 0.75));
        text-decoration-color: RGBA(var(--bs-emphasis-color-rgb), var(--bs-link-underline-opacity, 0.75));
    }
    .focus-ring:focus {
        outline: 0;
        box-shadow: var(--bs-focus-ring-x, 0) var(--bs-focus-ring-y, 0) var(--bs-focus-ring-blur, 0) var(--bs-focus-ring-width) var(--bs-focus-ring-color);
    }
    .icon-link {
        display: inline-flex;
        gap: 0.375rem;
        align-items: center;
        -webkit-text-decoration-color: rgba(var(--bs-link-color-rgb), var(--bs-link-opacity, 0.5));
        text-decoration-color: rgba(var(--bs-link-color-rgb), var(--bs-link-opacity, 0.5));
        text-underline-offset: 0.25em;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
    }
    .icon-link > .bi {
        flex-shrink: 0;
        width: 1em;
        height: 1em;
        fill: currentcolor;
        transition: 0.2s ease-in-out transform;
    }
    @media (prefers-reduced-motion: reduce) {
        .icon-link > .bi {
            transition: none;
        }
    }
    .icon-link-hover:focus-visible > .bi,
    .icon-link-hover:hover > .bi {
        transform: var(--bs-icon-link-transform, translate3d(0.25em, 0, 0));
    }
    .ratio {
        position: relative;
        width: 100%;
    }
    .ratio::before {
        display: block;
        padding-top: var(--bs-aspect-ratio);
        content: "";
    }
    .ratio > * {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .ratio-1x1 {
        --bs-aspect-ratio: 100%;
    }
    .ratio-4x3 {
        --bs-aspect-ratio: 75%;
    }
    .ratio-16x9 {
        --bs-aspect-ratio: 56.25%;
    }
    .ratio-21x9 {
        --bs-aspect-ratio: 42.8571428571%;
    }
    .fixed-top {
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1030;
    }
    .fixed-bottom {
        position: fixed;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1030;
    }
    .sticky-top {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 1020;
    }
    .sticky-bottom {
        position: -webkit-sticky;
        position: sticky;
        bottom: 0;
        z-index: 1020;
    }
    @media (min-width: 576px) {
        .sticky-sm-top {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .sticky-sm-bottom {
            position: -webkit-sticky;
            position: sticky;
            bottom: 0;
            z-index: 1020;
        }
    }
    @media (min-width: 768px) {
        .sticky-md-top {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .sticky-md-bottom {
            position: -webkit-sticky;
            position: sticky;
            bottom: 0;
            z-index: 1020;
        }
    }
    @media (min-width: 992px) {
        .sticky-lg-top {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .sticky-lg-bottom {
            position: -webkit-sticky;
            position: sticky;
            bottom: 0;
            z-index: 1020;
        }
    }
    @media (min-width: 1200px) {
        .sticky-xl-top {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .sticky-xl-bottom {
            position: -webkit-sticky;
            position: sticky;
            bottom: 0;
            z-index: 1020;
        }
    }
    @media (min-width: 1400px) {
        .sticky-xxl-top {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .sticky-xxl-bottom {
            position: -webkit-sticky;
            position: sticky;
            bottom: 0;
            z-index: 1020;
        }
    }
    .hstack {
        display: flex;
        flex-direction: row;
        align-items: center;
        align-self: stretch;
    }
    .vstack {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        align-self: stretch;
    }
    .visually-hidden,
    .visually-hidden-focusable:not(:focus):not(:focus-within) {
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }
    .visually-hidden-focusable:not(:focus):not(:focus-within):not(caption),
    .visually-hidden:not(caption) {
        position: absolute !important;
    }
    .stretched-link::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1;
        content: "";
    }
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .vr {
        display: inline-block;
        align-self: stretch;
        width: 1px;
        min-height: 1em;
        background-color: currentcolor;
        opacity: 0.25;
    }
    .align-baseline {
        vertical-align: baseline !important;
    }
    .align-top {
        vertical-align: top !important;
    }
    .align-middle {
        vertical-align: middle !important;
    }
    .align-bottom {
        vertical-align: bottom !important;
    }
    .align-text-bottom {
        vertical-align: text-bottom !important;
    }
    .align-text-top {
        vertical-align: text-top !important;
    }
    .float-start {
        float: left !important;
    }
    .float-end {
        float: right !important;
    }
    .float-none {
        float: none !important;
    }
    .object-fit-contain {
        -o-object-fit: contain !important;
        object-fit: contain !important;
    }
    .object-fit-cover {
        -o-object-fit: cover !important;
        object-fit: cover !important;
    }
    .object-fit-fill {
        -o-object-fit: fill !important;
        object-fit: fill !important;
    }
    .object-fit-scale {
        -o-object-fit: scale-down !important;
        object-fit: scale-down !important;
    }
    .object-fit-none {
        -o-object-fit: none !important;
        object-fit: none !important;
    }
    .opacity-0 {
        opacity: 0 !important;
    }
    .opacity-25 {
        opacity: 0.25 !important;
    }
    .opacity-50 {
        opacity: 0.5 !important;
    }
    .opacity-75 {
        opacity: 0.75 !important;
    }
    .opacity-100 {
        opacity: 1 !important;
    }
    .overflow-auto {
        overflow: auto !important;
    }
    .overflow-hidden {
        overflow: hidden !important;
    }
    .overflow-visible {
        overflow: visible !important;
    }
    .overflow-scroll {
        overflow: scroll !important;
    }
    .overflow-x-auto {
        overflow-x: auto !important;
    }
    .overflow-x-hidden {
        overflow-x: hidden !important;
    }
    .overflow-x-visible {
        overflow-x: visible !important;
    }
    .overflow-x-scroll {
        overflow-x: scroll !important;
    }
    .overflow-y-auto {
        overflow-y: auto !important;
    }
    .overflow-y-hidden {
        overflow-y: hidden !important;
    }
    .overflow-y-visible {
        overflow-y: visible !important;
    }
    .overflow-y-scroll {
        overflow-y: scroll !important;
    }
    .d-inline {
        display: inline !important;
    }
    .d-inline-block {
        display: inline-block !important;
    }
    .d-block {
        display: block !important;
    }
    .d-grid {
        display: grid !important;
    }
    .d-inline-grid {
        display: inline-grid !important;
    }
    .d-table {
        display: table !important;
    }
    .d-table-row {
        display: table-row !important;
    }
    .d-table-cell {
        display: table-cell !important;
    }
    .d-flex {
        display: flex !important;
    }
    .d-inline-flex {
        display: inline-flex !important;
    }
    .d-none {
        display: none !important;
    }
    .shadow {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    .shadow-lg {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }
    .shadow-none {
        box-shadow: none !important;
    }
    .focus-ring-primary {
        --bs-focus-ring-color: rgba(var(--bs-primary-rgb), var(--bs-focus-ring-opacity));
    }
    .focus-ring-secondary {
        --bs-focus-ring-color: rgba(var(--bs-secondary-rgb), var(--bs-focus-ring-opacity));
    }
    .focus-ring-success {
        --bs-focus-ring-color: rgba(var(--bs-success-rgb), var(--bs-focus-ring-opacity));
    }
    .focus-ring-info {
        --bs-focus-ring-color: rgba(var(--bs-info-rgb), var(--bs-focus-ring-opacity));
    }
    .focus-ring-warning {
        --bs-focus-ring-color: rgba(var(--bs-warning-rgb), var(--bs-focus-ring-opacity));
    }
    .focus-ring-danger {
        --bs-focus-ring-color: rgba(var(--bs-danger-rgb), var(--bs-focus-ring-opacity));
    }
    .focus-ring-light {
        --bs-focus-ring-color: rgba(var(--bs-light-rgb), var(--bs-focus-ring-opacity));
    }
    .focus-ring-dark {
        --bs-focus-ring-color: rgba(var(--bs-dark-rgb), var(--bs-focus-ring-opacity));
    }
    .position-static {
        position: static !important;
    }
    .position-relative {
        position: relative !important;
    }
    .position-absolute {
        position: absolute !important;
    }
    .position-fixed {
        position: fixed !important;
    }
    .position-sticky {
        position: -webkit-sticky !important;
        position: sticky !important;
    }
    .top-0 {
        top: 0 !important;
    }
    .top-50 {
        top: 50% !important;
    }
    .top-100 {
        top: 100% !important;
    }
    .bottom-0 {
        bottom: 0 !important;
    }
    .bottom-50 {
        bottom: 50% !important;
    }
    .bottom-100 {
        bottom: 100% !important;
    }
    .start-0 {
        left: 0 !important;
    }
    .start-50 {
        left: 50% !important;
    }
    .start-100 {
        left: 100% !important;
    }
    .end-0 {
        right: 0 !important;
    }
    .end-50 {
        right: 50% !important;
    }
    .end-100 {
        right: 100% !important;
    }
    .translate-middle {
        transform: translate(-50%, -50%) !important;
    }
    .translate-middle-x {
        transform: translateX(-50%) !important;
    }
    .translate-middle-y {
        transform: translateY(-50%) !important;
    }
    .border {
        border: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important;
    }
    .border-0 {
        border: 0 !important;
    }
    .border-top {
        border-top: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important;
    }
    .border-top-0 {
        border-top: 0 !important;
    }
    .border-end {
        border-right: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important;
    }
    .border-end-0 {
        border-right: 0 !important;
    }
    .border-bottom {
        border-bottom: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important;
    }
    .border-bottom-0 {
        border-bottom: 0 !important;
    }
    .border-start {
        border-left: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important;
    }
    .border-start-0 {
        border-left: 0 !important;
    }
    .border-primary {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-primary-rgb), var(--bs-border-opacity)) !important;
    }
    .border-secondary {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-secondary-rgb), var(--bs-border-opacity)) !important;
    }
    .border-success {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-success-rgb), var(--bs-border-opacity)) !important;
    }
    .border-info {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-info-rgb), var(--bs-border-opacity)) !important;
    }
    .border-warning {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-warning-rgb), var(--bs-border-opacity)) !important;
    }
    .border-danger {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-danger-rgb), var(--bs-border-opacity)) !important;
    }
    .border-light {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-light-rgb), var(--bs-border-opacity)) !important;
    }
    .border-dark {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-dark-rgb), var(--bs-border-opacity)) !important;
    }
    .border-black {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-black-rgb), var(--bs-border-opacity)) !important;
    }
    .border-white {
        --bs-border-opacity: 1;
        border-color: rgba(var(--bs-white-rgb), var(--bs-border-opacity)) !important;
    }
    .border-primary-subtle {
        border-color: var(--bs-primary-border-subtle) !important;
    }
    .border-secondary-subtle {
        border-color: var(--bs-secondary-border-subtle) !important;
    }
    .border-success-subtle {
        border-color: var(--bs-success-border-subtle) !important;
    }
    .border-info-subtle {
        border-color: var(--bs-info-border-subtle) !important;
    }
    .border-warning-subtle {
        border-color: var(--bs-warning-border-subtle) !important;
    }
    .border-danger-subtle {
        border-color: var(--bs-danger-border-subtle) !important;
    }
    .border-light-subtle {
        border-color: var(--bs-light-border-subtle) !important;
    }
    .border-dark-subtle {
        border-color: var(--bs-dark-border-subtle) !important;
    }
    .border-1 {
        border-width: 1px !important;
    }
    .border-2 {
        border-width: 2px !important;
    }
    .border-3 {
        border-width: 3px !important;
    }
    .border-4 {
        border-width: 4px !important;
    }
    .border-5 {
        border-width: 5px !important;
    }
    .border-opacity-10 {
        --bs-border-opacity: 0.1;
    }
    .border-opacity-25 {
        --bs-border-opacity: 0.25;
    }
    .border-opacity-50 {
        --bs-border-opacity: 0.5;
    }
    .border-opacity-75 {
        --bs-border-opacity: 0.75;
    }
    .border-opacity-100 {
        --bs-border-opacity: 1;
    }
    .w-25 {
        width: 25% !important;
    }
    .w-50 {
        width: 50% !important;
    }
    .w-75 {
        width: 75% !important;
    }
    .w-100 {
        width: 100% !important;
    }
    .w-auto {
        width: auto !important;
    }
    .mw-100 {
        max-width: 100% !important;
    }
    .vw-100 {
        width: 100vw !important;
    }
    .min-vw-100 {
        min-width: 100vw !important;
    }
    .h-25 {
        height: 25% !important;
    }
    .h-50 {
        height: 50% !important;
    }
    .h-75 {
        height: 75% !important;
    }
    .h-100 {
        height: 100% !important;
    }
    .h-auto {
        height: auto !important;
    }
    .mh-100 {
        max-height: 100% !important;
    }
    .vh-100 {
        height: 100vh !important;
    }
    .min-vh-100 {
        min-height: 100vh !important;
    }
    .flex-fill {
        flex: 1 1 auto !important;
    }
    .flex-row {
        flex-direction: row !important;
    }
    .flex-column {
        flex-direction: column !important;
    }
    .flex-row-reverse {
        flex-direction: row-reverse !important;
    }
    .flex-column-reverse {
        flex-direction: column-reverse !important;
    }
    .flex-grow-0 {
        flex-grow: 0 !important;
    }
    .flex-grow-1 {
        flex-grow: 1 !important;
    }
    .flex-shrink-0 {
        flex-shrink: 0 !important;
    }
    .flex-shrink-1 {
        flex-shrink: 1 !important;
    }
    .flex-wrap {
        flex-wrap: wrap !important;
    }
    .flex-nowrap {
        flex-wrap: nowrap !important;
    }
    .flex-wrap-reverse {
        flex-wrap: wrap-reverse !important;
    }
    .justify-content-start {
        justify-content: flex-start !important;
    }
    .justify-content-end {
        justify-content: flex-end !important;
    }
    .justify-content-center {
        justify-content: center !important;
    }
    .justify-content-between {
        justify-content: space-between !important;
    }
    .justify-content-around {
        justify-content: space-around !important;
    }
    .justify-content-evenly {
        justify-content: space-evenly !important;
    }
    .align-items-start {
        align-items: flex-start !important;
    }
    .align-items-end {
        align-items: flex-end !important;
    }
    .align-items-center {
        align-items: center !important;
    }
    .align-items-baseline {
        align-items: baseline !important;
    }
    .align-items-stretch {
        align-items: stretch !important;
    }
    .align-content-start {
        align-content: flex-start !important;
    }
    .align-content-end {
        align-content: flex-end !important;
    }
    .align-content-center {
        align-content: center !important;
    }
    .align-content-between {
        align-content: space-between !important;
    }
    .align-content-around {
        align-content: space-around !important;
    }
    .align-content-stretch {
        align-content: stretch !important;
    }
    .align-self-auto {
        align-self: auto !important;
    }
    .align-self-start {
        align-self: flex-start !important;
    }
    .align-self-end {
        align-self: flex-end !important;
    }
    .align-self-center {
        align-self: center !important;
    }
    .align-self-baseline {
        align-self: baseline !important;
    }
    .align-self-stretch {
        align-self: stretch !important;
    }
    .order-first {
        order: -1 !important;
    }
    .order-0 {
        order: 0 !important;
    }
    .order-1 {
        order: 1 !important;
    }
    .order-2 {
        order: 2 !important;
    }
    .order-3 {
        order: 3 !important;
    }
    .order-4 {
        order: 4 !important;
    }
    .order-5 {
        order: 5 !important;
    }
    .order-last {
        order: 6 !important;
    }
    .m-0 {
        margin: 0 !important;
    }
    .m-1 {
        margin: 0.25rem !important;
    }
    .m-2 {
        margin: 0.5rem !important;
    }
    .m-3 {
        margin: 1rem !important;
    }
    .m-4 {
        margin: 1.5rem !important;
    }
    .m-5 {
        margin: 3rem !important;
    }
    .m-auto {
        margin: auto !important;
    }
    .mx-0 {
        margin-right: 0 !important;
        margin-left: 0 !important;
    }
    .mx-1 {
        margin-right: 0.25rem !important;
        margin-left: 0.25rem !important;
    }
    .mx-2 {
        margin-right: 0.5rem !important;
        margin-left: 0.5rem !important;
    }
    .mx-3 {
        margin-right: 1rem !important;
        margin-left: 1rem !important;
    }
    .mx-4 {
        margin-right: 1.5rem !important;
        margin-left: 1.5rem !important;
    }
    .mx-5 {
        margin-right: 3rem !important;
        margin-left: 3rem !important;
    }
    .mx-auto {
        margin-right: auto !important;
        margin-left: auto !important;
    }
    .my-0 {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }
    .my-1 {
        margin-top: 0.25rem !important;
        margin-bottom: 0.25rem !important;
    }
    .my-2 {
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }
    .my-3 {
        margin-top: 1rem !important;
        margin-bottom: 1rem !important;
    }
    .my-4 {
        margin-top: 1.5rem !important;
        margin-bottom: 1.5rem !important;
    }
    .my-5 {
        margin-top: 3rem !important;
        margin-bottom: 3rem !important;
    }
    .my-auto {
        margin-top: auto !important;
        margin-bottom: auto !important;
    }
    .mt-0 {
        margin-top: 0 !important;
    }
    .mt-1 {
        margin-top: 0.25rem !important;
    }
    .mt-2 {
        margin-top: 0.5rem !important;
    }
    .mt-3 {
        margin-top: 1rem !important;
    }
    .mt-4 {
        margin-top: 1.5rem !important;
    }
    .mt-5 {
        margin-top: 3rem !important;
    }
    .mt-auto {
        margin-top: auto !important;
    }
    .me-0 {
        margin-right: 0 !important;
    }
    .me-1 {
        margin-right: 0.25rem !important;
    }
    .me-2 {
        margin-right: 0.5rem !important;
    }
    .me-3 {
        margin-right: 1rem !important;
    }
    .me-4 {
        margin-right: 1.5rem !important;
    }
    .me-5 {
        margin-right: 3rem !important;
    }
    .me-auto {
        margin-right: auto !important;
    }
    .mb-0 {
        margin-bottom: 0 !important;
    }
    .mb-1 {
        margin-bottom: 0.25rem !important;
    }
    .mb-2 {
        margin-bottom: 0.5rem !important;
    }
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
    .mb-5 {
        margin-bottom: 3rem !important;
    }
    .mb-auto {
        margin-bottom: auto !important;
    }
    .ms-0 {
        margin-left: 0 !important;
    }
    .ms-1 {
        margin-left: 0.25rem !important;
    }
    .ms-2 {
        margin-left: 0.5rem !important;
    }
    .ms-3 {
        margin-left: 1rem !important;
    }
    .ms-4 {
        margin-left: 1.5rem !important;
    }
    .ms-5 {
        margin-left: 3rem !important;
    }
    .ms-auto {
        margin-left: auto !important;
    }
    .p-0 {
        padding: 0 !important;
    }
    .p-1 {
        padding: 0.25rem !important;
    }
    .p-2 {
        padding: 0.5rem !important;
    }
    .p-3 {
        padding: 1rem !important;
    }
    .p-4 {
        padding: 1.5rem !important;
    }
    .p-5 {
        padding: 3rem !important;
    }
    .px-0 {
        padding-right: 0 !important;
        padding-left: 0 !important;
    }
    .px-1 {
        padding-right: 0.25rem !important;
        padding-left: 0.25rem !important;
    }
    .px-2 {
        padding-right: 0.5rem !important;
        padding-left: 0.5rem !important;
    }
    .px-3 {
        padding-right: 1rem !important;
        padding-left: 1rem !important;
    }
    .px-4 {
        padding-right: 1.5rem !important;
        padding-left: 1.5rem !important;
    }
    .px-5 {
        padding-right: 3rem !important;
        padding-left: 3rem !important;
    }
    .py-0 {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .py-1 {
        padding-top: 0.25rem !important;
        padding-bottom: 0.25rem !important;
    }
    .py-2 {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }
    .py-3 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    .py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    .py-5 {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
    }
    .pt-0 {
        padding-top: 0 !important;
    }
    .pt-1 {
        padding-top: 0.25rem !important;
    }
    .pt-2 {
        padding-top: 0.5rem !important;
    }
    .pt-3 {
        padding-top: 1rem !important;
    }
    .pt-4 {
        padding-top: 1.5rem !important;
    }
    .pt-5 {
        padding-top: 3rem !important;
    }
    .pe-0 {
        padding-right: 0 !important;
    }
    .pe-1 {
        padding-right: 0.25rem !important;
    }
    .pe-2 {
        padding-right: 0.5rem !important;
    }
    .pe-3 {
        padding-right: 1rem !important;
    }
    .pe-4 {
        padding-right: 1.5rem !important;
    }
    .pe-5 {
        padding-right: 3rem !important;
    }
    .pb-0 {
        padding-bottom: 0 !important;
    }
    .pb-1 {
        padding-bottom: 0.25rem !important;
    }
    .pb-2 {
        padding-bottom: 0.5rem !important;
    }
    .pb-3 {
        padding-bottom: 1rem !important;
    }
    .pb-4 {
        padding-bottom: 1.5rem !important;
    }
    .pb-5 {
        padding-bottom: 3rem !important;
    }
    .ps-0 {
        padding-left: 0 !important;
    }
    .ps-1 {
        padding-left: 0.25rem !important;
    }
    .ps-2 {
        padding-left: 0.5rem !important;
    }
    .ps-3 {
        padding-left: 1rem !important;
    }
    .ps-4 {
        padding-left: 1.5rem !important;
    }
    .ps-5 {
        padding-left: 3rem !important;
    }
    .gap-0 {
        gap: 0 !important;
    }
    .gap-1 {
        gap: 0.25rem !important;
    }
    .gap-2 {
        gap: 0.5rem !important;
    }
    .gap-3 {
        gap: 1rem !important;
    }
    .gap-4 {
        gap: 1.5rem !important;
    }
    .gap-5 {
        gap: 3rem !important;
    }
    .row-gap-0 {
        row-gap: 0 !important;
    }
    .row-gap-1 {
        row-gap: 0.25rem !important;
    }
    .row-gap-2 {
        row-gap: 0.5rem !important;
    }
    .row-gap-3 {
        row-gap: 1rem !important;
    }
    .row-gap-4 {
        row-gap: 1.5rem !important;
    }
    .row-gap-5 {
        row-gap: 3rem !important;
    }
    .column-gap-0 {
        -moz-column-gap: 0 !important;
        column-gap: 0 !important;
    }
    .column-gap-1 {
        -moz-column-gap: 0.25rem !important;
        column-gap: 0.25rem !important;
    }
    .column-gap-2 {
        -moz-column-gap: 0.5rem !important;
        column-gap: 0.5rem !important;
    }
    .column-gap-3 {
        -moz-column-gap: 1rem !important;
        column-gap: 1rem !important;
    }
    .column-gap-4 {
        -moz-column-gap: 1.5rem !important;
        column-gap: 1.5rem !important;
    }
    .column-gap-5 {
        -moz-column-gap: 3rem !important;
        column-gap: 3rem !important;
    }
    .font-monospace {
        font-family: var(--bs-font-monospace) !important;
    }
    .fs-1 {
        font-size: calc(1.375rem + 1.5vw) !important;
    }
    .fs-2 {
        font-size: calc(1.325rem + 0.9vw) !important;
    }
    .fs-3 {
        font-size: calc(1.3rem + 0.6vw) !important;
    }
    .fs-4 {
        font-size: calc(1.275rem + 0.3vw) !important;
    }
    .fs-5 {
        font-size: 1.25rem !important;
    }
    .fs-6 {
        font-size: 1rem !important;
    }
    .fst-italic {
        font-style: italic !important;
    }
    .fst-normal {
        font-style: normal !important;
    }
    .fw-lighter {
        font-weight: lighter !important;
    }
    .fw-light {
        font-weight: 300 !important;
    }
    .fw-normal {
        font-weight: 400 !important;
    }
    .fw-medium {
        font-weight: 500 !important;
    }
    .fw-semibold {
        font-weight: 600 !important;
    }
    .fw-bold {
        font-weight: 700 !important;
    }
    .fw-bolder {
        font-weight: bolder !important;
    }
    .lh-1 {
        line-height: 1 !important;
    }
    .lh-sm {
        line-height: 1.25 !important;
    }
    .lh-base {
        line-height: 1.5 !important;
    }
    .lh-lg {
        line-height: 2 !important;
    }
    .text-start {
        text-align: left !important;
    }
    .text-end {
        text-align: right !important;
    }
    .text-center {
        text-align: center !important;
    }
    .text-decoration-none {
        text-decoration: none !important;
    }
    .text-decoration-underline {
        text-decoration: underline !important;
    }
    .text-decoration-line-through {
        text-decoration: line-through !important;
    }
    .text-lowercase {
        text-transform: lowercase !important;
    }
    .text-uppercase {
        text-transform: uppercase !important;
    }
    .text-capitalize {
        text-transform: capitalize !important;
    }
    .text-wrap {
        white-space: normal !important;
    }
    .text-nowrap {
        white-space: nowrap !important;
    }
    .text-break {
        word-wrap: break-word !important;
        word-break: break-word !important;
    }
    .text-primary {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-primary-rgb), var(--bs-text-opacity)) !important;
    }
    .text-secondary {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-secondary-rgb), var(--bs-text-opacity)) !important;
    }
    .text-success {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-success-rgb), var(--bs-text-opacity)) !important;
    }
    .text-info {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-info-rgb), var(--bs-text-opacity)) !important;
    }
    .text-warning {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-warning-rgb), var(--bs-text-opacity)) !important;
    }
    .text-danger {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-danger-rgb), var(--bs-text-opacity)) !important;
    }
    .text-light {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-light-rgb), var(--bs-text-opacity)) !important;
    }
    .text-dark {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-dark-rgb), var(--bs-text-opacity)) !important;
    }
    .text-black {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-black-rgb), var(--bs-text-opacity)) !important;
    }
    .text-white {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-white-rgb), var(--bs-text-opacity)) !important;
    }
    .text-body {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-body-color-rgb), var(--bs-text-opacity)) !important;
    }
    .text-muted {
        --bs-text-opacity: 1;
        color: var(--bs-secondary-color) !important;
    }
    .text-black-50 {
        --bs-text-opacity: 1;
        color: rgba(0, 0, 0, 0.5) !important;
    }
    .text-white-50 {
        --bs-text-opacity: 1;
        color: rgba(255, 255, 255, 0.5) !important;
    }
    .text-body-secondary {
        --bs-text-opacity: 1;
        color: var(--bs-secondary-color) !important;
    }
    .text-body-tertiary {
        --bs-text-opacity: 1;
        color: var(--bs-tertiary-color) !important;
    }
    .text-body-emphasis {
        --bs-text-opacity: 1;
        color: var(--bs-emphasis-color) !important;
    }
    .text-reset {
        --bs-text-opacity: 1;
        color: inherit !important;
    }
    .text-opacity-25 {
        --bs-text-opacity: 0.25;
    }
    .text-opacity-50 {
        --bs-text-opacity: 0.5;
    }
    .text-opacity-75 {
        --bs-text-opacity: 0.75;
    }
    .text-opacity-100 {
        --bs-text-opacity: 1;
    }
    .text-primary-emphasis {
        color: var(--bs-primary-text-emphasis) !important;
    }
    .text-secondary-emphasis {
        color: var(--bs-secondary-text-emphasis) !important;
    }
    .text-success-emphasis {
        color: var(--bs-success-text-emphasis) !important;
    }
    .text-info-emphasis {
        color: var(--bs-info-text-emphasis) !important;
    }
    .text-warning-emphasis {
        color: var(--bs-warning-text-emphasis) !important;
    }
    .text-danger-emphasis {
        color: var(--bs-danger-text-emphasis) !important;
    }
    .text-light-emphasis {
        color: var(--bs-light-text-emphasis) !important;
    }
    .text-dark-emphasis {
        color: var(--bs-dark-text-emphasis) !important;
    }
    .link-opacity-10 {
        --bs-link-opacity: 0.1;
    }
    .link-opacity-10-hover:hover {
        --bs-link-opacity: 0.1;
    }
    .link-opacity-25 {
        --bs-link-opacity: 0.25;
    }
    .link-opacity-25-hover:hover {
        --bs-link-opacity: 0.25;
    }
    .link-opacity-50 {
        --bs-link-opacity: 0.5;
    }
    .link-opacity-50-hover:hover {
        --bs-link-opacity: 0.5;
    }
    .link-opacity-75 {
        --bs-link-opacity: 0.75;
    }
    .link-opacity-75-hover:hover {
        --bs-link-opacity: 0.75;
    }
    .link-opacity-100 {
        --bs-link-opacity: 1;
    }
    .link-opacity-100-hover:hover {
        --bs-link-opacity: 1;
    }
    .link-offset-1 {
        text-underline-offset: 0.125em !important;
    }
    .link-offset-1-hover:hover {
        text-underline-offset: 0.125em !important;
    }
    .link-offset-2 {
        text-underline-offset: 0.25em !important;
    }
    .link-offset-2-hover:hover {
        text-underline-offset: 0.25em !important;
    }
    .link-offset-3 {
        text-underline-offset: 0.375em !important;
    }
    .link-offset-3-hover:hover {
        text-underline-offset: 0.375em !important;
    }
    .link-underline-primary {
        --bs-link-underline-opacity: 1;
        -webkit-text-decoration-color: rgba(var(--bs-primary-rgb), var(--bs-link-underline-opacity)) !important;
        text-decoration-color: rgba(var(--bs-primary-rgb), var(--bs-link-underline-opacity)) !important;
    }
    .link-underline-secondary {
        --bs-link-underline-opacity: 1;
        -webkit-text-decoration-color: rgba(var(--bs-secondary-rgb), var(--bs-link-underline-opacity)) !important;
        text-decoration-color: rgba(var(--bs-secondary-rgb), var(--bs-link-underline-opacity)) !important;
    }
    .link-underline-success {
        --bs-link-underline-opacity: 1;
        -webkit-text-decoration-color: rgba(var(--bs-success-rgb), var(--bs-link-underline-opacity)) !important;
        text-decoration-color: rgba(var(--bs-success-rgb), var(--bs-link-underline-opacity)) !important;
    }
    .link-underline-info {
        --bs-link-underline-opacity: 1;
        -webkit-text-decoration-color: rgba(var(--bs-info-rgb), var(--bs-link-underline-opacity)) !important;
        text-decoration-color: rgba(var(--bs-info-rgb), var(--bs-link-underline-opacity)) !important;
    }
    .link-underline-warning {
        --bs-link-underline-opacity: 1;
        -webkit-text-decoration-color: rgba(var(--bs-warning-rgb), var(--bs-link-underline-opacity)) !important;
        text-decoration-color: rgba(var(--bs-warning-rgb), var(--bs-link-underline-opacity)) !important;
    }
    .link-underline-danger {
        --bs-link-underline-opacity: 1;
        -webkit-text-decoration-color: rgba(var(--bs-danger-rgb), var(--bs-link-underline-opacity)) !important;
        text-decoration-color: rgba(var(--bs-danger-rgb), var(--bs-link-underline-opacity)) !important;
    }
    .link-underline-light {
        --bs-link-underline-opacity: 1;
        -webkit-text-decoration-color: rgba(var(--bs-light-rgb), var(--bs-link-underline-opacity)) !important;
        text-decoration-color: rgba(var(--bs-light-rgb), var(--bs-link-underline-opacity)) !important;
    }
    .link-underline-dark {
        --bs-link-underline-opacity: 1;
        -webkit-text-decoration-color: rgba(var(--bs-dark-rgb), var(--bs-link-underline-opacity)) !important;
        text-decoration-color: rgba(var(--bs-dark-rgb), var(--bs-link-underline-opacity)) !important;
    }
    .link-underline {
        --bs-link-underline-opacity: 1;
        -webkit-text-decoration-color: rgba(var(--bs-link-color-rgb), var(--bs-link-underline-opacity, 1)) !important;
        text-decoration-color: rgba(var(--bs-link-color-rgb), var(--bs-link-underline-opacity, 1)) !important;
    }
    .link-underline-opacity-0 {
        --bs-link-underline-opacity: 0;
    }
    .link-underline-opacity-0-hover:hover {
        --bs-link-underline-opacity: 0;
    }
    .link-underline-opacity-10 {
        --bs-link-underline-opacity: 0.1;
    }
    .link-underline-opacity-10-hover:hover {
        --bs-link-underline-opacity: 0.1;
    }
    .link-underline-opacity-25 {
        --bs-link-underline-opacity: 0.25;
    }
    .link-underline-opacity-25-hover:hover {
        --bs-link-underline-opacity: 0.25;
    }
    .link-underline-opacity-50 {
        --bs-link-underline-opacity: 0.5;
    }
    .link-underline-opacity-50-hover:hover {
        --bs-link-underline-opacity: 0.5;
    }
    .link-underline-opacity-75 {
        --bs-link-underline-opacity: 0.75;
    }
    .link-underline-opacity-75-hover:hover {
        --bs-link-underline-opacity: 0.75;
    }
    .link-underline-opacity-100 {
        --bs-link-underline-opacity: 1;
    }
    .link-underline-opacity-100-hover:hover {
        --bs-link-underline-opacity: 1;
    }
    .bg-primary {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-primary-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-secondary {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-secondary-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-success {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-success-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-info {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-info-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-warning {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-warning-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-danger {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-danger-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-light {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-light-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-dark {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-dark-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-black {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-black-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-white {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-white-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-body {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-body-bg-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-transparent {
        --bs-bg-opacity: 1;
        background-color: transparent !important;
    }
    .bg-body-secondary {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-secondary-bg-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-body-tertiary {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-tertiary-bg-rgb), var(--bs-bg-opacity)) !important;
    }
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    .bg-opacity-25 {
        --bs-bg-opacity: 0.25;
    }
    .bg-opacity-50 {
        --bs-bg-opacity: 0.5;
    }
    .bg-opacity-75 {
        --bs-bg-opacity: 0.75;
    }
    .bg-opacity-100 {
        --bs-bg-opacity: 1;
    }
    .bg-primary-subtle {
        background-color: var(--bs-primary-bg-subtle) !important;
    }
    .bg-secondary-subtle {
        background-color: var(--bs-secondary-bg-subtle) !important;
    }
    .bg-success-subtle {
        background-color: var(--bs-success-bg-subtle) !important;
    }
    .bg-info-subtle {
        background-color: var(--bs-info-bg-subtle) !important;
    }
    .bg-warning-subtle {
        background-color: var(--bs-warning-bg-subtle) !important;
    }
    .bg-danger-subtle {
        background-color: var(--bs-danger-bg-subtle) !important;
    }
    .bg-light-subtle {
        background-color: var(--bs-light-bg-subtle) !important;
    }
    .bg-dark-subtle {
        background-color: var(--bs-dark-bg-subtle) !important;
    }
    .bg-gradient {
        background-image: var(--bs-gradient) !important;
    }
    .user-select-all {
        -webkit-user-select: all !important;
        -moz-user-select: all !important;
        user-select: all !important;
    }
    .user-select-auto {
        -webkit-user-select: auto !important;
        -moz-user-select: auto !important;
        user-select: auto !important;
    }
    .user-select-none {
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        user-select: none !important;
    }
    .pe-none {
        pointer-events: none !important;
    }
    .pe-auto {
        pointer-events: auto !important;
    }
    .rounded {
        border-radius: var(--bs-border-radius) !important;
    }
    .rounded-0 {
        border-radius: 0 !important;
    }
    .rounded-1 {
        border-radius: var(--bs-border-radius-sm) !important;
    }
    .rounded-2 {
        border-radius: var(--bs-border-radius) !important;
    }
    .rounded-3 {
        border-radius: var(--bs-border-radius-lg) !important;
    }
    .rounded-4 {
        border-radius: var(--bs-border-radius-xl) !important;
    }
    .rounded-5 {
        border-radius: var(--bs-border-radius-xxl) !important;
    }
    .rounded-circle {
        border-radius: 50% !important;
    }
    .rounded-pill {
        border-radius: var(--bs-border-radius-pill) !important;
    }
    .rounded-top {
        border-top-left-radius: var(--bs-border-radius) !important;
        border-top-right-radius: var(--bs-border-radius) !important;
    }
    .rounded-top-0 {
        border-top-left-radius: 0 !important;
        border-top-right-radius: 0 !important;
    }
    .rounded-top-1 {
        border-top-left-radius: var(--bs-border-radius-sm) !important;
        border-top-right-radius: var(--bs-border-radius-sm) !important;
    }
    .rounded-top-2 {
        border-top-left-radius: var(--bs-border-radius) !important;
        border-top-right-radius: var(--bs-border-radius) !important;
    }
    .rounded-top-3 {
        border-top-left-radius: var(--bs-border-radius-lg) !important;
        border-top-right-radius: var(--bs-border-radius-lg) !important;
    }
    .rounded-top-4 {
        border-top-left-radius: var(--bs-border-radius-xl) !important;
        border-top-right-radius: var(--bs-border-radius-xl) !important;
    }
    .rounded-top-5 {
        border-top-left-radius: var(--bs-border-radius-xxl) !important;
        border-top-right-radius: var(--bs-border-radius-xxl) !important;
    }
    .rounded-top-circle {
        border-top-left-radius: 50% !important;
        border-top-right-radius: 50% !important;
    }
    .rounded-top-pill {
        border-top-left-radius: var(--bs-border-radius-pill) !important;
        border-top-right-radius: var(--bs-border-radius-pill) !important;
    }
    .rounded-end {
        border-top-right-radius: var(--bs-border-radius) !important;
        border-bottom-right-radius: var(--bs-border-radius) !important;
    }
    .rounded-end-0 {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .rounded-end-1 {
        border-top-right-radius: var(--bs-border-radius-sm) !important;
        border-bottom-right-radius: var(--bs-border-radius-sm) !important;
    }
    .rounded-end-2 {
        border-top-right-radius: var(--bs-border-radius) !important;
        border-bottom-right-radius: var(--bs-border-radius) !important;
    }
    .rounded-end-3 {
        border-top-right-radius: var(--bs-border-radius-lg) !important;
        border-bottom-right-radius: var(--bs-border-radius-lg) !important;
    }
    .rounded-end-4 {
        border-top-right-radius: var(--bs-border-radius-xl) !important;
        border-bottom-right-radius: var(--bs-border-radius-xl) !important;
    }
    .rounded-end-5 {
        border-top-right-radius: var(--bs-border-radius-xxl) !important;
        border-bottom-right-radius: var(--bs-border-radius-xxl) !important;
    }
    .rounded-end-circle {
        border-top-right-radius: 50% !important;
        border-bottom-right-radius: 50% !important;
    }
    .rounded-end-pill {
        border-top-right-radius: var(--bs-border-radius-pill) !important;
        border-bottom-right-radius: var(--bs-border-radius-pill) !important;
    }
    .rounded-bottom {
        border-bottom-right-radius: var(--bs-border-radius) !important;
        border-bottom-left-radius: var(--bs-border-radius) !important;
    }
    .rounded-bottom-0 {
        border-bottom-right-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    .rounded-bottom-1 {
        border-bottom-right-radius: var(--bs-border-radius-sm) !important;
        border-bottom-left-radius: var(--bs-border-radius-sm) !important;
    }
    .rounded-bottom-2 {
        border-bottom-right-radius: var(--bs-border-radius) !important;
        border-bottom-left-radius: var(--bs-border-radius) !important;
    }
    .rounded-bottom-3 {
        border-bottom-right-radius: var(--bs-border-radius-lg) !important;
        border-bottom-left-radius: var(--bs-border-radius-lg) !important;
    }
    .rounded-bottom-4 {
        border-bottom-right-radius: var(--bs-border-radius-xl) !important;
        border-bottom-left-radius: var(--bs-border-radius-xl) !important;
    }
    .rounded-bottom-5 {
        border-bottom-right-radius: var(--bs-border-radius-xxl) !important;
        border-bottom-left-radius: var(--bs-border-radius-xxl) !important;
    }
    .rounded-bottom-circle {
        border-bottom-right-radius: 50% !important;
        border-bottom-left-radius: 50% !important;
    }
    .rounded-bottom-pill {
        border-bottom-right-radius: var(--bs-border-radius-pill) !important;
        border-bottom-left-radius: var(--bs-border-radius-pill) !important;
    }
    .rounded-start {
        border-bottom-left-radius: var(--bs-border-radius) !important;
        border-top-left-radius: var(--bs-border-radius) !important;
    }
    .rounded-start-0 {
        border-bottom-left-radius: 0 !important;
        border-top-left-radius: 0 !important;
    }
    .rounded-start-1 {
        border-bottom-left-radius: var(--bs-border-radius-sm) !important;
        border-top-left-radius: var(--bs-border-radius-sm) !important;
    }
    .rounded-start-2 {
        border-bottom-left-radius: var(--bs-border-radius) !important;
        border-top-left-radius: var(--bs-border-radius) !important;
    }
    .rounded-start-3 {
        border-bottom-left-radius: var(--bs-border-radius-lg) !important;
        border-top-left-radius: var(--bs-border-radius-lg) !important;
    }
    .rounded-start-4 {
        border-bottom-left-radius: var(--bs-border-radius-xl) !important;
        border-top-left-radius: var(--bs-border-radius-xl) !important;
    }
    .rounded-start-5 {
        border-bottom-left-radius: var(--bs-border-radius-xxl) !important;
        border-top-left-radius: var(--bs-border-radius-xxl) !important;
    }
    .rounded-start-circle {
        border-bottom-left-radius: 50% !important;
        border-top-left-radius: 50% !important;
    }
    .rounded-start-pill {
        border-bottom-left-radius: var(--bs-border-radius-pill) !important;
        border-top-left-radius: var(--bs-border-radius-pill) !important;
    }
    .visible {
        visibility: visible !important;
    }
    .invisible {
        visibility: hidden !important;
    }
    .z-n1 {
        z-index: -1 !important;
    }
    .z-0 {
        z-index: 0 !important;
    }
    .z-1 {
        z-index: 1 !important;
    }
    .z-2 {
        z-index: 2 !important;
    }
    .z-3 {
        z-index: 3 !important;
    }
    @media (min-width: 576px) {
        .float-sm-start {
            float: left !important;
        }
        .float-sm-end {
            float: right !important;
        }
        .float-sm-none {
            float: none !important;
        }
        .object-fit-sm-contain {
            -o-object-fit: contain !important;
            object-fit: contain !important;
        }
        .object-fit-sm-cover {
            -o-object-fit: cover !important;
            object-fit: cover !important;
        }
        .object-fit-sm-fill {
            -o-object-fit: fill !important;
            object-fit: fill !important;
        }
        .object-fit-sm-scale {
            -o-object-fit: scale-down !important;
            object-fit: scale-down !important;
        }
        .object-fit-sm-none {
            -o-object-fit: none !important;
            object-fit: none !important;
        }
        .d-sm-inline {
            display: inline !important;
        }
        .d-sm-inline-block {
            display: inline-block !important;
        }
        .d-sm-block {
            display: block !important;
        }
        .d-sm-grid {
            display: grid !important;
        }
        .d-sm-inline-grid {
            display: inline-grid !important;
        }
        .d-sm-table {
            display: table !important;
        }
        .d-sm-table-row {
            display: table-row !important;
        }
        .d-sm-table-cell {
            display: table-cell !important;
        }
        .d-sm-flex {
            display: flex !important;
        }
        .d-sm-inline-flex {
            display: inline-flex !important;
        }
        .d-sm-none {
            display: none !important;
        }
        .flex-sm-fill {
            flex: 1 1 auto !important;
        }
        .flex-sm-row {
            flex-direction: row !important;
        }
        .flex-sm-column {
            flex-direction: column !important;
        }
        .flex-sm-row-reverse {
            flex-direction: row-reverse !important;
        }
        .flex-sm-column-reverse {
            flex-direction: column-reverse !important;
        }
        .flex-sm-grow-0 {
            flex-grow: 0 !important;
        }
        .flex-sm-grow-1 {
            flex-grow: 1 !important;
        }
        .flex-sm-shrink-0 {
            flex-shrink: 0 !important;
        }
        .flex-sm-shrink-1 {
            flex-shrink: 1 !important;
        }
        .flex-sm-wrap {
            flex-wrap: wrap !important;
        }
        .flex-sm-nowrap {
            flex-wrap: nowrap !important;
        }
        .flex-sm-wrap-reverse {
            flex-wrap: wrap-reverse !important;
        }
        .justify-content-sm-start {
            justify-content: flex-start !important;
        }
        .justify-content-sm-end {
            justify-content: flex-end !important;
        }
        .justify-content-sm-center {
            justify-content: center !important;
        }
        .justify-content-sm-between {
            justify-content: space-between !important;
        }
        .justify-content-sm-around {
            justify-content: space-around !important;
        }
        .justify-content-sm-evenly {
            justify-content: space-evenly !important;
        }
        .align-items-sm-start {
            align-items: flex-start !important;
        }
        .align-items-sm-end {
            align-items: flex-end !important;
        }
        .align-items-sm-center {
            align-items: center !important;
        }
        .align-items-sm-baseline {
            align-items: baseline !important;
        }
        .align-items-sm-stretch {
            align-items: stretch !important;
        }
        .align-content-sm-start {
            align-content: flex-start !important;
        }
        .align-content-sm-end {
            align-content: flex-end !important;
        }
        .align-content-sm-center {
            align-content: center !important;
        }
        .align-content-sm-between {
            align-content: space-between !important;
        }
        .align-content-sm-around {
            align-content: space-around !important;
        }
        .align-content-sm-stretch {
            align-content: stretch !important;
        }
        .align-self-sm-auto {
            align-self: auto !important;
        }
        .align-self-sm-start {
            align-self: flex-start !important;
        }
        .align-self-sm-end {
            align-self: flex-end !important;
        }
        .align-self-sm-center {
            align-self: center !important;
        }
        .align-self-sm-baseline {
            align-self: baseline !important;
        }
        .align-self-sm-stretch {
            align-self: stretch !important;
        }
        .order-sm-first {
            order: -1 !important;
        }
        .order-sm-0 {
            order: 0 !important;
        }
        .order-sm-1 {
            order: 1 !important;
        }
        .order-sm-2 {
            order: 2 !important;
        }
        .order-sm-3 {
            order: 3 !important;
        }
        .order-sm-4 {
            order: 4 !important;
        }
        .order-sm-5 {
            order: 5 !important;
        }
        .order-sm-last {
            order: 6 !important;
        }
        .m-sm-0 {
            margin: 0 !important;
        }
        .m-sm-1 {
            margin: 0.25rem !important;
        }
        .m-sm-2 {
            margin: 0.5rem !important;
        }
        .m-sm-3 {
            margin: 1rem !important;
        }
        .m-sm-4 {
            margin: 1.5rem !important;
        }
        .m-sm-5 {
            margin: 3rem !important;
        }
        .m-sm-auto {
            margin: auto !important;
        }
        .mx-sm-0 {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        .mx-sm-1 {
            margin-right: 0.25rem !important;
            margin-left: 0.25rem !important;
        }
        .mx-sm-2 {
            margin-right: 0.5rem !important;
            margin-left: 0.5rem !important;
        }
        .mx-sm-3 {
            margin-right: 1rem !important;
            margin-left: 1rem !important;
        }
        .mx-sm-4 {
            margin-right: 1.5rem !important;
            margin-left: 1.5rem !important;
        }
        .mx-sm-5 {
            margin-right: 3rem !important;
            margin-left: 3rem !important;
        }
        .mx-sm-auto {
            margin-right: auto !important;
            margin-left: auto !important;
        }
        .my-sm-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        .my-sm-1 {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }
        .my-sm-2 {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        .my-sm-3 {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }
        .my-sm-4 {
            margin-top: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        .my-sm-5 {
            margin-top: 3rem !important;
            margin-bottom: 3rem !important;
        }
        .my-sm-auto {
            margin-top: auto !important;
            margin-bottom: auto !important;
        }
        .mt-sm-0 {
            margin-top: 0 !important;
        }
        .mt-sm-1 {
            margin-top: 0.25rem !important;
        }
        .mt-sm-2 {
            margin-top: 0.5rem !important;
        }
        .mt-sm-3 {
            margin-top: 1rem !important;
        }
        .mt-sm-4 {
            margin-top: 1.5rem !important;
        }
        .mt-sm-5 {
            margin-top: 3rem !important;
        }
        .mt-sm-auto {
            margin-top: auto !important;
        }
        .me-sm-0 {
            margin-right: 0 !important;
        }
        .me-sm-1 {
            margin-right: 0.25rem !important;
        }
        .me-sm-2 {
            margin-right: 0.5rem !important;
        }
        .me-sm-3 {
            margin-right: 1rem !important;
        }
        .me-sm-4 {
            margin-right: 1.5rem !important;
        }
        .me-sm-5 {
            margin-right: 3rem !important;
        }
        .me-sm-auto {
            margin-right: auto !important;
        }
        .mb-sm-0 {
            margin-bottom: 0 !important;
        }
        .mb-sm-1 {
            margin-bottom: 0.25rem !important;
        }
        .mb-sm-2 {
            margin-bottom: 0.5rem !important;
        }
        .mb-sm-3 {
            margin-bottom: 1rem !important;
        }
        .mb-sm-4 {
            margin-bottom: 1.5rem !important;
        }
        .mb-sm-5 {
            margin-bottom: 3rem !important;
        }
        .mb-sm-auto {
            margin-bottom: auto !important;
        }
        .ms-sm-0 {
            margin-left: 0 !important;
        }
        .ms-sm-1 {
            margin-left: 0.25rem !important;
        }
        .ms-sm-2 {
            margin-left: 0.5rem !important;
        }
        .ms-sm-3 {
            margin-left: 1rem !important;
        }
        .ms-sm-4 {
            margin-left: 1.5rem !important;
        }
        .ms-sm-5 {
            margin-left: 3rem !important;
        }
        .ms-sm-auto {
            margin-left: auto !important;
        }
        .p-sm-0 {
            padding: 0 !important;
        }
        .p-sm-1 {
            padding: 0.25rem !important;
        }
        .p-sm-2 {
            padding: 0.5rem !important;
        }
        .p-sm-3 {
            padding: 1rem !important;
        }
        .p-sm-4 {
            padding: 1.5rem !important;
        }
        .p-sm-5 {
            padding: 3rem !important;
        }
        .px-sm-0 {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        .px-sm-1 {
            padding-right: 0.25rem !important;
            padding-left: 0.25rem !important;
        }
        .px-sm-2 {
            padding-right: 0.5rem !important;
            padding-left: 0.5rem !important;
        }
        .px-sm-3 {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
        }
        .px-sm-4 {
            padding-right: 1.5rem !important;
            padding-left: 1.5rem !important;
        }
        .px-sm-5 {
            padding-right: 3rem !important;
            padding-left: 3rem !important;
        }
        .py-sm-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .py-sm-1 {
            padding-top: 0.25rem !important;
            padding-bottom: 0.25rem !important;
        }
        .py-sm-2 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        .py-sm-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        .py-sm-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
        .py-sm-5 {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }
        .pt-sm-0 {
            padding-top: 0 !important;
        }
        .pt-sm-1 {
            padding-top: 0.25rem !important;
        }
        .pt-sm-2 {
            padding-top: 0.5rem !important;
        }
        .pt-sm-3 {
            padding-top: 1rem !important;
        }
        .pt-sm-4 {
            padding-top: 1.5rem !important;
        }
        .pt-sm-5 {
            padding-top: 3rem !important;
        }
        .pe-sm-0 {
            padding-right: 0 !important;
        }
        .pe-sm-1 {
            padding-right: 0.25rem !important;
        }
        .pe-sm-2 {
            padding-right: 0.5rem !important;
        }
        .pe-sm-3 {
            padding-right: 1rem !important;
        }
        .pe-sm-4 {
            padding-right: 1.5rem !important;
        }
        .pe-sm-5 {
            padding-right: 3rem !important;
        }
        .pb-sm-0 {
            padding-bottom: 0 !important;
        }
        .pb-sm-1 {
            padding-bottom: 0.25rem !important;
        }
        .pb-sm-2 {
            padding-bottom: 0.5rem !important;
        }
        .pb-sm-3 {
            padding-bottom: 1rem !important;
        }
        .pb-sm-4 {
            padding-bottom: 1.5rem !important;
        }
        .pb-sm-5 {
            padding-bottom: 3rem !important;
        }
        .ps-sm-0 {
            padding-left: 0 !important;
        }
        .ps-sm-1 {
            padding-left: 0.25rem !important;
        }
        .ps-sm-2 {
            padding-left: 0.5rem !important;
        }
        .ps-sm-3 {
            padding-left: 1rem !important;
        }
        .ps-sm-4 {
            padding-left: 1.5rem !important;
        }
        .ps-sm-5 {
            padding-left: 3rem !important;
        }
        .gap-sm-0 {
            gap: 0 !important;
        }
        .gap-sm-1 {
            gap: 0.25rem !important;
        }
        .gap-sm-2 {
            gap: 0.5rem !important;
        }
        .gap-sm-3 {
            gap: 1rem !important;
        }
        .gap-sm-4 {
            gap: 1.5rem !important;
        }
        .gap-sm-5 {
            gap: 3rem !important;
        }
        .row-gap-sm-0 {
            row-gap: 0 !important;
        }
        .row-gap-sm-1 {
            row-gap: 0.25rem !important;
        }
        .row-gap-sm-2 {
            row-gap: 0.5rem !important;
        }
        .row-gap-sm-3 {
            row-gap: 1rem !important;
        }
        .row-gap-sm-4 {
            row-gap: 1.5rem !important;
        }
        .row-gap-sm-5 {
            row-gap: 3rem !important;
        }
        .column-gap-sm-0 {
            -moz-column-gap: 0 !important;
            column-gap: 0 !important;
        }
        .column-gap-sm-1 {
            -moz-column-gap: 0.25rem !important;
            column-gap: 0.25rem !important;
        }
        .column-gap-sm-2 {
            -moz-column-gap: 0.5rem !important;
            column-gap: 0.5rem !important;
        }
        .column-gap-sm-3 {
            -moz-column-gap: 1rem !important;
            column-gap: 1rem !important;
        }
        .column-gap-sm-4 {
            -moz-column-gap: 1.5rem !important;
            column-gap: 1.5rem !important;
        }
        .column-gap-sm-5 {
            -moz-column-gap: 3rem !important;
            column-gap: 3rem !important;
        }
        .text-sm-start {
            text-align: left !important;
        }
        .text-sm-end {
            text-align: right !important;
        }
        .text-sm-center {
            text-align: center !important;
        }
    }
    @media (min-width: 768px) {
        .float-md-start {
            float: left !important;
        }
        .float-md-end {
            float: right !important;
        }
        .float-md-none {
            float: none !important;
        }
        .object-fit-md-contain {
            -o-object-fit: contain !important;
            object-fit: contain !important;
        }
        .object-fit-md-cover {
            -o-object-fit: cover !important;
            object-fit: cover !important;
        }
        .object-fit-md-fill {
            -o-object-fit: fill !important;
            object-fit: fill !important;
        }
        .object-fit-md-scale {
            -o-object-fit: scale-down !important;
            object-fit: scale-down !important;
        }
        .object-fit-md-none {
            -o-object-fit: none !important;
            object-fit: none !important;
        }
        .d-md-inline {
            display: inline !important;
        }
        .d-md-inline-block {
            display: inline-block !important;
        }
        .d-md-block {
            display: block !important;
        }
        .d-md-grid {
            display: grid !important;
        }
        .d-md-inline-grid {
            display: inline-grid !important;
        }
        .d-md-table {
            display: table !important;
        }
        .d-md-table-row {
            display: table-row !important;
        }
        .d-md-table-cell {
            display: table-cell !important;
        }
        .d-md-flex {
            display: flex !important;
        }
        .d-md-inline-flex {
            display: inline-flex !important;
        }
        .d-md-none {
            display: none !important;
        }
        .flex-md-fill {
            flex: 1 1 auto !important;
        }
        .flex-md-row {
            flex-direction: row !important;
        }
        .flex-md-column {
            flex-direction: column !important;
        }
        .flex-md-row-reverse {
            flex-direction: row-reverse !important;
        }
        .flex-md-column-reverse {
            flex-direction: column-reverse !important;
        }
        .flex-md-grow-0 {
            flex-grow: 0 !important;
        }
        .flex-md-grow-1 {
            flex-grow: 1 !important;
        }
        .flex-md-shrink-0 {
            flex-shrink: 0 !important;
        }
        .flex-md-shrink-1 {
            flex-shrink: 1 !important;
        }
        .flex-md-wrap {
            flex-wrap: wrap !important;
        }
        .flex-md-nowrap {
            flex-wrap: nowrap !important;
        }
        .flex-md-wrap-reverse {
            flex-wrap: wrap-reverse !important;
        }
        .justify-content-md-start {
            justify-content: flex-start !important;
        }
        .justify-content-md-end {
            justify-content: flex-end !important;
        }
        .justify-content-md-center {
            justify-content: center !important;
        }
        .justify-content-md-between {
            justify-content: space-between !important;
        }
        .justify-content-md-around {
            justify-content: space-around !important;
        }
        .justify-content-md-evenly {
            justify-content: space-evenly !important;
        }
        .align-items-md-start {
            align-items: flex-start !important;
        }
        .align-items-md-end {
            align-items: flex-end !important;
        }
        .align-items-md-center {
            align-items: center !important;
        }
        .align-items-md-baseline {
            align-items: baseline !important;
        }
        .align-items-md-stretch {
            align-items: stretch !important;
        }
        .align-content-md-start {
            align-content: flex-start !important;
        }
        .align-content-md-end {
            align-content: flex-end !important;
        }
        .align-content-md-center {
            align-content: center !important;
        }
        .align-content-md-between {
            align-content: space-between !important;
        }
        .align-content-md-around {
            align-content: space-around !important;
        }
        .align-content-md-stretch {
            align-content: stretch !important;
        }
        .align-self-md-auto {
            align-self: auto !important;
        }
        .align-self-md-start {
            align-self: flex-start !important;
        }
        .align-self-md-end {
            align-self: flex-end !important;
        }
        .align-self-md-center {
            align-self: center !important;
        }
        .align-self-md-baseline {
            align-self: baseline !important;
        }
        .align-self-md-stretch {
            align-self: stretch !important;
        }
        .order-md-first {
            order: -1 !important;
        }
        .order-md-0 {
            order: 0 !important;
        }
        .order-md-1 {
            order: 1 !important;
        }
        .order-md-2 {
            order: 2 !important;
        }
        .order-md-3 {
            order: 3 !important;
        }
        .order-md-4 {
            order: 4 !important;
        }
        .order-md-5 {
            order: 5 !important;
        }
        .order-md-last {
            order: 6 !important;
        }
        .m-md-0 {
            margin: 0 !important;
        }
        .m-md-1 {
            margin: 0.25rem !important;
        }
        .m-md-2 {
            margin: 0.5rem !important;
        }
        .m-md-3 {
            margin: 1rem !important;
        }
        .m-md-4 {
            margin: 1.5rem !important;
        }
        .m-md-5 {
            margin: 3rem !important;
        }
        .m-md-auto {
            margin: auto !important;
        }
        .mx-md-0 {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        .mx-md-1 {
            margin-right: 0.25rem !important;
            margin-left: 0.25rem !important;
        }
        .mx-md-2 {
            margin-right: 0.5rem !important;
            margin-left: 0.5rem !important;
        }
        .mx-md-3 {
            margin-right: 1rem !important;
            margin-left: 1rem !important;
        }
        .mx-md-4 {
            margin-right: 1.5rem !important;
            margin-left: 1.5rem !important;
        }
        .mx-md-5 {
            margin-right: 3rem !important;
            margin-left: 3rem !important;
        }
        .mx-md-auto {
            margin-right: auto !important;
            margin-left: auto !important;
        }
        .my-md-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        .my-md-1 {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }
        .my-md-2 {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        .my-md-3 {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }
        .my-md-4 {
            margin-top: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        .my-md-5 {
            margin-top: 3rem !important;
            margin-bottom: 3rem !important;
        }
        .my-md-auto {
            margin-top: auto !important;
            margin-bottom: auto !important;
        }
        .mt-md-0 {
            margin-top: 0 !important;
        }
        .mt-md-1 {
            margin-top: 0.25rem !important;
        }
        .mt-md-2 {
            margin-top: 0.5rem !important;
        }
        .mt-md-3 {
            margin-top: 1rem !important;
        }
        .mt-md-4 {
            margin-top: 1.5rem !important;
        }
        .mt-md-5 {
            margin-top: 3rem !important;
        }
        .mt-md-auto {
            margin-top: auto !important;
        }
        .me-md-0 {
            margin-right: 0 !important;
        }
        .me-md-1 {
            margin-right: 0.25rem !important;
        }
        .me-md-2 {
            margin-right: 0.5rem !important;
        }
        .me-md-3 {
            margin-right: 1rem !important;
        }
        .me-md-4 {
            margin-right: 1.5rem !important;
        }
        .me-md-5 {
            margin-right: 3rem !important;
        }
        .me-md-auto {
            margin-right: auto !important;
        }
        .mb-md-0 {
            margin-bottom: 0 !important;
        }
        .mb-md-1 {
            margin-bottom: 0.25rem !important;
        }
        .mb-md-2 {
            margin-bottom: 0.5rem !important;
        }
        .mb-md-3 {
            margin-bottom: 1rem !important;
        }
        .mb-md-4 {
            margin-bottom: 1.5rem !important;
        }
        .mb-md-5 {
            margin-bottom: 3rem !important;
        }
        .mb-md-auto {
            margin-bottom: auto !important;
        }
        .ms-md-0 {
            margin-left: 0 !important;
        }
        .ms-md-1 {
            margin-left: 0.25rem !important;
        }
        .ms-md-2 {
            margin-left: 0.5rem !important;
        }
        .ms-md-3 {
            margin-left: 1rem !important;
        }
        .ms-md-4 {
            margin-left: 1.5rem !important;
        }
        .ms-md-5 {
            margin-left: 3rem !important;
        }
        .ms-md-auto {
            margin-left: auto !important;
        }
        .p-md-0 {
            padding: 0 !important;
        }
        .p-md-1 {
            padding: 0.25rem !important;
        }
        .p-md-2 {
            padding: 0.5rem !important;
        }
        .p-md-3 {
            padding: 1rem !important;
        }
        .p-md-4 {
            padding: 1.5rem !important;
        }
        .p-md-5 {
            padding: 3rem !important;
        }
        .px-md-0 {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        .px-md-1 {
            padding-right: 0.25rem !important;
            padding-left: 0.25rem !important;
        }
        .px-md-2 {
            padding-right: 0.5rem !important;
            padding-left: 0.5rem !important;
        }
        .px-md-3 {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
        }
        .px-md-4 {
            padding-right: 1.5rem !important;
            padding-left: 1.5rem !important;
        }
        .px-md-5 {
            padding-right: 3rem !important;
            padding-left: 3rem !important;
        }
        .py-md-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .py-md-1 {
            padding-top: 0.25rem !important;
            padding-bottom: 0.25rem !important;
        }
        .py-md-2 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        .py-md-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        .py-md-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
        .py-md-5 {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }
        .pt-md-0 {
            padding-top: 0 !important;
        }
        .pt-md-1 {
            padding-top: 0.25rem !important;
        }
        .pt-md-2 {
            padding-top: 0.5rem !important;
        }
        .pt-md-3 {
            padding-top: 1rem !important;
        }
        .pt-md-4 {
            padding-top: 1.5rem !important;
        }
        .pt-md-5 {
            padding-top: 3rem !important;
        }
        .pe-md-0 {
            padding-right: 0 !important;
        }
        .pe-md-1 {
            padding-right: 0.25rem !important;
        }
        .pe-md-2 {
            padding-right: 0.5rem !important;
        }
        .pe-md-3 {
            padding-right: 1rem !important;
        }
        .pe-md-4 {
            padding-right: 1.5rem !important;
        }
        .pe-md-5 {
            padding-right: 3rem !important;
        }
        .pb-md-0 {
            padding-bottom: 0 !important;
        }
        .pb-md-1 {
            padding-bottom: 0.25rem !important;
        }
        .pb-md-2 {
            padding-bottom: 0.5rem !important;
        }
        .pb-md-3 {
            padding-bottom: 1rem !important;
        }
        .pb-md-4 {
            padding-bottom: 1.5rem !important;
        }
        .pb-md-5 {
            padding-bottom: 3rem !important;
        }
        .ps-md-0 {
            padding-left: 0 !important;
        }
        .ps-md-1 {
            padding-left: 0.25rem !important;
        }
        .ps-md-2 {
            padding-left: 0.5rem !important;
        }
        .ps-md-3 {
            padding-left: 1rem !important;
        }
        .ps-md-4 {
            padding-left: 1.5rem !important;
        }
        .ps-md-5 {
            padding-left: 3rem !important;
        }
        .gap-md-0 {
            gap: 0 !important;
        }
        .gap-md-1 {
            gap: 0.25rem !important;
        }
        .gap-md-2 {
            gap: 0.5rem !important;
        }
        .gap-md-3 {
            gap: 1rem !important;
        }
        .gap-md-4 {
            gap: 1.5rem !important;
        }
        .gap-md-5 {
            gap: 3rem !important;
        }
        .row-gap-md-0 {
            row-gap: 0 !important;
        }
        .row-gap-md-1 {
            row-gap: 0.25rem !important;
        }
        .row-gap-md-2 {
            row-gap: 0.5rem !important;
        }
        .row-gap-md-3 {
            row-gap: 1rem !important;
        }
        .row-gap-md-4 {
            row-gap: 1.5rem !important;
        }
        .row-gap-md-5 {
            row-gap: 3rem !important;
        }
        .column-gap-md-0 {
            -moz-column-gap: 0 !important;
            column-gap: 0 !important;
        }
        .column-gap-md-1 {
            -moz-column-gap: 0.25rem !important;
            column-gap: 0.25rem !important;
        }
        .column-gap-md-2 {
            -moz-column-gap: 0.5rem !important;
            column-gap: 0.5rem !important;
        }
        .column-gap-md-3 {
            -moz-column-gap: 1rem !important;
            column-gap: 1rem !important;
        }
        .column-gap-md-4 {
            -moz-column-gap: 1.5rem !important;
            column-gap: 1.5rem !important;
        }
        .column-gap-md-5 {
            -moz-column-gap: 3rem !important;
            column-gap: 3rem !important;
        }
        .text-md-start {
            text-align: left !important;
        }
        .text-md-end {
            text-align: right !important;
        }
        .text-md-center {
            text-align: center !important;
        }
    }
    @media (min-width: 992px) {
        .float-lg-start {
            float: left !important;
        }
        .float-lg-end {
            float: right !important;
        }
        .float-lg-none {
            float: none !important;
        }
        .object-fit-lg-contain {
            -o-object-fit: contain !important;
            object-fit: contain !important;
        }
        .object-fit-lg-cover {
            -o-object-fit: cover !important;
            object-fit: cover !important;
        }
        .object-fit-lg-fill {
            -o-object-fit: fill !important;
            object-fit: fill !important;
        }
        .object-fit-lg-scale {
            -o-object-fit: scale-down !important;
            object-fit: scale-down !important;
        }
        .object-fit-lg-none {
            -o-object-fit: none !important;
            object-fit: none !important;
        }
        .d-lg-inline {
            display: inline !important;
        }
        .d-lg-inline-block {
            display: inline-block !important;
        }
        .d-lg-block {
            display: block !important;
        }
        .d-lg-grid {
            display: grid !important;
        }
        .d-lg-inline-grid {
            display: inline-grid !important;
        }
        .d-lg-table {
            display: table !important;
        }
        .d-lg-table-row {
            display: table-row !important;
        }
        .d-lg-table-cell {
            display: table-cell !important;
        }
        .d-lg-flex {
            display: flex !important;
        }
        .d-lg-inline-flex {
            display: inline-flex !important;
        }
        .d-lg-none {
            display: none !important;
        }
        .flex-lg-fill {
            flex: 1 1 auto !important;
        }
        .flex-lg-row {
            flex-direction: row !important;
        }
        .flex-lg-column {
            flex-direction: column !important;
        }
        .flex-lg-row-reverse {
            flex-direction: row-reverse !important;
        }
        .flex-lg-column-reverse {
            flex-direction: column-reverse !important;
        }
        .flex-lg-grow-0 {
            flex-grow: 0 !important;
        }
        .flex-lg-grow-1 {
            flex-grow: 1 !important;
        }
        .flex-lg-shrink-0 {
            flex-shrink: 0 !important;
        }
        .flex-lg-shrink-1 {
            flex-shrink: 1 !important;
        }
        .flex-lg-wrap {
            flex-wrap: wrap !important;
        }
        .flex-lg-nowrap {
            flex-wrap: nowrap !important;
        }
        .flex-lg-wrap-reverse {
            flex-wrap: wrap-reverse !important;
        }
        .justify-content-lg-start {
            justify-content: flex-start !important;
        }
        .justify-content-lg-end {
            justify-content: flex-end !important;
        }
        .justify-content-lg-center {
            justify-content: center !important;
        }
        .justify-content-lg-between {
            justify-content: space-between !important;
        }
        .justify-content-lg-around {
            justify-content: space-around !important;
        }
        .justify-content-lg-evenly {
            justify-content: space-evenly !important;
        }
        .align-items-lg-start {
            align-items: flex-start !important;
        }
        .align-items-lg-end {
            align-items: flex-end !important;
        }
        .align-items-lg-center {
            align-items: center !important;
        }
        .align-items-lg-baseline {
            align-items: baseline !important;
        }
        .align-items-lg-stretch {
            align-items: stretch !important;
        }
        .align-content-lg-start {
            align-content: flex-start !important;
        }
        .align-content-lg-end {
            align-content: flex-end !important;
        }
        .align-content-lg-center {
            align-content: center !important;
        }
        .align-content-lg-between {
            align-content: space-between !important;
        }
        .align-content-lg-around {
            align-content: space-around !important;
        }
        .align-content-lg-stretch {
            align-content: stretch !important;
        }
        .align-self-lg-auto {
            align-self: auto !important;
        }
        .align-self-lg-start {
            align-self: flex-start !important;
        }
        .align-self-lg-end {
            align-self: flex-end !important;
        }
        .align-self-lg-center {
            align-self: center !important;
        }
        .align-self-lg-baseline {
            align-self: baseline !important;
        }
        .align-self-lg-stretch {
            align-self: stretch !important;
        }
        .order-lg-first {
            order: -1 !important;
        }
        .order-lg-0 {
            order: 0 !important;
        }
        .order-lg-1 {
            order: 1 !important;
        }
        .order-lg-2 {
            order: 2 !important;
        }
        .order-lg-3 {
            order: 3 !important;
        }
        .order-lg-4 {
            order: 4 !important;
        }
        .order-lg-5 {
            order: 5 !important;
        }
        .order-lg-last {
            order: 6 !important;
        }
        .m-lg-0 {
            margin: 0 !important;
        }
        .m-lg-1 {
            margin: 0.25rem !important;
        }
        .m-lg-2 {
            margin: 0.5rem !important;
        }
        .m-lg-3 {
            margin: 1rem !important;
        }
        .m-lg-4 {
            margin: 1.5rem !important;
        }
        .m-lg-5 {
            margin: 3rem !important;
        }
        .m-lg-auto {
            margin: auto !important;
        }
        .mx-lg-0 {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        .mx-lg-1 {
            margin-right: 0.25rem !important;
            margin-left: 0.25rem !important;
        }
        .mx-lg-2 {
            margin-right: 0.5rem !important;
            margin-left: 0.5rem !important;
        }
        .mx-lg-3 {
            margin-right: 1rem !important;
            margin-left: 1rem !important;
        }
        .mx-lg-4 {
            margin-right: 1.5rem !important;
            margin-left: 1.5rem !important;
        }
        .mx-lg-5 {
            margin-right: 3rem !important;
            margin-left: 3rem !important;
        }
        .mx-lg-auto {
            margin-right: auto !important;
            margin-left: auto !important;
        }
        .my-lg-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        .my-lg-1 {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }
        .my-lg-2 {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        .my-lg-3 {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }
        .my-lg-4 {
            margin-top: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        .my-lg-5 {
            margin-top: 3rem !important;
            margin-bottom: 3rem !important;
        }
        .my-lg-auto {
            margin-top: auto !important;
            margin-bottom: auto !important;
        }
        .mt-lg-0 {
            margin-top: 0 !important;
        }
        .mt-lg-1 {
            margin-top: 0.25rem !important;
        }
        .mt-lg-2 {
            margin-top: 0.5rem !important;
        }
        .mt-lg-3 {
            margin-top: 1rem !important;
        }
        .mt-lg-4 {
            margin-top: 1.5rem !important;
        }
        .mt-lg-5 {
            margin-top: 3rem !important;
        }
        .mt-lg-auto {
            margin-top: auto !important;
        }
        .me-lg-0 {
            margin-right: 0 !important;
        }
        .me-lg-1 {
            margin-right: 0.25rem !important;
        }
        .me-lg-2 {
            margin-right: 0.5rem !important;
        }
        .me-lg-3 {
            margin-right: 1rem !important;
        }
        .me-lg-4 {
            margin-right: 1.5rem !important;
        }
        .me-lg-5 {
            margin-right: 3rem !important;
        }
        .me-lg-auto {
            margin-right: auto !important;
        }
        .mb-lg-0 {
            margin-bottom: 0 !important;
        }
        .mb-lg-1 {
            margin-bottom: 0.25rem !important;
        }
        .mb-lg-2 {
            margin-bottom: 0.5rem !important;
        }
        .mb-lg-3 {
            margin-bottom: 1rem !important;
        }
        .mb-lg-4 {
            margin-bottom: 1.5rem !important;
        }
        .mb-lg-5 {
            margin-bottom: 3rem !important;
        }
        .mb-lg-auto {
            margin-bottom: auto !important;
        }
        .ms-lg-0 {
            margin-left: 0 !important;
        }
        .ms-lg-1 {
            margin-left: 0.25rem !important;
        }
        .ms-lg-2 {
            margin-left: 0.5rem !important;
        }
        .ms-lg-3 {
            margin-left: 1rem !important;
        }
        .ms-lg-4 {
            margin-left: 1.5rem !important;
        }
        .ms-lg-5 {
            margin-left: 3rem !important;
        }
        .ms-lg-auto {
            margin-left: auto !important;
        }
        .p-lg-0 {
            padding: 0 !important;
        }
        .p-lg-1 {
            padding: 0.25rem !important;
        }
        .p-lg-2 {
            padding: 0.5rem !important;
        }
        .p-lg-3 {
            padding: 1rem !important;
        }
        .p-lg-4 {
            padding: 1.5rem !important;
        }
        .p-lg-5 {
            padding: 3rem !important;
        }
        .px-lg-0 {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        .px-lg-1 {
            padding-right: 0.25rem !important;
            padding-left: 0.25rem !important;
        }
        .px-lg-2 {
            padding-right: 0.5rem !important;
            padding-left: 0.5rem !important;
        }
        .px-lg-3 {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
        }
        .px-lg-4 {
            padding-right: 1.5rem !important;
            padding-left: 1.5rem !important;
        }
        .px-lg-5 {
            padding-right: 3rem !important;
            padding-left: 3rem !important;
        }
        .py-lg-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .py-lg-1 {
            padding-top: 0.25rem !important;
            padding-bottom: 0.25rem !important;
        }
        .py-lg-2 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        .py-lg-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        .py-lg-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
        .py-lg-5 {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }
        .pt-lg-0 {
            padding-top: 0 !important;
        }
        .pt-lg-1 {
            padding-top: 0.25rem !important;
        }
        .pt-lg-2 {
            padding-top: 0.5rem !important;
        }
        .pt-lg-3 {
            padding-top: 1rem !important;
        }
        .pt-lg-4 {
            padding-top: 1.5rem !important;
        }
        .pt-lg-5 {
            padding-top: 3rem !important;
        }
        .pe-lg-0 {
            padding-right: 0 !important;
        }
        .pe-lg-1 {
            padding-right: 0.25rem !important;
        }
        .pe-lg-2 {
            padding-right: 0.5rem !important;
        }
        .pe-lg-3 {
            padding-right: 1rem !important;
        }
        .pe-lg-4 {
            padding-right: 1.5rem !important;
        }
        .pe-lg-5 {
            padding-right: 3rem !important;
        }
        .pb-lg-0 {
            padding-bottom: 0 !important;
        }
        .pb-lg-1 {
            padding-bottom: 0.25rem !important;
        }
        .pb-lg-2 {
            padding-bottom: 0.5rem !important;
        }
        .pb-lg-3 {
            padding-bottom: 1rem !important;
        }
        .pb-lg-4 {
            padding-bottom: 1.5rem !important;
        }
        .pb-lg-5 {
            padding-bottom: 3rem !important;
        }
        .ps-lg-0 {
            padding-left: 0 !important;
        }
        .ps-lg-1 {
            padding-left: 0.25rem !important;
        }
        .ps-lg-2 {
            padding-left: 0.5rem !important;
        }
        .ps-lg-3 {
            padding-left: 1rem !important;
        }
        .ps-lg-4 {
            padding-left: 1.5rem !important;
        }
        .ps-lg-5 {
            padding-left: 3rem !important;
        }
        .gap-lg-0 {
            gap: 0 !important;
        }
        .gap-lg-1 {
            gap: 0.25rem !important;
        }
        .gap-lg-2 {
            gap: 0.5rem !important;
        }
        .gap-lg-3 {
            gap: 1rem !important;
        }
        .gap-lg-4 {
            gap: 1.5rem !important;
        }
        .gap-lg-5 {
            gap: 3rem !important;
        }
        .row-gap-lg-0 {
            row-gap: 0 !important;
        }
        .row-gap-lg-1 {
            row-gap: 0.25rem !important;
        }
        .row-gap-lg-2 {
            row-gap: 0.5rem !important;
        }
        .row-gap-lg-3 {
            row-gap: 1rem !important;
        }
        .row-gap-lg-4 {
            row-gap: 1.5rem !important;
        }
        .row-gap-lg-5 {
            row-gap: 3rem !important;
        }
        .column-gap-lg-0 {
            -moz-column-gap: 0 !important;
            column-gap: 0 !important;
        }
        .column-gap-lg-1 {
            -moz-column-gap: 0.25rem !important;
            column-gap: 0.25rem !important;
        }
        .column-gap-lg-2 {
            -moz-column-gap: 0.5rem !important;
            column-gap: 0.5rem !important;
        }
        .column-gap-lg-3 {
            -moz-column-gap: 1rem !important;
            column-gap: 1rem !important;
        }
        .column-gap-lg-4 {
            -moz-column-gap: 1.5rem !important;
            column-gap: 1.5rem !important;
        }
        .column-gap-lg-5 {
            -moz-column-gap: 3rem !important;
            column-gap: 3rem !important;
        }
        .text-lg-start {
            text-align: left !important;
        }
        .text-lg-end {
            text-align: right !important;
        }
        .text-lg-center {
            text-align: center !important;
        }
    }
    @media (min-width: 1200px) {
        .float-xl-start {
            float: left !important;
        }
        .float-xl-end {
            float: right !important;
        }
        .float-xl-none {
            float: none !important;
        }
        .object-fit-xl-contain {
            -o-object-fit: contain !important;
            object-fit: contain !important;
        }
        .object-fit-xl-cover {
            -o-object-fit: cover !important;
            object-fit: cover !important;
        }
        .object-fit-xl-fill {
            -o-object-fit: fill !important;
            object-fit: fill !important;
        }
        .object-fit-xl-scale {
            -o-object-fit: scale-down !important;
            object-fit: scale-down !important;
        }
        .object-fit-xl-none {
            -o-object-fit: none !important;
            object-fit: none !important;
        }
        .d-xl-inline {
            display: inline !important;
        }
        .d-xl-inline-block {
            display: inline-block !important;
        }
        .d-xl-block {
            display: block !important;
        }
        .d-xl-grid {
            display: grid !important;
        }
        .d-xl-inline-grid {
            display: inline-grid !important;
        }
        .d-xl-table {
            display: table !important;
        }
        .d-xl-table-row {
            display: table-row !important;
        }
        .d-xl-table-cell {
            display: table-cell !important;
        }
        .d-xl-flex {
            display: flex !important;
        }
        .d-xl-inline-flex {
            display: inline-flex !important;
        }
        .d-xl-none {
            display: none !important;
        }
        .flex-xl-fill {
            flex: 1 1 auto !important;
        }
        .flex-xl-row {
            flex-direction: row !important;
        }
        .flex-xl-column {
            flex-direction: column !important;
        }
        .flex-xl-row-reverse {
            flex-direction: row-reverse !important;
        }
        .flex-xl-column-reverse {
            flex-direction: column-reverse !important;
        }
        .flex-xl-grow-0 {
            flex-grow: 0 !important;
        }
        .flex-xl-grow-1 {
            flex-grow: 1 !important;
        }
        .flex-xl-shrink-0 {
            flex-shrink: 0 !important;
        }
        .flex-xl-shrink-1 {
            flex-shrink: 1 !important;
        }
        .flex-xl-wrap {
            flex-wrap: wrap !important;
        }
        .flex-xl-nowrap {
            flex-wrap: nowrap !important;
        }
        .flex-xl-wrap-reverse {
            flex-wrap: wrap-reverse !important;
        }
        .justify-content-xl-start {
            justify-content: flex-start !important;
        }
        .justify-content-xl-end {
            justify-content: flex-end !important;
        }
        .justify-content-xl-center {
            justify-content: center !important;
        }
        .justify-content-xl-between {
            justify-content: space-between !important;
        }
        .justify-content-xl-around {
            justify-content: space-around !important;
        }
        .justify-content-xl-evenly {
            justify-content: space-evenly !important;
        }
        .align-items-xl-start {
            align-items: flex-start !important;
        }
        .align-items-xl-end {
            align-items: flex-end !important;
        }
        .align-items-xl-center {
            align-items: center !important;
        }
        .align-items-xl-baseline {
            align-items: baseline !important;
        }
        .align-items-xl-stretch {
            align-items: stretch !important;
        }
        .align-content-xl-start {
            align-content: flex-start !important;
        }
        .align-content-xl-end {
            align-content: flex-end !important;
        }
        .align-content-xl-center {
            align-content: center !important;
        }
        .align-content-xl-between {
            align-content: space-between !important;
        }
        .align-content-xl-around {
            align-content: space-around !important;
        }
        .align-content-xl-stretch {
            align-content: stretch !important;
        }
        .align-self-xl-auto {
            align-self: auto !important;
        }
        .align-self-xl-start {
            align-self: flex-start !important;
        }
        .align-self-xl-end {
            align-self: flex-end !important;
        }
        .align-self-xl-center {
            align-self: center !important;
        }
        .align-self-xl-baseline {
            align-self: baseline !important;
        }
        .align-self-xl-stretch {
            align-self: stretch !important;
        }
        .order-xl-first {
            order: -1 !important;
        }
        .order-xl-0 {
            order: 0 !important;
        }
        .order-xl-1 {
            order: 1 !important;
        }
        .order-xl-2 {
            order: 2 !important;
        }
        .order-xl-3 {
            order: 3 !important;
        }
        .order-xl-4 {
            order: 4 !important;
        }
        .order-xl-5 {
            order: 5 !important;
        }
        .order-xl-last {
            order: 6 !important;
        }
        .m-xl-0 {
            margin: 0 !important;
        }
        .m-xl-1 {
            margin: 0.25rem !important;
        }
        .m-xl-2 {
            margin: 0.5rem !important;
        }
        .m-xl-3 {
            margin: 1rem !important;
        }
        .m-xl-4 {
            margin: 1.5rem !important;
        }
        .m-xl-5 {
            margin: 3rem !important;
        }
        .m-xl-auto {
            margin: auto !important;
        }
        .mx-xl-0 {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        .mx-xl-1 {
            margin-right: 0.25rem !important;
            margin-left: 0.25rem !important;
        }
        .mx-xl-2 {
            margin-right: 0.5rem !important;
            margin-left: 0.5rem !important;
        }
        .mx-xl-3 {
            margin-right: 1rem !important;
            margin-left: 1rem !important;
        }
        .mx-xl-4 {
            margin-right: 1.5rem !important;
            margin-left: 1.5rem !important;
        }
        .mx-xl-5 {
            margin-right: 3rem !important;
            margin-left: 3rem !important;
        }
        .mx-xl-auto {
            margin-right: auto !important;
            margin-left: auto !important;
        }
        .my-xl-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        .my-xl-1 {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }
        .my-xl-2 {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        .my-xl-3 {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }
        .my-xl-4 {
            margin-top: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        .my-xl-5 {
            margin-top: 3rem !important;
            margin-bottom: 3rem !important;
        }
        .my-xl-auto {
            margin-top: auto !important;
            margin-bottom: auto !important;
        }
        .mt-xl-0 {
            margin-top: 0 !important;
        }
        .mt-xl-1 {
            margin-top: 0.25rem !important;
        }
        .mt-xl-2 {
            margin-top: 0.5rem !important;
        }
        .mt-xl-3 {
            margin-top: 1rem !important;
        }
        .mt-xl-4 {
            margin-top: 1.5rem !important;
        }
        .mt-xl-5 {
            margin-top: 3rem !important;
        }
        .mt-xl-auto {
            margin-top: auto !important;
        }
        .me-xl-0 {
            margin-right: 0 !important;
        }
        .me-xl-1 {
            margin-right: 0.25rem !important;
        }
        .me-xl-2 {
            margin-right: 0.5rem !important;
        }
        .me-xl-3 {
            margin-right: 1rem !important;
        }
        .me-xl-4 {
            margin-right: 1.5rem !important;
        }
        .me-xl-5 {
            margin-right: 3rem !important;
        }
        .me-xl-auto {
            margin-right: auto !important;
        }
        .mb-xl-0 {
            margin-bottom: 0 !important;
        }
        .mb-xl-1 {
            margin-bottom: 0.25rem !important;
        }
        .mb-xl-2 {
            margin-bottom: 0.5rem !important;
        }
        .mb-xl-3 {
            margin-bottom: 1rem !important;
        }
        .mb-xl-4 {
            margin-bottom: 1.5rem !important;
        }
        .mb-xl-5 {
            margin-bottom: 3rem !important;
        }
        .mb-xl-auto {
            margin-bottom: auto !important;
        }
        .ms-xl-0 {
            margin-left: 0 !important;
        }
        .ms-xl-1 {
            margin-left: 0.25rem !important;
        }
        .ms-xl-2 {
            margin-left: 0.5rem !important;
        }
        .ms-xl-3 {
            margin-left: 1rem !important;
        }
        .ms-xl-4 {
            margin-left: 1.5rem !important;
        }
        .ms-xl-5 {
            margin-left: 3rem !important;
        }
        .ms-xl-auto {
            margin-left: auto !important;
        }
        .p-xl-0 {
            padding: 0 !important;
        }
        .p-xl-1 {
            padding: 0.25rem !important;
        }
        .p-xl-2 {
            padding: 0.5rem !important;
        }
        .p-xl-3 {
            padding: 1rem !important;
        }
        .p-xl-4 {
            padding: 1.5rem !important;
        }
        .p-xl-5 {
            padding: 3rem !important;
        }
        .px-xl-0 {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        .px-xl-1 {
            padding-right: 0.25rem !important;
            padding-left: 0.25rem !important;
        }
        .px-xl-2 {
            padding-right: 0.5rem !important;
            padding-left: 0.5rem !important;
        }
        .px-xl-3 {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
        }
        .px-xl-4 {
            padding-right: 1.5rem !important;
            padding-left: 1.5rem !important;
        }
        .px-xl-5 {
            padding-right: 3rem !important;
            padding-left: 3rem !important;
        }
        .py-xl-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .py-xl-1 {
            padding-top: 0.25rem !important;
            padding-bottom: 0.25rem !important;
        }
        .py-xl-2 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        .py-xl-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        .py-xl-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
        .py-xl-5 {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }
        .pt-xl-0 {
            padding-top: 0 !important;
        }
        .pt-xl-1 {
            padding-top: 0.25rem !important;
        }
        .pt-xl-2 {
            padding-top: 0.5rem !important;
        }
        .pt-xl-3 {
            padding-top: 1rem !important;
        }
        .pt-xl-4 {
            padding-top: 1.5rem !important;
        }
        .pt-xl-5 {
            padding-top: 3rem !important;
        }
        .pe-xl-0 {
            padding-right: 0 !important;
        }
        .pe-xl-1 {
            padding-right: 0.25rem !important;
        }
        .pe-xl-2 {
            padding-right: 0.5rem !important;
        }
        .pe-xl-3 {
            padding-right: 1rem !important;
        }
        .pe-xl-4 {
            padding-right: 1.5rem !important;
        }
        .pe-xl-5 {
            padding-right: 3rem !important;
        }
        .pb-xl-0 {
            padding-bottom: 0 !important;
        }
        .pb-xl-1 {
            padding-bottom: 0.25rem !important;
        }
        .pb-xl-2 {
            padding-bottom: 0.5rem !important;
        }
        .pb-xl-3 {
            padding-bottom: 1rem !important;
        }
        .pb-xl-4 {
            padding-bottom: 1.5rem !important;
        }
        .pb-xl-5 {
            padding-bottom: 3rem !important;
        }
        .ps-xl-0 {
            padding-left: 0 !important;
        }
        .ps-xl-1 {
            padding-left: 0.25rem !important;
        }
        .ps-xl-2 {
            padding-left: 0.5rem !important;
        }
        .ps-xl-3 {
            padding-left: 1rem !important;
        }
        .ps-xl-4 {
            padding-left: 1.5rem !important;
        }
        .ps-xl-5 {
            padding-left: 3rem !important;
        }
        .gap-xl-0 {
            gap: 0 !important;
        }
        .gap-xl-1 {
            gap: 0.25rem !important;
        }
        .gap-xl-2 {
            gap: 0.5rem !important;
        }
        .gap-xl-3 {
            gap: 1rem !important;
        }
        .gap-xl-4 {
            gap: 1.5rem !important;
        }
        .gap-xl-5 {
            gap: 3rem !important;
        }
        .row-gap-xl-0 {
            row-gap: 0 !important;
        }
        .row-gap-xl-1 {
            row-gap: 0.25rem !important;
        }
        .row-gap-xl-2 {
            row-gap: 0.5rem !important;
        }
        .row-gap-xl-3 {
            row-gap: 1rem !important;
        }
        .row-gap-xl-4 {
            row-gap: 1.5rem !important;
        }
        .row-gap-xl-5 {
            row-gap: 3rem !important;
        }
        .column-gap-xl-0 {
            -moz-column-gap: 0 !important;
            column-gap: 0 !important;
        }
        .column-gap-xl-1 {
            -moz-column-gap: 0.25rem !important;
            column-gap: 0.25rem !important;
        }
        .column-gap-xl-2 {
            -moz-column-gap: 0.5rem !important;
            column-gap: 0.5rem !important;
        }
        .column-gap-xl-3 {
            -moz-column-gap: 1rem !important;
            column-gap: 1rem !important;
        }
        .column-gap-xl-4 {
            -moz-column-gap: 1.5rem !important;
            column-gap: 1.5rem !important;
        }
        .column-gap-xl-5 {
            -moz-column-gap: 3rem !important;
            column-gap: 3rem !important;
        }
        .text-xl-start {
            text-align: left !important;
        }
        .text-xl-end {
            text-align: right !important;
        }
        .text-xl-center {
            text-align: center !important;
        }
    }
    @media (min-width: 1400px) {
        .float-xxl-start {
            float: left !important;
        }
        .float-xxl-end {
            float: right !important;
        }
        .float-xxl-none {
            float: none !important;
        }
        .object-fit-xxl-contain {
            -o-object-fit: contain !important;
            object-fit: contain !important;
        }
        .object-fit-xxl-cover {
            -o-object-fit: cover !important;
            object-fit: cover !important;
        }
        .object-fit-xxl-fill {
            -o-object-fit: fill !important;
            object-fit: fill !important;
        }
        .object-fit-xxl-scale {
            -o-object-fit: scale-down !important;
            object-fit: scale-down !important;
        }
        .object-fit-xxl-none {
            -o-object-fit: none !important;
            object-fit: none !important;
        }
        .d-xxl-inline {
            display: inline !important;
        }
        .d-xxl-inline-block {
            display: inline-block !important;
        }
        .d-xxl-block {
            display: block !important;
        }
        .d-xxl-grid {
            display: grid !important;
        }
        .d-xxl-inline-grid {
            display: inline-grid !important;
        }
        .d-xxl-table {
            display: table !important;
        }
        .d-xxl-table-row {
            display: table-row !important;
        }
        .d-xxl-table-cell {
            display: table-cell !important;
        }
        .d-xxl-flex {
            display: flex !important;
        }
        .d-xxl-inline-flex {
            display: inline-flex !important;
        }
        .d-xxl-none {
            display: none !important;
        }
        .flex-xxl-fill {
            flex: 1 1 auto !important;
        }
        .flex-xxl-row {
            flex-direction: row !important;
        }
        .flex-xxl-column {
            flex-direction: column !important;
        }
        .flex-xxl-row-reverse {
            flex-direction: row-reverse !important;
        }
        .flex-xxl-column-reverse {
            flex-direction: column-reverse !important;
        }
        .flex-xxl-grow-0 {
            flex-grow: 0 !important;
        }
        .flex-xxl-grow-1 {
            flex-grow: 1 !important;
        }
        .flex-xxl-shrink-0 {
            flex-shrink: 0 !important;
        }
        .flex-xxl-shrink-1 {
            flex-shrink: 1 !important;
        }
        .flex-xxl-wrap {
            flex-wrap: wrap !important;
        }
        .flex-xxl-nowrap {
            flex-wrap: nowrap !important;
        }
        .flex-xxl-wrap-reverse {
            flex-wrap: wrap-reverse !important;
        }
        .justify-content-xxl-start {
            justify-content: flex-start !important;
        }
        .justify-content-xxl-end {
            justify-content: flex-end !important;
        }
        .justify-content-xxl-center {
            justify-content: center !important;
        }
        .justify-content-xxl-between {
            justify-content: space-between !important;
        }
        .justify-content-xxl-around {
            justify-content: space-around !important;
        }
        .justify-content-xxl-evenly {
            justify-content: space-evenly !important;
        }
        .align-items-xxl-start {
            align-items: flex-start !important;
        }
        .align-items-xxl-end {
            align-items: flex-end !important;
        }
        .align-items-xxl-center {
            align-items: center !important;
        }
        .align-items-xxl-baseline {
            align-items: baseline !important;
        }
        .align-items-xxl-stretch {
            align-items: stretch !important;
        }
        .align-content-xxl-start {
            align-content: flex-start !important;
        }
        .align-content-xxl-end {
            align-content: flex-end !important;
        }
        .align-content-xxl-center {
            align-content: center !important;
        }
        .align-content-xxl-between {
            align-content: space-between !important;
        }
        .align-content-xxl-around {
            align-content: space-around !important;
        }
        .align-content-xxl-stretch {
            align-content: stretch !important;
        }
        .align-self-xxl-auto {
            align-self: auto !important;
        }
        .align-self-xxl-start {
            align-self: flex-start !important;
        }
        .align-self-xxl-end {
            align-self: flex-end !important;
        }
        .align-self-xxl-center {
            align-self: center !important;
        }
        .align-self-xxl-baseline {
            align-self: baseline !important;
        }
        .align-self-xxl-stretch {
            align-self: stretch !important;
        }
        .order-xxl-first {
            order: -1 !important;
        }
        .order-xxl-0 {
            order: 0 !important;
        }
        .order-xxl-1 {
            order: 1 !important;
        }
        .order-xxl-2 {
            order: 2 !important;
        }
        .order-xxl-3 {
            order: 3 !important;
        }
        .order-xxl-4 {
            order: 4 !important;
        }
        .order-xxl-5 {
            order: 5 !important;
        }
        .order-xxl-last {
            order: 6 !important;
        }
        .m-xxl-0 {
            margin: 0 !important;
        }
        .m-xxl-1 {
            margin: 0.25rem !important;
        }
        .m-xxl-2 {
            margin: 0.5rem !important;
        }
        .m-xxl-3 {
            margin: 1rem !important;
        }
        .m-xxl-4 {
            margin: 1.5rem !important;
        }
        .m-xxl-5 {
            margin: 3rem !important;
        }
        .m-xxl-auto {
            margin: auto !important;
        }
        .mx-xxl-0 {
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        .mx-xxl-1 {
            margin-right: 0.25rem !important;
            margin-left: 0.25rem !important;
        }
        .mx-xxl-2 {
            margin-right: 0.5rem !important;
            margin-left: 0.5rem !important;
        }
        .mx-xxl-3 {
            margin-right: 1rem !important;
            margin-left: 1rem !important;
        }
        .mx-xxl-4 {
            margin-right: 1.5rem !important;
            margin-left: 1.5rem !important;
        }
        .mx-xxl-5 {
            margin-right: 3rem !important;
            margin-left: 3rem !important;
        }
        .mx-xxl-auto {
            margin-right: auto !important;
            margin-left: auto !important;
        }
        .my-xxl-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        .my-xxl-1 {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }
        .my-xxl-2 {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        .my-xxl-3 {
            margin-top: 1rem !important;
            margin-bottom: 1rem !important;
        }
        .my-xxl-4 {
            margin-top: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        .my-xxl-5 {
            margin-top: 3rem !important;
            margin-bottom: 3rem !important;
        }
        .my-xxl-auto {
            margin-top: auto !important;
            margin-bottom: auto !important;
        }
        .mt-xxl-0 {
            margin-top: 0 !important;
        }
        .mt-xxl-1 {
            margin-top: 0.25rem !important;
        }
        .mt-xxl-2 {
            margin-top: 0.5rem !important;
        }
        .mt-xxl-3 {
            margin-top: 1rem !important;
        }
        .mt-xxl-4 {
            margin-top: 1.5rem !important;
        }
        .mt-xxl-5 {
            margin-top: 3rem !important;
        }
        .mt-xxl-auto {
            margin-top: auto !important;
        }
        .me-xxl-0 {
            margin-right: 0 !important;
        }
        .me-xxl-1 {
            margin-right: 0.25rem !important;
        }
        .me-xxl-2 {
            margin-right: 0.5rem !important;
        }
        .me-xxl-3 {
            margin-right: 1rem !important;
        }
        .me-xxl-4 {
            margin-right: 1.5rem !important;
        }
        .me-xxl-5 {
            margin-right: 3rem !important;
        }
        .me-xxl-auto {
            margin-right: auto !important;
        }
        .mb-xxl-0 {
            margin-bottom: 0 !important;
        }
        .mb-xxl-1 {
            margin-bottom: 0.25rem !important;
        }
        .mb-xxl-2 {
            margin-bottom: 0.5rem !important;
        }
        .mb-xxl-3 {
            margin-bottom: 1rem !important;
        }
        .mb-xxl-4 {
            margin-bottom: 1.5rem !important;
        }
        .mb-xxl-5 {
            margin-bottom: 3rem !important;
        }
        .mb-xxl-auto {
            margin-bottom: auto !important;
        }
        .ms-xxl-0 {
            margin-left: 0 !important;
        }
        .ms-xxl-1 {
            margin-left: 0.25rem !important;
        }
        .ms-xxl-2 {
            margin-left: 0.5rem !important;
        }
        .ms-xxl-3 {
            margin-left: 1rem !important;
        }
        .ms-xxl-4 {
            margin-left: 1.5rem !important;
        }
        .ms-xxl-5 {
            margin-left: 3rem !important;
        }
        .ms-xxl-auto {
            margin-left: auto !important;
        }
        .p-xxl-0 {
            padding: 0 !important;
        }
        .p-xxl-1 {
            padding: 0.25rem !important;
        }
        .p-xxl-2 {
            padding: 0.5rem !important;
        }
        .p-xxl-3 {
            padding: 1rem !important;
        }
        .p-xxl-4 {
            padding: 1.5rem !important;
        }
        .p-xxl-5 {
            padding: 3rem !important;
        }
        .px-xxl-0 {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        .px-xxl-1 {
            padding-right: 0.25rem !important;
            padding-left: 0.25rem !important;
        }
        .px-xxl-2 {
            padding-right: 0.5rem !important;
            padding-left: 0.5rem !important;
        }
        .px-xxl-3 {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
        }
        .px-xxl-4 {
            padding-right: 1.5rem !important;
            padding-left: 1.5rem !important;
        }
        .px-xxl-5 {
            padding-right: 3rem !important;
            padding-left: 3rem !important;
        }
        .py-xxl-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .py-xxl-1 {
            padding-top: 0.25rem !important;
            padding-bottom: 0.25rem !important;
        }
        .py-xxl-2 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        .py-xxl-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        .py-xxl-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
        .py-xxl-5 {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }
        .pt-xxl-0 {
            padding-top: 0 !important;
        }
        .pt-xxl-1 {
            padding-top: 0.25rem !important;
        }
        .pt-xxl-2 {
            padding-top: 0.5rem !important;
        }
        .pt-xxl-3 {
            padding-top: 1rem !important;
        }
        .pt-xxl-4 {
            padding-top: 1.5rem !important;
        }
        .pt-xxl-5 {
            padding-top: 3rem !important;
        }
        .pe-xxl-0 {
            padding-right: 0 !important;
        }
        .pe-xxl-1 {
            padding-right: 0.25rem !important;
        }
        .pe-xxl-2 {
            padding-right: 0.5rem !important;
        }
        .pe-xxl-3 {
            padding-right: 1rem !important;
        }
        .pe-xxl-4 {
            padding-right: 1.5rem !important;
        }
        .pe-xxl-5 {
            padding-right: 3rem !important;
        }
        .pb-xxl-0 {
            padding-bottom: 0 !important;
        }
        .pb-xxl-1 {
            padding-bottom: 0.25rem !important;
        }
        .pb-xxl-2 {
            padding-bottom: 0.5rem !important;
        }
        .pb-xxl-3 {
            padding-bottom: 1rem !important;
        }
        .pb-xxl-4 {
            padding-bottom: 1.5rem !important;
        }
        .pb-xxl-5 {
            padding-bottom: 3rem !important;
        }
        .ps-xxl-0 {
            padding-left: 0 !important;
        }
        .ps-xxl-1 {
            padding-left: 0.25rem !important;
        }
        .ps-xxl-2 {
            padding-left: 0.5rem !important;
        }
        .ps-xxl-3 {
            padding-left: 1rem !important;
        }
        .ps-xxl-4 {
            padding-left: 1.5rem !important;
        }
        .ps-xxl-5 {
            padding-left: 3rem !important;
        }
        .gap-xxl-0 {
            gap: 0 !important;
        }
        .gap-xxl-1 {
            gap: 0.25rem !important;
        }
        .gap-xxl-2 {
            gap: 0.5rem !important;
        }
        .gap-xxl-3 {
            gap: 1rem !important;
        }
        .gap-xxl-4 {
            gap: 1.5rem !important;
        }
        .gap-xxl-5 {
            gap: 3rem !important;
        }
        .row-gap-xxl-0 {
            row-gap: 0 !important;
        }
        .row-gap-xxl-1 {
            row-gap: 0.25rem !important;
        }
        .row-gap-xxl-2 {
            row-gap: 0.5rem !important;
        }
        .row-gap-xxl-3 {
            row-gap: 1rem !important;
        }
        .row-gap-xxl-4 {
            row-gap: 1.5rem !important;
        }
        .row-gap-xxl-5 {
            row-gap: 3rem !important;
        }
        .column-gap-xxl-0 {
            -moz-column-gap: 0 !important;
            column-gap: 0 !important;
        }
        .column-gap-xxl-1 {
            -moz-column-gap: 0.25rem !important;
            column-gap: 0.25rem !important;
        }
        .column-gap-xxl-2 {
            -moz-column-gap: 0.5rem !important;
            column-gap: 0.5rem !important;
        }
        .column-gap-xxl-3 {
            -moz-column-gap: 1rem !important;
            column-gap: 1rem !important;
        }
        .column-gap-xxl-4 {
            -moz-column-gap: 1.5rem !important;
            column-gap: 1.5rem !important;
        }
        .column-gap-xxl-5 {
            -moz-column-gap: 3rem !important;
            column-gap: 3rem !important;
        }
        .text-xxl-start {
            text-align: left !important;
        }
        .text-xxl-end {
            text-align: right !important;
        }
        .text-xxl-center {
            text-align: center !important;
        }
    }
    @media (min-width: 1200px) {
        .fs-1 {
            font-size: 2.5rem !important;
        }
        .fs-2 {
            font-size: 2rem !important;
        }
        .fs-3 {
            font-size: 1.75rem !important;
        }
        .fs-4 {
            font-size: 1.5rem !important;
        }
    }
    @media print {
        .d-print-inline {
            display: inline !important;
        }
        .d-print-inline-block {
            display: inline-block !important;
        }
        .d-print-block {
            display: block !important;
        }
        .d-print-grid {
            display: grid !important;
        }
        .d-print-inline-grid {
            display: inline-grid !important;
        }
        .d-print-table {
            display: table !important;
        }
        .d-print-table-row {
            display: table-row !important;
        }
        .d-print-table-cell {
            display: table-cell !important;
        }
        .d-print-flex {
            display: flex !important;
        }
        .d-print-inline-flex {
            display: inline-flex !important;
        }
        .d-print-none {
            display: none !important;
        }
    }


      .additional_sign_field {
      width: 100%;
      display: flex;
    }

    .additional_cust_name {
      width: 34%;
      padding-left: 5%;
      padding-top: 5%;
      font-size: 20px;
      font-weight: bold;
      border: 1px solid;
    }

    .additional_cust_sign {
      width: 76%;
      border: 1px solid;
    }

    #sig-canvas {
      border: 2px solid black;
      border-radius: 15px;
      cursor: crosshair;
      /* margin-left: 10%; */
    }

    #sig-clearBtn,
    #sig-submitBtn-owner,
    #sig-submitBtn-customer,
    #ztsa_agrmnt_reject,
    #ztsa_agrmnt_accept {
      margin-top: 10px;
      margin-right: 30px;
      margin-bottom: 10px;
    }

    /*     #sig-clearBtn {
      margin-left: 10%;
    } */

    /*     #sig-clearBtn1 {
      margin-top: 30px;
      margin-right: 25%;
    } */

    img[src=""],
    img:not([src]) {
      opacity: 0;
    }

    img[src] {
      display: block;
      width: 300px;
      height: 100px;
    }

    body {
      width: fit-content;
      padding-left: 5%;
      padding-right: 5%;
      background: #555;
      margin: 0 auto;
    }

    .agreement_front_page {
      width: fit-content;
      border: 2px solid black;
      padding: 15px 60px 15px 70px;
      background: white;
      margin-top: 5%;
      font-size: 18px;
    }

    .signature_pad {
      background-color: white;
    }

    /*     .message_pad {
      margin-left: 9%;
    } */

    /*     #sig-submitBtn-customer {
      float: right;
      margin-right: 21%;
      margin-top: -47px;
    } */

    #ztsa_customer_comment {
      width: 630px;
      height: 140px;
    }

    .line_brake {
      height: 1px;
      background: black;
      margin-bottom: 2%;
    }

    /*     .sign_here {
      margin-left: 10%;
    } */

    /*     #sig-submitBtn-owner {
      float: right;
      margin-top: -29px;
      margin-right: 4%;
    } */

    #agrmnt_logo {
      height: 50px !important;
      width: 100px !important;
    }

    #ztsa_header {
      display: flex;
    }

    #header_text {
      width: 92%;
      margin: auto 0;
      font-size: 20px;
    }

    #header_logo {
      width: 8%;
    }

    #header_logo img {
      width: 100%;
      height: 45px;
    }
  </style>
</head>

<body>
  <div class="agreement_front_page" style="border: 2px solid black;">
    <div id="ztsa_agreement_render">
      <div id='ztsa_header'>
        <?php
        if ($logo == "") { ?>
          <div id='header_text' style='width:100%'><?php echo wp_kses_post($agreement['header']); ?></div>
        <?php } else {
          if ($logo_alignment == 'right') { ?>
            <div id='header_text'><?php echo wp_kses_post($agreement['header']); ?></div><div id='header_logo'><img src='<?php echo esc_url($logo); ?>' alt='LOGO' height='45'></div>
          <?php } else { ?>
            <div id='header_logo'><img src='<?php echo  esc_url($logo); ?>' alt='LOGO'></div><div id='header_text'><?php echo wp_kses_post($agreement['header']); ?></div>
          <?php }
        }
        ?>
      </div>

      <hr>
	  <?php printf("<div id='ztsa_body'>%s</div>",$agreement['body'] ); ?>
      
      <hr>
      <div id='ztsa_footer'><?php echo wp_kses_post($agreement['footer']); ?></div>

    </div>

    <?php
    if (empty($customer_sign)) { ?> 
         <hr class="line_brake">
        <div class="signature_pad">
        <div><label class="sign_here"><?php  esc_html_e("Please Sign Here!", "smart-agreements"); ?></label></div>
        <div> <canvas id="sig-canvas" width="620" height="160"></canvas><div>
        <div>
          <button class="btn btn-danger" id="sig-clearBtn"><?php esc_html_e("Clear Signature", "smart-agreements"); ?></button>
          <input type ="button" class="btn btn-success" id="sig-submitBtn-customer"  value="Submit Signature customer">
        </div>
    <?php } elseif ($additional_entry_id > 0) {
      if (empty($additional_user_sign)) { ?> 
          <hr class="line_brake">
          <div class="signature_pad">
          <div><label class="sign_here"><?php esc_html_e("Please Sign Here!", "smart-agreements"); ?></label></div>
          <div><canvas id="sig-canvas" width="620" height="160"></canvas></div>
          <div>
          <button class="btn btn-danger" id="sig-clearBtn"><?php esc_html_e("Clear Signature", "smart-agreements"); ?></button> 
          <input type ="button" class="btn btn-success" id="sig-submitBtn-customer"  value="Submit Signature customer">
          </div>
      <?php }
    }
    ?>


    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
      <?php wp_nonce_field('customer_response', 'customer_response'); ?>
      <input type="hidden" name="action" value="customer_response">
      <input type="hidden" name="additional_customer_id" value="<?php echo esc_attr($additional_entry_id) ?>">
      <input type="hidden" name="ztsa_customer_response_id" id="ztsa_cstomer_response_id" value="<?php echo esc_attr($customer_id) ?>">
      <input type="hidden" name="signature-customer" id="signature-customer">
      <?php
      if (isset($customer_signeture_button)) {
        if (empty($customer_sign)) { ?>
          <div class='message_pad'><?php echo wp_kses_post($customer_signeture_button); ?></div>
        <?php } elseif ($additional_entry_id > 0) {
          if (empty($additional_user_sign)) { ?>
            <div class='message_pad'><?php echo  wp_kses_post($customer_signeture_button); ?></div>
          <?php }
        }
      }
      ?>
    </form>
    <?php
    if (isset($owner_signeture_button)) { ?> 
      <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
      <?php echo wp_nonce_field('ztsa_owner_signeture', 'ztsa_owner_signeture'); ?>
      <input type="hidden" name="action" value="ztsa_owner_signeture">
      <input type="hidden" name="ztsa_owner_signeture_id" id="ztsa_owner_signeture_id" value="<?php echo esc_attr($customer_id); ?> ">
      <input type="hidden" name="signature-owner" id="signature-owner">
          <hr class="line_brake">
          <div class="signature_pad">
          <div><label class="sign_here"><?php  esc_html_e("Please Sign Here!", "smart-agreements"); ?></label></div>
          <div><canvas id="sig-canvas" width="620" height="160"></canvas></div>
          <div>
          <button class="btn btn-danger" id="sig-clearBtn1"><?php esc_html_e("Clear Signature", "smart-agreements"); ?></button> 
          <button class="btn btn-success" id="sig-submitBtn-owner"  name="sig-submitBtn-owner" ><?php esc_html_e("Submit Signature owner", "smart-agreements"); ?></button>
          </div></form>
    <?php }
    ?>
  </div>

  <script>
    !(function (e, t) {
        "use strict";
        "object" == typeof module && "object" == typeof module.exports
            ? (module.exports = e.document
                  ? t(e, !0)
                  : function (e) {
                        if (!e.document) throw new Error("jQuery requires a window with a document");
                        return t(e);
                    })
            : t(e);
    })("undefined" != typeof window ? window : this, function (C, e) {
        "use strict";
        var t = [],
            r = Object.getPrototypeOf,
            s = t.slice,
            g = t.flat
                ? function (e) {
                      return t.flat.call(e);
                  }
                : function (e) {
                      return t.concat.apply([], e);
                  },
            u = t.push,
            i = t.indexOf,
            n = {},
            o = n.toString,
            y = n.hasOwnProperty,
            a = y.toString,
            l = a.call(Object),
            v = {},
            m = function (e) {
                return "function" == typeof e && "number" != typeof e.nodeType && "function" != typeof e.item;
            },
            x = function (e) {
                return null != e && e === e.window;
            },
            E = C.document,
            c = { type: !0, src: !0, nonce: !0, noModule: !0 };
        function b(e, t, n) {
            var r,
                i,
                o = (n = n || E).createElement("script");
            if (((o.text = e), t)) for (r in c) (i = t[r] || (t.getAttribute && t.getAttribute(r))) && o.setAttribute(r, i);
            n.head.appendChild(o).parentNode.removeChild(o);
        }
        function w(e) {
            return null == e ? e + "" : "object" == typeof e || "function" == typeof e ? n[o.call(e)] || "object" : typeof e;
        }
        var f = "3.6.4",
            S = function (e, t) {
                return new S.fn.init(e, t);
            };
        function p(e) {
            var t = !!e && "length" in e && e.length,
                n = w(e);
            return !m(e) && !x(e) && ("array" === n || 0 === t || ("number" == typeof t && 0 < t && t - 1 in e));
        }
        (S.fn = S.prototype = {
            jquery: f,
            constructor: S,
            length: 0,
            toArray: function () {
                return s.call(this);
            },
            get: function (e) {
                return null == e ? s.call(this) : e < 0 ? this[e + this.length] : this[e];
            },
            pushStack: function (e) {
                var t = S.merge(this.constructor(), e);
                return (t.prevObject = this), t;
            },
            each: function (e) {
                return S.each(this, e);
            },
            map: function (n) {
                return this.pushStack(
                    S.map(this, function (e, t) {
                        return n.call(e, t, e);
                    })
                );
            },
            slice: function () {
                return this.pushStack(s.apply(this, arguments));
            },
            first: function () {
                return this.eq(0);
            },
            last: function () {
                return this.eq(-1);
            },
            even: function () {
                return this.pushStack(
                    S.grep(this, function (e, t) {
                        return (t + 1) % 2;
                    })
                );
            },
            odd: function () {
                return this.pushStack(
                    S.grep(this, function (e, t) {
                        return t % 2;
                    })
                );
            },
            eq: function (e) {
                var t = this.length,
                    n = +e + (e < 0 ? t : 0);
                return this.pushStack(0 <= n && n < t ? [this[n]] : []);
            },
            end: function () {
                return this.prevObject || this.constructor();
            },
            push: u,
            sort: t.sort,
            splice: t.splice,
        }),
            (S.extend = S.fn.extend = function () {
                var e,
                    t,
                    n,
                    r,
                    i,
                    o,
                    a = arguments[0] || {},
                    s = 1,
                    u = arguments.length,
                    l = !1;
                for ("boolean" == typeof a && ((l = a), (a = arguments[s] || {}), s++), "object" == typeof a || m(a) || (a = {}), s === u && ((a = this), s--); s < u; s++)
                    if (null != (e = arguments[s]))
                        for (t in e)
                            (r = e[t]),
                                "__proto__" !== t &&
                                    a !== r &&
                                    (l && r && (S.isPlainObject(r) || (i = Array.isArray(r)))
                                        ? ((n = a[t]), (o = i && !Array.isArray(n) ? [] : i || S.isPlainObject(n) ? n : {}), (i = !1), (a[t] = S.extend(l, o, r)))
                                        : void 0 !== r && (a[t] = r));
                return a;
            }),
            S.extend({
                expando: "jQuery" + (f + Math.random()).replace(/\D/g, ""),
                isReady: !0,
                error: function (e) {
                    throw new Error(e);
                },
                noop: function () {},
                isPlainObject: function (e) {
                    var t, n;
                    return !(!e || "[object Object]" !== o.call(e)) && (!(t = r(e)) || ("function" == typeof (n = y.call(t, "constructor") && t.constructor) && a.call(n) === l));
                },
                isEmptyObject: function (e) {
                    var t;
                    for (t in e) return !1;
                    return !0;
                },
                globalEval: function (e, t, n) {
                    b(e, { nonce: t && t.nonce }, n);
                },
                each: function (e, t) {
                    var n,
                        r = 0;
                    if (p(e)) {
                        for (n = e.length; r < n; r++) if (!1 === t.call(e[r], r, e[r])) break;
                    } else for (r in e) if (!1 === t.call(e[r], r, e[r])) break;
                    return e;
                },
                makeArray: function (e, t) {
                    var n = t || [];
                    return null != e && (p(Object(e)) ? S.merge(n, "string" == typeof e ? [e] : e) : u.call(n, e)), n;
                },
                inArray: function (e, t, n) {
                    return null == t ? -1 : i.call(t, e, n);
                },
                merge: function (e, t) {
                    for (var n = +t.length, r = 0, i = e.length; r < n; r++) e[i++] = t[r];
                    return (e.length = i), e;
                },
                grep: function (e, t, n) {
                    for (var r = [], i = 0, o = e.length, a = !n; i < o; i++) !t(e[i], i) !== a && r.push(e[i]);
                    return r;
                },
                map: function (e, t, n) {
                    var r,
                        i,
                        o = 0,
                        a = [];
                    if (p(e)) for (r = e.length; o < r; o++) null != (i = t(e[o], o, n)) && a.push(i);
                    else for (o in e) null != (i = t(e[o], o, n)) && a.push(i);
                    return g(a);
                },
                guid: 1,
                support: v,
            }),
            "function" == typeof Symbol && (S.fn[Symbol.iterator] = t[Symbol.iterator]),
            S.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "), function (e, t) {
                n["[object " + t + "]"] = t.toLowerCase();
            });
        var d = (function (n) {
            var e,
                d,
                b,
                o,
                i,
                h,
                f,
                g,
                w,
                u,
                l,
                T,
                C,
                a,
                E,
                y,
                s,
                c,
                v,
                S = "sizzle" + 1 * new Date(),
                p = n.document,
                k = 0,
                r = 0,
                m = ue(),
                x = ue(),
                A = ue(),
                N = ue(),
                j = function (e, t) {
                    return e === t && (l = !0), 0;
                },
                D = {}.hasOwnProperty,
                t = [],
                q = t.pop,
                L = t.push,
                H = t.push,
                O = t.slice,
                P = function (e, t) {
                    for (var n = 0, r = e.length; n < r; n++) if (e[n] === t) return n;
                    return -1;
                },
                R = "checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",
                M = "[\\x20\\t\\r\\n\\f]",
                I = "(?:\\\\[\\da-fA-F]{1,6}" + M + "?|\\\\[^\\r\\n\\f]|[\\w-]|[^\0-\\x7f])+",
                W = "\\[" + M + "*(" + I + ")(?:" + M + "*([*^$|!~]?=)" + M + "*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|(" + I + "))|)" + M + "*\\]",
                F = ":(" + I + ")(?:\\((('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|((?:\\\\.|[^\\\\()[\\]]|" + W + ")*)|.*)\\)|)",
                $ = new RegExp(M + "+", "g"),
                B = new RegExp("^" + M + "+|((?:^|[^\\\\])(?:\\\\.)*)" + M + "+$", "g"),
                _ = new RegExp("^" + M + "*," + M + "*"),
                z = new RegExp("^" + M + "*([>+~]|" + M + ")" + M + "*"),
                U = new RegExp(M + "|>"),
                X = new RegExp(F),
                V = new RegExp("^" + I + "$"),
                G = {
                    ID: new RegExp("^#(" + I + ")"),
                    CLASS: new RegExp("^\\.(" + I + ")"),
                    TAG: new RegExp("^(" + I + "|[*])"),
                    ATTR: new RegExp("^" + W),
                    PSEUDO: new RegExp("^" + F),
                    CHILD: new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + M + "*(even|odd|(([+-]|)(\\d*)n|)" + M + "*(?:([+-]|)" + M + "*(\\d+)|))" + M + "*\\)|)", "i"),
                    bool: new RegExp("^(?:" + R + ")$", "i"),
                    needsContext: new RegExp("^" + M + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + M + "*((?:-\\d)?\\d*)" + M + "*\\)|)(?=[^-]|$)", "i"),
                },
                Y = /HTML$/i,
                Q = /^(?:input|select|textarea|button)$/i,
                J = /^h\d$/i,
                K = /^[^{]+\{\s*\[native \w/,
                Z = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,
                ee = /[+~]/,
                te = new RegExp("\\\\[\\da-fA-F]{1,6}" + M + "?|\\\\([^\\r\\n\\f])", "g"),
                ne = function (e, t) {
                    var n = "0x" + e.slice(1) - 65536;
                    return t || (n < 0 ? String.fromCharCode(n + 65536) : String.fromCharCode((n >> 10) | 55296, (1023 & n) | 56320));
                },
                re = /([\0-\x1f\x7f]|^-?\d)|^-$|[^\0-\x1f\x7f-\uFFFF\w-]/g,
                ie = function (e, t) {
                    return t ? ("\0" === e ? "\ufffd" : e.slice(0, -1) + "\\" + e.charCodeAt(e.length - 1).toString(16) + " ") : "\\" + e;
                },
                oe = function () {
                    T();
                },
                ae = be(
                    function (e) {
                        return !0 === e.disabled && "fieldset" === e.nodeName.toLowerCase();
                    },
                    { dir: "parentNode", next: "legend" }
                );
            try {
                H.apply((t = O.call(p.childNodes)), p.childNodes), t[p.childNodes.length].nodeType;
            } catch (e) {
                H = {
                    apply: t.length
                        ? function (e, t) {
                              L.apply(e, O.call(t));
                          }
                        : function (e, t) {
                              var n = e.length,
                                  r = 0;
                              while ((e[n++] = t[r++]));
                              e.length = n - 1;
                          },
                };
            }
            function se(t, e, n, r) {
                var i,
                    o,
                    a,
                    s,
                    u,
                    l,
                    c,
                    f = e && e.ownerDocument,
                    p = e ? e.nodeType : 9;
                if (((n = n || []), "string" != typeof t || !t || (1 !== p && 9 !== p && 11 !== p))) return n;
                if (!r && (T(e), (e = e || C), E)) {
                    if (11 !== p && (u = Z.exec(t)))
                        if ((i = u[1])) {
                            if (9 === p) {
                                if (!(a = e.getElementById(i))) return n;
                                if (a.id === i) return n.push(a), n;
                            } else if (f && (a = f.getElementById(i)) && v(e, a) && a.id === i) return n.push(a), n;
                        } else {
                            if (u[2]) return H.apply(n, e.getElementsByTagName(t)), n;
                            if ((i = u[3]) && d.getElementsByClassName && e.getElementsByClassName) return H.apply(n, e.getElementsByClassName(i)), n;
                        }
                    if (d.qsa && !N[t + " "] && (!y || !y.test(t)) && (1 !== p || "object" !== e.nodeName.toLowerCase())) {
                        if (((c = t), (f = e), 1 === p && (U.test(t) || z.test(t)))) {
                            ((f = (ee.test(t) && ve(e.parentNode)) || e) === e && d.scope) || ((s = e.getAttribute("id")) ? (s = s.replace(re, ie)) : e.setAttribute("id", (s = S))), (o = (l = h(t)).length);
                            while (o--) l[o] = (s ? "#" + s : ":scope") + " " + xe(l[o]);
                            c = l.join(",");
                        }
                        try {
                            return H.apply(n, f.querySelectorAll(c)), n;
                        } catch (e) {
                            N(t, !0);
                        } finally {
                            s === S && e.removeAttribute("id");
                        }
                    }
                }
                return g(t.replace(B, "$1"), e, n, r);
            }
            function ue() {
                var r = [];
                return function e(t, n) {
                    return r.push(t + " ") > b.cacheLength && delete e[r.shift()], (e[t + " "] = n);
                };
            }
            function le(e) {
                return (e[S] = !0), e;
            }
            function ce(e) {
                var t = C.createElement("fieldset");
                try {
                    return !!e(t);
                } catch (e) {
                    return !1;
                } finally {
                    t.parentNode && t.parentNode.removeChild(t), (t = null);
                }
            }
            function fe(e, t) {
                var n = e.split("|"),
                    r = n.length;
                while (r--) b.attrHandle[n[r]] = t;
            }
            function pe(e, t) {
                var n = t && e,
                    r = n && 1 === e.nodeType && 1 === t.nodeType && e.sourceIndex - t.sourceIndex;
                if (r) return r;
                if (n) while ((n = n.nextSibling)) if (n === t) return -1;
                return e ? 1 : -1;
            }
            function de(t) {
                return function (e) {
                    return "input" === e.nodeName.toLowerCase() && e.type === t;
                };
            }
            function he(n) {
                return function (e) {
                    var t = e.nodeName.toLowerCase();
                    return ("input" === t || "button" === t) && e.type === n;
                };
            }
            function ge(t) {
                return function (e) {
                    return "form" in e
                        ? e.parentNode && !1 === e.disabled
                            ? "label" in e
                                ? "label" in e.parentNode
                                    ? e.parentNode.disabled === t
                                    : e.disabled === t
                                : e.isDisabled === t || (e.isDisabled !== !t && ae(e) === t)
                            : e.disabled === t
                        : "label" in e && e.disabled === t;
                };
            }
            function ye(a) {
                return le(function (o) {
                    return (
                        (o = +o),
                        le(function (e, t) {
                            var n,
                                r = a([], e.length, o),
                                i = r.length;
                            while (i--) e[(n = r[i])] && (e[n] = !(t[n] = e[n]));
                        })
                    );
                });
            }
            function ve(e) {
                return e && "undefined" != typeof e.getElementsByTagName && e;
            }
            for (e in ((d = se.support = {}),
            (i = se.isXML = function (e) {
                var t = e && e.namespaceURI,
                    n = e && (e.ownerDocument || e).documentElement;
                return !Y.test(t || (n && n.nodeName) || "HTML");
            }),
            (T = se.setDocument = function (e) {
                var t,
                    n,
                    r = e ? e.ownerDocument || e : p;
                return (
                    r != C &&
                        9 === r.nodeType &&
                        r.documentElement &&
                        ((a = (C = r).documentElement),
                        (E = !i(C)),
                        p != C && (n = C.defaultView) && n.top !== n && (n.addEventListener ? n.addEventListener("unload", oe, !1) : n.attachEvent && n.attachEvent("onunload", oe)),
                        (d.scope = ce(function (e) {
                            return a.appendChild(e).appendChild(C.createElement("div")), "undefined" != typeof e.querySelectorAll && !e.querySelectorAll(":scope fieldset div").length;
                        })),
                        (d.cssHas = ce(function () {
                            try {
                                return C.querySelector(":has(*,:jqfake)"), !1;
                            } catch (e) {
                                return !0;
                            }
                        })),
                        (d.attributes = ce(function (e) {
                            return (e.className = "i"), !e.getAttribute("className");
                        })),
                        (d.getElementsByTagName = ce(function (e) {
                            return e.appendChild(C.createComment("")), !e.getElementsByTagName("*").length;
                        })),
                        (d.getElementsByClassName = K.test(C.getElementsByClassName)),
                        (d.getById = ce(function (e) {
                            return (a.appendChild(e).id = S), !C.getElementsByName || !C.getElementsByName(S).length;
                        })),
                        d.getById
                            ? ((b.filter.ID = function (e) {
                                  var t = e.replace(te, ne);
                                  return function (e) {
                                      return e.getAttribute("id") === t;
                                  };
                              }),
                              (b.find.ID = function (e, t) {
                                  if ("undefined" != typeof t.getElementById && E) {
                                      var n = t.getElementById(e);
                                      return n ? [n] : [];
                                  }
                              }))
                            : ((b.filter.ID = function (e) {
                                  var n = e.replace(te, ne);
                                  return function (e) {
                                      var t = "undefined" != typeof e.getAttributeNode && e.getAttributeNode("id");
                                      return t && t.value === n;
                                  };
                              }),
                              (b.find.ID = function (e, t) {
                                  if ("undefined" != typeof t.getElementById && E) {
                                      var n,
                                          r,
                                          i,
                                          o = t.getElementById(e);
                                      if (o) {
                                          if ((n = o.getAttributeNode("id")) && n.value === e) return [o];
                                          (i = t.getElementsByName(e)), (r = 0);
                                          while ((o = i[r++])) if ((n = o.getAttributeNode("id")) && n.value === e) return [o];
                                      }
                                      return [];
                                  }
                              })),
                        (b.find.TAG = d.getElementsByTagName
                            ? function (e, t) {
                                  return "undefined" != typeof t.getElementsByTagName ? t.getElementsByTagName(e) : d.qsa ? t.querySelectorAll(e) : void 0;
                              }
                            : function (e, t) {
                                  var n,
                                      r = [],
                                      i = 0,
                                      o = t.getElementsByTagName(e);
                                  if ("*" === e) {
                                      while ((n = o[i++])) 1 === n.nodeType && r.push(n);
                                      return r;
                                  }
                                  return o;
                              }),
                        (b.find.CLASS =
                            d.getElementsByClassName &&
                            function (e, t) {
                                if ("undefined" != typeof t.getElementsByClassName && E) return t.getElementsByClassName(e);
                            }),
                        (s = []),
                        (y = []),
                        (d.qsa = K.test(C.querySelectorAll)) &&
                            (ce(function (e) {
                                var t;
                                (a.appendChild(e).innerHTML = "<a id='" + S + "'></a><select id='" + S + "-\r\\' msallowcapture=''><option selected=''></option></select>"),
                                    e.querySelectorAll("[msallowcapture^='']").length && y.push("[*^$]=" + M + "*(?:''|\"\")"),
                                    e.querySelectorAll("[selected]").length || y.push("\\[" + M + "*(?:value|" + R + ")"),
                                    e.querySelectorAll("[id~=" + S + "-]").length || y.push("~="),
                                    (t = C.createElement("input")).setAttribute("name", ""),
                                    e.appendChild(t),
                                    e.querySelectorAll("[name='']").length || y.push("\\[" + M + "*name" + M + "*=" + M + "*(?:''|\"\")"),
                                    e.querySelectorAll(":checked").length || y.push(":checked"),
                                    e.querySelectorAll("a#" + S + "+*").length || y.push(".#.+[+~]"),
                                    e.querySelectorAll("\\\f"),
                                    y.push("[\\r\\n\\f]");
                            }),
                            ce(function (e) {
                                e.innerHTML = "<a href='' disabled='disabled'></a><select disabled='disabled'><option/></select>";
                                var t = C.createElement("input");
                                t.setAttribute("type", "hidden"),
                                    e.appendChild(t).setAttribute("name", "D"),
                                    e.querySelectorAll("[name=d]").length && y.push("name" + M + "*[*^$|!~]?="),
                                    2 !== e.querySelectorAll(":enabled").length && y.push(":enabled", ":disabled"),
                                    (a.appendChild(e).disabled = !0),
                                    2 !== e.querySelectorAll(":disabled").length && y.push(":enabled", ":disabled"),
                                    e.querySelectorAll("*,:x"),
                                    y.push(",.*:");
                            })),
                        (d.matchesSelector = K.test((c = a.matches || a.webkitMatchesSelector || a.mozMatchesSelector || a.oMatchesSelector || a.msMatchesSelector))) &&
                            ce(function (e) {
                                (d.disconnectedMatch = c.call(e, "*")), c.call(e, "[s!='']:x"), s.push("!=", F);
                            }),
                        d.cssHas || y.push(":has"),
                        (y = y.length && new RegExp(y.join("|"))),
                        (s = s.length && new RegExp(s.join("|"))),
                        (t = K.test(a.compareDocumentPosition)),
                        (v =
                            t || K.test(a.contains)
                                ? function (e, t) {
                                      var n = (9 === e.nodeType && e.documentElement) || e,
                                          r = t && t.parentNode;
                                      return e === r || !(!r || 1 !== r.nodeType || !(n.contains ? n.contains(r) : e.compareDocumentPosition && 16 & e.compareDocumentPosition(r)));
                                  }
                                : function (e, t) {
                                      if (t) while ((t = t.parentNode)) if (t === e) return !0;
                                      return !1;
                                  }),
                        (j = t
                            ? function (e, t) {
                                  if (e === t) return (l = !0), 0;
                                  var n = !e.compareDocumentPosition - !t.compareDocumentPosition;
                                  return (
                                      n ||
                                      (1 & (n = (e.ownerDocument || e) == (t.ownerDocument || t) ? e.compareDocumentPosition(t) : 1) || (!d.sortDetached && t.compareDocumentPosition(e) === n)
                                          ? e == C || (e.ownerDocument == p && v(p, e))
                                              ? -1
                                              : t == C || (t.ownerDocument == p && v(p, t))
                                              ? 1
                                              : u
                                              ? P(u, e) - P(u, t)
                                              : 0
                                          : 4 & n
                                          ? -1
                                          : 1)
                                  );
                              }
                            : function (e, t) {
                                  if (e === t) return (l = !0), 0;
                                  var n,
                                      r = 0,
                                      i = e.parentNode,
                                      o = t.parentNode,
                                      a = [e],
                                      s = [t];
                                  if (!i || !o) return e == C ? -1 : t == C ? 1 : i ? -1 : o ? 1 : u ? P(u, e) - P(u, t) : 0;
                                  if (i === o) return pe(e, t);
                                  n = e;
                                  while ((n = n.parentNode)) a.unshift(n);
                                  n = t;
                                  while ((n = n.parentNode)) s.unshift(n);
                                  while (a[r] === s[r]) r++;
                                  return r ? pe(a[r], s[r]) : a[r] == p ? -1 : s[r] == p ? 1 : 0;
                              })),
                    C
                );
            }),
            (se.matches = function (e, t) {
                return se(e, null, null, t);
            }),
            (se.matchesSelector = function (e, t) {
                if ((T(e), d.matchesSelector && E && !N[t + " "] && (!s || !s.test(t)) && (!y || !y.test(t))))
                    try {
                        var n = c.call(e, t);
                        if (n || d.disconnectedMatch || (e.document && 11 !== e.document.nodeType)) return n;
                    } catch (e) {
                        N(t, !0);
                    }
                return 0 < se(t, C, null, [e]).length;
            }),
            (se.contains = function (e, t) {
                return (e.ownerDocument || e) != C && T(e), v(e, t);
            }),
            (se.attr = function (e, t) {
                (e.ownerDocument || e) != C && T(e);
                var n = b.attrHandle[t.toLowerCase()],
                    r = n && D.call(b.attrHandle, t.toLowerCase()) ? n(e, t, !E) : void 0;
                return void 0 !== r ? r : d.attributes || !E ? e.getAttribute(t) : (r = e.getAttributeNode(t)) && r.specified ? r.value : null;
            }),
            (se.escape = function (e) {
                return (e + "").replace(re, ie);
            }),
            (se.error = function (e) {
                throw new Error("Syntax error, unrecognized expression: " + e);
            }),
            (se.uniqueSort = function (e) {
                var t,
                    n = [],
                    r = 0,
                    i = 0;
                if (((l = !d.detectDuplicates), (u = !d.sortStable && e.slice(0)), e.sort(j), l)) {
                    while ((t = e[i++])) t === e[i] && (r = n.push(i));
                    while (r--) e.splice(n[r], 1);
                }
                return (u = null), e;
            }),
            (o = se.getText = function (e) {
                var t,
                    n = "",
                    r = 0,
                    i = e.nodeType;
                if (i) {
                    if (1 === i || 9 === i || 11 === i) {
                        if ("string" == typeof e.textContent) return e.textContent;
                        for (e = e.firstChild; e; e = e.nextSibling) n += o(e);
                    } else if (3 === i || 4 === i) return e.nodeValue;
                } else while ((t = e[r++])) n += o(t);
                return n;
            }),
            ((b = se.selectors = {
                cacheLength: 50,
                createPseudo: le,
                match: G,
                attrHandle: {},
                find: {},
                relative: { ">": { dir: "parentNode", first: !0 }, " ": { dir: "parentNode" }, "+": { dir: "previousSibling", first: !0 }, "~": { dir: "previousSibling" } },
                preFilter: {
                    ATTR: function (e) {
                        return (e[1] = e[1].replace(te, ne)), (e[3] = (e[3] || e[4] || e[5] || "").replace(te, ne)), "~=" === e[2] && (e[3] = " " + e[3] + " "), e.slice(0, 4);
                    },
                    CHILD: function (e) {
                        return (
                            (e[1] = e[1].toLowerCase()),
                            "nth" === e[1].slice(0, 3) ? (e[3] || se.error(e[0]), (e[4] = +(e[4] ? e[5] + (e[6] || 1) : 2 * ("even" === e[3] || "odd" === e[3]))), (e[5] = +(e[7] + e[8] || "odd" === e[3]))) : e[3] && se.error(e[0]),
                            e
                        );
                    },
                    PSEUDO: function (e) {
                        var t,
                            n = !e[6] && e[2];
                        return G.CHILD.test(e[0])
                            ? null
                            : (e[3] ? (e[2] = e[4] || e[5] || "") : n && X.test(n) && (t = h(n, !0)) && (t = n.indexOf(")", n.length - t) - n.length) && ((e[0] = e[0].slice(0, t)), (e[2] = n.slice(0, t))), e.slice(0, 3));
                    },
                },
                filter: {
                    TAG: function (e) {
                        var t = e.replace(te, ne).toLowerCase();
                        return "*" === e
                            ? function () {
                                  return !0;
                              }
                            : function (e) {
                                  return e.nodeName && e.nodeName.toLowerCase() === t;
                              };
                    },
                    CLASS: function (e) {
                        var t = m[e + " "];
                        return (
                            t ||
                            ((t = new RegExp("(^|" + M + ")" + e + "(" + M + "|$)")) &&
                                m(e, function (e) {
                                    return t.test(("string" == typeof e.className && e.className) || ("undefined" != typeof e.getAttribute && e.getAttribute("class")) || "");
                                }))
                        );
                    },
                    ATTR: function (n, r, i) {
                        return function (e) {
                            var t = se.attr(e, n);
                            return null == t
                                ? "!=" === r
                                : !r ||
                                      ((t += ""),
                                      "=" === r
                                          ? t === i
                                          : "!=" === r
                                          ? t !== i
                                          : "^=" === r
                                          ? i && 0 === t.indexOf(i)
                                          : "*=" === r
                                          ? i && -1 < t.indexOf(i)
                                          : "$=" === r
                                          ? i && t.slice(-i.length) === i
                                          : "~=" === r
                                          ? -1 < (" " + t.replace($, " ") + " ").indexOf(i)
                                          : "|=" === r && (t === i || t.slice(0, i.length + 1) === i + "-"));
                        };
                    },
                    CHILD: function (h, e, t, g, y) {
                        var v = "nth" !== h.slice(0, 3),
                            m = "last" !== h.slice(-4),
                            x = "of-type" === e;
                        return 1 === g && 0 === y
                            ? function (e) {
                                  return !!e.parentNode;
                              }
                            : function (e, t, n) {
                                  var r,
                                      i,
                                      o,
                                      a,
                                      s,
                                      u,
                                      l = v !== m ? "nextSibling" : "previousSibling",
                                      c = e.parentNode,
                                      f = x && e.nodeName.toLowerCase(),
                                      p = !n && !x,
                                      d = !1;
                                  if (c) {
                                      if (v) {
                                          while (l) {
                                              a = e;
                                              while ((a = a[l])) if (x ? a.nodeName.toLowerCase() === f : 1 === a.nodeType) return !1;
                                              u = l = "only" === h && !u && "nextSibling";
                                          }
                                          return !0;
                                      }
                                      if (((u = [m ? c.firstChild : c.lastChild]), m && p)) {
                                          (d = (s = (r = (i = (o = (a = c)[S] || (a[S] = {}))[a.uniqueID] || (o[a.uniqueID] = {}))[h] || [])[0] === k && r[1]) && r[2]), (a = s && c.childNodes[s]);
                                          while ((a = (++s && a && a[l]) || (d = s = 0) || u.pop()))
                                              if (1 === a.nodeType && ++d && a === e) {
                                                  i[h] = [k, s, d];
                                                  break;
                                              }
                                      } else if ((p && (d = s = (r = (i = (o = (a = e)[S] || (a[S] = {}))[a.uniqueID] || (o[a.uniqueID] = {}))[h] || [])[0] === k && r[1]), !1 === d))
                                          while ((a = (++s && a && a[l]) || (d = s = 0) || u.pop()))
                                              if ((x ? a.nodeName.toLowerCase() === f : 1 === a.nodeType) && ++d && (p && ((i = (o = a[S] || (a[S] = {}))[a.uniqueID] || (o[a.uniqueID] = {}))[h] = [k, d]), a === e)) break;
                                      return (d -= y) === g || (d % g == 0 && 0 <= d / g);
                                  }
                              };
                    },
                    PSEUDO: function (e, o) {
                        var t,
                            a = b.pseudos[e] || b.setFilters[e.toLowerCase()] || se.error("unsupported pseudo: " + e);
                        return a[S]
                            ? a(o)
                            : 1 < a.length
                            ? ((t = [e, e, "", o]),
                              b.setFilters.hasOwnProperty(e.toLowerCase())
                                  ? le(function (e, t) {
                                        var n,
                                            r = a(e, o),
                                            i = r.length;
                                        while (i--) e[(n = P(e, r[i]))] = !(t[n] = r[i]);
                                    })
                                  : function (e) {
                                        return a(e, 0, t);
                                    })
                            : a;
                    },
                },
                pseudos: {
                    not: le(function (e) {
                        var r = [],
                            i = [],
                            s = f(e.replace(B, "$1"));
                        return s[S]
                            ? le(function (e, t, n, r) {
                                  var i,
                                      o = s(e, null, r, []),
                                      a = e.length;
                                  while (a--) (i = o[a]) && (e[a] = !(t[a] = i));
                              })
                            : function (e, t, n) {
                                  return (r[0] = e), s(r, null, n, i), (r[0] = null), !i.pop();
                              };
                    }),
                    has: le(function (t) {
                        return function (e) {
                            return 0 < se(t, e).length;
                        };
                    }),
                    contains: le(function (t) {
                        return (
                            (t = t.replace(te, ne)),
                            function (e) {
                                return -1 < (e.textContent || o(e)).indexOf(t);
                            }
                        );
                    }),
                    lang: le(function (n) {
                        return (
                            V.test(n || "") || se.error("unsupported lang: " + n),
                            (n = n.replace(te, ne).toLowerCase()),
                            function (e) {
                                var t;
                                do {
                                    if ((t = E ? e.lang : e.getAttribute("xml:lang") || e.getAttribute("lang"))) return (t = t.toLowerCase()) === n || 0 === t.indexOf(n + "-");
                                } while ((e = e.parentNode) && 1 === e.nodeType);
                                return !1;
                            }
                        );
                    }),
                    target: function (e) {
                        var t = n.location && n.location.hash;
                        return t && t.slice(1) === e.id;
                    },
                    root: function (e) {
                        return e === a;
                    },
                    focus: function (e) {
                        return e === C.activeElement && (!C.hasFocus || C.hasFocus()) && !!(e.type || e.href || ~e.tabIndex);
                    },
                    enabled: ge(!1),
                    disabled: ge(!0),
                    checked: function (e) {
                        var t = e.nodeName.toLowerCase();
                        return ("input" === t && !!e.checked) || ("option" === t && !!e.selected);
                    },
                    selected: function (e) {
                        return e.parentNode && e.parentNode.selectedIndex, !0 === e.selected;
                    },
                    empty: function (e) {
                        for (e = e.firstChild; e; e = e.nextSibling) if (e.nodeType < 6) return !1;
                        return !0;
                    },
                    parent: function (e) {
                        return !b.pseudos.empty(e);
                    },
                    header: function (e) {
                        return J.test(e.nodeName);
                    },
                    input: function (e) {
                        return Q.test(e.nodeName);
                    },
                    button: function (e) {
                        var t = e.nodeName.toLowerCase();
                        return ("input" === t && "button" === e.type) || "button" === t;
                    },
                    text: function (e) {
                        var t;
                        return "input" === e.nodeName.toLowerCase() && "text" === e.type && (null == (t = e.getAttribute("type")) || "text" === t.toLowerCase());
                    },
                    first: ye(function () {
                        return [0];
                    }),
                    last: ye(function (e, t) {
                        return [t - 1];
                    }),
                    eq: ye(function (e, t, n) {
                        return [n < 0 ? n + t : n];
                    }),
                    even: ye(function (e, t) {
                        for (var n = 0; n < t; n += 2) e.push(n);
                        return e;
                    }),
                    odd: ye(function (e, t) {
                        for (var n = 1; n < t; n += 2) e.push(n);
                        return e;
                    }),
                    lt: ye(function (e, t, n) {
                        for (var r = n < 0 ? n + t : t < n ? t : n; 0 <= --r; ) e.push(r);
                        return e;
                    }),
                    gt: ye(function (e, t, n) {
                        for (var r = n < 0 ? n + t : n; ++r < t; ) e.push(r);
                        return e;
                    }),
                },
            }).pseudos.nth = b.pseudos.eq),
            { radio: !0, checkbox: !0, file: !0, password: !0, image: !0 }))
                b.pseudos[e] = de(e);
            for (e in { submit: !0, reset: !0 }) b.pseudos[e] = he(e);
            function me() {}
            function xe(e) {
                for (var t = 0, n = e.length, r = ""; t < n; t++) r += e[t].value;
                return r;
            }
            function be(s, e, t) {
                var u = e.dir,
                    l = e.next,
                    c = l || u,
                    f = t && "parentNode" === c,
                    p = r++;
                return e.first
                    ? function (e, t, n) {
                          while ((e = e[u])) if (1 === e.nodeType || f) return s(e, t, n);
                          return !1;
                      }
                    : function (e, t, n) {
                          var r,
                              i,
                              o,
                              a = [k, p];
                          if (n) {
                              while ((e = e[u])) if ((1 === e.nodeType || f) && s(e, t, n)) return !0;
                          } else
                              while ((e = e[u]))
                                  if (1 === e.nodeType || f)
                                      if (((i = (o = e[S] || (e[S] = {}))[e.uniqueID] || (o[e.uniqueID] = {})), l && l === e.nodeName.toLowerCase())) e = e[u] || e;
                                      else {
                                          if ((r = i[c]) && r[0] === k && r[1] === p) return (a[2] = r[2]);
                                          if (((i[c] = a)[2] = s(e, t, n))) return !0;
                                      }
                          return !1;
                      };
            }
            function we(i) {
                return 1 < i.length
                    ? function (e, t, n) {
                          var r = i.length;
                          while (r--) if (!i[r](e, t, n)) return !1;
                          return !0;
                      }
                    : i[0];
            }
            function Te(e, t, n, r, i) {
                for (var o, a = [], s = 0, u = e.length, l = null != t; s < u; s++) (o = e[s]) && ((n && !n(o, r, i)) || (a.push(o), l && t.push(s)));
                return a;
            }
            function Ce(d, h, g, y, v, e) {
                return (
                    y && !y[S] && (y = Ce(y)),
                    v && !v[S] && (v = Ce(v, e)),
                    le(function (e, t, n, r) {
                        var i,
                            o,
                            a,
                            s = [],
                            u = [],
                            l = t.length,
                            c =
                                e ||
                                (function (e, t, n) {
                                    for (var r = 0, i = t.length; r < i; r++) se(e, t[r], n);
                                    return n;
                                })(h || "*", n.nodeType ? [n] : n, []),
                            f = !d || (!e && h) ? c : Te(c, s, d, n, r),
                            p = g ? (v || (e ? d : l || y) ? [] : t) : f;
                        if ((g && g(f, p, n, r), y)) {
                            (i = Te(p, u)), y(i, [], n, r), (o = i.length);
                            while (o--) (a = i[o]) && (p[u[o]] = !(f[u[o]] = a));
                        }
                        if (e) {
                            if (v || d) {
                                if (v) {
                                    (i = []), (o = p.length);
                                    while (o--) (a = p[o]) && i.push((f[o] = a));
                                    v(null, (p = []), i, r);
                                }
                                o = p.length;
                                while (o--) (a = p[o]) && -1 < (i = v ? P(e, a) : s[o]) && (e[i] = !(t[i] = a));
                            }
                        } else (p = Te(p === t ? p.splice(l, p.length) : p)), v ? v(null, t, p, r) : H.apply(t, p);
                    })
                );
            }
            function Ee(e) {
                for (
                    var i,
                        t,
                        n,
                        r = e.length,
                        o = b.relative[e[0].type],
                        a = o || b.relative[" "],
                        s = o ? 1 : 0,
                        u = be(
                            function (e) {
                                return e === i;
                            },
                            a,
                            !0
                        ),
                        l = be(
                            function (e) {
                                return -1 < P(i, e);
                            },
                            a,
                            !0
                        ),
                        c = [
                            function (e, t, n) {
                                var r = (!o && (n || t !== w)) || ((i = t).nodeType ? u(e, t, n) : l(e, t, n));
                                return (i = null), r;
                            },
                        ];
                    s < r;
                    s++
                )
                    if ((t = b.relative[e[s].type])) c = [be(we(c), t)];
                    else {
                        if ((t = b.filter[e[s].type].apply(null, e[s].matches))[S]) {
                            for (n = ++s; n < r; n++) if (b.relative[e[n].type]) break;
                            return Ce(1 < s && we(c), 1 < s && xe(e.slice(0, s - 1).concat({ value: " " === e[s - 2].type ? "*" : "" })).replace(B, "$1"), t, s < n && Ee(e.slice(s, n)), n < r && Ee((e = e.slice(n))), n < r && xe(e));
                        }
                        c.push(t);
                    }
                return we(c);
            }
            return (
                (me.prototype = b.filters = b.pseudos),
                (b.setFilters = new me()),
                (h = se.tokenize = function (e, t) {
                    var n,
                        r,
                        i,
                        o,
                        a,
                        s,
                        u,
                        l = x[e + " "];
                    if (l) return t ? 0 : l.slice(0);
                    (a = e), (s = []), (u = b.preFilter);
                    while (a) {
                        for (o in ((n && !(r = _.exec(a))) || (r && (a = a.slice(r[0].length) || a), s.push((i = []))),
                        (n = !1),
                        (r = z.exec(a)) && ((n = r.shift()), i.push({ value: n, type: r[0].replace(B, " ") }), (a = a.slice(n.length))),
                        b.filter))
                            !(r = G[o].exec(a)) || (u[o] && !(r = u[o](r))) || ((n = r.shift()), i.push({ value: n, type: o, matches: r }), (a = a.slice(n.length)));
                        if (!n) break;
                    }
                    return t ? a.length : a ? se.error(e) : x(e, s).slice(0);
                }),
                (f = se.compile = function (e, t) {
                    var n,
                        y,
                        v,
                        m,
                        x,
                        r,
                        i = [],
                        o = [],
                        a = A[e + " "];
                    if (!a) {
                        t || (t = h(e)), (n = t.length);
                        while (n--) (a = Ee(t[n]))[S] ? i.push(a) : o.push(a);
                        (a = A(
                            e,
                            ((y = o),
                            (m = 0 < (v = i).length),
                            (x = 0 < y.length),
                            (r = function (e, t, n, r, i) {
                                var o,
                                    a,
                                    s,
                                    u = 0,
                                    l = "0",
                                    c = e && [],
                                    f = [],
                                    p = w,
                                    d = e || (x && b.find.TAG("*", i)),
                                    h = (k += null == p ? 1 : Math.random() || 0.1),
                                    g = d.length;
                                for (i && (w = t == C || t || i); l !== g && null != (o = d[l]); l++) {
                                    if (x && o) {
                                        (a = 0), t || o.ownerDocument == C || (T(o), (n = !E));
                                        while ((s = y[a++]))
                                            if (s(o, t || C, n)) {
                                                r.push(o);
                                                break;
                                            }
                                        i && (k = h);
                                    }
                                    m && ((o = !s && o) && u--, e && c.push(o));
                                }
                                if (((u += l), m && l !== u)) {
                                    a = 0;
                                    while ((s = v[a++])) s(c, f, t, n);
                                    if (e) {
                                        if (0 < u) while (l--) c[l] || f[l] || (f[l] = q.call(r));
                                        f = Te(f);
                                    }
                                    H.apply(r, f), i && !e && 0 < f.length && 1 < u + v.length && se.uniqueSort(r);
                                }
                                return i && ((k = h), (w = p)), c;
                            }),
                            m ? le(r) : r)
                        )).selector = e;
                    }
                    return a;
                }),
                (g = se.select = function (e, t, n, r) {
                    var i,
                        o,
                        a,
                        s,
                        u,
                        l = "function" == typeof e && e,
                        c = !r && h((e = l.selector || e));
                    if (((n = n || []), 1 === c.length)) {
                        if (2 < (o = c[0] = c[0].slice(0)).length && "ID" === (a = o[0]).type && 9 === t.nodeType && E && b.relative[o[1].type]) {
                            if (!(t = (b.find.ID(a.matches[0].replace(te, ne), t) || [])[0])) return n;
                            l && (t = t.parentNode), (e = e.slice(o.shift().value.length));
                        }
                        i = G.needsContext.test(e) ? 0 : o.length;
                        while (i--) {
                            if (((a = o[i]), b.relative[(s = a.type)])) break;
                            if ((u = b.find[s]) && (r = u(a.matches[0].replace(te, ne), (ee.test(o[0].type) && ve(t.parentNode)) || t))) {
                                if ((o.splice(i, 1), !(e = r.length && xe(o)))) return H.apply(n, r), n;
                                break;
                            }
                        }
                    }
                    return (l || f(e, c))(r, t, !E, n, !t || (ee.test(e) && ve(t.parentNode)) || t), n;
                }),
                (d.sortStable = S.split("").sort(j).join("") === S),
                (d.detectDuplicates = !!l),
                T(),
                (d.sortDetached = ce(function (e) {
                    return 1 & e.compareDocumentPosition(C.createElement("fieldset"));
                })),
                ce(function (e) {
                    return (e.innerHTML = "<a href='#'></a>"), "#" === e.firstChild.getAttribute("href");
                }) ||
                    fe("type|href|height|width", function (e, t, n) {
                        if (!n) return e.getAttribute(t, "type" === t.toLowerCase() ? 1 : 2);
                    }),
                (d.attributes &&
                    ce(function (e) {
                        return (e.innerHTML = "<input/>"), e.firstChild.setAttribute("value", ""), "" === e.firstChild.getAttribute("value");
                    })) ||
                    fe("value", function (e, t, n) {
                        if (!n && "input" === e.nodeName.toLowerCase()) return e.defaultValue;
                    }),
                ce(function (e) {
                    return null == e.getAttribute("disabled");
                }) ||
                    fe(R, function (e, t, n) {
                        var r;
                        if (!n) return !0 === e[t] ? t.toLowerCase() : (r = e.getAttributeNode(t)) && r.specified ? r.value : null;
                    }),
                se
            );
        })(C);
        (S.find = d), (S.expr = d.selectors), (S.expr[":"] = S.expr.pseudos), (S.uniqueSort = S.unique = d.uniqueSort), (S.text = d.getText), (S.isXMLDoc = d.isXML), (S.contains = d.contains), (S.escapeSelector = d.escape);
        var h = function (e, t, n) {
                var r = [],
                    i = void 0 !== n;
                while ((e = e[t]) && 9 !== e.nodeType)
                    if (1 === e.nodeType) {
                        if (i && S(e).is(n)) break;
                        r.push(e);
                    }
                return r;
            },
            T = function (e, t) {
                for (var n = []; e; e = e.nextSibling) 1 === e.nodeType && e !== t && n.push(e);
                return n;
            },
            k = S.expr.match.needsContext;
        function A(e, t) {
            return e.nodeName && e.nodeName.toLowerCase() === t.toLowerCase();
        }
        var N = /^<([a-z][^\/\0>:\x20\t\r\n\f]*)[\x20\t\r\n\f]*\/?>(?:<\/\1>|)$/i;
        function j(e, n, r) {
            return m(n)
                ? S.grep(e, function (e, t) {
                      return !!n.call(e, t, e) !== r;
                  })
                : n.nodeType
                ? S.grep(e, function (e) {
                      return (e === n) !== r;
                  })
                : "string" != typeof n
                ? S.grep(e, function (e) {
                      return -1 < i.call(n, e) !== r;
                  })
                : S.filter(n, e, r);
        }
        (S.filter = function (e, t, n) {
            var r = t[0];
            return (
                n && (e = ":not(" + e + ")"),
                1 === t.length && 1 === r.nodeType
                    ? S.find.matchesSelector(r, e)
                        ? [r]
                        : []
                    : S.find.matches(
                          e,
                          S.grep(t, function (e) {
                              return 1 === e.nodeType;
                          })
                      )
            );
        }),
            S.fn.extend({
                find: function (e) {
                    var t,
                        n,
                        r = this.length,
                        i = this;
                    if ("string" != typeof e)
                        return this.pushStack(
                            S(e).filter(function () {
                                for (t = 0; t < r; t++) if (S.contains(i[t], this)) return !0;
                            })
                        );
                    for (n = this.pushStack([]), t = 0; t < r; t++) S.find(e, i[t], n);
                    return 1 < r ? S.uniqueSort(n) : n;
                },
                filter: function (e) {
                    return this.pushStack(j(this, e || [], !1));
                },
                not: function (e) {
                    return this.pushStack(j(this, e || [], !0));
                },
                is: function (e) {
                    return !!j(this, "string" == typeof e && k.test(e) ? S(e) : e || [], !1).length;
                },
            });
        var D,
            q = /^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]+))$/;
        ((S.fn.init = function (e, t, n) {
            var r, i;
            if (!e) return this;
            if (((n = n || D), "string" == typeof e)) {
                if (!(r = "<" === e[0] && ">" === e[e.length - 1] && 3 <= e.length ? [null, e, null] : q.exec(e)) || (!r[1] && t)) return !t || t.jquery ? (t || n).find(e) : this.constructor(t).find(e);
                if (r[1]) {
                    if (((t = t instanceof S ? t[0] : t), S.merge(this, S.parseHTML(r[1], t && t.nodeType ? t.ownerDocument || t : E, !0)), N.test(r[1]) && S.isPlainObject(t))) for (r in t) m(this[r]) ? this[r](t[r]) : this.attr(r, t[r]);
                    return this;
                }
                return (i = E.getElementById(r[2])) && ((this[0] = i), (this.length = 1)), this;
            }
            return e.nodeType ? ((this[0] = e), (this.length = 1), this) : m(e) ? (void 0 !== n.ready ? n.ready(e) : e(S)) : S.makeArray(e, this);
        }).prototype = S.fn),
            (D = S(E));
        var L = /^(?:parents|prev(?:Until|All))/,
            H = { children: !0, contents: !0, next: !0, prev: !0 };
        function O(e, t) {
            while ((e = e[t]) && 1 !== e.nodeType);
            return e;
        }
        S.fn.extend({
            has: function (e) {
                var t = S(e, this),
                    n = t.length;
                return this.filter(function () {
                    for (var e = 0; e < n; e++) if (S.contains(this, t[e])) return !0;
                });
            },
            closest: function (e, t) {
                var n,
                    r = 0,
                    i = this.length,
                    o = [],
                    a = "string" != typeof e && S(e);
                if (!k.test(e))
                    for (; r < i; r++)
                        for (n = this[r]; n && n !== t; n = n.parentNode)
                            if (n.nodeType < 11 && (a ? -1 < a.index(n) : 1 === n.nodeType && S.find.matchesSelector(n, e))) {
                                o.push(n);
                                break;
                            }
                return this.pushStack(1 < o.length ? S.uniqueSort(o) : o);
            },
            index: function (e) {
                return e ? ("string" == typeof e ? i.call(S(e), this[0]) : i.call(this, e.jquery ? e[0] : e)) : this[0] && this[0].parentNode ? this.first().prevAll().length : -1;
            },
            add: function (e, t) {
                return this.pushStack(S.uniqueSort(S.merge(this.get(), S(e, t))));
            },
            addBack: function (e) {
                return this.add(null == e ? this.prevObject : this.prevObject.filter(e));
            },
        }),
            S.each(
                {
                    parent: function (e) {
                        var t = e.parentNode;
                        return t && 11 !== t.nodeType ? t : null;
                    },
                    parents: function (e) {
                        return h(e, "parentNode");
                    },
                    parentsUntil: function (e, t, n) {
                        return h(e, "parentNode", n);
                    },
                    next: function (e) {
                        return O(e, "nextSibling");
                    },
                    prev: function (e) {
                        return O(e, "previousSibling");
                    },
                    nextAll: function (e) {
                        return h(e, "nextSibling");
                    },
                    prevAll: function (e) {
                        return h(e, "previousSibling");
                    },
                    nextUntil: function (e, t, n) {
                        return h(e, "nextSibling", n);
                    },
                    prevUntil: function (e, t, n) {
                        return h(e, "previousSibling", n);
                    },
                    siblings: function (e) {
                        return T((e.parentNode || {}).firstChild, e);
                    },
                    children: function (e) {
                        return T(e.firstChild);
                    },
                    contents: function (e) {
                        return null != e.contentDocument && r(e.contentDocument) ? e.contentDocument : (A(e, "template") && (e = e.content || e), S.merge([], e.childNodes));
                    },
                },
                function (r, i) {
                    S.fn[r] = function (e, t) {
                        var n = S.map(this, i, e);
                        return "Until" !== r.slice(-5) && (t = e), t && "string" == typeof t && (n = S.filter(t, n)), 1 < this.length && (H[r] || S.uniqueSort(n), L.test(r) && n.reverse()), this.pushStack(n);
                    };
                }
            );
        var P = /[^\x20\t\r\n\f]+/g;
        function R(e) {
            return e;
        }
        function M(e) {
            throw e;
        }
        function I(e, t, n, r) {
            var i;
            try {
                e && m((i = e.promise)) ? i.call(e).done(t).fail(n) : e && m((i = e.then)) ? i.call(e, t, n) : t.apply(void 0, [e].slice(r));
            } catch (e) {
                n.apply(void 0, [e]);
            }
        }
        (S.Callbacks = function (r) {
            var e, n;
            r =
                "string" == typeof r
                    ? ((e = r),
                      (n = {}),
                      S.each(e.match(P) || [], function (e, t) {
                          n[t] = !0;
                      }),
                      n)
                    : S.extend({}, r);
            var i,
                t,
                o,
                a,
                s = [],
                u = [],
                l = -1,
                c = function () {
                    for (a = a || r.once, o = i = !0; u.length; l = -1) {
                        t = u.shift();
                        while (++l < s.length) !1 === s[l].apply(t[0], t[1]) && r.stopOnFalse && ((l = s.length), (t = !1));
                    }
                    r.memory || (t = !1), (i = !1), a && (s = t ? [] : "");
                },
                f = {
                    add: function () {
                        return (
                            s &&
                                (t && !i && ((l = s.length - 1), u.push(t)),
                                (function n(e) {
                                    S.each(e, function (e, t) {
                                        m(t) ? (r.unique && f.has(t)) || s.push(t) : t && t.length && "string" !== w(t) && n(t);
                                    });
                                })(arguments),
                                t && !i && c()),
                            this
                        );
                    },
                    remove: function () {
                        return (
                            S.each(arguments, function (e, t) {
                                var n;
                                while (-1 < (n = S.inArray(t, s, n))) s.splice(n, 1), n <= l && l--;
                            }),
                            this
                        );
                    },
                    has: function (e) {
                        return e ? -1 < S.inArray(e, s) : 0 < s.length;
                    },
                    empty: function () {
                        return s && (s = []), this;
                    },
                    disable: function () {
                        return (a = u = []), (s = t = ""), this;
                    },
                    disabled: function () {
                        return !s;
                    },
                    lock: function () {
                        return (a = u = []), t || i || (s = t = ""), this;
                    },
                    locked: function () {
                        return !!a;
                    },
                    fireWith: function (e, t) {
                        return a || ((t = [e, (t = t || []).slice ? t.slice() : t]), u.push(t), i || c()), this;
                    },
                    fire: function () {
                        return f.fireWith(this, arguments), this;
                    },
                    fired: function () {
                        return !!o;
                    },
                };
            return f;
        }),
            S.extend({
                Deferred: function (e) {
                    var o = [
                            ["notify", "progress", S.Callbacks("memory"), S.Callbacks("memory"), 2],
                            ["resolve", "done", S.Callbacks("once memory"), S.Callbacks("once memory"), 0, "resolved"],
                            ["reject", "fail", S.Callbacks("once memory"), S.Callbacks("once memory"), 1, "rejected"],
                        ],
                        i = "pending",
                        a = {
                            state: function () {
                                return i;
                            },
                            always: function () {
                                return s.done(arguments).fail(arguments), this;
                            },
                            catch: function (e) {
                                return a.then(null, e);
                            },
                            pipe: function () {
                                var i = arguments;
                                return S.Deferred(function (r) {
                                    S.each(o, function (e, t) {
                                        var n = m(i[t[4]]) && i[t[4]];
                                        s[t[1]](function () {
                                            var e = n && n.apply(this, arguments);
                                            e && m(e.promise) ? e.promise().progress(r.notify).done(r.resolve).fail(r.reject) : r[t[0] + "With"](this, n ? [e] : arguments);
                                        });
                                    }),
                                        (i = null);
                                }).promise();
                            },
                            then: function (t, n, r) {
                                var u = 0;
                                function l(i, o, a, s) {
                                    return function () {
                                        var n = this,
                                            r = arguments,
                                            e = function () {
                                                var e, t;
                                                if (!(i < u)) {
                                                    if ((e = a.apply(n, r)) === o.promise()) throw new TypeError("Thenable self-resolution");
                                                    (t = e && ("object" == typeof e || "function" == typeof e) && e.then),
                                                        m(t)
                                                            ? s
                                                                ? t.call(e, l(u, o, R, s), l(u, o, M, s))
                                                                : (u++, t.call(e, l(u, o, R, s), l(u, o, M, s), l(u, o, R, o.notifyWith)))
                                                            : (a !== R && ((n = void 0), (r = [e])), (s || o.resolveWith)(n, r));
                                                }
                                            },
                                            t = s
                                                ? e
                                                : function () {
                                                      try {
                                                          e();
                                                      } catch (e) {
                                                          S.Deferred.exceptionHook && S.Deferred.exceptionHook(e, t.stackTrace), u <= i + 1 && (a !== M && ((n = void 0), (r = [e])), o.rejectWith(n, r));
                                                      }
                                                  };
                                        i ? t() : (S.Deferred.getStackHook && (t.stackTrace = S.Deferred.getStackHook()), C.setTimeout(t));
                                    };
                                }
                                return S.Deferred(function (e) {
                                    o[0][3].add(l(0, e, m(r) ? r : R, e.notifyWith)), o[1][3].add(l(0, e, m(t) ? t : R)), o[2][3].add(l(0, e, m(n) ? n : M));
                                }).promise();
                            },
                            promise: function (e) {
                                return null != e ? S.extend(e, a) : a;
                            },
                        },
                        s = {};
                    return (
                        S.each(o, function (e, t) {
                            var n = t[2],
                                r = t[5];
                            (a[t[1]] = n.add),
                                r &&
                                    n.add(
                                        function () {
                                            i = r;
                                        },
                                        o[3 - e][2].disable,
                                        o[3 - e][3].disable,
                                        o[0][2].lock,
                                        o[0][3].lock
                                    ),
                                n.add(t[3].fire),
                                (s[t[0]] = function () {
                                    return s[t[0] + "With"](this === s ? void 0 : this, arguments), this;
                                }),
                                (s[t[0] + "With"] = n.fireWith);
                        }),
                        a.promise(s),
                        e && e.call(s, s),
                        s
                    );
                },
                when: function (e) {
                    var n = arguments.length,
                        t = n,
                        r = Array(t),
                        i = s.call(arguments),
                        o = S.Deferred(),
                        a = function (t) {
                            return function (e) {
                                (r[t] = this), (i[t] = 1 < arguments.length ? s.call(arguments) : e), --n || o.resolveWith(r, i);
                            };
                        };
                    if (n <= 1 && (I(e, o.done(a(t)).resolve, o.reject, !n), "pending" === o.state() || m(i[t] && i[t].then))) return o.then();
                    while (t--) I(i[t], a(t), o.reject);
                    return o.promise();
                },
            });
        var W = /^(Eval|Internal|Range|Reference|Syntax|Type|URI)Error$/;
        (S.Deferred.exceptionHook = function (e, t) {
            C.console && C.console.warn && e && W.test(e.name) && C.console.warn("jQuery.Deferred exception: " + e.message, e.stack, t);
        }),
            (S.readyException = function (e) {
                C.setTimeout(function () {
                    throw e;
                });
            });
        var F = S.Deferred();
        function $() {
            E.removeEventListener("DOMContentLoaded", $), C.removeEventListener("load", $), S.ready();
        }
        (S.fn.ready = function (e) {
            return (
                F.then(e)["catch"](function (e) {
                    S.readyException(e);
                }),
                this
            );
        }),
            S.extend({
                isReady: !1,
                readyWait: 1,
                ready: function (e) {
                    (!0 === e ? --S.readyWait : S.isReady) || ((S.isReady = !0) !== e && 0 < --S.readyWait) || F.resolveWith(E, [S]);
                },
            }),
            (S.ready.then = F.then),
            "complete" === E.readyState || ("loading" !== E.readyState && !E.documentElement.doScroll) ? C.setTimeout(S.ready) : (E.addEventListener("DOMContentLoaded", $), C.addEventListener("load", $));
        var B = function (e, t, n, r, i, o, a) {
                var s = 0,
                    u = e.length,
                    l = null == n;
                if ("object" === w(n)) for (s in ((i = !0), n)) B(e, t, s, n[s], !0, o, a);
                else if (
                    void 0 !== r &&
                    ((i = !0),
                    m(r) || (a = !0),
                    l &&
                        (a
                            ? (t.call(e, r), (t = null))
                            : ((l = t),
                              (t = function (e, t, n) {
                                  return l.call(S(e), n);
                              }))),
                    t)
                )
                    for (; s < u; s++) t(e[s], n, a ? r : r.call(e[s], s, t(e[s], n)));
                return i ? e : l ? t.call(e) : u ? t(e[0], n) : o;
            },
            _ = /^-ms-/,
            z = /-([a-z])/g;
        function U(e, t) {
            return t.toUpperCase();
        }
        function X(e) {
            return e.replace(_, "ms-").replace(z, U);
        }
        var V = function (e) {
            return 1 === e.nodeType || 9 === e.nodeType || !+e.nodeType;
        };
        function G() {
            this.expando = S.expando + G.uid++;
        }
        (G.uid = 1),
            (G.prototype = {
                cache: function (e) {
                    var t = e[this.expando];
                    return t || ((t = {}), V(e) && (e.nodeType ? (e[this.expando] = t) : Object.defineProperty(e, this.expando, { value: t, configurable: !0 }))), t;
                },
                set: function (e, t, n) {
                    var r,
                        i = this.cache(e);
                    if ("string" == typeof t) i[X(t)] = n;
                    else for (r in t) i[X(r)] = t[r];
                    return i;
                },
                get: function (e, t) {
                    return void 0 === t ? this.cache(e) : e[this.expando] && e[this.expando][X(t)];
                },
                access: function (e, t, n) {
                    return void 0 === t || (t && "string" == typeof t && void 0 === n) ? this.get(e, t) : (this.set(e, t, n), void 0 !== n ? n : t);
                },
                remove: function (e, t) {
                    var n,
                        r = e[this.expando];
                    if (void 0 !== r) {
                        if (void 0 !== t) {
                            n = (t = Array.isArray(t) ? t.map(X) : (t = X(t)) in r ? [t] : t.match(P) || []).length;
                            while (n--) delete r[t[n]];
                        }
                        (void 0 === t || S.isEmptyObject(r)) && (e.nodeType ? (e[this.expando] = void 0) : delete e[this.expando]);
                    }
                },
                hasData: function (e) {
                    var t = e[this.expando];
                    return void 0 !== t && !S.isEmptyObject(t);
                },
            });
        var Y = new G(),
            Q = new G(),
            J = /^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,
            K = /[A-Z]/g;
        function Z(e, t, n) {
            var r, i;
            if (void 0 === n && 1 === e.nodeType)
                if (((r = "data-" + t.replace(K, "-$&").toLowerCase()), "string" == typeof (n = e.getAttribute(r)))) {
                    try {
                        n = "true" === (i = n) || ("false" !== i && ("null" === i ? null : i === +i + "" ? +i : J.test(i) ? JSON.parse(i) : i));
                    } catch (e) {}
                    Q.set(e, t, n);
                } else n = void 0;
            return n;
        }
        S.extend({
            hasData: function (e) {
                return Q.hasData(e) || Y.hasData(e);
            },
            data: function (e, t, n) {
                return Q.access(e, t, n);
            },
            removeData: function (e, t) {
                Q.remove(e, t);
            },
            _data: function (e, t, n) {
                return Y.access(e, t, n);
            },
            _removeData: function (e, t) {
                Y.remove(e, t);
            },
        }),
            S.fn.extend({
                data: function (n, e) {
                    var t,
                        r,
                        i,
                        o = this[0],
                        a = o && o.attributes;
                    if (void 0 === n) {
                        if (this.length && ((i = Q.get(o)), 1 === o.nodeType && !Y.get(o, "hasDataAttrs"))) {
                            t = a.length;
                            while (t--) a[t] && 0 === (r = a[t].name).indexOf("data-") && ((r = X(r.slice(5))), Z(o, r, i[r]));
                            Y.set(o, "hasDataAttrs", !0);
                        }
                        return i;
                    }
                    return "object" == typeof n
                        ? this.each(function () {
                              Q.set(this, n);
                          })
                        : B(
                              this,
                              function (e) {
                                  var t;
                                  if (o && void 0 === e) return void 0 !== (t = Q.get(o, n)) ? t : void 0 !== (t = Z(o, n)) ? t : void 0;
                                  this.each(function () {
                                      Q.set(this, n, e);
                                  });
                              },
                              null,
                              e,
                              1 < arguments.length,
                              null,
                              !0
                          );
                },
                removeData: function (e) {
                    return this.each(function () {
                        Q.remove(this, e);
                    });
                },
            }),
            S.extend({
                queue: function (e, t, n) {
                    var r;
                    if (e) return (t = (t || "fx") + "queue"), (r = Y.get(e, t)), n && (!r || Array.isArray(n) ? (r = Y.access(e, t, S.makeArray(n))) : r.push(n)), r || [];
                },
                dequeue: function (e, t) {
                    t = t || "fx";
                    var n = S.queue(e, t),
                        r = n.length,
                        i = n.shift(),
                        o = S._queueHooks(e, t);
                    "inprogress" === i && ((i = n.shift()), r--),
                        i &&
                            ("fx" === t && n.unshift("inprogress"),
                            delete o.stop,
                            i.call(
                                e,
                                function () {
                                    S.dequeue(e, t);
                                },
                                o
                            )),
                        !r && o && o.empty.fire();
                },
                _queueHooks: function (e, t) {
                    var n = t + "queueHooks";
                    return (
                        Y.get(e, n) ||
                        Y.access(e, n, {
                            empty: S.Callbacks("once memory").add(function () {
                                Y.remove(e, [t + "queue", n]);
                            }),
                        })
                    );
                },
            }),
            S.fn.extend({
                queue: function (t, n) {
                    var e = 2;
                    return (
                        "string" != typeof t && ((n = t), (t = "fx"), e--),
                        arguments.length < e
                            ? S.queue(this[0], t)
                            : void 0 === n
                            ? this
                            : this.each(function () {
                                  var e = S.queue(this, t, n);
                                  S._queueHooks(this, t), "fx" === t && "inprogress" !== e[0] && S.dequeue(this, t);
                              })
                    );
                },
                dequeue: function (e) {
                    return this.each(function () {
                        S.dequeue(this, e);
                    });
                },
                clearQueue: function (e) {
                    return this.queue(e || "fx", []);
                },
                promise: function (e, t) {
                    var n,
                        r = 1,
                        i = S.Deferred(),
                        o = this,
                        a = this.length,
                        s = function () {
                            --r || i.resolveWith(o, [o]);
                        };
                    "string" != typeof e && ((t = e), (e = void 0)), (e = e || "fx");
                    while (a--) (n = Y.get(o[a], e + "queueHooks")) && n.empty && (r++, n.empty.add(s));
                    return s(), i.promise(t);
                },
            });
        var ee = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,
            te = new RegExp("^(?:([+-])=|)(" + ee + ")([a-z%]*)$", "i"),
            ne = ["Top", "Right", "Bottom", "Left"],
            re = E.documentElement,
            ie = function (e) {
                return S.contains(e.ownerDocument, e);
            },
            oe = { composed: !0 };
        re.getRootNode &&
            (ie = function (e) {
                return S.contains(e.ownerDocument, e) || e.getRootNode(oe) === e.ownerDocument;
            });
        var ae = function (e, t) {
            return "none" === (e = t || e).style.display || ("" === e.style.display && ie(e) && "none" === S.css(e, "display"));
        };
        function se(e, t, n, r) {
            var i,
                o,
                a = 20,
                s = r
                    ? function () {
                          return r.cur();
                      }
                    : function () {
                          return S.css(e, t, "");
                      },
                u = s(),
                l = (n && n[3]) || (S.cssNumber[t] ? "" : "px"),
                c = e.nodeType && (S.cssNumber[t] || ("px" !== l && +u)) && te.exec(S.css(e, t));
            if (c && c[3] !== l) {
                (u /= 2), (l = l || c[3]), (c = +u || 1);
                while (a--) S.style(e, t, c + l), (1 - o) * (1 - (o = s() / u || 0.5)) <= 0 && (a = 0), (c /= o);
                (c *= 2), S.style(e, t, c + l), (n = n || []);
            }
            return n && ((c = +c || +u || 0), (i = n[1] ? c + (n[1] + 1) * n[2] : +n[2]), r && ((r.unit = l), (r.start = c), (r.end = i))), i;
        }
        var ue = {};
        function le(e, t) {
            for (var n, r, i, o, a, s, u, l = [], c = 0, f = e.length; c < f; c++)
                (r = e[c]).style &&
                    ((n = r.style.display),
                    t
                        ? ("none" === n && ((l[c] = Y.get(r, "display") || null), l[c] || (r.style.display = "")),
                          "" === r.style.display &&
                              ae(r) &&
                              (l[c] =
                                  ((u = a = o = void 0),
                                  (a = (i = r).ownerDocument),
                                  (s = i.nodeName),
                                  (u = ue[s]) || ((o = a.body.appendChild(a.createElement(s))), (u = S.css(o, "display")), o.parentNode.removeChild(o), "none" === u && (u = "block"), (ue[s] = u)))))
                        : "none" !== n && ((l[c] = "none"), Y.set(r, "display", n)));
            for (c = 0; c < f; c++) null != l[c] && (e[c].style.display = l[c]);
            return e;
        }
        S.fn.extend({
            show: function () {
                return le(this, !0);
            },
            hide: function () {
                return le(this);
            },
            toggle: function (e) {
                return "boolean" == typeof e
                    ? e
                        ? this.show()
                        : this.hide()
                    : this.each(function () {
                          ae(this) ? S(this).show() : S(this).hide();
                      });
            },
        });
        var ce,
            fe,
            pe = /^(?:checkbox|radio)$/i,
            de = /<([a-z][^\/\0>\x20\t\r\n\f]*)/i,
            he = /^$|^module$|\/(?:java|ecma)script/i;
        (ce = E.createDocumentFragment().appendChild(E.createElement("div"))),
            (fe = E.createElement("input")).setAttribute("type", "radio"),
            fe.setAttribute("checked", "checked"),
            fe.setAttribute("name", "t"),
            ce.appendChild(fe),
            (v.checkClone = ce.cloneNode(!0).cloneNode(!0).lastChild.checked),
            (ce.innerHTML = "<textarea>x</textarea>"),
            (v.noCloneChecked = !!ce.cloneNode(!0).lastChild.defaultValue),
            (ce.innerHTML = "<option></option>"),
            (v.option = !!ce.lastChild);
        var ge = { thead: [1, "<table>", "</table>"], col: [2, "<table><colgroup>", "</colgroup></table>"], tr: [2, "<table><tbody>", "</tbody></table>"], td: [3, "<table><tbody><tr>", "</tr></tbody></table>"], _default: [0, "", ""] };
        function ye(e, t) {
            var n;
            return (n = "undefined" != typeof e.getElementsByTagName ? e.getElementsByTagName(t || "*") : "undefined" != typeof e.querySelectorAll ? e.querySelectorAll(t || "*") : []), void 0 === t || (t && A(e, t)) ? S.merge([e], n) : n;
        }
        function ve(e, t) {
            for (var n = 0, r = e.length; n < r; n++) Y.set(e[n], "globalEval", !t || Y.get(t[n], "globalEval"));
        }
        (ge.tbody = ge.tfoot = ge.colgroup = ge.caption = ge.thead), (ge.th = ge.td), v.option || (ge.optgroup = ge.option = [1, "<select multiple='multiple'>", "</select>"]);
        var me = /<|&#?\w+;/;
        function xe(e, t, n, r, i) {
            for (var o, a, s, u, l, c, f = t.createDocumentFragment(), p = [], d = 0, h = e.length; d < h; d++)
                if ((o = e[d]) || 0 === o)
                    if ("object" === w(o)) S.merge(p, o.nodeType ? [o] : o);
                    else if (me.test(o)) {
                        (a = a || f.appendChild(t.createElement("div"))), (s = (de.exec(o) || ["", ""])[1].toLowerCase()), (u = ge[s] || ge._default), (a.innerHTML = u[1] + S.htmlPrefilter(o) + u[2]), (c = u[0]);
                        while (c--) a = a.lastChild;
                        S.merge(p, a.childNodes), ((a = f.firstChild).textContent = "");
                    } else p.push(t.createTextNode(o));
            (f.textContent = ""), (d = 0);
            while ((o = p[d++]))
                if (r && -1 < S.inArray(o, r)) i && i.push(o);
                else if (((l = ie(o)), (a = ye(f.appendChild(o), "script")), l && ve(a), n)) {
                    c = 0;
                    while ((o = a[c++])) he.test(o.type || "") && n.push(o);
                }
            return f;
        }
        var be = /^([^.]*)(?:\.(.+)|)/;
        function we() {
            return !0;
        }
        function Te() {
            return !1;
        }
        function Ce(e, t) {
            return (
                (e ===
                    (function () {
                        try {
                            return E.activeElement;
                        } catch (e) {}
                    })()) ==
                ("focus" === t)
            );
        }
        function Ee(e, t, n, r, i, o) {
            var a, s;
            if ("object" == typeof t) {
                for (s in ("string" != typeof n && ((r = r || n), (n = void 0)), t)) Ee(e, s, n, r, t[s], o);
                return e;
            }
            if ((null == r && null == i ? ((i = n), (r = n = void 0)) : null == i && ("string" == typeof n ? ((i = r), (r = void 0)) : ((i = r), (r = n), (n = void 0))), !1 === i)) i = Te;
            else if (!i) return e;
            return (
                1 === o &&
                    ((a = i),
                    ((i = function (e) {
                        return S().off(e), a.apply(this, arguments);
                    }).guid = a.guid || (a.guid = S.guid++))),
                e.each(function () {
                    S.event.add(this, t, i, r, n);
                })
            );
        }
        function Se(e, i, o) {
            o
                ? (Y.set(e, i, !1),
                  S.event.add(e, i, {
                      namespace: !1,
                      handler: function (e) {
                          var t,
                              n,
                              r = Y.get(this, i);
                          if (1 & e.isTrigger && this[i]) {
                              if (r.length) (S.event.special[i] || {}).delegateType && e.stopPropagation();
                              else if (((r = s.call(arguments)), Y.set(this, i, r), (t = o(this, i)), this[i](), r !== (n = Y.get(this, i)) || t ? Y.set(this, i, !1) : (n = {}), r !== n))
                                  return e.stopImmediatePropagation(), e.preventDefault(), n && n.value;
                          } else r.length && (Y.set(this, i, { value: S.event.trigger(S.extend(r[0], S.Event.prototype), r.slice(1), this) }), e.stopImmediatePropagation());
                      },
                  }))
                : void 0 === Y.get(e, i) && S.event.add(e, i, we);
        }
        (S.event = {
            global: {},
            add: function (t, e, n, r, i) {
                var o,
                    a,
                    s,
                    u,
                    l,
                    c,
                    f,
                    p,
                    d,
                    h,
                    g,
                    y = Y.get(t);
                if (V(t)) {
                    n.handler && ((n = (o = n).handler), (i = o.selector)),
                        i && S.find.matchesSelector(re, i),
                        n.guid || (n.guid = S.guid++),
                        (u = y.events) || (u = y.events = Object.create(null)),
                        (a = y.handle) ||
                            (a = y.handle = function (e) {
                                return "undefined" != typeof S && S.event.triggered !== e.type ? S.event.dispatch.apply(t, arguments) : void 0;
                            }),
                        (l = (e = (e || "").match(P) || [""]).length);
                    while (l--)
                        (d = g = (s = be.exec(e[l]) || [])[1]),
                            (h = (s[2] || "").split(".").sort()),
                            d &&
                                ((f = S.event.special[d] || {}),
                                (d = (i ? f.delegateType : f.bindType) || d),
                                (f = S.event.special[d] || {}),
                                (c = S.extend({ type: d, origType: g, data: r, handler: n, guid: n.guid, selector: i, needsContext: i && S.expr.match.needsContext.test(i), namespace: h.join(".") }, o)),
                                (p = u[d]) || (((p = u[d] = []).delegateCount = 0), (f.setup && !1 !== f.setup.call(t, r, h, a)) || (t.addEventListener && t.addEventListener(d, a))),
                                f.add && (f.add.call(t, c), c.handler.guid || (c.handler.guid = n.guid)),
                                i ? p.splice(p.delegateCount++, 0, c) : p.push(c),
                                (S.event.global[d] = !0));
                }
            },
            remove: function (e, t, n, r, i) {
                var o,
                    a,
                    s,
                    u,
                    l,
                    c,
                    f,
                    p,
                    d,
                    h,
                    g,
                    y = Y.hasData(e) && Y.get(e);
                if (y && (u = y.events)) {
                    l = (t = (t || "").match(P) || [""]).length;
                    while (l--)
                        if (((d = g = (s = be.exec(t[l]) || [])[1]), (h = (s[2] || "").split(".").sort()), d)) {
                            (f = S.event.special[d] || {}), (p = u[(d = (r ? f.delegateType : f.bindType) || d)] || []), (s = s[2] && new RegExp("(^|\\.)" + h.join("\\.(?:.*\\.|)") + "(\\.|$)")), (a = o = p.length);
                            while (o--)
                                (c = p[o]),
                                    (!i && g !== c.origType) ||
                                        (n && n.guid !== c.guid) ||
                                        (s && !s.test(c.namespace)) ||
                                        (r && r !== c.selector && ("**" !== r || !c.selector)) ||
                                        (p.splice(o, 1), c.selector && p.delegateCount--, f.remove && f.remove.call(e, c));
                            a && !p.length && ((f.teardown && !1 !== f.teardown.call(e, h, y.handle)) || S.removeEvent(e, d, y.handle), delete u[d]);
                        } else for (d in u) S.event.remove(e, d + t[l], n, r, !0);
                    S.isEmptyObject(u) && Y.remove(e, "handle events");
                }
            },
            dispatch: function (e) {
                var t,
                    n,
                    r,
                    i,
                    o,
                    a,
                    s = new Array(arguments.length),
                    u = S.event.fix(e),
                    l = (Y.get(this, "events") || Object.create(null))[u.type] || [],
                    c = S.event.special[u.type] || {};
                for (s[0] = u, t = 1; t < arguments.length; t++) s[t] = arguments[t];
                if (((u.delegateTarget = this), !c.preDispatch || !1 !== c.preDispatch.call(this, u))) {
                    (a = S.event.handlers.call(this, u, l)), (t = 0);
                    while ((i = a[t++]) && !u.isPropagationStopped()) {
                        (u.currentTarget = i.elem), (n = 0);
                        while ((o = i.handlers[n++]) && !u.isImmediatePropagationStopped())
                            (u.rnamespace && !1 !== o.namespace && !u.rnamespace.test(o.namespace)) ||
                                ((u.handleObj = o), (u.data = o.data), void 0 !== (r = ((S.event.special[o.origType] || {}).handle || o.handler).apply(i.elem, s)) && !1 === (u.result = r) && (u.preventDefault(), u.stopPropagation()));
                    }
                    return c.postDispatch && c.postDispatch.call(this, u), u.result;
                }
            },
            handlers: function (e, t) {
                var n,
                    r,
                    i,
                    o,
                    a,
                    s = [],
                    u = t.delegateCount,
                    l = e.target;
                if (u && l.nodeType && !("click" === e.type && 1 <= e.button))
                    for (; l !== this; l = l.parentNode || this)
                        if (1 === l.nodeType && ("click" !== e.type || !0 !== l.disabled)) {
                            for (o = [], a = {}, n = 0; n < u; n++) void 0 === a[(i = (r = t[n]).selector + " ")] && (a[i] = r.needsContext ? -1 < S(i, this).index(l) : S.find(i, this, null, [l]).length), a[i] && o.push(r);
                            o.length && s.push({ elem: l, handlers: o });
                        }
                return (l = this), u < t.length && s.push({ elem: l, handlers: t.slice(u) }), s;
            },
            addProp: function (t, e) {
                Object.defineProperty(S.Event.prototype, t, {
                    enumerable: !0,
                    configurable: !0,
                    get: m(e)
                        ? function () {
                              if (this.originalEvent) return e(this.originalEvent);
                          }
                        : function () {
                              if (this.originalEvent) return this.originalEvent[t];
                          },
                    set: function (e) {
                        Object.defineProperty(this, t, { enumerable: !0, configurable: !0, writable: !0, value: e });
                    },
                });
            },
            fix: function (e) {
                return e[S.expando] ? e : new S.Event(e);
            },
            special: {
                load: { noBubble: !0 },
                click: {
                    setup: function (e) {
                        var t = this || e;
                        return pe.test(t.type) && t.click && A(t, "input") && Se(t, "click", we), !1;
                    },
                    trigger: function (e) {
                        var t = this || e;
                        return pe.test(t.type) && t.click && A(t, "input") && Se(t, "click"), !0;
                    },
                    _default: function (e) {
                        var t = e.target;
                        return (pe.test(t.type) && t.click && A(t, "input") && Y.get(t, "click")) || A(t, "a");
                    },
                },
                beforeunload: {
                    postDispatch: function (e) {
                        void 0 !== e.result && e.originalEvent && (e.originalEvent.returnValue = e.result);
                    },
                },
            },
        }),
            (S.removeEvent = function (e, t, n) {
                e.removeEventListener && e.removeEventListener(t, n);
            }),
            (S.Event = function (e, t) {
                if (!(this instanceof S.Event)) return new S.Event(e, t);
                e && e.type
                    ? ((this.originalEvent = e),
                      (this.type = e.type),
                      (this.isDefaultPrevented = e.defaultPrevented || (void 0 === e.defaultPrevented && !1 === e.returnValue) ? we : Te),
                      (this.target = e.target && 3 === e.target.nodeType ? e.target.parentNode : e.target),
                      (this.currentTarget = e.currentTarget),
                      (this.relatedTarget = e.relatedTarget))
                    : (this.type = e),
                    t && S.extend(this, t),
                    (this.timeStamp = (e && e.timeStamp) || Date.now()),
                    (this[S.expando] = !0);
            }),
            (S.Event.prototype = {
                constructor: S.Event,
                isDefaultPrevented: Te,
                isPropagationStopped: Te,
                isImmediatePropagationStopped: Te,
                isSimulated: !1,
                preventDefault: function () {
                    var e = this.originalEvent;
                    (this.isDefaultPrevented = we), e && !this.isSimulated && e.preventDefault();
                },
                stopPropagation: function () {
                    var e = this.originalEvent;
                    (this.isPropagationStopped = we), e && !this.isSimulated && e.stopPropagation();
                },
                stopImmediatePropagation: function () {
                    var e = this.originalEvent;
                    (this.isImmediatePropagationStopped = we), e && !this.isSimulated && e.stopImmediatePropagation(), this.stopPropagation();
                },
            }),
            S.each(
                {
                    altKey: !0,
                    bubbles: !0,
                    cancelable: !0,
                    changedTouches: !0,
                    ctrlKey: !0,
                    detail: !0,
                    eventPhase: !0,
                    metaKey: !0,
                    pageX: !0,
                    pageY: !0,
                    shiftKey: !0,
                    view: !0,
                    char: !0,
                    code: !0,
                    charCode: !0,
                    key: !0,
                    keyCode: !0,
                    button: !0,
                    buttons: !0,
                    clientX: !0,
                    clientY: !0,
                    offsetX: !0,
                    offsetY: !0,
                    pointerId: !0,
                    pointerType: !0,
                    screenX: !0,
                    screenY: !0,
                    targetTouches: !0,
                    toElement: !0,
                    touches: !0,
                    which: !0,
                },
                S.event.addProp
            ),
            S.each({ focus: "focusin", blur: "focusout" }, function (t, e) {
                S.event.special[t] = {
                    setup: function () {
                        return Se(this, t, Ce), !1;
                    },
                    trigger: function () {
                        return Se(this, t), !0;
                    },
                    _default: function (e) {
                        return Y.get(e.target, t);
                    },
                    delegateType: e,
                };
            }),
            S.each({ mouseenter: "mouseover", mouseleave: "mouseout", pointerenter: "pointerover", pointerleave: "pointerout" }, function (e, i) {
                S.event.special[e] = {
                    delegateType: i,
                    bindType: i,
                    handle: function (e) {
                        var t,
                            n = e.relatedTarget,
                            r = e.handleObj;
                        return (n && (n === this || S.contains(this, n))) || ((e.type = r.origType), (t = r.handler.apply(this, arguments)), (e.type = i)), t;
                    },
                };
            }),
            S.fn.extend({
                on: function (e, t, n, r) {
                    return Ee(this, e, t, n, r);
                },
                one: function (e, t, n, r) {
                    return Ee(this, e, t, n, r, 1);
                },
                off: function (e, t, n) {
                    var r, i;
                    if (e && e.preventDefault && e.handleObj) return (r = e.handleObj), S(e.delegateTarget).off(r.namespace ? r.origType + "." + r.namespace : r.origType, r.selector, r.handler), this;
                    if ("object" == typeof e) {
                        for (i in e) this.off(i, t, e[i]);
                        return this;
                    }
                    return (
                        (!1 !== t && "function" != typeof t) || ((n = t), (t = void 0)),
                        !1 === n && (n = Te),
                        this.each(function () {
                            S.event.remove(this, e, n, t);
                        })
                    );
                },
            });
        var ke = /<script|<style|<link/i,
            Ae = /checked\s*(?:[^=]|=\s*.checked.)/i,
            Ne = /^\s*<!\[CDATA\[|\]\]>\s*$/g;
        function je(e, t) {
            return (A(e, "table") && A(11 !== t.nodeType ? t : t.firstChild, "tr") && S(e).children("tbody")[0]) || e;
        }
        function De(e) {
            return (e.type = (null !== e.getAttribute("type")) + "/" + e.type), e;
        }
        function qe(e) {
            return "true/" === (e.type || "").slice(0, 5) ? (e.type = e.type.slice(5)) : e.removeAttribute("type"), e;
        }
        function Le(e, t) {
            var n, r, i, o, a, s;
            if (1 === t.nodeType) {
                if (Y.hasData(e) && (s = Y.get(e).events)) for (i in (Y.remove(t, "handle events"), s)) for (n = 0, r = s[i].length; n < r; n++) S.event.add(t, i, s[i][n]);
                Q.hasData(e) && ((o = Q.access(e)), (a = S.extend({}, o)), Q.set(t, a));
            }
        }
        function He(n, r, i, o) {
            r = g(r);
            var e,
                t,
                a,
                s,
                u,
                l,
                c = 0,
                f = n.length,
                p = f - 1,
                d = r[0],
                h = m(d);
            if (h || (1 < f && "string" == typeof d && !v.checkClone && Ae.test(d)))
                return n.each(function (e) {
                    var t = n.eq(e);
                    h && (r[0] = d.call(this, e, t.html())), He(t, r, i, o);
                });
            if (f && ((t = (e = xe(r, n[0].ownerDocument, !1, n, o)).firstChild), 1 === e.childNodes.length && (e = t), t || o)) {
                for (s = (a = S.map(ye(e, "script"), De)).length; c < f; c++) (u = e), c !== p && ((u = S.clone(u, !0, !0)), s && S.merge(a, ye(u, "script"))), i.call(n[c], u, c);
                if (s)
                    for (l = a[a.length - 1].ownerDocument, S.map(a, qe), c = 0; c < s; c++)
                        (u = a[c]),
                            he.test(u.type || "") &&
                                !Y.access(u, "globalEval") &&
                                S.contains(l, u) &&
                                (u.src && "module" !== (u.type || "").toLowerCase() ? S._evalUrl && !u.noModule && S._evalUrl(u.src, { nonce: u.nonce || u.getAttribute("nonce") }, l) : b(u.textContent.replace(Ne, ""), u, l));
            }
            return n;
        }
        function Oe(e, t, n) {
            for (var r, i = t ? S.filter(t, e) : e, o = 0; null != (r = i[o]); o++) n || 1 !== r.nodeType || S.cleanData(ye(r)), r.parentNode && (n && ie(r) && ve(ye(r, "script")), r.parentNode.removeChild(r));
            return e;
        }
        S.extend({
            htmlPrefilter: function (e) {
                return e;
            },
            clone: function (e, t, n) {
                var r,
                    i,
                    o,
                    a,
                    s,
                    u,
                    l,
                    c = e.cloneNode(!0),
                    f = ie(e);
                if (!(v.noCloneChecked || (1 !== e.nodeType && 11 !== e.nodeType) || S.isXMLDoc(e)))
                    for (a = ye(c), r = 0, i = (o = ye(e)).length; r < i; r++)
                        (s = o[r]), (u = a[r]), void 0, "input" === (l = u.nodeName.toLowerCase()) && pe.test(s.type) ? (u.checked = s.checked) : ("input" !== l && "textarea" !== l) || (u.defaultValue = s.defaultValue);
                if (t)
                    if (n) for (o = o || ye(e), a = a || ye(c), r = 0, i = o.length; r < i; r++) Le(o[r], a[r]);
                    else Le(e, c);
                return 0 < (a = ye(c, "script")).length && ve(a, !f && ye(e, "script")), c;
            },
            cleanData: function (e) {
                for (var t, n, r, i = S.event.special, o = 0; void 0 !== (n = e[o]); o++)
                    if (V(n)) {
                        if ((t = n[Y.expando])) {
                            if (t.events) for (r in t.events) i[r] ? S.event.remove(n, r) : S.removeEvent(n, r, t.handle);
                            n[Y.expando] = void 0;
                        }
                        n[Q.expando] && (n[Q.expando] = void 0);
                    }
            },
        }),
            S.fn.extend({
                detach: function (e) {
                    return Oe(this, e, !0);
                },
                remove: function (e) {
                    return Oe(this, e);
                },
                text: function (e) {
                    return B(
                        this,
                        function (e) {
                            return void 0 === e
                                ? S.text(this)
                                : this.empty().each(function () {
                                      (1 !== this.nodeType && 11 !== this.nodeType && 9 !== this.nodeType) || (this.textContent = e);
                                  });
                        },
                        null,
                        e,
                        arguments.length
                    );
                },
                append: function () {
                    return He(this, arguments, function (e) {
                        (1 !== this.nodeType && 11 !== this.nodeType && 9 !== this.nodeType) || je(this, e).appendChild(e);
                    });
                },
                prepend: function () {
                    return He(this, arguments, function (e) {
                        if (1 === this.nodeType || 11 === this.nodeType || 9 === this.nodeType) {
                            var t = je(this, e);
                            t.insertBefore(e, t.firstChild);
                        }
                    });
                },
                before: function () {
                    return He(this, arguments, function (e) {
                        this.parentNode && this.parentNode.insertBefore(e, this);
                    });
                },
                after: function () {
                    return He(this, arguments, function (e) {
                        this.parentNode && this.parentNode.insertBefore(e, this.nextSibling);
                    });
                },
                empty: function () {
                    for (var e, t = 0; null != (e = this[t]); t++) 1 === e.nodeType && (S.cleanData(ye(e, !1)), (e.textContent = ""));
                    return this;
                },
                clone: function (e, t) {
                    return (
                        (e = null != e && e),
                        (t = null == t ? e : t),
                        this.map(function () {
                            return S.clone(this, e, t);
                        })
                    );
                },
                html: function (e) {
                    return B(
                        this,
                        function (e) {
                            var t = this[0] || {},
                                n = 0,
                                r = this.length;
                            if (void 0 === e && 1 === t.nodeType) return t.innerHTML;
                            if ("string" == typeof e && !ke.test(e) && !ge[(de.exec(e) || ["", ""])[1].toLowerCase()]) {
                                e = S.htmlPrefilter(e);
                                try {
                                    for (; n < r; n++) 1 === (t = this[n] || {}).nodeType && (S.cleanData(ye(t, !1)), (t.innerHTML = e));
                                    t = 0;
                                } catch (e) {}
                            }
                            t && this.empty().append(e);
                        },
                        null,
                        e,
                        arguments.length
                    );
                },
                replaceWith: function () {
                    var n = [];
                    return He(
                        this,
                        arguments,
                        function (e) {
                            var t = this.parentNode;
                            S.inArray(this, n) < 0 && (S.cleanData(ye(this)), t && t.replaceChild(e, this));
                        },
                        n
                    );
                },
            }),
            S.each({ appendTo: "append", prependTo: "prepend", insertBefore: "before", insertAfter: "after", replaceAll: "replaceWith" }, function (e, a) {
                S.fn[e] = function (e) {
                    for (var t, n = [], r = S(e), i = r.length - 1, o = 0; o <= i; o++) (t = o === i ? this : this.clone(!0)), S(r[o])[a](t), u.apply(n, t.get());
                    return this.pushStack(n);
                };
            });
        var Pe = new RegExp("^(" + ee + ")(?!px)[a-z%]+$", "i"),
            Re = /^--/,
            Me = function (e) {
                var t = e.ownerDocument.defaultView;
                return (t && t.opener) || (t = C), t.getComputedStyle(e);
            },
            Ie = function (e, t, n) {
                var r,
                    i,
                    o = {};
                for (i in t) (o[i] = e.style[i]), (e.style[i] = t[i]);
                for (i in ((r = n.call(e)), t)) e.style[i] = o[i];
                return r;
            },
            We = new RegExp(ne.join("|"), "i"),
            Fe = "[\\x20\\t\\r\\n\\f]",
            $e = new RegExp("^" + Fe + "+|((?:^|[^\\\\])(?:\\\\.)*)" + Fe + "+$", "g");
        function Be(e, t, n) {
            var r,
                i,
                o,
                a,
                s = Re.test(t),
                u = e.style;
            return (
                (n = n || Me(e)) &&
                    ((a = n.getPropertyValue(t) || n[t]),
                    s && a && (a = a.replace($e, "$1") || void 0),
                    "" !== a || ie(e) || (a = S.style(e, t)),
                    !v.pixelBoxStyles() && Pe.test(a) && We.test(t) && ((r = u.width), (i = u.minWidth), (o = u.maxWidth), (u.minWidth = u.maxWidth = u.width = a), (a = n.width), (u.width = r), (u.minWidth = i), (u.maxWidth = o))),
                void 0 !== a ? a + "" : a
            );
        }
        function _e(e, t) {
            return {
                get: function () {
                    if (!e()) return (this.get = t).apply(this, arguments);
                    delete this.get;
                },
            };
        }
        !(function () {
            function e() {
                if (l) {
                    (u.style.cssText = "position:absolute;left:-11111px;width:60px;margin-top:1px;padding:0;border:0"),
                        (l.style.cssText = "position:relative;display:block;box-sizing:border-box;overflow:scroll;margin:auto;border:1px;padding:1px;width:60%;top:1%"),
                        re.appendChild(u).appendChild(l);
                    var e = C.getComputedStyle(l);
                    (n = "1%" !== e.top),
                        (s = 12 === t(e.marginLeft)),
                        (l.style.right = "60%"),
                        (o = 36 === t(e.right)),
                        (r = 36 === t(e.width)),
                        (l.style.position = "absolute"),
                        (i = 12 === t(l.offsetWidth / 3)),
                        re.removeChild(u),
                        (l = null);
                }
            }
            function t(e) {
                return Math.round(parseFloat(e));
            }
            var n,
                r,
                i,
                o,
                a,
                s,
                u = E.createElement("div"),
                l = E.createElement("div");
            l.style &&
                ((l.style.backgroundClip = "content-box"),
                (l.cloneNode(!0).style.backgroundClip = ""),
                (v.clearCloneStyle = "content-box" === l.style.backgroundClip),
                S.extend(v, {
                    boxSizingReliable: function () {
                        return e(), r;
                    },
                    pixelBoxStyles: function () {
                        return e(), o;
                    },
                    pixelPosition: function () {
                        return e(), n;
                    },
                    reliableMarginLeft: function () {
                        return e(), s;
                    },
                    scrollboxSize: function () {
                        return e(), i;
                    },
                    reliableTrDimensions: function () {
                        var e, t, n, r;
                        return (
                            null == a &&
                                ((e = E.createElement("table")),
                                (t = E.createElement("tr")),
                                (n = E.createElement("div")),
                                (e.style.cssText = "position:absolute;left:-11111px;border-collapse:separate"),
                                (t.style.cssText = "border:1px solid"),
                                (t.style.height = "1px"),
                                (n.style.height = "9px"),
                                (n.style.display = "block"),
                                re.appendChild(e).appendChild(t).appendChild(n),
                                (r = C.getComputedStyle(t)),
                                (a = parseInt(r.height, 10) + parseInt(r.borderTopWidth, 10) + parseInt(r.borderBottomWidth, 10) === t.offsetHeight),
                                re.removeChild(e)),
                            a
                        );
                    },
                }));
        })();
        var ze = ["Webkit", "Moz", "ms"],
            Ue = E.createElement("div").style,
            Xe = {};
        function Ve(e) {
            var t = S.cssProps[e] || Xe[e];
            return (
                t ||
                (e in Ue
                    ? e
                    : (Xe[e] =
                          (function (e) {
                              var t = e[0].toUpperCase() + e.slice(1),
                                  n = ze.length;
                              while (n--) if ((e = ze[n] + t) in Ue) return e;
                          })(e) || e))
            );
        }
        var Ge = /^(none|table(?!-c[ea]).+)/,
            Ye = { position: "absolute", visibility: "hidden", display: "block" },
            Qe = { letterSpacing: "0", fontWeight: "400" };
        function Je(e, t, n) {
            var r = te.exec(t);
            return r ? Math.max(0, r[2] - (n || 0)) + (r[3] || "px") : t;
        }
        function Ke(e, t, n, r, i, o) {
            var a = "width" === t ? 1 : 0,
                s = 0,
                u = 0;
            if (n === (r ? "border" : "content")) return 0;
            for (; a < 4; a += 2)
                "margin" === n && (u += S.css(e, n + ne[a], !0, i)),
                    r
                        ? ("content" === n && (u -= S.css(e, "padding" + ne[a], !0, i)), "margin" !== n && (u -= S.css(e, "border" + ne[a] + "Width", !0, i)))
                        : ((u += S.css(e, "padding" + ne[a], !0, i)), "padding" !== n ? (u += S.css(e, "border" + ne[a] + "Width", !0, i)) : (s += S.css(e, "border" + ne[a] + "Width", !0, i)));
            return !r && 0 <= o && (u += Math.max(0, Math.ceil(e["offset" + t[0].toUpperCase() + t.slice(1)] - o - u - s - 0.5)) || 0), u;
        }
        function Ze(e, t, n) {
            var r = Me(e),
                i = (!v.boxSizingReliable() || n) && "border-box" === S.css(e, "boxSizing", !1, r),
                o = i,
                a = Be(e, t, r),
                s = "offset" + t[0].toUpperCase() + t.slice(1);
            if (Pe.test(a)) {
                if (!n) return a;
                a = "auto";
            }
            return (
                ((!v.boxSizingReliable() && i) || (!v.reliableTrDimensions() && A(e, "tr")) || "auto" === a || (!parseFloat(a) && "inline" === S.css(e, "display", !1, r))) &&
                    e.getClientRects().length &&
                    ((i = "border-box" === S.css(e, "boxSizing", !1, r)), (o = s in e) && (a = e[s])),
                (a = parseFloat(a) || 0) + Ke(e, t, n || (i ? "border" : "content"), o, r, a) + "px"
            );
        }
        function et(e, t, n, r, i) {
            return new et.prototype.init(e, t, n, r, i);
        }
        S.extend({
            cssHooks: {
                opacity: {
                    get: function (e, t) {
                        if (t) {
                            var n = Be(e, "opacity");
                            return "" === n ? "1" : n;
                        }
                    },
                },
            },
            cssNumber: {
                animationIterationCount: !0,
                columnCount: !0,
                fillOpacity: !0,
                flexGrow: !0,
                flexShrink: !0,
                fontWeight: !0,
                gridArea: !0,
                gridColumn: !0,
                gridColumnEnd: !0,
                gridColumnStart: !0,
                gridRow: !0,
                gridRowEnd: !0,
                gridRowStart: !0,
                lineHeight: !0,
                opacity: !0,
                order: !0,
                orphans: !0,
                widows: !0,
                zIndex: !0,
                zoom: !0,
            },
            cssProps: {},
            style: function (e, t, n, r) {
                if (e && 3 !== e.nodeType && 8 !== e.nodeType && e.style) {
                    var i,
                        o,
                        a,
                        s = X(t),
                        u = Re.test(t),
                        l = e.style;
                    if ((u || (t = Ve(s)), (a = S.cssHooks[t] || S.cssHooks[s]), void 0 === n)) return a && "get" in a && void 0 !== (i = a.get(e, !1, r)) ? i : l[t];
                    "string" === (o = typeof n) && (i = te.exec(n)) && i[1] && ((n = se(e, t, i)), (o = "number")),
                        null != n &&
                            n == n &&
                            ("number" !== o || u || (n += (i && i[3]) || (S.cssNumber[s] ? "" : "px")),
                            v.clearCloneStyle || "" !== n || 0 !== t.indexOf("background") || (l[t] = "inherit"),
                            (a && "set" in a && void 0 === (n = a.set(e, n, r))) || (u ? l.setProperty(t, n) : (l[t] = n)));
                }
            },
            css: function (e, t, n, r) {
                var i,
                    o,
                    a,
                    s = X(t);
                return (
                    Re.test(t) || (t = Ve(s)),
                    (a = S.cssHooks[t] || S.cssHooks[s]) && "get" in a && (i = a.get(e, !0, n)),
                    void 0 === i && (i = Be(e, t, r)),
                    "normal" === i && t in Qe && (i = Qe[t]),
                    "" === n || n ? ((o = parseFloat(i)), !0 === n || isFinite(o) ? o || 0 : i) : i
                );
            },
        }),
            S.each(["height", "width"], function (e, u) {
                S.cssHooks[u] = {
                    get: function (e, t, n) {
                        if (t)
                            return !Ge.test(S.css(e, "display")) || (e.getClientRects().length && e.getBoundingClientRect().width)
                                ? Ze(e, u, n)
                                : Ie(e, Ye, function () {
                                      return Ze(e, u, n);
                                  });
                    },
                    set: function (e, t, n) {
                        var r,
                            i = Me(e),
                            o = !v.scrollboxSize() && "absolute" === i.position,
                            a = (o || n) && "border-box" === S.css(e, "boxSizing", !1, i),
                            s = n ? Ke(e, u, n, a, i) : 0;
                        return (
                            a && o && (s -= Math.ceil(e["offset" + u[0].toUpperCase() + u.slice(1)] - parseFloat(i[u]) - Ke(e, u, "border", !1, i) - 0.5)),
                            s && (r = te.exec(t)) && "px" !== (r[3] || "px") && ((e.style[u] = t), (t = S.css(e, u))),
                            Je(0, t, s)
                        );
                    },
                };
            }),
            (S.cssHooks.marginLeft = _e(v.reliableMarginLeft, function (e, t) {
                if (t)
                    return (
                        (parseFloat(Be(e, "marginLeft")) ||
                            e.getBoundingClientRect().left -
                                Ie(e, { marginLeft: 0 }, function () {
                                    return e.getBoundingClientRect().left;
                                })) + "px"
                    );
            })),
            S.each({ margin: "", padding: "", border: "Width" }, function (i, o) {
                (S.cssHooks[i + o] = {
                    expand: function (e) {
                        for (var t = 0, n = {}, r = "string" == typeof e ? e.split(" ") : [e]; t < 4; t++) n[i + ne[t] + o] = r[t] || r[t - 2] || r[0];
                        return n;
                    },
                }),
                    "margin" !== i && (S.cssHooks[i + o].set = Je);
            }),
            S.fn.extend({
                css: function (e, t) {
                    return B(
                        this,
                        function (e, t, n) {
                            var r,
                                i,
                                o = {},
                                a = 0;
                            if (Array.isArray(t)) {
                                for (r = Me(e), i = t.length; a < i; a++) o[t[a]] = S.css(e, t[a], !1, r);
                                return o;
                            }
                            return void 0 !== n ? S.style(e, t, n) : S.css(e, t);
                        },
                        e,
                        t,
                        1 < arguments.length
                    );
                },
            }),
            (((S.Tween = et).prototype = {
                constructor: et,
                init: function (e, t, n, r, i, o) {
                    (this.elem = e), (this.prop = n), (this.easing = i || S.easing._default), (this.options = t), (this.start = this.now = this.cur()), (this.end = r), (this.unit = o || (S.cssNumber[n] ? "" : "px"));
                },
                cur: function () {
                    var e = et.propHooks[this.prop];
                    return e && e.get ? e.get(this) : et.propHooks._default.get(this);
                },
                run: function (e) {
                    var t,
                        n = et.propHooks[this.prop];
                    return (
                        this.options.duration ? (this.pos = t = S.easing[this.easing](e, this.options.duration * e, 0, 1, this.options.duration)) : (this.pos = t = e),
                        (this.now = (this.end - this.start) * t + this.start),
                        this.options.step && this.options.step.call(this.elem, this.now, this),
                        n && n.set ? n.set(this) : et.propHooks._default.set(this),
                        this
                    );
                },
            }).init.prototype = et.prototype),
            ((et.propHooks = {
                _default: {
                    get: function (e) {
                        var t;
                        return 1 !== e.elem.nodeType || (null != e.elem[e.prop] && null == e.elem.style[e.prop]) ? e.elem[e.prop] : (t = S.css(e.elem, e.prop, "")) && "auto" !== t ? t : 0;
                    },
                    set: function (e) {
                        S.fx.step[e.prop] ? S.fx.step[e.prop](e) : 1 !== e.elem.nodeType || (!S.cssHooks[e.prop] && null == e.elem.style[Ve(e.prop)]) ? (e.elem[e.prop] = e.now) : S.style(e.elem, e.prop, e.now + e.unit);
                    },
                },
            }).scrollTop = et.propHooks.scrollLeft = {
                set: function (e) {
                    e.elem.nodeType && e.elem.parentNode && (e.elem[e.prop] = e.now);
                },
            }),
            (S.easing = {
                linear: function (e) {
                    return e;
                },
                swing: function (e) {
                    return 0.5 - Math.cos(e * Math.PI) / 2;
                },
                _default: "swing",
            }),
            (S.fx = et.prototype.init),
            (S.fx.step = {});
        var tt,
            nt,
            rt,
            it,
            ot = /^(?:toggle|show|hide)$/,
            at = /queueHooks$/;
        function st() {
            nt && (!1 === E.hidden && C.requestAnimationFrame ? C.requestAnimationFrame(st) : C.setTimeout(st, S.fx.interval), S.fx.tick());
        }
        function ut() {
            return (
                C.setTimeout(function () {
                    tt = void 0;
                }),
                (tt = Date.now())
            );
        }
        function lt(e, t) {
            var n,
                r = 0,
                i = { height: e };
            for (t = t ? 1 : 0; r < 4; r += 2 - t) i["margin" + (n = ne[r])] = i["padding" + n] = e;
            return t && (i.opacity = i.width = e), i;
        }
        function ct(e, t, n) {
            for (var r, i = (ft.tweeners[t] || []).concat(ft.tweeners["*"]), o = 0, a = i.length; o < a; o++) if ((r = i[o].call(n, t, e))) return r;
        }
        function ft(o, e, t) {
            var n,
                a,
                r = 0,
                i = ft.prefilters.length,
                s = S.Deferred().always(function () {
                    delete u.elem;
                }),
                u = function () {
                    if (a) return !1;
                    for (var e = tt || ut(), t = Math.max(0, l.startTime + l.duration - e), n = 1 - (t / l.duration || 0), r = 0, i = l.tweens.length; r < i; r++) l.tweens[r].run(n);
                    return s.notifyWith(o, [l, n, t]), n < 1 && i ? t : (i || s.notifyWith(o, [l, 1, 0]), s.resolveWith(o, [l]), !1);
                },
                l = s.promise({
                    elem: o,
                    props: S.extend({}, e),
                    opts: S.extend(!0, { specialEasing: {}, easing: S.easing._default }, t),
                    originalProperties: e,
                    originalOptions: t,
                    startTime: tt || ut(),
                    duration: t.duration,
                    tweens: [],
                    createTween: function (e, t) {
                        var n = S.Tween(o, l.opts, e, t, l.opts.specialEasing[e] || l.opts.easing);
                        return l.tweens.push(n), n;
                    },
                    stop: function (e) {
                        var t = 0,
                            n = e ? l.tweens.length : 0;
                        if (a) return this;
                        for (a = !0; t < n; t++) l.tweens[t].run(1);
                        return e ? (s.notifyWith(o, [l, 1, 0]), s.resolveWith(o, [l, e])) : s.rejectWith(o, [l, e]), this;
                    },
                }),
                c = l.props;
            for (
                !(function (e, t) {
                    var n, r, i, o, a;
                    for (n in e)
                        if (((i = t[(r = X(n))]), (o = e[n]), Array.isArray(o) && ((i = o[1]), (o = e[n] = o[0])), n !== r && ((e[r] = o), delete e[n]), (a = S.cssHooks[r]) && ("expand" in a)))
                            for (n in ((o = a.expand(o)), delete e[r], o)) (n in e) || ((e[n] = o[n]), (t[n] = i));
                        else t[r] = i;
                })(c, l.opts.specialEasing);
                r < i;
                r++
            )
                if ((n = ft.prefilters[r].call(l, o, c, l.opts))) return m(n.stop) && (S._queueHooks(l.elem, l.opts.queue).stop = n.stop.bind(n)), n;
            return (
                S.map(c, ct, l),
                m(l.opts.start) && l.opts.start.call(o, l),
                l.progress(l.opts.progress).done(l.opts.done, l.opts.complete).fail(l.opts.fail).always(l.opts.always),
                S.fx.timer(S.extend(u, { elem: o, anim: l, queue: l.opts.queue })),
                l
            );
        }
        (S.Animation = S.extend(ft, {
            tweeners: {
                "*": [
                    function (e, t) {
                        var n = this.createTween(e, t);
                        return se(n.elem, e, te.exec(t), n), n;
                    },
                ],
            },
            tweener: function (e, t) {
                m(e) ? ((t = e), (e = ["*"])) : (e = e.match(P));
                for (var n, r = 0, i = e.length; r < i; r++) (n = e[r]), (ft.tweeners[n] = ft.tweeners[n] || []), ft.tweeners[n].unshift(t);
            },
            prefilters: [
                function (e, t, n) {
                    var r,
                        i,
                        o,
                        a,
                        s,
                        u,
                        l,
                        c,
                        f = "width" in t || "height" in t,
                        p = this,
                        d = {},
                        h = e.style,
                        g = e.nodeType && ae(e),
                        y = Y.get(e, "fxshow");
                    for (r in (n.queue ||
                        (null == (a = S._queueHooks(e, "fx")).unqueued &&
                            ((a.unqueued = 0),
                            (s = a.empty.fire),
                            (a.empty.fire = function () {
                                a.unqueued || s();
                            })),
                        a.unqueued++,
                        p.always(function () {
                            p.always(function () {
                                a.unqueued--, S.queue(e, "fx").length || a.empty.fire();
                            });
                        })),
                    t))
                        if (((i = t[r]), ot.test(i))) {
                            if ((delete t[r], (o = o || "toggle" === i), i === (g ? "hide" : "show"))) {
                                if ("show" !== i || !y || void 0 === y[r]) continue;
                                g = !0;
                            }
                            d[r] = (y && y[r]) || S.style(e, r);
                        }
                    if ((u = !S.isEmptyObject(t)) || !S.isEmptyObject(d))
                        for (r in (f &&
                            1 === e.nodeType &&
                            ((n.overflow = [h.overflow, h.overflowX, h.overflowY]),
                            null == (l = y && y.display) && (l = Y.get(e, "display")),
                            "none" === (c = S.css(e, "display")) && (l ? (c = l) : (le([e], !0), (l = e.style.display || l), (c = S.css(e, "display")), le([e]))),
                            ("inline" === c || ("inline-block" === c && null != l)) &&
                                "none" === S.css(e, "float") &&
                                (u ||
                                    (p.done(function () {
                                        h.display = l;
                                    }),
                                    null == l && ((c = h.display), (l = "none" === c ? "" : c))),
                                (h.display = "inline-block"))),
                        n.overflow &&
                            ((h.overflow = "hidden"),
                            p.always(function () {
                                (h.overflow = n.overflow[0]), (h.overflowX = n.overflow[1]), (h.overflowY = n.overflow[2]);
                            })),
                        (u = !1),
                        d))
                            u ||
                                (y ? "hidden" in y && (g = y.hidden) : (y = Y.access(e, "fxshow", { display: l })),
                                o && (y.hidden = !g),
                                g && le([e], !0),
                                p.done(function () {
                                    for (r in (g || le([e]), Y.remove(e, "fxshow"), d)) S.style(e, r, d[r]);
                                })),
                                (u = ct(g ? y[r] : 0, r, p)),
                                r in y || ((y[r] = u.start), g && ((u.end = u.start), (u.start = 0)));
                },
            ],
            prefilter: function (e, t) {
                t ? ft.prefilters.unshift(e) : ft.prefilters.push(e);
            },
        })),
            (S.speed = function (e, t, n) {
                var r = e && "object" == typeof e ? S.extend({}, e) : { complete: n || (!n && t) || (m(e) && e), duration: e, easing: (n && t) || (t && !m(t) && t) };
                return (
                    S.fx.off ? (r.duration = 0) : "number" != typeof r.duration && (r.duration in S.fx.speeds ? (r.duration = S.fx.speeds[r.duration]) : (r.duration = S.fx.speeds._default)),
                    (null != r.queue && !0 !== r.queue) || (r.queue = "fx"),
                    (r.old = r.complete),
                    (r.complete = function () {
                        m(r.old) && r.old.call(this), r.queue && S.dequeue(this, r.queue);
                    }),
                    r
                );
            }),
            S.fn.extend({
                fadeTo: function (e, t, n, r) {
                    return this.filter(ae).css("opacity", 0).show().end().animate({ opacity: t }, e, n, r);
                },
                animate: function (t, e, n, r) {
                    var i = S.isEmptyObject(t),
                        o = S.speed(e, n, r),
                        a = function () {
                            var e = ft(this, S.extend({}, t), o);
                            (i || Y.get(this, "finish")) && e.stop(!0);
                        };
                    return (a.finish = a), i || !1 === o.queue ? this.each(a) : this.queue(o.queue, a);
                },
                stop: function (i, e, o) {
                    var a = function (e) {
                        var t = e.stop;
                        delete e.stop, t(o);
                    };
                    return (
                        "string" != typeof i && ((o = e), (e = i), (i = void 0)),
                        e && this.queue(i || "fx", []),
                        this.each(function () {
                            var e = !0,
                                t = null != i && i + "queueHooks",
                                n = S.timers,
                                r = Y.get(this);
                            if (t) r[t] && r[t].stop && a(r[t]);
                            else for (t in r) r[t] && r[t].stop && at.test(t) && a(r[t]);
                            for (t = n.length; t--; ) n[t].elem !== this || (null != i && n[t].queue !== i) || (n[t].anim.stop(o), (e = !1), n.splice(t, 1));
                            (!e && o) || S.dequeue(this, i);
                        })
                    );
                },
                finish: function (a) {
                    return (
                        !1 !== a && (a = a || "fx"),
                        this.each(function () {
                            var e,
                                t = Y.get(this),
                                n = t[a + "queue"],
                                r = t[a + "queueHooks"],
                                i = S.timers,
                                o = n ? n.length : 0;
                            for (t.finish = !0, S.queue(this, a, []), r && r.stop && r.stop.call(this, !0), e = i.length; e--; ) i[e].elem === this && i[e].queue === a && (i[e].anim.stop(!0), i.splice(e, 1));
                            for (e = 0; e < o; e++) n[e] && n[e].finish && n[e].finish.call(this);
                            delete t.finish;
                        })
                    );
                },
            }),
            S.each(["toggle", "show", "hide"], function (e, r) {
                var i = S.fn[r];
                S.fn[r] = function (e, t, n) {
                    return null == e || "boolean" == typeof e ? i.apply(this, arguments) : this.animate(lt(r, !0), e, t, n);
                };
            }),
            S.each({ slideDown: lt("show"), slideUp: lt("hide"), slideToggle: lt("toggle"), fadeIn: { opacity: "show" }, fadeOut: { opacity: "hide" }, fadeToggle: { opacity: "toggle" } }, function (e, r) {
                S.fn[e] = function (e, t, n) {
                    return this.animate(r, e, t, n);
                };
            }),
            (S.timers = []),
            (S.fx.tick = function () {
                var e,
                    t = 0,
                    n = S.timers;
                for (tt = Date.now(); t < n.length; t++) (e = n[t])() || n[t] !== e || n.splice(t--, 1);
                n.length || S.fx.stop(), (tt = void 0);
            }),
            (S.fx.timer = function (e) {
                S.timers.push(e), S.fx.start();
            }),
            (S.fx.interval = 13),
            (S.fx.start = function () {
                nt || ((nt = !0), st());
            }),
            (S.fx.stop = function () {
                nt = null;
            }),
            (S.fx.speeds = { slow: 600, fast: 200, _default: 400 }),
            (S.fn.delay = function (r, e) {
                return (
                    (r = (S.fx && S.fx.speeds[r]) || r),
                    (e = e || "fx"),
                    this.queue(e, function (e, t) {
                        var n = C.setTimeout(e, r);
                        t.stop = function () {
                            C.clearTimeout(n);
                        };
                    })
                );
            }),
            (rt = E.createElement("input")),
            (it = E.createElement("select").appendChild(E.createElement("option"))),
            (rt.type = "checkbox"),
            (v.checkOn = "" !== rt.value),
            (v.optSelected = it.selected),
            ((rt = E.createElement("input")).value = "t"),
            (rt.type = "radio"),
            (v.radioValue = "t" === rt.value);
        var pt,
            dt = S.expr.attrHandle;
        S.fn.extend({
            attr: function (e, t) {
                return B(this, S.attr, e, t, 1 < arguments.length);
            },
            removeAttr: function (e) {
                return this.each(function () {
                    S.removeAttr(this, e);
                });
            },
        }),
            S.extend({
                attr: function (e, t, n) {
                    var r,
                        i,
                        o = e.nodeType;
                    if (3 !== o && 8 !== o && 2 !== o)
                        return "undefined" == typeof e.getAttribute
                            ? S.prop(e, t, n)
                            : ((1 === o && S.isXMLDoc(e)) || (i = S.attrHooks[t.toLowerCase()] || (S.expr.match.bool.test(t) ? pt : void 0)),
                              void 0 !== n
                                  ? null === n
                                      ? void S.removeAttr(e, t)
                                      : i && "set" in i && void 0 !== (r = i.set(e, n, t))
                                      ? r
                                      : (e.setAttribute(t, n + ""), n)
                                  : i && "get" in i && null !== (r = i.get(e, t))
                                  ? r
                                  : null == (r = S.find.attr(e, t))
                                  ? void 0
                                  : r);
                },
                attrHooks: {
                    type: {
                        set: function (e, t) {
                            if (!v.radioValue && "radio" === t && A(e, "input")) {
                                var n = e.value;
                                return e.setAttribute("type", t), n && (e.value = n), t;
                            }
                        },
                    },
                },
                removeAttr: function (e, t) {
                    var n,
                        r = 0,
                        i = t && t.match(P);
                    if (i && 1 === e.nodeType) while ((n = i[r++])) e.removeAttribute(n);
                },
            }),
            (pt = {
                set: function (e, t, n) {
                    return !1 === t ? S.removeAttr(e, n) : e.setAttribute(n, n), n;
                },
            }),
            S.each(S.expr.match.bool.source.match(/\w+/g), function (e, t) {
                var a = dt[t] || S.find.attr;
                dt[t] = function (e, t, n) {
                    var r,
                        i,
                        o = t.toLowerCase();
                    return n || ((i = dt[o]), (dt[o] = r), (r = null != a(e, t, n) ? o : null), (dt[o] = i)), r;
                };
            });
        var ht = /^(?:input|select|textarea|button)$/i,
            gt = /^(?:a|area)$/i;
        function yt(e) {
            return (e.match(P) || []).join(" ");
        }
        function vt(e) {
            return (e.getAttribute && e.getAttribute("class")) || "";
        }
        function mt(e) {
            return Array.isArray(e) ? e : ("string" == typeof e && e.match(P)) || [];
        }
        S.fn.extend({
            prop: function (e, t) {
                return B(this, S.prop, e, t, 1 < arguments.length);
            },
            removeProp: function (e) {
                return this.each(function () {
                    delete this[S.propFix[e] || e];
                });
            },
        }),
            S.extend({
                prop: function (e, t, n) {
                    var r,
                        i,
                        o = e.nodeType;
                    if (3 !== o && 8 !== o && 2 !== o)
                        return (
                            (1 === o && S.isXMLDoc(e)) || ((t = S.propFix[t] || t), (i = S.propHooks[t])),
                            void 0 !== n ? (i && "set" in i && void 0 !== (r = i.set(e, n, t)) ? r : (e[t] = n)) : i && "get" in i && null !== (r = i.get(e, t)) ? r : e[t]
                        );
                },
                propHooks: {
                    tabIndex: {
                        get: function (e) {
                            var t = S.find.attr(e, "tabindex");
                            return t ? parseInt(t, 10) : ht.test(e.nodeName) || (gt.test(e.nodeName) && e.href) ? 0 : -1;
                        },
                    },
                },
                propFix: { for: "htmlFor", class: "className" },
            }),
            v.optSelected ||
                (S.propHooks.selected = {
                    get: function (e) {
                        var t = e.parentNode;
                        return t && t.parentNode && t.parentNode.selectedIndex, null;
                    },
                    set: function (e) {
                        var t = e.parentNode;
                        t && (t.selectedIndex, t.parentNode && t.parentNode.selectedIndex);
                    },
                }),
            S.each(["tabIndex", "readOnly", "maxLength", "cellSpacing", "cellPadding", "rowSpan", "colSpan", "useMap", "frameBorder", "contentEditable"], function () {
                S.propFix[this.toLowerCase()] = this;
            }),
            S.fn.extend({
                addClass: function (t) {
                    var e, n, r, i, o, a;
                    return m(t)
                        ? this.each(function (e) {
                              S(this).addClass(t.call(this, e, vt(this)));
                          })
                        : (e = mt(t)).length
                        ? this.each(function () {
                              if (((r = vt(this)), (n = 1 === this.nodeType && " " + yt(r) + " "))) {
                                  for (o = 0; o < e.length; o++) (i = e[o]), n.indexOf(" " + i + " ") < 0 && (n += i + " ");
                                  (a = yt(n)), r !== a && this.setAttribute("class", a);
                              }
                          })
                        : this;
                },
                removeClass: function (t) {
                    var e, n, r, i, o, a;
                    return m(t)
                        ? this.each(function (e) {
                              S(this).removeClass(t.call(this, e, vt(this)));
                          })
                        : arguments.length
                        ? (e = mt(t)).length
                            ? this.each(function () {
                                  if (((r = vt(this)), (n = 1 === this.nodeType && " " + yt(r) + " "))) {
                                      for (o = 0; o < e.length; o++) {
                                          i = e[o];
                                          while (-1 < n.indexOf(" " + i + " ")) n = n.replace(" " + i + " ", " ");
                                      }
                                      (a = yt(n)), r !== a && this.setAttribute("class", a);
                                  }
                              })
                            : this
                        : this.attr("class", "");
                },
                toggleClass: function (t, n) {
                    var e,
                        r,
                        i,
                        o,
                        a = typeof t,
                        s = "string" === a || Array.isArray(t);
                    return m(t)
                        ? this.each(function (e) {
                              S(this).toggleClass(t.call(this, e, vt(this), n), n);
                          })
                        : "boolean" == typeof n && s
                        ? n
                            ? this.addClass(t)
                            : this.removeClass(t)
                        : ((e = mt(t)),
                          this.each(function () {
                              if (s) for (o = S(this), i = 0; i < e.length; i++) (r = e[i]), o.hasClass(r) ? o.removeClass(r) : o.addClass(r);
                              else (void 0 !== t && "boolean" !== a) || ((r = vt(this)) && Y.set(this, "__className__", r), this.setAttribute && this.setAttribute("class", r || !1 === t ? "" : Y.get(this, "__className__") || ""));
                          }));
                },
                hasClass: function (e) {
                    var t,
                        n,
                        r = 0;
                    t = " " + e + " ";
                    while ((n = this[r++])) if (1 === n.nodeType && -1 < (" " + yt(vt(n)) + " ").indexOf(t)) return !0;
                    return !1;
                },
            });
        var xt = /\r/g;
        S.fn.extend({
            val: function (n) {
                var r,
                    e,
                    i,
                    t = this[0];
                return arguments.length
                    ? ((i = m(n)),
                      this.each(function (e) {
                          var t;
                          1 === this.nodeType &&
                              (null == (t = i ? n.call(this, e, S(this).val()) : n)
                                  ? (t = "")
                                  : "number" == typeof t
                                  ? (t += "")
                                  : Array.isArray(t) &&
                                    (t = S.map(t, function (e) {
                                        return null == e ? "" : e + "";
                                    })),
                              ((r = S.valHooks[this.type] || S.valHooks[this.nodeName.toLowerCase()]) && "set" in r && void 0 !== r.set(this, t, "value")) || (this.value = t));
                      }))
                    : t
                    ? (r = S.valHooks[t.type] || S.valHooks[t.nodeName.toLowerCase()]) && "get" in r && void 0 !== (e = r.get(t, "value"))
                        ? e
                        : "string" == typeof (e = t.value)
                        ? e.replace(xt, "")
                        : null == e
                        ? ""
                        : e
                    : void 0;
            },
        }),
            S.extend({
                valHooks: {
                    option: {
                        get: function (e) {
                            var t = S.find.attr(e, "value");
                            return null != t ? t : yt(S.text(e));
                        },
                    },
                    select: {
                        get: function (e) {
                            var t,
                                n,
                                r,
                                i = e.options,
                                o = e.selectedIndex,
                                a = "select-one" === e.type,
                                s = a ? null : [],
                                u = a ? o + 1 : i.length;
                            for (r = o < 0 ? u : a ? o : 0; r < u; r++)
                                if (((n = i[r]).selected || r === o) && !n.disabled && (!n.parentNode.disabled || !A(n.parentNode, "optgroup"))) {
                                    if (((t = S(n).val()), a)) return t;
                                    s.push(t);
                                }
                            return s;
                        },
                        set: function (e, t) {
                            var n,
                                r,
                                i = e.options,
                                o = S.makeArray(t),
                                a = i.length;
                            while (a--) ((r = i[a]).selected = -1 < S.inArray(S.valHooks.option.get(r), o)) && (n = !0);
                            return n || (e.selectedIndex = -1), o;
                        },
                    },
                },
            }),
            S.each(["radio", "checkbox"], function () {
                (S.valHooks[this] = {
                    set: function (e, t) {
                        if (Array.isArray(t)) return (e.checked = -1 < S.inArray(S(e).val(), t));
                    },
                }),
                    v.checkOn ||
                        (S.valHooks[this].get = function (e) {
                            return null === e.getAttribute("value") ? "on" : e.value;
                        });
            }),
            (v.focusin = "onfocusin" in C);
        var bt = /^(?:focusinfocus|focusoutblur)$/,
            wt = function (e) {
                e.stopPropagation();
            };
        S.extend(S.event, {
            trigger: function (e, t, n, r) {
                var i,
                    o,
                    a,
                    s,
                    u,
                    l,
                    c,
                    f,
                    p = [n || E],
                    d = y.call(e, "type") ? e.type : e,
                    h = y.call(e, "namespace") ? e.namespace.split(".") : [];
                if (
                    ((o = f = a = n = n || E),
                    3 !== n.nodeType &&
                        8 !== n.nodeType &&
                        !bt.test(d + S.event.triggered) &&
                        (-1 < d.indexOf(".") && ((d = (h = d.split(".")).shift()), h.sort()),
                        (u = d.indexOf(":") < 0 && "on" + d),
                        ((e = e[S.expando] ? e : new S.Event(d, "object" == typeof e && e)).isTrigger = r ? 2 : 3),
                        (e.namespace = h.join(".")),
                        (e.rnamespace = e.namespace ? new RegExp("(^|\\.)" + h.join("\\.(?:.*\\.|)") + "(\\.|$)") : null),
                        (e.result = void 0),
                        e.target || (e.target = n),
                        (t = null == t ? [e] : S.makeArray(t, [e])),
                        (c = S.event.special[d] || {}),
                        r || !c.trigger || !1 !== c.trigger.apply(n, t)))
                ) {
                    if (!r && !c.noBubble && !x(n)) {
                        for (s = c.delegateType || d, bt.test(s + d) || (o = o.parentNode); o; o = o.parentNode) p.push(o), (a = o);
                        a === (n.ownerDocument || E) && p.push(a.defaultView || a.parentWindow || C);
                    }
                    i = 0;
                    while ((o = p[i++]) && !e.isPropagationStopped())
                        (f = o),
                            (e.type = 1 < i ? s : c.bindType || d),
                            (l = (Y.get(o, "events") || Object.create(null))[e.type] && Y.get(o, "handle")) && l.apply(o, t),
                            (l = u && o[u]) && l.apply && V(o) && ((e.result = l.apply(o, t)), !1 === e.result && e.preventDefault());
                    return (
                        (e.type = d),
                        r ||
                            e.isDefaultPrevented() ||
                            (c._default && !1 !== c._default.apply(p.pop(), t)) ||
                            !V(n) ||
                            (u &&
                                m(n[d]) &&
                                !x(n) &&
                                ((a = n[u]) && (n[u] = null),
                                (S.event.triggered = d),
                                e.isPropagationStopped() && f.addEventListener(d, wt),
                                n[d](),
                                e.isPropagationStopped() && f.removeEventListener(d, wt),
                                (S.event.triggered = void 0),
                                a && (n[u] = a))),
                        e.result
                    );
                }
            },
            simulate: function (e, t, n) {
                var r = S.extend(new S.Event(), n, { type: e, isSimulated: !0 });
                S.event.trigger(r, null, t);
            },
        }),
            S.fn.extend({
                trigger: function (e, t) {
                    return this.each(function () {
                        S.event.trigger(e, t, this);
                    });
                },
                triggerHandler: function (e, t) {
                    var n = this[0];
                    if (n) return S.event.trigger(e, t, n, !0);
                },
            }),
            v.focusin ||
                S.each({ focus: "focusin", blur: "focusout" }, function (n, r) {
                    var i = function (e) {
                        S.event.simulate(r, e.target, S.event.fix(e));
                    };
                    S.event.special[r] = {
                        setup: function () {
                            var e = this.ownerDocument || this.document || this,
                                t = Y.access(e, r);
                            t || e.addEventListener(n, i, !0), Y.access(e, r, (t || 0) + 1);
                        },
                        teardown: function () {
                            var e = this.ownerDocument || this.document || this,
                                t = Y.access(e, r) - 1;
                            t ? Y.access(e, r, t) : (e.removeEventListener(n, i, !0), Y.remove(e, r));
                        },
                    };
                });
        var Tt = C.location,
            Ct = { guid: Date.now() },
            Et = /\?/;
        S.parseXML = function (e) {
            var t, n;
            if (!e || "string" != typeof e) return null;
            try {
                t = new C.DOMParser().parseFromString(e, "text/xml");
            } catch (e) {}
            return (
                (n = t && t.getElementsByTagName("parsererror")[0]),
                (t && !n) ||
                    S.error(
                        "Invalid XML: " +
                            (n
                                ? S.map(n.childNodes, function (e) {
                                      return e.textContent;
                                  }).join("\n")
                                : e)
                    ),
                t
            );
        };
        var St = /\[\]$/,
            kt = /\r?\n/g,
            At = /^(?:submit|button|image|reset|file)$/i,
            Nt = /^(?:input|select|textarea|keygen)/i;
        function jt(n, e, r, i) {
            var t;
            if (Array.isArray(e))
                S.each(e, function (e, t) {
                    r || St.test(n) ? i(n, t) : jt(n + "[" + ("object" == typeof t && null != t ? e : "") + "]", t, r, i);
                });
            else if (r || "object" !== w(e)) i(n, e);
            else for (t in e) jt(n + "[" + t + "]", e[t], r, i);
        }
        (S.param = function (e, t) {
            var n,
                r = [],
                i = function (e, t) {
                    var n = m(t) ? t() : t;
                    r[r.length] = encodeURIComponent(e) + "=" + encodeURIComponent(null == n ? "" : n);
                };
            if (null == e) return "";
            if (Array.isArray(e) || (e.jquery && !S.isPlainObject(e)))
                S.each(e, function () {
                    i(this.name, this.value);
                });
            else for (n in e) jt(n, e[n], t, i);
            return r.join("&");
        }),
            S.fn.extend({
                serialize: function () {
                    return S.param(this.serializeArray());
                },
                serializeArray: function () {
                    return this.map(function () {
                        var e = S.prop(this, "elements");
                        return e ? S.makeArray(e) : this;
                    })
                        .filter(function () {
                            var e = this.type;
                            return this.name && !S(this).is(":disabled") && Nt.test(this.nodeName) && !At.test(e) && (this.checked || !pe.test(e));
                        })
                        .map(function (e, t) {
                            var n = S(this).val();
                            return null == n
                                ? null
                                : Array.isArray(n)
                                ? S.map(n, function (e) {
                                      return { name: t.name, value: e.replace(kt, "\r\n") };
                                  })
                                : { name: t.name, value: n.replace(kt, "\r\n") };
                        })
                        .get();
                },
            });
        var Dt = /%20/g,
            qt = /#.*$/,
            Lt = /([?&])_=[^&]*/,
            Ht = /^(.*?):[ \t]*([^\r\n]*)$/gm,
            Ot = /^(?:GET|HEAD)$/,
            Pt = /^\/\//,
            Rt = {},
            Mt = {},
            It = "*/".concat("*"),
            Wt = E.createElement("a");
        function Ft(o) {
            return function (e, t) {
                "string" != typeof e && ((t = e), (e = "*"));
                var n,
                    r = 0,
                    i = e.toLowerCase().match(P) || [];
                if (m(t)) while ((n = i[r++])) "+" === n[0] ? ((n = n.slice(1) || "*"), (o[n] = o[n] || []).unshift(t)) : (o[n] = o[n] || []).push(t);
            };
        }
        function $t(t, i, o, a) {
            var s = {},
                u = t === Mt;
            function l(e) {
                var r;
                return (
                    (s[e] = !0),
                    S.each(t[e] || [], function (e, t) {
                        var n = t(i, o, a);
                        return "string" != typeof n || u || s[n] ? (u ? !(r = n) : void 0) : (i.dataTypes.unshift(n), l(n), !1);
                    }),
                    r
                );
            }
            return l(i.dataTypes[0]) || (!s["*"] && l("*"));
        }
        function Bt(e, t) {
            var n,
                r,
                i = S.ajaxSettings.flatOptions || {};
            for (n in t) void 0 !== t[n] && ((i[n] ? e : r || (r = {}))[n] = t[n]);
            return r && S.extend(!0, e, r), e;
        }
        (Wt.href = Tt.href),
            S.extend({
                active: 0,
                lastModified: {},
                etag: {},
                ajaxSettings: {
                    url: Tt.href,
                    type: "GET",
                    isLocal: /^(?:about|app|app-storage|.+-extension|file|res|widget):$/.test(Tt.protocol),
                    global: !0,
                    processData: !0,
                    async: !0,
                    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                    accepts: { "*": It, text: "text/plain", html: "text/html", xml: "application/xml, text/xml", json: "application/json, text/javascript" },
                    contents: { xml: /\bxml\b/, html: /\bhtml/, json: /\bjson\b/ },
                    responseFields: { xml: "responseXML", text: "responseText", json: "responseJSON" },
                    converters: { "* text": String, "text html": !0, "text json": JSON.parse, "text xml": S.parseXML },
                    flatOptions: { url: !0, context: !0 },
                },
                ajaxSetup: function (e, t) {
                    return t ? Bt(Bt(e, S.ajaxSettings), t) : Bt(S.ajaxSettings, e);
                },
                ajaxPrefilter: Ft(Rt),
                ajaxTransport: Ft(Mt),
                ajax: function (e, t) {
                    "object" == typeof e && ((t = e), (e = void 0)), (t = t || {});
                    var c,
                        f,
                        p,
                        n,
                        d,
                        r,
                        h,
                        g,
                        i,
                        o,
                        y = S.ajaxSetup({}, t),
                        v = y.context || y,
                        m = y.context && (v.nodeType || v.jquery) ? S(v) : S.event,
                        x = S.Deferred(),
                        b = S.Callbacks("once memory"),
                        w = y.statusCode || {},
                        a = {},
                        s = {},
                        u = "canceled",
                        T = {
                            readyState: 0,
                            getResponseHeader: function (e) {
                                var t;
                                if (h) {
                                    if (!n) {
                                        n = {};
                                        while ((t = Ht.exec(p))) n[t[1].toLowerCase() + " "] = (n[t[1].toLowerCase() + " "] || []).concat(t[2]);
                                    }
                                    t = n[e.toLowerCase() + " "];
                                }
                                return null == t ? null : t.join(", ");
                            },
                            getAllResponseHeaders: function () {
                                return h ? p : null;
                            },
                            setRequestHeader: function (e, t) {
                                return null == h && ((e = s[e.toLowerCase()] = s[e.toLowerCase()] || e), (a[e] = t)), this;
                            },
                            overrideMimeType: function (e) {
                                return null == h && (y.mimeType = e), this;
                            },
                            statusCode: function (e) {
                                var t;
                                if (e)
                                    if (h) T.always(e[T.status]);
                                    else for (t in e) w[t] = [w[t], e[t]];
                                return this;
                            },
                            abort: function (e) {
                                var t = e || u;
                                return c && c.abort(t), l(0, t), this;
                            },
                        };
                    if (
                        (x.promise(T),
                        (y.url = ((e || y.url || Tt.href) + "").replace(Pt, Tt.protocol + "//")),
                        (y.type = t.method || t.type || y.method || y.type),
                        (y.dataTypes = (y.dataType || "*").toLowerCase().match(P) || [""]),
                        null == y.crossDomain)
                    ) {
                        r = E.createElement("a");
                        try {
                            (r.href = y.url), (r.href = r.href), (y.crossDomain = Wt.protocol + "//" + Wt.host != r.protocol + "//" + r.host);
                        } catch (e) {
                            y.crossDomain = !0;
                        }
                    }
                    if ((y.data && y.processData && "string" != typeof y.data && (y.data = S.param(y.data, y.traditional)), $t(Rt, y, t, T), h)) return T;
                    for (i in ((g = S.event && y.global) && 0 == S.active++ && S.event.trigger("ajaxStart"),
                    (y.type = y.type.toUpperCase()),
                    (y.hasContent = !Ot.test(y.type)),
                    (f = y.url.replace(qt, "")),
                    y.hasContent
                        ? y.data && y.processData && 0 === (y.contentType || "").indexOf("application/x-www-form-urlencoded") && (y.data = y.data.replace(Dt, "+"))
                        : ((o = y.url.slice(f.length)),
                          y.data && (y.processData || "string" == typeof y.data) && ((f += (Et.test(f) ? "&" : "?") + y.data), delete y.data),
                          !1 === y.cache && ((f = f.replace(Lt, "$1")), (o = (Et.test(f) ? "&" : "?") + "_=" + Ct.guid++ + o)),
                          (y.url = f + o)),
                    y.ifModified && (S.lastModified[f] && T.setRequestHeader("If-Modified-Since", S.lastModified[f]), S.etag[f] && T.setRequestHeader("If-None-Match", S.etag[f])),
                    ((y.data && y.hasContent && !1 !== y.contentType) || t.contentType) && T.setRequestHeader("Content-Type", y.contentType),
                    T.setRequestHeader("Accept", y.dataTypes[0] && y.accepts[y.dataTypes[0]] ? y.accepts[y.dataTypes[0]] + ("*" !== y.dataTypes[0] ? ", " + It + "; q=0.01" : "") : y.accepts["*"]),
                    y.headers))
                        T.setRequestHeader(i, y.headers[i]);
                    if (y.beforeSend && (!1 === y.beforeSend.call(v, T, y) || h)) return T.abort();
                    if (((u = "abort"), b.add(y.complete), T.done(y.success), T.fail(y.error), (c = $t(Mt, y, t, T)))) {
                        if (((T.readyState = 1), g && m.trigger("ajaxSend", [T, y]), h)) return T;
                        y.async &&
                            0 < y.timeout &&
                            (d = C.setTimeout(function () {
                                T.abort("timeout");
                            }, y.timeout));
                        try {
                            (h = !1), c.send(a, l);
                        } catch (e) {
                            if (h) throw e;
                            l(-1, e);
                        }
                    } else l(-1, "No Transport");
                    function l(e, t, n, r) {
                        var i,
                            o,
                            a,
                            s,
                            u,
                            l = t;
                        h ||
                            ((h = !0),
                            d && C.clearTimeout(d),
                            (c = void 0),
                            (p = r || ""),
                            (T.readyState = 0 < e ? 4 : 0),
                            (i = (200 <= e && e < 300) || 304 === e),
                            n &&
                                (s = (function (e, t, n) {
                                    var r,
                                        i,
                                        o,
                                        a,
                                        s = e.contents,
                                        u = e.dataTypes;
                                    while ("*" === u[0]) u.shift(), void 0 === r && (r = e.mimeType || t.getResponseHeader("Content-Type"));
                                    if (r)
                                        for (i in s)
                                            if (s[i] && s[i].test(r)) {
                                                u.unshift(i);
                                                break;
                                            }
                                    if (u[0] in n) o = u[0];
                                    else {
                                        for (i in n) {
                                            if (!u[0] || e.converters[i + " " + u[0]]) {
                                                o = i;
                                                break;
                                            }
                                            a || (a = i);
                                        }
                                        o = o || a;
                                    }
                                    if (o) return o !== u[0] && u.unshift(o), n[o];
                                })(y, T, n)),
                            !i && -1 < S.inArray("script", y.dataTypes) && S.inArray("json", y.dataTypes) < 0 && (y.converters["text script"] = function () {}),
                            (s = (function (e, t, n, r) {
                                var i,
                                    o,
                                    a,
                                    s,
                                    u,
                                    l = {},
                                    c = e.dataTypes.slice();
                                if (c[1]) for (a in e.converters) l[a.toLowerCase()] = e.converters[a];
                                o = c.shift();
                                while (o)
                                    if ((e.responseFields[o] && (n[e.responseFields[o]] = t), !u && r && e.dataFilter && (t = e.dataFilter(t, e.dataType)), (u = o), (o = c.shift())))
                                        if ("*" === o) o = u;
                                        else if ("*" !== u && u !== o) {
                                            if (!(a = l[u + " " + o] || l["* " + o]))
                                                for (i in l)
                                                    if ((s = i.split(" "))[1] === o && (a = l[u + " " + s[0]] || l["* " + s[0]])) {
                                                        !0 === a ? (a = l[i]) : !0 !== l[i] && ((o = s[0]), c.unshift(s[1]));
                                                        break;
                                                    }
                                            if (!0 !== a)
                                                if (a && e["throws"]) t = a(t);
                                                else
                                                    try {
                                                        t = a(t);
                                                    } catch (e) {
                                                        return { state: "parsererror", error: a ? e : "No conversion from " + u + " to " + o };
                                                    }
                                        }
                                return { state: "success", data: t };
                            })(y, s, T, i)),
                            i
                                ? (y.ifModified && ((u = T.getResponseHeader("Last-Modified")) && (S.lastModified[f] = u), (u = T.getResponseHeader("etag")) && (S.etag[f] = u)),
                                  204 === e || "HEAD" === y.type ? (l = "nocontent") : 304 === e ? (l = "notmodified") : ((l = s.state), (o = s.data), (i = !(a = s.error))))
                                : ((a = l), (!e && l) || ((l = "error"), e < 0 && (e = 0))),
                            (T.status = e),
                            (T.statusText = (t || l) + ""),
                            i ? x.resolveWith(v, [o, l, T]) : x.rejectWith(v, [T, l, a]),
                            T.statusCode(w),
                            (w = void 0),
                            g && m.trigger(i ? "ajaxSuccess" : "ajaxError", [T, y, i ? o : a]),
                            b.fireWith(v, [T, l]),
                            g && (m.trigger("ajaxComplete", [T, y]), --S.active || S.event.trigger("ajaxStop")));
                    }
                    return T;
                },
                getJSON: function (e, t, n) {
                    return S.get(e, t, n, "json");
                },
                getScript: function (e, t) {
                    return S.get(e, void 0, t, "script");
                },
            }),
            S.each(["get", "post"], function (e, i) {
                S[i] = function (e, t, n, r) {
                    return m(t) && ((r = r || n), (n = t), (t = void 0)), S.ajax(S.extend({ url: e, type: i, dataType: r, data: t, success: n }, S.isPlainObject(e) && e));
                };
            }),
            S.ajaxPrefilter(function (e) {
                var t;
                for (t in e.headers) "content-type" === t.toLowerCase() && (e.contentType = e.headers[t] || "");
            }),
            (S._evalUrl = function (e, t, n) {
                return S.ajax({
                    url: e,
                    type: "GET",
                    dataType: "script",
                    cache: !0,
                    async: !1,
                    global: !1,
                    converters: { "text script": function () {} },
                    dataFilter: function (e) {
                        S.globalEval(e, t, n);
                    },
                });
            }),
            S.fn.extend({
                wrapAll: function (e) {
                    var t;
                    return (
                        this[0] &&
                            (m(e) && (e = e.call(this[0])),
                            (t = S(e, this[0].ownerDocument).eq(0).clone(!0)),
                            this[0].parentNode && t.insertBefore(this[0]),
                            t
                                .map(function () {
                                    var e = this;
                                    while (e.firstElementChild) e = e.firstElementChild;
                                    return e;
                                })
                                .append(this)),
                        this
                    );
                },
                wrapInner: function (n) {
                    return m(n)
                        ? this.each(function (e) {
                              S(this).wrapInner(n.call(this, e));
                          })
                        : this.each(function () {
                              var e = S(this),
                                  t = e.contents();
                              t.length ? t.wrapAll(n) : e.append(n);
                          });
                },
                wrap: function (t) {
                    var n = m(t);
                    return this.each(function (e) {
                        S(this).wrapAll(n ? t.call(this, e) : t);
                    });
                },
                unwrap: function (e) {
                    return (
                        this.parent(e)
                            .not("body")
                            .each(function () {
                                S(this).replaceWith(this.childNodes);
                            }),
                        this
                    );
                },
            }),
            (S.expr.pseudos.hidden = function (e) {
                return !S.expr.pseudos.visible(e);
            }),
            (S.expr.pseudos.visible = function (e) {
                return !!(e.offsetWidth || e.offsetHeight || e.getClientRects().length);
            }),
            (S.ajaxSettings.xhr = function () {
                try {
                    return new C.XMLHttpRequest();
                } catch (e) {}
            });
        var _t = { 0: 200, 1223: 204 },
            zt = S.ajaxSettings.xhr();
        (v.cors = !!zt && "withCredentials" in zt),
            (v.ajax = zt = !!zt),
            S.ajaxTransport(function (i) {
                var o, a;
                if (v.cors || (zt && !i.crossDomain))
                    return {
                        send: function (e, t) {
                            var n,
                                r = i.xhr();
                            if ((r.open(i.type, i.url, i.async, i.username, i.password), i.xhrFields)) for (n in i.xhrFields) r[n] = i.xhrFields[n];
                            for (n in (i.mimeType && r.overrideMimeType && r.overrideMimeType(i.mimeType), i.crossDomain || e["X-Requested-With"] || (e["X-Requested-With"] = "XMLHttpRequest"), e)) r.setRequestHeader(n, e[n]);
                            (o = function (e) {
                                return function () {
                                    o &&
                                        ((o = a = r.onload = r.onerror = r.onabort = r.ontimeout = r.onreadystatechange = null),
                                        "abort" === e
                                            ? r.abort()
                                            : "error" === e
                                            ? "number" != typeof r.status
                                                ? t(0, "error")
                                                : t(r.status, r.statusText)
                                            : t(_t[r.status] || r.status, r.statusText, "text" !== (r.responseType || "text") || "string" != typeof r.responseText ? { binary: r.response } : { text: r.responseText }, r.getAllResponseHeaders()));
                                };
                            }),
                                (r.onload = o()),
                                (a = r.onerror = r.ontimeout = o("error")),
                                void 0 !== r.onabort
                                    ? (r.onabort = a)
                                    : (r.onreadystatechange = function () {
                                          4 === r.readyState &&
                                              C.setTimeout(function () {
                                                  o && a();
                                              });
                                      }),
                                (o = o("abort"));
                            try {
                                r.send((i.hasContent && i.data) || null);
                            } catch (e) {
                                if (o) throw e;
                            }
                        },
                        abort: function () {
                            o && o();
                        },
                    };
            }),
            S.ajaxPrefilter(function (e) {
                e.crossDomain && (e.contents.script = !1);
            }),
            S.ajaxSetup({
                accepts: { script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript" },
                contents: { script: /\b(?:java|ecma)script\b/ },
                converters: {
                    "text script": function (e) {
                        return S.globalEval(e), e;
                    },
                },
            }),
            S.ajaxPrefilter("script", function (e) {
                void 0 === e.cache && (e.cache = !1), e.crossDomain && (e.type = "GET");
            }),
            S.ajaxTransport("script", function (n) {
                var r, i;
                if (n.crossDomain || n.scriptAttrs)
                    return {
                        send: function (e, t) {
                            (r = S("<script>")
                                .attr(n.scriptAttrs || {})
                                .prop({ charset: n.scriptCharset, src: n.url })
                                .on(
                                    "load error",
                                    (i = function (e) {
                                        r.remove(), (i = null), e && t("error" === e.type ? 404 : 200, e.type);
                                    })
                                )),
                                E.head.appendChild(r[0]);
                        },
                        abort: function () {
                            i && i();
                        },
                    };
            });
        var Ut,
            Xt = [],
            Vt = /(=)\?(?=&|$)|\?\?/;
        S.ajaxSetup({
            jsonp: "callback",
            jsonpCallback: function () {
                var e = Xt.pop() || S.expando + "_" + Ct.guid++;
                return (this[e] = !0), e;
            },
        }),
            S.ajaxPrefilter("json jsonp", function (e, t, n) {
                var r,
                    i,
                    o,
                    a = !1 !== e.jsonp && (Vt.test(e.url) ? "url" : "string" == typeof e.data && 0 === (e.contentType || "").indexOf("application/x-www-form-urlencoded") && Vt.test(e.data) && "data");
                if (a || "jsonp" === e.dataTypes[0])
                    return (
                        (r = e.jsonpCallback = m(e.jsonpCallback) ? e.jsonpCallback() : e.jsonpCallback),
                        a ? (e[a] = e[a].replace(Vt, "$1" + r)) : !1 !== e.jsonp && (e.url += (Et.test(e.url) ? "&" : "?") + e.jsonp + "=" + r),
                        (e.converters["script json"] = function () {
                            return o || S.error(r + " was not called"), o[0];
                        }),
                        (e.dataTypes[0] = "json"),
                        (i = C[r]),
                        (C[r] = function () {
                            o = arguments;
                        }),
                        n.always(function () {
                            void 0 === i ? S(C).removeProp(r) : (C[r] = i), e[r] && ((e.jsonpCallback = t.jsonpCallback), Xt.push(r)), o && m(i) && i(o[0]), (o = i = void 0);
                        }),
                        "script"
                    );
            }),
            (v.createHTMLDocument = (((Ut = E.implementation.createHTMLDocument("").body).innerHTML = "<form></form><form></form>"), 2 === Ut.childNodes.length)),
            (S.parseHTML = function (e, t, n) {
                return "string" != typeof e
                    ? []
                    : ("boolean" == typeof t && ((n = t), (t = !1)),
                      t || (v.createHTMLDocument ? (((r = (t = E.implementation.createHTMLDocument("")).createElement("base")).href = E.location.href), t.head.appendChild(r)) : (t = E)),
                      (o = !n && []),
                      (i = N.exec(e)) ? [t.createElement(i[1])] : ((i = xe([e], t, o)), o && o.length && S(o).remove(), S.merge([], i.childNodes)));
                var r, i, o;
            }),
            (S.fn.load = function (e, t, n) {
                var r,
                    i,
                    o,
                    a = this,
                    s = e.indexOf(" ");
                return (
                    -1 < s && ((r = yt(e.slice(s))), (e = e.slice(0, s))),
                    m(t) ? ((n = t), (t = void 0)) : t && "object" == typeof t && (i = "POST"),
                    0 < a.length &&
                        S.ajax({ url: e, type: i || "GET", dataType: "html", data: t })
                            .done(function (e) {
                                (o = arguments), a.html(r ? S("<div>").append(S.parseHTML(e)).find(r) : e);
                            })
                            .always(
                                n &&
                                    function (e, t) {
                                        a.each(function () {
                                            n.apply(this, o || [e.responseText, t, e]);
                                        });
                                    }
                            ),
                    this
                );
            }),
            (S.expr.pseudos.animated = function (t) {
                return S.grep(S.timers, function (e) {
                    return t === e.elem;
                }).length;
            }),
            (S.offset = {
                setOffset: function (e, t, n) {
                    var r,
                        i,
                        o,
                        a,
                        s,
                        u,
                        l = S.css(e, "position"),
                        c = S(e),
                        f = {};
                    "static" === l && (e.style.position = "relative"),
                        (s = c.offset()),
                        (o = S.css(e, "top")),
                        (u = S.css(e, "left")),
                        ("absolute" === l || "fixed" === l) && -1 < (o + u).indexOf("auto") ? ((a = (r = c.position()).top), (i = r.left)) : ((a = parseFloat(o) || 0), (i = parseFloat(u) || 0)),
                        m(t) && (t = t.call(e, n, S.extend({}, s))),
                        null != t.top && (f.top = t.top - s.top + a),
                        null != t.left && (f.left = t.left - s.left + i),
                        "using" in t ? t.using.call(e, f) : c.css(f);
                },
            }),
            S.fn.extend({
                offset: function (t) {
                    if (arguments.length)
                        return void 0 === t
                            ? this
                            : this.each(function (e) {
                                  S.offset.setOffset(this, t, e);
                              });
                    var e,
                        n,
                        r = this[0];
                    return r ? (r.getClientRects().length ? ((e = r.getBoundingClientRect()), (n = r.ownerDocument.defaultView), { top: e.top + n.pageYOffset, left: e.left + n.pageXOffset }) : { top: 0, left: 0 }) : void 0;
                },
                position: function () {
                    if (this[0]) {
                        var e,
                            t,
                            n,
                            r = this[0],
                            i = { top: 0, left: 0 };
                        if ("fixed" === S.css(r, "position")) t = r.getBoundingClientRect();
                        else {
                            (t = this.offset()), (n = r.ownerDocument), (e = r.offsetParent || n.documentElement);
                            while (e && (e === n.body || e === n.documentElement) && "static" === S.css(e, "position")) e = e.parentNode;
                            e && e !== r && 1 === e.nodeType && (((i = S(e).offset()).top += S.css(e, "borderTopWidth", !0)), (i.left += S.css(e, "borderLeftWidth", !0)));
                        }
                        return { top: t.top - i.top - S.css(r, "marginTop", !0), left: t.left - i.left - S.css(r, "marginLeft", !0) };
                    }
                },
                offsetParent: function () {
                    return this.map(function () {
                        var e = this.offsetParent;
                        while (e && "static" === S.css(e, "position")) e = e.offsetParent;
                        return e || re;
                    });
                },
            }),
            S.each({ scrollLeft: "pageXOffset", scrollTop: "pageYOffset" }, function (t, i) {
                var o = "pageYOffset" === i;
                S.fn[t] = function (e) {
                    return B(
                        this,
                        function (e, t, n) {
                            var r;
                            if ((x(e) ? (r = e) : 9 === e.nodeType && (r = e.defaultView), void 0 === n)) return r ? r[i] : e[t];
                            r ? r.scrollTo(o ? r.pageXOffset : n, o ? n : r.pageYOffset) : (e[t] = n);
                        },
                        t,
                        e,
                        arguments.length
                    );
                };
            }),
            S.each(["top", "left"], function (e, n) {
                S.cssHooks[n] = _e(v.pixelPosition, function (e, t) {
                    if (t) return (t = Be(e, n)), Pe.test(t) ? S(e).position()[n] + "px" : t;
                });
            }),
            S.each({ Height: "height", Width: "width" }, function (a, s) {
                S.each({ padding: "inner" + a, content: s, "": "outer" + a }, function (r, o) {
                    S.fn[o] = function (e, t) {
                        var n = arguments.length && (r || "boolean" != typeof e),
                            i = r || (!0 === e || !0 === t ? "margin" : "border");
                        return B(
                            this,
                            function (e, t, n) {
                                var r;
                                return x(e)
                                    ? 0 === o.indexOf("outer")
                                        ? e["inner" + a]
                                        : e.document.documentElement["client" + a]
                                    : 9 === e.nodeType
                                    ? ((r = e.documentElement), Math.max(e.body["scroll" + a], r["scroll" + a], e.body["offset" + a], r["offset" + a], r["client" + a]))
                                    : void 0 === n
                                    ? S.css(e, t, i)
                                    : S.style(e, t, n, i);
                            },
                            s,
                            n ? e : void 0,
                            n
                        );
                    };
                });
            }),
            S.each(["ajaxStart", "ajaxStop", "ajaxComplete", "ajaxError", "ajaxSuccess", "ajaxSend"], function (e, t) {
                S.fn[t] = function (e) {
                    return this.on(t, e);
                };
            }),
            S.fn.extend({
                bind: function (e, t, n) {
                    return this.on(e, null, t, n);
                },
                unbind: function (e, t) {
                    return this.off(e, null, t);
                },
                delegate: function (e, t, n, r) {
                    return this.on(t, e, n, r);
                },
                undelegate: function (e, t, n) {
                    return 1 === arguments.length ? this.off(e, "**") : this.off(t, e || "**", n);
                },
                hover: function (e, t) {
                    return this.mouseenter(e).mouseleave(t || e);
                },
            }),
            S.each("blur focus focusin focusout resize scroll click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup contextmenu".split(" "), function (e, n) {
                S.fn[n] = function (e, t) {
                    return 0 < arguments.length ? this.on(n, null, e, t) : this.trigger(n);
                };
            });
        var Gt = /^[\s\uFEFF\xA0]+|([^\s\uFEFF\xA0])[\s\uFEFF\xA0]+$/g;
        (S.proxy = function (e, t) {
            var n, r, i;
            if (("string" == typeof t && ((n = e[t]), (t = e), (e = n)), m(e)))
                return (
                    (r = s.call(arguments, 2)),
                    ((i = function () {
                        return e.apply(t || this, r.concat(s.call(arguments)));
                    }).guid = e.guid = e.guid || S.guid++),
                    i
                );
        }),
            (S.holdReady = function (e) {
                e ? S.readyWait++ : S.ready(!0);
            }),
            (S.isArray = Array.isArray),
            (S.parseJSON = JSON.parse),
            (S.nodeName = A),
            (S.isFunction = m),
            (S.isWindow = x),
            (S.camelCase = X),
            (S.type = w),
            (S.now = Date.now),
            (S.isNumeric = function (e) {
                var t = S.type(e);
                return ("number" === t || "string" === t) && !isNaN(e - parseFloat(e));
            }),
            (S.trim = function (e) {
                return null == e ? "" : (e + "").replace(Gt, "$1");
            }),
            "function" == typeof define &&
                define.amd &&
                define("jquery", [], function () {
                    return S;
                });
        var Yt = C.jQuery,
            Qt = C.$;
        return (
            (S.noConflict = function (e) {
                return C.$ === S && (C.$ = Qt), e && C.jQuery === S && (C.jQuery = Yt), S;
            }),
            "undefined" == typeof e && (C.jQuery = C.$ = S),
            S
        );
    });
  </script>

  <script>
    (function() {
      window.requestAnimFrame = (function(callback) {
        return window.requestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.mozRequestAnimationFrame ||
          window.oRequestAnimationFrame ||
          window.msRequestAnimaitonFrame ||
          function(callback) {
            window.setTimeout(callback, 1000 / 60);
          };
      })();

      var canvas = document.getElementById("sig-canvas");
      var ctx = canvas.getContext("2d");
      ctx.strokeStyle = "#222222";
      ctx.lineWidth = 1;

      var drawing = false;
      var mousePos = {
        x: 0,
        y: 0
      };
      var lastPos = mousePos;

      canvas.addEventListener("mousedown", function(e) {
        drawing = true;
        lastPos = getMousePos(canvas, e);
      }, false);

      canvas.addEventListener("mouseup", function(e) {
        drawing = false;
      }, false);

      canvas.addEventListener("mousemove", function(e) {
        mousePos = getMousePos(canvas, e);
      }, false);

      // Add touch event support for mobile
      canvas.addEventListener("touchstart", function(e) {

      }, false);

      canvas.addEventListener("touchmove", function(e) {
        var touch = e.touches[0];
        var me = new MouseEvent("mousemove", {
          clientX: touch.clientX,
          clientY: touch.clientY
        });
        canvas.dispatchEvent(me);
      }, false);

      canvas.addEventListener("touchstart", function(e) {
        mousePos = getTouchPos(canvas, e);
        var touch = e.touches[0];
        var me = new MouseEvent("mousedown", {
          clientX: touch.clientX,
          clientY: touch.clientY
        });
        canvas.dispatchEvent(me);
      }, false);

      canvas.addEventListener("touchend", function(e) {
        var me = new MouseEvent("mouseup", {});
        canvas.dispatchEvent(me);
      }, false);

      function getMousePos(canvasDom, mouseEvent) {
        var rect = canvasDom.getBoundingClientRect();
        return {
          x: mouseEvent.clientX - rect.left,
          y: mouseEvent.clientY - rect.top
        }
      }

      function getTouchPos(canvasDom, touchEvent) {
        var rect = canvasDom.getBoundingClientRect();
        return {
          x: touchEvent.touches[0].clientX - rect.left,
          y: touchEvent.touches[0].clientY - rect.top
        }
      }

      function renderCanvas() {
        if (drawing) {
          ctx.moveTo(lastPos.x, lastPos.y);
          ctx.lineTo(mousePos.x, mousePos.y);
          ctx.stroke();
          lastPos = mousePos;
        }
      }

      // Prevent scrolling when touching the canvas
      document.body.addEventListener("touchstart", function(e) {
        if (e.target == canvas) {
          e.preventDefault();
        }
      }, false);
      document.body.addEventListener("touchend", function(e) {
        if (e.target == canvas) {
          e.preventDefault();
        }
      }, false);
      document.body.addEventListener("touchmove", function(e) {
        if (e.target == canvas) {
          e.preventDefault();
        }
      }, false);

      (function drawLoop() {
        requestAnimFrame(drawLoop);
        renderCanvas();
      })();

      function clearCanvas() {
        canvas.width = canvas.width;
      }


      jQuery("#sig-clearBtn").click(function(e) {
e.preventDefault();
        clearCanvas();
        jQuery("#ztsa_agrmnt_reject").prop('disabled', false);
        jQuery("#ztsa_agrmnt_accept").prop('disabled', true)
        if ("<?php echo esc_attr($additional_entry_id); ?>" > 0) {
          jQuery("#coustomer_sign_<?php echo esc_attr($additional_entry_id); ?>").attr("src", "");
        } else {
          jQuery("#main_customer_sign").attr("src", "");
        }
      })
      jQuery("#sig-clearBtn1").click(function(e) {
e.preventDefault();
        clearCanvas();
      })
      jQuery("#sig-submitBtn-customer").click(function(e) {
        jQuery("#signature-customer").val(canvas.toDataURL());
        jQuery("#ztsa_agrmnt_accept").prop('disabled', false);
        jQuery("#ztsa_agrmnt_reject").prop('disabled', true)
        if ("<?php echo esc_attr($additional_entry_id); ?>" > 0) {
          jQuery("#coustomer_sign_<?php echo esc_attr($additional_entry_id); ?>").attr("src", canvas.toDataURL());
        } else {
          jQuery("#main_customer_sign").attr("src", canvas.toDataURL());
        }
      })
      jQuery("#sig-submitBtn-owner").click(function(e) {
        jQuery("#signature-owner").val(canvas.toDataURL());
      })
    })();
  </script>
</body>