<?php
/**
 * Unit tests: settings, the generated CSS, the login screen's notices and the stylesheet, with a few
 * WordPress functions stubbed.
 *
 * @package MG_Login
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'MG_LOGIN_URL', 'https://example.com/wp-content/plugins/mg-login/' );
require dirname( __DIR__, 2 ) . '/vendor/autoload.php';

$GLOBALS['mgl_options'] = array();
$GLOBALS['mgl_images']  = array( 7, 8 );

function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['mgl_options'] ) ? $GLOBALS['mgl_options'][ $name ] : $default;
}
function __( $text, $domain = 'default' ) {
	return $text;
}
function wp_get_attachment_image_src( $id, $size = 'thumbnail' ) {
	return in_array( (int) $id, $GLOBALS['mgl_images'], true ) ? array( 'https://example.com/wp-content/uploads/image-' . (int) $id . '.jpg', 1920, 1080, false ) : false;
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

$GLOBALS['mgl_hooks'] = array();

function add_action( $hook, $callback, $priority = 10, $args = 1 ) {
	$GLOBALS['mgl_hooks'][] = $hook;
	return true;
}
function add_filter( $hook, $callback, $priority = 10, $args = 1 ) {
	$GLOBALS['mgl_hooks'][] = $hook;
	return true;
}

require dirname( __DIR__, 2 ) . '/includes/class-settings.php';
require dirname( __DIR__, 2 ) . '/includes/class-style.php';
require dirname( __DIR__, 2 ) . '/includes/class-login-screen.php';
