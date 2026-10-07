<?php
/**
 * Admin Settings Page — inc/settings.php
 * Single settings page under Settings → Luxe Business Suite
 * Allows toggling each module on/off.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'lbs_settings_menu' );
add_action( 'admin_init', 'lbs_settings_init' );

function lbs_settings_menu() {
    add_options_page(
        __( 'Luxe Business Suite', 'luxe-business-suite' ),
        __( 'Luxe Business Suite', 'luxe-business-suite' ),
        'manage_options',
        'lbs-settings',
        'lbs_settings_page'
    );
}

function lbs_settings_init() {
    register_setting( 'lbs_settings_group', 'lbs_modules', array(
        'sanitize_callback' => 'lbs_sanitize_modules',
        'default'           => array_fill_keys( array_keys( lbs_modules() ), 1 ),
    ) );
    register_setting( 'lbs_settings_group', 'lbs_general', array(
        'sanitize_callback' => 'lbs_sanitize_general',
        'default'           => array(),
    ) );
}

function lbs_sanitize_modules( $input ) {
    $clean = array();
    foreach ( array_keys( lbs_modules() ) as $mod ) {
        $clean[ $mod ] = ! empty( $input[ $mod ] ) ? 1 : 0;
    }
    return $clean;
}

function lbs_sanitize_general( $input ) {
    return array(
        'studio_name'     => sanitize_text_field( $input['studio_name']     ?? '' ),
        'admin_email'     => sanitize_email(      $input['admin_email']     ?? '' ),
        'currency'        => sanitize_text_field( $input['currency']        ?? 'BDT' ),
        'booking_email'   => sanitize_email(      $input['booking_email']   ?? '' ),
        'portal_slug'     => sanitize_title(      $input['portal_slug']     ?? 'client-portal' ),
        'mailchimp_key'   => sanitize_text_field( $input['mailchimp_key']   ?? '' ),
        'mailchimp_list'  => sanitize_text_field( $input['mailchimp_list']  ?? '' ),
        'google_cal_id'   => sanitize_text_field( $input['google_cal_id']   ?? '' ),
    );
}

function lbs_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $mods    = get_option( 'lbs_modules', array_fill_keys( array_keys( lbs_modules() ), 1 ) );
    $general = get_option( 'lbs_general', array() );
    $saved   = isset( $_GET['settings-updated'] );
    ?>
    <div class="wrap lbs-settings-wrap">
        <h1>⬛ <?php _e( 'Luxe Business Suite', 'luxe-business-suite' ); ?></h1>
        <p style="color:#666;margin-bottom:2rem;"><?php _e( 'Toggle modules and configure global settings. Changes take effect after saving.', 'luxe-business-suite' ); ?></p>

        <?php if ( $saved ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e( 'Settings saved. Please re-save permalinks if you toggled the Booking or Portal module.', 'luxe-business-suite' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'lbs_settings_group' ); ?>

            <!-- MODULES -->
            <h2><?php _e( 'Modules', 'luxe-business-suite' ); ?></h2>
            <table class="form-table lbs-module-table">
                <?php foreach ( lbs_modules() as $key => $label ) :
                    $icons = array(
                        'crm'     => '👤',
                        'booking' => '📅',
                        'team'    => '👥',
                        'pricing' => '💰',
                        'journal' => '✏️',
                        'portal'  => '🔐',
                    );
                    $descriptions = array(
                        'crm'     => 'Saves every contact form enquiry as a CPT. Pipeline kanban, notes, CSV export.',
                        'booking' => 'Self-serve consultation booking with slots, approval workflow, and calendar sync.',
                        'team'    => 'CPT-driven team profiles, awards, press mentions, and studio timeline for the About page.',
                        'pricing' => 'Service packages CPT with features, FAQs, visibility toggles, and [lbs_packages] shortcode.',
                        'journal' => 'Adds reading time, post series, table of contents, AJAX search, and newsletter CTA.',
                        'portal'  => 'Password-protected client portal with project documents, mood boards, and updates.',
                    );
                ?>
                <tr>
                    <th scope="row">
                        <label for="lbs_module_<?php echo esc_attr( $key ); ?>">
                            <?php echo esc_html( $icons[ $key ] ?? '' ); ?> <?php echo esc_html( $label ); ?>
                        </label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox"
                                   id="lbs_module_<?php echo esc_attr( $key ); ?>"
                                   name="lbs_modules[<?php echo esc_attr( $key ); ?>]"
                                   value="1"
                                   <?php checked( $mods[ $key ] ?? 1, 1 ); ?>>
                            <strong><?php _e( 'Active', 'luxe-business-suite' ); ?></strong>
                        </label>
                        <p class="description"><?php echo esc_html( $descriptions[ $key ] ); ?></p>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <!-- GENERAL -->
            <h2 style="margin-top:2rem;"><?php _e( 'General Settings', 'luxe-business-suite' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="lbs_studio_name"><?php _e( 'Studio Name', 'luxe-business-suite' ); ?></label></th>
                    <td><input type="text" id="lbs_studio_name" name="lbs_general[studio_name]" value="<?php echo esc_attr( $general['studio_name'] ?? get_bloginfo('name') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="lbs_admin_email"><?php _e( 'Notification Email', 'luxe-business-suite' ); ?></label></th>
                    <td>
                        <input type="email" id="lbs_admin_email" name="lbs_general[admin_email]" value="<?php echo esc_attr( $general['admin_email'] ?? get_option('admin_email') ); ?>" class="regular-text">
                        <p class="description"><?php _e( 'Where new lead and booking notifications are sent.', 'luxe-business-suite' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="lbs_currency"><?php _e( 'Default Currency', 'luxe-business-suite' ); ?></label></th>
                    <td>
                        <select id="lbs_currency" name="lbs_general[currency]">
                            <?php foreach ( array( 'BDT' => 'BDT (৳)', 'USD' => 'USD ($)', 'GBP' => 'GBP (£)', 'EUR' => 'EUR (€)', 'AED' => 'AED (د.إ)' ) as $code => $label ) : ?>
                            <option value="<?php echo esc_attr( $code ); ?>" <?php selected( $general['currency'] ?? 'BDT', $code ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="lbs_portal_slug"><?php _e( 'Portal URL Slug', 'luxe-business-suite' ); ?></label></th>
                    <td>
                        <input type="text" id="lbs_portal_slug" name="lbs_general[portal_slug]" value="<?php echo esc_attr( $general['portal_slug'] ?? 'client-portal' ); ?>" class="regular-text">
                        <p class="description"><?php printf( __( 'Client portal will be at: %s', 'luxe-business-suite' ), '<code>' . home_url( '/' . ( $general['portal_slug'] ?? 'client-portal' ) ) . '</code>' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="lbs_mailchimp_key"><?php _e( 'Mailchimp API Key', 'luxe-business-suite' ); ?></label></th>
                    <td>
                        <input type="password" id="lbs_mailchimp_key" name="lbs_general[mailchimp_key]" value="<?php echo esc_attr( $general['mailchimp_key'] ?? '' ); ?>" class="regular-text" autocomplete="off">
                        <p class="description"><?php _e( 'Used by Journal newsletter sign-up. Leave blank to disable.', 'luxe-business-suite' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="lbs_mailchimp_list"><?php _e( 'Mailchimp List / Audience ID', 'luxe-business-suite' ); ?></label></th>
                    <td><input type="text" id="lbs_mailchimp_list" name="lbs_general[mailchimp_list]" value="<?php echo esc_attr( $general['mailchimp_list'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
            </table>

            <?php submit_button( __( 'Save Settings', 'luxe-business-suite' ) ); ?>
        </form>
    </div>

    <style>
    .lbs-settings-wrap h1 { margin-bottom: .5rem; }
    .lbs-module-table td label { font-weight: 600; }
    .lbs-module-table .description { margin-top: 4px; color: #757575; }
    </style>
    <?php
}
