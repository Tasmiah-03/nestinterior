<?php
/**
 * Template Name: About Page
 */
get_header(); ?>

<div class="page-hero">
    <div class="container">
        <span class="eyebrow"><?php esc_html_e('Our Studio', 'luxe-interior'); ?></span>
        <h1><?php esc_html_e('The Story Behind the Spaces', 'luxe-interior'); ?></h1>
    </div>
</div>

<!-- Intro -->
<section>
    <div class="container">
        <div class="grid-2" style="align-items:center;gap:5rem;">
            <div data-reveal="left">
                <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('Since 2010', 'luxe-interior'); ?></span>
                <h2><?php esc_html_e('Design Built on a Single Conviction', 'luxe-interior'); ?></h2>
                <p style="margin-top:1.5rem;"><?php esc_html_e('Great design is not a luxury — it is the difference between a space you tolerate and a space that genuinely improves your life. Since founding our studio in Dhaka in 2010, this conviction has guided every project we take on.', 'luxe-interior'); ?></p>
                <p><?php esc_html_e('We work slowly, thoughtfully, and collaboratively. We take on fewer projects so that every client receives the full attention of our senior team. We source materials with care and commission craftspeople whose work we genuinely admire.', 'luxe-interior'); ?></p>
                <p><?php esc_html_e('The result is work we are proud to put our name to — and spaces that our clients never want to leave.', 'luxe-interior'); ?></p>
            </div>
            <div data-reveal="right">
                <div class="about-stats" style="grid-template-columns:1fr 1fr;gap:2rem;display:grid;margin-top:0;padding-top:0;border-top:none;">
                    <?php $stats = array(
                        array('num' => '14+', 'label' => __('Years of Practice', 'luxe-interior'), 'count' => '14', 'suffix' => '+'),
                        array('num' => '280+', 'label' => __('Projects Completed', 'luxe-interior'), 'count' => '280', 'suffix' => '+'),
                        array('num' => '11', 'label' => __('Design Awards', 'luxe-interior'), 'count' => '11', 'suffix' => ''),
                        array('num' => '18', 'label' => __('Team Members', 'luxe-interior'), 'count' => '18', 'suffix' => ''),
                    );
                    foreach ($stats as $s) : ?>
                    <div style="padding:2rem;background:var(--color-cream);">
                        <div class="stat-num" data-count="<?php echo esc_attr($s['count']); ?>" data-suffix="<?php echo esc_attr($s['suffix']); ?>">
                            <?php echo esc_html($s['num']); ?>
                        </div>
                        <div class="stat-label"><?php echo esc_html($s['label']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section style="background:var(--color-charcoal);">
    <div class="container">
        <div class="section-header" style="justify-content:center;text-align:center;flex-direction:column;align-items:center;">
            <span class="eyebrow" style="color:var(--color-accent);"><?php esc_html_e('What Drives Us', 'luxe-interior'); ?></span>
            <h2 style="color:#fff;margin-top:0.75rem;"><?php esc_html_e('Our Core Values', 'luxe-interior'); ?></h2>
        </div>
        <div class="services-grid" style="margin-top:0;">
            <?php $values = array(
                array('icon' => '◆', 'title' => __('Craft First', 'luxe-interior'), 'desc' => __('We believe in making things properly. Every detail matters — the quality of a joint, the weight of a handle, the way light falls on a surface.', 'luxe-interior')),
                array('icon' => '◇', 'title' => __('Deep Listening', 'luxe-interior'), 'desc' => __('Great design begins with understanding how people actually live. We spend more time listening than presenting — it shows in every project.', 'luxe-interior')),
                array('icon' => '○', 'title' => __('Timeless Over Trendy', 'luxe-interior'), 'desc' => __('We design for longevity. A space should feel as considered in fifteen years as it does on completion day.', 'luxe-interior')),
                array('icon' => '□', 'title' => __('Responsibility', 'luxe-interior'), 'desc' => __('We specify materials with care for their environmental impact and prioritize local craftspeople and sustainable sources wherever possible.', 'luxe-interior')),
            );
            foreach ($values as $v) : ?>
            <div class="service-item" data-reveal>
                <div class="service-icon" style="font-size:1.2rem;color:var(--color-accent);"><?php echo esc_html($v['icon']); ?></div>
                <h4><?php echo esc_html($v['title']); ?></h4>
                <p><?php echo esc_html($v['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Team -->
<section>
    <div class="container">
        <div class="section-header" data-reveal>
            <div class="section-header-text">
                <span class="eyebrow"><?php esc_html_e('The People', 'luxe-interior'); ?></span>
                <h2><?php esc_html_e('Meet the Studio', 'luxe-interior'); ?></h2>
            </div>
        </div>
        <div class="grid-3">
            <?php $team = array(
                array('name' => 'Layla Rahman', 'role' => __('Founder & Principal Designer', 'luxe-interior'), 'bio' => __('With 18 years of practice and a degree from the Architectural Association in London, Layla founded the studio with a vision to bring considered, craft-led design to Bangladesh.', 'luxe-interior')),
                array('name' => 'Tariq Hossain', 'role' => __('Head of Architecture', 'luxe-interior'), 'bio' => __('Tariq leads our architectural projects, with particular expertise in structural interventions, extensions, and the relationship between interior and exterior space.', 'luxe-interior')),
                array('name' => 'Nadia Chowdhury', 'role' => __('Senior Interior Designer', 'luxe-interior'), 'bio' => __('Nadia specializes in residential projects and has an exceptional eye for material combinations, color, and the layering of textiles and soft furnishings.', 'luxe-interior')),
            );
            foreach ($team as $member) : ?>
            <div class="post-card" data-reveal>
                <div style="aspect-ratio:3/4;background:linear-gradient(135deg,var(--color-cream),#D8CFC4);display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
                    <span style="font-family:var(--font-display);font-size:5rem;color:var(--color-accent);opacity:0.4;"><?php echo esc_html(substr($member['name'], 0, 1)); ?></span>
                </div>
                <h4 style="font-family:var(--font-display);font-size:1.4rem;margin-bottom:0.25rem;"><?php echo esc_html($member['name']); ?></h4>
                <div class="eyebrow" style="margin-bottom:0.75rem;"><?php echo esc_html($member['role']); ?></div>
                <p style="font-size:0.9rem;"><?php echo esc_html($member['bio']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
