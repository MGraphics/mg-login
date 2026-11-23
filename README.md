# MG Login

WordPress plugin by [MGraphics](https://mgraphics.com.pl) for a branded login screen: a white card on a full-screen photo (the bundled MGraphics photo by default, or any image from the media library), the logo above it, plain fields with their labels above them, messages as plain text set off by a thin line, "Forgot your password?" under the password, a solid accent button, and the WordPress language switcher moved into the card directly under the form. The defaults give the MGraphics look; every part can be changed for a client site.

Polski opis, FAQ i lista zmian: [readme.txt](readme.txt). The plugin is fully translated into Polish.

- License: GPL-2.0-or-later ([LICENSE](LICENSE)); Open Sans: SIL OFL 1.1 ([assets/fonts/OFL.txt](assets/fonts/OFL.txt)); plugin-update-checker: MIT
- Requires WordPress 6.4+, PHP 7.4+; tested with WordPress 7.1 on PHP 8.4
- Author: MGraphics, https://mgraphics.com.pl

## Install

Download `mg-login-<version>.zip` from the latest release at https://github.com/MGraphics/mg-login/releases (the release asset, not GitHub's "Source code" archive) and upload it in **Plugins → Add New Plugin → Upload Plugin**. Later versions show up as normal WordPress updates (see *Updates* below).

## Settings (Settings → MG Login)

| Setting | Default |
|---|---|
| Logo: MGraphics / media library image / none | MGraphics logo |
| Logo width (px, 40–400; height follows the image, ~¾ on phones) | 306 |
| Logo link / logo title | home page / site title |
| Heading above the form | "Log in" in the screen's language ("Logowanie") |
| Card position on large screens: left / centre / right | left |
| Language switcher under the form | on |
| Accent / accent on hover | `#2563eb` / `#1d4ed8` |
| Background: MGraphics (the bundled photo) / media library image / colour only | MGraphics |
| Background colour | `#ece8e3` |
| Light shading towards the right | on |
| Additional CSS (login screen only) | – |

"Restore the defaults" at the bottom of the page deletes the stored settings.

The defaults are the MGraphics layout as on mgraphics.com.pl: the bundled photo, **Light shading** on, **Card position → left**, logo above the card.

## How it works

- Hooks only into the login screen (`login_enqueue_scripts`, `login_headerurl`, `login_headertext`, `login_body_class`, `login_form`, `login_init`, `login_display_language_dropdown`) and, in the admin area, its settings page. Public pages are byte-for-byte the same with the plugin on or off.
- `assets/css/login.css`: the page, card and logo keep the selectors of the original theme stylesheet; fields, buttons and messages are scoped to `#login` / `#login > form`, so they win over WordPress' own login CSS on every screen. The site's settings arrive as CSS custom properties in an inline style.
- Fields have their WordPress labels above them and nothing inside (no icons, no placeholders), on every screen. Messages are plain text with a thin line under them; `wp_admin_notice_args` (added on `login_init`, so on the login screen only) gives WordPress' two notices a live-region role: `#login_error` is `role="alert"`, `#login-message` is `role="status"`.
- `assets/js/login.js` is printed in the login footer right after WordPress' language switcher and moves it into the card before the first paint (and gives the same roles to notices printed by other plugins in the old markup). Without JavaScript the switcher stays in a white box under the card.
- The "Forgot your password?" link uses `wp_lostpassword_url()`, so it follows a private login address set by MG Stealth or WPS Hide Login.
- No external requests: Open Sans (latin + latin-ext, variable woff2) and the default logo and background photo are bundled. The photo is `assets/images/login-bg.webp` (offered through `image-set()`) with `login-bg.jpg` as the fallback; both belong to MGraphics and ship under the plugin's licence.

## Updates (GitHub Releases)

The GitHub build bundles [plugin-update-checker](https://github.com/YahnisElsts/plugin-update-checker) (MIT) and asks the Releases of the public repository https://github.com/MGraphics/mg-login for new versions. It installs only the release asset named `mg-login-<version>.zip`, never the source archive, and needs no token. The repository address is the constant `MG_LOGIN_UPDATE_REPOSITORY` in `mg-login.php` (it can also be defined in `wp-config.php`); `MG_LOGIN_DISABLE_UPDATES` switches the checks off. The WordPress.org build has neither the library nor the `Update URI` header.

### Releasing a version

1. Bump the version in three places: the `Version:` header and `MG_LOGIN_VERSION` in `mg-login.php`, and `Stable tag:` in `readme.txt`; add a changelog entry to `readme.txt`.
2. `bin/i18n.sh` when strings changed; `vendor/bin/phpunit`.
3. Commit, tag `v<version>` and push the tag: `git tag v1.0.1 && git push origin main v1.0.1`.
4. The tag workflow (`.github/workflows/release.yml`) runs the tests, builds both packages with `bin/build.sh` and attaches `mg-login-<version>.zip` and `mg-login-<version>-wporg.zip` to the GitHub Release `v<version>` (creating it when missing). A release can also be made by hand: `bin/build.sh`, then `gh release create v1.0.1 build/mg-login-1.0.1.zip build/mg-login-1.0.1-wporg.zip`.
5. Sites with the plugin see the update within 12 hours (or at once via **Dashboard → Updates → Check again**).

The repository must stay **public**, otherwise sites cannot read the releases without a token.

## Development

```sh
bin/dev-setup.sh                         # WordPress 7.1 (pl_PL) + MariaDB in Docker, http://127.0.0.1:8486
composer install && vendor/bin/phpunit   # unit tests
bin/build.sh                             # build/mg-login-<v>.zip and build/mg-login-<v>-wporg.zip, then an update check on the GitHub zip (tests/release/update-check.php)
bin/i18n.sh                              # .pot, Polish .po/.mo
```

Admin credentials of the dev site are written to `.local/admin.txt` (not in git).
