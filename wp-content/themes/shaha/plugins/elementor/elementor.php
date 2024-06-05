<?php
/* Elementor Builder support functions
------------------------------------------------------------------------------- */
// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('shaha_elm_theme_setup9')) {
	add_action( 'after_setup_theme', 'shaha_elm_theme_setup9', 9 );
	function shaha_elm_theme_setup9() {

		if (shaha_exists_elementor()) {
			add_action( 'wp_enqueue_scripts', 'shaha_elementor_frontend_scripts', 1100 );
            add_filter( 'shaha_filter_merge_styles',					'shaha_elm_merge_styles' );

			add_action( 'init',										'shaha_elm_init_once', 3 );
            add_action( 'elementor/editor/before_enqueue_scripts', 'shaha_elm_editor_scripts' );

            add_action( 'elementor/element/before_section_end', 'shaha_elm_add_theme_style_button', 10, 3 );
		}
		if (is_admin()) {
			add_filter( 'shaha_filter_tgmpa_required_plugins',	'shaha_elm_tgmpa_required_plugins' );
		}
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'shaha_elm_tgmpa_required_plugins' ) ) {

	function shaha_elm_tgmpa_required_plugins($list=array()) {
        if (shaha_storage_isset('required_plugins', 'elementor')) {
			$list[] = array(
                'name' 		=> esc_html__('Elementor', 'shaha'),
                'slug' 		=> 'elementor',
				'required' 	=> false
			);
		}
		return $list;
	}
}

// Check if Elementor is installed and activated
if ( !function_exists( 'shaha_exists_elementor' ) ) {
	function shaha_exists_elementor() {
        return class_exists('Elementor\Plugin');
	}
}

// Merge custom styles
if ( !function_exists( 'shaha_elm_merge_styles' ) ) {
	function shaha_elm_merge_styles($list) {
		if (shaha_exists_elementor()) {
            $list[] = 'plugins/elementor/elementor.css';
		}
		return $list;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'shaha_elementor_frontend_scripts' ) ) {

    function shaha_elementor_frontend_scripts() {
        if ( shaha_is_on( shaha_get_theme_option( 'debug_mode' ) ) ) {
            $shaha_url = shaha_get_file_url( 'plugins/elementor/elementor.css' );
            if ( '' != $shaha_url ) {
                wp_enqueue_style( 'shaha-elementor', $shaha_url, array(), null );
            }
        }
    }
}

// Load required styles and scripts for Elementor Editor mode
if ( ! function_exists( 'shaha_elm_editor_scripts' ) ) {

    function shaha_elm_editor_scripts() {
        // Load font icons
        wp_enqueue_style( 'fontello-icons', shaha_get_file_url( 'css/font-icons/css/fontello.css' ), array(), null );
    }
}

// Set Elementor's options at once
if ( ! function_exists( 'shaha_elm_init_once' ) ) {

    function shaha_elm_init_once() {
        if ( shaha_exists_elementor() && ! get_option( 'shaha_setup_elementor_options', false ) ) {
            // Set theme-specific values to the Elementor's options
            update_option( 'elementor_disable_color_schemes', 'yes' );
            update_option( 'elementor_disable_typography_schemes', 'yes' );
            update_option( 'elementor_container_width', 1200 );
            update_option( 'elementor_space_between_widgets', 0 );
            update_option( 'elementor_stretched_section_container', '.page_wrap' );
            update_option( 'elementor_page_title_selector', '.sc_layouts_title_caption' );
            // Set flag to prevent change Elementor's options again
            update_option( 'shaha_setup_elementor_options', 1 );
        }
    }
}

// Add theme-specific controls to sections and columns
if ( ! function_exists( 'shaha_elm_add_theme_style_button' ) ) {

    function shaha_elm_add_theme_style_button( $element, $section_id, $args ) {
        if ( is_object( $element ) ) {
            $el_name = $element->get_name();
            // Add theme specific button styles
            if ( 'button' == $el_name && 'section_button' === $section_id ) {
                $element->add_control(
                    'theme_style_button', array(
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'label'        => esc_html__( 'Theme Style Button', 'shaha' ),
                        'options' => array(
                            'none' => esc_html__( 'None', 'shaha' ),
                            'style_1' => esc_html__( 'Style 1', 'shaha' ),
                            'style_2' => esc_html__( 'Style 2', 'shaha' ),
                            'style_3' => esc_html__( 'Style 3', 'shaha' ),
                            'style_4' => esc_html__( 'Style 4', 'shaha' ),
                            'style_5' => esc_html__( 'Style 5', 'shaha' ),
                            'style_6' => esc_html__( 'Style 6', 'shaha' ),
                        ),
                        'default' => 'none',
                        'prefix_class' => 'sc_theme_button_',
                    )
                );
            }

            // Add color scheme selector
            if ( apply_filters(
                'shaha_filter_add_scheme_in_elements',
                ( in_array( $el_name, array( 'section', 'column' ) ) && 'section_advanced' === $section_id )
                || ( 'common' === $el_name && '_section_style' === $section_id ),
                $element, $section_id, $args
            ) ) {
                $element->add_control(
                    'scheme_heading',
                    [
                        'label' => esc_html__( 'Theme-specific params', 'shaha' ),
                        'type' => \Elementor\Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
                );
                $element->add_control(
                    'scheme', array(
                        'type'         => \Elementor\Controls_Manager::SELECT,
                        'label'        => esc_html__( 'Color scheme', 'shaha' ),
                        'label_block'  => true,
                        'options'      => shaha_array_merge( array( '' => esc_html__( 'Inherit', 'shaha' ) ), shaha_get_list_schemes() ),
                        'default'      => '',
                        'prefix_class' => 'scheme_',
                    )
                );
            }

            // Set default gap between columns to 'Extended'
            if ( 'section' == $el_name && 'section_layout' === $section_id ) {
                $element->update_control(
                    'gap', array(
                        'default' => 'extended',
                    )
                );
            }
        }
    }
}


// Add plugin-specific colors and fonts to the custom CSS
if (shaha_exists_elementor()) {
    require_once SHAHA_THEME_DIR . 'plugins/elementor/elementor-styles.php'; }