<?php
/**
 * AJAX Handlers — inc/ajax.php
 * • lcc_calculate  — server-side cost computation
 * • lcc_save_lead  — save estimate as a lead/enquiry
 * • lcc_email_estimate — email estimate to client
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_ajax_lcc_calculate',        'lcc_ajax_calculate' );
add_action( 'wp_ajax_nopriv_lcc_calculate', 'lcc_ajax_calculate' );

add_action( 'wp_ajax_lcc_save_lead',        'lcc_ajax_save_lead' );
add_action( 'wp_ajax_nopriv_lcc_save_lead', 'lcc_ajax_save_lead' );

add_action( 'wp_ajax_lcc_email_estimate',        'lcc_ajax_email_estimate' );
add_action( 'wp_ajax_nopriv_lcc_email_estimate', 'lcc_ajax_email_estimate' );

/* ============================================================
   CORE CALCULATION FUNCTION (server-side)
============================================================ */
function lcc_compute_estimate( array $data ) : array {
    $rates     = lcc_get_all_rates();
    $settings  = get_option( 'lcc_settings', lcc_default_settings() );

    $tier_key    = sanitize_key( $data['tier']       ?? 'full_design' );
    $finish_key  = sanitize_key( $data['finish']     ?? 'standard' );
    $complexity  = sanitize_key( $data['complexity'] ?? 'simple' );
    $rooms       = $data['rooms'] ?? array();
    $addons      = $data['addons'] ?? array();

    $tier   = $rates['tiers'][ $tier_key ]   ?? $rates['tiers']['full_design'];
    $finish = $rates['finishes'][ $finish_key ] ?? $rates['finishes']['standard'];
    $compl  = $rates['complexity'][ $complexity ] ?? $rates['complexity']['simple'];

    $base_rate         = (float) $tier['base_rate'];
    $finish_multiplier = (float) $finish['multiplier'];
    $complexity_pct    = (float) $compl['surcharge'];

    $rooms_breakdown   = array();
    $subtotal          = 0;
    $total_area        = 0;

    // Per-room calculation
    foreach ( $rooms as $room ) {
        $room_key  = sanitize_key( $room['type'] ?? 'custom' );
        $area      = max( 0, (float) ( $room['area'] ?? 0 ) );
        $room_data = $rates['rooms'][ $room_key ] ?? $rates['rooms']['custom'];

        $room_rate     = $base_rate * $finish_multiplier * (float) $room_data['multiplier'];
        $room_base     = $area * $room_rate;
        $room_compl    = $room_base * ( $complexity_pct / 100 );
        $room_total    = $room_base + $room_compl;

        $rooms_breakdown[] = array(
            'type'        => $room_key,
            'label'       => $room_data['label'],
            'icon'        => $room_data['icon'],
            'area'        => $area,
            'rate'        => round( $room_rate ),
            'base'        => round( $room_base ),
            'complexity'  => round( $room_compl ),
            'total'       => round( $room_total ),
        );

        $subtotal    += $room_total;
        $total_area  += $area;
    }

    // Add-ons
    $addons_breakdown = array();
    $addons_total     = 0;

    foreach ( $addons as $addon_key ) {
        $addon_key = sanitize_key( $addon_key );
        if ( ! isset( $rates['addons'][ $addon_key ] ) ) continue;
        $addon  = $rates['addons'][ $addon_key ];
        $amount = $addon['type'] === 'percent'
            ? $subtotal * ( (float) $addon['value'] / 100 )
            : (float) $addon['value'];

        $addons_breakdown[] = array(
            'key'    => $addon_key,
            'label'  => $addon['label'],
            'icon'   => $addon['icon'],
            'type'   => $addon['type'],
            'value'  => $addon['value'],
            'amount' => round( $amount ),
        );
        $addons_total += $amount;
    }

    $before_vat  = $subtotal + $addons_total;
    $vat_rate    = (float) ( $settings['vat_rate'] ?? 15 );
    $vat_amount  = $settings['show_vat'] ? $before_vat * ( $vat_rate / 100 ) : 0;
    $grand_total = $before_vat + $vat_amount;

    // Range (±15%)
    $low_total  = $grand_total * 0.85;
    $high_total = $grand_total * 1.15;

    $min_budget = (float) ( $settings['min_budget'] ?? 200000 );

    return array(
        'tier'             => array( 'key' => $tier_key, 'label' => $tier['label'], 'icon' => $tier['icon'], 'description' => $tier['description'] ),
        'finish'           => array( 'key' => $finish_key, 'label' => $finish['label'], 'icon' => $finish['icon'] ),
        'complexity'       => array( 'key' => $complexity, 'label' => $compl['label'], 'surcharge' => $complexity_pct ),
        'total_area'       => $total_area,
        'rooms'            => $rooms_breakdown,
        'addons'           => $addons_breakdown,
        'subtotal'         => round( $subtotal ),
        'addons_total'     => round( $addons_total ),
        'before_vat'       => round( $before_vat ),
        'vat_rate'         => $vat_rate,
        'vat_amount'       => round( $vat_amount ),
        'grand_total'      => round( $grand_total ),
        'low_total'        => round( $low_total ),
        'high_total'       => round( $high_total ),
        'avg_rate'         => $total_area > 0 ? round( $grand_total / $total_area ) : 0,
        'below_minimum'    => $grand_total < $min_budget,
        'min_budget'       => $min_budget,
        'min_budget_msg'   => $settings['min_budget_msg'] ?? '',
        'disclaimer'       => $settings['disclaimer'] ?? '',
        'currency'         => lcc_get_option( 'currency', 'BDT' ),
        'symbol'           => lcc_currency_symbol(),
        'unit'             => lcc_get_option( 'area_unit', 'sqft' ),
    );
}

/* ============================================================
   AJAX: CALCULATE
============================================================ */
function lcc_ajax_calculate() {
    check_ajax_referer( 'lcc_nonce', 'nonce' );

    $data = array(
        'tier'       => sanitize_key(  $_POST['tier']       ?? 'full_design' ),
        'finish'     => sanitize_key(  $_POST['finish']     ?? 'standard' ),
        'complexity' => sanitize_key(  $_POST['complexity'] ?? 'simple' ),
        'rooms'      => array(),
        'addons'     => array(),
    );

    // Parse rooms
    $rooms_raw = $_POST['rooms'] ?? array();
    if ( is_array( $rooms_raw ) ) {
        foreach ( $rooms_raw as $room ) {
            $data['rooms'][] = array(
                'type' => sanitize_key( $room['type'] ?? 'custom' ),
                'area' => max( 0, (float) ( $room['area'] ?? 0 ) ),
            );
        }
    }

    // Parse add-ons
    $addons_raw = $_POST['addons'] ?? array();
    if ( is_array( $addons_raw ) ) {
        foreach ( $addons_raw as $addon ) {
            $data['addons'][] = sanitize_key( $addon );
        }
    }

    // Validate: at least one room with area
    $has_area = false;
    foreach ( $data['rooms'] as $r ) {
        if ( $r['area'] > 0 ) { $has_area = true; break; }
    }
    if ( ! $has_area ) {
        wp_send_json_error( array( 'message' => __( 'Please enter at least one room area.', 'luxe-cost-calculator' ) ) );
    }

    $result = lcc_compute_estimate( $data );
    wp_send_json_success( $result );
}

/* ============================================================
   AJAX: SAVE LEAD (estimate → CRM)
============================================================ */
function lcc_ajax_save_lead() {
    check_ajax_referer( 'lcc_nonce', 'nonce' );

    $name     = sanitize_text_field( $_POST['name']    ?? '' );
    $email    = sanitize_email(      $_POST['email']   ?? '' );
    $phone    = sanitize_text_field( $_POST['phone']   ?? '' );
    $estimate = sanitize_text_field( $_POST['estimate_total'] ?? '' );
    $tier     = sanitize_text_field( $_POST['tier']    ?? '' );
    $finish   = sanitize_text_field( $_POST['finish']  ?? '' );
    $area     = sanitize_text_field( $_POST['total_area'] ?? '' );

    if ( ! $name || ! is_email( $email ) ) {
        wp_send_json_error( __( 'Please enter your name and a valid email.', 'luxe-cost-calculator' ) );
    }

    // Save as lbs_lead if LBS CRM plugin is active
    if ( post_type_exists( 'lbs_lead' ) ) {
        $post_id = wp_insert_post( array(
            'post_title'  => $name . ' — Cost Estimate — ' . date( 'd M Y' ),
            'post_type'   => 'lbs_lead',
            'post_status' => 'publish',
        ) );
        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, 'lbs_lead_name',    $name );
            update_post_meta( $post_id, 'lbs_lead_email',   $email );
            update_post_meta( $post_id, 'lbs_lead_phone',   $phone );
            update_post_meta( $post_id, 'lbs_lead_source',  'cost_calculator' );
            update_post_meta( $post_id, 'lbs_lead_message', "Estimate: $estimate | Tier: $tier | Finish: $finish | Total Area: $area" );
            update_post_meta( $post_id, 'lbs_lead_submitted', current_time('mysql') );

            // Auto-assign "new" status
            $new_term = get_term_by( 'slug', 'new', 'lbs_lead_status' );
            if ( $new_term ) wp_set_object_terms( $post_id, $new_term->term_id, 'lbs_lead_status' );
        }
    }

    // Notify admin
    $general = get_option( 'lbs_general', array() );
    $to      = $general['admin_email'] ?? get_option( 'admin_email' );
    $studio  = $general['studio_name'] ?? get_bloginfo('name');
    wp_mail( $to,
        "New Cost Estimate Request from $name — $studio",
        "Name: $name\nEmail: $email\nPhone: $phone\nEstimate: $estimate\nTier: $tier\nFinish: $finish\nTotal Area: $area sqft"
    );

    // Send estimate to client
    wp_mail( $email,
        "Your Interior Design Estimate — $studio",
        "Hi $name,\n\nThank you for using our cost calculator.\n\nYour estimate summary:\nService Tier: $tier\nFinish Level: $finish\nTotal Area: $area\nEstimated Investment: $estimate\n\nThis is an indicative estimate only. Please contact us to discuss your project in detail.\n\n$studio\n" . home_url()
    );

    wp_send_json_success( array(
        'message' => __( "Your estimate has been emailed to you. We'll be in touch soon!", 'luxe-cost-calculator' ),
    ) );
}

/* ============================================================
   AJAX: EMAIL ESTIMATE (without lead capture)
============================================================ */
function lcc_ajax_email_estimate() {
    check_ajax_referer( 'lcc_nonce', 'nonce' );
    $email    = sanitize_email( $_POST['email'] ?? '' );
    $estimate = sanitize_text_field( $_POST['summary'] ?? '' );
    $name     = sanitize_text_field( $_POST['name'] ?? 'there' );

    if ( ! is_email( $email ) ) {
        wp_send_json_error( __( 'Please enter a valid email address.', 'luxe-cost-calculator' ) );
    }

    $general = get_option( 'lbs_general', array() );
    $studio  = $general['studio_name'] ?? get_bloginfo('name');

    wp_mail( $email,
        "Your Interior Design Estimate — $studio",
        "Hi $name,\n\nHere is your cost estimate:\n\n$estimate\n\nThis is an indicative estimate. Contact us to discuss your project.\n\n$studio\n" . home_url()
    );

    wp_send_json_success( array( 'message' => __( 'Estimate sent to your inbox!', 'luxe-cost-calculator' ) ) );
}
