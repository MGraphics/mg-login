# MG Login

WordPress plugin by [MGraphics](https://mgraphics.com.pl) for a branded login screen: a white card on a full-screen photo, the logo above it, field icons and placeholders, "Forgot your password?" under the password, a solid accent button, and the WordPress language switcher moved into the card directly under the form. The defaults reproduce the login screen of mgraphics.com.pl; every part can be changed for a client site.

Polski opis, FAQ i lista zmian: [readme.txt](readme.txt). The plugin is fully translated into Polish.

- License: GPL-2.0-or-later ([LICENSE](LICENSE)); Open Sans: SIL OFL 1.1 ([assets/fonts/OFL.txt](assets/fonts/OFL.txt)); plugin-update-checker: MIT
- Requires WordPress 6.4+, PHP 7.4+; tested with WordPress 7.1 on PHP 8.4
- Author: MGraphics, https://mgraphics.com.pl

## Install

Download `mg-login-<version>.zip` from the latest [release](../../releases) and upload it in **Plugins → Add New → Upload Plugin**. Later versions show up as normal WordPress updates (see *Updates* below).

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
| Background: MGraphics photo / media library image / colour only | MGraphics photo |
| Background colour | `#ece8e3` |
| Light shading towards the right | on |
| Additional CSS (login screen only) | – |

"Restore the defaults" at the bottom of the page deletes the stored settings.

## How it works

- Hooks only into the login screen (`login_enqueue_scripts`, `login_headerurl`, `login_headertext`, `login_body_class`, `login_form`, `login_display_language_dropdown`) and, in the admin area, its settings page. Public pages are byte-for-byte the same with the plugin on or off.
- `assets/css/login.css` uses the selectors of the original theme stylesheet, so the cascade against WordPress' own login CSS is unchanged; the site's settings arrive as CSS custom properties in an inline style.
- `assets/js/login.js` is printed in the login footer right after WordPress' language switcher and moves it into the card before the first paint (and adds the placeholders). Without JavaScript the switcher stays in a white box under the card.
- The "Forgot your password?" link uses `wp_lostpassword_url()`, so it follows a private login address set by MG Stealth or WPS Hide Login.
- No external requests: Open Sans (latin + latin-ext, variable woff2) and the default images are bundled.

## Updates (GitLab Releases)

The GitLab build bundles [plugin-update-checker](https://github.com/YahnisElsts/plugin-update-checker) (MIT) and asks this project's Releases for new versions. It installs the release asset named `mg-login-<version>.zip`. The project address is the constant `MG_LOGIN_UPDATE_REPOSITORY` in `mg-login.php` (placeholder `https://gitlab.com/mgraphics/mg-login/`; it can also be defined in `wp-config.php`). `MG_LOGIN_DISABLE_UPDATES` switches the checks off. The WordPress.org build has neither the library nor the `Update URI` header.

### Releasing a version

1. Bump the version in three places: the `Version:` header and `MG_LOGIN_VERSION` in `mg-login.php`, and `Stable tag:` in `readme.txt`; add a changelog entry to `readme.txt`.
2. `bin/i18n.sh` when strings changed; `vendor/bin/phpunit`.
3. Commit, tag `v<version>` and push the tag: `git tag v1.0.1 && git push origin main v1.0.1`.
4. The tag pipeline (`.gitlab-ci.yml`) runs the tests, builds both packages with `bin/build.sh`, uploads them to the project's Generic Package Registry and creates the GitLab Release `v<version>` with `mg-login-<version>.zip` as a release asset.
5. Sites with the plugin see the update within 12 hours (or at once via **Dashboard → Updates → Check again**).

The project must be **public**, otherwise sites cannot read the releases without a token.

## Development

```sh
bin/dev-setup.sh                         # WordPress 7.1 (pl_PL) + MariaDB in Docker, http://127.0.0.1:8486
composer install && vendor/bin/phpunit   # unit tests
bin/build.sh                             # build/mg-login-<v>.zip and build/mg-login-<v>-wporg.zip
bin/i18n.sh                              # .pot, Polish .po/.mo
```

Admin credentials of the dev site are written to `.local/admin.txt` (not in git).
