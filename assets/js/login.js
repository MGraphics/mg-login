/**
 * MG Login — runs in the login screen footer, right after WordPress prints the language
 * switcher, so the changes below happen before the first paint.
 *
 * 1. Moves the language switcher into the card, under the form (before the privacy policy
 *    link when there is one). Without JavaScript it stays below the card.
 * 2. Marks notices printed by other plugins in the old markup (a bare <p class="message"> or
 *    #login_error) the way MG Login marks WordPress' own: errors as alerts, the rest as
 *    polite status messages.
 */
( function () {
	'use strict';

	var card = document.getElementById( 'login' );
	var switcher = document.querySelector( 'body > .language-switcher' );
	if ( card && switcher ) {
		card.insertBefore( switcher, card.querySelector( ':scope > .privacy-policy-page-link' ) );
		document.body.classList.add( 'mgl-switcher-in-card' );
	}

	if ( ! card ) {
		return;
	}
	var notices = card.querySelectorAll( ':scope > .message, :scope > .notice, :scope > #login_error' );
	Array.prototype.forEach.call( notices, function ( notice ) {
		if ( notice.hasAttribute( 'role' ) || notice.hasAttribute( 'aria-live' ) ) {
			return;
		}
		var error = 'login_error' === notice.id || notice.classList.contains( 'notice-error' );
		notice.setAttribute( 'role', error ? 'alert' : 'status' );
		notice.setAttribute( 'aria-live', error ? 'assertive' : 'polite' );
	} );
}() );
