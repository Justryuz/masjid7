<?php
/* Booked Appointments support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('shaha_booked_theme_setup9')) {
	add_action( 'after_setup_theme', 'shaha_booked_theme_setup9', 9 );
	function shaha_booked_theme_setup9() {
		if (shaha_exists_booked()) {
			add_action( 'wp_enqueue_scripts', 							'shaha_booked_frontend_scripts', 1100 );
			add_filter( 'shaha_filter_merge_styles',					'shaha_booked_merge_styles' );
		}
		if (is_admin()) {
			add_filter( 'shaha_filter_tgmpa_required_plugins',		'shaha_booked_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'shaha_booked_tgmpa_required_plugins' ) ) {

	function shaha_booked_tgmpa_required_plugins($list=array()) {
		if (shaha_storage_isset('required_plugins', 'booked')) {
			$path = shaha_get_file_dir('plugins/booked/booked.zip');
			if (!empty($path) || shaha_get_theme_setting('tgmpa_upload')) {
				$list[] = array(
					'name' 		=> shaha_storage_get_array('required_plugins', 'booked'),
					'slug' 		=> 'booked',
					'version'	=> '2.4.3',
					'source' 	=> !empty($path) ? $path : 'upload://booked.zip',
					'required' 	=> false
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( !function_exists( 'shaha_exists_booked' ) ) {
	function shaha_exists_booked() {
		return class_exists('booked_plugin');
	}
}
	
// Enqueue plugin's custom styles
if ( !function_exists( 'shaha_booked_frontend_scripts' ) ) {

	function shaha_booked_frontend_scripts() {
		if (shaha_is_on(shaha_get_theme_option('debug_mode')) && shaha_get_file_dir('plugins/booked/booked.css')!='')
			wp_enqueue_style( 'shaha-booked',  shaha_get_file_url('plugins/booked/booked.css'), array(), null );
	}
}
	
// Merge custom styles
if ( !function_exists( 'shaha_booked_merge_styles' ) ) {

	function shaha_booked_merge_styles($list) {
		$list[] = 'plugins/booked/booked.css';
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if (shaha_exists_booked()) { require_once SHAHA_THEME_DIR . 'plugins/booked/booked.styles.php'; }
?>