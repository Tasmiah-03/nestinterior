<footer id="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="logo">
                    <?php bloginfo('name'); ?><span>.</span>
                </a>
                <p><?php esc_html_e('We transform interiors into deeply personal sanctuaries — spaces that honor how you live, how you work, and how you dream.', 'luxe-interior'); ?></p>
                <div class="social-links">
                    <?php foreach ( luxe_social_links() as $key => $social ) : ?>
                        <a href="<?php echo esc_url($social['url']); ?>" class="social-link" aria-label="<?php echo esc_attr($social['label']); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo wp_kses( $social['icon'], array(
                            'svg'  => array( 'xmlns' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'viewBox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'aria-hidden' => true ),
                            'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ),
                            'path' => array( 'd' => true, 'fill' => true, 'stroke' => true ),
                            'line' => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ),
                            'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ),
                        ) ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e('Navigate', 'luxe-interior'); ?></h5>
                <ul>
                    <li><a href="<?php echo esc_url( home_url('/about') ); ?>"><?php esc_html_e('About Studio', 'luxe-interior'); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url('/services') ); ?>"><?php esc_html_e('Our Services', 'luxe-interior'); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url('/portfolio') ); ?>"><?php esc_html_e('Portfolio', 'luxe-interior'); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url('/journal') ); ?>"><?php esc_html_e('Journal', 'luxe-interior'); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url('/contact') ); ?>"><?php esc_html_e('Contact', 'luxe-interior'); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e('Services', 'luxe-interior'); ?></h5>
                <ul>
                    <li><a href="#"><?php esc_html_e('Residential Design', 'luxe-interior'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Commercial Interiors', 'luxe-interior'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Kitchen & Bath', 'luxe-interior'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Furniture Sourcing', 'luxe-interior'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Space Planning', 'luxe-interior'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Project Management', 'luxe-interior'); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e('Contact', 'luxe-interior'); ?></h5>
                <ul>
                    <li><a href="tel:<?php echo esc_attr( get_theme_mod('contact_phone', '+88017-1234-5678') ); ?>">
                        <?php echo esc_html( get_theme_mod('contact_phone', '+880 17 1234 5678') ); ?>
                    </a></li>
                    <li><a href="mailto:<?php echo esc_attr( get_theme_mod('contact_email', 'hello@luxeinterior.com') ); ?>">
                        <?php echo esc_html( get_theme_mod('contact_email', 'hello@luxeinterior.com') ); ?>
                    </a></li>
                    <li><?php echo esc_html( get_theme_mod('contact_address', '47 Design Avenue, Gulshan 1, Dhaka') ); ?></li>
                    <li><?php echo esc_html( get_theme_mod('contact_hours', 'Mon–Sat: 9am – 6pm') ); ?></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo esc_html( date('Y') ); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'luxe-interior'); ?></p>
            <p>
                <a href="<?php echo esc_url( home_url('/privacy-policy') ); ?>"><?php esc_html_e('Privacy Policy', 'luxe-interior'); ?></a>
                &nbsp;·&nbsp;
                <a href="<?php echo esc_url( home_url('/terms') ); ?>"><?php esc_html_e('Terms', 'luxe-interior'); ?></a>
            </p>
        </div>
    </div>
</footer>

<style>
/* Lightbox */
.lightbox-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(10,8,6,0.95);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.3s ease;
    padding: 2rem;
}
.lightbox-overlay.visible { opacity: 1; }
.lightbox-inner { position: relative; max-width: 90vw; max-height: 90vh; }
.lightbox-inner img { max-width: 100%; max-height: 80vh; object-fit: contain; }
.lightbox-close {
    position: absolute; top: -2.5rem; right: 0;
    background: none; border: none; color: #fff;
    font-size: 1.2rem; cursor: pointer; opacity: 0.7;
    transition: opacity 0.2s;
}
.lightbox-close:hover { opacity: 1; }
.lightbox-caption { color: rgba(255,255,255,0.6); font-size: 0.85rem; text-align: center; margin-top: 1rem; }

/* Custom cursor */
.luxe-cursor {
    width: 16px; height: 16px;
    border-radius: 50%;
    background: var(--color-accent);
    position: fixed; top: 0; left: 0;
    pointer-events: none; z-index: 99999;
    mix-blend-mode: multiply;
    transition: transform 0.15s ease, width 0.3s ease, height 0.3s ease;
    will-change: transform;
}
.luxe-cursor.cursor-hover { width: 36px; height: 36px; background: rgba(176,141,106,0.3); border: 1px solid var(--color-accent); }
@media (max-width: 768px) { .luxe-cursor { display: none; } }

/* Reveal animations */
[data-reveal] { opacity: 0; transform: translateY(24px); transition: opacity 0.7s ease, transform 0.7s ease; }
[data-reveal].revealed { opacity: 1; transform: none; }
[data-reveal="left"] { transform: translateX(-24px); }
[data-reveal="left"].revealed { transform: none; }
[data-reveal="right"] { transform: translateX(24px); }
[data-reveal="right"].revealed { transform: none; }

/* Mobile menu styles now in header.php */
</style>

<?php wp_footer(); ?>
</body>
</html>
