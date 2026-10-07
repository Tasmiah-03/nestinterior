<?php
/**
 * Template Name: Home — Split Hero
 * Alternative homepage with editorial split layout
 */
get_header(); ?>

<!-- SPLIT HERO -->
<section style="display:grid;grid-template-columns:1fr 1fr;min-height:100vh;padding-top:80px;">
    <!-- Left: Content -->
    <div style="display:flex;flex-direction:column;justify-content:center;padding:clamp(2rem,8vw,7rem);background:var(--color-cream);">
        <span class="eyebrow" style="display:block;margin-bottom:2rem;"><?php echo esc_html(get_theme_mod('hero_eyebrow','Award-Winning Interior Design Studio')); ?></span>
        <h1 style="font-size:clamp(3rem,5vw,5rem);margin-bottom:2rem;">
            <?php echo wp_kses_post(get_theme_mod('hero_title','Crafting Spaces That <em>Inspire</em>')); ?>
        </h1>
        <p style="font-size:1.1rem;max-width:420px;margin-bottom:3rem;">
            <?php echo esc_html(get_theme_mod('hero_subtitle','We transform interiors into deeply personal sanctuaries — spaces that honor how you live.')); ?>
        </p>
        <div style="display:flex;flex-direction:column;gap:1rem;max-width:260px;">
            <a href="<?php echo esc_url(home_url('/portfolio')); ?>" class="btn btn-accent" style="justify-content:center;">View Portfolio</a>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn" style="justify-content:center;">Start a Project</a>
        </div>
        <!-- Stats strip -->
        <div style="display:flex;gap:2.5rem;margin-top:4rem;padding-top:2rem;border-top:1px solid var(--color-border);">
            <?php foreach(array(array('14+','Years'),array('280+','Projects'),array('11','Awards')) as $s) : ?>
            <div>
                <div style="font-family:var(--font-display);font-size:2rem;color:var(--color-charcoal);"><?php echo esc_html($s[0]); ?></div>
                <div style="font-size:0.7rem;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;color:var(--color-muted);"><?php echo esc_html($s[1]); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Right: Image grid -->
    <div style="display:grid;grid-template-rows:1fr 1fr;gap:3px;background:#1C1C1A;">
        <div style="overflow:hidden;background:linear-gradient(135deg,#2C2420,#3D3028);"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:3px;">
            <div style="overflow:hidden;background:linear-gradient(225deg,#1A1410,#2C1E14);"></div>
            <div style="overflow:hidden;background:linear-gradient(135deg,#241C10,#3D2E1A);"></div>
        </div>
    </div>
</section>

<!-- MARQUEE / CREDENTIAL STRIP -->
<div style="background:var(--color-charcoal);padding:1.25rem 0;overflow:hidden;">
    <div style="display:flex;gap:3rem;animation:marquee 20s linear infinite;white-space:nowrap;">
        <?php $creds = array('Residential Design','Commercial Interiors','Lighting Design','FF&E Sourcing','Project Management','Kitchen & Bath','Space Planning','Hospitality Design');
        for ($i=0;$i<3;$i++) foreach($creds as $c) : ?>
        <span style="font-size:0.72rem;font-weight:500;letter-spacing:0.15em;text-transform:uppercase;color:rgba(255,255,255,0.4);">
            <?php echo esc_html($c); ?> <span style="color:var(--color-accent);margin-left:1.5rem;">·</span>
        </span>
        <?php endforeach; ?>
    </div>
    <style>@keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-33.33%)}}</style>
</div>

<!-- PORTFOLIO GRID (same as main homepage) -->
<section style="background:var(--color-cream);">
    <div class="container">
        <div class="section-header">
            <div class="section-header-text" data-reveal>
                <span class="eyebrow">Selected Work</span>
                <h2>Projects That Define Us</h2>
            </div>
            <a href="<?php echo esc_url(home_url('/portfolio')); ?>" class="btn" data-reveal="right">All Projects</a>
        </div>
        <div class="portfolio-grid">
            <?php
            $q = new WP_Query(array('post_type'=>'portfolio','posts_per_page'=>6,'post_status'=>'publish'));
            while($q->have_posts()) : $q->the_post();
                $t = get_the_terms(get_the_ID(),'portfolio_category');
                $cs = ($t&&!is_wp_error($t))?$t[0]->slug:'';
                $cn = ($t&&!is_wp_error($t))?$t[0]->name:''; ?>
            <div class="portfolio-card" data-category="<?php echo esc_attr($cs); ?>" data-lightbox>
                <?php if(has_post_thumbnail()) : the_post_thumbnail('luxe-portfolio',array('loading'=>'lazy'));
                else : ?><div style="width:100%;height:100%;background:var(--color-charcoal);aspect-ratio:4/3;"></div><?php endif; ?>
                <div class="portfolio-card-overlay">
                    <div class="portfolio-card-info">
                        <h3><?php the_title(); ?></h3>
                        <?php if($cn) : ?><span class="tag"><?php echo esc_html($cn); ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
