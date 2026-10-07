<?php
/**
 * Module 3 — Team & About Manager
 * inc/module-team.php
 *
 * CPTs: lbs_team_member, lbs_award, lbs_press
 * Taxonomies: lbs_specialism
 * Shortcodes: [lbs_team], [lbs_awards], [lbs_press], [lbs_timeline]
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   REGISTER CPTs + TAXONOMY
============================================================ */
add_action( 'init', 'lbs_team_register' );

function lbs_team_register() {

    // Team Member CPT
    register_post_type( 'lbs_team_member', array(
        'labels' => array(
            'name'          => __( 'Team Members',    'luxe-business-suite' ),
            'singular_name' => __( 'Team Member',     'luxe-business-suite' ),
            'menu_name'     => __( 'Team & About',    'luxe-business-suite' ),
            'add_new_item'  => __( 'Add Team Member', 'luxe-business-suite' ),
            'edit_item'     => __( 'Edit Member',     'luxe-business-suite' ),
            'all_items'     => __( 'All Members',     'luxe-business-suite' ),
        ),
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-id',
        'menu_position'   => 7,
        'supports'        => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes' ),
        'has_archive'     => false,
        'rewrite'         => array( 'slug' => 'team' ),
        'capability_type' => 'post',
        'show_in_rest'    => true,
    ) );

    // Award CPT
    register_post_type( 'lbs_award', array(
        'labels' => array(
            'name'          => __( 'Awards',       'luxe-business-suite' ),
            'singular_name' => __( 'Award',        'luxe-business-suite' ),
            'add_new_item'  => __( 'Add Award',    'luxe-business-suite' ),
            'all_items'     => __( 'All Awards',   'luxe-business-suite' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=lbs_team_member',
        'supports'        => array( 'title', 'page-attributes' ),
        'has_archive'     => false,
        'capability_type' => 'post',
    ) );

    // Press Mention CPT
    register_post_type( 'lbs_press', array(
        'labels' => array(
            'name'          => __( 'Press Mentions', 'luxe-business-suite' ),
            'singular_name' => __( 'Press Mention',  'luxe-business-suite' ),
            'add_new_item'  => __( 'Add Press Mention','luxe-business-suite' ),
            'all_items'     => __( 'All Press',       'luxe-business-suite' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=lbs_team_member',
        'supports'        => array( 'title', 'page-attributes' ),
        'has_archive'     => false,
        'capability_type' => 'post',
    ) );

    // Specialism Taxonomy
    register_taxonomy( 'lbs_specialism', array( 'lbs_team_member' ), array(
        'labels'       => array(
            'name'          => __( 'Specialisms',   'luxe-business-suite' ),
            'singular_name' => __( 'Specialism',    'luxe-business-suite' ),
            'add_new_item'  => __( 'Add Specialism','luxe-business-suite' ),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => false,
    ) );
}

/* ============================================================
   DEFAULT SPECIALISM TERMS
============================================================ */
add_action( 'init', function () {
    if ( get_option( 'lbs_specialisms_inserted' ) ) return;
    foreach ( array( 'Residential', 'Commercial', 'Lighting', 'FF&E', 'Project Management', 'Spatial Planning', 'Biophilic Design', 'Hospitality' ) as $s ) {
        if ( ! term_exists( $s, 'lbs_specialism' ) ) wp_insert_term( $s, 'lbs_specialism' );
    }
    update_option( 'lbs_specialisms_inserted', true );
}, 20 );

/* ============================================================
   TEAM MEMBER META BOXES
============================================================ */
add_action( 'add_meta_boxes', 'lbs_team_meta_boxes' );
add_action( 'save_post_lbs_team_member', 'lbs_save_team_meta', 10, 2 );

function lbs_team_meta_boxes() {
    add_meta_box( 'lbs_team_details',  __( '👤 Profile Details', 'luxe-business-suite' ), 'lbs_mb_team_details',  'lbs_team_member', 'normal', 'high' );
    add_meta_box( 'lbs_team_social',   __( '🔗 Social Links',    'luxe-business-suite' ), 'lbs_mb_team_social',   'lbs_team_member', 'side',   'default' );
    add_meta_box( 'lbs_team_display',  __( '⚙ Display Options', 'luxe-business-suite' ), 'lbs_mb_team_display',  'lbs_team_member', 'side',   'high' );
}

function lbs_mb_team_details( $post ) {
    wp_nonce_field( 'lbs_team_save', 'lbs_team_nonce' );
    $fields = array(
        'lbs_team_role'       => array( 'label' => 'Job Title / Role',      'type' => 'text',     'ph' => 'Lead Interior Designer' ),
        'lbs_team_email'      => array( 'label' => 'Work Email',             'type' => 'email',    'ph' => 'rina@luxeinterior.com' ),
        'lbs_team_phone'      => array( 'label' => 'Direct Phone',           'type' => 'text',     'ph' => '+880 17 XXXX XXXX' ),
        'lbs_team_location'   => array( 'label' => 'Based In',               'type' => 'text',     'ph' => 'Dhaka, Bangladesh' ),
        'lbs_team_joined'     => array( 'label' => 'Joined (Year)',          'type' => 'number',   'ph' => '2018' ),
        'lbs_team_education'  => array( 'label' => 'Education / Credentials','type' => 'text',     'ph' => 'BA Interior Architecture, BUET' ),
        'lbs_team_quote'      => array( 'label' => 'Personal Quote',         'type' => 'textarea', 'ph' => 'A short quote that captures their philosophy…' ),
        'lbs_team_fun_fact'   => array( 'label' => 'Fun Fact',               'type' => 'text',     'ph' => 'Has visited 40 countries for design inspiration' ),
    );
    echo '<div class="lbs-meta-grid">';
    foreach ( $fields as $key => $f ) {
        lbs_meta_field( array(
            'id' => $key, 'label' => $f['label'], 'type' => $f['type'],
            'value' => get_post_meta( $post->ID, $key, true ), 'placeholder' => $f['ph'],
        ) );
    }
    echo '</div>';
}

function lbs_mb_team_social( $post ) {
    $networks = array(
        'lbs_team_linkedin'  => array( 'label' => 'LinkedIn URL',  'ph' => 'https://linkedin.com/in/…' ),
        'lbs_team_instagram' => array( 'label' => 'Instagram URL', 'ph' => 'https://instagram.com/…' ),
        'lbs_team_website'   => array( 'label' => 'Personal Website', 'ph' => 'https://…' ),
    );
    foreach ( $networks as $key => $f ) {
        lbs_meta_field( array( 'id' => $key, 'label' => $f['label'], 'type' => 'url', 'value' => get_post_meta( $post->ID, $key, true ), 'placeholder' => $f['ph'] ) );
    }
}

function lbs_mb_team_display( $post ) {
    $founder  = get_post_meta( $post->ID, 'lbs_team_is_founder',    true );
    $featured = get_post_meta( $post->ID, 'lbs_team_is_featured',   true );
    $hide     = get_post_meta( $post->ID, 'lbs_team_hide',          true );

    lbs_meta_field( array( 'id' => 'lbs_team_is_founder',  'label' => 'Founder Badge', 'type' => 'checkbox', 'value' => $founder,  'placeholder' => 'Show "Founder" badge on card' ) );
    lbs_meta_field( array( 'id' => 'lbs_team_is_featured', 'label' => 'Featured',      'type' => 'checkbox', 'value' => $featured, 'placeholder' => 'Feature on homepage team section' ) );
    lbs_meta_field( array( 'id' => 'lbs_team_hide',        'label' => 'Hide',          'type' => 'checkbox', 'value' => $hide,     'placeholder' => 'Hide from frontend (draft-style)' ) );
}

function lbs_save_team_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_team_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_team_nonce'], 'lbs_team_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $text_fields = array( 'lbs_team_role','lbs_team_phone','lbs_team_location','lbs_team_joined','lbs_team_education','lbs_team_fun_fact' );
    foreach ( $text_fields as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[$f] ) );
    }
    $url_fields = array( 'lbs_team_linkedin','lbs_team_instagram','lbs_team_website' );
    foreach ( $url_fields as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, esc_url_raw( $_POST[$f] ) );
    }
    if ( isset( $_POST['lbs_team_email'] ) ) update_post_meta( $post_id, 'lbs_team_email', sanitize_email( $_POST['lbs_team_email'] ) );
    if ( isset( $_POST['lbs_team_quote'] ) ) update_post_meta( $post_id, 'lbs_team_quote', sanitize_textarea_field( $_POST['lbs_team_quote'] ) );

    foreach ( array( 'lbs_team_is_founder','lbs_team_is_featured','lbs_team_hide' ) as $cb ) {
        update_post_meta( $post_id, $cb, isset( $_POST[$cb] ) ? '1' : '0' );
    }
}

/* ============================================================
   AWARD META BOXES
============================================================ */
add_action( 'add_meta_boxes', 'lbs_award_meta_boxes' );
add_action( 'save_post_lbs_award', 'lbs_save_award_meta', 10, 2 );

function lbs_award_meta_boxes() {
    add_meta_box( 'lbs_award_info', __( '🏆 Award Details', 'luxe-business-suite' ), 'lbs_mb_award_info', 'lbs_award', 'normal', 'high' );
}

function lbs_mb_award_info( $post ) {
    wp_nonce_field( 'lbs_award_save', 'lbs_award_nonce' );
    $fields = array(
        'lbs_award_year'     => array( 'label' => 'Year',          'type' => 'number', 'ph' => date('Y') ),
        'lbs_award_org'      => array( 'label' => 'Awarding Body', 'type' => 'text',   'ph' => 'Design & Decor Awards Bangladesh' ),
        'lbs_award_category' => array( 'label' => 'Category',      'type' => 'text',   'ph' => 'Best Residential Interior' ),
        'lbs_award_project'  => array( 'label' => 'Related Project','type' => 'text',   'ph' => 'Gulshan Villa Renovation' ),
    );
    echo '<div class="lbs-meta-grid">';
    foreach ( $fields as $key => $f ) {
        lbs_meta_field( array( 'id' => $key, 'label' => $f['label'], 'type' => $f['type'], 'value' => get_post_meta( $post->ID, $key, true ), 'placeholder' => $f['ph'] ) );
    }
    echo '</div>';
}

function lbs_save_award_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_award_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_award_nonce'], 'lbs_award_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    foreach ( array( 'lbs_award_year','lbs_award_org','lbs_award_category','lbs_award_project' ) as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[$f] ) );
    }
}

/* ============================================================
   PRESS META BOXES
============================================================ */
add_action( 'add_meta_boxes', 'lbs_press_meta_boxes' );
add_action( 'save_post_lbs_press', 'lbs_save_press_meta', 10, 2 );

function lbs_press_meta_boxes() {
    add_meta_box( 'lbs_press_info', __( '📰 Press Details', 'luxe-business-suite' ), 'lbs_mb_press_info', 'lbs_press', 'normal', 'high' );
}

function lbs_mb_press_info( $post ) {
    wp_nonce_field( 'lbs_press_save', 'lbs_press_nonce' );
    $fields = array(
        'lbs_press_publication' => array( 'label' => 'Publication',   'type' => 'text', 'ph' => 'Architectural Digest' ),
        'lbs_press_date'        => array( 'label' => 'Date',          'type' => 'date', 'ph' => '' ),
        'lbs_press_url'         => array( 'label' => 'Article URL',   'type' => 'url',  'ph' => 'https://…' ),
        'lbs_press_quote'       => array( 'label' => 'Pull Quote',    'type' => 'textarea', 'ph' => 'Short quote from the article…' ),
    );
    echo '<div class="lbs-meta-grid">';
    foreach ( $fields as $key => $f ) {
        lbs_meta_field( array( 'id' => $key, 'label' => $f['label'], 'type' => $f['type'], 'value' => get_post_meta( $post->ID, $key, true ), 'placeholder' => $f['ph'] ) );
    }

    // Logo image picker
    $logo_id  = get_post_meta( $post->ID, 'lbs_press_logo', true );
    $logo_src = $logo_id ? wp_get_attachment_image_url( (int) $logo_id, 'thumbnail' ) : '';
    echo '<div class="lbs-field"><label class="lbs-label">Publication Logo</label>';
    echo '<div class="lbs-img-preview" style="width:120px;height:60px;background:#f6f7f7;border:1px dashed #ddd;display:flex;align-items:center;justify-content:center;margin-bottom:6px;overflow:hidden;">';
    echo $logo_src ? '<img src="' . esc_url($logo_src) . '" style="max-width:100%;max-height:100%;object-fit:contain;">' : '<span style="font-size:11px;color:#aaa;">No logo</span>';
    echo '</div>';
    echo '<input type="hidden" name="lbs_press_logo" id="lbs_press_logo" value="' . esc_attr($logo_id) . '">';
    echo '<button type="button" class="button lbs-pick-image" data-target="lbs_press_logo">Select Logo</button></div>';
    echo '</div>';
}

function lbs_save_press_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_press_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_press_nonce'], 'lbs_press_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    foreach ( array( 'lbs_press_publication','lbs_press_date','lbs_press_logo' ) as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[$f] ) );
    }
    if ( isset( $_POST['lbs_press_url'] ) )   update_post_meta( $post_id, 'lbs_press_url',   esc_url_raw( $_POST['lbs_press_url'] ) );
    if ( isset( $_POST['lbs_press_quote'] ) ) update_post_meta( $post_id, 'lbs_press_quote', sanitize_textarea_field( $_POST['lbs_press_quote'] ) );
}

/* ============================================================
   RENDER HELPERS
============================================================ */
function lbs_render_team_card( int $post_id ) {
    $name     = get_the_title( $post_id );
    $role     = get_post_meta( $post_id, 'lbs_team_role',       true );
    $quote    = get_post_meta( $post_id, 'lbs_team_quote',      true );
    $linkedin = get_post_meta( $post_id, 'lbs_team_linkedin',   true );
    $insta    = get_post_meta( $post_id, 'lbs_team_instagram',  true );
    $founder  = get_post_meta( $post_id, 'lbs_team_is_founder', true );
    $thumb    = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, array(480,600), array( 'loading' => 'lazy' ) ) : '<div class="lbs-team-placeholder"></div>';
    $specs    = get_the_terms( $post_id, 'lbs_specialism' );
    ?>
    <div class="lbs-team-card">
        <div class="lbs-team-card__img">
            <?php echo $thumb; ?>
            <?php if ( $founder ) : ?>
                <span class="lbs-team-card__badge"><?php _e( 'Founder', 'luxe-business-suite' ); ?></span>
            <?php endif; ?>
        </div>
        <div class="lbs-team-card__body">
            <h4 class="lbs-team-card__name"><?php echo esc_html( $name ); ?></h4>
            <?php if ( $role ) : ?>
                <p class="lbs-team-card__role"><?php echo esc_html( $role ); ?></p>
            <?php endif; ?>
            <?php if ( $specs && ! is_wp_error( $specs ) ) : ?>
                <div class="lbs-team-card__specs">
                    <?php foreach ( array_slice( $specs, 0, 3 ) as $s ) : ?>
                        <span class="lbs-tag"><?php echo esc_html( $s->name ); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ( $quote ) : ?>
                <blockquote class="lbs-team-card__quote">"<?php echo esc_html( $quote ); ?>"</blockquote>
            <?php endif; ?>
            <?php if ( $linkedin || $insta ) : ?>
                <div class="lbs-team-card__social">
                    <?php if ( $linkedin ) : ?><a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">in</a><?php endif; ?>
                    <?php if ( $insta )    : ?><a href="<?php echo esc_url($insta);    ?>" target="_blank" rel="noopener" aria-label="Instagram">ig</a><?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/* ============================================================
   SHORTCODES
============================================================ */
add_shortcode( 'lbs_team',     'lbs_sc_team' );
add_shortcode( 'lbs_awards',   'lbs_sc_awards' );
add_shortcode( 'lbs_press',    'lbs_sc_press' );
add_shortcode( 'lbs_timeline', 'lbs_sc_timeline' );

function lbs_sc_team( $atts ) {
    $a = shortcode_atts( array( 'count' => -1, 'featured' => 'false', 'columns' => 3 ), $atts );
    $args = array(
        'post_type'      => 'lbs_team_member',
        'posts_per_page' => (int) $a['count'],
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'OR',
            array( 'key' => 'lbs_team_hide', 'compare' => 'NOT EXISTS' ),
            array( 'key' => 'lbs_team_hide', 'value'   => '1', 'compare' => '!=' ),
        ),
    );
    if ( $a['featured'] === 'true' ) {
        $args['meta_query'][] = array( 'key' => 'lbs_team_is_featured', 'value' => '1' );
    }
    $q = new WP_Query( $args );
    if ( ! $q->have_posts() ) return '';

    $cols = in_array( (int) $a['columns'], array(2,3,4), true ) ? (int) $a['columns'] : 3;
    ob_start();
    echo '<div class="lbs-team-grid lbs-grid--cols-' . esc_attr( $cols ) . '">';
    while ( $q->have_posts() ) { $q->the_post(); lbs_render_team_card( get_the_ID() ); }
    wp_reset_postdata();
    echo '</div>';
    return ob_get_clean();
}

function lbs_sc_awards( $atts ) {
    $a = shortcode_atts( array( 'count' => -1 ), $atts );
    $awards = get_posts( array( 'post_type' => 'lbs_award', 'numberposts' => (int)$a['count'], 'orderby' => 'meta_value_num', 'meta_key' => 'lbs_award_year', 'order' => 'DESC' ) );
    if ( empty( $awards ) ) return '';
    ob_start();
    echo '<div class="lbs-awards-list">';
    foreach ( $awards as $award ) :
        $year = get_post_meta( $award->ID, 'lbs_award_year',     true );
        $org  = get_post_meta( $award->ID, 'lbs_award_org',      true );
        $cat  = get_post_meta( $award->ID, 'lbs_award_category', true );
        $proj = get_post_meta( $award->ID, 'lbs_award_project',  true );
    ?>
    <div class="lbs-award-item">
        <div class="lbs-award-year"><?php echo esc_html( $year ); ?></div>
        <div class="lbs-award-body">
            <strong class="lbs-award-title"><?php echo esc_html( get_the_title( $award->ID ) ); ?></strong>
            <?php if ( $cat ) : ?><span class="lbs-award-cat"><?php echo esc_html( $cat ); ?></span><?php endif; ?>
            <?php if ( $org ) : ?><span class="lbs-award-org"><?php echo esc_html( $org ); ?></span><?php endif; ?>
            <?php if ( $proj ) : ?><span class="lbs-award-proj"><?php echo esc_html( $proj ); ?></span><?php endif; ?>
        </div>
        <div class="lbs-award-icon">🏆</div>
    </div>
    <?php endforeach;
    echo '</div>';
    return ob_get_clean();
}

function lbs_sc_press( $atts ) {
    $a = shortcode_atts( array( 'count' => -1 ), $atts );
    $items = get_posts( array( 'post_type' => 'lbs_press', 'numberposts' => (int)$a['count'], 'orderby' => 'menu_order', 'order' => 'ASC' ) );
    if ( empty( $items ) ) return '';
    ob_start();
    echo '<div class="lbs-press-grid">';
    foreach ( $items as $item ) :
        $url   = get_post_meta( $item->ID, 'lbs_press_url',         true );
        $pub   = get_post_meta( $item->ID, 'lbs_press_publication', true );
        $quote = get_post_meta( $item->ID, 'lbs_press_quote',       true );
        $logo  = get_post_meta( $item->ID, 'lbs_press_logo',        true );
        $date  = get_post_meta( $item->ID, 'lbs_press_date',        true );
        $logo_img = $logo ? wp_get_attachment_image( (int)$logo, array(160,60), false, array( 'loading' => 'lazy', 'style' => 'max-height:48px;object-fit:contain;' ) ) : '';
    ?>
    <div class="lbs-press-item">
        <?php if ( $logo_img ) : ?>
            <div class="lbs-press-logo"><?php echo $logo_img; ?></div>
        <?php else : ?>
            <div class="lbs-press-pub-name"><?php echo esc_html( $pub ?: get_the_title( $item->ID ) ); ?></div>
        <?php endif; ?>
        <?php if ( $quote ) : ?><blockquote class="lbs-press-quote">"<?php echo esc_html( $quote ); ?>"</blockquote><?php endif; ?>
        <?php if ( $date  ) : ?><span class="lbs-press-date"><?php echo esc_html( date_i18n( 'M Y', strtotime($date) ) ); ?></span><?php endif; ?>
        <?php if ( $url   ) : ?><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" class="lbs-press-link"><?php _e( 'Read Article', 'luxe-business-suite' ); ?> ↗</a><?php endif; ?>
    </div>
    <?php endforeach;
    echo '</div>';
    return ob_get_clean();
}

function lbs_sc_timeline( $atts ) {
    // Timeline built from awards + press + team join years
    $a = shortcode_atts( array( 'count' => 10 ), $atts );

    $events = array();

    // Awards as events
    $awards = get_posts( array( 'post_type' => 'lbs_award', 'numberposts' => -1 ) );
    foreach ( $awards as $aw ) {
        $yr = get_post_meta( $aw->ID, 'lbs_award_year', true );
        if ( $yr ) $events[] = array( 'year' => (int)$yr, 'type' => 'award', 'title' => get_the_title($aw->ID), 'sub' => get_post_meta($aw->ID,'lbs_award_org',true) );
    }

    // Custom timeline items stored as options
    $milestones = get_option( 'lbs_timeline_milestones', array() );
    foreach ( $milestones as $m ) {
        $events[] = array( 'year' => (int)($m['year']??0), 'type' => 'milestone', 'title' => $m['title']??'', 'sub' => $m['sub']??'' );
    }

    usort( $events, fn($a,$b) => $b['year'] - $a['year'] );
    $events = array_slice( $events, 0, (int)$a['count'] );

    if ( empty( $events ) ) return '<p class="lbs-hint">' . __( 'No timeline events yet. Add awards or milestones.', 'luxe-business-suite' ) . '</p>';

    ob_start();
    echo '<div class="lbs-timeline">';
    foreach ( $events as $event ) :
        $icon = $event['type'] === 'award' ? '🏆' : '✦';
    ?>
    <div class="lbs-timeline-item lbs-timeline-<?php echo esc_attr($event['type']); ?>">
        <div class="lbs-timeline-year"><?php echo esc_html( $event['year'] ); ?></div>
        <div class="lbs-timeline-dot"><?php echo $icon; ?></div>
        <div class="lbs-timeline-content">
            <strong><?php echo esc_html( $event['title'] ); ?></strong>
            <?php if ( $event['sub'] ) : ?>
                <span class="lbs-timeline-sub"><?php echo esc_html( $event['sub'] ); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach;
    echo '</div>';
    return ob_get_clean();
}
