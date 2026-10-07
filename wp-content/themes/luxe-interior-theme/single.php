<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php if ( has_post_thumbnail() ) : ?>
    <div class="post-hero">
        <?php the_post_thumbnail('luxe-hero', array('loading'=>'eager')); ?>
        <div class="post-hero-overlay">
            <div class="container">
                <div class="post-hero-content">
                    <?php $cats = get_the_category(); if ($cats) : ?>
                    <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" style="text-decoration:none;">
                        <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php echo esc_html($cats[0]->name); ?></span>
                    </a>
                    <?php endif; ?>
                    <h1><?php the_title(); ?></h1>
                    <div style="display:flex;align-items:center;gap:1.5rem;margin-top:1.25rem;color:rgba(255,255,255,0.55);font-size:0.82rem;">
                        <span><?php echo esc_html(get_the_author()); ?></span>
                        <span>·</span>
                        <span><?php echo esc_html(get_the_date('F j, Y')); ?></span>
                        <span>·</span>
                        <span><?php
                        $words = str_word_count(strip_tags(get_the_content()));
                        echo esc_html(max(1, ceil($words/200)) . ' min read');
                        ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else : ?>
    <div class="page-hero">
        <div class="container">
            <?php $cats = get_the_category(); if ($cats) : ?>
            <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php echo esc_html($cats[0]->name); ?></span>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
        </div>
    </div>
    <?php endif; ?>

    <section>
        <div class="container">
            <div style="display:grid;grid-template-columns:1fr 300px;gap:5rem;align-items:start;">
                <div>
                    <div class="post-content"><?php the_content(); ?></div>

                    <?php $tags = get_the_tags(); if ($tags) : ?>
                    <div style="margin-top:2.5rem;padding-top:2rem;border-top:1px solid var(--color-border);display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                        <span style="font-size:0.72rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:var(--color-muted);">Tags:</span>
                        <?php foreach ($tags as $tag) : ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post-tag"><?php echo esc_html($tag->name); ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div style="display:flex;gap:1.5rem;align-items:flex-start;padding:2rem;background:var(--color-cream);margin-top:2.5rem;">
                        <?php echo get_avatar(get_the_author_meta('email'), 64, '', '', array('style'=>'border-radius:50%;flex-shrink:0;')); ?>
                        <div>
                            <h5 style="font-family:var(--font-body);font-size:0.72rem;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;color:var(--color-muted);margin-bottom:0.25rem;">Written by</h5>
                            <div style="font-weight:600;margin-bottom:0.5rem;"><?php the_author(); ?></div>
                            <p style="font-size:0.85rem;margin:0;"><?php echo esc_html(get_the_author_meta('description') ?: 'Interior designer and founder of Luxe Interior Studio, Dhaka.'); ?></p>
                        </div>
                    </div>

                    <?php comments_template(); ?>
                </div>
                <div style="position:sticky;top:120px;"><?php get_sidebar(); ?></div>
            </div>
        </div>
    </section>
</article>

<?php
$cats = get_the_category();
if ($cats) {
    $related = new WP_Query(array('post_type'=>'post','posts_per_page'=>3,'post__not_in'=>array(get_the_ID()),'category__in'=>array($cats[0]->term_id),'post_status'=>'publish'));
    if ($related->have_posts()) : ?>
<section style="background:var(--color-cream);">
    <div class="container">
        <div class="section-header" data-reveal>
            <div class="section-header-text">
                <span class="eyebrow">Continue Reading</span>
                <h2>Related Articles</h2>
            </div>
        </div>
        <div class="blog-grid">
            <?php while ($related->have_posts()) : $related->the_post();
                $r_cats = get_the_category(); $r_cat = $r_cats ? $r_cats[0]->name : 'Design'; ?>
            <article class="post-card" data-reveal>
                <a href="<?php the_permalink(); ?>" class="post-card-image">
                    <?php if (has_post_thumbnail()) : the_post_thumbnail('luxe-card',array('loading'=>'lazy'));
                    else : ?><div style="aspect-ratio:4/3;background:var(--color-border);"></div><?php endif; ?>
                </a>
                <div class="post-card-meta"><span class="post-tag"><?php echo esc_html($r_cat); ?></span><?php luxe_posted_on(); ?></div>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p><?php echo wp_trim_words(get_the_excerpt(),18,'…'); ?></p>
                <div class="post-card-footer"><a href="<?php the_permalink(); ?>" class="read-more">Read Article <span class="read-more-arrow">→</span></a></div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
    <?php endif; }
?>
<?php endwhile; ?>
<?php get_footer(); ?>
