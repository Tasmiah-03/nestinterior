<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
    $terms    = get_the_terms( get_the_ID(), 'portfolio_category' );
    $cat_name = ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';
    $gallery  = get_post_meta( get_the_ID(), 'project_gallery', true );
    $location = get_post_meta( get_the_ID(), 'project_location', true );
    $year     = get_post_meta( get_the_ID(), 'project_year', true );
    $area     = get_post_meta( get_the_ID(), 'project_area', true );
    $scope    = get_post_meta( get_the_ID(), 'project_scope', true );
?>

<!-- PROJECT HERO -->
<div class="post-hero" style="height:90vh;">
    <?php if ( has_post_thumbnail() ) :
        the_post_thumbnail( 'luxe-hero', array('loading'=>'eager') );
    else : ?>
        <div style="width:100%;height:100%;background:linear-gradient(135deg,#1C1814,#3D3028);"></div>
    <?php endif; ?>
    <div class="post-hero-overlay" style="background:linear-gradient(to top,rgba(10,8,6,0.9) 0%,rgba(10,8,6,0.2) 60%,transparent 100%);">
        <div class="container">
            <div class="post-hero-content">
                <?php if ($cat_name) : ?>
                <span class="eyebrow" style="display:block;margin-bottom:1rem;color:var(--color-accent);"><?php echo esc_html($cat_name); ?></span>
                <?php endif; ?>
                <h1 style="color:#fff;font-size:clamp(2.5rem,5vw,5rem);"><?php the_title(); ?></h1>
                <p style="color:rgba(255,255,255,0.65);font-size:1.1rem;max-width:560px;margin-top:1rem;"><?php the_excerpt(); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- PROJECT META BAR -->
<div style="background:var(--color-charcoal);padding:1.75rem 0;">
    <div class="container">
        <div style="display:flex;gap:3rem;flex-wrap:wrap;">
            <?php $meta_items = array_filter(array(
                $cat_name ? array('label'=>__('Category','luxe-interior'),'value'=>$cat_name) : null,
                $location ? array('label'=>__('Location','luxe-interior'),'value'=>$location) : null,
                $year     ? array('label'=>__('Year','luxe-interior'),'value'=>$year) : null,
                $area     ? array('label'=>__('Area','luxe-interior'),'value'=>$area) : null,
                $scope    ? array('label'=>__('Scope','luxe-interior'),'value'=>$scope) : null,
            ));
            if (empty($meta_items)) $meta_items = array(
                array('label'=>__('Category','luxe-interior'), 'value'=>($cat_name ?: __('Interior Design','luxe-interior'))),
                array('label'=>__('Location','luxe-interior'), 'value'=>__('Dhaka, Bangladesh','luxe-interior')),
                array('label'=>__('Scope','luxe-interior'),    'value'=>__('Full Interior Design','luxe-interior')),
            );
            foreach ($meta_items as $item) : ?>
            <div>
                <div style="font-size:0.65rem;font-weight:600;letter-spacing:0.15em;text-transform:uppercase;color:var(--color-accent);margin-bottom:0.3rem;"><?php echo esc_html($item['label']); ?></div>
                <div style="color:#fff;font-size:0.9rem;"><?php echo esc_html($item['value']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- PROJECT CONTENT -->
<section>
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 320px;gap:5rem;align-items:start;">
            <div class="post-content" data-reveal>
                <?php the_content(); ?>
            </div>
            <div style="position:sticky;top:120px;" data-reveal="right">
                <div style="background:var(--color-cream);padding:2.5rem;">
                    <h5 style="margin-bottom:1.5rem;"><?php esc_html_e('Project Details', 'luxe-interior'); ?></h5>
                    <?php
                    $details = array(
                        __('Client','luxe-interior')   => get_post_meta(get_the_ID(),'project_client',true) ?: __('Private Residence','luxe-interior'),
                        __('Location','luxe-interior') => $location ?: __('Dhaka, Bangladesh','luxe-interior'),
                        __('Year','luxe-interior')     => $year ?: get_the_date('Y'),
                        __('Area','luxe-interior')     => $area ?: __('Contact for details','luxe-interior'),
                        __('Category','luxe-interior') => $cat_name ?: __('Interior Design','luxe-interior'),
                    );
                    foreach ($details as $label => $value) : ?>
                    <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--color-border);font-size:0.82rem;">
                        <span style="color:var(--color-muted);font-weight:500;"><?php echo esc_html($label); ?></span>
                        <span style="color:var(--color-charcoal);text-align:right;max-width:55%;"><?php echo esc_html($value); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <a href="<?php echo esc_url( home_url('/contact') ); ?>" class="btn btn-accent" style="width:100%;justify-content:center;display:flex;margin-top:1.75rem;">
                        <?php esc_html_e('Start a Similar Project', 'luxe-interior'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PROJECT IMAGE GRID -->
<?php
$images = array();
if ( $gallery ) {
    $ids = explode(',', $gallery);
    foreach ($ids as $id) {
        $src = wp_get_attachment_image_src(trim($id), 'luxe-portfolio');
        if ($src) $images[] = array('src'=>$src[0],'alt'=>get_post_field('post_title',trim($id)));
    }
}
if ($images) : ?>
<section style="background:var(--color-cream);">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1.5rem;">
            <?php foreach ($images as $img) : ?>
            <div style="overflow:hidden;">
                <img src="<?php echo esc_url($img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy"
                     style="width:100%;height:400px;object-fit:cover;transition:transform 0.8s ease;"
                     onmouseenter="this.style.transform='scale(1.04)'" onmouseleave="this.style.transform='scale(1)'">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- RELATED PROJECTS -->
<?php
$related = new WP_Query(array(
    'post_type'      => 'portfolio',
    'posts_per_page' => 3,
    'post__not_in'   => array(get_the_ID()),
    'orderby'        => 'rand',
    'post_status'    => 'publish',
));
if ($related->have_posts()) : ?>
<section>
    <div class="container">
        <div class="section-header" data-reveal>
            <div class="section-header-text">
                <span class="eyebrow"><?php esc_html_e('More Work', 'luxe-interior'); ?></span>
                <h2><?php esc_html_e('Related Projects', 'luxe-interior'); ?></h2>
            </div>
            <a href="<?php echo esc_url( home_url('/portfolio') ); ?>" class="btn"><?php esc_html_e('View All', 'luxe-interior'); ?></a>
        </div>
        <div class="grid-3">
            <?php while ($related->have_posts()) : $related->the_post();
                $r_terms = get_the_terms(get_the_ID(),'portfolio_category');
                $r_cat = ($r_terms && !is_wp_error($r_terms)) ? $r_terms[0]->name : ''; ?>
            <div class="portfolio-card" style="aspect-ratio:4/3;" data-reveal>
                <?php if (has_post_thumbnail()) : the_post_thumbnail('luxe-portfolio',array('loading'=>'lazy'));
                else : ?><div style="width:100%;height:100%;background:var(--color-charcoal);"></div><?php endif; ?>
                <div class="portfolio-card-overlay">
                    <div class="portfolio-card-info">
                        <h3 style="font-size:1.3rem;"><?php the_title(); ?></h3>
                        <?php if ($r_cat) : ?><span class="tag"><?php echo esc_html($r_cat); ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
