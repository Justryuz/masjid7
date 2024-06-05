<?php
/*  Cookie Information support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('shaha_wp_gdpr_theme_setup9')) {
    add_action( 'after_setup_theme', 'shaha_wp_gdpr_theme_setup9', 9 );
    function shaha_wp_gdpr_theme_setup9() {
        if (is_admin()) {
            add_filter( 'shaha_filter_tgmpa_required_plugins',		'shaha_wp_gdpr_tgmpa_required_plugins' );
        }
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'shaha_wp_gdpr_tgmpa_required_plugins' ) ) {
    
    function shaha_wp_gdpr_tgmpa_required_plugins($list=array()) {
        if (shaha_storage_isset('required_plugins', 'wp-gdpr-compliance')) {
            $list[] = array(
                'name' 		=> shaha_storage_get_array('required_plugins', 'wp-gdpr-compliance'),
                'slug' 		=> 'wp-gdpr-compliance',
                'required' 	=> false
            );
        }
        return $list;
    }
}

?>