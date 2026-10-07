<?php
/**
 * Global helper functions — inc/helpers.php
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Sanitise helpers ───────────────────────────────────────── */
function lbs_sanitize_select( $value, $setting ) {
    $choices = $setting->manager->get_control( $setting->id )->choices ?? [];
    return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

function lbs_sanitize_checkbox( $v ) { return (bool) $v; }

/* ── Nonce helpers ──────────────────────────────────────────── */
function lbs_nonce_field( string $action ) {
    wp_nonce_field( 'lbs_' . $action, 'lbs_nonce_' . $action );
}

function lbs_verify_nonce( string $action ) : bool {
    $key = 'lbs_nonce_' . $action;
    return isset( $_POST[ $key ] ) && wp_verify_nonce( $_POST[ $key ], 'lbs_' . $action );
}

/* ── Admin notice ───────────────────────────────────────────── */
function lbs_admin_notice( string $message, string $type = 'success' ) {
    add_action( 'admin_notices', function () use ( $message, $type ) {
        echo '<div class="notice notice-' . esc_attr( $type ) . ' is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
    } );
}

/* ── Currency formatter ─────────────────────────────────────── */
function lbs_format_currency( $amount, string $currency = 'BDT' ) : string {
    $symbols = array( 'BDT' => '৳', 'USD' => '$', 'GBP' => '£', 'EUR' => '€', 'AED' => 'د.إ' );
    $sym = $symbols[ $currency ] ?? $currency . ' ';
    return $sym . number_format( (float) $amount );
}

/* ── Reading time ───────────────────────────────────────────── */
function lbs_reading_time( int $post_id ) : string {
    $words = str_word_count( wp_strip_all_tags( get_the_content( null, false, $post_id ) ) );
    $mins  = max( 1, (int) ceil( $words / 200 ) );
    return sprintf( _n( '%d min read', '%d min read', $mins, 'luxe-business-suite' ), $mins );
}

/* ── Render field helper for meta boxes ─────────────────────── */
function lbs_meta_field( array $args ) {
    $id    = $args['id'];
    $label = $args['label'];
    $type  = $args['type'] ?? 'text';
    $value = $args['value'] ?? '';
    $ph    = $args['placeholder'] ?? '';
    $hint  = $args['hint'] ?? '';
    $opts  = $args['options'] ?? array();

    echo '<div class="lbs-field">';
    echo '<label class="lbs-label" for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';

    switch ( $type ) {
        case 'textarea':
            echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" class="lbs-input" rows="3" placeholder="' . esc_attr( $ph ) . '">' . esc_textarea( $value ) . '</textarea>';
            break;
        case 'select':
            echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" class="lbs-input">';
            foreach ( $opts as $v => $l ) {
                echo '<option value="' . esc_attr( $v ) . '"' . selected( $value, $v, false ) . '>' . esc_html( $l ) . '</option>';
            }
            echo '</select>';
            break;
        case 'checkbox':
            echo '<label class="lbs-checkbox"><input type="checkbox" name="' . esc_attr( $id ) . '" value="1"' . checked( $value, '1', false ) . '> ' . esc_html( $ph ) . '</label>';
            break;
        default:
            echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $ph ) . '" class="lbs-input">';
    }

    if ( $hint ) echo '<p class="lbs-hint">' . esc_html( $hint ) . '</p>';
    echo '</div>';
}

/* ── Pagination helper ──────────────────────────────────────── */
function lbs_paginate( WP_Query $q ) {
    $pages = paginate_links( array(
        'total'   => $q->max_num_pages,
        'current' => max( 1, get_query_var( 'paged' ) ),
        'type'    => 'array',
    ) );
    if ( empty( $pages ) ) return;
    echo '<nav class="lbs-pagination">' . implode( '', $pages ) . '</nav>';
}
