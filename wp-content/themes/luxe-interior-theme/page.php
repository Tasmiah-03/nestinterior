<?php get_header(); ?>

<div class="page-hero">
    <div class="container">
        <h1><?php the_title(); ?></h1>
    </div>
</div>

<section>
    <div class="container" style="max-width:860px;">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="post-content">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php get_footer(); ?>
