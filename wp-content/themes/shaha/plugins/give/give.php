<?php
/* Give (donation forms) support functions
------------------------------------------------------------------------------- */

if ( ! defined( 'SHAHA_GIVE_FORMS_PT_FORMS' ) )			define( 'SHAHA_GIVE_FORMS_PT_FORMS', 'give_forms' );
if ( ! defined( 'SHAHA_GIVE_FORMS_PT_PAYMENT' ) )			define( 'SHAHA_GIVE_FORMS_PT_PAYMENT', 'give_payment' );
if ( ! defined( 'SHAHA_GIVE_FORMS_TAXONOMY_CATEGORY' ) )	define( 'SHAHA_GIVE_FORMS_TAXONOMY_CATEGORY', 'give_forms_category' );
if ( ! defined( 'SHAHA_GIVE_FORMS_TAXONOMY_TAG' ) )		define( 'SHAHA_GIVE_FORMS_TAXONOMY_TAG', 'give_forms_tag' );


// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'shaha_give_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'shaha_give_theme_setup3', 3 );
	function shaha_give_theme_setup3() {
		if ( shaha_exists_give() ) {
			// Section 'Give'
			shaha_storage_merge_array(
				'options', '', array_merge(
					array(
						'give' => array(
							'title' => esc_html__( 'Give Donations', 'shaha' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the Give Donations pages', 'shaha' ) ),
							'type'  => 'section',
						),
					),
					shaha_options_get_list_cpt_options( 'give', esc_html__( 'Give Donations', 'shaha' ) )
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'shaha_give_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'shaha_give_theme_setup9', 9 );
	function shaha_give_theme_setup9() {
		if ( shaha_exists_give() ) {
			add_action( 'wp_enqueue_scripts', 'shaha_give_frontend_scripts', 1100 );
			add_filter( 'shaha_filter_merge_styles', 'shaha_give_merge_styles' );
			add_filter( 'shaha_filter_get_post_categories', 'shaha_give_get_post_categories');
			add_filter( 'shaha_filter_post_type_taxonomy', 'shaha_give_post_type_taxonomy', 10, 2 );
			add_filter( 'shaha_filter_detect_blog_mode', 'shaha_give_detect_blog_mode' );
		}
		if ( is_admin() ) {
			add_filter( 'shaha_filter_tgmpa_required_plugins', 'shaha_give_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'shaha_give_tgmpa_required_plugins' ) ) {
	
	function shaha_give_tgmpa_required_plugins( $list = array() ) {
		if ( shaha_storage_isset( 'required_plugins', 'give' ) ) {
			$list[] = array(
				'name'     => shaha_storage_get_array( 'required_plugins', 'give'),
				'slug'     => 'give',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'shaha_exists_give' ) ) {
	function shaha_exists_give() {
		return class_exists( 'Give' );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'shaha_give_frontend_scripts' ) ) {
	
	function shaha_give_frontend_scripts() {
		if ( shaha_is_on( shaha_get_theme_option( 'debug_mode' ) ) ) {
			$shaha_url = shaha_get_file_url( 'plugins/give/give.css' );
			if ( '' != $shaha_url ) {
				wp_enqueue_style( 'shaha-give', $shaha_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'shaha_give_merge_styles' ) ) {
	
	function shaha_give_merge_styles( $list ) {
		$list[] = 'plugins/give/give.css';
		return $list;
	}
}

// Return true, if current page is any give page
if ( ! function_exists( 'shaha_is_give_page' ) ) {
	function shaha_is_give_page() {
		$rez = shaha_exists_give()
					&& ! is_search()
					&& (
						is_singular( SHAHA_GIVE_FORMS_PT_FORMS )
						|| is_post_type_archive( SHAHA_GIVE_FORMS_PT_FORMS )
						|| is_tax( SHAHA_GIVE_FORMS_TAXONOMY_CATEGORY )
						|| is_tax( SHAHA_GIVE_FORMS_TAXONOMY_TAG )
						|| ( function_exists( 'is_give_form' ) && is_give_form() )
						|| ( function_exists( 'is_give_category' ) && is_give_category() )
						|| ( function_exists( 'is_give_tag' ) && is_give_tag() )
						);
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'shaha_give_detect_blog_mode' ) ) {
	
	function shaha_give_detect_blog_mode( $mode = '' ) {
		if ( shaha_is_give_page() ) {
			$mode = 'give';
		}
		return $mode;
	}
}

// Return taxonomy for current post type
if ( ! function_exists( 'shaha_give_post_type_taxonomy' ) ) {
	
	function shaha_give_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( shaha_exists_give() && SHAHA_GIVE_FORMS_PT_FORMS == $post_type ) {
			$tax = SHAHA_GIVE_FORMS_TAXONOMY_CATEGORY;
		}
		return $tax;
	}
}

// Show categories of the current product
if ( ! function_exists( 'shaha_give_get_post_categories' ) ) {
	
	function shaha_give_get_post_categories( $cats = '' ) {
		if ( get_post_type() == SHAHA_GIVE_FORMS_PT_FORMS ) {
			$cats = shaha_get_post_terms( ', ', get_the_ID(), SHAHA_GIVE_FORMS_TAXONOMY_CATEGORY );
		}
		return $cats;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if (shaha_exists_give()) { require_once SHAHA_THEME_DIR . 'plugins/give/give-styles.php'; }