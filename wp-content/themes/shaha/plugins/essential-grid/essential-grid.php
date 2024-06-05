<?php
/* Essential Grid support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('shaha_essential_grid_theme_setup9')) {
	add_action( 'after_setup_theme', 'shaha_essential_grid_theme_setup9', 9 );
	function shaha_essential_grid_theme_setup9() {
		if (shaha_exists_essential_grid()) {
			add_action( 'wp_enqueue_scripts', 							'shaha_essential_grid_frontend_scripts', 1100 );
			add_filter( 'shaha_filter_merge_styles',					'shaha_essential_grid_merge_styles' );
		}
		if (is_admin()) {
			add_filter( 'shaha_filter_tgmpa_required_plugins',		'shaha_essential_grid_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'shaha_essential_grid_tgmpa_required_plugins' ) ) {
	
	function shaha_essential_grid_tgmpa_required_plugins($list=array()) {
		if (shaha_storage_isset('required_plugins', 'essential-grid')) {
			$path = shaha_get_file_dir('plugins/essential-grid/essential-grid.zip');
			if (!empty($path) || shaha_get_theme_setting('tgmpa_upload')) {
				$list[] = array(
						'name' 		=> shaha_storage_get_array('required_plugins', 'essential-grid'),
						'slug' 		=> 'essential-grid',
						'version'	=> '3.0.17.1',
						'source'	=> !empty($path) ? $path : 'upload://essential-grid.zip',
						'required' 	=> false
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( !function_exists( 'shaha_exists_essential_grid' ) ) {
	function shaha_exists_essential_grid() {
		return defined( 'ESG_PLUGIN_PATH' ) || defined( 'EG_PLUGIN_PATH' );
	}
}
	
// Enqueue plugin's custom styles
if ( !function_exists( 'shaha_essential_grid_frontend_scripts' ) ) {
	
	function shaha_essential_grid_frontend_scripts() {
		if (shaha_is_on(shaha_get_theme_option('debug_mode')) && shaha_get_file_dir('plugins/essential-grid/essential-grid.css')!='')
			wp_enqueue_style( 'shaha-essential-grid',  shaha_get_file_url('plugins/essential-grid/essential-grid.css'), array(), null );
	}
}
	
// Merge custom styles
if ( !function_exists( 'shaha_essential_grid_merge_styles' ) ) {
	
	function shaha_essential_grid_merge_styles($list) {
		$list[] = 'plugins/essential-grid/essential-grid.css';
		return $list;
	}
}

// Check if Ess. Grid installed and activated
if ( !function_exists( 'shaha_essgrids_get_popular_posts_query' ) ) {
	add_filter( 'essgrid_get_posts', 'shaha_essgrids_get_popular_posts_query', 10, 2 );
	add_filter( 'essgrid_get_posts_by_ids_query', 'shaha_essgrids_get_popular_posts_query', 10, 2 );
	add_filter( 'essgrid_get_popular_posts_query', 'shaha_essgrids_get_popular_posts_query', 10, 2 );
	add_filter( 'essgrid_get_related_posts', 'shaha_essgrids_get_popular_posts_query', 10, 2 );
	add_filter( 'essgrid_get_related_posts_query', 'shaha_essgrids_get_popular_posts_query', 10, 2 );
	function shaha_essgrids_get_popular_posts_query($args, $post_id) {
	  if (shaha_exists_tribe_events()) {
		$args['tribe_suppress_query_filters'] = true;
	  }
	  return $args;
	}
  }

?>