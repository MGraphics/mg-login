<?php
/**
 * Login screen: the background it resolves and the live-region roles of WordPress' notices.
 *
 * @package MG_Login
 */

use MG_Login\Login_Screen;
use MG_Login\Settings;
use PHPUnit\Framework\TestCase;

final class LoginScreenTest extends TestCase {

	private function background( array $settings ): array {
		$context = ( new Login_Screen() )->context( array_merge( Settings::defaults(), $settings ) );
		return array( $context['bg_url'], $context['bg_webp'] );
	}

	public function test_a_fresh_install_shows_the_bundled_mgraphics_photo(): void {
		$this->assertSame(
			array(
				'https://example.com/wp-content/plugins/mg-login/assets/images/login-bg.jpg',
				'https://example.com/wp-content/plugins/mg-login/assets/images/login-bg.webp',
			),
			$this->background( array() )
		);
	}

	public function test_a_media_library_image_is_kept(): void {
		$this->assertSame(
			array( 'https://example.com/wp-content/uploads/image-7.jpg', '' ),
			$this->background( array( 'bg_type' => 'custom', 'bg_image_id' => 7 ) )
		);
	}

	public function test_a_deleted_media_library_image_falls_back_to_the_photo(): void {
		list( $url ) = $this->background( array( 'bg_type' => 'custom', 'bg_image_id' => 99 ) );
		$this->assertStringEndsWith( '/assets/images/login-bg.jpg', $url );
	}

	public function test_colour_only_has_no_image(): void {
		$this->assertSame( array( '', '' ), $this->background( array( 'bg_type' => 'none' ) ) );
	}

	public function test_the_bundled_photo_files_exist(): void {
		$dir = dirname( __DIR__, 2 ) . '/assets/images/';
		$this->assertFileExists( $dir . 'login-bg.jpg' );
		$this->assertFileExists( $dir . 'login-bg.webp' );
		$this->assertLessThan( 250 * 1024, filesize( $dir . 'login-bg.jpg' ) );
		$this->assertLessThan( 150 * 1024, filesize( $dir . 'login-bg.webp' ) );
	}

	public function test_errors_are_alerts(): void {
		$args = ( new Login_Screen() )->notice_args(
			array(
				'type' => 'error',
				'id'   => 'login_error',
			)
		);
		$this->assertSame( 'alert', $args['attributes']['role'] );
		$this->assertSame( 'assertive', $args['attributes']['aria-live'] );
		$this->assertSame( 'login_error', $args['id'] );
	}

	public function test_messages_are_polite_status_messages(): void {
		$args = ( new Login_Screen() )->notice_args(
			array(
				'type'               => 'info',
				'id'                 => 'login-message',
				'additional_classes' => array( 'message' ),
			)
		);
		$this->assertSame( 'status', $args['attributes']['role'] );
		$this->assertSame( 'polite', $args['attributes']['aria-live'] );
		$this->assertSame( array( 'message' ), $args['additional_classes'] );
	}

	public function test_nested_and_foreign_notices_are_left_alone(): void {
		$screen = new Login_Screen();
		// The instruction inside #login-message has no id; other notices have their own.
		$inner = array(
			'type'               => 'info',
			'additional_classes' => array( 'message' ),
		);
		$this->assertSame( $inner, $screen->notice_args( $inner ) );
		$other = array(
			'type' => 'warning',
			'id'   => 'something-else',
		);
		$this->assertSame( $other, $screen->notice_args( $other ) );
		$this->assertSame( 'not an array', $screen->notice_args( 'not an array' ) );
	}

	public function test_attributes_set_by_others_are_kept(): void {
		$args = ( new Login_Screen() )->notice_args(
			array(
				'id'         => 'login_error',
				'attributes' => array(
					'role'      => 'status',
					'data-test' => '1',
				),
			)
		);
		$this->assertSame( 'status', $args['attributes']['role'] );
		$this->assertSame( '1', $args['attributes']['data-test'] );
		$this->assertSame( 'assertive', $args['attributes']['aria-live'] );
	}

	public function test_the_roles_are_added_on_the_login_screen_only(): void {
		$GLOBALS['mgl_hooks'] = array();
		$screen               = new Login_Screen();
		$screen->register();
		$this->assertContains( 'login_init', $GLOBALS['mgl_hooks'] );
		$this->assertNotContains( 'wp_admin_notice_args', $GLOBALS['mgl_hooks'] );
		$screen->announce_notices();
		$this->assertContains( 'wp_admin_notice_args', $GLOBALS['mgl_hooks'] );
	}
}
