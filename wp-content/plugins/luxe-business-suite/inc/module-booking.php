<?php
/**
 * Module 2 — Service Booking System
 * inc/module-booking.php
 *
 * • lbs_booking CPT + lbs_timeslot CPT
 * • Admin: define available days + slots per service
 * • Frontend: step-by-step booking form (service → date → time → details)
 * • Approval workflow (pending → confirmed → cancelled)
 * • Confirmation emails (client + admin)
 * • Cancellation via token link
 * • AJAX slot availability check
 * • Shortcode [lbs_booking_form]
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   CPTs
============================================================ */
add_action( 'init', 'lbs_booking_register' );

function lbs_booking_register() {

    // Booking CPT
    register_post_type( 'lbs_booking', array(
        'labels'       => array(
            'name'          => __( 'Bookings',           'luxe-business-suite' ),
            'singular_name' => __( 'Booking',            'luxe-business-suite' ),
            'menu_name'     => __( 'Bookings',           'luxe-business-suite' ),
            'all_items'     => __( 'All Bookings',       'luxe-business-suite' ),
            'add_new_item'  => __( 'Add New Booking',    'luxe-business-suite' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=lbs_lead',
        'supports'        => array( 'title' ),
        'menu_icon'       => 'dashicons-calendar-alt',
        'has_archive'     => false,
        'capability_type' => 'post',
    ) );

    // Availability Slot CPT (admin-defined available dates/times)
    register_post_type( 'lbs_timeslot', array(
        'labels'       => array(
            'name'          => __( 'Availability Slots', 'luxe-business-suite' ),
            'singular_name' => __( 'Slot',               'luxe-business-suite' ),
            'menu_name'     => __( 'Availability',       'luxe-business-suite' ),
            'add_new_item'  => __( 'Add Slot',           'luxe-business-suite' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=lbs_lead',
        'supports'        => array( 'title' ),
        'has_archive'     => false,
        'capability_type' => 'post',
    ) );
}

/* ============================================================
   TIMESLOT META BOXES
============================================================ */
add_action( 'add_meta_boxes', 'lbs_timeslot_meta_boxes' );
add_action( 'save_post_lbs_timeslot', 'lbs_save_timeslot_meta', 10, 2 );

function lbs_timeslot_meta_boxes() {
    add_meta_box( 'lbs_slot_details', __( '📅 Slot Details', 'luxe-business-suite' ), 'lbs_mb_slot_details', 'lbs_timeslot', 'normal', 'high' );
}

function lbs_mb_slot_details( $post ) {
    wp_nonce_field( 'lbs_slot_save', 'lbs_slot_nonce' );
    $date     = get_post_meta( $post->ID, 'lbs_slot_date',     true );
    $time     = get_post_meta( $post->ID, 'lbs_slot_time',     true );
    $duration = get_post_meta( $post->ID, 'lbs_slot_duration', true ) ?: '60';
    $service  = get_post_meta( $post->ID, 'lbs_slot_service',  true );
    $max      = get_post_meta( $post->ID, 'lbs_slot_max',      true ) ?: '1';
    $booked   = get_post_meta( $post->ID, 'lbs_slot_booked',   true ) ?: '0';
    ?>
    <div class="lbs-meta-grid">
        <div class="lbs-field">
            <label class="lbs-label">Date</label>
            <input type="date" name="lbs_slot_date" value="<?php echo esc_attr( $date ); ?>" class="lbs-input" min="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="lbs-field">
            <label class="lbs-label">Start Time</label>
            <input type="time" name="lbs_slot_time" value="<?php echo esc_attr( $time ); ?>" class="lbs-input">
        </div>
        <div class="lbs-field">
            <label class="lbs-label">Duration (minutes)</label>
            <select name="lbs_slot_duration" class="lbs-input">
                <?php foreach ( array( '30', '45', '60', '90', '120' ) as $d ) : ?>
                <option value="<?php echo esc_attr($d); ?>" <?php selected( $duration, $d ); ?>><?php echo esc_html($d); ?> min</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lbs-field">
            <label class="lbs-label">For Service</label>
            <select name="lbs_slot_service" class="lbs-input">
                <option value="">All Services</option>
                <?php foreach ( array( 'consultation' => 'Consultation', 'residential' => 'Residential Design', 'commercial' => 'Commercial Interiors', 'discovery' => 'Discovery Call' ) as $v => $l ) : ?>
                <option value="<?php echo esc_attr($v); ?>" <?php selected( $service, $v ); ?>><?php echo esc_html($l); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lbs-field">
            <label class="lbs-label">Max Bookings</label>
            <input type="number" name="lbs_slot_max" value="<?php echo esc_attr( $max ); ?>" class="lbs-input" min="1" max="10">
        </div>
        <div class="lbs-field">
            <label class="lbs-label">Current Bookings</label>
            <input type="number" name="lbs_slot_booked" value="<?php echo esc_attr( $booked ); ?>" class="lbs-input" min="0">
        </div>
    </div>
    <?php
}

function lbs_save_timeslot_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_slot_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_slot_nonce'], 'lbs_slot_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    foreach ( array( 'lbs_slot_date', 'lbs_slot_time', 'lbs_slot_duration', 'lbs_slot_service', 'lbs_slot_max', 'lbs_slot_booked' ) as $f ) {
        if ( isset( $_POST[ $f ] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[ $f ] ) );
    }
}

/* ============================================================
   BOOKING META BOXES
============================================================ */
add_action( 'add_meta_boxes', 'lbs_booking_meta_boxes' );
add_action( 'save_post_lbs_booking', 'lbs_save_booking_meta', 10, 2 );

function lbs_booking_meta_boxes() {
    add_meta_box( 'lbs_booking_details', __( '📅 Booking Details', 'luxe-business-suite' ), 'lbs_mb_booking_details', 'lbs_booking', 'normal', 'high' );
    add_meta_box( 'lbs_booking_actions', __( '⚙ Actions',          'luxe-business-suite' ), 'lbs_mb_booking_actions', 'lbs_booking', 'side',   'high' );
}

function lbs_mb_booking_details( $post ) {
    wp_nonce_field( 'lbs_booking_save', 'lbs_booking_nonce' );
    $fields = array(
        'lbs_booking_name'    => 'Client Name',
        'lbs_booking_email'   => 'Client Email',
        'lbs_booking_phone'   => 'Phone',
        'lbs_booking_service' => 'Service',
        'lbs_booking_date'    => 'Date',
        'lbs_booking_time'    => 'Time',
        'lbs_booking_notes'   => 'Notes',
    );
    echo '<div class="lbs-meta-grid">';
    foreach ( $fields as $key => $label ) {
        $val = get_post_meta( $post->ID, $key, true );
        $type = ( strpos( $key, 'notes' ) !== false ) ? 'textarea' : ( strpos( $key, 'email' ) !== false ? 'email' : 'text' );
        lbs_meta_field( array( 'id' => $key, 'label' => $label, 'type' => $type, 'value' => $val ) );
    }
    echo '</div>';
}

function lbs_mb_booking_actions( $post ) {
    $status = get_post_meta( $post->ID, 'lbs_booking_status', true ) ?: 'pending';
    echo '<div class="lbs-field"><label class="lbs-label">Status</label>';
    echo '<select name="lbs_booking_status" class="lbs-input">';
    foreach ( array( 'pending' => '⏳ Pending', 'confirmed' => '✅ Confirmed', 'cancelled' => '❌ Cancelled', 'completed' => '🏆 Completed' ) as $v => $l ) {
        echo '<option value="' . esc_attr($v) . '"' . selected($status,$v,false) . '>' . esc_html($l) . '</option>';
    }
    echo '</select></div>';

    // Confirm / Cancel quick buttons
    $confirm_url = wp_nonce_url( admin_url('admin-post.php?action=lbs_confirm_booking&id=' . $post->ID), 'lbs_confirm_' . $post->ID );
    $cancel_url  = wp_nonce_url( admin_url('admin-post.php?action=lbs_cancel_booking&id='  . $post->ID), 'lbs_cancel_'  . $post->ID );
    echo '<p style="margin-top:12px;"><a href="' . esc_url($confirm_url) . '" class="button button-primary" style="width:100%;justify-content:center;margin-bottom:6px;">✅ Confirm Booking</a></p>';
    echo '<p><a href="' . esc_url($cancel_url)  . '" class="button" style="width:100%;justify-content:center;color:#d63638;">❌ Cancel Booking</a></p>';

    $client_email = get_post_meta( $post->ID, 'lbs_booking_email', true );
    if ( $client_email ) echo '<p style="font-size:11px;color:#666;margin-top:8px;">Client: ' . esc_html($client_email) . '</p>';
}

function lbs_save_booking_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_booking_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_booking_nonce'], 'lbs_booking_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    foreach ( array( 'lbs_booking_name','lbs_booking_phone','lbs_booking_service','lbs_booking_date','lbs_booking_time','lbs_booking_status' ) as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[$f] ) );
    }
    if ( isset( $_POST['lbs_booking_email'] ) ) update_post_meta( $post_id, 'lbs_booking_email', sanitize_email( $_POST['lbs_booking_email'] ) );
    if ( isset( $_POST['lbs_booking_notes'] ) ) update_post_meta( $post_id, 'lbs_booking_notes', sanitize_textarea_field( $_POST['lbs_booking_notes'] ) );
}

/* ============================================================
   CONFIRM / CANCEL ACTIONS
============================================================ */
add_action( 'admin_post_lbs_confirm_booking', 'lbs_action_confirm_booking' );
add_action( 'admin_post_lbs_cancel_booking',  'lbs_action_cancel_booking' );

function lbs_action_confirm_booking() {
    $id = absint( $_GET['id'] ?? 0 );
    if ( ! $id || ! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'lbs_confirm_' . $id ) ) wp_die('Security check');
    update_post_meta( $id, 'lbs_booking_status', 'confirmed' );
    lbs_booking_send_confirmation( $id );
    wp_safe_redirect( add_query_arg( 'lbs_confirmed', 1, admin_url('post.php?post=' . $id . '&action=edit') ) );
    exit;
}

function lbs_action_cancel_booking() {
    $id = absint( $_GET['id'] ?? 0 );
    if ( ! $id || ! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'lbs_cancel_' . $id ) ) wp_die('Security check');
    update_post_meta( $id, 'lbs_booking_status', 'cancelled' );
    lbs_booking_send_cancellation( $id );
    wp_safe_redirect( add_query_arg( 'lbs_cancelled', 1, admin_url('post.php?post=' . $id . '&action=edit') ) );
    exit;
}

function lbs_booking_send_confirmation( int $id ) {
    $email   = get_post_meta( $id, 'lbs_booking_email', true );
    $name    = get_post_meta( $id, 'lbs_booking_name',  true );
    $service = get_post_meta( $id, 'lbs_booking_service', true );
    $date    = get_post_meta( $id, 'lbs_booking_date',  true );
    $time    = get_post_meta( $id, 'lbs_booking_time',  true );
    $general = get_option( 'lbs_general', array() );
    $studio  = $general['studio_name'] ?? get_bloginfo('name');
    $cancel  = home_url( '/?lbs_cancel_token=' . get_post_meta( $id, 'lbs_cancel_token', true ) );

    $subject = __( 'Your booking is confirmed — ', 'luxe-business-suite' ) . $studio;
    $body    = "Hi $name,\n\nYour consultation has been confirmed.\n\n";
    $body   .= "Service: $service\nDate: $date\nTime: $time\n\n";
    $body   .= "To cancel or reschedule: $cancel\n\nWe look forward to speaking with you.\n\n$studio";
    wp_mail( $email, $subject, $body );
}

function lbs_booking_send_cancellation( int $id ) {
    $email  = get_post_meta( $id, 'lbs_booking_email', true );
    $name   = get_post_meta( $id, 'lbs_booking_name',  true );
    $general= get_option( 'lbs_general', array() );
    $studio = $general['studio_name'] ?? get_bloginfo('name');
    wp_mail( $email, __('Your booking has been cancelled — ', 'luxe-business-suite') . $studio,
        "Hi $name,\n\nYour booking has been cancelled. Please get in touch to reschedule.\n\n$studio" );
}

/* ============================================================
   AJAX — GET AVAILABLE SLOTS FOR A DATE
============================================================ */
add_action( 'wp_ajax_lbs_get_slots',        'lbs_ajax_get_slots' );
add_action( 'wp_ajax_nopriv_lbs_get_slots', 'lbs_ajax_get_slots' );

function lbs_ajax_get_slots() {
    check_ajax_referer( 'lbs_ajax', 'nonce' );
    $date    = sanitize_text_field( $_POST['date']    ?? '' );
    $service = sanitize_text_field( $_POST['service'] ?? '' );

    // Validate date
    if ( ! $date ) {
        wp_send_json_error( 'Invalid date.' );
    }

    // Try to load admin-created slots from DB first
    $args = array(
        'post_type'      => 'lbs_timeslot',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            'relation' => 'AND',
            array( 'key' => 'lbs_slot_date', 'value' => $date, 'compare' => '=' ),
        ),
    );
    if ( $service ) {
        $args['meta_query'][] = array(
            'relation' => 'OR',
            array( 'key' => 'lbs_slot_service', 'value' => '',       'compare' => '=' ),
            array( 'key' => 'lbs_slot_service', 'value' => $service, 'compare' => '=' ),
        );
    }

    $slots  = get_posts( $args );
    $result = array();

    foreach ( $slots as $slot ) {
        $max    = (int) get_post_meta( $slot->ID, 'lbs_slot_max',    true );
        $booked = (int) get_post_meta( $slot->ID, 'lbs_slot_booked', true );
        if ( $booked < $max ) {
            $result[] = array(
                'id'       => $slot->ID,
                'time'     => get_post_meta( $slot->ID, 'lbs_slot_time',     true ),
                'duration' => get_post_meta( $slot->ID, 'lbs_slot_duration', true ),
                'spots'    => $max - $booked,
                'virtual'  => false,
            );
        }
    }

    // If no DB slots found, return default time slots (virtual)
    if ( empty( $result ) ) {
        // Check for already-booked virtual slots on this date
        $booked_virtual = lbs_get_booked_virtual_slots( $date );

        // Default available times: 9am-5pm, 60-minute sessions
        $default_times = array(
            '09:00', '10:00', '11:00', '12:00',
            '14:00', '15:00', '16:00', '17:00',
        );

        // Block lunch hour + already fully booked slots
        foreach ( $default_times as $t ) {
            $booked_count = isset( $booked_virtual[ $t ] ) ? (int) $booked_virtual[ $t ] : 0;
            if ( $booked_count >= 1 ) continue; // max 1 per virtual slot
            $result[] = array(
                'id'       => 0,              // 0 signals a virtual slot
                'time'     => $t,
                'duration' => '60',
                'spots'    => 1,
                'virtual'  => true,
                'v_date'   => $date,
                'v_time'   => $t,
            );
        }
    }

    usort( $result, fn( $a, $b ) => strcmp( $a['time'], $b['time'] ) );
    wp_send_json_success( $result );
}

/**
 * Count how many virtual-slot bookings exist for a given date, keyed by time.
 */
function lbs_get_booked_virtual_slots( string $date ): array {
    $bookings = get_posts( array(
        'post_type'      => 'lbs_booking',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array( 'key' => 'lbs_booking_date',  'value' => $date, 'compare' => '=' ),
            array( 'key' => 'lbs_booking_slot',  'value' => '0',   'compare' => '=' ),
            array( 'key' => 'lbs_booking_status','value' => 'cancelled', 'compare' => '!=' ),
        ),
    ) );
    $counts = array();
    foreach ( $bookings as $b ) {
        $t = get_post_meta( $b->ID, 'lbs_booking_time', true );
        $counts[ $t ] = ( $counts[ $t ] ?? 0 ) + 1;
    }
    return $counts;
}

/* ============================================================
   FRONT-END BOOKING FORM HANDLER
============================================================ */
add_action( 'admin_post_lbs_submit_booking',        'lbs_handle_booking' );
add_action( 'admin_post_nopriv_lbs_submit_booking', 'lbs_handle_booking' );

function lbs_handle_booking() {
    $referer = wp_get_referer() ?: home_url( '/book-consultation/' );

    if ( ! isset( $_POST['lbs_booking_nonce_front'] ) || ! wp_verify_nonce( $_POST['lbs_booking_nonce_front'], 'lbs_booking_front' ) ) {
        wp_safe_redirect( add_query_arg( 'lbs_book', 'error_nonce', $referer ) ); exit;
    }

    $name    = sanitize_text_field( $_POST['lbs_name']    ?? '' );
    $email   = sanitize_email(      $_POST['lbs_email']   ?? '' );
    $phone   = sanitize_text_field( $_POST['lbs_phone']   ?? '' );
    $service = sanitize_text_field( $_POST['lbs_service'] ?? '' );
    $slot_id = (int) ( $_POST['lbs_slot_id'] ?? 0 );
    $notes   = sanitize_textarea_field( $_POST['lbs_notes'] ?? '' );

    // Require name
    if ( ! $name ) {
        wp_safe_redirect( add_query_arg( 'lbs_book', 'error_name', $referer ) ); exit;
    }
    // Require valid email
    if ( ! $email || ! is_email( $email ) ) {
        wp_safe_redirect( add_query_arg( 'lbs_book', 'error_email', $referer ) ); exit;
    }
    // Require service selection
    if ( ! $service ) {
        wp_safe_redirect( add_query_arg( 'lbs_book', 'error_service', $referer ) ); exit;
    }

    // Resolve slot date and time
    $is_virtual = ( $slot_id === 0 );
    if ( $is_virtual ) {
        // Primary: use the v_date/v_time set by JS when user clicked a slot
        $slot_date = sanitize_text_field( $_POST['lbs_v_date'] ?? '' );
        $slot_time = sanitize_text_field( $_POST['lbs_v_time'] ?? '' );
        // Fallback: use the date input + 'To be confirmed' if JS failed
        if ( empty( $slot_date ) ) {
            $slot_date = sanitize_text_field( $_POST['lbs_date'] ?? '' );
        }
        if ( empty( $slot_time ) ) {
            $slot_time = 'To be confirmed';
        }
        // Still require at least a date
        if ( empty( $slot_date ) ) {
            wp_safe_redirect( add_query_arg( 'lbs_book', 'error_date', $referer ) ); exit;
        }
    } else {
        $slot_date = get_post_meta( $slot_id, 'lbs_slot_date', true );
        $slot_time = get_post_meta( $slot_id, 'lbs_slot_time', true );
        if ( ! $slot_date ) {
            wp_safe_redirect( add_query_arg( 'lbs_book', 'error_slot', $referer ) ); exit;
        }
    }
    $token     = wp_generate_password( 32, false );

    $book_id = wp_insert_post( array(
        'post_title'  => $name . ' — ' . $slot_date . ' ' . $slot_time,
        'post_type'   => 'lbs_booking',
        'post_status' => 'publish',
    ) );

    if ( $book_id && ! is_wp_error( $book_id ) ) {
        foreach ( array(
            'lbs_booking_name'    => $name,
            'lbs_booking_email'   => $email,
            'lbs_booking_phone'   => $phone,
            'lbs_booking_service' => $service,
            'lbs_booking_date'    => $slot_date,
            'lbs_booking_time'    => $slot_time,
            'lbs_booking_notes'   => $notes,
            'lbs_booking_status'  => 'pending',
            'lbs_booking_slot'    => $slot_id,
            'lbs_cancel_token'    => $token,
        ) as $key => $val ) {
            update_post_meta( $book_id, $key, $val );
        }
        // Increment slot booked count (only for real DB slots)
        if ( ! $is_virtual && $slot_id ) {
            $booked = (int) get_post_meta( $slot_id, 'lbs_slot_booked', true );
            update_post_meta( $slot_id, 'lbs_slot_booked', $booked + 1 );
        }
        // Notify admin
        $general = get_option( 'lbs_general', array() );
        $to      = $general['admin_email'] ?? get_option('admin_email');
        wp_mail( $to, 'New Booking Request — ' . $name, "Date: $slot_date $slot_time\nService: $service\nEmail: $email\nPhone: $phone\n\nNotes: $notes\n\n" . admin_url('post.php?post=' . $book_id . '&action=edit') );
        // Client pending email
        wp_mail( $email, 'Booking Request Received — ' . ( $general['studio_name'] ?? get_bloginfo('name') ),
            "Hi $name,\n\nWe've received your booking request for $slot_date at $slot_time. We'll confirm shortly.\n\n" . ( $general['studio_name'] ?? get_bloginfo('name') ) );
    }

    wp_safe_redirect( add_query_arg( 'lbs_book', 'success', wp_get_referer() ) );
    exit;
}

/* ============================================================
   CANCEL VIA TOKEN
============================================================ */
add_action( 'template_redirect', function () {
    $token = sanitize_text_field( $_GET['lbs_cancel_token'] ?? '' );
    if ( ! $token ) return;

    $bookings = get_posts( array( 'post_type' => 'lbs_booking', 'numberposts' => 1,
        'meta_query' => array( array( 'key' => 'lbs_cancel_token', 'value' => $token ) ) ) );
    if ( empty( $bookings ) ) {
        wp_safe_redirect( home_url( '/?lbs_cancel=invalid' ) ); exit;
    }
    update_post_meta( $bookings[0]->ID, 'lbs_booking_status', 'cancelled' );
    lbs_booking_send_cancellation( $bookings[0]->ID );
    wp_safe_redirect( home_url( '/?lbs_cancel=done' ) );
    exit;
} );

/* ============================================================
   SHORTCODE [lbs_booking_form]
============================================================ */
add_shortcode( 'lbs_booking_form', 'lbs_sc_booking_form' );

function lbs_sc_booking_form( $atts ) {
    $a = shortcode_atts( array( 'service' => '' ), $atts );
    $success = isset( $_GET['lbs_book'] ) && $_GET['lbs_book'] === 'success';
    $error   = isset( $_GET['lbs_book'] ) && $_GET['lbs_book'] === 'error';

    ob_start();
    if ( $success ) :
    ?>
    <div class="lbs-form-message lbs-form-success">
        <strong><?php _e( '✅ Booking request received!', 'luxe-business-suite' ); ?></strong><br>
        <?php _e( "We'll confirm your appointment within a few hours. Check your email for details.", 'luxe-business-suite' ); ?>
    </div>
    <?php
    // Show specific error messages
    elseif ( $error ) :
        $error_code = $_GET['lbs_book'] ?? 'error';
        $messages = array(
            'error_name'    => __( 'Please enter your full name.', 'luxe-business-suite' ),
            'error_email'   => __( 'Please enter a valid email address.', 'luxe-business-suite' ),
            'error_service' => __( 'Please select a service type.', 'luxe-business-suite' ),
            'error_date'    => __( 'Please choose a date for your appointment.', 'luxe-business-suite' ),
            'error_slot'    => __( 'Please select an available time slot.', 'luxe-business-suite' ),
            'error_nonce'   => __( 'Session expired. Please refresh the page and try again.', 'luxe-business-suite' ),
            'error'         => __( 'Something went wrong. Please check your details and try again.', 'luxe-business-suite' ),
        );
        $msg = $messages[ $error_code ] ?? $messages['error'];
    ?>
    <div class="lbs-form-message lbs-form-error">
        ⚠ <?php echo esc_html( $msg ); ?>
    </div>
    <?php else : ?>
    <div class="lbs-booking-widget">
        <form class="lbs-booking-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <?php wp_nonce_field( 'lbs_booking_front', 'lbs_booking_nonce_front' ); ?>
            <input type="hidden" name="action" value="lbs_submit_booking">
            <input type="hidden" name="lbs_slot_id" id="lbs_slot_id" value="">

            <!-- Step 1: Service -->
            <div class="lbs-booking-step" data-step="1">
                <h4><?php _e( '1. Select a Service', 'luxe-business-suite' ); ?></h4>
                <div class="lbs-service-options">
                    <?php $services = array( 'discovery' => 'Discovery Call (Free)', 'consultation' => 'Design Consultation', 'residential' => 'Residential Design', 'commercial' => 'Commercial Interiors' );
                    foreach ( $services as $val => $lbl ) : ?>
                    <label class="lbs-service-option <?php echo ( $a['service'] === $val ) ? 'selected' : ''; ?>">
                        <input type="radio" name="lbs_service" value="<?php echo esc_attr($val); ?>" <?php checked( $a['service'], $val ); ?> required>
                        <?php echo esc_html($lbl); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Step 2: Date -->
            <div class="lbs-booking-step" data-step="2">
                <h4><?php _e( '2. Choose a Date', 'luxe-business-suite' ); ?></h4>
                <input type="date" id="lbs_booking_date" name="lbs_date" class="lbs-input"
                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                    style="max-width:240px;">
            </div>
            <!-- Hidden virtual-slot date/time (filled by JS) -->
            <input type="hidden" name="lbs_v_date" id="lbs_v_date" value="">
            <input type="hidden" name="lbs_v_time" id="lbs_v_time" value="">

            <!-- Step 3: Time Slot -->
            <div class="lbs-booking-step" data-step="3">
                <h4><?php _e( '3. Available Times', 'luxe-business-suite' ); ?></h4>
                <div id="lbs-slots-container">
                    <p class="lbs-hint"><?php _e( 'Select a service and date to see available times.', 'luxe-business-suite' ); ?></p>
                </div>
            </div>

            <!-- Step 4: Your Details -->
            <div class="lbs-booking-step" data-step="4">
                <h4><?php _e( '4. Your Details', 'luxe-business-suite' ); ?></h4>
                <div class="lbs-form-row lbs-form-2col">
                    <div class="lbs-form-group">
                        <label><?php _e( 'Full Name *', 'luxe-business-suite' ); ?></label>
                        <input type="text" name="lbs_name" required placeholder="Sarah Ahmed" class="lbs-input">
                    </div>
                    <div class="lbs-form-group">
                        <label><?php _e( 'Email Address *', 'luxe-business-suite' ); ?></label>
                        <input type="email" name="lbs_email" required placeholder="sarah@example.com" class="lbs-input">
                    </div>
                </div>
                <div class="lbs-form-group">
                    <label><?php _e( 'Phone', 'luxe-business-suite' ); ?></label>
                    <input type="tel" name="lbs_phone" placeholder="+880 17 XXXX XXXX" class="lbs-input">
                </div>
                <div class="lbs-form-group">
                    <label><?php _e( 'Notes or questions', 'luxe-business-suite' ); ?></label>
                    <textarea name="lbs_notes" rows="3" class="lbs-input" placeholder="<?php esc_attr_e( 'Anything you\'d like us to know before the call…', 'luxe-business-suite' ); ?>"></textarea>
                </div>
                <button type="submit" class="btn btn-accent" style="margin-top:1rem;">
                    <?php _e( 'Request Booking', 'luxe-business-suite' ); ?> →
                </button>
            </div>
        </form>
    </div>
    <?php endif;
    return ob_get_clean();
}
