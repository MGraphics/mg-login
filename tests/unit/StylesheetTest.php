<?php
/**
 * The login stylesheet and script: plain fields, plain notices, readable colours.
 *
 * @package MG_Login
 */

use PHPUnit\Framework\TestCase;

final class StylesheetTest extends TestCase {

	private function css(): string {
		return (string) file_get_contents( dirname( __DIR__, 2 ) . '/assets/css/login.css' );
	}

	/**
	 * The declarations of every rule whose selector list matches the pattern.
	 *
	 * @return array<string>
	 */
	private function rules( string $selector_pattern ): array {
		$css = preg_replace( '~/\*.*?\*/~s', '', $this->css() );
		preg_match_all( '~([^{}]+)\{([^{}]*)\}~', (string) $css, $m, PREG_SET_ORDER );
		$out = array();
		foreach ( $m as $rule ) {
			if ( preg_match( $selector_pattern, $rule[1] ) ) {
				$out[] = $rule[2];
			}
		}
		return $out;
	}

	private function token( string $name ): string {
		$this->assertSame( 1, preg_match( '~' . preg_quote( $name, '~' ) . ':\s*(#[0-9a-f]{6});~i', $this->css(), $m ), $name );
		return $m[1];
	}

	/**
	 * WCAG contrast ratio of two #rrggbb colours.
	 */
	private function contrast( string $a, string $b ): float {
		$luminance = static function ( string $hex ): float {
			$sum = 0.0;
			foreach ( array( 0.2126, 0.7152, 0.0722 ) as $i => $weight ) {
				$c    = hexdec( substr( $hex, 1 + 2 * $i, 2 ) ) / 255;
				$sum += $weight * ( $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 ) );
			}
			return $sum;
		};
		$la = $luminance( $a );
		$lb = $luminance( $b );
		return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
	}

	public function test_fields_have_no_icons_and_no_placeholders(): void {
		$css = $this->css();
		$this->assertStringNotContainsString( 'data:image', $css );
		$this->assertStringNotContainsString( '::placeholder', $css );
		$this->assertStringNotContainsString( 'label[for="user_login"]', $css, 'The field labels are visible.' );
		$js = (string) file_get_contents( dirname( __DIR__, 2 ) . '/assets/js/login.js' );
		$this->assertStringNotContainsString( 'placeholder', $js );
	}

	public function test_notices_are_plain_text_with_a_rule(): void {
		$rules = $this->rules( '~#login_error|\.notice|\.message|\.success~' );
		$this->assertNotEmpty( $rules );
		foreach ( $rules as $declarations ) {
			$this->assertDoesNotMatchRegularExpression( '~border-left~', $declarations );
			$this->assertDoesNotMatchRegularExpression( '~background(-color)?:\s*(#|rgb)~', $declarations );
		}
		$base = implode( ';', $this->rules( '~\.login #login \.message,~' ) );
		$this->assertStringContainsString( 'background: none', $base );
		$this->assertStringContainsString( 'border-bottom: 1px solid var(--mgl-rule)', $base );
		$this->assertStringContainsString( 'border-radius: 0', $base );
	}

	public function test_text_and_field_colours_are_readable_on_the_white_card(): void {
		foreach ( array( '--mgl-ink', '--mgl-text', '--mgl-faint', '--mgl-error', '--mgl-warning', '--mgl-success' ) as $name ) {
			$this->assertGreaterThanOrEqual( 4.5, $this->contrast( $this->token( $name ), '#ffffff' ), $name );
		}
		// The outline of a field against the card and the field (WCAG 1.4.11: 3:1).
		$this->assertSame( '#ffffff', $this->token( '--mgl-field-bg' ) );
		$this->assertGreaterThanOrEqual( 3.0, $this->contrast( $this->token( '--mgl-field-border' ), '#ffffff' ) );
		// The pale decoration colour is not used for the links.
		$this->assertEmpty( preg_grep( '~var\(--mgl-muted\)~', $this->rules( '~\ba\b|lostpassword|privacy~' ) ) );
		// The default accent as link text.
		$this->assertGreaterThanOrEqual( 4.5, $this->contrast( $this->token( '--mgl-accent' ), '#ffffff' ) );
	}

	public function test_only_the_cards_own_heading_is_the_logo(): void {
		$this->assertEmpty( $this->rules( '~body\.login h1\b~' ), 'A bare "body.login h1" also catches headings inside forms.' );
		$this->assertNotEmpty( $this->rules( '~body\.login #login > h1~' ) );
	}
}
