<?php
/**
 * Rates & pricing data — inc/rates.php
 * All rates are per sqft in BDT by default.
 * Admins override via Settings page.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Default rates (BDT per sqft) ──────────────────────────── */
function lcc_default_rates() : array {
    return array(

        /* SERVICE TIERS ---------------------------------------- */
        'tiers' => array(
            'consultation' => array(
                'label'       => __( 'Design Consultation', 'luxe-cost-calculator' ),
                'description' => __( 'Expert guidance + space plan. You manage execution.', 'luxe-cost-calculator' ),
                'multiplier'  => 1.0,
                'base_rate'   => 180,   // BDT per sqft
                'icon'        => '📐',
                'color'       => '#6366f1',
                'includes'    => array( 'Space planning', '2D floor plan', 'Mood board', 'Material list' ),
            ),
            'full_design' => array(
                'label'       => __( 'Full Interior Design', 'luxe-cost-calculator' ),
                'description' => __( 'End-to-end design, procurement & supervision.', 'luxe-cost-calculator' ),
                'multiplier'  => 1.0,
                'base_rate'   => 450,   // BDT per sqft
                'icon'        => '✦',
                'color'       => '#C8A375',
                'includes'    => array( 'Full concept design', '3D visualization', 'FF&E sourcing', 'Procurement mgmt', 'Site supervision', 'Final styling' ),
            ),
            'turnkey' => array(
                'label'       => __( 'Turnkey Package', 'luxe-cost-calculator' ),
                'description' => __( 'Full design + all civil/fit-out works managed by us.', 'luxe-cost-calculator' ),
                'multiplier'  => 1.0,
                'base_rate'   => 950,   // BDT per sqft
                'icon'        => '🏠',
                'color'       => '#10b981',
                'includes'    => array( 'Everything in Full Design', 'Civil works coordination', 'Electrical & plumbing', 'Carpentry & joinery', 'Painting & finishes' ),
            ),
        ),

        /* FINISH / QUALITY LEVELS ------------------------------ */
        'finishes' => array(
            'standard' => array(
                'label'      => __( 'Standard', 'luxe-cost-calculator' ),
                'desc'       => __( 'Quality local materials, clean execution.', 'luxe-cost-calculator' ),
                'multiplier' => 1.0,
                'icon'       => '◇',
            ),
            'premium' => array(
                'label'      => __( 'Premium', 'luxe-cost-calculator' ),
                'desc'       => __( 'Mix of imported & local premium materials.', 'luxe-cost-calculator' ),
                'multiplier' => 1.6,
                'icon'       => '◆',
            ),
            'luxury' => array(
                'label'      => __( 'Luxury', 'luxe-cost-calculator' ),
                'desc'       => __( 'Fully imported, bespoke & artisan materials.', 'luxe-cost-calculator' ),
                'multiplier' => 2.6,
                'icon'       => '✦',
            ),
        ),

        /* ROOM TYPE RATE MULTIPLIERS --------------------------- */
        'rooms' => array(
            'living_room' => array(
                'label'      => __( 'Living Room',   'luxe-cost-calculator' ),
                'multiplier' => 1.0,
                'icon'       => '🛋',
                'typical_sqft' => 250,
            ),
            'master_bedroom' => array(
                'label'      => __( 'Master Bedroom', 'luxe-cost-calculator' ),
                'multiplier' => 1.05,
                'icon'       => '🛏',
                'typical_sqft' => 200,
            ),
            'bedroom' => array(
                'label'      => __( 'Bedroom',       'luxe-cost-calculator' ),
                'multiplier' => 0.9,
                'icon'       => '🚪',
                'typical_sqft' => 150,
            ),
            'kitchen' => array(
                'label'      => __( 'Kitchen',       'luxe-cost-calculator' ),
                'multiplier' => 1.4,
                'icon'       => '🍳',
                'typical_sqft' => 130,
            ),
            'dining_room' => array(
                'label'      => __( 'Dining Room',   'luxe-cost-calculator' ),
                'multiplier' => 0.9,
                'icon'       => '🍽',
                'typical_sqft' => 160,
            ),
            'bathroom' => array(
                'label'      => __( 'Bathroom',      'luxe-cost-calculator' ),
                'multiplier' => 1.6,
                'icon'       => '🚿',
                'typical_sqft' => 60,
            ),
            'master_bath' => array(
                'label'      => __( 'Master Bathroom','luxe-cost-calculator' ),
                'multiplier' => 1.8,
                'icon'       => '🛁',
                'typical_sqft' => 90,
            ),
            'home_office' => array(
                'label'      => __( 'Home Office',   'luxe-cost-calculator' ),
                'multiplier' => 1.1,
                'icon'       => '💼',
                'typical_sqft' => 120,
            ),
            'kids_room' => array(
                'label'      => __( "Kid's Room",    'luxe-cost-calculator' ),
                'multiplier' => 0.95,
                'icon'       => '🧸',
                'typical_sqft' => 130,
            ),
            'entryway' => array(
                'label'      => __( 'Entryway / Foyer','luxe-cost-calculator' ),
                'multiplier' => 1.1,
                'icon'       => '🚪',
                'typical_sqft' => 80,
            ),
            'balcony' => array(
                'label'      => __( 'Balcony / Terrace','luxe-cost-calculator' ),
                'multiplier' => 0.7,
                'icon'       => '🌿',
                'typical_sqft' => 100,
            ),
            'custom' => array(
                'label'      => __( 'Other / Custom', 'luxe-cost-calculator' ),
                'multiplier' => 1.0,
                'icon'       => '📐',
                'typical_sqft' => 100,
            ),
        ),

        /* ADD-ON SERVICES -------------------------------------- */
        'addons' => array(
            'lighting_design' => array(
                'label'    => __( 'Lighting Design', 'luxe-cost-calculator' ),
                'type'     => 'percent',   // percent of subtotal
                'value'    => 8,
                'icon'     => '💡',
            ),
            '3d_visualization' => array(
                'label'    => __( '3D Visualization', 'luxe-cost-calculator' ),
                'type'     => 'fixed',     // fixed amount
                'value'    => 35000,
                'icon'     => '🖥',
            ),
            'landscape' => array(
                'label'    => __( 'Landscape / Outdoor', 'luxe-cost-calculator' ),
                'type'     => 'percent',
                'value'    => 12,
                'icon'     => '🌳',
            ),
            'smart_home' => array(
                'label'    => __( 'Smart Home Automation', 'luxe-cost-calculator' ),
                'type'     => 'fixed',
                'value'    => 80000,
                'icon'     => '📱',
            ),
            'custom_furniture' => array(
                'label'    => __( 'Bespoke Furniture', 'luxe-cost-calculator' ),
                'type'     => 'percent',
                'value'    => 15,
                'icon'     => '🛠',
            ),
            'art_curation' => array(
                'label'    => __( 'Art & Accessory Curation', 'luxe-cost-calculator' ),
                'type'     => 'fixed',
                'value'    => 25000,
                'icon'     => '🎨',
            ),
        ),

        /* AREA COMPLEXITY SURCHARGES --------------------------- */
        'complexity' => array(
            'simple'  => array( 'label' => __( 'Simple (open plan)', 'luxe-cost-calculator' ),     'surcharge' => 0 ),
            'medium'  => array( 'label' => __( 'Medium complexity', 'luxe-cost-calculator' ),      'surcharge' => 10 ),  // % surcharge
            'complex' => array( 'label' => __( 'High complexity (irregular/heritage)', 'luxe-cost-calculator' ), 'surcharge' => 22 ),
        ),
    );
}

/* ── Default plugin settings ────────────────────────────────── */
function lcc_default_settings() : array {
    return array(
        'currency'      => 'BDT',
        'area_unit'     => 'sqft',          // sqft or sqm
        'vat_rate'      => 15,              // % VAT / tax
        'show_vat'      => true,
        'show_addons'   => true,
        'show_breakdown'=> true,
        'disclaimer'    => __( 'This estimate is indicative only. Final costs depend on exact specifications, market rates at time of procurement, and site conditions. A detailed quotation will be provided after our initial consultation.', 'luxe-cost-calculator' ),
        'cta_text'      => __( 'Get a Detailed Quote', 'luxe-cost-calculator' ),
        'cta_url'       => '/contact',
        'lead_capture'  => true,            // show name/email before results
        'min_budget'    => 200000,          // minimum project budget
        'min_budget_msg'=> __( 'Our minimum project investment is ৳2 Lakh. Please contact us to discuss your project.', 'luxe-cost-calculator' ),
    );
}

/* ── Get merged rates (defaults overridden by DB) ───────────── */
function lcc_get_all_rates() : array {
    $defaults = lcc_default_rates();
    $saved    = get_option( 'lcc_rates', array() );
    return array_replace_recursive( $defaults, $saved );
}

/* ── Option helpers ─────────────────────────────────────────── */
function lcc_get_option( string $key, $default = '' ) {
    $settings = get_option( 'lcc_settings', lcc_default_settings() );
    return $settings[ $key ] ?? $default;
}

function lcc_currency_symbol() : string {
    $map = array( 'BDT' => '৳', 'USD' => '$', 'GBP' => '£', 'EUR' => '€', 'AED' => 'د.إ' );
    return $map[ lcc_get_option( 'currency', 'BDT' ) ] ?? '৳';
}

/* ── Format money ───────────────────────────────────────────── */
function lcc_format_money( float $amount ) : string {
    $sym = lcc_currency_symbol();
    if ( lcc_get_option( 'currency', 'BDT' ) === 'BDT' && $amount >= 100000 ) {
        $lakh = $amount / 100000;
        return $sym . number_format( $lakh, 2 ) . ' L';
    }
    return $sym . number_format( $amount, 0 );
}
