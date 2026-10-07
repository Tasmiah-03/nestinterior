<?php
/**
 * Portfolio Project Meta Boxes
 */

if ( ! defined('ABSPATH') ) exit;

function luxe_add_meta_boxes() {
    add_meta_box(
        'luxe_project_details',
        __('Project Details', 'luxe-interior'),
        'luxe_project_details_callback',
        'portfolio',
        'side',
        'high'
    );
    add_meta_box(
        'luxe_project_gallery',
        __('Project Gallery', 'luxe-interior'),
        'luxe_project_gallery_callback',
        'portfolio',
        'normal',
        'default'
    );

    // Logistics Meta Boxes
    add_meta_box(
        'luxe_logistics_details',
        __('Logistics & Timeline', 'luxe-interior'),
        'luxe_logistics_details_callback',
        'project_logistics',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'luxe_add_meta_boxes');

function luxe_project_details_callback($post) {
    wp_nonce_field('luxe_project_meta', 'luxe_project_nonce');
    $fields = array(
        'project_client'   => __('Client Name', 'luxe-interior'),
        'project_location' => __('Location', 'luxe-interior'),
        'project_year'     => __('Year Completed', 'luxe-interior'),
        'project_area'     => __('Floor Area (e.g. 1,200 sqft)', 'luxe-interior'),
        'project_scope'    => __('Scope of Work', 'luxe-interior'),
        'project_duration' => __('Project Duration', 'luxe-interior'),
    );
    echo '<style>.luxe-meta-field{margin-bottom:1rem;}.luxe-meta-field label{display:block;font-weight:600;margin-bottom:3px;font-size:11px;text-transform:uppercase;letter-spacing:.05em;}.luxe-meta-field input{width:100%;}</style>';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<div class="luxe-meta-field">';
        echo '<label for="' . esc_attr($key) . '">' . esc_html($label) . '</label>';
        echo '<input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
        echo '</div>';
    }
}

function luxe_project_gallery_callback($post) {
    $gallery = get_post_meta($post->ID, 'project_gallery', true);
    echo '<p style="color:#666;font-size:13px;">' . __('Enter comma-separated attachment IDs for additional project images. You can find attachment IDs in the Media Library.', 'luxe-interior') . '</p>';
    echo '<input type="text" name="project_gallery" value="' . esc_attr($gallery) . '" style="width:100%;padding:6px 8px;" placeholder="e.g. 123, 124, 125">';
    echo '<p style="margin-top:1rem;"><button type="button" class="button" id="luxe-gallery-picker">' . __('Select Images', 'luxe-interior') . '</button></p>';
    ?>
    <script>
    jQuery(function($){
        var frame;
        $('#luxe-gallery-picker').on('click', function() {
            if (frame) { frame.open(); return; }
            frame = wp.media({ title: 'Select Gallery Images', button: { text: 'Use These Images' }, multiple: true });
            frame.on('select', function() {
                var ids = frame.state().get('selection').map(function(a){ return a.id; }).join(', ');
                $('input[name="project_gallery"]').val(ids);
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function luxe_save_project_meta($post_id) {
    if (!isset($_POST['luxe_project_nonce']) || !wp_verify_nonce($_POST['luxe_project_nonce'], 'luxe_project_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array('project_client','project_location','project_year','project_area','project_scope','project_duration','project_gallery');
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_portfolio', 'luxe_save_project_meta');

// Enqueue media uploader on portfolio edit screen
function luxe_enqueue_media_uploader($hook) {
    global $post;
    if (($hook === 'post-new.php' || $hook === 'post.php') && isset($post) && ($post->post_type === 'portfolio' || $post->post_type === 'project_logistics')) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'luxe_enqueue_media_uploader');

/**
 * Logistics Meta Box Callback
 */
function luxe_logistics_details_callback($post) {
    wp_nonce_field('luxe_logistics_meta', 'luxe_logistics_nonce');
    $status = get_post_meta($post->ID, 'logistic_status', true);
    $percentage = get_post_meta($post->ID, 'logistic_progress', true);
    $client = get_post_meta($post->ID, 'logistic_client', true);
    $budget = get_post_meta($post->ID, 'logistic_budget', true);
    
    $stages = array(
        'discovery'    => __('Discovery & Briefing', 'luxe-interior'),
        'concept'      => __('Concept Design', 'luxe-interior'),
        'development'  => __('Design Development', 'luxe-interior'),
        'procurement'  => __('Procurement & FF&E', 'luxe-interior'),
        'installation' => __('Installation & Styling', 'luxe-interior'),
        'completed'    => __('Project Completed', 'luxe-interior'),
    );
    ?>
    <style>
        .luxe-logistics-row { display: flex; gap: 2rem; margin-bottom: 1.5rem; }
        .luxe-logistics-field { flex: 1; }
        .luxe-logistics-field label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #555; }
        .luxe-logistics-field input, .luxe-logistics-field select { width: 100%; padding: 8px; border: 1px solid #ddd; }
        .progress-preview { height: 10px; background: #eee; border-radius: 5px; margin-top: 10px; overflow: hidden; }
        .progress-bar { height: 100%; background: #B08D6A; transition: width 0.3s ease; }
    </style>
    <div class="luxe-logistics-row">
        <div class="luxe-logistics-field">
            <label><?php _e('Client Name', 'luxe-interior'); ?></label>
            <input type="text" name="logistic_client" value="<?php echo esc_attr($client); ?>" placeholder="e.g. Mrs. Tasneem Ahmed">
        </div>
        <div class="luxe-logistics-field">
            <label><?php _e('Current Stage', 'luxe-interior'); ?></label>
            <select name="logistic_status">
                <?php foreach ($stages as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($status, $key); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="luxe-logistics-row">
        <div class="luxe-logistics-field">
            <label><?php _e('Project Budget (Est.)', 'luxe-interior'); ?></label>
            <input type="text" name="logistic_budget" value="<?php echo esc_attr($budget); ?>" placeholder="e.g. ৳45,00,000">
        </div>
        <div class="luxe-logistics-field">
            <label><?php _e('Progress Percentage (%)', 'luxe-interior'); ?></label>
            <input type="number" name="logistic_progress" value="<?php echo esc_attr($percentage); ?>" min="0" max="100" id="logistic-pct">
            <div class="progress-preview"><div class="progress-bar" style="width: <?php echo esc_attr($percentage); ?>%;"></div></div>
        </div>
    </div>
    <script>
        document.getElementById('logistic-pct').addEventListener('input', function() {
            document.querySelector('.progress-bar').style.width = this.value + '%';
        });
    </script>
    <?php
}

/**
 * Save Logistics Meta
 */
function luxe_save_logistics_meta($post_id) {
    if (!isset($_POST['luxe_logistics_nonce']) || !wp_verify_nonce($_POST['luxe_logistics_nonce'], 'luxe_logistics_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    $fields = array('logistic_client', 'logistic_status', 'logistic_budget', 'logistic_progress');
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_project_logistics', 'luxe_save_logistics_meta');
