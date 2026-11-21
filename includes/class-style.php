<?php
/**
 * The site-specific part of the login stylesheet: CSS custom properties built from the settings.
 *
 * @package MG_Login
 */

namespace MG_Login;

defined( 'ABSPATH' ) || exit;

/**
 * Builds the inline CSS.
 */
final class Style {

	/**
	 * Size of the bundled logo (login-logo.png).
	 */
	const DEFAULT_LOGO_W = 425;
	const DEFAULT_LOGO_H = 107;

	/**
	 * Logo width on phones relative to the desktop width (224 px for the default 306 px).
	 */
	const PHONE_LOGO_SCALE = 224 / 306;

	/**
	 * The inline CSS for the given settings.
	 *
	 * @param array<string, mixed> $s       Settings (Settings::all()).
	 * @param array<string, mixed> $context Resolved values: heading (string), logo_url (string),
	 *                                      logo_w / logo_h (int, natural size), bg_url (string),
	 *                                      bg_design (bool, the CSS background when there is no image).
	 * @return string
	 */
	public static function inline_css( array $s, array $context ): string {
		$width  = (int) $s['logo_width'];
		$ratio  = ( $context['logo_w'] > 0 && $context['logo_h'] > 0 ) ? $context['logo_h'] / $context['logo_w'] : self::DEFAULT_LOGO_H / self::DEFAULT_LOGO_W;
		$height = (int) round( $width * $ratio );
		$phone  = (int) round( $width * self::PHONE_LOGO_SCALE );

		$vars = array(
			'--mgl-accent'           => $s['accent'],
			'--mgl-accent-dark'      => $s['accent_hover'],
			'--mgl-accent-ring'      => self::rgba( $s['accent'], 0.14 ),
			'--mgl-accent-shadow'    => self::rgba( $s['accent'], 0.30 ),
			'--mgl-accent-shadow-hi' => self::rgba( $s['accent'], 0.38 ),
			'--mgl-bg-color'         => $s['bg_color'],
			'--mgl-bg-image'         => self::background( $context ),
			'--mgl-logo-image'       => '' !== $context['logo_url'] ? self::url( $context['logo_url'] ) : 'none',
			'--mgl-logo-w'           => $width . 'px',
			'--mgl-logo-h'           => $height . 'px',
			'--mgl-logo-w-sm'        => $phone . 'px',
			'--mgl-logo-h-sm'        => (int) round( $phone * $ratio ) . 'px',
			'--mgl-heading'          => self::string( (string) $context['heading'] ),
			'--mgl-card-align'       => self::align( (string) $s['card_position'] ),
			'--mgl-overlay'          => $s['bg_overlay'] ? 'block' : 'none',
		);

		$css = 'body.login.mgl-login{';
		foreach ( $vars as $name => $value ) {
			$css .= $name . ':' . $value . ';';
		}
		$css .= '}';

		if ( '' !== (string) $s['custom_css'] ) {
			$css .= "\n" . Settings::css( (string) $s['custom_css'] );
		}
		return $css;
	}

	/**
	 * The background-image value: the chosen image, the MGraphics design drawn in login.css, or
	 * none (colour only).
	 *
	 * @param array<string, mixed> $context Resolved values.
	 * @return string
	 */
	public static function background( array $context ): string {
		if ( '' !== (string) ( $context['bg_url'] ?? '' ) ) {
			return self::url( (string) $context['bg_url'] );
		}
		return empty( $context['bg_design'] ) ? 'none' : 'var(--mgl-bg-design)';
	}

	/**
	 * Flexbox alignment for the card position.
	 *
	 * @param string $position left | center | right.
	 * @return string
	 */
	public static function align( string $position ): string {
		switch ( $position ) {
			case 'center':
				return 'center';
			case 'right':
				return 'flex-end';
			default:
				return 'flex-start';
		}
	}

	/**
	 * rgba() from #rrggbb.
	 *
	 * @param string $hex   Colour.
	 * @param float  $alpha Opacity.
	 * @return string
	 */
	public static function rgba( string $hex, float $alpha ): string {
		$hex = ltrim( $hex, '#' );
		if ( ! preg_match( '/^[0-9a-f]{6}$/i', $hex ) ) {
			$hex = '2563eb';
		}
		return sprintf(
			'rgba(%d, %d, %d, %s)',
			hexdec( substr( $hex, 0, 2 ) ),
			hexdec( substr( $hex, 2, 2 ) ),
			hexdec( substr( $hex, 4, 2 ) ),
			rtrim( rtrim( number_format( $alpha, 2, '.', '' ), '0' ), '.' )
		);
	}

	/**
	 * A quoted CSS string. Characters that could end the string, the declaration or the style
	 * element are written as CSS escapes.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public static function string( string $text ): string {
		$out    = '';
		$length = strlen( $text );
		for ( $i = 0; $i < $length; $i++ ) {
			$char = $text[ $i ];
			$code = ord( $char );
			if ( $code < 0x20 || $code === 0x7f || false !== strpos( "\"'\\<>&;{}", $char ) ) {
				$out .= '\\' . dechex( $code ) . ' ';
			} else {
				$out .= $char;
			}
		}
		return '"' . $out . '"';
	}

	/**
	 * A url() value for an absolute http(s) address.
	 *
	 * @param string $address Address.
	 * @return string
	 */
	public static function url( string $address ): string {
		if ( ! preg_match( '~^(https?:)?//~i', $address ) ) {
			return 'none';
		}
		return 'url(' . self::string( $address ) . ')';
	}
}
