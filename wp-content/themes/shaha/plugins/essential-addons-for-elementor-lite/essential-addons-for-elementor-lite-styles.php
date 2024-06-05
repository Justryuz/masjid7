<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'shaha_essential_addons_for_elementor_get_css' ) ) {
	add_filter( 'shaha_filter_get_css', 'shaha_essential_addons_for_elementor_get_css', 10, 4 );
	function shaha_essential_addons_for_elementor_get_css( $css, $colors, $fonts, $scheme='' ) {
        if (isset($css['fonts']) && $fonts) {
            $css['fonts'] .= <<<CSS
            
            .elementor-widget-eael-countdown .eael-countdown-digits {
            	{$fonts['h1_font-family']}
            }
			
CSS;
		}

        if (isset($css['colors']) && $colors) {
            $css['colors'] .= <<<CSS

        /* Progress Bar */
        .eael-progressbar-circle .eael-progressbar-circle-inner {
            border-color: {$colors['alter_bg_color']};
        }
        .eael-progressbar-circle .eael-progressbar-circle-half {
            border-color: {$colors['alter_link']};
        }
        .eael-progressbar-circle .eael-progressbar-count-wrap {
             color: {$colors['text']};
        }
        
        /* Countdown */
        .elementor-widget-eael-countdown .eael-countdown-digits {
            color: {$colors['text_dark']};
        }
        .elementor-widget-eael-countdown .eael-countdown-label {
            color: {$colors['text_dark']};
        }

CSS;
		}

		return $css;
	}
}

