<?php
/**
 * Template Name: Contact Page
 */
get_header(); ?>

<div class="page-hero">
    <div class="container">
        <span class="eyebrow"><?php esc_html_e('Get In Touch', 'luxe-interior'); ?></span>
        <h1><?php esc_html_e('Start a Conversation', 'luxe-interior'); ?></h1>
    </div>
</div>

<section>
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info" data-reveal="left">
                <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('Studio Information', 'luxe-interior'); ?></span>
                <h3 style="margin-bottom:2rem;"><?php esc_html_e('We\'d love to hear about your project', 'luxe-interior'); ?></h3>
                <p><?php esc_html_e('Whether you\'re planning a full renovation, refreshing a single room, or building from the ground up — every great space begins with a conversation. Let\'s talk.', 'luxe-interior'); ?></p>

                <div style="margin-top:2.5rem;">
                    <div class="contact-detail">
                        <div class="contact-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <div style="font-size:0.72rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:var(--color-muted);margin-bottom:0.25rem;"><?php esc_html_e('Studio Address', 'luxe-interior'); ?></div>
                            <div><?php echo esc_html( get_theme_mod('contact_address', '47 Design Avenue, Gulshan 1, Dhaka 1212') ); ?></div>
                        </div>
                    </div>
                    <div class="contact-detail">
                        <div class="contact-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z"/></svg>
                        </div>
                        <div>
                            <div style="font-size:0.72rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:var(--color-muted);margin-bottom:0.25rem;"><?php esc_html_e('Phone', 'luxe-interior'); ?></div>
                            <a href="tel:<?php echo esc_attr(get_theme_mod('contact_phone', '+88017-1234-5678')); ?>">
                                <?php echo esc_html(get_theme_mod('contact_phone', '+880 17 1234 5678')); ?>
                            </a>
                        </div>
                    </div>
                    <div class="contact-detail">
                        <div class="contact-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <div style="font-size:0.72rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:var(--color-muted);margin-bottom:0.25rem;"><?php esc_html_e('Email', 'luxe-interior'); ?></div>
                            <a href="mailto:<?php echo esc_attr(get_theme_mod('contact_email', 'hello@luxeinterior.com')); ?>">
                                <?php echo esc_html(get_theme_mod('contact_email', 'hello@luxeinterior.com')); ?>
                            </a>
                        </div>
                    </div>
                    <div class="contact-detail">
                        <div class="contact-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <div style="font-size:0.72rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:var(--color-muted);margin-bottom:0.25rem;"><?php esc_html_e('Studio Hours', 'luxe-interior'); ?></div>
                            <div><?php echo esc_html(get_theme_mod('contact_hours', 'Mon–Sat: 9:00am – 6:00pm')); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div data-reveal="right">
                <?php if ( shortcode_exists('contact-form-7') ) :
                    echo do_shortcode('[contact-form-7 id="contact-form" title="Contact Form"]');
                else : ?>
                <form class="lbs-contact-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field( 'lbs_contact_form', 'lbs_cf_nonce' ); ?>
                    <input type="hidden" name="action" value="lbs_contact_form">

                    <div class="form-group" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label for="contact_name"><?php esc_html_e('Full Name *', 'luxe-interior'); ?></label>
                            <input type="text" id="contact_name" name="lbs_name" required placeholder="<?php esc_attr_e('Sarah Ahmed', 'luxe-interior'); ?>">
                        </div>
                        <div>
                            <label for="contact_email"><?php esc_html_e('Email Address *', 'luxe-interior'); ?></label>
                            <input type="email" id="contact_email" name="lbs_email" required placeholder="sarah@example.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contact_phone"><?php esc_html_e('Phone Number', 'luxe-interior'); ?></label>
                        <input type="text" id="contact_phone" name="lbs_phone" placeholder="+880 17 XXXX XXXX">
                    </div>

                    <div class="form-group">
                        <label for="contact_service"><?php esc_html_e('Service Interest *', 'luxe-interior'); ?></label>
                        <select id="contact_service" name="lbs_service" required>
                            <option value=""><?php esc_html_e('Select a service…', 'luxe-interior'); ?></option>
                            <option value="residential"><?php esc_html_e('Residential Interior Design', 'luxe-interior'); ?></option>
                            <option value="commercial"><?php esc_html_e('Commercial Interiors', 'luxe-interior'); ?></option>
                            <option value="kitchen"><?php esc_html_e('Kitchen & Bathroom Design', 'luxe-interior'); ?></option>
                            <option value="ffe"><?php esc_html_e('Furniture & FF&E Sourcing', 'luxe-interior'); ?></option>
                            <option value="lighting"><?php esc_html_e('Lighting Design', 'luxe-interior'); ?></option>
                            <option value="pm"><?php esc_html_e('Project Management', 'luxe-interior'); ?></option>
                            <option value="consultation"><?php esc_html_e('Space Planning & Consultation', 'luxe-interior'); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contact_budget"><?php esc_html_e('Approximate Budget', 'luxe-interior'); ?></label>
                        <select id="contact_budget" name="lbs_budget">
                            <option value=""><?php esc_html_e('Select a range…', 'luxe-interior'); ?></option>
                            <option value="under-5l"><?php esc_html_e('Under ৳5 Lakh', 'luxe-interior'); ?></option>
                            <option value="5-15l"><?php esc_html_e('৳5 – 15 Lakh', 'luxe-interior'); ?></option>
                            <option value="15-50l"><?php esc_html_e('৳15 – 50 Lakh', 'luxe-interior'); ?></option>
                            <option value="50l-1cr"><?php esc_html_e('৳50 Lakh – 1 Crore', 'luxe-interior'); ?></option>
                            <option value="over-1cr"><?php esc_html_e('Over ৳1 Crore', 'luxe-interior'); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contact_message"><?php esc_html_e('Tell Us About Your Project *', 'luxe-interior'); ?></label>
                        <textarea id="contact_message" name="lbs_message" required placeholder="<?php esc_attr_e('Describe your space, your vision, your timeline — anything that helps us understand your project…', 'luxe-interior'); ?>"></textarea>
                    </div>

                    <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center;">
                        <?php esc_html_e('Send Message', 'luxe-interior'); ?>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
