<?php
/**
 * The login screen: stylesheet, logo link, "forgot password" link inside the form, placeholders
 * and the language switcher moved under the form.
 *
 * Every hook here belongs to the login screen (wp-login.php, also when another plugin serves it
 * at a private address), so nothing is printed on the public pages. Sign-in, password reset,
 * registration, redirects and messages are left to WordPress.
 *
 * @package MG_Login
 */

namespace MG_Login;

defined( 'ABSPATH' ) || exit;

/**
 * Login screen.
 */
final class Login_Screen {

	/**
	 * Hooks.
	 */
	public function register(): void {
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue' ), 20 );
		add_filter( 'login_headerurl', array( $this, 'logo_url' ), 20 );
		add_filter( 'login_headertext', array( $this, 'logo_title' ), 20 );
		add_filter( 'login_body_class', array( $this, 'body_class' ) );
		add_action( 'login_form', array( $this, 'lost_password_link' ) );
		add_filter( 'login_display_language_dropdown', array( $this, 'language_dropdown' ), 20 );
	}

	/**
	 * Stylesheet, the site's settings as CSS custom properties, and the small script that moves
	 * the language switcher into the card and adds the field placeholders.
	 */
	public function enqueue(): void {
		$s = Settings::all();

		wp_enqueue_style( 'mg-login', MG_LOGIN_URL . 'assets/css/login.css', array( 'login' ), MG_LOGIN_VERSION );
		wp_add_inline_style( 'mg-login', Style::inline_css( $s, $this->context( $s ) ) );

		wp_enqueue_script( 'mg-login', MG_LOGIN_URL . 'assets/js/login.js', array(), MG_LOGIN_VERSION, true );
		wp_localize_script(
			'mg-login',
			'mgLogin',
			array(
				'user' => __( 'Username or email', 'mg-login' ),
				'pass' => __( 'Password', 'mg-login' ),
			)
		);
	}

	/**
	 * Resolved images and texts for the stylesheet.
	 *
	 * @param array<string, mixed> $s Settings.
	 * @return array<string, mixed>
	 */
	public function context( array $s ): array {
		$context = array(
			'heading'  => '' !== $s['heading'] ? (string) $s['heading'] : __( 'Log in', 'mg-login' ),
			'logo_url' => '',
			'logo_w'   => Style::DEFAULT_LOGO_W,
			'logo_h'   => Style::DEFAULT_LOGO_H,
			'bg_url'   => '',
		);

		if ( 'default' === $s['logo_type'] ) {
			$context['logo_url'] = MG_LOGIN_URL . 'assets/images/login-logo.png';
		} elseif ( 'custom' === $s['logo_type'] && $s['logo_id'] ) {
			$image = wp_get_attachment_image_src( (int) $s['logo_id'], 'full' );
			if ( $image ) {
				$context['logo_url'] = (string) $image[0];
				$context['logo_w']   = (int) $image[1];
				$context['logo_h']   = (int) $image[2];
			} else {
				$context['logo_url'] = MG_LOGIN_URL . 'assets/images/login-logo.png';
			}
		}

		if ( 'default' === $s['bg_type'] ) {
			$context['bg_url'] = MG_LOGIN_URL . 'assets/images/login-bg.jpg';
		} elseif ( 'custom' === $s['bg_type'] && $s['bg_image_id'] ) {
			$image             = wp_get_attachment_image_src( (int) $s['bg_image_id'], 'full' );
			$context['bg_url'] = $image ? (string) $image[0] : MG_LOGIN_URL . 'assets/images/login-bg.jpg';
		}
		return $context;
	}

	/**
	 * Logo link: the configured address, or the site's home page.
	 *
	 * @return string
	 */
	public function logo_url(): string {
		$url = (string) Settings::get( 'logo_url' );
		return '' !== $url ? $url : home_url( '/' );
	}

	/**
	 * Logo text (read by screen readers): the configured title, or the site name.
	 *
	 * @return string
	 */
	public function logo_title(): string {
		$title = (string) Settings::get( 'logo_title' );
		return '' !== $title ? $title : get_bloginfo( 'name' );
	}

	/**
	 * Body classes.
	 *
	 * @param array<string> $classes Classes.
	 * @return array<string>
	 */
	public function body_class( $classes ): array {
		$classes   = (array) $classes;
		$classes[] = 'mgl-login';
		if ( 'none' === Settings::get( 'logo_type' ) ) {
			$classes[] = 'mgl-no-logo';
		}
		return $classes;
	}

	/**
	 * "Forgot your password?" under the password field. It points at WordPress' own
	 * lost-password address (which other plugins may have moved), so the built-in flow is used.
	 * Front-end forms that fire the same action (some membership plugins do) are left alone.
	 */
	public function lost_password_link(): void {
		if ( ! did_action( 'login_init' ) ) {
			return;
		}
		printf(
			'<p class="mgl-lostpassword"><a href="%s">%s</a></p>',
			esc_url( wp_lostpassword_url() ),
			esc_html__( 'Forgot your password?', 'mg-login' )
		);
	}

	/**
	 * Whether WordPress shows the language switcher.
	 *
	 * @param bool $display Current value.
	 * @return bool
	 */
	public function language_dropdown( $display ): bool {
		return (bool) $display && (bool) Settings::get( 'language_switcher' );
	}
}
