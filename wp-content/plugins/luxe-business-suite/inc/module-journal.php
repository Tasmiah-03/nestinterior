<?php
/**
 * Module 5 — Journal Enhancements
 * inc/module-journal.php
 *
 * • Reading time auto-calculation
 * • Post series taxonomy + navigation
 * • Table of contents (sticky sidebar)
 * • Author rich bio box
 * • AJAX live search
 * • Inline newsletter sign-up CTA
 * • Shortcodes: [lbs_journal_search], [lbs_newsletter_cta]
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   REGISTER POST SERIES TAXONOMY
============================================================ */
add_action( 'init', 'lbs_journal_register' );

function lbs_journal_register() {
    register_taxonomy( 'lbs_post_series', array( 'post' ), array(
        'labels'       => array(
            'name'          => __( 'Post Series',   'luxe-business-suite' ),
            'singular_name' => __( 'Series',        'luxe-business-suite' ),
            'add_new_item'  => __( 'Add New Series','luxe-business-suite' ),
            'menu_name'     => __( 'Post Series',   'luxe-business-suite' ),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'series' ),
        'show_in_rest'      => true,
    ) );
}

/* ============================================================
   POST META BOXES (reading time override, featured image caption)
============================================================ */
add_action( 'add_meta_boxes', 'lbs_journal_meta_boxes' );
add_action( 'save_post',      'lbs_save_journal_meta', 10, 2 );

function lbs_journal_meta_boxes() {
    add_meta_box( 'lbs_journal_meta', __( '✏️ Journal Settings', 'luxe-business-suite' ), 'lbs_mb_journal_settings', 'post', 'side', 'default' );
}

function lbs_mb_journal_settings( $post ) {
    wp_nonce_field( 'lbs_journal_meta_save', 'lbs_journal_meta_nonce' );
    $rt_override   = get_post_meta( $post->ID, 'lbs_reading_time_override', true );
    $toc_disable   = get_post_meta( $post->ID, 'lbs_disable_toc',           true );
    $nl_disable    = get_post_meta( $post->ID, 'lbs_disable_newsletter',    true );
    $hero_caption  = get_post_meta( $post->ID, 'lbs_hero_caption',          true );
    $series_pos    = get_post_meta( $post->ID, 'lbs_series_position',       true );

    echo '<div class="lbs-field"><label class="lbs-label">Reading Time Override</label>';
    echo '<input type="text" name="lbs_reading_time_override" value="' . esc_attr($rt_override) . '" class="lbs-input" placeholder="Auto (e.g. 5 min read)"><p class="lbs-hint">Leave blank to auto-calculate.</p></div>';

    echo '<div class="lbs-field"><label class="lbs-label">Series Position</label>';
    echo '<input type="number" name="lbs_series_position" value="' . esc_attr($series_pos) . '" class="lbs-input" placeholder="1" min="1"><p class="lbs-hint">Order within a post series.</p></div>';

    echo '<div class="lbs-field"><label class="lbs-label">Hero Image Caption</label>';
    echo '<input type="text" name="lbs_hero_caption" value="' . esc_attr($hero_caption) . '" class="lbs-input" placeholder="Photography: Rina C."></div>';

    echo '<div class="lbs-field"><label class="lbs-checkbox"><input type="checkbox" name="lbs_disable_toc" value="1"' . checked($toc_disable,'1',false) . '> Disable Table of Contents</label></div>';
    echo '<div class="lbs-field"><label class="lbs-checkbox"><input type="checkbox" name="lbs_disable_newsletter" value="1"' . checked($nl_disable,'1',false) . '> Disable Newsletter CTA</label></div>';
}

function lbs_save_journal_meta( $post_id, $post ) {
    if ( $post->post_type !== 'post' ) return;
    if ( ! isset( $_POST['lbs_journal_meta_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_journal_meta_nonce'], 'lbs_journal_meta_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    foreach ( array( 'lbs_reading_time_override','lbs_hero_caption','lbs_series_position' ) as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[$f] ) );
    }
    foreach ( array( 'lbs_disable_toc','lbs_disable_newsletter' ) as $cb ) {
        update_post_meta( $post_id, $cb, isset( $_POST[$cb] ) ? '1' : '0' );
    }
}

/* ============================================================
   READING TIME FILTER (adds to_the_excerpt / post meta)
============================================================ */
add_filter( 'the_content', 'lbs_inject_reading_time_tag', 1 );

function lbs_inject_reading_time_tag( string $content ) : string {
    if ( ! is_single() || get_post_type() !== 'post' ) return $content;
    return $content; // reading time displayed via template functions, not injected into content
}

function lbs_get_reading_time( int $post_id = 0 ) : string {
    $post_id = $post_id ?: get_the_ID();
    $override = get_post_meta( $post_id, 'lbs_reading_time_override', true );
    if ( $override ) return esc_html( $override );
    return lbs_reading_time( $post_id );
}

/* ============================================================
   TABLE OF CONTENTS GENERATOR
============================================================ */
add_filter( 'the_content', 'lbs_generate_toc', 20 );

function lbs_generate_toc( string $content ) : string {
    if ( ! is_single() || get_post_type() !== 'post' ) return $content;
    if ( get_post_meta( get_the_ID(), 'lbs_disable_toc', true ) === '1' ) return $content;

    // Extract H2 and H3 headings
    preg_match_all( '/<h([23])([^>]*)>(.*?)<\/h[23]>/i', $content, $matches, PREG_SET_ORDER );
    if ( count( $matches ) < 3 ) return $content; // skip TOC for short posts

    $toc   = '<nav class="lbs-toc" aria-label="' . esc_attr__('Table of Contents','luxe-business-suite') . '">';
    $toc  .= '<div class="lbs-toc__header"><span>' . __('Contents','luxe-business-suite') . '</span><button class="lbs-toc__toggle" aria-expanded="true">–</button></div>';
    $toc  .= '<ol class="lbs-toc__list">';
    $i     = 0;

    foreach ( $matches as $match ) {
        $level = (int) $match[1];
        $text  = wp_strip_all_tags( $match[3] );
        $slug  = 'lbs-heading-' . $i++;
        $anchor_attr = ' id="' . esc_attr($slug) . '"';

        // Add id to heading in content
        $content = str_replace( $match[0], '<h' . $level . $match[2] . $anchor_attr . '>' . $match[3] . '</h' . $level . '>', $content );

        $indent = $level === 3 ? ' style="margin-left:1.25rem;"' : '';
        $toc   .= '<li' . $indent . '><a href="#' . esc_attr($slug) . '">' . esc_html($text) . '</a></li>';
    }

    $toc .= '</ol></nav>';
    return $toc . $content;
}

/* ============================================================
   SERIES NAVIGATION
============================================================ */
add_action( 'the_content', 'lbs_series_navigation_append', 30 );

function lbs_series_navigation_append( string $content ) : string {
    if ( ! is_single() || get_post_type() !== 'post' ) return $content;

    $series = get_the_terms( get_the_ID(), 'lbs_post_series' );
    if ( ! $series || is_wp_error( $series ) ) return $content;

    $series_term = $series[0];
    $all_posts = get_posts( array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'lbs_series_position',
        'order'          => 'ASC',
        'tax_query'      => array( array( 'taxonomy' => 'lbs_post_series', 'field' => 'term_id', 'terms' => $series_term->term_id ) ),
    ) );

    if ( count( $all_posts ) < 2 ) return $content;

    $current_pos = -1;
    foreach ( $all_posts as $idx => $p ) {
        if ( $p->ID === get_the_ID() ) { $current_pos = $idx; break; }
    }

    $prev = $current_pos > 0 ? $all_posts[ $current_pos - 1 ] : null;
    $next = $current_pos < count($all_posts) - 1 ? $all_posts[ $current_pos + 1 ] : null;

    $html  = '<div class="lbs-series-nav">';
    $html .= '<div class="lbs-series-nav__header">';
    $html .= '<span class="lbs-series-nav__eyebrow">' . __('Part of a Series','luxe-business-suite') . '</span>';
    $html .= '<strong class="lbs-series-nav__title">' . esc_html($series_term->name) . '</strong>';
    $html .= '<span class="lbs-series-nav__count">' . sprintf( __('%d of %d','luxe-business-suite'), $current_pos+1, count($all_posts) ) . '</span>';
    $html .= '</div><div class="lbs-series-nav__links">';

    if ( $prev ) {
        $html .= '<a href="' . esc_url(get_permalink($prev->ID)) . '" class="lbs-series-nav__link lbs-series-nav__link--prev">';
        $html .= '<span>' . __('← Previous','luxe-business-suite') . '</span>';
        $html .= '<strong>' . esc_html(get_the_title($prev->ID)) . '</strong></a>';
    }
    if ( $next ) {
        $html .= '<a href="' . esc_url(get_permalink($next->ID)) . '" class="lbs-series-nav__link lbs-series-nav__link--next">';
        $html .= '<span>' . __('Next →','luxe-business-suite') . '</span>';
        $html .= '<strong>' . esc_html(get_the_title($next->ID)) . '</strong></a>';
    }

    $html .= '</div></div>';
    return $content . $html;
}

/* ============================================================
   RICH AUTHOR BIO BOX
============================================================ */
add_action( 'the_content', 'lbs_author_bio_append', 25 );

function lbs_author_bio_append( string $content ) : string {
    if ( ! is_single() || get_post_type() !== 'post' ) return $content;

    $author_id = get_the_author_meta( 'ID' );
    $name      = get_the_author_meta( 'display_name' );
    $bio       = get_the_author_meta( 'description' );
    $url       = get_the_author_meta( 'user_url' );
    $avatar    = get_avatar( $author_id, 96, '', $name, array( 'class' => 'lbs-author-bio__avatar' ) );

    // Custom author fields stored on user meta
    $role      = get_user_meta( $author_id, 'lbs_author_role',      true );
    $linkedin  = get_user_meta( $author_id, 'lbs_author_linkedin',  true );
    $instagram = get_user_meta( $author_id, 'lbs_author_instagram', true );

    if ( ! $bio ) return $content;

    $html  = '<div class="lbs-author-bio">';
    $html .= '<div class="lbs-author-bio__avatar-wrap">' . $avatar . '</div>';
    $html .= '<div class="lbs-author-bio__content">';
    $html .= '<span class="lbs-author-bio__eyebrow">' . __('Written by','luxe-business-suite') . '</span>';
    $html .= '<strong class="lbs-author-bio__name">' . esc_html($name) . '</strong>';
    if ( $role ) $html .= '<span class="lbs-author-bio__role">' . esc_html($role) . '</span>';
    $html .= '<p class="lbs-author-bio__desc">' . esc_html($bio) . '</p>';
    $html .= '<div class="lbs-author-bio__links">';
    if ( $url )       $html .= '<a href="' . esc_url($url)       . '" target="_blank" rel="noopener">' . __('Website','luxe-business-suite')   . ' ↗</a>';
    if ( $linkedin )  $html .= '<a href="' . esc_url($linkedin)  . '" target="_blank" rel="noopener">LinkedIn ↗</a>';
    if ( $instagram ) $html .= '<a href="' . esc_url($instagram) . '" target="_blank" rel="noopener">Instagram ↗</a>';
    $html .= '</div></div></div>';

    return $content . $html;
}

/* ============================================================
   NEWSLETTER INLINE CTA (injected mid-content)
============================================================ */
add_filter( 'the_content', 'lbs_inject_newsletter_cta', 22 );

function lbs_inject_newsletter_cta( string $content ) : string {
    if ( ! is_single() || get_post_type() !== 'post' ) return $content;
    if ( get_post_meta( get_the_ID(), 'lbs_disable_newsletter', true ) === '1' ) return $content;

    $general = get_option( 'lbs_general', array() );
    if ( empty( $general['mailchimp_key'] ) && empty( $general['mailchimp_list'] ) ) return $content;

    // Insert after paragraph 4 (or at end if < 4 paragraphs)
    $paragraphs = explode( '</p>', $content );
    $insert_at  = min( 4, count($paragraphs) - 1 );
    $cta        = lbs_newsletter_cta_html();

    $paragraphs[ $insert_at ] .= $cta;
    return implode( '</p>', $paragraphs );
}

function lbs_newsletter_cta_html() : string {
    ob_start(); ?>
    <div class="lbs-nl-cta">
        <div class="lbs-nl-cta__inner">
            <div class="lbs-nl-cta__text">
                <strong><?php _e( 'Stay Inspired', 'luxe-business-suite' ); ?></strong>
                <span><?php _e( 'Design insights and project reveals — straight to your inbox.', 'luxe-business-suite' ); ?></span>
            </div>
            <form class="lbs-nl-cta__form lbs-newsletter-form" data-source="inline">
                <?php wp_nonce_field( 'lbs_newsletter', 'lbs_nl_nonce' ); ?>
                <input type="email" name="lbs_nl_email" required placeholder="<?php esc_attr_e('Your email…','luxe-business-suite'); ?>" class="lbs-nl-input">
                <button type="submit" class="btn btn-accent lbs-nl-btn"><?php _e('Subscribe','luxe-business-suite'); ?></button>
            </form>
            <p class="lbs-nl-msg" hidden></p>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ============================================================
   NEWSLETTER AJAX HANDLER
============================================================ */
add_action( 'wp_ajax_lbs_newsletter_subscribe',        'lbs_ajax_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_lbs_newsletter_subscribe', 'lbs_ajax_newsletter_subscribe' );

function lbs_ajax_newsletter_subscribe() {
    check_ajax_referer( 'lbs_ajax', 'nonce' );
    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( ! is_email( $email ) ) {
        wp_send_json_error( __('Please enter a valid email address.','luxe-business-suite') );
    }

    $general   = get_option( 'lbs_general', array() );
    $api_key   = $general['mailchimp_key']  ?? '';
    $list_id   = $general['mailchimp_list'] ?? '';

    if ( $api_key && $list_id ) {
        $dc  = substr( $api_key, strpos( $api_key, '-' ) + 1 );
        $url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$list_id}/members";
        wp_remote_post( $url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
                'Content-Type'  => 'application/json',
            ),
            'body' => wp_json_encode( array( 'email_address' => $email, 'status' => 'subscribed' ) ),
        ) );
    } else {
        // Fallback: store locally
        $subs = get_option( 'lbs_newsletter_subscribers', array() );
        if ( ! in_array( $email, $subs, true ) ) {
            $subs[] = $email;
            update_option( 'lbs_newsletter_subscribers', $subs );
        }
    }

    wp_send_json_success( __( 'You\'re subscribed! Thank you.', 'luxe-business-suite' ) );
}

/* ============================================================
   AJAX LIVE SEARCH
============================================================ */
add_action( 'wp_ajax_lbs_journal_search',        'lbs_ajax_journal_search' );
add_action( 'wp_ajax_nopriv_lbs_journal_search', 'lbs_ajax_journal_search' );

function lbs_ajax_journal_search() {
    check_ajax_referer( 'lbs_ajax', 'nonce' );
    $term = sanitize_text_field( $_POST['term'] ?? '' );
    if ( strlen( $term ) < 2 ) wp_send_json_success( array() );

    $q = new WP_Query( array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        's'              => $term,
        'posts_per_page' => 8,
    ) );
    $results = array();
    while ( $q->have_posts() ) {
        $q->the_post();
        $results[] = array(
            'id'      => get_the_ID(),
            'title'   => get_the_title(),
            'url'     => get_permalink(),
            'excerpt' => wp_trim_words( get_the_excerpt(), 15 ),
            'date'    => get_the_date( 'd M Y' ),
            'thumb'   => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'thumbnail' ) : '',
        );
    }
    wp_reset_postdata();
    wp_send_json_success( $results );
}

/* ============================================================
   SHORTCODES
============================================================ */
add_shortcode( 'lbs_journal_search',  'lbs_sc_journal_search' );
add_shortcode( 'lbs_newsletter_cta',  'lbs_sc_newsletter_cta' );
add_shortcode( 'lbs_reading_time',    'lbs_sc_reading_time' );

function lbs_sc_journal_search( $atts ) {
    ob_start(); ?>
    <div class="lbs-journal-search">
        <div class="lbs-search-wrap">
            <input type="search" id="lbs-search-input" class="lbs-search-input"
                   placeholder="<?php esc_attr_e('Search journal posts…','luxe-business-suite'); ?>"
                   autocomplete="off" aria-label="<?php esc_attr_e('Search','luxe-business-suite'); ?>">
            <span class="lbs-search-icon" aria-hidden="true">⌕</span>
        </div>
        <div id="lbs-search-results" class="lbs-search-results" hidden aria-live="polite"></div>
    </div>
    <?php return ob_get_clean();
}

function lbs_sc_newsletter_cta() {
    return lbs_newsletter_cta_html();
}

function lbs_sc_reading_time( $atts ) {
    $a = shortcode_atts( array( 'id' => get_the_ID() ), $atts );
    return '<span class="lbs-reading-time">' . lbs_get_reading_time( (int)$a['id'] ) . '</span>';
}

/* ============================================================
   ARCHIVE READING TIME + SERIES in post meta
============================================================ */
add_action( 'wp_head', function () {
    if ( ! is_single() || get_post_type() !== 'post' ) return;
    $rt = lbs_get_reading_time( get_the_ID() );
    echo '<meta name="twitter:label1" content="Reading time"><meta name="twitter:data1" content="' . esc_attr($rt) . '">' . "\n";
} );
