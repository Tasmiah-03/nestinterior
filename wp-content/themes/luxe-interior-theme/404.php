<?php get_header(); ?>

<section style="min-height:80vh;display:flex;align-items:center;justify-content:center;text-align:center;padding-top:80px;">
    <div class="container">
        <span style="font-family:var(--font-display);font-size:12rem;color:var(--color-border);line-height:1;display:block;">404</span>
        <h2 style="margin-top:-2rem;margin-bottom:1rem;"><?php esc_html_e('Room Not Found', 'luxe-interior'); ?></h2>
        <p style="max-width:420px;margin-inline:auto;margin-bottom:2rem;"><?php esc_html_e('This space doesn\'t exist — or has been moved. Let\'s find you somewhere beautiful to land.', 'luxe-interior'); ?></p>
        <a href="<?php echo esc_url( home_url('/') ); ?>" class="btn btn-accent"><?php esc_html_e('Return Home', 'luxe-interior'); ?></a>
    </div>
</section>

<?php get_footer(); ?>
