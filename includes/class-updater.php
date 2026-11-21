<?php
/**
 * Updates from the Releases of the public GitLab project (plugin-update-checker, MIT).
 * The build for WordPress.org leaves the library out, and then nothing here runs.
 *
 * @package MG_Login
 */

namespace MG_Login;

defined( 'ABSPATH' ) || exit;

/**
 * Update checker.
 */
final class Updater {

	/**
	 * Starts the update checker when the library is bundled.
	 */
	public function register(): void {
		$library = MG_LOGIN_DIR . 'lib/plugin-update-checker/plugin-update-checker.php';
		if ( ! is_readable( $library ) || ( defined( 'MG_LOGIN_DISABLE_UPDATES' ) && MG_LOGIN_DISABLE_UPDATES ) ) {
			return;
		}
		require_once $library;
		if ( ! class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			return;
		}
		$checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			MG_LOGIN_UPDATE_REPOSITORY,
			MG_LOGIN_FILE,
			'mg-login'
		);
		$api     = $checker->getVcsApi();
		if ( $api && method_exists( $api, 'enableReleaseAssets' ) ) {
			// The release package built by CI (mg-login-1.2.3.zip), not GitLab's source archive.
			$api->enableReleaseAssets( '/mg-login-[0-9][0-9.]*\.zip($|[?&#])/i' );
		}
	}
}
