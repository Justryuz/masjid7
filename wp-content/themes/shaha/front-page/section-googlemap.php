<div class="front_page_section front_page_section_googlemap<?php
			$shaha_scheme = shaha_get_theme_option('front_page_googlemap_scheme');
			if (!shaha_is_inherit($shaha_scheme)) echo ' scheme_'.esc_attr($shaha_scheme);
			echo ' front_page_section_paddings_'.esc_attr(shaha_get_theme_option('front_page_googlemap_paddings'));
		?>"<?php
		$shaha_css = '';
		$shaha_bg_image = shaha_get_theme_option('front_page_googlemap_bg_image');
		if (!empty($shaha_bg_image)) 
			$shaha_css .= 'background-image: url('.esc_url(shaha_get_attachment_url($shaha_bg_image)).');';
		if (!empty($shaha_css))
			echo ' style="' . esc_attr($shaha_css) . '"';
?>><?php
	// Add anchor
	$shaha_anchor_icon = shaha_get_theme_option('front_page_googlemap_anchor_icon');	
	$shaha_anchor_text = shaha_get_theme_option('front_page_googlemap_anchor_text');	
	if ((!empty($shaha_anchor_icon) || !empty($shaha_anchor_text)) && shortcode_exists('trx_sc_anchor')) {
		echo do_shortcode('[trx_sc_anchor id="front_page_section_googlemap"'
										. (!empty($shaha_anchor_icon) ? ' icon="'.esc_attr($shaha_anchor_icon).'"' : '')
										. (!empty($shaha_anchor_text) ? ' title="'.esc_attr($shaha_anchor_text).'"' : '')
										. ']');
	}
	?>
	<div class="front_page_section_inner front_page_section_googlemap_inner<?php
			if (shaha_get_theme_option('front_page_googlemap_fullheight'))
				echo ' shaha-full-height sc_layouts_flex sc_layouts_columns_middle';
			?>"<?php
			$shaha_css = '';
			$shaha_bg_mask = shaha_get_theme_option('front_page_googlemap_bg_mask');
			$shaha_bg_color = shaha_get_theme_option('front_page_googlemap_bg_color');
			if (!empty($shaha_bg_color) && $shaha_bg_mask > 0)
				$shaha_css .= 'background-color: '.esc_attr($shaha_bg_mask==1
																	? $shaha_bg_color
																	: shaha_hex2rgba($shaha_bg_color, $shaha_bg_mask)
																).';';
			if (!empty($shaha_css))
				echo ' style="' . esc_attr($shaha_css) . '"';
	?>>
		<div class="front_page_section_content_wrap front_page_section_googlemap_content_wrap<?php
			$shaha_layout = shaha_get_theme_option('front_page_googlemap_layout');
			if ($shaha_layout != 'fullwidth')
				echo ' content_wrap';
		?>">
			<?php
			// Content wrap with title and description
			$shaha_caption = shaha_get_theme_option('front_page_googlemap_caption');
			$shaha_description = shaha_get_theme_option('front_page_googlemap_description');
			if (!empty($shaha_caption) || !empty($shaha_description) || (current_user_can('edit_theme_options') && is_customize_preview())) {
				if ($shaha_layout == 'fullwidth') {
					?><div class="content_wrap"><?php
				}
					// Caption
					if (!empty($shaha_caption) || (current_user_can('edit_theme_options') && is_customize_preview())) {
						?><h2 class="front_page_section_caption front_page_section_googlemap_caption front_page_block_<?php echo !empty($shaha_caption) ? 'filled' : 'empty'; ?>"><?php
							echo wp_kses($shaha_caption, 'shaha_kses_content' );
						?></h2><?php
					}
				
					// Description (text)
					if (!empty($shaha_description) || (current_user_can('edit_theme_options') && is_customize_preview())) {
						?><div class="front_page_section_description front_page_section_googlemap_description front_page_block_<?php echo !empty($shaha_description) ? 'filled' : 'empty'; ?>"><?php
							echo wp_kses(wpautop($shaha_description), 'shaha_kses_content' );
						?></div><?php
					}
				if ($shaha_layout == 'fullwidth') {
					?></div><?php
				}
			}

			// Content (text)
			$shaha_content = shaha_get_theme_option('front_page_googlemap_content');
			if (!empty($shaha_content) || (current_user_can('edit_theme_options') && is_customize_preview())) {
				if ($shaha_layout == 'columns') {
					?><div class="front_page_section_columns front_page_section_googlemap_columns columns_wrap">
						<div class="column-1_3">
					<?php
				} else if ($shaha_layout == 'fullwidth') {
					?><div class="content_wrap"><?php
				}
	
				?><div class="front_page_section_content front_page_section_googlemap_content front_page_block_<?php echo !empty($shaha_content) ? 'filled' : 'empty'; ?>"><?php
					echo wp_kses($shaha_content, 'shaha_kses_content' );
				?></div><?php
	
				if ($shaha_layout == 'columns') {
					?></div><div class="column-2_3"><?php
				} else if ($shaha_layout == 'fullwidth') {
					?></div><?php
				}
			}
			
			// Widgets output
			?><div class="front_page_section_output front_page_section_googlemap_output"><?php 
				if (is_active_sidebar('front_page_googlemap_widgets')) {
					dynamic_sidebar( 'front_page_googlemap_widgets' );
				} else if (current_user_can( 'edit_theme_options' )) {
					if (!shaha_exists_trx_addons())
						shaha_customizer_need_trx_addons_message();
					else
						shaha_customizer_need_widgets_message('front_page_googlemap_caption', 'ThemeREX Addons - Google map');
				}
			?></div><?php

			if ($shaha_layout == 'columns' && (!empty($shaha_content) || (current_user_can('edit_theme_options') && is_customize_preview()))) {
				?></div></div><?php
			}
			?>			
		</div>
	</div>
</div>