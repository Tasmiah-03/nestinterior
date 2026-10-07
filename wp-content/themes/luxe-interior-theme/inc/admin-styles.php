<?php
/**
 * Admin Panel Styles & Customizations
 */

if ( ! defined('ABSPATH') ) exit;

// Admin color scheme & branding
function luxe_admin_styles() {
    echo '<style>
    /* Luxe Interior Admin Theme */
    #adminmenu .wp-menu-image.dashicons-admin-home::before { color: #B08D6A !important; }
    #adminmenu .wp-menu-image.dashicons-format-quote::before { color: #B08D6A !important; }
    .about-wrap h1 { font-family: Georgia, serif; }

    /* Post type icons in admin list */
    .post-type-portfolio .dashicons-admin-home { color: #B08D6A; }

    /* Custom admin notices */
    .luxe-admin-notice {
        background: #FDF8F3;
        border-left: 4px solid #B08D6A;
        padding: 12px 16px;
    }

    /* Column widths for portfolio list */
    .column-thumbnail { width: 80px; }
    .column-portfolio_category { width: 140px; }
    </style>';
}
add_action('admin_head', 'luxe_admin_styles');

// Portfolio thumbnail column
function luxe_portfolio_columns($columns) {
    $new = array();
    foreach ($columns as $key => $val) {
        if ($key === 'title') {
            $new['thumbnail'] = __('Image', 'luxe-interior');
        }
        $new[$key] = $val;
    }
    $new['portfolio_category'] = __('Category', 'luxe-interior');
    return $new;
}
add_filter('manage_portfolio_posts_columns', 'luxe_portfolio_columns');

function luxe_portfolio_column_content($column, $post_id) {
    if ($column === 'thumbnail') {
        if (has_post_thumbnail($post_id)) {
            echo '<a href="' . esc_url(get_edit_post_link($post_id)) . '">';
            echo get_the_post_thumbnail($post_id, array(60,60), array('style'=>'border-radius:4px;object-fit:cover;'));
            echo '</a>';
        } else {
            echo '<span style="color:#999;font-size:11px;">No image</span>';
        }
    }
    if ($column === 'portfolio_category') {
        $terms = get_the_terms($post_id, 'portfolio_category');
        if ($terms && !is_wp_error($terms)) {
            echo implode(', ', wp_list_pluck($terms, 'name'));
        } else {
            echo '—';
        }
    }
}
add_action('manage_portfolio_posts_custom_column', 'luxe_portfolio_column_content', 10, 2);

// Welcome notice on first activation
function luxe_admin_welcome_notice() {
    if (get_transient('luxe_welcome_notice')) : ?>
    <div class="luxe-admin-notice notice is-dismissible">
        <p><strong>🏠 Luxe Interior Theme activated!</strong> Head to <a href="<?php echo esc_url(admin_url('tools.php?page=luxe-import')); ?>">Tools → Import Sample Data</a> to populate your site with demo content, or visit the <a href="<?php echo esc_url(admin_url('customize.php')); ?>">Customizer</a> to configure your settings.</p>
    </div>
    <?php
    delete_transient('luxe_welcome_notice');
    endif;
}
add_action('admin_notices', 'luxe_admin_welcome_notice');

function luxe_set_welcome_notice() {
    set_transient('luxe_welcome_notice', true, 60 * 60 * 24);
}
add_action('after_switch_theme', 'luxe_set_welcome_notice');

// Gutenberg editor styles
function luxe_block_editor_styles() {
    wp_enqueue_style(
        'luxe-editor-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Jost:wght@300;400;500&display=swap',
        array(), null
    );
    add_editor_style('css/editor-style.css');
}
add_action('enqueue_block_editor_assets', 'luxe_block_editor_styles');
