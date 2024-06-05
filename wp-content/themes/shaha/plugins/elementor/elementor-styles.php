<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( !function_exists( 'shaha_elm_get_css' ) ) {
	add_filter( 'shaha_filter_get_css', 'shaha_elm_get_css', 10, 4 );
	function shaha_elm_get_css($css, $colors, $fonts, $scheme='') {
		if (isset($css['fonts']) && $fonts) {
			$css['fonts'] .= <<<CSS
			
			.elementor-widget-counter  .elementor-counter-number-wrapper {
				{$fonts['h1_font-family']}
			}
			.elementor-widget-counter  .elementor-counter-title {
			    {$fonts['h6_font-family']}  
			}

CSS;
		}

		if (isset($css['colors']) && $colors) {
			$css['colors'] .= <<<CSS

/* Shape above and below rows */
.elementor-shape .elementor-shape-fill {
	fill: {$colors['bg_color']};
}

/* Divider */
.elementor-divider-separator,
.scheme_self.elementor-divider-separator {
	border-color: {$colors['bd_color']};
}
.elementor-widget-divider,
.scheme_self.elementor-widget-divider {
    --divider-color: {$colors['bd_color']};
    --divider-border-color: {$colors['bd_color']};
}
/* Title */
.elementor-heading-title,
.scheme_self.elementor-heading-title {
    color: {$colors['text_dark']};
}
.elementor-heading-title span,
.scheme_self.elementor-heading-title span {
    color: {$colors['text_dark']};
}
.hover_style_1 .elementor-heading-title a:hover {
    color: {$colors['text_hover']} !important;     
}
.hover_style_2 .elementor-heading-title a:hover {
    color: {$colors['text_hover2']} !important;     
}
/* Counter */
.elementor-widget-counter .elementor-counter-number-wrapper,
.scheme_self.elementor-widget-counter .elementor-counter-number-wrapper {
    color: {$colors['text_dark']};
}
.elementor-widget-counter .elementor-counter-title,
.scheme_self.elementor-widget-counter .elementor-counter-title {
    color: {$colors['text_dark']};
}


CSS;
		}
		
		return $css;
	}
}