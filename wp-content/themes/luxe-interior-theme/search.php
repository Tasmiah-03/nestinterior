<?php get_header(); ?>

<div class="page-hero">
    <div class="container">
        <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('Search Results', 'luxe-interior'); ?></span>
        <h1>
            <?php
            printf(
                /* translators: %s: search query */
                esc_html__('Results for: "%s"', 'luxe-interior'),
                '<em>' . esc_html( get_search_query() ) . '</em>'
            );
            ?>
        </h1>
    </div>
</div>

<section>
    <div class="container">
        <?php if ( have_posts() ) : ?>
            <p style="margin-bottom:2.5rem;color:var(--color-muted);">
                <?php printf( esc_html__('%d results found', 'luxe-interior'), $wp_query->found_posts ); ?>
            </p>
            <div class="blog-grid">
                <?php while ( have_posts() ) : the_post();
                    $cats = get_the_category();
                    $cat_name = $cats ? $cats[0]->name : ucfirst(get_post_type()); ?>
                    <article class="post-card" data-reveal>
                        <a href="<?php the_permalink(); ?>" class="post-card-image">
                            <?php if (has_post_thumbnail()) :
                                the_post_thumbnail('luxe-card',array('loading'=>'lazy'));
                            else : ?>
                                <div style="width:100%;aspect-ratio:4/3;background:var(--color-cream);"></div>
                            <?php endif; ?>
                        </a>
                        <div class="post-card-meta">
                            <span class="post-tag"><?php echo esc_html($cat_name); ?></span>
                            <?php luxe_posted_on(); ?>
                        </div>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p><?php echo wp_trim_words(get_the_excerpt(),20,'…'); ?></p>
                        <div class="post-card-footer">
                            <a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Read More','luxe-interior'); ?> <span class="read-more-arrow">→</span></a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <div style="text-align:center;padding:5rem 0;">
                <h3><?php esc_html_e('Nothing found', 'luxe-interior'); ?></h3>
                <p style="margin-bottom:2rem;"><?php esc_html_e('Try a different search term, or browse our journal.', 'luxe-interior'); ?></p>
                <form role="search" method="get" action="<?php echo esc_url( home_url('/') ); ?>" style="max-width:400px;margin-inline:auto;">
                    <div class="search-form">
                        <input type="search" class="search-field" placeholder="<?php esc_attr_e('Try again…','luxe-interior'); ?>" name="s" value="<?php echo esc_attr(get_search_query()); ?>">
                        <button type="submit" class="search-submit">→</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
