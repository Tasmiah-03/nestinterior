<?php get_header(); ?>

<div class="page-hero">
    <div class="container">
        <span class="eyebrow"><?php esc_html_e('The Journal', 'luxe-interior'); ?></span>
        <h1><?php esc_html_e('Ideas &amp; Inspiration', 'luxe-interior'); ?></h1>
    </div>
</div>

<section>
    <div class="container">
        <div class="blog-grid">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
                $cats = get_the_category();
                $cat_name = $cats ? $cats[0]->name : 'Design'; ?>
                <article class="post-card" data-reveal>
                        <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="post-card-image">
                            <?php the_post_thumbnail('luxe-card', array('loading' => 'lazy')); ?>
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
                            <?php esc_html_e('Read Article', 'luxe-interior'); ?> <span class="read-more-arrow">→</span>
                        </a>
                    </div>
                </article>
            <?php endwhile;
            else : ?>
                <div class="no-posts">
                    <p><?php esc_html_e('No posts found. Check back later for new stories and inspiration.', 'luxe-interior'); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php the_posts_pagination(array(
            'prev_text' => '← ' . __('Previous', 'luxe-interior'),
            'next_text' => __('Next', 'luxe-interior') . ' →',
        )); ?>
    </div>
</section>

<?php get_footer(); ?>
