<?php
/* ThemeREX Donations support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if (!function_exists('shaha_trx_donations_theme_setup1')) {
	add_action( 'after_setup_theme', 'shaha_trx_donations_theme_setup1', 1 );
	function shaha_trx_donations_theme_setup1() {
		add_filter( 'shaha_filter_list_posts_types',	'shaha_trx_donations_list_post_types');
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if (!function_exists('shaha_trx_donations_theme_setup3')) {
	add_action( 'after_setup_theme', 'shaha_trx_donations_theme_setup3', 3 );
	function shaha_trx_donations_theme_setup3() {
		if (shaha_exists_trx_donations()) {
		
			// Section 'Donations'
			shaha_storage_merge_array('options', '', array_merge(
				array(
					'donations' => array(
						"title" => esc_html__('Donations', 'shaha'),
						"desc" => wp_kses_data( __('Select parameters to display the donations pages', 'shaha') ),
						"type" => "section"
						)
				),
				shaha_options_get_list_cpt_options('donations')
			));
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('shaha_trx_donations_theme_setup9')) {
	add_action( 'after_setup_theme', 'shaha_trx_donations_theme_setup9', 9 );
	function shaha_trx_donations_theme_setup9() {
		
		if (shaha_exists_trx_donations()) {
			add_action( 'wp_enqueue_scripts', 								'shaha_trx_donations_frontend_scripts', 1100 );
			add_filter( 'shaha_filter_merge_styles',						'shaha_trx_donations_merge_styles' );
			add_filter( 'shaha_filter_get_post_info',		 				'shaha_trx_donations_get_post_info');
			add_filter( 'shaha_filter_post_type_taxonomy',				'shaha_trx_donations_post_type_taxonomy', 10, 2 );
			if (!is_admin()) {
				add_filter( 'shaha_filter_detect_blog_mode',				'shaha_trx_donations_detect_blog_mode' );
				add_filter( 'shaha_filter_get_post_categories', 			'shaha_trx_donations_get_post_categories');
				add_action( 'shaha_action_before_post_meta',				'shaha_trx_donations_action_before_post_meta');
			}
		}
		if (is_admin()) {
			add_filter( 'shaha_filter_tgmpa_required_plugins',			'shaha_trx_donations_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'shaha_trx_donations_tgmpa_required_plugins' ) ) {
	
	function shaha_trx_donations_tgmpa_required_plugins($list=array()) {
		if (shaha_storage_isset('required_plugins', 'trx_donations')) {
			$path = shaha_get_file_dir('plugins/trx_donations/trx_donations.zip');
			if (!empty($path) || shaha_get_theme_setting('tgmpa_upload')) {
				$list[] = array(
					'name' 		=> shaha_storage_get_array('required_plugins', 'trx_donations'),
					'slug' 		=> 'trx_donations',
					'version'	=> '1.7.2',
					'source'	=> !empty($path) ? $path : 'upload://trx_donations.zip',
					'required' 	=> false
				);
			}
		}
		return $list;
	}
}



// Check if trx_donations installed and activated
if ( !function_exists( 'shaha_exists_trx_donations' ) ) {
	function shaha_exists_trx_donations() {
		return class_exists('TRX_DONATIONS');
	}
}

// Return true, if current page is any trx_donations page
if ( !function_exists( 'shaha_is_trx_donations_page' ) ) {
	function shaha_is_trx_donations_page() {
		$rez = false;
		if (shaha_exists_trx_donations()) {
			$rez = (is_single() && get_query_var('post_type') == TRX_DONATIONS::POST_TYPE) 
					|| is_post_type_archive(TRX_DONATIONS::POST_TYPE) 
					|| is_tax(TRX_DONATIONS::TAXONOMY);
		}
		return $rez;
	}
}

// Detect current blog mode
if ( !function_exists( 'shaha_trx_donations_detect_blog_mode' ) ) {
	
	function shaha_trx_donations_detect_blog_mode($mode='') {
		if (shaha_is_trx_donations_page())
			$mode = 'donations';
		return $mode;
	}
}

// Return taxonomy for current post type
if ( !function_exists( 'shaha_trx_donations_post_type_taxonomy' ) ) {
	
	function shaha_trx_donations_post_type_taxonomy($tax='', $post_type='') {
		if (shaha_exists_trx_donations() && $post_type == TRX_DONATIONS::POST_TYPE)
			$tax = TRX_DONATIONS::TAXONOMY;
		return $tax;
	}
}

// Show categories of the current product
if ( !function_exists( 'shaha_trx_donations_get_post_categories' ) ) {
	
	function shaha_trx_donations_get_post_categories($cats='') {
		if ( shaha_exists_trx_donations() && get_post_type()==TRX_DONATIONS::POST_TYPE ) {
			$cats = shaha_get_post_terms(', ', get_the_ID(), TRX_DONATIONS::TAXONOMY);
		}
		return $cats;
	}
}

// Add 'donation' to the list of the supported post-types
if ( !function_exists( 'shaha_trx_donations_list_post_types' ) ) {
	
	function shaha_trx_donations_list_post_types($list=array()) {
		if (shaha_exists_trx_donations())
			$list[TRX_DONATIONS::POST_TYPE] = esc_html__('Donations', 'shaha');
		return $list;
	}
}

// Show price of the current product in the widgets and search results
if ( !function_exists( 'shaha_trx_donations_get_post_info' ) ) {
	
	function shaha_trx_donations_get_post_info($post_info='') {
		if (shaha_exists_trx_donations()) {
			if (get_post_type()==TRX_DONATIONS::POST_TYPE) {
				// Goal and raised
				$goal = get_post_meta( get_the_ID(), 'trx_donations_goal', true );
				if (!empty($goal)) {
					$raised = get_post_meta( get_the_ID(), 'trx_donations_raised', true );
					if (empty($raised)) $raised = 0;
					$manual = get_post_meta( get_the_ID(), 'trx_donations_manual', true );
					$plugin = TRX_DONATIONS::get_instance();
					$post_info .= '<div class="post_info post_meta post_donation_info">'
										. '<span class="post_info_item post_meta_item post_donation_item post_donation_goal">'
											. '<span class="post_info_label post_meta_label post_donation_label">' . esc_html__('Group goal:', 'shaha') . '</span>'
											. ' ' 
											. '<span class="post_info_number post_meta_number post_donation_number">' . trim($plugin->get_money($goal)) . '</span>'
										. '</span>'
										. '<span class="post_info_item post_meta_item post_donation_item post_donation_raised">'
											. '<span class="post_info_label post_meta_label post_donation_label">' . esc_html__('Raised:', 'shaha') . '</span>'
											. ' '
											. '<span class="post_info_number post_meta_number post_donation_number">' . trim($plugin->get_money($raised+$manual)) . ' (' . round(($raised+$manual)*100/$goal, 2) . '%)' . '</span>'
										. '</span>'
									. '</div>';
				}
			}
		}
		return $post_info;
	}
}

// Show price of the current product in the search results streampage
if ( !function_exists( 'shaha_trx_donations_action_before_post_meta' ) ) {
	
	function shaha_trx_donations_action_before_post_meta() {
		if (!is_single() && get_post_type()==TRX_DONATIONS::POST_TYPE) {
			shaha_show_layout(shaha_trx_donations_get_post_info());
		}
	}
}
	
// Enqueue trx_donations custom styles
if ( !function_exists( 'shaha_trx_donations_frontend_scripts' ) ) {
	
	function shaha_trx_donations_frontend_scripts() {
		if (shaha_is_on(shaha_get_theme_option('debug_mode')) && shaha_get_file_dir('plugins/trx_donations/trx_donations.css')!='')
			wp_enqueue_style( 'shaha-trx-donations',  shaha_get_file_url('plugins/trx_donations/trx_donations.css'), array(), null );
	}
}
	
// Merge custom styles
if ( !function_exists( 'shaha_trx_donations_merge_styles' ) ) {
	
	function shaha_trx_donations_merge_styles($list) {
		$list[] = 'plugins/trx_donations/trx_donations.css';
		return $list;
	}
}

// Return text for the "I agree ..." checkbox
if ( ! function_exists( 'shaha_trx_donations_privacy_text' ) ) {
    
    function shaha_trx_donations_privacy_text( $text='' ) {
        return shaha_get_privacy_text();
    }
}


// Add plugin-specific colors and fonts to the custom CSS
if (shaha_exists_trx_donations()) { require_once SHAHA_THEME_DIR . 'plugins/trx_donations/trx_donations.styles.php'; }
?>