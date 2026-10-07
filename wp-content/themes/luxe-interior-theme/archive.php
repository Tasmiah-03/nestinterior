<?php get_header(); ?>

<div class="page-hero">
    <div class="container">
        <?php if ( is_category() ) : ?>
            <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('Category', 'luxe-interior'); ?></span>
            <h1><?php single_cat_title(); ?></h1>
            <?php if ( category_description() ) : ?>
            <p style="color:rgba(255,255,255,0.65);margin-top:1rem;max-width:560px;"><?php echo wp_kses_post( category_description() ); ?></p>
            <?php endif; ?>
        <?php elseif ( is_tag() ) : ?>
            <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('Tag', 'luxe-interior'); ?></span>
            <h1><?php single_tag_title(); ?></h1>
        <?php elseif ( is_author() ) : ?>
            <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('Author', 'luxe-interior'); ?></span>
            <h1><?php the_author_meta('display_name', get_query_var('author')); ?></h1>
        <?php elseif ( is_date() ) : ?>
            <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('Archive', 'luxe-interior'); ?></span>
            <h1><?php echo esc_html( get_the_archive_title() ); ?></h1>
        <?php else : ?>
            <h1><?php esc_html_e('Journal', 'luxe-interior'); ?></h1>
        <?php endif; ?>
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
                        <?php the_post_thumbnail('luxe-card', array('loading'=>'lazy')); ?>
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
                <div style="grid-column:1/-1;text-align:center;padding:4rem 0;">
                    <p><?php esc_html_e('No posts found in this category.', 'luxe-interior'); ?></p>
                    <a href="<?php echo esc_url( home_url('/journal') ); ?>" class="btn" style="margin-top:1.5rem;"><?php esc_html_e('View All Posts', 'luxe-interior'); ?></a>
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
