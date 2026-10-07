<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ============================================================
     SITE HEADER
============================================================ -->
<header id="site-header" role="banner">
    <div class="hdr-inner">

        <!-- Logo -->
        <div class="hdr-logo">
            <?php if ( has_custom_logo() ) :
                the_custom_logo();
            else : ?>
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="hdr-logo__text">
                    <?php bloginfo('name'); ?><span class="hdr-logo__dot">.</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Primary Nav (desktop) -->
        <nav class="hdr-nav" role="navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'luxe-interior'); ?>">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'hdr-nav__list',
                'fallback_cb'    => function() {
                    $links = array(
                        'About'     => '/about',
                        'Services'  => '/services',
                        'Portfolio' => '/portfolio',
                        'Journal'   => '/journal',
                        'Contact'   => '/contact',
                    );
                    echo '<ul class="hdr-nav__list">';
                    foreach ( $links as $label => $path ) {
                        echo '<li><a href="' . esc_url( home_url( $path ) ) . '">' . esc_html( $label ) . '</a></li>';
                    }
                    echo '</ul>';
                },
            ) );
            ?>
        </nav>

        <!-- Right actions -->
        <div class="hdr-actions">
            <a href="<?php echo esc_url( home_url('/book-consultation') ); ?>"
               class="hdr-cta-btn"
               id="hdr-book-btn">
                <?php esc_html_e( 'Book Consultation', 'luxe-interior' ); ?>
            </a>

            <!-- Hamburger (mobile) -->
            <button class="hdr-hamburger" id="hdr-hamburger"
                    aria-label="<?php esc_attr_e('Open menu', 'luxe-interior'); ?>"
                    aria-expanded="false"
                    aria-controls="hdr-mobile-menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

    </div><!-- .hdr-inner -->
</header>

<!-- ============================================================
     MOBILE MENU OVERLAY
============================================================ -->
<div class="hdr-mobile-overlay" id="hdr-mobile-overlay" aria-hidden="true"></div>

<nav class="hdr-mobile-menu" id="hdr-mobile-menu"
     aria-label="<?php esc_attr_e('Mobile Navigation', 'luxe-interior'); ?>"
     aria-hidden="true">

    <button class="hdr-mobile-close" id="hdr-mobile-close"
            aria-label="<?php esc_attr_e('Close menu', 'luxe-interior'); ?>">&#10005;</button>

    <div class="hdr-mobile-logo">
        <a href="<?php echo esc_url( home_url('/') ); ?>">
            <?php bloginfo('name'); ?><span>.</span>
        </a>
    </div>

    <?php
    wp_nav_menu( array(
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'hdr-mobile-list',
        'fallback_cb'    => function() {
            $links = array(
                'Home'      => '/',
                'About'     => '/about',
                'Services'  => '/services',
                'Portfolio' => '/portfolio',
                'Journal'   => '/journal',
                'Contact'   => '/contact',
            );
            echo '<ul class="hdr-mobile-list">';
            foreach ( $links as $label => $path ) {
                echo '<li><a href="' . esc_url( home_url( $path ) ) . '">' . esc_html( $label ) . '</a></li>';
            }
            echo '</ul>';
        },
    ) );
    ?>

    <a href="<?php echo esc_url( home_url('/book-consultation') ); ?>"
       class="hdr-cta-btn hdr-mobile-cta">
        <?php esc_html_e( 'Book a Consultation', 'luxe-interior' ); ?>
    </a>
</nav>

<style>
/* ============================================================
   HEADER — Professional Luxe Design
============================================================ */
#site-header {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 200;
    transition: background 0.35s ease, box-shadow 0.35s ease, padding 0.35s ease;
    padding: 0;
}

/* Transparent on hero pages; solid when scrolled */
#site-header.hdr-transparent { background: transparent; }
#site-header.hdr-scrolled {
    background: rgba(14, 11, 9, 0.96);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 1px 0 rgba(176,141,106,0.15), 0 4px 24px rgba(0,0,0,0.3);
}

/* Inner flex row */
.hdr-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 2rem;
    height: 76px;
    display: flex;
    align-items: center;
    gap: 2rem;
}

/* ── Logo ──────────────────────────────────────── */
.hdr-logo { flex-shrink: 0; }
.hdr-logo__text {
    font-family: var(--font-display, 'Cormorant Garamond', Georgia, serif);
    font-size: 1.55rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    color: #fff;
    text-decoration: none;
    white-space: nowrap;
    transition: color 0.2s;
}
.hdr-logo__text:hover { color: var(--color-accent, #B08D6A); }
.hdr-logo__dot { color: var(--color-accent, #B08D6A); }

/* Custom logo image sizing */
.hdr-logo .custom-logo-link img { max-height: 44px; width: auto; }

/* ── Desktop Nav ──────────────────────────────── */
.hdr-nav {
    flex: 1;
    display: flex;
    justify-content: center;
}
.hdr-nav__list {
    list-style: none;
    margin: 0; padding: 0;
    display: flex;
    align-items: center;
    gap: 0;
}
.hdr-nav__list li { position: relative; }
.hdr-nav__list > li > a {
    display: block;
    padding: 0.5rem 1.1rem;
    font-family: var(--font-body, 'Jost', sans-serif);
    font-size: 0.76rem;
    font-weight: 500;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.82);
    text-decoration: none;
    position: relative;
    transition: color 0.2s;
}
.hdr-nav__list > li > a::after {
    content: '';
    position: absolute;
    bottom: -2px; left: 1.1rem; right: 1.1rem;
    height: 1px;
    background: var(--color-accent, #B08D6A);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.25s ease;
}
.hdr-nav__list > li > a:hover,
.hdr-nav__list > li.current-menu-item > a,
.hdr-nav__list > li.current-page-ancestor > a {
    color: #fff;
}
.hdr-nav__list > li > a:hover::after,
.hdr-nav__list > li.current-menu-item > a::after {
    transform: scaleX(1);
}

/* Dropdown submenu */
.hdr-nav__list .sub-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    min-width: 210px;
    background: #1A1614;
    border: 1px solid rgba(176,141,106,0.2);
    border-top: 2px solid var(--color-accent, #B08D6A);
    padding: 0.5rem 0;
    list-style: none;
    margin: 0;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: opacity 0.2s, transform 0.2s, visibility 0.2s;
    box-shadow: 0 16px 48px rgba(0,0,0,0.4);
    z-index: 300;
}
.hdr-nav__list li:hover > .sub-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.hdr-nav__list .sub-menu a {
    display: block;
    padding: 0.65rem 1.25rem;
    font-size: 0.76rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.72);
    text-decoration: none;
    transition: color 0.2s, background 0.2s;
}
.hdr-nav__list .sub-menu a:hover {
    color: var(--color-accent, #B08D6A);
    background: rgba(255,255,255,0.04);
}

/* ── Right actions ────────────────────────────── */
.hdr-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-shrink: 0;
}

/* CTA button */
.hdr-cta-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.6rem 1.35rem;
    font-family: var(--font-body, 'Jost', sans-serif);
    font-size: 0.73rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    text-decoration: none;
    color: #fff;
    background: var(--color-accent, #B08D6A);
    border: 1.5px solid var(--color-accent, #B08D6A);
    border-radius: 2px;
    transition: background 0.25s, color 0.25s, border-color 0.25s, transform 0.2s;
    white-space: nowrap;
}
.hdr-cta-btn:hover {
    background: transparent;
    color: var(--color-accent, #B08D6A);
    transform: translateY(-1px);
}

/* ── Hamburger ────────────────────────────────── */
.hdr-hamburger {
    display: none;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    width: 36px;
    height: 36px;
    padding: 4px;
    background: none;
    border: none;
    cursor: pointer;
    z-index: 201;
}
.hdr-hamburger span {
    display: block;
    width: 22px;
    height: 1.5px;
    background: #fff;
    border-radius: 2px;
    transition: transform 0.3s ease, opacity 0.3s ease;
    transform-origin: center;
}
/* Animate to X when open */
.hdr-hamburger.is-open span:nth-child(1) { transform: rotate(45deg) translate(4.5px, 4.5px); }
.hdr-hamburger.is-open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
.hdr-hamburger.is-open span:nth-child(3) { transform: rotate(-45deg) translate(4.5px, -4.5px); }

/* ── Mobile Overlay ───────────────────────────── */
.hdr-mobile-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    z-index: 299;
    backdrop-filter: blur(2px);
    opacity: 0;
    transition: opacity 0.35s ease;
}
.hdr-mobile-overlay.is-open { opacity: 1; }

/* ── Mobile Menu Panel ────────────────────────── */
.hdr-mobile-menu {
    position: fixed;
    top: 0; right: 0;
    width: min(340px, 90vw);
    height: 100vh;
    background: #14110F;
    border-left: 1px solid rgba(176,141,106,0.2);
    z-index: 300;
    display: flex;
    flex-direction: column;
    padding: 2rem 2rem 3rem;
    transform: translateX(100%);
    transition: transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94);
    overflow-y: auto;
}
.hdr-mobile-menu.is-open { transform: translateX(0); }

.hdr-mobile-close {
    position: absolute;
    top: 1.25rem; right: 1.5rem;
    background: none;
    border: none;
    color: rgba(255,255,255,0.5);
    font-size: 1.25rem;
    cursor: pointer;
    line-height: 1;
    padding: 4px;
    transition: color 0.2s;
}
.hdr-mobile-close:hover { color: var(--color-accent, #B08D6A); }

.hdr-mobile-logo { margin: 1rem 0 2.5rem; }
.hdr-mobile-logo a {
    font-family: var(--font-display, serif);
    font-size: 1.4rem;
    font-weight: 600;
    color: #fff;
    text-decoration: none;
    letter-spacing: 0.04em;
}
.hdr-mobile-logo span { color: var(--color-accent, #B08D6A); }

.hdr-mobile-list {
    list-style: none;
    margin: 0; padding: 0;
    flex: 1;
}
.hdr-mobile-list li {
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.hdr-mobile-list > li > a {
    display: block;
    padding: 1rem 0;
    font-family: var(--font-display, serif);
    font-size: 1.5rem;
    color: rgba(255,255,255,0.82);
    text-decoration: none;
    transition: color 0.2s, padding-left 0.2s;
}
.hdr-mobile-list > li > a:hover {
    color: var(--color-accent, #B08D6A);
    padding-left: 0.5rem;
}
/* Mobile submenu */
.hdr-mobile-list .sub-menu {
    list-style: none;
    margin: 0; padding: 0 0 0.75rem 1rem;
}
.hdr-mobile-list .sub-menu a {
    display: block;
    padding: 0.4rem 0;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.5);
    text-decoration: none;
    letter-spacing: 0.04em;
    transition: color 0.2s;
}
.hdr-mobile-list .sub-menu a:hover { color: var(--color-accent, #B08D6A); }

.hdr-mobile-cta {
    margin-top: 2rem;
    justify-content: center;
    text-align: center;
}

/* ── Responsive breakpoints ───────────────────── */
@media (max-width: 1024px) {
    .hdr-nav { display: none; }
    .hdr-hamburger { display: flex; }
    .hdr-cta-btn:not(.hdr-mobile-cta) { display: none; }
}
@media (max-width: 480px) {
    .hdr-inner { padding: 0 1.25rem; height: 64px; }
}
</style>

<script>
/* Header: scroll state + mobile menu */
(function () {
    var header  = document.getElementById('site-header');
    var burger  = document.getElementById('hdr-hamburger');
    var overlay = document.getElementById('hdr-mobile-overlay');
    var panel   = document.getElementById('hdr-mobile-menu');
    var closeBtn= document.getElementById('hdr-mobile-close');

    /* Scroll-based class */
    function onScroll() {
        if (!header) return;
        if (window.scrollY > 50) {
            header.classList.add('hdr-scrolled');
            header.classList.remove('hdr-transparent');
        } else {
            header.classList.remove('hdr-scrolled');
            header.classList.add('hdr-transparent');
        }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll(); // run once on load

    /* Mobile menu open / close */
    function openMenu() {
        overlay.style.display = 'block';
        requestAnimationFrame(function () {
            overlay.classList.add('is-open');
            panel.classList.add('is-open');
        });
        burger.classList.add('is-open');
        burger.setAttribute('aria-expanded', 'true');
        panel.setAttribute('aria-hidden', 'false');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        overlay.classList.remove('is-open');
        panel.classList.remove('is-open');
        burger.classList.remove('is-open');
        burger.setAttribute('aria-expanded', 'false');
        panel.setAttribute('aria-hidden', 'true');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        setTimeout(function () { overlay.style.display = 'none'; }, 400);
    }

    if (burger)   burger.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (overlay)  overlay.addEventListener('click', closeMenu);

    /* Close menu when any mobile nav link is clicked */
    if (panel) {
        panel.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });
    }

    /* Escape key closes menu */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });
})();
</script>

