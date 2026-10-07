<?php
/**
 * Module 1 — Lead CRM
 * inc/module-crm.php
 *
 * • lbs_lead CPT + lbs_lead_status taxonomy
 * • Contact form handler (replaces theme's plain form)
 * • Lead pipeline admin view (Kanban-style columns)
 * • Internal staff notes
 * • Admin email notification + auto-reply
 * • CSV export
 * • Dashboard widget
 * • Shortcode [lbs_contact_form]
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   CPT + TAXONOMY
============================================================ */
add_action( 'init', 'lbs_crm_register' );

function lbs_crm_register() {
    // CPT
    register_post_type( 'lbs_lead', array(
        'labels'        => array(
            'name'          => __( 'Leads',       'luxe-business-suite' ),
            'singular_name' => __( 'Lead',        'luxe-business-suite' ),
            'menu_name'     => __( 'Lead CRM',    'luxe-business-suite' ),
            'add_new'       => __( 'Add Lead',    'luxe-business-suite' ),
            'add_new_item'  => __( 'Add New Lead','luxe-business-suite' ),
            'edit_item'     => __( 'Edit Lead',   'luxe-business-suite' ),
            'all_items'     => __( 'All Leads',   'luxe-business-suite' ),
        ),
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-groups',
        'menu_position'      => 6,
        'supports'           => array( 'title' ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'show_in_rest'       => false,
    ) );

    // Status taxonomy
    register_taxonomy( 'lbs_lead_status', 'lbs_lead', array(
        'labels'       => array(
            'name'          => __( 'Lead Status', 'luxe-business-suite' ),
            'singular_name' => __( 'Status',      'luxe-business-suite' ),
        ),
        'hierarchical'      => false,
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => false,
    ) );

    // Insert default status terms
    lbs_crm_default_statuses();
}

function lbs_crm_default_statuses() {
    if ( get_option( 'lbs_crm_statuses_inserted' ) ) return;
    $statuses = array(
        'new'           => array( 'name' => '🆕 New',            'description' => '#3B82F6' ),
        'qualified'     => array( 'name' => '✅ Qualified',       'description' => '#10B981' ),
        'proposal-sent' => array( 'name' => '📄 Proposal Sent',  'description' => '#F59E0B' ),
        'won'           => array( 'name' => '🏆 Won',            'description' => '#6366F1' ),
        'lost'          => array( 'name' => '❌ Lost',            'description' => '#EF4444' ),
        'on-hold'       => array( 'name' => '⏸ On Hold',         'description' => '#9CA3AF' ),
    );
    foreach ( $statuses as $slug => $args ) {
        if ( ! term_exists( $slug, 'lbs_lead_status' ) ) {
            wp_insert_term( $args['name'], 'lbs_lead_status', array(
                'slug'        => $slug,
                'description' => $args['description'], // store colour in description
            ) );
        }
    }
    update_option( 'lbs_crm_statuses_inserted', true );
}

/* ============================================================
   META BOXES
============================================================ */
add_action( 'add_meta_boxes', 'lbs_crm_meta_boxes' );
add_action( 'save_post_lbs_lead', 'lbs_crm_save_meta', 10, 2 );

function lbs_crm_meta_boxes() {
    add_meta_box( 'lbs_lead_details', __( '👤 Lead Details',    'luxe-business-suite' ), 'lbs_mb_lead_details', 'lbs_lead', 'normal', 'high' );
    add_meta_box( 'lbs_lead_project', __( '🏗 Project Info',    'luxe-business-suite' ), 'lbs_mb_lead_project', 'lbs_lead', 'normal', 'default' );
    add_meta_box( 'lbs_lead_notes',   __( '📝 Internal Notes',  'luxe-business-suite' ), 'lbs_mb_lead_notes',   'lbs_lead', 'side',   'default' );
    add_meta_box( 'lbs_lead_meta',    __( '⚙ CRM Meta',         'luxe-business-suite' ), 'lbs_mb_lead_meta',    'lbs_lead', 'side',   'high' );
}

function lbs_mb_lead_details( $post ) {
    wp_nonce_field( 'lbs_lead_save', 'lbs_lead_nonce' );
    $fields = array(
        'lbs_lead_name'    => array( 'label' => 'Full Name',     'type' => 'text',  'ph' => 'Sarah Ahmed' ),
        'lbs_lead_email'   => array( 'label' => 'Email',         'type' => 'email', 'ph' => 'sarah@example.com' ),
        'lbs_lead_phone'   => array( 'label' => 'Phone',         'type' => 'text',  'ph' => '+880 17 XXXX XXXX' ),
        'lbs_lead_company' => array( 'label' => 'Company',       'type' => 'text',  'ph' => 'Optional' ),
        'lbs_lead_source'  => array( 'label' => 'Lead Source',   'type' => 'select', 'opts' => array(
            '' => '— Select —', 'website' => 'Website', 'referral' => 'Referral',
            'instagram' => 'Instagram', 'houzz' => 'Houzz', 'google' => 'Google',
            'facebook' => 'Facebook', 'event' => 'Event', 'other' => 'Other',
        ) ),
    );
    echo '<div class="lbs-meta-grid">';
    foreach ( $fields as $key => $f ) {
        lbs_meta_field( array(
            'id'          => $key,
            'label'       => $f['label'],
            'type'        => $f['type'],
            'value'       => get_post_meta( $post->ID, $key, true ),
            'placeholder' => $f['ph'] ?? '',
            'options'     => $f['opts'] ?? array(),
        ) );
    }
    echo '</div>';
}

function lbs_mb_lead_project( $post ) {
    $fields = array(
        'lbs_lead_service'   => array( 'label' => 'Service Interest', 'type' => 'select', 'opts' => array(
            '' => '— Select —',
            'residential' => 'Residential Interior Design',
            'commercial'  => 'Commercial Interiors',
            'kitchen'     => 'Kitchen & Bathroom Design',
            'ffe'         => 'FF&E Sourcing',
            'lighting'    => 'Lighting Design',
            'pm'          => 'Project Management',
            'consultation'=> 'Consultation',
        ) ),
        'lbs_lead_budget'    => array( 'label' => 'Budget Range', 'type' => 'select', 'opts' => array(
            '' => '— Select —',
            'under-5l'  => 'Under ৳5 Lakh',
            '5-15l'     => '৳5 – 15 Lakh',
            '15-50l'    => '৳15 – 50 Lakh',
            '50l-1cr'   => '৳50 Lakh – 1 Crore',
            'over-1cr'  => 'Over ৳1 Crore',
            'undisclosed' => 'Prefer not to say',
        ) ),
        'lbs_lead_timeline'  => array( 'label' => 'Timeline',         'type' => 'text',     'ph' => 'e.g. Q3 2025' ),
        'lbs_lead_location'  => array( 'label' => 'Project Location', 'type' => 'text',     'ph' => 'Gulshan, Dhaka' ),
        'lbs_lead_message'   => array( 'label' => 'Message',          'type' => 'textarea', 'ph' => 'Project details…' ),
    );
    echo '<div class="lbs-meta-grid">';
    foreach ( $fields as $key => $f ) {
        lbs_meta_field( array(
            'id'          => $key,
            'label'       => $f['label'],
            'type'        => $f['type'],
            'value'       => get_post_meta( $post->ID, $key, true ),
            'placeholder' => $f['ph'] ?? '',
            'options'     => $f['opts'] ?? array(),
        ) );
    }
    echo '</div>';
}

function lbs_mb_lead_notes( $post ) {
    $notes = get_post_meta( $post->ID, 'lbs_lead_notes', true );
    echo '<textarea name="lbs_lead_notes" rows="8" style="width:100%;font-size:12px;" placeholder="Internal staff notes — not visible to client…">' . esc_textarea( $notes ) . '</textarea>';
}

function lbs_mb_lead_meta( $post ) {
    $score       = get_post_meta( $post->ID, 'lbs_lead_score',       true );
    $follow_up   = get_post_meta( $post->ID, 'lbs_lead_follow_up',   true );
    $assigned    = get_post_meta( $post->ID, 'lbs_lead_assigned',    true );
    $submitted   = get_post_meta( $post->ID, 'lbs_lead_submitted',   true );
    $ip          = get_post_meta( $post->ID, 'lbs_lead_ip',          true );

    echo '<div class="lbs-field"><label class="lbs-label">Score / Priority</label>';
    echo '<select name="lbs_lead_score" class="lbs-input">';
    foreach ( array( '' => '—', 'hot' => '🔥 Hot', 'warm' => '🌡 Warm', 'cold' => '❄️ Cold' ) as $v => $l ) {
        echo '<option value="' . esc_attr( $v ) . '"' . selected( $score, $v, false ) . '>' . esc_html( $l ) . '</option>';
    }
    echo '</select></div>';

    echo '<div class="lbs-field"><label class="lbs-label">Follow-up Date</label>';
    echo '<input type="date" name="lbs_lead_follow_up" value="' . esc_attr( $follow_up ) . '" class="lbs-input"></div>';

    // Assigned to
    $users = get_users( array( 'role__in' => array( 'administrator', 'editor', 'author' ) ) );
    echo '<div class="lbs-field"><label class="lbs-label">Assigned To</label><select name="lbs_lead_assigned" class="lbs-input"><option value="">— Unassigned —</option>';
    foreach ( $users as $u ) {
        echo '<option value="' . esc_attr( $u->ID ) . '"' . selected( $assigned, $u->ID, false ) . '>' . esc_html( $u->display_name ) . '</option>';
    }
    echo '</select></div>';

    if ( $submitted ) {
        echo '<p class="lbs-hint">Submitted: ' . esc_html( date_i18n( 'd M Y H:i', strtotime( $submitted ) ) ) . '</p>';
    }
    if ( $ip ) {
        echo '<p class="lbs-hint">IP: ' . esc_html( $ip ) . '</p>';
    }
}

function lbs_crm_save_meta( $post_id, $post ) {
    if ( ! isset( $_POST['lbs_lead_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_lead_nonce'], 'lbs_lead_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $text_fields = array(
        'lbs_lead_name', 'lbs_lead_phone', 'lbs_lead_company',
        'lbs_lead_source', 'lbs_lead_service', 'lbs_lead_budget',
        'lbs_lead_timeline', 'lbs_lead_location', 'lbs_lead_score',
        'lbs_lead_follow_up', 'lbs_lead_assigned',
    );
    foreach ( $text_fields as $f ) {
        if ( isset( $_POST[ $f ] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[ $f ] ) );
    }
    if ( isset( $_POST['lbs_lead_email'] ) )   update_post_meta( $post_id, 'lbs_lead_email',   sanitize_email( $_POST['lbs_lead_email'] ) );
    if ( isset( $_POST['lbs_lead_message'] ) ) update_post_meta( $post_id, 'lbs_lead_message', sanitize_textarea_field( $_POST['lbs_lead_message'] ) );
    if ( isset( $_POST['lbs_lead_notes'] ) )   update_post_meta( $post_id, 'lbs_lead_notes',   sanitize_textarea_field( $_POST['lbs_lead_notes'] ) );
}

/* ============================================================
   CONTACT FORM HANDLER (admin-post)
============================================================ */
add_action( 'admin_post_lbs_contact_form',        'lbs_handle_contact_form' );
add_action( 'admin_post_nopriv_lbs_contact_form', 'lbs_handle_contact_form' );

function lbs_handle_contact_form() {
    if ( ! isset( $_POST['lbs_cf_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_cf_nonce'], 'lbs_contact_form' ) ) {
        wp_die( __( 'Security check failed.', 'luxe-business-suite' ) );
    }

    $name    = sanitize_text_field( $_POST['lbs_name']    ?? '' );
    $email   = sanitize_email(      $_POST['lbs_email']   ?? '' );
    $phone   = sanitize_text_field( $_POST['lbs_phone']   ?? '' );
    $service = sanitize_text_field( $_POST['lbs_service'] ?? '' );
    $budget  = sanitize_text_field( $_POST['lbs_budget']  ?? '' );
    $message = sanitize_textarea_field( $_POST['lbs_message'] ?? '' );

    if ( ! $name || ! is_email( $email ) ) {
        wp_safe_redirect( add_query_arg( 'lbs_cf', 'error', wp_get_referer() ) );
        exit;
    }

    // Create CPT lead
    $post_id = wp_insert_post( array(
        'post_title'  => esc_html( $name . ' — ' . date( 'd M Y' ) ),
        'post_type'   => 'lbs_lead',
        'post_status' => 'publish',
    ) );

    if ( $post_id && ! is_wp_error( $post_id ) ) {
        update_post_meta( $post_id, 'lbs_lead_name',      $name );
        update_post_meta( $post_id, 'lbs_lead_email',     $email );
        update_post_meta( $post_id, 'lbs_lead_phone',     $phone );
        update_post_meta( $post_id, 'lbs_lead_service',   $service );
        update_post_meta( $post_id, 'lbs_lead_budget',    $budget );
        update_post_meta( $post_id, 'lbs_lead_message',   $message );
        update_post_meta( $post_id, 'lbs_lead_submitted', current_time( 'mysql' ) );
        update_post_meta( $post_id, 'lbs_lead_ip',        sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ) );
        update_post_meta( $post_id, 'lbs_lead_source',    'website' );

        // Assign "new" status
        $new_term = get_term_by( 'slug', 'new', 'lbs_lead_status' );
        if ( $new_term ) wp_set_object_terms( $post_id, $new_term->term_id, 'lbs_lead_status' );

        // Emails
        lbs_crm_send_admin_notification( $post_id );
        lbs_crm_send_autoreply( $email, $name );
    }

    wp_safe_redirect( add_query_arg( 'lbs_cf', 'success', wp_get_referer() ) );
    exit;
}

function lbs_crm_send_admin_notification( int $post_id ) {
    $general = get_option( 'lbs_general', array() );
    $to      = $general['admin_email'] ?? get_option( 'admin_email' );
    $name    = get_post_meta( $post_id, 'lbs_lead_name',    true );
    $email   = get_post_meta( $post_id, 'lbs_lead_email',   true );
    $service = get_post_meta( $post_id, 'lbs_lead_service', true );
    $budget  = get_post_meta( $post_id, 'lbs_lead_budget',  true );
    $message = get_post_meta( $post_id, 'lbs_lead_message', true );
    $edit_url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );

    $subject = sprintf( __( 'New Enquiry from %s — %s', 'luxe-business-suite' ), $name, get_bloginfo('name') );
    $body    = "New lead received.\n\n";
    $body   .= "Name: $name\nEmail: $email\nService: $service\nBudget: $budget\n\nMessage:\n$message\n\n";
    $body   .= "View in CRM: $edit_url";

    wp_mail( $to, $subject, $body );
}

function lbs_crm_send_autoreply( string $email, string $name ) {
    $general  = get_option( 'lbs_general', array() );
    $studio   = $general['studio_name'] ?? get_bloginfo('name');
    $subject  = sprintf( __( 'Thank you for reaching out, %s', 'luxe-business-suite' ), $name );
    $body  = "Hi $name,\n\nThank you for your enquiry. We've received your message and one of our team will be in touch within 24–48 hours.\n\n";
    $body .= "In the meantime, feel free to browse our portfolio at " . home_url('/showcase') . "\n\n";
    $body .= "Warm regards,\n" . $studio . " Team";
    wp_mail( $email, $subject, $body );
}

/* ============================================================
   ADMIN COLUMNS
============================================================ */
add_filter( 'manage_lbs_lead_posts_columns',       'lbs_crm_columns' );
add_action( 'manage_lbs_lead_posts_custom_column', 'lbs_crm_render_column', 10, 2 );

function lbs_crm_columns( array $cols ) : array {
    unset( $cols['date'] );
    return array_merge(
        array( 'cb' => $cols['cb'], 'title' => __( 'Lead', 'luxe-business-suite' ) ),
        array(
            'lbs_email'   => __( 'Email',   'luxe-business-suite' ),
            'lbs_service' => __( 'Service', 'luxe-business-suite' ),
            'lbs_budget'  => __( 'Budget',  'luxe-business-suite' ),
            'lbs_score'   => __( 'Score',   'luxe-business-suite' ),
            'lbs_follow'  => __( 'Follow-up','luxe-business-suite' ),
            'lbs_date'    => __( 'Received','luxe-business-suite' ),
        )
    );
}

function lbs_crm_render_column( string $col, int $id ) {
    $map = array(
        'lbs_email'   => 'lbs_lead_email',
        'lbs_service' => 'lbs_lead_service',
        'lbs_budget'  => 'lbs_lead_budget',
        'lbs_score'   => 'lbs_lead_score',
        'lbs_follow'  => 'lbs_lead_follow_up',
    );
    if ( isset( $map[ $col ] ) ) {
        $v = get_post_meta( $id, $map[ $col ], true );
        if ( $col === 'lbs_score' ) {
            $icons = array( 'hot' => '🔥', 'warm' => '🌡', 'cold' => '❄️' );
            echo esc_html( $icons[ $v ] ?? '—' );
        } elseif ( $col === 'lbs_follow' ) {
            echo $v ? '<span style="color:' . ( strtotime($v) < time() ? '#d63638' : '#2271b1' ) . '">' . esc_html( date_i18n('d M', strtotime($v)) ) . '</span>' : '—';
        } else {
            echo $v ? esc_html( $v ) : '<span style="color:#aaa">—</span>';
        }
    }
    if ( $col === 'lbs_date' ) {
        echo esc_html( get_the_date( 'd M Y', $id ) );
    }
}

/* ============================================================
   CSV EXPORT
============================================================ */
add_action( 'admin_post_lbs_export_leads', 'lbs_crm_export_csv' );

function lbs_crm_export_csv() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Forbidden' );
    if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'lbs_export_leads' ) ) wp_die( 'Security check failed.' );

    $leads = get_posts( array( 'post_type' => 'lbs_lead', 'numberposts' => -1, 'post_status' => 'publish' ) );

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="leads-' . date('Y-m-d') . '.csv"' );
    $out = fopen( 'php://output', 'w' );
    fputcsv( $out, array( 'Name','Email','Phone','Service','Budget','Source','Score','Message','Date' ) );

    foreach ( $leads as $lead ) {
        fputcsv( $out, array(
            get_post_meta( $lead->ID, 'lbs_lead_name',    true ),
            get_post_meta( $lead->ID, 'lbs_lead_email',   true ),
            get_post_meta( $lead->ID, 'lbs_lead_phone',   true ),
            get_post_meta( $lead->ID, 'lbs_lead_service', true ),
            get_post_meta( $lead->ID, 'lbs_lead_budget',  true ),
            get_post_meta( $lead->ID, 'lbs_lead_source',  true ),
            get_post_meta( $lead->ID, 'lbs_lead_score',   true ),
            get_post_meta( $lead->ID, 'lbs_lead_message', true ),
            get_the_date( 'Y-m-d H:i:s', $lead->ID ),
        ) );
    }
    fclose( $out );
    exit;
}

// Add export button to leads list
add_action( 'restrict_manage_posts', function () {
    global $typenow;
    if ( $typenow !== 'lbs_lead' ) return;
    $nonce = wp_create_nonce( 'lbs_export_leads' );
    $url   = admin_url( 'admin-post.php?action=lbs_export_leads&_nonce=' . $nonce );
    echo '<a href="' . esc_url( $url ) . '" class="button">⬇ ' . esc_html__( 'Export CSV', 'luxe-business-suite' ) . '</a>';
} );

/* ============================================================
   QUICK STATUS CHANGE (AJAX)
============================================================ */
add_action( 'wp_ajax_lbs_update_lead_status', 'lbs_ajax_update_lead_status' );

function lbs_ajax_update_lead_status() {
    check_ajax_referer( 'lbs_ajax', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( 'Permission denied' );

    $lead_id = absint( $_POST['lead_id'] ?? 0 );
    $status  = sanitize_text_field( $_POST['status'] ?? '' );

    if ( ! $lead_id || ! $status ) wp_send_json_error( 'Invalid data' );

    $term = get_term_by( 'slug', $status, 'lbs_lead_status' );
    if ( ! $term ) wp_send_json_error( 'Unknown status' );

    wp_set_object_terms( $lead_id, $term->term_id, 'lbs_lead_status' );
    wp_send_json_success( array( 'status' => $status, 'label' => $term->name ) );
}

/* ============================================================
   DASHBOARD WIDGET
============================================================ */
add_action( 'wp_dashboard_setup', 'lbs_crm_dashboard_widget' );

function lbs_crm_dashboard_widget() {
    wp_add_dashboard_widget( 'lbs_lead_overview', '📊 Lead Overview — Luxe Business Suite', 'lbs_crm_render_dashboard' );
}

function lbs_crm_render_dashboard() {
    // Leads this month
    $this_month = new WP_Query( array(
        'post_type' => 'lbs_lead', 'posts_per_page' => -1,
        'date_query' => array( array( 'year' => date('Y'), 'month' => date('n') ) ),
    ) );
    $total = new WP_Query( array( 'post_type' => 'lbs_lead', 'posts_per_page' => -1 ) );
    $won   = new WP_Query( array( 'post_type' => 'lbs_lead', 'posts_per_page' => -1,
        'tax_query' => array( array( 'taxonomy' => 'lbs_lead_status', 'field' => 'slug', 'terms' => 'won' ) ) ) );

    $conversion = $total->found_posts > 0 ? round( ( $won->found_posts / $total->found_posts ) * 100 ) : 0;

    echo '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;">';
    foreach ( array(
        array( 'This Month',  $this_month->found_posts, '#2271b1' ),
        array( 'Total Leads', $total->found_posts,      '#6366f1' ),
        array( 'Won Rate',    $conversion . '%',        '#10b981' ),
    ) as $stat ) {
        echo '<div style="background:#f6f7f7;border-radius:6px;padding:12px;text-align:center;">';
        echo '<div style="font-size:24px;font-weight:600;color:' . esc_attr($stat[2]) . ';">' . esc_html($stat[1]) . '</div>';
        echo '<div style="font-size:11px;color:#757575;text-transform:uppercase;letter-spacing:.05em;margin-top:4px;">' . esc_html($stat[0]) . '</div>';
        echo '</div>';
    }
    echo '</div>';

    // Status breakdown
    $statuses = get_terms( array( 'taxonomy' => 'lbs_lead_status', 'hide_empty' => false ) );
    echo '<table style="width:100%;font-size:13px;border-collapse:collapse;">';
    foreach ( $statuses as $s ) {
        $count = $s->count;
        if ( ! $count ) continue;
        $color = $s->description ?: '#9CA3AF';
        echo '<tr><td style="padding:5px 0;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:' . esc_attr($color) . ';margin-right:6px;"></span>' . esc_html($s->name) . '</td><td style="text-align:right;font-weight:600;">' . esc_html($count) . '</td></tr>';
    }
    echo '</table>';

    $export_nonce = wp_create_nonce( 'lbs_export_leads' );
    $export_url   = admin_url( 'admin-post.php?action=lbs_export_leads&_nonce=' . $export_nonce );
    echo '<p style="margin-top:12px;"><a href="' . esc_url($export_url) . '" class="button button-small">⬇ Export CSV</a> <a href="' . esc_url(admin_url('edit.php?post_type=lbs_lead')) . '" class="button button-small" style="margin-left:6px;">View All Leads</a></p>';
}

/* ============================================================
   SHORTCODE [lbs_contact_form]
============================================================ */
add_shortcode( 'lbs_contact_form', 'lbs_sc_contact_form' );

function lbs_sc_contact_form( $atts ) {
    $a = shortcode_atts( array( 'redirect' => '' ), $atts );
    $success = isset( $_GET['lbs_cf'] ) && $_GET['lbs_cf'] === 'success';
    $error   = isset( $_GET['lbs_cf'] ) && $_GET['lbs_cf'] === 'error';

    ob_start(); ?>
    <?php if ( $success ) : ?>
    <div class="lbs-form-message lbs-form-success">
        <strong><?php _e( 'Message sent!', 'luxe-business-suite' ); ?></strong>
        <?php _e( 'Thank you — we\'ll be in touch within 24–48 hours.', 'luxe-business-suite' ); ?>
    </div>
    <?php elseif ( $error ) : ?>
    <div class="lbs-form-message lbs-form-error">
        <?php _e( 'Something went wrong. Please check your details and try again.', 'luxe-business-suite' ); ?>
    </div>
    <?php else : ?>
    <form class="lbs-contact-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" novalidate>
        <?php wp_nonce_field( 'lbs_contact_form', 'lbs_cf_nonce' ); ?>
        <input type="hidden" name="action" value="lbs_contact_form">

        <div class="lbs-form-row lbs-form-2col">
            <div class="lbs-form-group">
                <label for="lbs_name"><?php _e( 'Full Name *', 'luxe-business-suite' ); ?></label>
                <input type="text" id="lbs_name" name="lbs_name" required placeholder="Sarah Ahmed">
            </div>
            <div class="lbs-form-group">
                <label for="lbs_email"><?php _e( 'Email Address *', 'luxe-business-suite' ); ?></label>
                <input type="email" id="lbs_email" name="lbs_email" required placeholder="sarah@example.com">
            </div>
        </div>

        <div class="lbs-form-row lbs-form-2col">
            <div class="lbs-form-group">
                <label for="lbs_phone"><?php _e( 'Phone Number', 'luxe-business-suite' ); ?></label>
                <input type="tel" id="lbs_phone" name="lbs_phone" placeholder="+880 17 XXXX XXXX">
            </div>
            <div class="lbs-form-group">
                <label for="lbs_service"><?php _e( 'Service Interest *', 'luxe-business-suite' ); ?></label>
                <select id="lbs_service" name="lbs_service" required>
                    <option value=""><?php _e( 'Select a service…', 'luxe-business-suite' ); ?></option>
                    <option value="residential"><?php _e( 'Residential Interior Design', 'luxe-business-suite' ); ?></option>
                    <option value="commercial"><?php _e( 'Commercial Interiors', 'luxe-business-suite' ); ?></option>
                    <option value="kitchen"><?php _e( 'Kitchen & Bathroom Design', 'luxe-business-suite' ); ?></option>
                    <option value="ffe"><?php _e( 'FF&E Sourcing', 'luxe-business-suite' ); ?></option>
                    <option value="lighting"><?php _e( 'Lighting Design', 'luxe-business-suite' ); ?></option>
                    <option value="pm"><?php _e( 'Project Management', 'luxe-business-suite' ); ?></option>
                    <option value="consultation"><?php _e( 'Consultation', 'luxe-business-suite' ); ?></option>
                </select>
            </div>
        </div>

        <div class="lbs-form-row lbs-form-2col">
            <div class="lbs-form-group">
                <label for="lbs_budget"><?php _e( 'Approximate Budget', 'luxe-business-suite' ); ?></label>
                <select id="lbs_budget" name="lbs_budget">
                    <option value=""><?php _e( 'Select a range…', 'luxe-business-suite' ); ?></option>
                    <option value="under-5l"><?php _e( 'Under ৳5 Lakh', 'luxe-business-suite' ); ?></option>
                    <option value="5-15l"><?php _e( '৳5 – 15 Lakh', 'luxe-business-suite' ); ?></option>
                    <option value="15-50l"><?php _e( '৳15 – 50 Lakh', 'luxe-business-suite' ); ?></option>
                    <option value="50l-1cr"><?php _e( '৳50 Lakh – 1 Crore', 'luxe-business-suite' ); ?></option>
                    <option value="over-1cr"><?php _e( 'Over ৳1 Crore', 'luxe-business-suite' ); ?></option>
                </select>
            </div>
            <div class="lbs-form-group">
                <label for="lbs_timeline"><?php _e( 'Ideal Timeline', 'luxe-business-suite' ); ?></label>
                <input type="text" id="lbs_timeline" name="lbs_timeline" placeholder="e.g. Start Q3 2025">
            </div>
        </div>

        <div class="lbs-form-group">
            <label for="lbs_message"><?php _e( 'Tell Us About Your Project *', 'luxe-business-suite' ); ?></label>
            <textarea id="lbs_message" name="lbs_message" required rows="5" placeholder="<?php esc_attr_e( 'Describe your space, vision, and anything that helps us understand your project…', 'luxe-business-suite' ); ?>"></textarea>
        </div>

        <button type="submit" class="btn btn-accent lbs-submit-btn">
            <?php _e( 'Send Message', 'luxe-business-suite' ); ?>
            <span class="lbs-submit-arrow">→</span>
        </button>
    </form>
    <?php endif;
    return ob_get_clean();
}
