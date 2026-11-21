<?php
/**
 * Settings sanitising.
 *
 * @package MG_Login
 */

use MG_Login\Settings;
use PHPUnit\Framework\TestCase;

final class SettingsTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['mgl_options'] = array();
	}

	public function test_defaults_are_the_mgraphics_look(): void {
		$s = Settings::all();
		$this->assertSame( 'default', $s['logo_type'] );
		$this->assertSame( 306, $s['logo_width'] );
		$this->assertSame( '#2563eb', $s['accent'] );
		$this->assertSame( '#1d4ed8', $s['accent_hover'] );
		$this->assertSame( '#ece8e3', $s['bg_color'] );
		$this->assertSame( 'left', $s['card_position'] );
		$this->assertTrue( $s['language_switcher'] );
		$this->assertTrue( $s['bg_overlay'] );
	}

	public function test_stored_unknown_keys_are_ignored(): void {
		$GLOBALS['mgl_options'][ Settings::OPTION ] = array( 'accent' => '#000000', 'evil' => 'x' );
		$s = Settings::all();
		$this->assertSame( '#000000', $s['accent'] );
		$this->assertArrayNotHasKey( 'evil', $s );
	}

	public function test_invalid_values_fall_back(): void {
		$out = Settings::sanitize(
			array(
				'logo_type'     => 'remote',
				'logo_width'    => 'wide',
				'accent'        => 'red; background:url(x)',
				'bg_color'      => '#ABC',
				'card_position' => 'top',
				'logo_url'      => 'javascript:alert(1)',
			)
		);
		$this->assertSame( 'default', $out['logo_type'] );
		$this->assertSame( 306, $out['logo_width'] );
		$this->assertSame( '#2563eb', $out['accent'] );
		$this->assertSame( '#aabbcc', $out['bg_color'] );
		$this->assertSame( 'left', $out['card_position'] );
		$this->assertSame( '', $out['logo_url'] );
	}

	public function test_logo_width_is_clamped(): void {
		$this->assertSame( Settings::LOGO_WIDTH_MAX, Settings::sanitize( array( 'logo_width' => 5000 ) )['logo_width'] );
		$this->assertSame( Settings::LOGO_WIDTH_MIN, Settings::sanitize( array( 'logo_width' => 1 ) )['logo_width'] );
	}

	public function test_custom_image_needs_an_image_attachment(): void {
		$out = Settings::sanitize( array( 'logo_type' => 'custom', 'logo_id' => 99, 'bg_type' => 'custom', 'bg_image_id' => 8 ) );
		$this->assertSame( 'default', $out['logo_type'] );
		$this->assertSame( 0, $out['logo_id'] );
		$this->assertSame( 'custom', $out['bg_type'] );
		$this->assertSame( 8, $out['bg_image_id'] );
	}

	public function test_checkboxes_absent_mean_off(): void {
		$out = Settings::sanitize( array() );
		$this->assertFalse( $out['language_switcher'] );
		$this->assertFalse( $out['bg_overlay'] );
	}

	public function test_custom_css_cannot_close_the_style_element(): void {
		$css = Settings::css( "a{color:red}</style><script>alert(1)</script>\n<b>x</b> p > q{}" );
		$this->assertStringNotContainsString( '<', $css );
		$this->assertStringNotContainsString( 'alert', $css );
		$this->assertStringContainsString( 'p > q{}', $css );
	}
}
