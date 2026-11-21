<?php
/**
 * Settings → MG Login.
 *
 * @package MG_Login
 */

namespace MG_Login;

defined( 'ABSPATH' ) || exit;

/**
 * Settings page.
 */
final class Admin {

	const PAGE  = 'mg-login';
	const GROUP = 'mg_login';

	/**
	 * Hook suffix of the settings page.
	 *
	 * @var string
	 */
	private $hook = '';

	/**
	 * Hooks.
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_post_mg_login_reset', array( $this, 'reset' ) );
		add_filter( 'plugin_action_links_' . MG_LOGIN_BASENAME, array( $this, 'action_links' ) );
	}

	/**
	 * Menu entry.
	 */
	public function menu(): void {
		$this->hook = (string) add_options_page(
			__( 'MG Login', 'mg-login' ),
			__( 'MG Login', 'mg-login' ),
			'manage_options',
			self::PAGE,
			array( $this, 'render' )
		);
	}

	/**
	 * The option.
	 */
	public function register_setting(): void {
		register_setting(
			self::GROUP,
			Settings::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( Settings::class, 'sanitize' ),
				'default'           => Settings::defaults(),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * "Settings" link on the Plugins screen.
	 *
	 * @param array<string> $links Links.
	 * @return array<string>
	 */
	public function action_links( $links ): array {
		$links = (array) $links;
		array_unshift(
			$links,
			sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-general.php?page=' . self::PAGE ) ), esc_html__( 'Settings', 'mg-login' ) )
		);
		return $links;
	}

	/**
	 * Media library, colour pickers and the page script.
	 *
	 * @param string $hook Current admin page.
	 */
	public function enqueue( $hook ): void {
		if ( $hook !== $this->hook ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'mg-login-admin', MG_LOGIN_URL . 'assets/css/admin.css', array(), MG_LOGIN_VERSION );
		wp_enqueue_script( 'mg-login-admin', MG_LOGIN_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), MG_LOGIN_VERSION, true );
		wp_localize_script(
			'mg-login-admin',
			'mgLoginAdmin',
			array(
				'chooseLogo'       => __( 'Choose the logo', 'mg-login' ),
				'chooseBackground' => __( 'Choose the background image', 'mg-login' ),
				'use'              => __( 'Use this image', 'mg-login' ),
			)
		);
	}

	/**
	 * Restores the defaults.
	 */
	public function reset(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'mg-login' ), 403 );
		}
		check_admin_referer( 'mg_login_reset' );
		delete_option( Settings::OPTION );
		wp_safe_redirect( add_query_arg( array( 'page' => self::PAGE, 'mg-login-reset' => '1' ), admin_url( 'options-general.php' ) ) );
		exit;
	}

	/**
	 * The page.
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$s    = Settings::all();
		$name = Settings::OPTION;
		$d    = Settings::defaults();
		?>
		<div class="wrap mg-login-admin">
			<h1><?php esc_html_e( 'MG Login', 'mg-login' ); ?></h1>
			<p class="mg-login-lead">
				<?php esc_html_e( 'The look of the login screen. Sign-in itself, the login address and the e-mails are not changed. Leave a field empty to use the automatic value.', 'mg-login' ); ?>
				<a class="button button-secondary" href="<?php echo esc_url( wp_login_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open the login screen', 'mg-login' ); ?></a>
			</p>
			<?php
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only notice after the nonce-checked reset.
			if ( isset( $_GET['mg-login-reset'] ) ) {
				printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html__( 'The default settings have been restored.', 'mg-login' ) );
			}
			?>
			<form method="post" action="options.php">
				<?php settings_fields( self::GROUP ); ?>

				<h2><?php esc_html_e( 'Logo', 'mg-login' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Logo', 'mg-login' ); ?></th>
						<td>
							<fieldset class="mg-login-choice" data-target="logo">
								<?php
								$this->radio( $name . '[logo_type]', 'default', $s['logo_type'], __( 'MGraphics logo (default)', 'mg-login' ) );
								$this->radio( $name . '[logo_type]', 'custom', $s['logo_type'], __( 'Image from the media library', 'mg-login' ) );
								$this->radio( $name . '[logo_type]', 'none', $s['logo_type'], __( 'No logo', 'mg-login' ) );
								?>
							</fieldset>
							<?php $this->media_field( $name . '[logo_id]', (int) $s['logo_id'], 'logo' ); ?>
							<p class="description"><?php esc_html_e( 'A PNG with a transparent background works best; the logo is shown above the card.', 'mg-login' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mg-login-logo-width"><?php esc_html_e( 'Logo width', 'mg-login' ); ?></label></th>
						<td>
							<input type="number" id="mg-login-logo-width" name="<?php echo esc_attr( $name ); ?>[logo_width]" value="<?php echo esc_attr( (string) $s['logo_width'] ); ?>" min="<?php echo esc_attr( (string) Settings::LOGO_WIDTH_MIN ); ?>" max="<?php echo esc_attr( (string) Settings::LOGO_WIDTH_MAX ); ?>" step="1" class="small-text"> px
							<p class="description">
								<?php
								/* translators: %d: default width in pixels. */
								echo esc_html( sprintf( __( 'Default: %d px. The height follows the image; on phones the logo is about a quarter smaller.', 'mg-login' ), (int) $d['logo_width'] ) );
								?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mg-login-logo-url"><?php esc_html_e( 'Logo link', 'mg-login' ); ?></label></th>
						<td>
							<input type="url" id="mg-login-logo-url" name="<?php echo esc_attr( $name ); ?>[logo_url]" value="<?php echo esc_attr( (string) $s['logo_url'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>">
							<p class="description"><?php esc_html_e( 'Empty: the home page of this site.', 'mg-login' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mg-login-logo-title"><?php esc_html_e( 'Logo title', 'mg-login' ); ?></label></th>
						<td>
							<input type="text" id="mg-login-logo-title" name="<?php echo esc_attr( $name ); ?>[logo_title]" value="<?php echo esc_attr( (string) $s['logo_title'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
							<p class="description"><?php esc_html_e( 'Text of the logo link, read by screen readers. Empty: the site title.', 'mg-login' ); ?></p>
						</td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Card', 'mg-login' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="mg-login-heading"><?php esc_html_e( 'Heading', 'mg-login' ); ?></label></th>
						<td>
							<input type="text" id="mg-login-heading" name="<?php echo esc_attr( $name ); ?>[heading]" value="<?php echo esc_attr( (string) $s['heading'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr__( 'Log in', 'mg-login' ); ?>">
							<p class="description"><?php esc_html_e( 'Shown above the login form. Empty: "Log in" in the language of the login screen.', 'mg-login' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Card position', 'mg-login' ); ?></th>
						<td>
							<fieldset>
								<?php
								$this->radio( $name . '[card_position]', 'left', $s['card_position'], __( 'Left (default)', 'mg-login' ) );
								$this->radio( $name . '[card_position]', 'center', $s['card_position'], __( 'Centre', 'mg-login' ) );
								$this->radio( $name . '[card_position]', 'right', $s['card_position'], __( 'Right', 'mg-login' ) );
								?>
							</fieldset>
							<p class="description"><?php esc_html_e( 'On large screens. On tablets and phones the card is always centred.', 'mg-login' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Language switcher', 'mg-login' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( $name ); ?>[language_switcher]" value="1" <?php checked( (bool) $s['language_switcher'] ); ?>>
								<?php esc_html_e( 'Show the language switcher under the form', 'mg-login' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'WordPress shows it only when at least one language other than English is installed.', 'mg-login' ); ?></p>
						</td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Colours', 'mg-login' ); ?></h2>
				<table class="form-table" role="presentation">
					<?php
					$this->color_row( $name, 'accent', (string) $s['accent'], (string) $d['accent'], __( 'Accent', 'mg-login' ), __( 'Button, links, field focus and the tick of "Remember Me".', 'mg-login' ) );
					$this->color_row( $name, 'accent_hover', (string) $s['accent_hover'], (string) $d['accent_hover'], __( 'Accent on hover', 'mg-login' ), __( 'Button and links under the mouse; usually a slightly darker accent.', 'mg-login' ) );
					?>
				</table>

				<h2><?php esc_html_e( 'Background', 'mg-login' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Background image', 'mg-login' ); ?></th>
						<td>
							<fieldset class="mg-login-choice" data-target="bg">
								<?php
								$this->radio( $name . '[bg_type]', 'default', $s['bg_type'], __( 'MGraphics photo (default)', 'mg-login' ) );
								$this->radio( $name . '[bg_type]', 'custom', $s['bg_type'], __( 'Image from the media library', 'mg-login' ) );
								$this->radio( $name . '[bg_type]', 'none', $s['bg_type'], __( 'No image, colour only', 'mg-login' ) );
								?>
							</fieldset>
							<?php $this->media_field( $name . '[bg_image_id]', (int) $s['bg_image_id'], 'bg' ); ?>
							<p class="description"><?php esc_html_e( 'The image covers the whole screen; about 1920 × 1080 px is enough.', 'mg-login' ); ?></p>
						</td>
					</tr>
					<?php $this->color_row( $name, 'bg_color', (string) $s['bg_color'], (string) $d['bg_color'], __( 'Background colour', 'mg-login' ), __( 'Shown without an image and while the image loads.', 'mg-login' ) ); ?>
					<tr>
						<th scope="row"><?php esc_html_e( 'Shading', 'mg-login' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( $name ); ?>[bg_overlay]" value="1" <?php checked( (bool) $s['bg_overlay'] ); ?>>
								<?php esc_html_e( 'Darken the background slightly towards the right', 'mg-login' ); ?>
							</label>
						</td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Custom CSS', 'mg-login' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="mg-login-css"><?php esc_html_e( 'Additional CSS', 'mg-login' ); ?></label></th>
						<td>
							<textarea id="mg-login-css" name="<?php echo esc_attr( $name ); ?>[custom_css]" rows="8" class="large-text code" spellcheck="false"><?php echo esc_textarea( (string) $s['custom_css'] ); ?></textarea>
							<p class="description">
								<?php
								echo wp_kses(
									__( 'Loaded only on the login screen, after the plugin\'s styles. Example: <code>body.login #login { border-radius: 0; }</code>', 'mg-login' ),
									array( 'code' => array() )
								);
								?>
							</p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mg-login-reset">
				<input type="hidden" name="action" value="mg_login_reset">
				<?php wp_nonce_field( 'mg_login_reset' ); ?>
				<button type="submit" class="button-link button-link-delete mg-login-reset-button" data-confirm="<?php echo esc_attr__( 'Restore all MG Login settings to the defaults?', 'mg-login' ); ?>"><?php esc_html_e( 'Restore the defaults', 'mg-login' ); ?></button>
			</form>
		</div>
		<?php
	}

	/**
	 * A radio button.
	 *
	 * @param string $name    Field name.
	 * @param string $value   Value.
	 * @param string $current Current value.
	 * @param string $label   Label.
	 */
	private function radio( string $name, string $value, string $current, string $label ): void {
		printf(
			'<label class="mg-login-radio"><input type="radio" name="%s" value="%s" %s> %s</label>',
			esc_attr( $name ),
			esc_attr( $value ),
			checked( $current, $value, false ),
			esc_html( $label )
		);
	}

	/**
	 * Image picker (media library).
	 *
	 * @param string $name Field name.
	 * @param int    $id   Attachment ID.
	 * @param string $kind logo | bg.
	 */
	private function media_field( string $name, int $id, string $kind ): void {
		$preview = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
		?>
		<div class="mg-login-media" data-kind="<?php echo esc_attr( $kind ); ?>">
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $id ); ?>" class="mg-login-media-id">
			<div class="mg-login-media-preview<?php echo $preview ? '' : ' is-empty'; ?>">
				<?php if ( $preview ) : ?>
					<img src="<?php echo esc_url( $preview ); ?>" alt="">
				<?php endif; ?>
			</div>
			<button type="button" class="button mg-login-media-choose"><?php esc_html_e( 'Choose image', 'mg-login' ); ?></button>
			<button type="button" class="button-link mg-login-media-remove"<?php echo $id ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove', 'mg-login' ); ?></button>
		</div>
		<?php
	}

	/**
	 * A colour row.
	 *
	 * @param string $name        Option name.
	 * @param string $key         Setting key.
	 * @param string $value       Value.
	 * @param string $default     Default value.
	 * @param string $label       Label.
	 * @param string $description Description.
	 */
	private function color_row( string $name, string $key, string $value, string $default, string $label, string $description ): void {
		$id = 'mg-login-' . str_replace( '_', '-', $key );
		?>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td>
				<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name . '[' . $key . ']' ); ?>" value="<?php echo esc_attr( $value ); ?>" class="mg-login-color" data-default-color="<?php echo esc_attr( $default ); ?>">
				<p class="description"><?php echo esc_html( $description ); ?></p>
			</td>
		</tr>
		<?php
	}
}
