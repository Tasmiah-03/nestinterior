<?php
/**
 * Template Name: Portfolio Page
 */
get_header(); ?>

<div class="page-hero">
    <div class="container">
        <span class="eyebrow"><?php esc_html_e('Our Work', 'luxe-interior'); ?></span>
        <h1><?php the_title(); ?></h1>
    </div>
</div>

<section>
    <div class="container">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <div class="page-content" data-reveal>
                <?php the_content(); ?>
            </div>
        <?php endwhile; endif; ?>
    </div>
</section>

<section class="portfolio-section" style="background:var(--color-cream);">
    <div class="container">
        <div class="portfolio-filter" role="group">
            <button class="filter-btn active" data-category="all"><?php esc_html_e('All Projects', 'luxe-interior'); ?></button>
            <?php
            $terms = luxe_get_portfolio_categories();
            if ( !is_wp_error($terms) ) :
                foreach ($terms as $term) : ?>
                    <button class="filter-btn" data-category="<?php echo esc_attr($term->slug); ?>">
                        <?php echo esc_html($term->name); ?> (<?php echo esc_html($term->count); ?>)
                    </button>
                <?php endforeach;
            endif; ?>
        </div>

        <div class="portfolio-grid" id="portfolio-grid">
            <?php
            $portfolio_query = new WP_Query(array(
                'post_type'      => 'portfolio',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            ));
            while ($portfolio_query->have_posts()) : $portfolio_query->the_post();
                $terms = get_the_terms(get_the_ID(), 'portfolio_category');
                $cat_slug = ($terms && !is_wp_error($terms)) ? $terms[0]->slug : '';
                $cat_name = ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';
            ?>
            <div class="portfolio-card" data-category="<?php echo esc_attr($cat_slug); ?>" data-lightbox>
                <?php if (has_post_thumbnail()) :
                    the_post_thumbnail('luxe-portfolio', array('loading' => 'lazy'));
                else : ?>
                    <div style="width:100%;height:100%;background:var(--color-charcoal);aspect-ratio:4/3;"></div>
                <?php endif; ?>
                <div class="portfolio-card-overlay">
                    <div class="portfolio-card-info">
                        <h3><?php the_title(); ?></h3>
                        <?php if ($cat_name) : ?>
                        <span class="tag"><?php echo esc_html($cat_name); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
