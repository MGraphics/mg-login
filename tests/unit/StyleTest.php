<?php
/**
 * Generated CSS.
 *
 * @package MG_Login
 */

use MG_Login\Settings;
use MG_Login\Style;
use PHPUnit\Framework\TestCase;

final class StyleTest extends TestCase {

	private function context( array $overrides = array() ): array {
		return array_merge(
			array(
				'heading'  => 'Logowanie',
				'logo_url' => 'https://example.com/logo.png',
				'logo_w'   => Style::DEFAULT_LOGO_W,
				'logo_h'   => Style::DEFAULT_LOGO_H,
				'bg_url'   => 'https://example.com/bg.jpg',
			),
			$overrides
		);
	}

	public function test_default_logo_sizes_match_the_original_theme(): void {
		$css = Style::inline_css( Settings::defaults(), $this->context() );
		$this->assertStringContainsString( '--mgl-logo-w:306px;', $css );
		$this->assertStringContainsString( '--mgl-logo-h:77px;', $css );
		$this->assertStringContainsString( '--mgl-logo-w-sm:224px;', $css );
		$this->assertStringContainsString( '--mgl-logo-h-sm:56px;', $css );
	}

	public function test_logo_height_follows_the_image(): void {
		$s          = Settings::defaults();
		$s['logo_width'] = 200;
		$css        = Style::inline_css( $s, $this->context( array( 'logo_w' => 400, 'logo_h' => 400 ) ) );
		$this->assertStringContainsString( '--mgl-logo-h:200px;', $css );
	}

	public function test_accent_derivatives(): void {
		$this->assertSame( 'rgba(37, 99, 235, 0.14)', Style::rgba( '#2563eb', 0.14 ) );
		$this->assertSame( 'rgba(37, 99, 235, 0.3)', Style::rgba( '#2563eb', 0.30 ) );
		$this->assertSame( 'rgba(16, 185, 129, 0.38)', Style::rgba( '#10b981', 0.38 ) );
	}

	public function test_card_position(): void {
		$this->assertSame( 'flex-start', Style::align( 'left' ) );
		$this->assertSame( 'center', Style::align( 'center' ) );
		$this->assertSame( 'flex-end', Style::align( 'right' ) );
		$this->assertSame( 'flex-start', Style::align( 'anything' ) );
	}

	public function test_strings_are_escaped(): void {
		$this->assertSame( '"Logowanie"', Style::string( 'Logowanie' ) );
		$this->assertSame( '"Zaloguj się"', Style::string( 'Zaloguj się' ) );
		$escaped = Style::string( '"};</style><script>' );
		$this->assertStringNotContainsString( '<', $escaped );
		$this->assertStringNotContainsString( '}', $escaped );
		$this->assertSame( 2, substr_count( $escaped, '"' ) );
	}

	public function test_only_absolute_http_urls(): void {
		$this->assertSame( 'none', Style::url( 'javascript:alert(1)' ) );
		$this->assertSame( 'none', Style::url( 'images/a.png' ) );
		$this->assertStringStartsWith( 'url("https://', Style::url( 'https://example.com/a.png?x=1&y=2' ) );
	}

	public function test_no_image_means_none(): void {
		$css = Style::inline_css( Settings::defaults(), $this->context( array( 'logo_url' => '', 'bg_url' => '' ) ) );
		$this->assertStringContainsString( '--mgl-bg-image:none;', $css );
		$this->assertStringContainsString( '--mgl-logo-image:none;', $css );
	}

	public function test_default_background_is_the_css_design(): void {
		$css = Style::inline_css( Settings::defaults(), $this->context( array( 'bg_url' => '', 'bg_design' => true ) ) );
		$this->assertStringContainsString( '--mgl-bg-image:var(--mgl-bg-design);', $css );
	}

	public function test_an_image_wins_over_the_design(): void {
		$css = Style::inline_css( Settings::defaults(), $this->context( array( 'bg_design' => true ) ) );
		$this->assertStringContainsString( '--mgl-bg-image:url("https://example.com/bg.jpg");', $css );
	}

	public function test_the_mgraphics_photo_layout(): void {
		// As on mgraphics.com.pl: a photo from the media library, the light shading, the card on the left.
		$s                  = Settings::defaults();
		$s['bg_type']       = 'custom';
		$s['bg_image_id']   = 7;
		$s['bg_overlay']    = true;
		$s['card_position'] = 'left';
		$css                = Style::inline_css( $s, $this->context( array( 'bg_url' => 'https://example.com/wp-content/uploads/login-bg.jpg' ) ) );
		$this->assertStringContainsString( '--mgl-bg-image:url("https://example.com/wp-content/uploads/login-bg.jpg");', $css );
		$this->assertStringContainsString( '--mgl-shade:var(--mgl-shade-design);', $css );
		$this->assertStringContainsString( '--mgl-card-align:flex-start;', $css );
		$this->assertStringContainsString( '--mgl-logo-w:306px;', $css );
		$this->assertStringContainsString( '--mgl-bg-color:#ece8e3;', $css );
	}

	public function test_shading_can_be_switched_off(): void {
		$s               = Settings::defaults();
		$s['bg_overlay'] = false;
		$this->assertStringContainsString( '--mgl-shade:none;', Style::inline_css( $s, $this->context() ) );
	}

	public function test_custom_css_is_appended(): void {
		$s               = Settings::defaults();
		$s['custom_css'] = 'body.login #login{border-radius:0}';
		$css             = Style::inline_css( $s, $this->context() );
		$this->assertStringEndsWith( "\nbody.login #login{border-radius:0}", $css );
	}
}
