/* global jQuery:false, elementorFrontend:false */

// Attention! This action must be executed before the document.ready
// to add properties to the elementorFrontend object before it used first time!
(function(){
    "use strict";
    // Add page settings to the elementorFrontend object
    // in the frontend for non-Elementor pages (blog pages, categories, tags, etc.)
    // Run this code after the all other init code via setTimeout(0)
    setTimeout(function(){
        if (typeof elementorFrontend !== 'undefined'
            && !elementorFrontend.isEditMode()
            && typeof elementorFrontend.config !== 'undefined'
            && typeof elementorFrontend.config.settings !== 'undefined'
            && typeof elementorFrontend.config.settings.general === 'undefined'
        ) {
            elementorFrontend.config.settings.general = {
                'elementor_stretched_section_container': TRX_ADDONS_STORAGE['elementor_stretched_section_container']
            };
        }
    }, 0);

    // Disable Elementor's lightbox on the .esgbox links
    jQuery('.elementor-widget-container a.esgbox').attr('data-elementor-open-lightbox', 'no');
})();

jQuery(document).ready(function() {
    "use strict";

    var trx_addons_once_resize = false;

    // Init hooks after the 1ms, because elementorFrontend.hooks isn't available on 'ready' event
    //setTimeout(function(){
    // Make sure you run this code under Elementor - not work with last Elementor
    jQuery( window ).on( 'elementor/frontend/init', function() {
        if (typeof window.elementorFrontend !== 'undefined' && typeof window.elementorFrontend.hooks !== 'undefined') {

            // If Elementor is in the Editor's Preview mode
            if (elementorFrontend.isEditMode()) {
                // Init elements after creation
                elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $cont ) {

                    // Init hidden elements (widgets, shortcodes) when its added to the preview area
                    jQuery(document).trigger('action.init_hidden_elements', [$cont]);

                    // Trigger 'resize' actions after the element is added (inited)
                    if ($cont.parents('.elementor-section-stretched').length > 0 && !trx_addons_once_resize) {
                        trx_addons_once_resize = true;
                        jQuery(document).trigger('action.resize_trx_addons', [$cont.parents('.elementor-section-stretched')]);
                    } else {
                        jQuery(document).trigger('action.resize_trx_addons', [$cont]);
                    }

                } );

                // If Elementor is in Frontend
            } else {
                trx_addons_once_resize = true;
                jQuery(document).trigger('action.resize_trx_addons');
            }

        }
        // Init hooks after the 1ms, because elementorFrontend.hooks isn't available on 'ready' event
        //}, typeof elementorFrontend === 'undefined' || typeof elementorFrontend.hooks === 'undefined' ? 1 : 0);
    });

});