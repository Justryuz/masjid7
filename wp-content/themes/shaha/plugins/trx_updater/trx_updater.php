<?php
/* TRX Updater support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('shaha_trx_updater_theme_setup9')) {
	add_action( 'after_setup_theme', 'shaha_trx_updater_theme_setup9', 9 );
	function shaha_trx_updater_theme_setup9() {

		if (is_admin()) {
			add_filter( 'shaha_filter_tgmpa_required_plugins',			'shaha_trx_updater_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'shaha_trx_updater_tgmpa_required_plugins' ) ) {
	
	function shaha_trx_updater_tgmpa_required_plugins($list=array()) {
		if (shaha_storage_isset('required_plugins', 'trx_updater')) {
			$path = shaha_get_file_dir('plugins/trx_updater/trx_updater.zip');
			if (!empty($path) || shaha_get_theme_setting('tgmpa_upload')) {
				$list[] = array(
					'name' => esc_html__('ThemeREX Updater', 'shaha'),
					'slug' => 'trx_updater',
					'version' => '2.0.0',
					'source' => !empty($path) ? $path : 'upload://trx_updater.zip',
					'required' => false
				);
			}
		}
		return $list;
	}
}

// Check if this plugin installed and activated
if ( !function_exists( 'shaha_exists_trx_updater' ) ) {
	function shaha_exists_trx_updater() {
		return function_exists( 'trx_updater_load_plugin_textdomain' );
	}
}
