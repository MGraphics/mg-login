<?php
/**
 * Unit tests: settings and the generated CSS, with a few WordPress functions stubbed.
 *
 * @package MG_Login
 */

define( 'ABSPATH', __DIR__ . '/' );
require dirname( __DIR__, 2 ) . '/vendor/autoload.php';

$GLOBALS['mgl_options'] = array();
$GLOBALS['mgl_images']  = array( 7, 8 );

function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['mgl_options'] ) ? $GLOBALS['mgl_options'][ $name ] : $default;
}
function absint( $value ) {
	return abs( (int) $value );
}
function wp_attachment_is_image( $id ) {
	return in_array( (int) $id, $GLOBALS['mgl_images'], true );
}
function esc_url_raw( $url, $protocols = null ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	$scheme = strtolower( (string) parse_url( $url, PHP_URL_SCHEME ) );
	return in_array( $scheme, (array) $protocols, true ) ? $url : '';
}
function sanitize_text_field( $text ) {
	return trim( strip_tags( (string) $text ) );
}
function wp_strip_all_tags( $text ) {
	$text = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', (string) $text );
	return strip_tags( $text );
}

require dirname( __DIR__, 2 ) . '/includes/class-settings.php';
require dirname( __DIR__, 2 ) . '/includes/class-style.php';
