<?php
/**
 * Luxe Interior Theme Functions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================
// THEME SETUP
// ============================================================
function luxe_setup() {
    load_theme_textdomain( 'luxe-interior', get_template_directory() . '/languages' );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );

    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 240,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Image sizes
    add_image_size( 'luxe-hero',      1920, 1080, true );
    add_image_size( 'luxe-portfolio', 900,  700,  true );
    add_image_size( 'luxe-card',      800,  600,  true );
    add_image_size( 'luxe-thumb',     400,  300,  true );
    add_image_size( 'luxe-wide',      1200, 600,  true );

    // Menus
    register_nav_menus( array(
        'primary'  => __( 'Primary Navigation', 'luxe-interior' ),
        'footer'   => __( 'Footer Navigation', 'luxe-interior' ),
        'social'   => __( 'Social Links', 'luxe-interior' ),
    ));
}
add_action( 'after_setup_theme', 'luxe_setup' );

// ============================================================
// ENSURE PORTFOLIO PAGE EXISTS
// ============================================================
function luxe_create_portfolio_page() {
    if ( ! get_page_by_path('portfolio') ) {
        $page_id = wp_insert_post( array(
            'post_title'    => 'Portfolio',
            'post_name'     => 'portfolio',
            'post_type'     => 'page',
            'post_status'   => 'publish',
        ));
        if ( $page_id ) {
            update_post_meta( $page_id, '_wp_page_template', 'page-portfolio.php' );
        }
    }
}
add_action( 'init', 'luxe_create_portfolio_page', 999 );

// ============================================================
// FORCE PORTFOLIO TEMPLATE
// ============================================================
function luxe_portfolio_template( $template ) {
    if ( is_page('portfolio') ) {
        $new_template = locate_template( array( 'page-portfolio.php' ) );
        if ( $new_template ) return $new_template;
    }
    return $template;
}
add_filter( 'template_include', 'luxe_portfolio_template' );

// ============================================================
// ENQUEUE SCRIPTS & STYLES
// ============================================================
function luxe_scripts() {
    // Google Fonts
    wp_enqueue_style(
        'luxe-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Outfit:wght@300;400;500;600&display=swap',
        array(), null
    );

    // Main Stylesheet
    wp_enqueue_style( 'luxe-style', get_stylesheet_uri(), array( 'luxe-fonts' ), '1.0.0' );

    // Main JS
    wp_enqueue_script( 'luxe-main', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true );

    // Comments
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Pass data to JS
    wp_localize_script( 'luxe-main', 'luxeData', array(
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'luxe_nonce' ),
        'siteUrl'   => get_site_url(),
    ));
}
add_action( 'wp_enqueue_scripts', 'luxe_scripts' );

// ============================================================
// CUSTOM POST TYPES
// ============================================================

// Portfolio CPT
function luxe_register_post_types() {
    register_post_type( 'portfolio', array(
        'labels' => array(
            'name'               => __( 'Portfolio', 'luxe-interior' ),
            'singular_name'      => __( 'Project', 'luxe-interior' ),
            'add_new'            => __( 'Add New Project', 'luxe-interior' ),
            'add_new_item'       => __( 'Add New Project', 'luxe-interior' ),
            'edit_item'          => __( 'Edit Project', 'luxe-interior' ),
            'new_item'           => __( 'New Project', 'luxe-interior' ),
            'view_item'          => __( 'View Project', 'luxe-interior' ),
            'search_items'       => __( 'Search Projects', 'luxe-interior' ),
            'not_found'          => __( 'No projects found', 'luxe-interior' ),
            'not_found_in_trash' => __( 'No projects found in trash', 'luxe-interior' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'projects', 'with_front' => false ),
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'menu_icon'    => 'dashicons-admin-home',
        'show_in_rest' => true,
    ));

    // Testimonials CPT
    register_post_type( 'testimonial', array(
        'labels' => array(
            'name'          => __( 'Testimonials', 'luxe-interior' ),
            'singular_name' => __( 'Testimonial', 'luxe-interior' ),
            'add_new_item'  => __( 'Add New Testimonial', 'luxe-interior' ),
        ),
        'public'      => false,
        'show_ui'     => true,
        'supports'    => array( 'title', 'editor', 'custom-fields' ),
        'menu_icon'   => 'dashicons-format-quote',
        'show_in_rest'=> true,
    ));

    // Project Logistics CPT
    register_post_type( 'project_logistics', array(
        'labels' => array(
            'name'               => __( 'Logistics', 'luxe-interior' ),
            'singular_name'      => __( 'Project Logistic', 'luxe-interior' ),
            'add_new'            => __( 'Add Project Timeline', 'luxe-interior' ),
            'add_new_item'       => __( 'Add New Project Timeline', 'luxe-interior' ),
            'edit_item'          => __( 'Edit Timeline', 'luxe-interior' ),
            'all_items'          => __( 'Project Logistics', 'luxe-interior' ),
            'menu_name'          => __( 'Logistics', 'luxe-interior' ),
        ),
        'public'       => true,
        'show_ui'      => true,
        'show_in_menu' => true,
        'supports'     => array( 'title', 'editor', 'custom-fields' ),
        'menu_icon'    => 'dashicons-calendar-alt',
        'show_in_rest' => true,
    ));
}
add_action( 'init', 'luxe_register_post_types' );

// ============================================================
// TAXONOMIES
// ============================================================
function luxe_register_taxonomies() {
    // Portfolio Categories
    register_taxonomy( 'portfolio_category', 'portfolio', array(
        'labels' => array(
            'name'          => __( 'Project Categories', 'luxe-interior' ),
            'singular_name' => __( 'Category', 'luxe-interior' ),
            'add_new_item'  => __( 'Add New Category', 'luxe-interior' ),
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'project-type' ),
    ));
}
add_action( 'init', 'luxe_register_taxonomies' );

// ============================================================
// WIDGETS
// ============================================================
function luxe_widgets_init() {
    $sidebar_defaults = array(
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>',
    );

    register_sidebar( array_merge( $sidebar_defaults, array(
        'name' => __( 'Blog Sidebar', 'luxe-interior' ),
        'id'   => 'sidebar-1',
    )));

    register_sidebar( array_merge( $sidebar_defaults, array(
        'name' => __( 'Footer Column 1', 'luxe-interior' ),
        'id'   => 'footer-1',
    )));

    register_sidebar( array_merge( $sidebar_defaults, array(
        'name' => __( 'Footer Column 2', 'luxe-interior' ),
        'id'   => 'footer-2',
    )));
}
add_action( 'widgets_init', 'luxe_widgets_init' );

// ============================================================
// CUSTOM EXCERPT LENGTH
// ============================================================
function luxe_excerpt_length( $length ) {
    return is_admin() ? $length : 20;
}
add_filter( 'excerpt_length', 'luxe_excerpt_length' );

function luxe_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'luxe_excerpt_more' );

// ============================================================
// CUSTOM WALKER FOR NAV MENUS
// ============================================================
class Luxe_Nav_Walker extends Walker_Nav_Menu {
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $classes = implode( ' ', $item->classes );
        $is_current = in_array( 'current-menu-item', $item->classes );
        $output .= '<li class="' . esc_attr( $classes ) . '">';
        $output .= '<a href="' . esc_url( $item->url ) . '"' .
            ( $is_current ? ' class="current" aria-current="page"' : '' ) . '>' .
            esc_html( $item->title ) . '</a>';
    }
}

// ============================================================
// THEME CUSTOMIZER
// ============================================================
function luxe_customize_register( $wp_customize ) {

    // Site Identity Section already exists — add tagline enhancements

    // Colors Section
    $wp_customize->add_section( 'luxe_colors', array(
        'title'    => __( 'Theme Colors', 'luxe-interior' ),
        'priority' => 40,
    ));

    $wp_customize->add_setting( 'accent_color', array(
        'default'           => '#B08D6A',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'accent_color', array(
        'label'   => __( 'Accent Color', 'luxe-interior' ),
        'section' => 'luxe_colors',
    )));

    // Contact Info Section
    $wp_customize->add_section( 'luxe_contact', array(
        'title'    => __( 'Contact Information', 'luxe-interior' ),
        'priority' => 50,
    ));

    $contact_fields = array(
        'contact_address' => array( 'label' => 'Address', 'default' => '47 Design Avenue, Dhaka 1212, Bangladesh' ),
        'contact_phone'   => array( 'label' => 'Phone',   'default' => '+880 17 1234 5678' ),
        'contact_email'   => array( 'label' => 'Email',   'default' => 'hello@luxeinterior.com' ),
        'contact_hours'   => array( 'label' => 'Hours',   'default' => 'Mon–Sat: 9am – 6pm' ),
    );

    foreach ( $contact_fields as $key => $field ) {
        $wp_customize->add_setting( $key, array(
            'default'           => $field['default'],
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control( $key, array(
            'label'   => __( $field['label'], 'luxe-interior' ),
            'section' => 'luxe_contact',
            'type'    => 'text',
        ));
    }

    // Social Links
    $wp_customize->add_section( 'luxe_social', array(
        'title'    => __( 'Social Media Links', 'luxe-interior' ),
        'priority' => 60,
    ));

    $social_fields = array(
        'social_instagram' => array( 'label' => 'Instagram URL', 'default' => '#' ),
        'social_pinterest'  => array( 'label' => 'Pinterest URL',  'default' => '#' ),
        'social_facebook'  => array( 'label' => 'Facebook URL',  'default' => '#' ),
        'social_houzz'     => array( 'label' => 'Houzz URL',     'default' => '#' ),
    );

    foreach ( $social_fields as $key => $field ) {
        $wp_customize->add_setting( $key, array(
            'default'           => $field['default'],
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control( $key, array(
            'label'   => __( $field['label'], 'luxe-interior' ),
            'section' => 'luxe_social',
            'type'    => 'url',
        ));
    }

    // Hero Section
    $wp_customize->add_section( 'luxe_hero', array(
        'title'    => __( 'Hero Section', 'luxe-interior' ),
        'priority' => 30,
    ));

    $hero_fields = array(
        'hero_eyebrow'  => array( 'label' => 'Eyebrow Text',  'default' => 'Award-Winning Interior Design Studio' ),
        'hero_title'    => array( 'label' => 'Hero Title',    'default' => 'Crafting Spaces That <em>Inspire</em> & Endure' ),
        'hero_subtitle' => array( 'label' => 'Hero Subtitle', 'default' => 'We blend timeless aesthetics with purposeful function to transform interiors into deeply personal sanctuaries.' ),
    );

    foreach ( $hero_fields as $key => $field ) {
        $wp_customize->add_setting( $key, array(
            'default'           => $field['default'],
            'sanitize_callback' => 'wp_kses_post',
        ));
        $wp_customize->add_control( $key, array(
            'label'   => __( $field['label'], 'luxe-interior' ),
            'section' => 'luxe_hero',
            'type'    => 'text',
        ));
    }

    $wp_customize->add_setting( 'hero_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_image', array(
        'label'   => __( 'Hero Background Image', 'luxe-interior' ),
        'section' => 'luxe_hero',
    )));
}
add_action( 'customize_register', 'luxe_customize_register' );

// Output customizer CSS
function luxe_customizer_css() {
    $accent = get_theme_mod( 'accent_color', '#B08D6A' );
    echo '<style>:root{--color-accent:' . esc_attr( $accent ) . ';}</style>';
}
add_action( 'wp_head', 'luxe_customizer_css' );

// ============================================================
// HELPER FUNCTIONS
// ============================================================

function luxe_get_option( $key, $default = '' ) {
    return get_theme_mod( $key, $default );
}

function luxe_social_links() {
    $instagram_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>';
    $pinterest_svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>';
    $facebook_svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>';
    $houzz_svg     = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M 4 0 L 4 11.5 L 12 7.5 L 20 11.5 L 20 24 L 13 24 L 13 16 L 11 16 L 11 24 L 4 24 L 4 13 L 0 15 L 0 11.5 Z"/></svg>';
    $links = array(
        'instagram' => array( 'url' => get_theme_mod( 'social_instagram', '#' ), 'label' => 'Instagram', 'icon' => $instagram_svg ),
        'pinterest'  => array( 'url' => get_theme_mod( 'social_pinterest',  '#' ), 'label' => 'Pinterest',  'icon' => $pinterest_svg ),
        'facebook'  => array( 'url' => get_theme_mod( 'social_facebook',  '#' ), 'label' => 'Facebook',  'icon' => $facebook_svg ),
        'houzz'     => array( 'url' => get_theme_mod( 'social_houzz',     '#' ), 'label' => 'Houzz',     'icon' => $houzz_svg ),
    );
    return $links;
}

function luxe_get_portfolio_categories() {
    return get_terms( array(
        'taxonomy'   => 'portfolio_category',
        'hide_empty' => true,
    ));
}

function luxe_posted_on() {
    printf(
        '<span class="post-date">%s</span>',
        esc_html( get_the_date( 'M j, Y' ) )
    );
}

function luxe_posted_by() {
    printf(
        '<span class="post-author">%s</span>',
        esc_html( get_the_author() )
    );
}

// ============================================================
// AJAX: PORTFOLIO FILTER
// ============================================================
function luxe_filter_portfolio() {
    check_ajax_referer( 'luxe_nonce', 'nonce' );

    $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';

    $args = array(
        'post_type'      => 'portfolio',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
    );

    if ( $category && $category !== 'all' ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'portfolio_category',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $query = new WP_Query( $args );
    ob_start();
    while ( $query->have_posts() ) : $query->the_post();
        get_template_part( 'template-parts/content', 'portfolio-card' );
    endwhile;
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success( $html );
}
add_action( 'wp_ajax_luxe_filter_portfolio', 'luxe_filter_portfolio' );
add_action( 'wp_ajax_nopriv_luxe_filter_portfolio', 'luxe_filter_portfolio' );

// ============================================================
// SAMPLE DATA IMPORT FUNCTION (run once via WP CLI or admin)
// ============================================================
function luxe_import_sample_data() {
    if ( get_option( 'luxe_sample_data_imported' ) ) return;

    // Sample Portfolio Projects
    $projects = array(
        array(
            'title'    => 'The Meridian Penthouse',
            'excerpt'  => 'A sweeping 4,200 sq ft penthouse in Gulshan, reimagined as a sanctuary of warm minimalism. Natural stone, aged oak, and curated art pieces anchor the space.',
            'content'  => '<p>Located on the 32nd floor of a prime Gulshan tower, the Meridian Penthouse project was a masterclass in restraint. The clients, a couple who had lived in Copenhagen for a decade, wanted to bring Scandinavian warmth to Dhaka.</p><p>We sourced travertine marble from Turkey, aged oak flooring from France, and commissioned local artisans to create bespoke lighting fixtures in brass and hand-blown glass. The result is a home that feels simultaneously global and deeply rooted.</p><p>Every room connects visually to the city skyline beyond the floor-to-ceiling glazing, making Dhaka itself the ultimate piece of décor.</p>',
            'category' => 'residential',
        ),
        array(
            'title'    => 'Studio Verde — Café & Co-Working',
            'excerpt'  => 'A biophilic café and co-working hybrid in Banani. 3,000 plants, terracotta tiles, arched corridors, and a micro-bakery tucked into a courtyard garden.',
            'content'  => '<p>Studio Verde was born from a simple brief: "make it feel like being in a greenhouse in the South of France." We delivered exactly that — and more.</p><p>The space spans two floors and a rooftop garden. Arched doorways reference Moroccan riads, while the color palette of sage, terracotta, and warm cream draws from Mediterranean coastlines. We installed a living moss wall spanning 12 meters behind the espresso bar.</p>',
            'category' => 'commercial',
        ),
        array(
            'title'    => 'House Dara — Baridhara Villa',
            'excerpt'  => 'A full renovation of a 1990s villa, stripped to its bones and reborn as a contemporary family home with a resort-like pool pavilion and chef\'s kitchen.',
            'content'  => '<p>House Dara required structural imagination. The existing 1990s villa had good bones but a fragmented floor plan that worked against natural light and family flow. We removed three internal walls, elevated the kitchen to a chef-grade island layout, and created a seamless indoor-outdoor connection to the pool pavilion.</p><p>Materials were chosen for longevity: honed Nero Marquina marble, hand-made textured tiles from a Rajshahi artisan, and custom millwork in white-oiled ash.</p>',
            'category' => 'residential',
        ),
        array(
            'title'    => 'The Atelier — Fashion Boutique',
            'excerpt'  => 'An intimate luxury fashion boutique in Uttara designed as a "jewel box." Curved walls, velvet upholstery, and museum-quality lighting transform shopping into theater.',
            'content'  => '<p>The brief for The Atelier was evocative: "like walking into a Parisian jewel box." Working in a narrow 800 sq ft shopfront, we created an environment where every surface rewards close inspection.</p><p>Curved display niches are upholstered in dusty pink velvet. The floor is hand-laid herringbone in pale marble. Lighting is entirely museum-grade, highlighting garments as if they were sculptures.</p>',
            'category' => 'retail',
        ),
        array(
            'title'    => 'Lakeview Retreat — Sylhet',
            'excerpt'  => 'A weekend retreat perched above a tea garden in Sylhet. Raw concrete, burnished copper accents, and panoramic glazing frame the rolling green hills beyond.',
            'content'  => '<p>This project in the hills of Sylhet challenged us to create something that disappeared into its landscape rather than imposing upon it. Working with the natural contours of the hillside, we positioned the main pavilion to capture uninterrupted views of the tea garden below.</p><p>The material palette was kept deliberately raw: board-formed concrete, local stone, copper fixtures that will patina gracefully, and local rattan woven by craftspeople from the nearby village.</p>',
            'category' => 'residential',
        ),
        array(
            'title'    => 'Nexus Offices — Motijheel HQ',
            'excerpt'  => 'A corporate headquarters redefining Dhaka office culture — warm wood joinery, height-adjustable desks, a rooftop terrace, and quiet focus pods woven throughout.',
            'content'  => '<p>Nexus came to us with a bold mission: prove that an office in Motijheel could feel as inspiring as a tech campus in Singapore. Working across five floors of a Grade A building, we created distinct zones for focused work, collaboration, and genuine rest.</p><p>The material strategy — warm walnut joinery, terrazzo floors in the common areas, and acoustic felt panels in collaboration zones — creates a sense of quality that rivals the world\'s best offices.</p>',
            'category' => 'commercial',
        ),
    );

    $categories = array(
        array( 'name' => 'Residential', 'slug' => 'residential' ),
        array( 'name' => 'Commercial',  'slug' => 'commercial' ),
        array( 'name' => 'Retail',      'slug' => 'retail' ),
        array( 'name' => 'Hospitality', 'slug' => 'hospitality' ),
    );

    foreach ( $categories as $cat ) {
        wp_insert_term( $cat['name'], 'portfolio_category', array( 'slug' => $cat['slug'] ) );
    }

    foreach ( $projects as $project ) {
        $term = get_term_by( 'slug', $project['category'], 'portfolio_category' );
        $post_id = wp_insert_post( array(
            'post_title'   => $project['title'],
            'post_excerpt' => $project['excerpt'],
            'post_content' => $project['content'],
            'post_type'    => 'portfolio',
            'post_status'  => 'publish',
        ));
        if ( $post_id && $term ) {
            wp_set_post_terms( $post_id, array( $term->term_id ), 'portfolio_category' );
        }
    }

    // Sample Blog Posts
    $posts = array(
        array(
            'title'    => '10 Interior Design Trends Dominating 2025',
            'category' => 'Trends',
            'content'  => '<p>Interior design in 2025 is a study in beautiful contradiction. We see raw and refined living side by side — exposed concrete walls draped with hand-knotted wool tapestries, brutalist architecture softened by cascading indoor gardens, and industrial steel frames housing the warmest, most inviting living spaces imaginable.</p><h2>1. Warm Minimalism</h2><p>The cold, antiseptic minimalism of the early 2010s has given way to something far more human. Warm minimalism embraces the same commitment to simplicity and negative space, but wraps it in a palette of creams, dusty terracottas, and aged wood tones. Every object earns its place, but the room still feels alive.</p><h2>2. Biophilic Design at Scale</h2><p>Plants are no longer accent pieces — they are architectural elements. Living walls, indoor trees that pierce through double-height ceilings, and glass extensions filled with structured botanical gardens are transforming homes into genuine sanctuaries.</p><h2>3. The Return of Craftsmanship</h2><p>There is a profound hunger for the handmade. Artisan ceramics, hand-blown glass lighting, bespoke joinery, and woven textiles from local craftspeople are appearing in the most sophisticated interiors worldwide — and the story behind each object has become as important as the object itself.</p><h2>4. Curved Everything</h2><p>The straight line is out of fashion. Curved sofas, arched doorways, rounded kitchen islands, and sinuous staircases are bringing a sense of organic movement and sensuality to contemporary spaces that rigid geometry could never achieve.</p><blockquote>The best room is one that, ten years from now, you would still feel grateful to wake up inside.</blockquote><h2>5. Sustainable Luxury</h2><p>Clients increasingly refuse to choose between beautiful and responsible. Reclaimed timber, recycled metals, natural pigment paints, and furniture certified by the Forest Stewardship Council are becoming the baseline expectation rather than a premium upgrade.</p>',
            'excerpt'  => 'From warm minimalism and biophilic design to artisanal craftsmanship and curved forms — the aesthetic movements shaping the world\'s most beautiful interiors right now.',
        ),
        array(
            'title'    => 'How to Choose the Perfect Lighting for Every Room',
            'category' => 'Guides',
            'content'  => '<p>Lighting is the single most transformative element in any interior. A poorly lit space with exquisite furniture will always feel worse than a beautifully lit room with modest pieces. After two decades of designing spaces, this is perhaps the most consistent truth we have encountered.</p><h2>Understanding the Three Layers</h2><p>Professional interior designers think about lighting in three layers: ambient, task, and accent. Ambient light provides overall illumination — it fills the room. Task lighting serves a specific function: reading by a chair, chopping vegetables at an island, applying makeup at a mirror. Accent lighting highlights architecture, artwork, or objects you want people to notice.</p><p>Most domestic interiors make the mistake of relying entirely on ambient light — a single overhead fitting that attempts to do everything and succeeds at nothing. The moment you introduce multiple light sources at different heights and with different intentions, the room transforms.</p><h2>The Living Room</h2><p>A living room should never be lit from a single overhead source. Instead, build a composition: a statement pendant or chandelier establishes the room\'s scale, floor lamps beside seating create warmth and intimacy, and picture lights or LED strips inside shelving units add depth. Install everything on dimmers.</p><h2>The Kitchen</h2><p>Kitchens need excellent task lighting above work surfaces — recessed downlights or undermount LED strips beneath wall cabinets. But the kitchen also needs warmth, especially if it opens into a dining or living area. A pendant over an island, warm in color temperature (2700–3000K), bridges function and atmosphere elegantly.</p><h2>The Bedroom</h2><p>The bedroom is where most people make their biggest lighting mistake: a central overhead fitting with a switch by the door. Instead, consider wall-mounted reading lights on either side of the bed (freeing the bedside tables from lamps), a dimmer switch that you can operate from bed, and zero overhead lighting altogether.</p>',
            'excerpt'  => 'The single most transformative element in any interior is also the most misunderstood. A professional guide to layering light in every room of your home.',
        ),
        array(
            'title'    => 'The Art of the Neutral Palette: Beyond Beige',
            'category' => 'Color & Materials',
            'content'  => '<p>Few words in interior design have been as misused, misunderstood, and maligned as "neutral." For a generation, neutral was synonymous with safe — the beige of the uncommitted, the greige of the indecisive. But in the hands of a skilled designer, a neutral palette is anything but passive. It is the most demanding and rewarding challenge in the field.</p><h2>Warm vs. Cool Neutrals</h2><p>The first principle is understanding temperature. Warm neutrals — creams, ivories, taupes, sandy tones — read differently under light than cool neutrals, which carry grey, blue, or green undertones. Neither is superior; each creates a fundamentally different atmosphere. Warm neutrals feel sheltering and sensuous. Cool neutrals feel expansive and precise.</p><h2>Texture as Color</h2><p>In a neutral palette, texture does the work that color cannot. A cream linen sofa, cream plaster walls, and cream cotton curtains could easily read as a failure of imagination — unless the linen has a pronounced weave, the plaster is polished to a soft sheen, and the cotton has a slight openwork detail. Then suddenly the room sings.</p><h2>The Accent Question</h2><p>Every neutral room needs a counterpoint. This might be a single deep charcoal wall, a piece of art with an unexpected pop of cobalt, or the warmth of brass hardware against a palette of pale stone. The accent does not need to be large or loud — in fact, it should not be. Its power comes from restraint.</p><h2>Our Favorite Neutral Combinations</h2><p>After years of practice, certain combinations have become near-universal references in our studio: aged ivory with warm charcoal and unlacquered brass; pale sage with warm white and brushed bronze; dusky blush with deep chocolate and matte black; bleached oak with natural linen and terracotta.</p>',
            'excerpt'  => 'True mastery of the neutral palette goes far beyond beige. A deep dive into color temperature, texture, and the precise moment when restraint becomes extraordinary.',
        ),
        array(
            'title'    => 'Small Space, Big Impact: Designing for Dhaka Apartments',
            'category' => 'Guides',
            'content'  => '<p>The typical apartment in Dhaka presents a familiar challenge: ambitions that exceed square footage. We regularly work with 800–1,200 sq ft apartments in Gulshan, Banani, and Dhanmondi where the brief is, invariably, "make it feel bigger."</p><p>The honest answer is that great design does not make a small space bigger. It makes you forget about its size entirely. The goal is not spatial illusion — it is experiential richness, so that every moment spent in the space feels generous and considered.</p><h2>The Floor Plan First</h2><p>Before any material or furniture decision is made, reconsider the floor plan. The most common mistake in small Dhaka apartments is retaining the developer\'s partition walls, which create a warren of small, dark rooms. Often, removing the wall between kitchen and living — where structurally permissible — transforms a flat entirely.</p><h2>The Vertical Dimension</h2><p>Most small apartments are designed horizontally. The most elegant small space interventions work vertically: built-in joinery that runs floor to ceiling, shelving that draws the eye upward, curtains hung as high as possible even with low ceilings. Height implies space even when square footage does not.</p><h2>Multi-Functional Furniture</h2><p>The most transformative investment for a small apartment is a dining table that extends from a console, a sofa with integrated storage, or a bed with a lifting platform. These are not compromises — the very best examples, from makers like Artek or local custom carpenters we work with in Narsingdi — are genuinely beautiful objects.</p>',
            'excerpt'  => 'Designing small apartments in Dhaka requires a different kind of thinking. Here is our studio\'s complete playbook for making every square foot extraordinary.',
        ),
        array(
            'title'    => 'Behind the Project: Designing the Studio Verde Café',
            'category' => 'Behind the Scenes',
            'content'  => '<p>Every project has a moment where the concept crystallizes — a single conversation, a sketch on a napkin, an image seen in passing. For Studio Verde, it was a photograph shared by the client on a Tuesday afternoon in February: a sun-drenched courtyard in Lisbon, terracotta pots overflowing with greenery, the shadows of bougainvillea falling across cracked stone tiles.</p><p>"I want people to forget they\'re in Banani for a moment," she said. "I want them to feel they\'ve stepped somewhere else."</p><h2>The Design Process</h2><p>We began with a rigorous study of biophilic design principles — the research-backed field exploring how designed connections to nature affect human wellbeing. The evidence is compelling: spaces with natural elements reduce cortisol levels, improve focus, and increase time spent in a location by an average of 14%.</p><p>For a café-co-working hybrid, that last statistic was commercially significant.</p><h2>The Plant Strategy</h2><p>We worked with a botanical consultant from Chittagong to specify 47 plant species suitable for interior conditions. The design integrated planters at every scale: micro-terrariums on tables, medium planters defining circulation routes, and the centerpiece — a 12-meter living moss wall behind the espresso bar, maintained by a built-in irrigation and misting system.</p><h2>The Outcome</h2><p>Studio Verde opened to queues down the block. Within six weeks, it was booked solid for co-working memberships. The client reported that average customer dwell time was 2.4 hours — extraordinary for a café, and a direct consequence of the environment we created together.</p>',
            'excerpt'  => 'The full story of how we transformed a bare commercial shell in Banani into Dhaka\'s most photographed café — the client brief, design decisions, and the 3,000 plants.',
        ),
    );

    // Create blog categories
    $blog_cats = array( 'Trends', 'Guides', 'Color & Materials', 'Behind the Scenes' );
    foreach ( $blog_cats as $cat_name ) {
        wp_insert_term( $cat_name, 'category' );
    }

    foreach ( $posts as $post_data ) {
        $cat = get_term_by( 'name', $post_data['category'], 'category' );
        $post_id = wp_insert_post( array(
            'post_title'   => $post_data['title'],
            'post_excerpt' => $post_data['excerpt'],
            'post_content' => $post_data['content'],
            'post_type'    => 'post',
            'post_status'  => 'publish',
        ));
        if ( $post_id && $cat ) {
            wp_set_post_categories( $post_id, array( $cat->term_id ) );
        }
    }

    // Sample Testimonials
    $testimonials = array(
        array(
            'title'   => 'Fareeha R.',
            'content' => 'Luxe Interior transformed our Gulshan apartment beyond what we imagined possible. They understood our lifestyle within the first meeting and translated it into a home that genuinely feels like us — only better.',
            'meta'    => array( 'client_role' => 'Residential Client, Gulshan', 'rating' => '5' ),
        ),
        array(
            'title'   => 'Omar & Tasneem',
            'content' => 'We gave them an impossible brief: a family home that feels like a boutique hotel. They delivered exactly that, on budget and six weeks ahead of schedule. The craftsmanship is extraordinary.',
            'meta'    => array( 'client_role' => 'Villa Project, Baridhara', 'rating' => '5' ),
        ),
        array(
            'title'   => 'Nahida S.',
            'content' => 'As a business owner, I needed a space that would impress clients and inspire my team. The Nexus offices have changed how people feel about coming to work. The feedback from staff has been extraordinary.',
            'meta'    => array( 'client_role' => 'CEO, Nexus Group', 'rating' => '5' ),
        ),
    );

    foreach ( $testimonials as $t ) {
        $post_id = wp_insert_post( array(
            'post_title'   => $t['title'],
            'post_content' => $t['content'],
            'post_type'    => 'testimonial',
            'post_status'  => 'publish',
        ));
        if ( $post_id ) {
            foreach ( $t['meta'] as $k => $v ) {
                update_post_meta( $post_id, $k, $v );
            }
        }
    }

    // Sample Pages
    $pages = array(
        array( 'title' => 'About',    'slug' => 'about',    'template' => 'page-about.php' ),
        array( 'title' => 'Services', 'slug' => 'services', 'template' => 'page-services.php' ),
        array( 'title' => 'Portfolio','slug' => 'portfolio','template' => 'page-portfolio.php' ),
        array( 'title' => 'Journal',  'slug' => 'journal',  'template' => '' ),
        array( 'title' => 'Contact',  'slug' => 'contact',  'template' => 'page-contact.php' ),
    );

    foreach ( $pages as $page ) {
        if ( ! get_page_by_path( $page['slug'] ) ) {
            $page_id = wp_insert_post( array(
                'post_title'  => $page['title'],
                'post_name'   => $page['slug'],
                'post_type'   => 'page',
                'post_status' => 'publish',
                'post_content'=> '',
            ));
            if ( $page_id && $page['template'] ) {
                update_post_meta( $page_id, '_wp_page_template', $page['template'] );
            }
        }
    }

    update_option( 'luxe_sample_data_imported', true );
}
// Uncomment to run import on theme activation:
// add_action( 'after_switch_theme', 'luxe_import_sample_data' );
// Or add to admin menu for manual trigger:
function luxe_import_admin_page() {
    add_management_page( 'Import Sample Data', 'Luxe: Import Data', 'manage_options', 'luxe-import', function() {
        if ( isset( $_POST['luxe_import'] ) && check_admin_referer( 'luxe_import_nonce' ) ) {
            delete_option( 'luxe_sample_data_imported' );
            luxe_import_sample_data();
            echo '<div class="updated"><p>Sample data imported successfully!</p></div>';
        }
        ?>
        <div class="wrap">
            <h1>Luxe Interior — Import Sample Data</h1>
            <p>This will create sample portfolio projects, blog posts, testimonials, and pages.</p>
            <form method="post">
                <?php wp_nonce_field( 'luxe_import_nonce' ); ?>
                <input type="hidden" name="luxe_import" value="1">
                <input type="submit" class="button button-primary" value="Import Sample Data">
            </form>
        </div>
        <?php
    });
}
add_action( 'admin_menu', 'luxe_import_admin_page' );

// ============================================================
// LOAD INC FILES
// ============================================================
require_once get_template_directory() . '/inc/meta-boxes.php';
require_once get_template_directory() . '/inc/admin-styles.php';

// Enqueue customizer live preview JS
function luxe_customizer_preview_js() {
    wp_enqueue_script(
        'luxe-customizer-live',
        get_template_directory_uri() . '/inc/customizer-live.js',
        array( 'customize-preview', 'jquery' ),
        '1.0.0',
        true
    );
}
add_action( 'customize_preview_init', 'luxe_customizer_preview_js' );


