<?php
/**
 * Module 4 — Pricing & Packages
 * inc/module-pricing.php
 *
 * CPTs: lbs_service_package, lbs_faq
 * Taxonomy: lbs_package_type
 * Shortcodes: [lbs_packages], [lbs_faqs]
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   REGISTER CPTs + TAXONOMY
============================================================ */
add_action( 'init', 'lbs_pricing_register' );

function lbs_pricing_register() {

    // Service Package CPT
    register_post_type( 'lbs_service_package', array(
        'labels' => array(
            'name'          => __( 'Service Packages',  'luxe-business-suite' ),
            'singular_name' => __( 'Package',           'luxe-business-suite' ),
            'menu_name'     => __( 'Pricing',           'luxe-business-suite' ),
            'add_new_item'  => __( 'Add Package',       'luxe-business-suite' ),
            'all_items'     => __( 'All Packages',      'luxe-business-suite' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-tag',
        'menu_position'   => 8,
        'supports'        => array( 'title', 'editor', 'page-attributes' ),
        'has_archive'     => false,
        'capability_type' => 'post',
    ) );

    // FAQ CPT
    register_post_type( 'lbs_faq', array(
        'labels' => array(
            'name'          => __( 'FAQs',        'luxe-business-suite' ),
            'singular_name' => __( 'FAQ',         'luxe-business-suite' ),
            'add_new_item'  => __( 'Add FAQ',     'luxe-business-suite' ),
            'all_items'     => __( 'All FAQs',    'luxe-business-suite' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=lbs_service_package',
        'supports'        => array( 'title', 'editor', 'page-attributes' ),
        'has_archive'     => false,
        'capability_type' => 'post',
    ) );

    // Package Type taxonomy (Residential / Commercial / etc.)
    register_taxonomy( 'lbs_package_type', array( 'lbs_service_package', 'lbs_faq' ), array(
        'labels'            => array(
            'name'          => __( 'Package Types', 'luxe-business-suite' ),
            'singular_name' => __( 'Type',          'luxe-business-suite' ),
            'add_new_item'  => __( 'Add Type',      'luxe-business-suite' ),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => false,
        'rewrite'           => false,
    ) );
}

/* ============================================================
   DEFAULT PACKAGE TYPES
============================================================ */
add_action( 'init', function () {
    if ( get_option( 'lbs_package_types_inserted' ) ) return;
    foreach ( array( 'Residential', 'Commercial', 'Consultation', 'Hospitality' ) as $t ) {
        if ( ! term_exists( $t, 'lbs_package_type' ) ) wp_insert_term( $t, 'lbs_package_type' );
    }
    update_option( 'lbs_package_types_inserted', true );
}, 20 );

/* ============================================================
   PACKAGE META BOXES
============================================================ */
add_action( 'add_meta_boxes', 'lbs_pricing_meta_boxes' );
add_action( 'save_post_lbs_service_package', 'lbs_save_package_meta', 10, 2 );
add_action( 'save_post_lbs_faq',             'lbs_save_faq_meta',     10, 2 );

function lbs_pricing_meta_boxes() {
    add_meta_box( 'lbs_pkg_price',    __( '💰 Pricing',          'luxe-business-suite' ), 'lbs_mb_pkg_price',    'lbs_service_package', 'side',   'high' );
    add_meta_box( 'lbs_pkg_features', __( '✅ Features List',     'luxe-business-suite' ), 'lbs_mb_pkg_features', 'lbs_service_package', 'normal', 'high' );
    add_meta_box( 'lbs_pkg_display',  __( '⚙ Display Options',   'luxe-business-suite' ), 'lbs_mb_pkg_display',  'lbs_service_package', 'side',   'default' );
    add_meta_box( 'lbs_faq_group',    __( '📂 FAQ Group',         'luxe-business-suite' ), 'lbs_mb_faq_group',    'lbs_faq',             'side',   'high' );
}

function lbs_mb_pkg_price( $post ) {
    wp_nonce_field( 'lbs_pkg_save', 'lbs_pkg_nonce' );
    $general  = get_option( 'lbs_general', array() );
    $currency = $general['currency'] ?? 'BDT';
    $price_type  = get_post_meta( $post->ID, 'lbs_pkg_price_type', true ) ?: 'fixed';
    $price_from  = get_post_meta( $post->ID, 'lbs_pkg_price_from', true );
    $price_to    = get_post_meta( $post->ID, 'lbs_pkg_price_to',   true );
    $price_label = get_post_meta( $post->ID, 'lbs_pkg_price_label',true );
    $price_unit  = get_post_meta( $post->ID, 'lbs_pkg_price_unit', true );
    ?>
    <div class="lbs-field">
        <label class="lbs-label"><?php _e( 'Price Type', 'luxe-business-suite' ); ?></label>
        <select name="lbs_pkg_price_type" class="lbs-input" id="lbs_pkg_price_type">
            <option value="fixed"   <?php selected($price_type,'fixed');   ?>><?php _e('Fixed Price', 'luxe-business-suite'); ?></option>
            <option value="from"    <?php selected($price_type,'from');    ?>><?php _e('Starting From', 'luxe-business-suite'); ?></option>
            <option value="range"   <?php selected($price_type,'range');   ?>><?php _e('Price Range', 'luxe-business-suite'); ?></option>
            <option value="request" <?php selected($price_type,'request'); ?>><?php _e('Upon Request', 'luxe-business-suite'); ?></option>
            <option value="custom"  <?php selected($price_type,'custom');  ?>><?php _e('Custom Label', 'luxe-business-suite'); ?></option>
        </select>
    </div>
    <div class="lbs-field">
        <label class="lbs-label"><?php printf( __('Price (%s)', 'luxe-business-suite'), $currency ); ?></label>
        <input type="number" name="lbs_pkg_price_from" value="<?php echo esc_attr($price_from); ?>" class="lbs-input" placeholder="0" min="0">
    </div>
    <div class="lbs-field">
        <label class="lbs-label"><?php printf( __('Price To (%s) — range only', 'luxe-business-suite'), $currency ); ?></label>
        <input type="number" name="lbs_pkg_price_to" value="<?php echo esc_attr($price_to); ?>" class="lbs-input" placeholder="0" min="0">
    </div>
    <div class="lbs-field">
        <label class="lbs-label"><?php _e( 'Custom Label', 'luxe-business-suite' ); ?></label>
        <input type="text" name="lbs_pkg_price_label" value="<?php echo esc_attr($price_label); ?>" class="lbs-input" placeholder="e.g. Contact for pricing">
    </div>
    <div class="lbs-field">
        <label class="lbs-label"><?php _e( 'Billed Per', 'luxe-business-suite' ); ?></label>
        <select name="lbs_pkg_price_unit" class="lbs-input">
            <?php foreach ( array( '' => '—', 'project' => 'project', 'room' => 'room', 'sqft' => 'sqft', 'hour' => 'hour', 'session' => 'session' ) as $v => $l ) : ?>
            <option value="<?php echo esc_attr($v); ?>" <?php selected($price_unit,$v); ?>><?php echo esc_html($l); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}

function lbs_mb_pkg_features( $post ) {
    $features = get_post_meta( $post->ID, 'lbs_pkg_features', true );
    if ( ! is_array( $features ) ) $features = array();
    if ( empty( $features ) ) $features = array( array( 'text' => '', 'included' => '1' ) );
    ?>
    <p class="lbs-hint"><?php _e( 'List what is included (and excluded) in this package.', 'luxe-business-suite' ); ?></p>
    <ul id="lbs-features-list" style="list-style:none;padding:0;margin:0 0 12px;">
    <?php foreach ( $features as $i => $feat ) : ?>
        <li class="lbs-feature-row" style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <select name="lbs_feat_included[<?php echo $i; ?>]" style="width:90px;font-size:12px;padding:5px;">
                <option value="1" <?php selected( $feat['included'] ?? '1', '1' ); ?>>✅ Yes</option>
                <option value="0" <?php selected( $feat['included'] ?? '1', '0' ); ?>>❌ No</option>
            </select>
            <input type="text" name="lbs_feat_text[<?php echo $i; ?>]" value="<?php echo esc_attr($feat['text']??''); ?>" placeholder="Feature description…" style="flex:1;font-size:13px;padding:5px 8px;border:1px solid #ddd;">
            <button type="button" class="button-link" onclick="this.closest('li').remove()" style="color:#d63638;font-size:16px;" title="Remove">✕</button>
        </li>
    <?php endforeach; ?>
    </ul>
    <button type="button" id="lbs-add-feature" class="button">+ <?php _e('Add Feature', 'luxe-business-suite'); ?></button>
    <script>
    document.getElementById('lbs-add-feature').addEventListener('click', function(){
        var list = document.getElementById('lbs-features-list');
        var idx  = list.children.length;
        var li   = document.createElement('li');
        li.className = 'lbs-feature-row';
        li.style.cssText = 'display:flex;align-items:center;gap:8px;margin-bottom:8px;';
        li.innerHTML = '<select name="lbs_feat_included['+idx+']" style="width:90px;font-size:12px;padding:5px;"><option value="1">✅ Yes</option><option value="0">❌ No</option></select>'
            + '<input type="text" name="lbs_feat_text['+idx+']" value="" placeholder="Feature description…" style="flex:1;font-size:13px;padding:5px 8px;border:1px solid #ddd;">'
            + '<button type="button" class="button-link" onclick="this.closest(\'li\').remove()" style="color:#d63638;font-size:16px;">✕</button>';
        list.appendChild(li);
    });
    </script>
    <?php
}

function lbs_mb_pkg_display( $post ) {
    $featured    = get_post_meta( $post->ID, 'lbs_pkg_featured',    true );
    $highlighted = get_post_meta( $post->ID, 'lbs_pkg_highlighted', true );
    $cta_label   = get_post_meta( $post->ID, 'lbs_pkg_cta_label',   true );
    $cta_url     = get_post_meta( $post->ID, 'lbs_pkg_cta_url',     true );
    $badge       = get_post_meta( $post->ID, 'lbs_pkg_badge',       true );
    $hidden      = get_post_meta( $post->ID, 'lbs_pkg_hidden',      true );

    lbs_meta_field( array( 'id' => 'lbs_pkg_featured',    'label' => 'Mark as Popular',      'type' => 'checkbox', 'value' => $featured,    'placeholder' => 'Show "Popular" badge' ) );
    lbs_meta_field( array( 'id' => 'lbs_pkg_highlighted', 'label' => 'Highlight (accent)',    'type' => 'checkbox', 'value' => $highlighted, 'placeholder' => 'Use accent colour card style' ) );
    lbs_meta_field( array( 'id' => 'lbs_pkg_badge',       'label' => 'Custom Badge Text',     'type' => 'text',     'value' => $badge,       'placeholder' => 'e.g. Best Value' ) );
    lbs_meta_field( array( 'id' => 'lbs_pkg_cta_label',   'label' => 'CTA Button Label',      'type' => 'text',     'value' => $cta_label,   'placeholder' => 'Book a Consultation' ) );
    lbs_meta_field( array( 'id' => 'lbs_pkg_cta_url',     'label' => 'CTA Button URL',        'type' => 'url',      'value' => $cta_url,     'placeholder' => '/contact' ) );
    lbs_meta_field( array( 'id' => 'lbs_pkg_hidden',      'label' => 'Hide from Frontend',    'type' => 'checkbox', 'value' => $hidden,      'placeholder' => 'Do not display publicly' ) );
}

function lbs_mb_faq_group( $post ) {
    wp_nonce_field( 'lbs_faq_save', 'lbs_faq_nonce' );
    $icon = get_post_meta( $post->ID, 'lbs_faq_icon', true );
    lbs_meta_field( array( 'id' => 'lbs_faq_icon', 'label' => 'Icon (emoji or symbol)', 'type' => 'text', 'value' => $icon, 'placeholder' => '❓' ) );
}

function lbs_save_package_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_pkg_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_pkg_nonce'], 'lbs_pkg_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    foreach ( array( 'lbs_pkg_price_type','lbs_pkg_price_unit','lbs_pkg_badge','lbs_pkg_cta_label' ) as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[$f] ) );
    }
    foreach ( array( 'lbs_pkg_price_from','lbs_pkg_price_to' ) as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, absint( $_POST[$f] ) );
    }
    if ( isset( $_POST['lbs_pkg_price_label'] ) ) update_post_meta( $post_id, 'lbs_pkg_price_label', sanitize_text_field( $_POST['lbs_pkg_price_label'] ) );
    if ( isset( $_POST['lbs_pkg_cta_url'] ) )     update_post_meta( $post_id, 'lbs_pkg_cta_url',     esc_url_raw( $_POST['lbs_pkg_cta_url'] ) );
    foreach ( array( 'lbs_pkg_featured','lbs_pkg_highlighted','lbs_pkg_hidden' ) as $cb ) {
        update_post_meta( $post_id, $cb, isset( $_POST[$cb] ) ? '1' : '0' );
    }

    // Features
    $texts    = $_POST['lbs_feat_text']     ?? array();
    $included = $_POST['lbs_feat_included'] ?? array();
    $features = array();
    foreach ( $texts as $i => $text ) {
        $t = sanitize_text_field( $text );
        if ( $t ) $features[] = array( 'text' => $t, 'included' => sanitize_text_field( $included[$i] ?? '1' ) );
    }
    update_post_meta( $post_id, 'lbs_pkg_features', $features );
}

function lbs_save_faq_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_faq_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_faq_nonce'], 'lbs_faq_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( isset( $_POST['lbs_faq_icon'] ) ) update_post_meta( $post_id, 'lbs_faq_icon', sanitize_text_field( $_POST['lbs_faq_icon'] ) );
}

/* ============================================================
   PRICE FORMATTER
============================================================ */
function lbs_format_package_price( int $post_id ) : string {
    $type  = get_post_meta( $post_id, 'lbs_pkg_price_type',  true ) ?: 'fixed';
    $from  = get_post_meta( $post_id, 'lbs_pkg_price_from',  true );
    $to    = get_post_meta( $post_id, 'lbs_pkg_price_to',    true );
    $label = get_post_meta( $post_id, 'lbs_pkg_price_label', true );
    $unit  = get_post_meta( $post_id, 'lbs_pkg_price_unit',  true );
    $general  = get_option( 'lbs_general', array() );
    $currency = $general['currency'] ?? 'BDT';

    if ( $type === 'request' ) return __( 'Upon Request', 'luxe-business-suite' );
    if ( $type === 'custom' && $label ) return $label;

    $f = lbs_format_currency( $from, $currency );
    $t = $to ? lbs_format_currency( $to, $currency ) : '';

    $price_str = match( $type ) {
        'from'  => __( 'From ', 'luxe-business-suite' ) . $f,
        'range' => $t ? "$f – $t" : $f,
        default => $f,
    };

    if ( $unit ) $price_str .= ' <small class="lbs-pkg-unit">/ ' . esc_html($unit) . '</small>';
    return $price_str;
}

/* ============================================================
   SHORTCODES
============================================================ */
add_shortcode( 'lbs_packages', 'lbs_sc_packages' );
add_shortcode( 'lbs_faqs',     'lbs_sc_faqs' );

function lbs_sc_packages( $atts ) {
    $a = shortcode_atts( array(
        'type'    => '',
        'count'   => -1,
        'columns' => 3,
    ), $atts );

    $args = array(
        'post_type'      => 'lbs_service_package',
        'posts_per_page' => (int) $a['count'],
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'OR',
            array( 'key' => 'lbs_pkg_hidden', 'compare' => 'NOT EXISTS' ),
            array( 'key' => 'lbs_pkg_hidden', 'value'   => '1', 'compare' => '!=' ),
        ),
    );
    if ( $a['type'] ) {
        $args['tax_query'] = array( array( 'taxonomy' => 'lbs_package_type', 'field' => 'slug', 'terms' => sanitize_text_field($a['type']) ) );
    }
    $q = new WP_Query( $args );
    if ( ! $q->have_posts() ) return '';

    $cols = in_array( (int)$a['columns'], array(2,3,4), true ) ? (int)$a['columns'] : 3;
    ob_start();
    echo '<div class="lbs-packages-grid lbs-grid--cols-' . esc_attr($cols) . '">';
    while ( $q->have_posts() ) :
        $q->the_post();
        $pid         = get_the_ID();
        $featured    = get_post_meta( $pid, 'lbs_pkg_featured',    true );
        $highlighted = get_post_meta( $pid, 'lbs_pkg_highlighted', true );
        $badge_text  = get_post_meta( $pid, 'lbs_pkg_badge',       true );
        $cta_label   = get_post_meta( $pid, 'lbs_pkg_cta_label',   true ) ?: __( 'Get Started', 'luxe-business-suite' );
        $cta_url     = get_post_meta( $pid, 'lbs_pkg_cta_url',     true ) ?: '/contact';
        $features    = get_post_meta( $pid, 'lbs_pkg_features',    true );
        if ( ! is_array( $features ) ) $features = array();

        $card_class = 'lbs-package-card';
        if ( $highlighted ) $card_class .= ' lbs-package-card--highlight';
        if ( $featured )    $card_class .= ' lbs-package-card--popular';
        ?>
        <div class="<?php echo esc_attr($card_class); ?>">
            <?php if ( $featured || $badge_text ) : ?>
                <div class="lbs-pkg-badge"><?php echo esc_html( $badge_text ?: __('Most Popular','luxe-business-suite') ); ?></div>
            <?php endif; ?>
            <div class="lbs-pkg-name"><?php the_title(); ?></div>
            <div class="lbs-pkg-price"><?php echo lbs_format_package_price( $pid ); ?></div>
            <?php if ( has_excerpt() ) : ?>
                <p class="lbs-pkg-desc"><?php the_excerpt(); ?></p>
            <?php endif; ?>
            <?php if ( ! empty( $features ) ) : ?>
            <ul class="lbs-pkg-features">
                <?php foreach ( $features as $feat ) : ?>
                <li class="<?php echo $feat['included'] === '1' ? 'lbs-feat--yes' : 'lbs-feat--no'; ?>">
                    <span class="lbs-feat-icon"><?php echo $feat['included'] === '1' ? '✓' : '✗'; ?></span>
                    <?php echo esc_html( $feat['text'] ); ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <a href="<?php echo esc_url($cta_url); ?>" class="btn <?php echo $highlighted ? 'btn-accent' : 'btn-outline'; ?> lbs-pkg-cta">
                <?php echo esc_html($cta_label); ?>
            </a>
        </div>
    <?php endwhile;
    wp_reset_postdata();
    echo '</div>';
    return ob_get_clean();
}

function lbs_sc_faqs( $atts ) {
    $a = shortcode_atts( array( 'type' => '', 'count' => -1 ), $atts );
    $args = array(
        'post_type'      => 'lbs_faq',
        'posts_per_page' => (int) $a['count'],
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );
    if ( $a['type'] ) {
        $args['tax_query'] = array( array( 'taxonomy' => 'lbs_package_type', 'field' => 'slug', 'terms' => sanitize_text_field($a['type']) ) );
    }
    $faqs = new WP_Query( $args );
    if ( ! $faqs->have_posts() ) return '';

    ob_start();
    echo '<div class="lbs-faq-accordion" itemscope itemtype="https://schema.org/FAQPage">';
    while ( $faqs->have_posts() ) :
        $faqs->the_post();
        $icon = get_post_meta( get_the_ID(), 'lbs_faq_icon', true ) ?: '❓';
    ?>
    <div class="lbs-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
        <button class="lbs-faq-question" aria-expanded="false" itemprop="name">
            <span class="lbs-faq-icon"><?php echo esc_html($icon); ?></span>
            <?php the_title(); ?>
            <span class="lbs-faq-chevron" aria-hidden="true">›</span>
        </button>
        <div class="lbs-faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" hidden>
            <div itemprop="text"><?php the_content(); ?></div>
        </div>
    </div>
    <?php endwhile;
    wp_reset_postdata();
    echo '</div>';
    return ob_get_clean();
}
