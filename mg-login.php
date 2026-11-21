<?php
/**
 * Plugin Name:       MG Login
 * Plugin URI:        https://mgraphics.com.pl/mg-login/
 * Description:       A branded login screen: your logo, colours and background photo on a clean card, with the language switcher placed under the form. Styles only the login screen; nothing is added to the public pages.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            MGraphics
 * Author URI:        https://mgraphics.com.pl
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mg-login
 * Domain Path:       /languages
 * Update URI:        https://gitlab.com/mgraphics/mg-login
 *
 * @package MG_Login
 */

defined( 'ABSPATH' ) || exit;

define( 'MG_LOGIN_VERSION', '1.0.0' );
define( 'MG_LOGIN_FILE', __FILE__ );
define( 'MG_LOGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MG_LOGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MG_LOGIN_BASENAME', plugin_basename( __FILE__ ) );

// gitlab-build:start (removed from the WordPress.org build by bin/build.sh)
/*
 * Public GitLab project whose Releases deliver updates of this plugin. Can be overridden in
 * wp-config.php; define MG_LOGIN_DISABLE_UPDATES to switch update checks off.
 */
if ( ! defined( 'MG_LOGIN_UPDATE_REPOSITORY' ) ) {
	define( 'MG_LOGIN_UPDATE_REPOSITORY', 'https://gitlab.com/mgraphics/mg-login/' );
}
require_once MG_LOGIN_DIR . 'includes/class-updater.php';
// gitlab-build:end

require_once MG_LOGIN_DIR . 'includes/class-settings.php';
require_once MG_LOGIN_DIR . 'includes/class-style.php';
require_once MG_LOGIN_DIR . 'includes/class-login-screen.php';
require_once MG_LOGIN_DIR . 'includes/class-admin.php';
require_once MG_LOGIN_DIR . 'includes/class-plugin.php';

MG_Login\Plugin::instance()->boot();
