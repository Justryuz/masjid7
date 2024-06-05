<?php
/**
 * The "News List" template to show post's content
 *
 * Used in the widget Recent News.
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */
 
$widget_args = get_query_var('trx_addons_args_recent_news');
$style = $widget_args['style'];
$number = $widget_args['number'];
$count = $widget_args['count'];
$columns = $widget_args['columns'];
$post_format = get_post_format();
$post_format = empty($post_format) ? 'standard' : str_replace('post-format-', '', $post_format);
$animation = apply_filters('trx_addons_blog_animation', '');

if ((int)$columns > 1) {
    ?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $columns)); ?>"><?php
}
?><article 
	<?php post_class( 'post_item post_layout_'.esc_attr($style)
					.' post_format_'.esc_attr($post_format)
					); ?>
	<?php echo (!empty($animation) ? ' data-animation="'.esc_attr($animation).'"' : ''); ?>
	>

	<?php
	if ( is_sticky() && is_home() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	?><div class="post_body">
        <div class="post_header entry-header"><?php

            the_title( '<h4 class="post_title entry-title"><a href="'.esc_url(get_permalink()).'" rel="bookmark">', '</a></h4>' );

            if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
                ?><div class="post_meta"><span class="post_date"><a href="<?php the_permalink(); ?>"><?php echo get_the_date(); ?></a></span><?php
                if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
                    trx_addons_get_template_part('templates/tpl.post-counters.php',
                                                    'trx_addons_args_post_counters',
                                                    array(
                                                        'counters' => 'comments'
                                                    )
                                                );
                }
                ?></div><?php
            }
            ?>
			</div><!-- .entry-header -->

	</div><!-- .post_body -->

</article><?php
if ((int)$columns > 1) {
    ?></div><?php
}