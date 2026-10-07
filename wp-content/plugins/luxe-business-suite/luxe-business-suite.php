<?php
/**
 * Plugin Name:       Luxe Business Suite
 * Plugin URI:        https://luxeinterior.com
 * Description:       All-in-one business logic plugin for Luxe Interior. Six toggleable modules: Lead CRM, Service Booking, Team & About Manager, Pricing & Packages, Journal Enhancements, and Client Portal — built specifically for the Luxe Interior theme.
 * Version:           1.0.0
 * Author:            Luxe Interior Design
 * Author URI:        https://luxeinterior.com
 * Text Domain:       luxe-business-suite
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * License:           GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Constants ─────────────────────────────────────────────── */
define( 'LBS_VERSION', '1.0.0' );
define( 'LBS_DIR',     plugin_dir_path( __FILE__ ) );
define( 'LBS_URL',     plugin_dir_url(  __FILE__ ) );
define( 'LBS_PREFIX',  'lbs_' );

/* ── Default module toggles ─────────────────────────────────── */
function lbs_modules() {
    return array(
        'crm'     => __( 'Lead CRM',             'luxe-business-suite' ),
        'booking' => __( 'Service Booking',       'luxe-business-suite' ),
        'team'    => __( 'Team & About Manager',  'luxe-business-suite' ),
        'pricing' => __( 'Pricing & Packages',    'luxe-business-suite' ),
        'journal' => __( 'Journal Enhancements',  'luxe-business-suite' ),
        'portal'  => __( 'Client Portal',         'luxe-business-suite' ),
    );
}

function lbs_module_active( string $module ) : bool {
    $opts = get_option( 'lbs_modules', array() );
    // All active by default on first install
    if ( empty( $opts ) ) return true;
    return ! empty( $opts[ $module ] );
}

/* ── Load modules ───────────────────────────────────────────── */
require_once LBS_DIR . 'inc/helpers.php';
require_once LBS_DIR . 'inc/settings.php';
require_once LBS_DIR . 'inc/assets.php';

if ( lbs_module_active( 'crm' ) )     require_once LBS_DIR . 'inc/module-crm.php';
if ( lbs_module_active( 'booking' ) ) require_once LBS_DIR . 'inc/module-booking.php';
if ( lbs_module_active( 'team' ) )    require_once LBS_DIR . 'inc/module-team.php';
if ( lbs_module_active( 'pricing' ) ) require_once LBS_DIR . 'inc/module-pricing.php';
if ( lbs_module_active( 'journal' ) ) require_once LBS_DIR . 'inc/module-journal.php';
if ( lbs_module_active( 'portal' ) )  require_once LBS_DIR . 'inc/module-portal.php';

/* ── Activation / Deactivation ──────────────────────────────── */
register_activation_hook( __FILE__, 'lbs_activate' );
register_deactivation_hook( __FILE__, 'lbs_deactivate' );

function lbs_activate() {
    // Set all modules active by default
    if ( ! get_option( 'lbs_modules' ) ) {
        $defaults = array_fill_keys( array_keys( lbs_modules() ), 1 );
        update_option( 'lbs_modules', $defaults );
    }
    // Create portal page if needed
    lbs_create_portal_page();
    flush_rewrite_rules();
}

function lbs_deactivate() {
    flush_rewrite_rules();
}

function lbs_create_portal_page() {
    if ( get_page_by_path( 'client-portal' ) ) return;
    wp_insert_post( array(
        'post_title'   => 'Client Portal',
        'post_name'    => 'client-portal',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '[lbs_client_portal]',
    ) );
}
