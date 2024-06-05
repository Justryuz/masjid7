<?php
if (($shaha_slider_sc = shaha_get_theme_option('front_page_title_shortcode')) != '' && strpos($shaha_slider_sc, '[')!==false && strpos($shaha_slider_sc, ']')!==false) {

	?><div class="front_page_section front_page_section_title front_page_section_slider front_page_section_title_slider"><?php
		// Add anchor
		$shaha_anchor_icon = shaha_get_theme_option('front_page_title_anchor_icon');	
		$shaha_anchor_text = shaha_get_theme_option('front_page_title_anchor_text');	
		if ((!empty($shaha_anchor_icon) || !empty($shaha_anchor_text)) && shortcode_exists('trx_sc_anchor')) {
			echo do_shortcode('[trx_sc_anchor id="front_page_section_title"'
											. (!empty($shaha_anchor_icon) ? ' icon="'.esc_attr($shaha_anchor_icon).'"' : '')
											. (!empty($shaha_anchor_text) ? ' title="'.esc_attr($shaha_anchor_text).'"' : '')
											. ']');
		}
		// Show slider (or any other content, generated by shortcode)
		echo do_shortcode($shaha_slider_sc);
	?></div><?php

} else {

	?><div class="front_page_section front_page_section_title<?php
				$shaha_scheme = shaha_get_theme_option('front_page_title_scheme');
				if (!shaha_is_inherit($shaha_scheme)) echo ' scheme_'.esc_attr($shaha_scheme);
				echo ' front_page_section_paddings_'.esc_attr(shaha_get_theme_option('front_page_title_paddings'));
			?>"<?php
			$shaha_css = '';
			$shaha_bg_image = shaha_get_theme_option('front_page_title_bg_image');
			if (!empty($shaha_bg_image)) 
				$shaha_css .= 'background-image: url('.esc_url(shaha_get_attachment_url($shaha_bg_image)).');';
			if (!empty($shaha_css))
				echo ' style="' . esc_attr($shaha_css) . '"';
	?>><?php
		// Add anchor
		$shaha_anchor_icon = shaha_get_theme_option('front_page_title_anchor_icon');	
		$shaha_anchor_text = shaha_get_theme_option('front_page_title_anchor_text');	
		if ((!empty($shaha_anchor_icon) || !empty($shaha_anchor_text)) && shortcode_exists('trx_sc_anchor')) {
			echo do_shortcode('[trx_sc_anchor id="front_page_section_title"'
											. (!empty($shaha_anchor_icon) ? ' icon="'.esc_attr($shaha_anchor_icon).'"' : '')
											. (!empty($shaha_anchor_text) ? ' title="'.esc_attr($shaha_anchor_text).'"' : '')
											. ']');
		}
		?>
		<div class="front_page_section_inner front_page_section_title_inner<?php
			if (shaha_get_theme_option('front_page_title_fullheight'))
				echo ' shaha-full-height sc_layouts_flex sc_layouts_columns_middle';
			?>"<?php
				$shaha_css = '';
				$shaha_bg_mask = shaha_get_theme_option('front_page_title_bg_mask');
				$shaha_bg_color = shaha_get_theme_option('front_page_title_bg_color');
				if (!empty($shaha_bg_color) && $shaha_bg_mask > 0)
					$shaha_css .= 'background-color: '.esc_attr($shaha_bg_mask==1
																		? $shaha_bg_color
																		: shaha_hex2rgba($shaha_bg_color, $shaha_bg_mask)
																	).';';
				if (!empty($shaha_css))
					echo ' style="' . esc_attr($shaha_css) . '"';
		?>>
			<div class="front_page_section_content_wrap front_page_section_title_content_wrap content_wrap">
				<?php
				// Caption
				$shaha_caption = shaha_get_theme_option('front_page_title_caption');
				if (!empty($shaha_caption) || (current_user_can('edit_theme_options') && is_customize_preview())) {
					?><h1 class="front_page_section_caption front_page_section_title_caption front_page_block_<?php echo !empty($shaha_caption) ? 'filled' : 'empty'; ?>"><?php echo wp_kses($shaha_caption, 'shaha_kses_content' ); ?></h1><?php
				}
			
				// Description (text)
				$shaha_description = shaha_get_theme_option('front_page_title_description');
				if (!empty($shaha_description) || (current_user_can('edit_theme_options') && is_customize_preview())) {
					?><div class="front_page_section_description front_page_section_title_description front_page_block_<?php echo !empty($shaha_description) ? 'filled' : 'empty'; ?>"><?php echo wp_kses(wpautop($shaha_description), 'shaha_kses_content' ); ?></div><?php
				}
				
				// Buttons
				if (shaha_get_theme_option('front_page_title_button1_link')!='' || shaha_get_theme_option('front_page_title_button2_link')!='') {
					?><div class="front_page_section_buttons front_page_section_title_buttons"><?php
						shaha_show_layout(shaha_customizer_partial_refresh_front_page_title_button1_link());
						shaha_show_layout(shaha_customizer_partial_refresh_front_page_title_button2_link());
					?></div><?php
				}
				?>
			</div>
		</div>
	</div>
	<?php
}