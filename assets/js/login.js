/**
 * MG Login — runs in the login screen footer, right after WordPress prints the language
 * switcher, so the changes below happen before the first paint.
 *
 * 1. Moves the language switcher into the card, under the form (before the privacy policy
 *    link when there is one). Without JavaScript it stays below the card.
 * 2. Adds placeholders to the username and password fields of the main login form (their
 *    labels are visually hidden but stay for screen readers).
 */
( function () {
	'use strict';

	var card = document.getElementById( 'login' );
	var switcher = document.querySelector( 'body > .language-switcher' );
	if ( card && switcher ) {
		card.insertBefore( switcher, card.querySelector( ':scope > .privacy-policy-page-link' ) );
		document.body.classList.add( 'mgl-switcher-in-card' );
	}

	var texts = window.mgLogin || {};
	var user = document.getElementById( 'user_login' );
	var pass = document.getElementById( 'user_pass' );
	if ( user && pass && document.getElementById( 'loginform' ) ) {
		if ( texts.user && ! user.placeholder ) {
			user.placeholder = texts.user;
		}
		if ( texts.pass && ! pass.placeholder ) {
			pass.placeholder = texts.pass;
		}
	}
}() );
