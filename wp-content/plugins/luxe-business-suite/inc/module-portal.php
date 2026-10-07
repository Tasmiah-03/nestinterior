<?php
/**
 * Module 6 — Client Portal
 * inc/module-portal.php
 *
 * CPTs: lbs_client_project, lbs_project_doc, lbs_mood_board
 * • Branded login page at /client-portal
 * • Per-user project access (server-enforced)
 * • Document upload + download
 * • Mood board image galleries
 * • Progress update feed
 * • Admin → client email notification on new doc/update
 * • Shortcode: [lbs_client_portal]
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   CPT REGISTRATION
============================================================ */
add_action( 'init', 'lbs_portal_register' );

function lbs_portal_register() {

    // Client Project CPT
    register_post_type( 'lbs_client_project', array(
        'labels' => array(
            'name'          => __( 'Client Projects',   'luxe-business-suite' ),
            'singular_name' => __( 'Client Project',    'luxe-business-suite' ),
            'menu_name'     => __( 'Client Portal',     'luxe-business-suite' ),
            'add_new_item'  => __( 'Add Client Project','luxe-business-suite' ),
            'all_items'     => __( 'All Projects',      'luxe-business-suite' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-shield',
        'menu_position'   => 9,
        'supports'        => array( 'title', 'editor', 'thumbnail' ),
        'has_archive'     => false,
        'capability_type' => 'post',
    ) );

    // Project Document CPT
    register_post_type( 'lbs_project_doc', array(
        'labels' => array(
            'name'          => __( 'Project Documents', 'luxe-business-suite' ),
            'singular_name' => __( 'Document',          'luxe-business-suite' ),
            'add_new_item'  => __( 'Upload Document',   'luxe-business-suite' ),
            'all_items'     => __( 'All Documents',     'luxe-business-suite' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=lbs_client_project',
        'supports'        => array( 'title' ),
        'has_archive'     => false,
        'capability_type' => 'post',
    ) );

    // Mood Board CPT
    register_post_type( 'lbs_mood_board', array(
        'labels' => array(
            'name'          => __( 'Mood Boards',    'luxe-business-suite' ),
            'singular_name' => __( 'Mood Board',     'luxe-business-suite' ),
            'add_new_item'  => __( 'Add Mood Board', 'luxe-business-suite' ),
            'all_items'     => __( 'All Mood Boards','luxe-business-suite' ),
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=lbs_client_project',
        'supports'        => array( 'title', 'editor', 'thumbnail' ),
        'has_archive'     => false,
        'capability_type' => 'post',
    ) );
}

/* ============================================================
   CLIENT PROJECT META BOXES
============================================================ */
add_action( 'add_meta_boxes', 'lbs_portal_meta_boxes' );
add_action( 'save_post_lbs_client_project', 'lbs_save_client_project_meta', 10, 2 );
add_action( 'save_post_lbs_project_doc',    'lbs_save_doc_meta',            10, 2 );
add_action( 'save_post_lbs_mood_board',     'lbs_save_mood_board_meta',     10, 2 );

function lbs_portal_meta_boxes() {
    add_meta_box( 'lbs_cp_details',  __( '👤 Project Details',   'luxe-business-suite' ), 'lbs_mb_cp_details',  'lbs_client_project', 'normal', 'high' );
    add_meta_box( 'lbs_cp_client',   __( '🔐 Client Access',     'luxe-business-suite' ), 'lbs_mb_cp_client',   'lbs_client_project', 'side',   'high' );
    add_meta_box( 'lbs_cp_updates',  __( '📋 Progress Updates',  'luxe-business-suite' ), 'lbs_mb_cp_updates',  'lbs_client_project', 'normal', 'default' );
    add_meta_box( 'lbs_doc_details', __( '📎 Document Details',  'luxe-business-suite' ), 'lbs_mb_doc_details', 'lbs_project_doc',    'normal', 'high' );
    add_meta_box( 'lbs_mb_gallery',  __( '🖼 Mood Board Images', 'luxe-business-suite' ), 'lbs_mb_mb_gallery',  'lbs_mood_board',     'normal', 'high' );
}

function lbs_mb_cp_details( $post ) {
    wp_nonce_field( 'lbs_cp_save', 'lbs_cp_nonce' );
    $fields = array(
        'lbs_cp_service'     => array( 'label' => 'Service Type',      'type' => 'text',   'ph' => 'Residential Interior Design' ),
        'lbs_cp_location'    => array( 'label' => 'Project Location',  'type' => 'text',   'ph' => 'Gulshan-2, Dhaka' ),
        'lbs_cp_start_date'  => array( 'label' => 'Start Date',        'type' => 'date',   'ph' => '' ),
        'lbs_cp_end_date'    => array( 'label' => 'Target End Date',   'type' => 'date',   'ph' => '' ),
        'lbs_cp_status'      => array( 'label' => 'Project Status',    'type' => 'select', 'opts' => array(
            'briefing'      => '📋 Briefing',
            'concept'       => '✏️ Concept Design',
            'development'   => '🔨 Design Development',
            'procurement'   => '🛍 Procurement',
            'installation'  => '🏗 Installation',
            'snagging'      => '🔍 Snagging',
            'complete'      => '✅ Complete',
        ) ),
        'lbs_cp_progress_pct'=> array( 'label' => 'Progress (%)',      'type' => 'number', 'ph' => '0' ),
    );
    echo '<div class="lbs-meta-grid">';
    foreach ( $fields as $key => $f ) {
        lbs_meta_field( array( 'id' => $key, 'label' => $f['label'], 'type' => $f['type'], 'value' => get_post_meta( $post->ID, $key, true ), 'placeholder' => $f['ph'] ?? '', 'options' => $f['opts'] ?? array() ) );
    }
    echo '</div>';
}

function lbs_mb_cp_client( $post ) {
    $assigned_user = get_post_meta( $post->ID, 'lbs_cp_user_id', true );
    $users = get_users( array( 'role__in' => array( 'subscriber', 'customer', 'author', 'editor', 'administrator' ) ) );
    echo '<div class="lbs-field"><label class="lbs-label">Assign to WordPress User</label>';
    echo '<select name="lbs_cp_user_id" class="lbs-input">';
    echo '<option value="">— Unassigned —</option>';
    foreach ( $users as $u ) {
        echo '<option value="' . esc_attr($u->ID) . '"' . selected($assigned_user,$u->ID,false) . '>' . esc_html($u->display_name) . ' (' . esc_html($u->user_email) . ')</option>';
    }
    echo '</select><p class="lbs-hint">Only this user can view this project in the portal.</p></div>';

    // Notify button
    $notify_url = wp_nonce_url( admin_url('admin-post.php?action=lbs_notify_client&project_id=' . $post->ID), 'lbs_notify_' . $post->ID );
    echo '<a href="' . esc_url($notify_url) . '" class="button" style="margin-top:8px;">📧 Send Project Update Email</a>';
}

function lbs_mb_cp_updates( $post ) {
    $updates = get_post_meta( $post->ID, 'lbs_cp_updates', true );
    if ( ! is_array( $updates ) ) $updates = array();

    echo '<p class="lbs-hint">' . __( 'Add short progress notes. Newest first. Visible to the client in their portal.', 'luxe-business-suite' ) . '</p>';
    echo '<div id="lbs-updates-list">';
    foreach ( $updates as $i => $upd ) {
        echo '<div class="lbs-update-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:flex-start;">';
        echo '<input type="date" name="lbs_upd_date[' . $i . ']" value="' . esc_attr($upd['date']??'') . '" style="width:130px;font-size:12px;padding:4px 6px;border:1px solid #ddd;">';
        echo '<input type="text" name="lbs_upd_text[' . $i . ']" value="' . esc_attr($upd['text']??'') . '" placeholder="Progress note…" style="flex:1;font-size:12px;padding:4px 8px;border:1px solid #ddd;">';
        echo '<button type="button" onclick="this.closest(\'.lbs-update-row\').remove()" style="background:none;border:none;color:#d63638;cursor:pointer;font-size:16px;">✕</button>';
        echo '</div>';
    }
    echo '</div>';
    echo '<button type="button" id="lbs-add-update" class="button" style="margin-top:8px;">+ Add Update</button>';
    echo '<script>document.getElementById("lbs-add-update").addEventListener("click",function(){';
    echo 'var list=document.getElementById("lbs-updates-list");var i=list.children.length;';
    echo 'var div=document.createElement("div");div.className="lbs-update-row";div.style.cssText="display:flex;gap:8px;margin-bottom:8px;align-items:flex-start;";';
    echo 'div.innerHTML=\'<input type="date" name="lbs_upd_date[\'+i+\']" value="'.date('Y-m-d').'" style="width:130px;font-size:12px;padding:4px 6px;border:1px solid #ddd;"><input type="text" name="lbs_upd_text[\'+i+\']" value="" placeholder="Progress note…" style="flex:1;font-size:12px;padding:4px 8px;border:1px solid #ddd;"><button type="button" onclick="this.closest(\\\'div\\\').remove()" style="background:none;border:none;color:#d63638;cursor:pointer;font-size:16px;">✕</button>\';';
    echo 'list.appendChild(div);});</script>';
}

function lbs_mb_doc_details( $post ) {
    wp_nonce_field( 'lbs_doc_save', 'lbs_doc_nonce' );
    $project_id  = get_post_meta( $post->ID, 'lbs_doc_project',  true );
    $file_id     = get_post_meta( $post->ID, 'lbs_doc_file',     true );
    $doc_type    = get_post_meta( $post->ID, 'lbs_doc_type',     true );
    $description = get_post_meta( $post->ID, 'lbs_doc_description', true );

    // Project selector
    $projects = get_posts( array( 'post_type' => 'lbs_client_project', 'numberposts' => -1 ) );
    echo '<div class="lbs-field"><label class="lbs-label">Linked Project</label><select name="lbs_doc_project" class="lbs-input"><option value="">— Select Project —</option>';
    foreach ( $projects as $p ) {
        echo '<option value="' . esc_attr($p->ID) . '"' . selected($project_id,$p->ID,false) . '>' . esc_html(get_the_title($p->ID)) . '</option>';
    }
    echo '</select></div>';

    lbs_meta_field( array( 'id' => 'lbs_doc_type', 'label' => 'Document Type', 'type' => 'select', 'value' => $doc_type, 'options' => array(
        ''           => '— Select —',
        'brief'      => 'Design Brief',
        'concept'    => 'Concept Drawings',
        'spec'       => 'Technical Specification',
        'quote'      => 'Quotation / Estimate',
        'invoice'    => 'Invoice',
        'contract'   => 'Contract',
        'schedule'   => 'Schedule of Works',
        'handover'   => 'Handover Document',
        'other'      => 'Other',
    ) ) );

    lbs_meta_field( array( 'id' => 'lbs_doc_description', 'label' => 'Description', 'type' => 'textarea', 'value' => $description, 'placeholder' => 'Brief note about this document…' ) );

    // File picker (uses wp.media for PDFs and documents too)
    $file_url = $file_id ? wp_get_attachment_url( (int)$file_id ) : '';
    $file_name = $file_id ? basename( $file_url ) : '';
    echo '<div class="lbs-field"><label class="lbs-label">File Attachment</label>';
    echo '<div class="lbs-doc-preview" style="background:#f6f7f7;padding:8px 12px;border:1px dashed #ddd;border-radius:3px;font-size:12px;min-height:36px;display:flex;align-items:center;margin-bottom:6px;">';
    echo $file_id ? '📎 ' . esc_html($file_name) : '<span style="color:#aaa">No file attached</span>';
    echo '</div>';
    echo '<input type="hidden" name="lbs_doc_file" id="lbs_doc_file" value="' . esc_attr($file_id) . '">';
    echo '<button type="button" class="button lbs-pick-file" data-target="lbs_doc_file">Select File</button>';
    if ( $file_url ) echo ' <a href="' . esc_url($file_url) . '" target="_blank" class="button">Download</a>';
    echo '</div>';
}

function lbs_mb_mb_gallery( $post ) {
    wp_nonce_field( 'lbs_mb_save', 'lbs_mb_nonce' );
    $project_id = get_post_meta( $post->ID, 'lbs_mb_project', true );
    $img_ids    = get_post_meta( $post->ID, 'lbs_mb_images',  true );
    if ( ! is_array( $img_ids ) ) $img_ids = $img_ids ? array_filter( array_map( 'absint', explode(',', $img_ids) ) ) : array();

    $projects = get_posts( array( 'post_type' => 'lbs_client_project', 'numberposts' => -1 ) );
    echo '<div class="lbs-field"><label class="lbs-label">Linked Project</label><select name="lbs_mb_project" class="lbs-input"><option value="">— Select —</option>';
    foreach ( $projects as $p ) echo '<option value="' . esc_attr($p->ID) . '"' . selected($project_id,$p->ID,false) . '>' . esc_html(get_the_title($p->ID)) . '</option>';
    echo '</select></div>';

    echo '<ul id="lbs-mb-gallery-list" style="list-style:none;padding:0;margin:0 0 10px;display:flex;flex-wrap:wrap;gap:8px;">';
    foreach ( $img_ids as $img_id ) {
        $img_id = absint( $img_id );
        if ( ! $img_id ) continue;
        $thumb = wp_get_attachment_image( $img_id, array(80,80), false, array( 'style' => 'width:80px;height:80px;object-fit:cover;display:block;' ) );
        echo '<li data-id="' . esc_attr($img_id) . '" style="position:relative;">' . $thumb;
        echo '<button type="button" style="position:absolute;top:0;right:0;background:rgba(0,0,0,.6);color:#fff;border:none;cursor:pointer;width:20px;height:20px;font-size:12px;line-height:20px;text-align:center;padding:0;" onclick="this.parentElement.remove();lbsUpdateMbIds();">✕</button></li>';
    }
    echo '</ul>';
    echo '<input type="hidden" id="lbs_mb_images" name="lbs_mb_images" value="' . esc_attr( implode(',', $img_ids) ) . '">';
    echo '<button type="button" class="button lbs-pick-gallery" id="lbs-add-mb-images">+ Add Images</button>';
    echo '<script>function lbsUpdateMbIds(){var ids=[];document.querySelectorAll("#lbs-mb-gallery-list li[data-id]").forEach(function(li){ids.push(li.dataset.id);});document.getElementById("lbs_mb_images").value=ids.join(",");}</script>';
}

function lbs_save_client_project_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_cp_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_cp_nonce'], 'lbs_cp_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    foreach ( array( 'lbs_cp_service','lbs_cp_location','lbs_cp_start_date','lbs_cp_end_date','lbs_cp_status','lbs_cp_progress_pct' ) as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[$f] ) );
    }
    if ( isset( $_POST['lbs_cp_user_id'] ) ) update_post_meta( $post_id, 'lbs_cp_user_id', absint( $_POST['lbs_cp_user_id'] ) );

    // Updates
    $upd_texts = $_POST['lbs_upd_text'] ?? array();
    $upd_dates = $_POST['lbs_upd_date'] ?? array();
    $updates   = array();
    foreach ( $upd_texts as $i => $text ) {
        $t = sanitize_text_field( $text );
        if ( $t ) $updates[] = array( 'date' => sanitize_text_field($upd_dates[$i]??''), 'text' => $t );
    }
    usort( $updates, fn($a,$b) => strcmp($b['date'],$a['date']) );
    update_post_meta( $post_id, 'lbs_cp_updates', $updates );
}

function lbs_save_doc_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_doc_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_doc_nonce'], 'lbs_doc_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    foreach ( array( 'lbs_doc_project','lbs_doc_type','lbs_doc_file' ) as $f ) {
        if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, sanitize_text_field( $_POST[$f] ) );
    }
    if ( isset( $_POST['lbs_doc_description'] ) ) update_post_meta( $post_id, 'lbs_doc_description', sanitize_textarea_field( $_POST['lbs_doc_description'] ) );
}

function lbs_save_mood_board_meta( $post_id ) {
    if ( ! isset( $_POST['lbs_mb_nonce'] ) || ! wp_verify_nonce( $_POST['lbs_mb_nonce'], 'lbs_mb_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( isset( $_POST['lbs_mb_project'] ) ) update_post_meta( $post_id, 'lbs_mb_project', absint( $_POST['lbs_mb_project'] ) );
    if ( isset( $_POST['lbs_mb_images'] ) ) {
        $ids = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( $_POST['lbs_mb_images'] ) ) ) );
        update_post_meta( $post_id, 'lbs_mb_images', $ids );
    }
}

/* ============================================================
   ADMIN ACTION — NOTIFY CLIENT
============================================================ */
add_action( 'admin_post_lbs_notify_client', 'lbs_action_notify_client' );

function lbs_action_notify_client() {
    $project_id = absint( $_GET['project_id'] ?? 0 );
    if ( ! $project_id || ! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'lbs_notify_' . $project_id ) ) wp_die('Security check');
    if ( ! current_user_can( 'edit_posts' ) ) wp_die('Permission denied');

    $user_id  = get_post_meta( $project_id, 'lbs_cp_user_id', true );
    $user     = $user_id ? get_userdata( (int)$user_id ) : null;
    if ( ! $user ) {
        wp_safe_redirect( add_query_arg( 'lbs_notified', 'no_user', admin_url('post.php?post='.$project_id.'&action=edit') ) );
        exit;
    }

    $general  = get_option( 'lbs_general', array() );
    $studio   = $general['studio_name'] ?? get_bloginfo('name');
    $status   = get_post_meta( $project_id, 'lbs_cp_status', true );
    $pct      = get_post_meta( $project_id, 'lbs_cp_progress_pct', true );
    $portal   = home_url( '/' . ( $general['portal_slug'] ?? 'client-portal' ) );

    wp_mail(
        $user->user_email,
        sprintf( __('%s — Your Project Has Been Updated', 'luxe-business-suite'), $studio ),
        "Hi {$user->display_name},\n\nYour project has been updated.\n\nStatus: $status\nProgress: $pct%\n\nLog in to your client portal to see the latest updates and documents:\n$portal\n\n$studio"
    );

    wp_safe_redirect( add_query_arg( 'lbs_notified', '1', admin_url('post.php?post='.$project_id.'&action=edit') ) );
    exit;
}

/* ============================================================
   PORTAL SHORTCODE [lbs_client_portal]
============================================================ */
add_shortcode( 'lbs_client_portal', 'lbs_sc_client_portal' );

function lbs_sc_client_portal() {
    ob_start();

    if ( ! is_user_logged_in() ) {
        lbs_render_portal_login();
    } else {
        lbs_render_portal_dashboard();
    }

    return ob_get_clean();
}

function lbs_render_portal_login() {
    $error   = isset( $_GET['login'] ) && $_GET['login'] === 'failed';
    $logout  = isset( $_GET['lbs_logout'] );
    $redirect = get_permalink();
    ?>
    <div class="lbs-portal-login">
        <div class="lbs-portal-login__card">
            <div class="lbs-portal-login__logo">
                <?php if ( has_custom_logo() ) the_custom_logo(); else echo '<strong>' . get_bloginfo('name') . '</strong>'; ?>
            </div>
            <h2 class="lbs-portal-login__title"><?php _e( 'Client Portal', 'luxe-business-suite' ); ?></h2>
            <p class="lbs-portal-login__sub"><?php _e( 'Sign in to view your project documents and updates.', 'luxe-business-suite' ); ?></p>

            <?php if ( $error ) : ?>
            <div class="lbs-form-message lbs-form-error"><?php _e( 'Incorrect email or password. Please try again.', 'luxe-business-suite' ); ?></div>
            <?php endif; ?>
            <?php if ( $logout ) : ?>
            <div class="lbs-form-message lbs-form-success"><?php _e( 'You have been signed out.', 'luxe-business-suite' ); ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url( wp_login_url( $redirect ) ); ?>" class="lbs-portal-login__form">
                <div class="lbs-form-group">
                    <label for="user_login"><?php _e( 'Email Address', 'luxe-business-suite' ); ?></label>
                    <input type="text" id="user_login" name="log" required autocomplete="username" class="lbs-input">
                </div>
                <div class="lbs-form-group">
                    <label for="user_pass"><?php _e( 'Password', 'luxe-business-suite' ); ?></label>
                    <input type="password" id="user_pass" name="pwd" required autocomplete="current-password" class="lbs-input">
                </div>
                <div class="lbs-form-group" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                    <label class="lbs-checkbox"><input type="checkbox" name="rememberme" value="forever"> <?php _e('Remember me','luxe-business-suite'); ?></label>
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" style="font-size:0.8rem;"><?php _e('Forgot password?','luxe-business-suite'); ?></a>
                </div>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect); ?>">
                <?php wp_nonce_field( 'login_nonce', '_wpnonce' ); ?>
                <button type="submit" class="btn btn-accent" style="width:100%;"><?php _e( 'Sign In', 'luxe-business-suite' ); ?></button>
            </form>
        </div>
    </div>
    <?php
}

function lbs_render_portal_dashboard() {
    $user_id  = get_current_user_id();
    $is_admin = current_user_can( 'edit_posts' );

    // Fetch this user's projects (or all projects if admin)
    $query_args = array(
        'post_type'      => 'lbs_client_project',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    if ( ! $is_admin ) {
        $query_args['meta_query'] = array( array( 'key' => 'lbs_cp_user_id', 'value' => $user_id, 'compare' => '=' ) );
    }
    $projects = get_posts( $query_args );
    $active_project_id = absint( $_GET['project'] ?? ( ! empty($projects) ? $projects[0]->ID : 0 ) );

    // Security: non-admin cannot view other users' projects
    if ( ! $is_admin && $active_project_id ) {
        $owner = get_post_meta( $active_project_id, 'lbs_cp_user_id', true );
        if ( (int)$owner !== $user_id ) $active_project_id = 0;
    }
    ?>
    <div class="lbs-portal">
        <!-- Sidebar -->
        <aside class="lbs-portal__sidebar">
            <div class="lbs-portal__user">
                <?php echo get_avatar( $user_id, 48, '', '', array( 'class' => 'lbs-portal__avatar' ) ); ?>
                <div>
                    <strong><?php echo esc_html( wp_get_current_user()->display_name ); ?></strong>
                    <span><?php _e( 'Client', 'luxe-business-suite' ); ?></span>
                </div>
            </div>

            <?php if ( ! empty( $projects ) ) : ?>
            <nav class="lbs-portal__nav">
                <span class="lbs-portal__nav-label"><?php _e( 'Your Projects', 'luxe-business-suite' ); ?></span>
                <?php foreach ( $projects as $proj ) :
                    $status = get_post_meta( $proj->ID, 'lbs_cp_status', true );
                    $is_active = $proj->ID === $active_project_id;
                ?>
                <a href="?project=<?php echo esc_attr($proj->ID); ?>"
                   class="lbs-portal__nav-link <?php echo $is_active ? 'active' : ''; ?>">
                    <?php echo esc_html( get_the_title($proj->ID) ); ?>
                    <?php if ( $status ) : ?>
                    <span class="lbs-portal__nav-status"><?php echo esc_html($status); ?></span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </nav>
            <?php endif; ?>

            <?php if ( current_user_can('manage_options') ) : ?>
                <a href="<?php echo esc_url( admin_url() ); ?>" class="lbs-portal__nav-link" style="margin-top:auto;border-top:1px solid rgba(0,0,0,0.05);padding-top:1rem;color:var(--color-accent);">
                    ⚙ <?php _e('Go to Dashboard','luxe-business-suite'); ?>
                </a>
            <?php endif; ?>

            <a href="<?php echo esc_url( wp_logout_url( add_query_arg('lbs_logout','1', get_permalink()) ) ); ?>" class="lbs-portal__logout"><?php _e('Sign Out','luxe-business-suite'); ?></a>
        </aside>

        <!-- Main content -->
        <main class="lbs-portal__main">
        <?php if ( ! $active_project_id || empty($projects) ) : ?>
            <div class="lbs-portal__empty">
                <p><?php _e( 'No projects assigned yet. Please contact us for access.', 'luxe-business-suite' ); ?></p>
            </div>
        <?php else :
            lbs_render_portal_project( $active_project_id );
        endif; ?>
        </main>
    </div>
    <?php
}

function lbs_render_portal_project( int $project_id ) {
    $title    = get_the_title( $project_id );
    $service  = get_post_meta( $project_id, 'lbs_cp_service',      true );
    $location = get_post_meta( $project_id, 'lbs_cp_location',     true );
    $status   = get_post_meta( $project_id, 'lbs_cp_status',       true );
    $pct      = (int) get_post_meta( $project_id, 'lbs_cp_progress_pct', true );
    $start    = get_post_meta( $project_id, 'lbs_cp_start_date',   true );
    $end      = get_post_meta( $project_id, 'lbs_cp_end_date',     true );
    $updates  = get_post_meta( $project_id, 'lbs_cp_updates',      true );
    if ( ! is_array( $updates ) ) $updates = array();

    // Fetch documents for this project
    $docs = get_posts( array(
        'post_type'      => 'lbs_project_doc',
        'numberposts'    => -1,
        'meta_query'     => array( array( 'key' => 'lbs_doc_project', 'value' => $project_id ) ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    // Fetch mood boards for this project
    $boards = get_posts( array(
        'post_type'      => 'lbs_mood_board',
        'numberposts'    => -1,
        'meta_query'     => array( array( 'key' => 'lbs_mb_project', 'value' => $project_id ) ),
    ) );
    ?>
    <div class="lbs-portal-project">

        <!-- Header -->
        <div class="lbs-portal-project__header">
            <div>
                <h2><?php echo esc_html($title); ?></h2>
                <p class="lbs-portal-project__meta">
                    <?php if ($service) echo esc_html($service); ?>
                    <?php if ($location) echo ' &nbsp;·&nbsp; 📍 ' . esc_html($location); ?>
                    <?php if ($start) echo ' &nbsp;·&nbsp; ' . esc_html(date_i18n('M Y', strtotime($start))); ?>
                    <?php if ($end)   echo ' → ' . esc_html(date_i18n('M Y', strtotime($end))); ?>
                </p>
            </div>
            <?php if ($status) : ?>
            <span class="lbs-portal-project__status-badge"><?php echo esc_html($status); ?></span>
            <?php endif; ?>
        </div>

        <!-- Progress Bar -->
        <?php if ($pct) : ?>
        <div class="lbs-progress-wrap">
            <div class="lbs-progress-header">
                <span><?php _e('Project Progress','luxe-business-suite'); ?></span>
                <strong><?php echo esc_html($pct); ?>%</strong>
            </div>
            <div class="lbs-progress-bar">
                <div class="lbs-progress-bar__fill" style="width:<?php echo esc_attr($pct); ?>%"></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Documents -->
        <?php if ( ! empty($docs) ) : ?>
        <section class="lbs-portal-section">
            <h3><?php _e('Documents','luxe-business-suite'); ?> <span class="lbs-portal-section__count"><?php echo count($docs); ?></span></h3>
            <div class="lbs-docs-grid">
                <?php foreach ($docs as $doc) :
                    $file_id  = get_post_meta( $doc->ID, 'lbs_doc_file', true );
                    $file_url = $file_id ? wp_get_attachment_url( (int)$file_id ) : '';
                    $doc_type = get_post_meta( $doc->ID, 'lbs_doc_type', true );
                    $doc_desc = get_post_meta( $doc->ID, 'lbs_doc_description', true );
                    $type_icons = array( 'invoice' => '🧾', 'contract' => '📜', 'concept' => '✏️', 'spec' => '📐', 'quote' => '💰', 'schedule' => '📅', 'handover' => '🏠', 'brief' => '📋' );
                    $icon = $type_icons[$doc_type] ?? '📎';
                ?>
                <div class="lbs-doc-card">
                    <div class="lbs-doc-card__icon"><?php echo esc_html($icon); ?></div>
                    <div class="lbs-doc-card__info">
                        <strong><?php echo esc_html(get_the_title($doc->ID)); ?></strong>
                        <?php if ($doc_type) : ?><span class="lbs-doc-card__type"><?php echo esc_html(ucfirst($doc_type)); ?></span><?php endif; ?>
                        <?php if ($doc_desc) : ?><p><?php echo esc_html($doc_desc); ?></p><?php endif; ?>
                        <span class="lbs-doc-card__date"><?php echo esc_html(get_the_date('d M Y',$doc->ID)); ?></span>
                    </div>
                    <?php if ($file_url) : ?>
                    <a href="<?php echo esc_url($file_url); ?>" class="lbs-doc-card__download" download target="_blank">↓</a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Mood Boards -->
        <?php if ( ! empty($boards) ) : ?>
        <section class="lbs-portal-section">
            <h3><?php _e('Mood Boards','luxe-business-suite'); ?></h3>
            <?php foreach ($boards as $board) :
                $img_ids = get_post_meta( $board->ID, 'lbs_mb_images', true );
                if ( ! is_array($img_ids) ) $img_ids = array();
            ?>
            <div class="lbs-mood-board">
                <h4><?php echo esc_html(get_the_title($board->ID)); ?></h4>
                <?php if ( has_post_thumbnail($board->ID) || ! empty($img_ids) ) : ?>
                <div class="lbs-mood-board__grid">
                    <?php if ( has_post_thumbnail($board->ID) ) echo get_the_post_thumbnail($board->ID, array(300,200), array('loading'=>'lazy')); ?>
                    <?php foreach ( $img_ids as $img_id ) :
                        $img_id = absint($img_id);
                        if ( ! $img_id ) continue;
                        echo wp_get_attachment_image($img_id, array(300,200), false, array('loading'=>'lazy'));
                    endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if ( get_post_field('post_content',$board->ID) ) : ?>
                <div class="lbs-mood-board__desc"><?php echo wpautop(wp_kses_post(get_post_field('post_content',$board->ID))); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <!-- Progress Updates -->
        <?php if ( ! empty($updates) ) : ?>
        <section class="lbs-portal-section">
            <h3><?php _e('Project Updates','luxe-business-suite'); ?></h3>
            <div class="lbs-updates-feed">
                <?php foreach ($updates as $upd) : ?>
                <div class="lbs-update-entry">
                    <span class="lbs-update-entry__date"><?php echo $upd['date'] ? esc_html(date_i18n('d M Y', strtotime($upd['date']))) : ''; ?></span>
                    <p class="lbs-update-entry__text"><?php echo esc_html($upd['text']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

    </div>
    <?php
}
