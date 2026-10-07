/**
 * Luxe Interior — Customizer Live Preview
 */
( function( $ ) {

    // Accent color live preview
    wp.customize( 'accent_color', function( value ) {
        value.bind( function( newval ) {
            document.documentElement.style.setProperty( '--color-accent', newval );
        } );
    } );

    // Site title
    wp.customize( 'blogname', function( value ) {
        value.bind( function( newval ) {
            $( '.site-logo a, .footer-brand .logo' ).html( newval + '<span>.</span>' );
        } );
    } );

    // Hero texts
    wp.customize( 'hero_eyebrow', function( value ) {
        value.bind( function( newval ) {
            $( '.hero-content .eyebrow' ).text( newval );
        } );
    } );

    wp.customize( 'hero_title', function( value ) {
        value.bind( function( newval ) {
            $( '.hero-content h1' ).html( newval );
        } );
    } );

    wp.customize( 'hero_subtitle', function( value ) {
        value.bind( function( newval ) {
            $( '.hero-content p' ).text( newval );
        } );
    } );

} )( jQuery );
