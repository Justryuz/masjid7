<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( !function_exists( 'shaha_trx_donations_get_css' ) ) {
	add_filter( 'shaha_filter_get_css', 'shaha_trx_donations_get_css', 10, 4 );
	function shaha_trx_donations_get_css($css, $colors, $fonts, $scheme='') {
		if (isset($css['fonts']) && $fonts) {
			$css['fonts'] .= <<<CSS
.sc_donations_info .sc_donations_supporters_item_amount_value,
.sc_donations_info .sc_donations_supporters_item_name {
	{$fonts['h5_font-family']}
}
.single-donation .nav-links .post-title,
.post_type_donation.post_item_single .post_supporters_title {
	{$fonts['p_font-family']}
}
CSS;
		}

		if (isset($css['colors']) && $colors) {
			$css['colors'] .= <<<CSS
.sc_donations_info .sc_donations_data_number {
	color: {$colors['text_dark']};
}
.sc_donations_info .sc_donations_supporters_item_amount_inner,
.sc_donations_info .sc_donations_supporters_item_info_inner {
	background-color: {$colors['alter_bg_color']};
}
.sc_donations_info .sc_donations_supporters_item:hover .sc_donations_supporters_item_amount_inner,
.sc_donations_info .sc_donations_supporters_item:hover .sc_donations_supporters_item_info_inner {
	background-color: {$colors['alter_bg_hover']};
}
.sc_donations_info .sc_donations_supporters_item_amount_value {
	color: {$colors['alter_link']};
}
.sc_donations_info .sc_donations_supporters_item_name {
	color: {$colors['alter_dark']};
}
.sc_donations_info .sc_donations_supporters_item_amount_date,
.sc_donations_info .sc_donations_supporters_item_message {
	color: {$colors['alter_text']};
}

.sc_donations_info .sc_donations_scale_raised {
	background-color: {$colors['text_link']};
}
.sc_donations_info .sc_donations_scale_raised .sc_donations_data_label {
	color: {$colors['text_hover2']};
}
.sc_donations_info .sc_donations_data_number {
	color: {$colors['text']};
}
.sc_donations_info .sc_donations_data_number .sc_donations_data_percent {
	color: {$colors['text_dark']};
}

.single-donation .nav-links .meta-nav,
.post_type_donation.post_item_single .post_sidebar .post_help{
	color: {$colors['text']};
}
.post_type_donation .post_goal_amount {
	color: {$colors['text_link']};
}
.post_type_donation.post_item_single .post_sidebar .post_raised .post_raised_amount {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
}
.sc_donations_form_field_note:before {
	color: {$colors['inverse_text']};
}
.sc_donations_form_field_note {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
	border-color: {$colors['text_link']};
}
.sc_donations_form_field_note b{
	color: {$colors['inverse_text']};
}
.single-donation .nav-links a:before,
.post_type_donation .sc_donations_form_submit {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_hover']};
	border-color: {$colors['text_hover']};
}
.single-donation .nav-links a:hover:before,
.post_type_donation .sc_donations_form_submit:hover {
	color: {$colors['inverse_text']};
	background-color: {$colors['text_link']};
	border-color: {$colors['text_link']};
}
.single-donation .nav-links{
	border-color: {$colors['bd_color']};
}

CSS;
		}
		
		return $css;
	}
}
?>