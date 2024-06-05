<?php
/**
 * Plugin support: Elementor
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Return true if Elementor exists and current mode is preview
if ( !function_exists( 'trx_addons_elm_is_preview' ) ) {
    function trx_addons_elm_is_preview() {
        return trx_addons_exists_elementor()
            && (\Elementor\Plugin::$instance->preview->is_preview_mode()
                || (trx_addons_get_value_gp('post') > 0
                    && trx_addons_get_value_gp('action') == 'elementor'
                )
            );
    }
}

// Load required styles and scripts for the frontend
/*
if ( !function_exists( 'trx_addons_elm_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_elm_load_scripts_front');
	function trx_addons_elm_load_scripts_front() {
		if (trx_addons_exists_elementor()) {
			if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
				wp_enqueue_script( 'trx_addons-elementor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'elementor/elementor.js'), array('jquery'), null, true );
			}
		}
	}
}
*/

// Merge specific styles into single stylesheet
if ( !function_exists( 'trx_addons_elm_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_elm_merge_styles');
	function trx_addons_elm_merge_styles($list) {
		if (trx_addons_exists_elementor()) {
			$list[] = TRX_ADDONS_PLUGIN_API . 'elementor/elementor.css';
		}
		return $list;
	}
}

// Merge plugin's specific scripts into single file
if ( !function_exists( 'trx_addons_elm_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_elm_merge_scripts');
	function trx_addons_elm_merge_scripts($list) {
		if (trx_addons_exists_elementor()) {
			$list[] = TRX_ADDONS_PLUGIN_API . 'elementor/elementor.js';
		}
		return $list;
	}
}

// Load required styles and scripts for Elementor Editor mode
if ( !function_exists( 'trx_addons_elm_editor_load_scripts' ) ) {
	add_action("elementor/editor/before_enqueue_scripts", 'trx_addons_elm_editor_load_scripts');
	function trx_addons_elm_editor_load_scripts() {
		trx_addons_load_scripts_admin(true);
		trx_addons_localize_scripts_admin();
        do_action('trx_addons_action_pagebuilder_admin_scripts');
	}
}

// Load required scripts for Elementor Preview mode
if ( !function_exists( 'trx_addons_elm_preview_load_scripts' ) ) {
	add_action("elementor/frontend/after_enqueue_scripts", 'trx_addons_elm_preview_load_scripts');
	function trx_addons_elm_preview_load_scripts() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_script( 'trx_addons-elementor-preview', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'elementor/elementor.js'), array('jquery'), null, true );
		}
		if ( trx_addons_elm_is_preview() ) {
			do_action('trx_addons_action_pagebuilder_preview_scripts', 'elementor');
		}
	}
}

// Add shortcode's specific vars into JS storage
if ( !function_exists( 'trx_addons_elm_localize_script' ) ) {
	add_filter("trx_addons_filter_localize_script", 'trx_addons_elm_localize_script');
	function trx_addons_elm_localize_script($vars) {
		$vars['elementor_stretched_section_container'] = get_option('elementor_stretched_section_container');
		return $vars;
	}
}


// Init Elementor's support
//--------------------------------------------------------

// Set Elementor's options at once
if (!function_exists('trx_addons_elm_init_once')) {
	add_action( 'init', 'trx_addons_elm_init_once', 2 );
	function trx_addons_elm_init_once() {
		if (trx_addons_exists_elementor() && !get_option('trx_addons_setup_elementor_options', false)) {
			// Set components specific values to the Elementor's options
			do_action('trx_addons_action_set_elementor_options');
			// Set flag to prevent change Elementor's options again
			update_option('trx_addons_setup_elementor_options', 1);
		}
	}
}

// Replace widget's args with theme-specific args
if ( !function_exists( 'trx_addons_elm_wordpress_widget_args' ) ) {
    add_filter( 'elementor/widgets/wordpress/widget_args', 'trx_addons_elm_wordpress_widget_args', 10, 2 );
    function trx_addons_elm_wordpress_widget_args($widget_args, $widget) {
        return trx_addons_prepare_widgets_args($widget->get_name(), $widget->get_name(), $widget_args);
    }
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_elm_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_elm_importer_required_plugins', 10, 2 );
	function trx_addons_elm_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'elementor')!==false && !trx_addons_exists_elementor())
			$not_installed .= '<br>' . esc_html__('Elementor (free PageBuilder)', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_elm_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_elm_importer_set_options' );
	function trx_addons_elm_importer_set_options($options=array()) {
		if ( trx_addons_exists_elementor() && in_array('elementor', $options['required_plugins']) ) {
			$options['additional_options'][] = 'elementor%';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}

// Prepare group atts for the new Elementor version: make associative array from list by key 'name'
// After the update Elementor 3.1.0+ (or near) internal structure of field type ::REPEATER was changed
// (fields list was converted to the associative array)
// and as result js-errors appears in the Elementor Editor:
// "Cannot read property 'global' of undefined"
// "TypeError: undefined is not an object (evaluating 't[o].global')"
if ( !function_exists( 'trx_addons_elm_prepare_group_params' ) ) {
	add_filter( 'trx_addons_sc_param_group_params', 'trx_addons_elm_prepare_group_params', 999 );
	function trx_addons_elm_prepare_group_params( $args ) {
		if ( is_array( $args ) && ! empty( $args[0]['name'] ) ) {
			$new = array();
			foreach( $args as $item ) {
				if ( ! empty( $item['name'] ) ) {
					$new[ $item['name'] ] = $item;
				}
			}
			$args = $new;
		}
		return $args;
	}
}


// Fix for Elementor 3.3.0+ - move options 'blogname' and 'blogdescription'
// to the end of the list (after all 'elementor_%' options)
if ( !function_exists( 'trx_addons_elm_importer_theme_options_data' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options_data', 'trx_addons_elm_importer_theme_options_data', 10, 1 );
	function trx_addons_elm_importer_theme_options_data( $data ) {
		if ( isset( $data['blogname'] ) ) {
			$val = $data['blogname'];
			unset( $data['blogname'] );
			$data['blogname'] = $val;
		}
		if ( isset( $data['blogdescription'] ) ) {
			$val = $data['blogdescription'];
			unset( $data['blogdescription'] );
			$data['blogdescription'] = $val;
		}
		return $data;
	}
}
