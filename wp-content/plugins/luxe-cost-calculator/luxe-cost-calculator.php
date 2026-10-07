<?php
/**
 * Plugin Name:       Luxe Cost Calculator
 * Plugin URI:        https://luxeinterior.com
 * Description:       Interactive per-square-foot/metre cost estimator for Luxe Interior. Live frontend calculator with room breakdown, service tier selection, finish levels, and PDF-ready quote summary. Shortcode: [lbs_cost_calculator]
 * Version:           1.0.0
 * Author:            Luxe Interior Design
 * Author URI:        https://luxeinterior.com
 * Text Domain:       luxe-cost-calculator
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * License:           GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LCC_VERSION', '1.0.0' );
define( 'LCC_DIR',     plugin_dir_path( __FILE__ ) );
define( 'LCC_URL',     plugin_dir_url(  __FILE__ ) );

require_once LCC_DIR . 'inc/rates.php';
require_once LCC_DIR . 'inc/settings.php';
require_once LCC_DIR . 'inc/ajax.php';
require_once LCC_DIR . 'inc/shortcode.php';

/* ── Assets ─────────────────────────────────────────────────── */
add_action( 'wp_enqueue_scripts', 'lcc_frontend_assets' );

function lcc_frontend_assets() {
    if ( ! is_singular() && ! is_page() ) return;
    global $post;
    if ( ! has_shortcode( $post->post_content ?? '', 'lbs_cost_calculator' ) ) return;

    wp_enqueue_style(
        'lcc-frontend',
        LCC_URL . 'assets/css/calculator.css',
        array(),
        LCC_VERSION
    );
    wp_enqueue_script(
        'lcc-frontend',
        LCC_URL . 'assets/js/calculator.js',
        array( 'jquery' ),
        LCC_VERSION,
        true
    );
    wp_localize_script( 'lcc-frontend', 'lccData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'lcc_nonce' ),
        'rates'   => lcc_get_all_rates(),
        'currency'=> lcc_get_option( 'currency', 'BDT' ),
        'symbol'  => lcc_currency_symbol(),
        'unit'    => lcc_get_option( 'area_unit', 'sqft' ),
        'strings' => array(
            'perUnit'    => lcc_get_option( 'area_unit', 'sqft' ) === 'sqft' ? __( 'per sqft', 'luxe-cost-calculator' ) : __( 'per m²', 'luxe-cost-calculator' ),
            'totalEst'   => __( 'Estimated Total', 'luxe-cost-calculator' ),
            'addRoom'    => __( 'Add Room', 'luxe-cost-calculator' ),
            'removeRoom' => __( 'Remove', 'luxe-cost-calculator' ),
        ),
    ) );
}

/* ── Activation ─────────────────────────────────────────────── */
register_activation_hook( __FILE__, function () {
    if ( ! get_option( 'lcc_rates' ) ) {
        update_option( 'lcc_rates', lcc_default_rates() );
    }
    if ( ! get_option( 'lcc_settings' ) ) {
        update_option( 'lcc_settings', lcc_default_settings() );
    }
} );
