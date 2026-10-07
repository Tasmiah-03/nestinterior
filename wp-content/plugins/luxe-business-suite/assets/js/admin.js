/**
 * Luxe Business Suite — Admin JS v1.0.0
 * Handles media pickers in admin meta boxes
 */
( function ( $ ) {
    'use strict';

    $( function () {

        // Generic single image picker
        $( document ).on( 'click', '.lbs-pick-image', function ( e ) {
            e.preventDefault();
            var targetId = $( this ).data( 'target' );
            var $input   = $( '#' + targetId );

            var frame = wp.media( {
                title  : lbsAdmin.selectImage,
                button : { text: lbsAdmin.useImage },
                multiple: false,
                library: { type: 'image' },
            } );

            frame.on( 'select', function () {
                var attachment = frame.state().get( 'selection' ).first().toJSON();
                var thumbUrl   = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $input.val( attachment.id );
                var $preview = $input.siblings( '.lbs-img-preview' );
                if ( $preview.length ) {
                    $preview.html( '<img src="' + thumbUrl + '" style="max-width:100%;max-height:100px;object-fit:contain;">' ).css( 'border-style', 'solid' );
                }
            } );

            frame.open();
        } );

        // Generic file picker
        $( document ).on( 'click', '.lbs-pick-file', function ( e ) {
            e.preventDefault();
            var targetId = $( this ).data( 'target' );
            var $input   = $( '#' + targetId );

            var frame = wp.media( {
                title   : 'Select File',
                button  : { text: 'Use This File' },
                multiple: false,
            } );

            frame.on( 'select', function () {
                var attachment = frame.state().get( 'selection' ).first().toJSON();
                $input.val( attachment.id );
                var fileName = attachment.filename || attachment.url.split('/').pop();
                var $preview = $input.siblings( '.lbs-doc-preview' );
                if ( $preview.length ) $preview.html( '📎 ' + fileName );
            } );

            frame.open();
        } );

        // Mood board gallery picker
        $( document ).on( 'click', '.lbs-pick-gallery', function ( e ) {
            e.preventDefault();
            var $list  = $( '#lbs-mb-gallery-list' );
            var $input = $( '#lbs_mb_images' );

            if ( ! $list.length || ! $input.length ) return;

            var frame = wp.media( {
                title   : 'Add Mood Board Images',
                button  : { text: 'Add to Board' },
                multiple: 'add',
                library : { type: 'image' },
            } );

            frame.on( 'select', function () {
                var existing = $list.find( 'li[data-id]' ).map( function () {
                    return parseInt( $( this ).data('id'), 10 );
                } ).get();

                frame.state().get('selection').each( function ( attachment ) {
                    var id = attachment.id;
                    if ( existing.indexOf( id ) !== -1 ) return;
                    var thumbUrl = attachment.attributes.sizes && attachment.attributes.sizes.thumbnail
                        ? attachment.attributes.sizes.thumbnail.url
                        : attachment.attributes.url;

                    var $li = $( '<li data-id="' + id + '" style="position:relative;"></li>' );
                    $li.html(
                        '<img src="' + thumbUrl + '" style="width:80px;height:80px;object-fit:cover;display:block;">'
                        + '<button type="button" onclick="this.parentElement.remove();lbsUpdateMbIds()" style="position:absolute;top:0;right:0;background:rgba(0,0,0,.6);color:#fff;border:none;cursor:pointer;width:20px;height:20px;font-size:12px;line-height:20px;text-align:center;padding:0;">✕</button>'
                    );
                    $list.append( $li );
                    existing.push( id );
                } );

                var ids = $list.find( 'li[data-id]' ).map( function () {
                    return $( this ).data( 'id' );
                } ).get();
                $input.val( ids.join( ',' ) );
            } );

            frame.open();
        } );

    } );

} )( jQuery );
