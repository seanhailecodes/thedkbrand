jQuery(document).ready(function($){
    /* Move Fornt page widgets to frontpage panel */
	wp.customize.section( 'sidebar-widgets-service' ).panel( 'frontpage_settings' );
	wp.customize.section( 'sidebar-widgets-service' ).priority( '20' );
    wp.customize.section( 'sidebar-widgets-about' ).panel( 'frontpage_settings' );
    wp.customize.section( 'sidebar-widgets-about' ).priority( '70' );
    wp.customize.section( 'sidebar-widgets-testimonial' ).panel( 'frontpage_settings' );
    wp.customize.section( 'sidebar-widgets-testimonial' ).priority( '80' );    
    wp.customize.section( 'sidebar-widgets-cta' ).panel( 'frontpage_settings' );
    wp.customize.section( 'sidebar-widgets-cta' ).priority( '90' );
    wp.customize.section( 'sidebar-widgets-client' ).panel( 'frontpage_settings' );
    wp.customize.section( 'sidebar-widgets-client' ).priority( '110' );

    /* Move featured widgets to general settings */
    wp.customize.section( 'sidebar-widgets-featured' ).panel( 'general_settings' );
    wp.customize.section( 'sidebar-widgets-featured' ).priority( '35' );  
    
    /* Home page preview url */
    wp.customize.panel( 'frontpage_settings', function( section ){
        section.expanded.bind( function( isExpanded ) {
            if( isExpanded ){
                wp.customize.previewer.previewUrl.set( blossom_shop_cdata.home );
            }
        });
    });
    
});

( function( api ) {

    // Extends our custom "example-1" section.
    api.sectionConstructor['pro-section'] = api.Section.extend( {

        // No events for this type of section.
        attachEvents: function () {},

        // Always make the section active.
        isContextuallyActive: function () {
            return true;
        }
    } );

} )( wp.customize );