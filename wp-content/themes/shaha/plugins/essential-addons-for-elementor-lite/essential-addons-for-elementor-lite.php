<?php
/* EssentialAddonsForElementorLite support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'shaha_essential_addons_for_elementor_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'shaha_essential_addons_for_elementor_theme_setup9', 9 );
	function shaha_essential_addons_for_elementor_theme_setup9() {
		if ( shaha_exists_essential_addons_for_elementor() ) {
			add_action( 'wp_enqueue_scripts', 'shaha_essential_addons_for_elementor_frontend_scripts', 1100 );
			add_filter( 'shaha_filter_merge_styles', 'shaha_essential_addons_for_elementor_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'shaha_filter_tgmpa_required_plugins', 'shaha_essential_addons_for_elementor_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'shaha_essential_addons_for_elementor_tgmpa_required_plugins' ) ) {

	function shaha_essential_addons_for_elementor_tgmpa_required_plugins( $list = array() ) {
		if ( shaha_storage_isset( 'required_plugins', 'essential-addons-for-elementor-lite' )) {
			$list[] = array(
				'name'     => shaha_storage_get_array( 'required_plugins', 'essential-addons-for-elementor-lite' ),
				'slug'     => 'essential-addons-for-elementor-lite',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'shaha_exists_essential_addons_for_elementor' ) ) {
	function shaha_exists_essential_addons_for_elementor() {
        return class_exists( 'Elementor\Plugin' ) || defined( 'EAEL_PLUGIN_VERSION' );
	}
}

// Custom styles and scripts
//------------------------------------------------------------------------

// Enqueue styles for frontend
if ( ! function_exists( 'shaha_essential_addons_for_elementor_frontend_scripts' ) ) {

	function shaha_essential_addons_for_elementor_frontend_scripts() {
		if ( shaha_is_on( shaha_get_theme_option( 'debug_mode' ) ) ) {
			$shaha_url = shaha_get_file_url( 'plugins/essential-addons-for-elementor-lite/essential-addons-for-elementor-lite.css' );
			if ( '' != $shaha_url ) {
				wp_enqueue_style( 'shaha-essential-addons-for-elementor-lite', $shaha_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'shaha_essential_addons_for_elementor_merge_styles' ) ) {

	function shaha_essential_addons_for_elementor_merge_styles( $list ) {
		$list[] = 'plugins/essential-addons-for-elementor-lite/essential-addons-for-elementor-lite.css';
		return $list;
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( shaha_exists_essential_addons_for_elementor() ) {
	require_once SHAHA_THEME_DIR . 'plugins/essential-addons-for-elementor-lite/essential-addons-for-elementor-lite-styles.php';
}

