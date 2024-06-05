<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('shaha_revslider_theme_setup9')) {
	add_action( 'after_setup_theme', 'shaha_revslider_theme_setup9', 9 );
	function shaha_revslider_theme_setup9() {
		if (shaha_exists_revslider()) {
			add_action( 'wp_enqueue_scripts', 					'shaha_revslider_frontend_scripts', 1100 );
			add_filter( 'shaha_filter_merge_styles',			'shaha_revslider_merge_styles' );
		}
		if (is_admin()) {
			add_filter( 'shaha_filter_tgmpa_required_plugins','shaha_revslider_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'shaha_revslider_tgmpa_required_plugins' ) ) {
	
	function shaha_revslider_tgmpa_required_plugins($list=array()) {
		if (shaha_storage_isset('required_plugins', 'revslider')) {
			$path = shaha_get_file_dir('plugins/revslider/revslider.zip');
			if (!empty($path) || shaha_get_theme_setting('tgmpa_upload')) {
				$list[] = array(
					'name' 		=> shaha_storage_get_array('required_plugins', 'revslider'),
					'slug' 		=> 'revslider',
					'version'	=> '6.6.12',
					'source'	=> !empty($path) ? $path : 'upload://revslider.zip',
					'required' 	=> false
				);
			}
		}
		return $list;
	}
}

// Check if RevSlider installed and activated
if ( !function_exists( 'shaha_exists_revslider' ) ) {
	function shaha_exists_revslider() {
		return function_exists('rev_slider_shortcode');
	}
}
	
// Enqueue custom styles
if ( !function_exists( 'shaha_revslider_frontend_scripts' ) ) {
	
	function shaha_revslider_frontend_scripts() {
		if (shaha_is_on(shaha_get_theme_option('debug_mode')) && shaha_get_file_dir('plugins/revslider/revslider.css')!='')
			wp_enqueue_style( 'shaha-revslider',  shaha_get_file_url('plugins/revslider/revslider.css'), array(), null );
	}
}
	
// Merge custom styles
if ( !function_exists( 'shaha_revslider_merge_styles' ) ) {
	
	function shaha_revslider_merge_styles($list) {
		$list[] = 'plugins/revslider/revslider.css';
		return $list;
	}
}
?>