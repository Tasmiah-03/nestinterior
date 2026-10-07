<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); 
    $status = get_post_meta(get_the_ID(), 'logistic_status', true);
    $percentage = get_post_meta(get_the_ID(), 'logistic_progress', true) ?: 0;
    $client = get_post_meta(get_the_ID(), 'logistic_client', true);
    $budget = get_post_meta(get_the_ID(), 'logistic_budget', true);
    
    $stages = array(
        'discovery'    => __('Discovery & Briefing', 'luxe-interior'),
        'concept'      => __('Concept Design', 'luxe-interior'),
        'development'  => __('Design Development', 'luxe-interior'),
        'procurement'  => __('Procurement & FF&E', 'luxe-interior'),
        'installation' => __('Installation & Styling', 'luxe-interior'),
        'completed'    => __('Project Completed', 'luxe-interior'),
    );
    $current_stage_label = isset($stages[$status]) ? $stages[$status] : __('Unknown Stage', 'luxe-interior');
?>

<div class="page-hero">
    <div class="container">
        <span class="eyebrow" style="display:block;margin-bottom:1rem;">Client Portal</span>
        <h1><?php echo esc_html( $client ? $client . "'s Project" : get_the_title() ); ?></h1>
    </div>
</div>

<section style="padding: 4rem 0 6rem; background: var(--color-cream);">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto; background: var(--color-light); padding: 3rem; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.05);">
            
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2rem; margin-bottom: 0.5rem;">Project Status</h2>
                <p style="color: var(--color-muted); font-size: 1.1rem;"><?php echo esc_html($current_stage_label); ?></p>
            </div>

            <!-- Progress Bar -->
            <div style="margin-bottom: 3rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-weight: 600; font-family: var(--font-heading);">
                    <span>Overall Progress</span>
                    <span><?php echo esc_html($percentage); ?>%</span>
                </div>
                <div style="height: 12px; background: #E5E0DA; border-radius: 6px; overflow: hidden;">
                    <div style="height: 100%; background: var(--color-accent); width: <?php echo esc_attr($percentage); ?>%; transition: width 1s ease-in-out;"></div>
                </div>
            </div>

            <!-- Details Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; border-top: 1px solid var(--color-border); padding-top: 2rem;">
                <div>
                    <h5 style="font-family: var(--font-body); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--color-muted); margin-bottom: 0.5rem;">Client Name</h5>
                    <div style="font-size: 1.1rem; font-weight: 500;"><?php echo esc_html($client ?: 'N/A'); ?></div>
                </div>
                <div>
                    <h5 style="font-family: var(--font-body); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--color-muted); margin-bottom: 0.5rem;">Estimated Budget</h5>
                    <div style="font-size: 1.1rem; font-weight: 500;"><?php echo esc_html($budget ?: 'N/A'); ?></div>
                </div>
            </div>

            <!-- Timeline Stages -->
            <div style="margin-top: 3rem; border-top: 1px solid var(--color-border); padding-top: 2rem;">
                <h5 style="font-family: var(--font-body); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--color-muted); margin-bottom: 1.5rem;">Project Timeline</h5>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php 
                    $stage_keys = array_keys($stages);
                    $current_index = array_search($status, $stage_keys);
                    if ($current_index === false) $current_index = -1;

                    foreach ($stages as $key => $label) :
                        $index = array_search($key, $stage_keys);
                        $is_completed = $index < $current_index;
                        $is_current = $index === $current_index;
                        
                        $icon = $is_completed ? '✓' : ($is_current ? '●' : '○');
                        $color = $is_completed ? 'var(--color-accent)' : ($is_current ? 'var(--color-dark)' : 'var(--color-muted)');
                        $font_weight = $is_current ? '600' : '400';
                    ?>
                    <div style="display: flex; align-items: center; gap: 1rem; color: <?php echo $color; ?>;">
                        <span style="font-size: 1.2rem; line-height: 1; width: 24px; text-align: center;"><?php echo $icon; ?></span>
                        <span style="font-weight: <?php echo $font_weight; ?>; font-size: 1.05rem;"><?php echo esc_html($label); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
