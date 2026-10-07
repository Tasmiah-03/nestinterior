<?php if ( ! is_active_sidebar('sidebar-1') && ! is_single() ) return; ?>

<aside class="sidebar" role="complementary" style="display:flex;flex-direction:column;gap:2.5rem;">

    <!-- Search -->
    <div class="widget">
        <h5 class="widget-title"><?php esc_html_e('Search', 'luxe-interior'); ?></h5>
        <form role="search" method="get" action="<?php echo esc_url( home_url('/') ); ?>">
            <div class="search-form">
                <input type="search" class="search-field" placeholder="<?php esc_attr_e('Search articles…', 'luxe-interior'); ?>" name="s" value="<?php echo esc_attr(get_search_query()); ?>">
                <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Search','luxe-interior'); ?>">→</button>
            </div>
        </form>
    </div>

    <!-- Categories -->
    <div class="widget">
        <h5 class="widget-title"><?php esc_html_e('Topics', 'luxe-interior'); ?></h5>
        <ul style="display:flex;flex-direction:column;gap:0.5rem;">
            <?php wp_list_categories(array(
                'title_li'   => '',
                'show_count' => true,
                'walker'     => new class extends Walker_Category {
                    function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
                        $count = $args['show_count'] ? ' <span style="color:var(--color-muted);font-size:0.8rem;">(' . esc_html($category->count) . ')</span>' : '';
                        $output .= '<li><a href="' . esc_url(get_category_link($category->term_id)) . '" style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--color-border);font-size:0.88rem;transition:color 0.2s;" onmouseenter="this.style.color=\'var(--color-accent)\'" onmouseleave="this.style.color=\'\'">' . esc_html($category->name) . $count . '</a></li>';
                    }
                },
            )); ?>
        </ul>
    </div>

    <!-- Recent Posts -->
    <div class="widget">
        <h5 class="widget-title"><?php esc_html_e('Recent Articles', 'luxe-interior'); ?></h5>
        <?php
        $recent = new WP_Query(array('post_type'=>'post','posts_per_page'=>4,'post_status'=>'publish'));
        while ($recent->have_posts()) : $recent->the_post(); ?>
        <a href="<?php the_permalink(); ?>" style="display:flex;gap:1rem;align-items:flex-start;margin-bottom:1.25rem;padding-bottom:1.25rem;border-bottom:1px solid var(--color-border);text-decoration:none;" onmouseenter="this.querySelector('h6').style.color='var(--color-accent)'" onmouseleave="this.querySelector('h6').style.color='var(--color-charcoal)'">
            <?php if (has_post_thumbnail()) : ?>
            <div style="width:64px;height:64px;flex-shrink:0;overflow:hidden;">
                <?php the_post_thumbnail('thumbnail',array('style'=>'width:100%;height:100%;object-fit:cover;')); ?>
            </div>
            <?php endif; ?>
            <div>
                <h6 style="font-family:var(--font-body);font-size:0.85rem;font-weight:500;line-height:1.4;color:var(--color-charcoal);transition:color 0.2s;margin-bottom:0.25rem;"><?php the_title(); ?></h6>
                <span style="font-size:0.72rem;color:var(--color-muted);"><?php echo esc_html(get_the_date('M j, Y')); ?></span>
            </div>
        </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>

    <!-- Portfolio Highlights -->
    <div class="widget">
        <h5 class="widget-title"><?php esc_html_e('Featured Projects', 'luxe-interior'); ?></h5>
        <?php
        $projects = new WP_Query(array('post_type'=>'portfolio','posts_per_page'=>3,'post_status'=>'publish','orderby'=>'rand'));
        while ($projects->have_posts()) : $projects->the_post(); ?>
        <a href="<?php the_permalink(); ?>" style="display:block;aspect-ratio:16/9;overflow:hidden;margin-bottom:0.75rem;position:relative;">
            <?php if (has_post_thumbnail()) :
                the_post_thumbnail('luxe-thumb',array('style'=>'width:100%;height:100%;object-fit:cover;transition:transform 0.5s ease;','onmouseenter'=>"this.style.transform='scale(1.05)'","onmouseleave"=>"this.style.transform='scale(1)'"));
            else : ?>
                <div style="width:100%;height:100%;background:var(--color-charcoal);"></div>
            <?php endif; ?>
            <div style="position:absolute;bottom:0;left:0;right:0;padding:0.75rem;background:linear-gradient(transparent,rgba(10,8,6,0.8));">
                <span style="font-size:0.75rem;color:#fff;font-weight:500;"><?php the_title(); ?></span>
            </div>
        </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>

    <!-- CTA Widget -->
    <div style="background:var(--color-charcoal);padding:2rem;">
        <h5 style="color:#fff;font-family:var(--font-display);font-size:1.4rem;font-weight:400;margin-bottom:0.75rem;"><?php esc_html_e('Transform Your Space', 'luxe-interior'); ?></h5>
        <p style="font-size:0.82rem;color:rgba(255,255,255,0.55);margin-bottom:1.5rem;"><?php esc_html_e('Ready to start your interior design project? Let\'s talk.', 'luxe-interior'); ?></p>
        <a href="<?php echo esc_url( home_url('/contact') ); ?>" class="btn" style="border-color:rgba(255,255,255,0.3);color:#fff;width:100%;justify-content:center;display:flex;font-size:0.7rem;"><?php esc_html_e('Book a Consultation', 'luxe-interior'); ?></a>
    </div>

    <?php dynamic_sidebar('sidebar-1'); ?>

</aside>
