/**
 * MG Login settings page: colour pickers, media library pickers, reset confirmation.
 */
( function ( $ ) {
	'use strict';

	var texts = window.mgLoginAdmin || {};

	$( function () {
		$( '.mg-login-color' ).wpColorPicker();

		// The image picker is relevant only when "Image from the media library" is chosen.
		$( '.mg-login-choice' ).each( function () {
			var $choice = $( this );
			var $media = $( '.mg-login-media[data-kind="' + $choice.data( 'target' ) + '"]' );
			var sync = function () {
				$media.toggle( 'custom' === $choice.find( 'input:checked' ).val() );
			};
			$choice.on( 'change', 'input', sync );
			sync();
		} );

		$( '.mg-login-media' ).each( function () {
			var $box = $( this );
			var $id = $box.find( '.mg-login-media-id' );
			var $preview = $box.find( '.mg-login-media-preview' );
			var $remove = $box.find( '.mg-login-media-remove' );
			var frame = null;

			$box.on( 'click', '.mg-login-media-choose', function ( event ) {
				event.preventDefault();
				if ( ! frame ) {
					frame = wp.media( {
						title: 'logo' === $box.data( 'kind' ) ? texts.chooseLogo : texts.chooseBackground,
						button: { text: texts.use },
						library: { type: 'image' },
						multiple: false
					} );
					frame.on( 'select', function () {
						var image = frame.state().get( 'selection' ).first().toJSON();
						var url = image.sizes && image.sizes.medium ? image.sizes.medium.url : image.url;
						$id.val( image.id );
						$preview.removeClass( 'is-empty' ).empty().append( $( '<img alt="">' ).attr( 'src', url ) );
						$remove.prop( 'hidden', false );
					} );
				}
				frame.open();
			} );

			$remove.on( 'click', function ( event ) {
				event.preventDefault();
				$id.val( '0' );
				$preview.addClass( 'is-empty' ).empty();
				$remove.prop( 'hidden', true );
			} );
		} );

		$( '.mg-login-reset-button' ).on( 'click', function ( event ) {
			if ( ! window.confirm( $( this ).data( 'confirm' ) ) ) {
				event.preventDefault();
			}
		} );
	} );
}( jQuery ) );
