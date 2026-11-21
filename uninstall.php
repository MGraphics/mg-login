<?php
/**
 * Deleting the plugin removes its settings. Images chosen from the media library stay there.
 *
 * @package MG_Login
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'mg_login_settings' );
