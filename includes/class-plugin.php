<?php
/**
 * Bootstrap.
 *
 * @package MG_Login
 */

namespace MG_Login;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin object.
 */
final class Plugin {

	/**
	 * Instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * The instance.
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Registers the hooks. Public pages get nothing: the login screen hooks only run on the
	 * login screen (whatever its address) and the settings page only in the admin area.
	 */
	public function boot(): void {
		add_action( 'init', array( $this, 'load_textdomain' ), 0 );

		( new Login_Screen() )->register();

		if ( is_admin() ) {
			( new Admin() )->register();
		}

		if ( class_exists( __NAMESPACE__ . '\\Updater' ) && ( is_admin() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) ) ) {
			( new Updater() )->register();
		}
	}

	/**
	 * Translations bundled with the plugin.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain( 'mg-login', false, dirname( MG_LOGIN_BASENAME ) . '/languages' ); // phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- Needed outside WordPress.org.
	}
}
