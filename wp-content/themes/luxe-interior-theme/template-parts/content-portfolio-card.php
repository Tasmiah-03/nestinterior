<?php
/**
 * Template Part: Portfolio Card
 * Used in AJAX filter and portfolio archive
 */
$terms    = get_the_terms( get_the_ID(), 'portfolio_category' );
$cat_slug = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : '';
$cat_name = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
?>
<div class="portfolio-card" data-category="<?php echo esc_attr( $cat_slug ); ?>" data-lightbox>
    <?php if ( has_post_thumbnail() ) :
        the_post_thumbnail( 'luxe-portfolio', array( 'loading' => 'lazy', 'alt' => get_the_title() ) );
    else : ?>
        <div style="width:100%;height:100%;background:var(--color-charcoal);aspect-ratio:4/3;"></div>
    <?php endif; ?>
    <div class="portfolio-card-overlay">
        <div class="portfolio-card-info">
            <h3><?php the_title(); ?></h3>
            <?php if ( $cat_name ) : ?>
                <span class="tag"><?php echo esc_html( $cat_name ); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>
