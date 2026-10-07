<?php get_header(); ?>

<!-- ============================================================
     HERO
============================================================ -->
<section class="hero">
    <div class="hero-bg" id="hero-slider">
        <?php $hero_image = get_theme_mod('hero_image', ''); ?>
        <?php if ($hero_image) : ?>
        <div class="hero-image active">
            <img src="<?php echo esc_url($hero_image); ?>" alt="<?php esc_attr_e('Interior Design Hero', 'luxe-interior'); ?>" loading="eager">
        </div>
        <?php else : ?>
        <div class="hero-image active">
            <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder-1.jpg" alt="Luxury Living" loading="eager" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="hero-image">
            <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder-2.jpg" alt="Modern Architecture" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="hero-image">
            <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder-3.jpg" alt="Elegant Space" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php endif; ?>
    </div>
    <div class="hero-overlay"></div>

    <div class="container">
        <div class="hero-content">
            <span class="eyebrow"><?php echo esc_html( get_theme_mod('hero_eyebrow', 'Award-Winning Interior Design Studio') ); ?></span>
            <h1><?php echo wp_kses_post( get_theme_mod('hero_title', 'Crafting Spaces That <em>Inspire</em> &amp; Endure') ); ?></h1>
            <p><?php echo esc_html( get_theme_mod('hero_subtitle', 'We blend timeless aesthetics with purposeful function to transform interiors into deeply personal sanctuaries.') ); ?></p>
            <div class="hero-actions">
                <a href="<?php echo esc_url( home_url('/portfolio') ); ?>" class="btn btn-accent"><?php esc_html_e('View Our Work', 'luxe-interior'); ?></a>
                <a href="<?php echo esc_url( home_url('/book-consultation') ); ?>" class="btn btn-ghost"><?php esc_html_e('Book a Consultation', 'luxe-interior'); ?></a>
            </div>
        </div>
    </div>

    <div class="hero-scroll" aria-hidden="true">
        <span><?php esc_html_e('Scroll', 'luxe-interior'); ?></span>
    </div>
</section>

<!-- ============================================================
     SERVICES STRIP
============================================================ -->
<div class="services-strip">
    <div class="container">
        <span class="eyebrow" style="margin-bottom:var(--space-md);display:block;text-align:center;"><?php esc_html_e('What We Do', 'luxe-interior'); ?></span>
        <div class="services-grid">
            <div class="service-item" data-reveal>
                <div class="service-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h4><?php esc_html_e('Residential Design', 'luxe-interior'); ?></h4>
                <p><?php esc_html_e('Bespoke homes that reflect your identity and elevate everyday living.', 'luxe-interior'); ?></p>
            </div>
            <div class="service-item" data-reveal>
                <div class="service-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <h4><?php esc_html_e('Commercial Interiors', 'luxe-interior'); ?></h4>
                <p><?php esc_html_e('Offices, retail spaces, and hospitality venues that work beautifully.', 'luxe-interior'); ?></p>
            </div>
            <div class="service-item" data-reveal>
                <div class="service-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                </div>
                <h4><?php esc_html_e('Lighting Design', 'luxe-interior'); ?></h4>
                <p><?php esc_html_e('Layered, atmospheric lighting that transforms mood and perception.', 'luxe-interior'); ?></p>
            </div>
            <div class="service-item" data-reveal>
                <div class="service-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                </div>
                <h4><?php esc_html_e('FF&E Sourcing', 'luxe-interior'); ?></h4>
                <p><?php esc_html_e('Global access to exceptional furniture, fixtures, and finishes.', 'luxe-interior'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     PORTFOLIO
============================================================ -->
<section class="portfolio-section">
    <div class="container">
        <div class="section-header">
            <div class="section-header-text" data-reveal>
                <span class="eyebrow"><?php esc_html_e('Selected Work', 'luxe-interior'); ?></span>
                <h2><?php esc_html_e('Projects That Define Us', 'luxe-interior'); ?></h2>
                <p><?php esc_html_e('From intimate residences to landmark commercial spaces, each project is a collaboration built on trust, vision, and craft.', 'luxe-interior'); ?></p>
            </div>
            <a href="<?php echo esc_url( home_url('/portfolio') ); ?>" class="btn" data-reveal="right"><?php esc_html_e('View All Projects', 'luxe-interior'); ?></a>
        </div>

        <div class="portfolio-filter" role="group" aria-label="<?php esc_attr_e('Filter projects', 'luxe-interior'); ?>">
            <button class="filter-btn active" data-category="all"><?php esc_html_e('All', 'luxe-interior'); ?></button>
            <?php
            $terms = luxe_get_portfolio_categories();
            if ( ! is_wp_error($terms) ) :
                foreach ( $terms as $term ) : ?>
                    <button class="filter-btn" data-category="<?php echo esc_attr($term->slug); ?>">
                        <?php echo esc_html($term->name); ?>
                    </button>
                <?php endforeach;
            endif; ?>
        </div>

        <div class="portfolio-grid">
            <?php
            $portfolio_query = new WP_Query(array(
                'post_type'      => 'portfolio',
                'posts_per_page' => 6,
                'post_status'    => 'publish',
            ));
            $i = 0;
            while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post();
                $i++;
                $terms = get_the_terms( get_the_ID(), 'portfolio_category' );
                $cat_slug = ($terms && !is_wp_error($terms)) ? $terms[0]->slug : '';
                $cat_name = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'Design';
            ?>
            <div class="portfolio-card" data-category="<?php echo esc_attr($cat_slug); ?>" data-lightbox>
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail('luxe-portfolio', array('alt' => get_the_title(), 'loading' => ($i <= 2 ? 'eager' : 'lazy'))); ?>
                <?php else : ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder-<?php echo $i; ?>.jpg"
                         alt="<?php echo esc_attr(get_the_title()); ?>"
                         style="width:100%;height:100%;object-fit:cover;background:<?php echo $i % 2 ? '#2C2420' : '#3D3028'; ?>">
                <?php endif; ?>
                <div class="portfolio-card-overlay">
                    <div class="portfolio-card-info">
                        <h3><?php the_title(); ?></h3>
                        <span class="tag"><?php echo esc_html($cat_name); ?></span>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>

<!-- ============================================================
     DESIGN PROCESS
============================================================ -->
<section class="process-section" style="background: var(--color-warm-white); padding-block: var(--space-2xl);">
    <div class="container">
        <div class="section-header" style="text-align: center; justify-content: center; margin-bottom: var(--space-xl);">
            <div class="section-header-text" data-reveal>
                <span class="eyebrow"><?php esc_html_e('How We Work', 'luxe-interior'); ?></span>
                <h2><?php esc_html_e('Our Design Process', 'luxe-interior'); ?></h2>
                <p style="margin-inline: auto;"><?php esc_html_e('A systematic approach to creating spaces that feel entirely effortless.', 'luxe-interior'); ?></p>
            </div>
        </div>
        
        <div class="grid-3" style="gap: 3rem;">
            <div class="process-step" data-reveal>
                <div style="font-family: var(--font-display); font-size: 4rem; color: var(--color-accent); opacity: 0.3; line-height: 1; margin-bottom: 1rem;">01</div>
                <h4 style="color: var(--color-charcoal); margin-bottom: 1rem;"><?php esc_html_e('Discovery & Concept', 'luxe-interior'); ?></h4>
                <p><?php esc_html_e('We begin by listening deeply to your lifestyle, aesthetics, and functional needs. This phase yields the creative direction and spatial strategy.', 'luxe-interior'); ?></p>
            </div>
            <div class="process-step" data-reveal style="transition-delay: 0.2s;">
                <div style="font-family: var(--font-display); font-size: 4rem; color: var(--color-accent); opacity: 0.3; line-height: 1; margin-bottom: 1rem;">02</div>
                <h4 style="color: var(--color-charcoal); margin-bottom: 1rem;"><?php esc_html_e('Detailed Design', 'luxe-interior'); ?></h4>
                <p><?php esc_html_e('Concepts are refined into precise architectural drawings, material selections, and lighting plans, leaving nothing to chance.', 'luxe-interior'); ?></p>
            </div>
            <div class="process-step" data-reveal style="transition-delay: 0.4s;">
                <div style="font-family: var(--font-display); font-size: 4rem; color: var(--color-accent); opacity: 0.3; line-height: 1; margin-bottom: 1rem;">03</div>
                <h4 style="color: var(--color-charcoal); margin-bottom: 1rem;"><?php esc_html_e('Execution & Styling', 'luxe-interior'); ?></h4>
                <p><?php esc_html_e('We oversee all procurement, construction, and installation, delivering a finished home complete down to the final object.', 'luxe-interior'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     ABOUT
============================================================ -->
<div class="about-section">
    <div class="about-image" data-reveal="left">
        <?php
        $about_page = get_page_by_path('about');
        if ( $about_page && has_post_thumbnail($about_page->ID) ) :
            echo get_the_post_thumbnail($about_page->ID, 'luxe-wide', array('alt' => __('About Luxe Interior', 'luxe-interior')));
        else : ?>
            <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder-4.jpg" alt="Our Studio" style="width:100%;height:100%;object-fit:cover;">
        <?php endif; ?>
    </div>
    <div class="about-content" data-reveal="right">
        <span class="eyebrow"><?php esc_html_e('Our Story', 'luxe-interior'); ?></span>
        <h2><?php esc_html_e('Design That Earns Its Place', 'luxe-interior'); ?></h2>
        <p><?php esc_html_e('Founded in Dhaka in 2010, Luxe Interior has spent over a decade building spaces that are deeply considered, beautifully made, and built to last. We believe great design begins with listening.', 'luxe-interior'); ?></p>
        <p><?php esc_html_e('Our multidisciplinary team of architects, interior designers, and craftspeople approaches every project with the same conviction: a space should honor both the people who live in it and the world it inhabits.', 'luxe-interior'); ?></p>
        <a href="<?php echo esc_url( home_url('/about') ); ?>" class="btn" style="margin-top:1rem;">
            <?php esc_html_e('Learn Our Story', 'luxe-interior'); ?>
        </a>
        <div class="about-stats">
            <div>
                <div class="stat-num" data-count="14" data-suffix="+">14+</div>
                <div class="stat-label"><?php esc_html_e('Years of Practice', 'luxe-interior'); ?></div>
            </div>
            <div>
                <div class="stat-num" data-count="280" data-suffix="+">280+</div>
                <div class="stat-label"><?php esc_html_e('Projects Completed', 'luxe-interior'); ?></div>
            </div>
            <div>
                <div class="stat-num" data-count="11">11</div>
                <div class="stat-label"><?php esc_html_e('Design Awards', 'luxe-interior'); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     TESTIMONIALS
============================================================ -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <div class="section-header-text" data-reveal>
                <span class="eyebrow"><?php esc_html_e('Client Stories', 'luxe-interior'); ?></span>
                <h2><?php esc_html_e('What Our Clients Say', 'luxe-interior'); ?></h2>
            </div>
        </div>
        <div class="testimonials-grid">
            <?php
            $testi_query = new WP_Query(array(
                'post_type'      => 'testimonial',
                'posts_per_page' => 3,
                'post_status'    => 'publish',
            ));
            while ( $testi_query->have_posts() ) : $testi_query->the_post();
                $role = get_post_meta( get_the_ID(), 'client_role', true );
                $initials = implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', get_the_title()), 0, 2)));
            ?>
            <div class="testimonial-card" data-reveal>
                <div class="quote-mark">&ldquo;</div>
                <p><?php the_content(); ?></p>
                <div class="testimonial-author">
                    <div class="author-avatar"><?php echo esc_html($initials); ?></div>
                    <div>
                        <div class="author-name"><?php the_title(); ?></div>
                        <?php if ($role) : ?>
                        <div class="author-role"><?php echo esc_html($role); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata();

            // Fallback if no testimonials yet
            if ( ! $testi_query->post_count ) :
                $fallbacks = array(
                    array('name' => 'Fareeha R.', 'role' => 'Residential Client, Gulshan', 'quote' => 'Luxe Interior transformed our apartment beyond what we imagined possible. They understood our lifestyle within the first meeting and translated it into a home that genuinely feels like us — only better.'),
                    array('name' => 'Omar & Tasneem', 'role' => 'Villa Project, Baridhara', 'quote' => 'We gave them an impossible brief: a family home that feels like a boutique hotel. They delivered exactly that, on budget and six weeks ahead of schedule. The craftsmanship is extraordinary.'),
                    array('name' => 'Nahida S.', 'role' => 'CEO, Nexus Group', 'quote' => 'As a business owner, I needed a space that would impress clients and inspire my team. The offices have changed how people feel about coming to work. The feedback has been extraordinary.'),
                );
                foreach ($fallbacks as $fb) :
                    $initials = implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $fb['name']), 0, 2)));
            ?>
            <div class="testimonial-card" data-reveal>
                <div class="quote-mark">&ldquo;</div>
                <p><?php echo esc_html($fb['quote']); ?></p>
                <div class="testimonial-author">
                    <div class="author-avatar"><?php echo esc_html($initials); ?></div>
                    <div>
                        <div class="author-name"><?php echo esc_html($fb['name']); ?></div>
                        <div class="author-role"><?php echo esc_html($fb['role']); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     BLOG
============================================================ -->
<section>
    <div class="container">
        <div class="section-header">
            <div class="section-header-text" data-reveal>
                <span class="eyebrow"><?php esc_html_e('The Journal', 'luxe-interior'); ?></span>
                <h2><?php esc_html_e('Ideas, Insights &amp; Inspiration', 'luxe-interior'); ?></h2>
                <p><?php esc_html_e('Perspectives on design from our studio — trends, project stories, and guides to living beautifully.', 'luxe-interior'); ?></p>
            </div>
            <a href="<?php echo esc_url( home_url('/journal') ); ?>" class="btn" data-reveal="right"><?php esc_html_e('Read All Posts', 'luxe-interior'); ?></a>
        </div>

        <div class="blog-grid">
            <?php
            $blog_query = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => 3,
                'post_status'    => 'publish',
            ));
            while ( $blog_query->have_posts() ) : $blog_query->the_post();
                $cats = get_the_category();
                $cat_name = $cats ? $cats[0]->name : 'Design';
            ?>
            <article class="post-card" data-reveal>
                <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_permalink(); ?>" class="post-card-image" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                    <?php the_post_thumbnail('luxe-card', array('alt' => get_the_title(), 'loading' => 'lazy')); ?>
                </a>
                <?php endif; ?>
                <div class="post-card-meta">
                    <span class="post-tag"><?php echo esc_html($cat_name); ?></span>
                    <?php luxe_posted_on(); ?>
                </div>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p><?php echo wp_trim_words( get_the_excerpt(), 22, '&hellip;' ); ?></p>
                <div class="post-card-footer">
                    <a href="<?php the_permalink(); ?>" class="read-more">
                        <?php esc_html_e('Read Article', 'luxe-interior'); ?>
                        <span class="read-more-arrow">→</span>
                    </a>
                    <?php luxe_posted_by(); ?>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>

<!-- ============================================================
     NEWSLETTER
============================================================ -->
<div class="newsletter-section">
    <div class="container">
        <h2 data-reveal><?php esc_html_e('Stay Inspired', 'luxe-interior'); ?></h2>
        <p data-reveal><?php esc_html_e('Monthly dispatches from the studio — new projects, design perspectives, and ideas for living beautifully.', 'luxe-interior'); ?></p>
        <form class="newsletter-form" data-reveal action="#" method="post">
            <?php wp_nonce_field('luxe_newsletter_nonce'); ?>
            <input type="email" name="email" placeholder="<?php esc_attr_e('Your email address', 'luxe-interior'); ?>" required>
            <button type="submit"><?php esc_html_e('Subscribe', 'luxe-interior'); ?></button>
        </form>
    </div>
</div>

<?php get_footer(); ?>
