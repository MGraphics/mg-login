<?php
/**
 * The login screen: stylesheet, logo link, "forgot password" link inside the form, notices
 * announced to screen readers and the language switcher moved under the form.
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
		add_action( 'login_init', array( $this, 'announce_notices' ) );
		add_filter( 'login_display_language_dropdown', array( $this, 'language_dropdown' ), 20 );
	}

	/**
	 * Stylesheet, the site's settings as CSS custom properties, and the small script that moves
	 * the language switcher into the card.
	 */
	public function enqueue(): void {
		$s = Settings::all();

		wp_enqueue_style( 'mg-login', MG_LOGIN_URL . 'assets/css/login.css', array( 'login' ), MG_LOGIN_VERSION );
		wp_add_inline_style( 'mg-login', Style::inline_css( $s, $this->context( $s ) ) );

		wp_enqueue_script( 'mg-login', MG_LOGIN_URL . 'assets/js/login.js', array(), MG_LOGIN_VERSION, true );
	}

	/**
	 * Resolved images and texts for the stylesheet.
	 *
	 * @param array<string, mixed> $s Settings.
	 * @return array<string, mixed>
	 */
	public function context( array $s ): array {
		$context = array(
			'heading'   => '' !== $s['heading'] ? (string) $s['heading'] : __( 'Log in', 'mg-login' ),
			'logo_url'  => '',
			'logo_w'    => Style::DEFAULT_LOGO_W,
			'logo_h'    => Style::DEFAULT_LOGO_H,
			'bg_url'    => '',
			'bg_webp'   => '',
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

		if ( 'custom' === $s['bg_type'] && $s['bg_image_id'] ) {
			$image = wp_get_attachment_image_src( (int) $s['bg_image_id'], 'full' );
			if ( $image ) {
				$context['bg_url'] = (string) $image[0];
			}
		}
		// The MGraphics photo: the default, and the fallback for a deleted media library image.
		if ( 'none' !== $s['bg_type'] && '' === $context['bg_url'] ) {
			$context['bg_url']  = MG_LOGIN_URL . 'assets/images/login-bg.jpg';
			$context['bg_webp'] = MG_LOGIN_URL . 'assets/images/login-bg.webp';
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
	 * From here on (the login screen only) WordPress' notices carry a live-region role.
	 */
	public function announce_notices(): void {
		add_filter( 'wp_admin_notice_args', array( $this, 'notice_args' ) );
	}

	/**
	 * The two notices WordPress prints above the login forms: errors (#login_error) become
	 * alerts, messages (#login-message: logged out, instructions, confirmations) polite status
	 * messages. Notices nested inside them and attributes set by others are left alone.
	 *
	 * @param mixed $args Arguments of wp_admin_notice().
	 * @return mixed
	 */
	public function notice_args( $args ) {
		if ( ! is_array( $args ) ) {
			return $args;
		}
		$id = isset( $args['id'] ) ? (string) $args['id'] : '';
		if ( 'login_error' !== $id && 'login-message' !== $id ) {
			return $args;
		}
		$attributes         = ( isset( $args['attributes'] ) && is_array( $args['attributes'] ) ) ? $args['attributes'] : array();
		$error              = 'login_error' === $id;
		$args['attributes'] = $attributes + array(
			'role'      => $error ? 'alert' : 'status',
			'aria-live' => $error ? 'assertive' : 'polite',
		);
		return $args;
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
