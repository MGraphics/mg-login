<?php
/**
 * Settings: defaults (the MGraphics look), reading and sanitising.
 *
 * @package MG_Login
 */

namespace MG_Login;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin settings, stored as one option.
 */
final class Settings {

	const OPTION = 'mg_login_settings';

	/**
	 * Logo width range in pixels.
	 */
	const LOGO_WIDTH_MIN = 40;
	const LOGO_WIDTH_MAX = 400;

	/**
	 * Default values. Empty strings mean "use the automatic value" (site address, site name,
	 * translated heading).
	 *
	 * @return array<string, mixed>
	 */
	public static function defaults(): array {
		return array(
			'logo_type'         => 'default', // default | custom | none.
			'logo_id'           => 0,
			'logo_width'        => 306,
			'logo_url'          => '',
			'logo_title'        => '',
			'heading'           => '',
			'accent'            => '#2563eb',
			'accent_hover'      => '#1d4ed8',
			'bg_color'          => '#ece8e3',
			'bg_type'           => 'default', // default | custom | none.
			'bg_image_id'       => 0,
			'bg_overlay'        => true,
			'card_position'     => 'left', // left | center | right.
			'language_switcher' => true,
			'custom_css'        => '',
		);
	}

	/**
	 * All settings, merged with the defaults.
	 *
	 * @return array<string, mixed>
	 */
	public static function all(): array {
		$stored = get_option( self::OPTION, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		return array_merge( self::defaults(), array_intersect_key( $stored, self::defaults() ) );
	}

	/**
	 * One setting.
	 *
	 * @param string $key Key.
	 * @return mixed
	 */
	public static function get( string $key ) {
		$all = self::all();
		return $all[ $key ] ?? null;
	}

	/**
	 * Sanitises the submitted settings. Unknown keys are dropped; invalid values fall back to
	 * the defaults.
	 *
	 * @param mixed $input Submitted values.
	 * @return array<string, mixed>
	 */
	public static function sanitize( $input ): array {
		$defaults = self::defaults();
		$input    = is_array( $input ) ? $input : array();
		$out      = $defaults;

		$out['logo_type']     = self::choice( $input['logo_type'] ?? '', array( 'default', 'custom', 'none' ), $defaults['logo_type'] );
		$out['logo_id']       = self::image_id( $input['logo_id'] ?? 0 );
		$out['logo_width']    = self::int_range( $input['logo_width'] ?? $defaults['logo_width'], self::LOGO_WIDTH_MIN, self::LOGO_WIDTH_MAX, $defaults['logo_width'] );
		$out['logo_url']      = isset( $input['logo_url'] ) ? esc_url_raw( trim( (string) $input['logo_url'] ), array( 'http', 'https' ) ) : '';
		$out['logo_title']    = isset( $input['logo_title'] ) ? sanitize_text_field( (string) $input['logo_title'] ) : '';
		$out['heading']       = isset( $input['heading'] ) ? sanitize_text_field( (string) $input['heading'] ) : '';
		$out['accent']        = self::color( $input['accent'] ?? '', $defaults['accent'] );
		$out['accent_hover']  = self::color( $input['accent_hover'] ?? '', $defaults['accent_hover'] );
		$out['bg_color']      = self::color( $input['bg_color'] ?? '', $defaults['bg_color'] );
		$out['bg_type']       = self::choice( $input['bg_type'] ?? '', array( 'default', 'custom', 'none' ), $defaults['bg_type'] );
		$out['bg_image_id']   = self::image_id( $input['bg_image_id'] ?? 0 );
		$out['bg_overlay']    = ! empty( $input['bg_overlay'] );
		$out['card_position'] = self::choice( $input['card_position'] ?? '', array( 'left', 'center', 'right' ), $defaults['card_position'] );
		// A checkbox: absent from the form when unticked.
		$out['language_switcher'] = ! empty( $input['language_switcher'] );
		$out['custom_css']        = self::css( (string) ( $input['custom_css'] ?? '' ) );

		// A custom logo or background without an image falls back to the default one.
		if ( 'custom' === $out['logo_type'] && 0 === $out['logo_id'] ) {
			$out['logo_type'] = 'default';
		}
		if ( 'custom' === $out['bg_type'] && 0 === $out['bg_image_id'] ) {
			$out['bg_type'] = 'default';
		}
		return $out;
	}

	/**
	 * A value from a fixed list.
	 *
	 * @param mixed         $value    Value.
	 * @param array<string> $allowed  Allowed values.
	 * @param string        $fallback Fallback.
	 * @return string
	 */
	private static function choice( $value, array $allowed, string $fallback ): string {
		$value = is_string( $value ) ? $value : '';
		return in_array( $value, $allowed, true ) ? $value : $fallback;
	}

	/**
	 * An integer within a range.
	 *
	 * @param mixed $value    Value.
	 * @param int   $min      Minimum.
	 * @param int   $max      Maximum.
	 * @param int   $fallback Fallback for non-numbers.
	 * @return int
	 */
	private static function int_range( $value, int $min, int $max, int $fallback ): int {
		if ( ! is_numeric( $value ) ) {
			return $fallback;
		}
		return max( $min, min( $max, (int) $value ) );
	}

	/**
	 * The ID of an image attachment, or 0.
	 *
	 * @param mixed $value Value.
	 * @return int
	 */
	private static function image_id( $value ): int {
		$id = absint( $value );
		return ( $id && wp_attachment_is_image( $id ) ) ? $id : 0;
	}

	/**
	 * A #rrggbb colour.
	 *
	 * @param mixed  $value    Value.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	private static function color( $value, string $fallback ): string {
		$value = is_string( $value ) ? trim( $value ) : '';
		if ( preg_match( '/^#([0-9a-f]{3})$/i', $value, $m ) ) {
			$value = '#' . $m[1][0] . $m[1][0] . $m[1][1] . $m[1][1] . $m[1][2] . $m[1][2];
		}
		return preg_match( '/^#[0-9a-f]{6}$/i', $value ) ? strtolower( $value ) : $fallback;
	}

	/**
	 * Custom CSS: plain text that cannot close the style element.
	 *
	 * @param string $css CSS.
	 * @return string
	 */
	public static function css( string $css ): string {
		$css = wp_strip_all_tags( $css );
		$css = str_replace( '<', '', $css );
		return trim( $css );
	}
}
