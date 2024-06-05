<?php
/**
 * The template to display login link
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0.1
 */

// Display link
$args = get_query_var('trx_addons_args_login');

// If user not logged in
if ( !is_user_logged_in() ) {
	?><a href="#trx_addons_login_popup" class="trx_addons_popup_link trx_addons_login_link "><?php
		?><span class="sc_layouts_item_icon sc_layouts_login_icon trx_addons_icon-user-alt"></span><?php
		if (!empty($args['text_login'])) {
			?><span class="sc_layouts_item_details sc_layouts_login_details"><?php
				$rows = explode('|', $args['text_login']);
				if (!empty($rows[0])) {
					?><span class="sc_layouts_item_details_line1 sc_layouts_iconed_text_line1"><?php echo esc_html($rows[0]); ?></span><?php
				}
				if (!empty($rows[1])) {
					?><span class="sc_layouts_item_details_line2 sc_layouts_iconed_text_line2"><?php echo esc_html($rows[1]); ?></span><?php
				}
			?></span><?php
		}
	?></a><?php

// Else if user logged in
} else {
	if (empty($args['user_menu'])) {
		?><a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="trx_addons_login_link"><?php
	} else {
		?><span class="trx_addons_login_link"><?php
	}
		?><span class="sc_layouts_item_icon sc_layouts_login_icon trx_addons_icon-<?php echo empty($args['user_menu']) ? 'user-times' : 'user-alt'; ?>"></span><?php
		if (!empty($args['text_logout'])) {
			?><span class="sc_layouts_item_details sc_layouts_login_details"><?php
				$current_user = wp_get_current_user();
				$rows = explode('|', str_replace('%s',
												$current_user->user_firstname,	// user_login or user_firstname or user_lastname or display_name
												$args['text_logout'])
								);
				if (!empty($rows[0])) {
					?><span class="sc_layouts_item_details_line1 sc_layouts_iconed_text_line1"><?php echo esc_html($rows[0]); ?></span><?php
				}
				if (!empty($rows[1])) {
					?><span class="sc_layouts_item_details_line2 sc_layouts_iconed_text_line2"><?php echo esc_html($rows[1]); ?></span><?php
				}
			?></span><?php
		}
	if (empty($args['user_menu'])) {
		?></a><?php 
	} else {
			?><span class="trx_addons_login_menu"><?php
				do_action('trx_addons_action_login_menu_start');
				// New post
				if (current_user_can('publish_posts')) {
					?><a href="<?php echo esc_url(home_url('/')); ?>/wp-admin/post-new.php?post_type=post" class="trx_addons_login_menu_item trx_addons_icon-wpforms"><?php esc_html_e('New post', 'trx_addons'); ?></a><?php
					// Delimiter
					?><span class="trx_addons_login_menu_delimiter"></span><?php
				}
				do_action('trx_addons_action_login_menu_settings');
				// Settings
				?><a href="<?php echo esc_url(get_edit_user_link()); ?>" class="trx_addons_login_menu_item trx_addons_icon-cog"><?php esc_html_e('My profile', 'trx_addons'); ?></a><?php
				// Delimiter
				?><span class="trx_addons_login_menu_delimiter"></span><?php
				do_action('trx_addons_action_login_menu_logout');
				// Logout
				?><a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="trx_addons_login_menu_item trx_addons_icon-user-times"><?php esc_html_e('Logout', 'trx_addons'); ?></a><?php
				do_action('trx_addons_action_login_menu_end');
			?></span><?php 
		?></span><?php 
	}
}
?>