<?php if ( post_password_required() ) return; ?>

<div id="comments" class="comments-area">
    <?php if ( have_comments() ) : ?>
        <h3 class="comments-title" style="font-family:var(--font-display);font-size:1.8rem;margin-bottom:2rem;">
            <?php
            $comment_count = get_comments_number();
            printf(
                esc_html( _n( '%1$s Comment on "%2$s"', '%1$s Comments on "%2$s"', $comment_count, 'luxe-interior' ) ),
                number_format_i18n( $comment_count ),
                get_the_title()
            );
            ?>
        </h3>

        <ol class="comment-list" style="list-style:none;display:flex;flex-direction:column;gap:2rem;">
            <?php wp_list_comments(array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size'=> 48,
                'callback'   => function($comment, $args, $depth) {
                    $tag = ($comment->comment_type === 'pingback' || $comment->comment_type === 'trackback') ? 'div' : 'li';
                    echo '<' . esc_attr($tag) . ' id="comment-' . esc_attr(get_comment_ID()) . '" ' . comment_class('comment-item', '', '', false) . '>';
                    echo '<div style="display:flex;gap:1rem;padding:1.5rem;background:var(--color-cream);">';
                    echo '<div style="flex-shrink:0;">' . get_avatar($comment, 48, '', '', array('class'=>'comment-avatar','style'=>'border-radius:50%;')) . '</div>';
                    echo '<div style="flex:1;">';
                    echo '<div style="display:flex;align-items:baseline;gap:1rem;margin-bottom:0.5rem;">';
                    echo '<span style="font-weight:600;font-size:0.9rem;">' . get_comment_author_link() . '</span>';
                    echo '<span style="font-size:0.75rem;color:var(--color-muted);">' . get_comment_date() . '</span>';
                    if (!$comment->comment_approved) echo '<em style="font-size:0.75rem;color:var(--color-accent);">' . __('Awaiting moderation', 'luxe-interior') . '</em>';
                    echo '</div>';
                    comment_text();
                    echo '<div style="margin-top:0.75rem;">' . get_comment_reply_link(array('depth'=>$depth,'max_depth'=>$args['max_depth'],'reply_text'=>__('Reply →','luxe-interior'))) . '</div>';
                    echo '</div></div>';
                    echo '</' . esc_attr($tag) . '>';
                }
            )); ?>
        </ol>

        <?php the_comments_navigation(array(
            'prev_text' => '← ' . __('Older comments', 'luxe-interior'),
            'next_text' => __('Newer comments', 'luxe-interior') . ' →',
        )); ?>

    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments') ) : ?>
        <p style="color:var(--color-muted);font-style:italic;"><?php esc_html_e('Comments are closed.', 'luxe-interior'); ?></p>
    <?php endif; ?>

    <?php comment_form(array(
        'title_reply'        => __('Leave a Comment', 'luxe-interior'),
        'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title" style="font-family:var(--font-display);font-size:1.8rem;margin-bottom:2rem;">',
        'title_reply_after'  => '</h3>',
        'class_form'         => 'wpcf7-form',
        'class_submit'       => 'btn btn-accent',
        'label_submit'       => __('Post Comment', 'luxe-interior'),
        'comment_field'      => '<div class="form-group"><label for="comment">' . __('Comment *', 'luxe-interior') . '</label><textarea id="comment" name="comment" rows="6" required></textarea></div>',
        'fields' => array(
            'author' => '<div class="form-group" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;"><div><label for="author">' . __('Name *', 'luxe-interior') . '</label><input id="author" name="author" type="text" required></div>',
            'email'  => '<div><label for="email">' . __('Email *', 'luxe-interior') . '</label><input id="email" name="email" type="email" required></div></div>',
            'url'    => '<div class="form-group"><label for="url">' . __('Website', 'luxe-interior') . '</label><input id="url" name="url" type="url"></div>',
        ),
    )); ?>
</div>
