<?php
/**
 * Release check: loads the update checker of an unpacked GitHub release package and runs a real
 * update check (requestInfo) against a fixture of a GitHub release whose notes are Markdown.
 * WordPress is replaced by a few stubs; HTTP answers come from the fixture, nothing goes online.
 *
 * Usage: php tests/release/update-check.php <unpacked>/mg-login
 * Exit code 0 and "OK ..." when the check returned the release, anything else is a failure.
 *
 * @package MG_Login
 */

if ( 2 !== $argc || ! is_file( $argv[1] . '/mg-login.php' ) ) {
	fwrite( STDERR, "Usage: php update-check.php <unpacked package>/mg-login\n" );
	exit( 2 );
}
$mgl_plugin_dir = rtrim( realpath( $argv[1] ), '/' ) . '/';

define( 'ABSPATH', sys_get_temp_dir() . '/' );
define( 'WP_DEBUG', false );
define( 'WP_PLUGIN_DIR', dirname( $mgl_plugin_dir ) );
define( 'WPMU_PLUGIN_DIR', sys_get_temp_dir() . '/mgl-no-mu-plugins' );
define( 'MG_LOGIN_FILE', $mgl_plugin_dir . 'mg-login.php' );
define( 'MG_LOGIN_DIR', $mgl_plugin_dir );
define( 'MG_LOGIN_UPDATE_REPOSITORY', 'https://github.com/MGraphics/mg-login/' );

$GLOBALS['mgl_options']  = array();
$GLOBALS['mgl_requests'] = array();
$GLOBALS['mgl_errors']   = array();

class WP_Error {
	private $code;
	private $message;
	public function __construct( $code = '', $message = '' ) {
		$this->code    = $code;
		$this->message = $message;
	}
	public function get_error_code() {
		return $this->code;
	}
	public function get_error_message() {
		return $this->message;
	}
}

function is_wp_error( $thing ) {
	return $thing instanceof WP_Error;
}
function add_action( ...$args ) {
	return true;
}
function add_filter( ...$args ) {
	return true;
}
function remove_action( ...$args ) {
	return true;
}
function remove_filter( ...$args ) {
	return true;
}
function has_filter( ...$args ) {
	return false;
}
function did_action( $hook ) {
	return 0;
}
function apply_filters( $hook, $value, ...$args ) {
	return $value;
}
function do_action( $hook, ...$args ) {
	if ( 'puc_api_error' === $hook && is_wp_error( $args[0] ) ) {
		$GLOBALS['mgl_errors'][] = $args[0]->get_error_code() . ': ' . $args[0]->get_error_message();
	}
}
function register_deactivation_hook( ...$args ) {
}
function is_admin() {
	return false;
}
function wp_doing_cron() {
	return false;
}
function wp_next_scheduled( ...$args ) {
	return false;
}
function wp_schedule_event( ...$args ) {
	return true;
}
function wp_clear_scheduled_hook( ...$args ) {
	return 0;
}
function get_site_option( $name, $default = false ) {
	return $GLOBALS['mgl_options'][ $name ] ?? $default;
}
function get_option( $name, $default = false ) {
	return get_site_option( $name, $default );
}
function update_site_option( $name, $value ) {
	$GLOBALS['mgl_options'][ $name ] = $value;
	return true;
}
function delete_site_option( $name ) {
	unset( $GLOBALS['mgl_options'][ $name ] );
	return true;
}
function get_site_transient( $name ) {
	return false;
}
function set_site_transient( ...$args ) {
	return true;
}
function delete_site_transient( $name ) {
	return true;
}
function trailingslashit( $value ) {
	return rtrim( $value, '/\\' ) . '/';
}
function untrailingslashit( $value ) {
	return rtrim( $value, '/\\' );
}
function wp_normalize_path( $path ) {
	return preg_replace( '|/+|', '/', str_replace( '\\', '/', $path ) );
}
function plugin_basename( $file ) {
	$file = wp_normalize_path( $file );
	$dir  = wp_normalize_path( WP_PLUGIN_DIR );
	return ltrim( str_replace( $dir, '', $file ), '/' );
}
function plugins_url( $path = '', $plugin = '' ) {
	return 'https://example.test/wp-content/plugins/' . basename( dirname( $plugin ) ) . '/' . ltrim( $path, '/' );
}
function wp_parse_url( $url, $component = -1 ) {
	return parse_url( $url, $component );
}
function add_query_arg( $args, $url ) {
	return $url . ( false === strpos( $url, '?' ) ? '?' : '&' ) . http_build_query( $args );
}
function balanceTags( $text ) {
	return $text;
}
function wp_kses( $text, $allowed ) {
	return strip_tags( $text, array_keys( $allowed ) );
}
function esc_html( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES );
}
function esc_attr( $text ) {
	return esc_html( $text );
}
function esc_url( $url ) {
	return (string) $url;
}
function __( $text, $domain = 'default' ) {
	return $text;
}
function _x( $text, $context, $domain = 'default' ) {
	return $text;
}
function get_locale() {
	return 'en_US';
}
function get_user_locale() {
	return 'en_US';
}
function current_user_can( ...$args ) {
	return false;
}
function wp_json_encode( $data ) {
	return json_encode( $data );
}
function _cleanup_header_comment( $str ) {
	return trim( preg_replace( '/\s*(?:\*\/|\?>).*/', '', $str ) );
}
function get_file_data( $file, $headers ) {
	$data = file_get_contents( $file, false, null, 0, 8192 );
	$out  = array();
	foreach ( $headers as $field => $regex ) {
		$out[ $field ] = preg_match( '/^(?:[ \t]*<\?php)?[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $data, $m )
			? trim( preg_replace( '/\s*(?:\*\/|\?>).*/', '', $m[1] ) )
			: '';
	}
	return $out;
}
function get_plugin_data( $file, $markup = true, $translate = true ) {
	$data = get_file_data(
		$file,
		array(
			'Name'        => 'Plugin Name',
			'PluginURI'   => 'Plugin URI',
			'Version'     => 'Version',
			'Description' => 'Description',
			'Author'      => 'Author',
			'AuthorURI'   => 'Author URI',
			'TextDomain'  => 'Text Domain',
			'DomainPath'  => 'Domain Path',
			'Network'     => 'Network',
			'RequiresWP'  => 'Requires at least',
			'RequiresPHP' => 'Requires PHP',
			'UpdateURI'   => 'Update URI',
		)
	);
	$data['Network'] = false;
	$data['Title']   = $data['Name'];
	return $data;
}

// GitHub API fixture: release v9.9.9 with Markdown notes and the release asset mg-login-9.9.9.zip.
function mgl_fixture_release() {
	return array(
		'tag_name'     => 'v9.9.9',
		'name'         => 'MG Login 9.9.9',
		'body'         => "## Changes\n\n* **Fixed** the update check.\n* See `readme.txt`.",
		'created_at'   => '2026-11-21T12:00:00Z',
		'published_at' => '2026-11-21T12:00:00Z',
		'zipball_url'  => 'https://api.github.com/repos/MGraphics/mg-login/zipball/v9.9.9',
		'draft'        => false,
		'prerelease'   => false,
		'assets'       => array(
			array(
				'name'                 => 'mg-login-9.9.9.zip',
				'url'                  => 'https://api.github.com/repos/MGraphics/mg-login/releases/assets/1',
				'browser_download_url' => 'https://github.com/MGraphics/mg-login/releases/download/v9.9.9/mg-login-9.9.9.zip',
				'download_count'       => 3,
			),
		),
	);
}

function wp_remote_get( $url, $options = array() ) {
	$GLOBALS['mgl_requests'][] = $url;
	$path                      = (string) parse_url( $url, PHP_URL_PATH );
	$body                      = null;
	if ( '/repos/MGraphics/mg-login/releases/latest' === $path ) {
		$body = mgl_fixture_release();
	} elseif ( '/repos/MGraphics/mg-login/releases' === $path ) {
		$body = array( mgl_fixture_release() );
	} elseif ( preg_match( '#^/repos/MGraphics/mg-login/contents/(.+)$#', $path, $m ) ) {
		$file = $GLOBALS['mgl_plugin_dir'] . rawurldecode( $m[1] );
		if ( is_file( $file ) ) {
			$content = file_get_contents( $file );
			if ( 'mg-login.php' === $m[1] ) {
				$content = preg_replace( '/^(\s*\*\s*Version:\s*).*$/m', '${1}9.9.9', $content, 1 );
			}
			$body = array(
				'type'     => 'file',
				'encoding' => 'base64',
				'content'  => base64_encode( $content ),
			);
		}
	}
	if ( null === $body ) {
		return array(
			'response' => array( 'code' => 404, 'message' => 'Not Found' ),
			'headers'  => array(),
			'body'     => '{"message":"Not Found"}',
		);
	}
	return array(
		'response' => array( 'code' => 200, 'message' => 'OK' ),
		'headers'  => array(),
		'body'     => json_encode( $body ),
	);
}
function wp_remote_retrieve_response_code( $response ) {
	return is_array( $response ) ? $response['response']['code'] : '';
}
function wp_remote_retrieve_response_message( $response ) {
	return is_array( $response ) ? $response['response']['message'] : '';
}
function wp_remote_retrieve_body( $response ) {
	return is_array( $response ) ? $response['body'] : '';
}
function wp_remote_retrieve_headers( $response ) {
	return is_array( $response ) ? $response['headers'] : array();
}

$GLOBALS['mgl_plugin_dir'] = $mgl_plugin_dir;

// The plugin's own wiring, exactly as shipped.
require $mgl_plugin_dir . 'includes/class-updater.php';
( new MG_Login\Updater() )->register();

$factory = '\YahnisElsts\PluginUpdateChecker\v5\PucFactory';
if ( ! class_exists( $factory ) ) {
	fwrite( STDERR, "FAIL: the package has no plugin-update-checker.\n" );
	exit( 1 );
}
// Updater::register() keeps no reference, so build the same checker again for the check.
$checker = $factory::buildUpdateChecker( MG_LOGIN_UPDATE_REPOSITORY, MG_LOGIN_FILE, 'mg-login' );
$api     = $checker->getVcsApi();
$api->enableReleaseAssets( '/^mg-login-[0-9][0-9.]*\.zip$/i', $api::REQUIRE_RELEASE_ASSETS );

// The release notes go through Parsedown (lib/plugin-update-checker/vendor) ...
$release = $api->chooseReference( 'main' );
// ... and a full check also parses readme.txt with the bundled readme parser.
$info = $checker->requestInfo();

$problems = array();
if ( null === $release ) {
	$problems[] = 'no release found';
} elseif ( false === strpos( (string) $release->changelog, '<strong>Fixed</strong>' ) ) {
	$problems[] = 'release notes not rendered from Markdown: ' . var_export( $release->changelog, true );
}
if ( null === $info ) {
	$problems[] = 'requestInfo() returned no update information';
} else {
	if ( '9.9.9' !== $info->version ) {
		$problems[] = 'version ' . var_export( $info->version, true );
	}
	if ( 'https://github.com/MGraphics/mg-login/releases/download/v9.9.9/mg-login-9.9.9.zip' !== $info->download_url ) {
		$problems[] = 'download_url ' . var_export( $info->download_url, true );
	}
	if ( empty( $info->sections['changelog'] ) ) {
		$problems[] = 'no changelog';
	}
}
if ( $problems ) {
	fwrite( STDERR, 'FAIL: ' . implode( '; ', array_merge( $problems, $GLOBALS['mgl_errors'] ) ) . "\n" );
	exit( 1 );
}
echo "OK update check: version {$info->version}, {$info->download_url}, Markdown release notes rendered\n";
