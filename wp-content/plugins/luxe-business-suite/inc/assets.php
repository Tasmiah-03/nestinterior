<?php
/**
 * Asset Enqueue — inc/assets.php
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts',    'lbs_frontend_assets' );
add_action( 'admin_enqueue_scripts', 'lbs_admin_assets' );

function lbs_frontend_assets() {
    wp_enqueue_style(  'lbs-frontend', LBS_URL . 'assets/css/frontend.css', array(), LBS_VERSION );
    wp_enqueue_script( 'lbs-frontend', LBS_URL . 'assets/js/frontend.js',   array( 'jquery' ), LBS_VERSION, true );
    wp_localize_script( 'lbs-frontend', 'lbsData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'lbs_ajax' ),
        'strings' => array(
            'searching'     => __( 'Searching…',        'luxe-business-suite' ),
            'noResults'     => __( 'No posts found.',   'luxe-business-suite' ),
            'bookingSlots'  => __( 'Loading slots…',    'luxe-business-suite' ),
            'portalLoading' => __( 'Loading…',          'luxe-business-suite' ),
        ),
    ) );
}

function lbs_admin_assets( string $hook ) {
    $post_types = array( 'lbs_lead', 'lbs_booking', 'lbs_team_member', 'lbs_award',
                         'lbs_press', 'lbs_service_package', 'lbs_faq',
                         'lbs_client_project', 'lbs_project_doc', 'lbs_mood_board' );

    wp_enqueue_style( 'lbs-admin', LBS_URL . 'assets/css/admin.css', array(), LBS_VERSION );

    global $post;
    $is_lbs_post = isset( $post ) && in_array( $post->post_type, $post_types, true );

    if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && $is_lbs_post ) {
        wp_enqueue_media();
        wp_enqueue_script( 'lbs-admin', LBS_URL . 'assets/js/admin.js', array( 'jquery' ), LBS_VERSION, true );
        wp_localize_script( 'lbs-admin', 'lbsAdmin', array(
            'selectImage' => __( 'Select Image',     'luxe-business-suite' ),
            'useImage'    => __( 'Use This Image',   'luxe-business-suite' ),
            'remove'      => __( 'Remove',           'luxe-business-suite' ),
        ) );
    }

    // Dashboard widget scripts
    if ( $hook === 'index.php' ) {
        wp_enqueue_script( 'lbs-dashboard', LBS_URL . 'assets/js/dashboard.js', array( 'jquery' ), LBS_VERSION, true );
    }
}
