=== MG Login ===
Contributors: mgraphics
Tags: login, custom login, login page, branding, login logo
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A branded login screen: your logo, colours and background on a clean card, with the language switcher under the form.

== Description ==

MG Login gives the WordPress login screen a clean, branded look: a white card on a soft full-screen background, the logo above it, plain fields with their labels above them, a "Forgot your password?" link under the password, a solid accent button and the language switcher moved from the bottom of the page into the card, directly under the form. Messages (logged out, errors, instructions, confirmations) are plain text set off from the form by a thin line, and are announced to screen readers.

Out of the box it shows the MGraphics look. For a client site change, in **Settings → MG Login**:

* the logo (from the media library, or none), its width, link and title;
* the heading above the form;
* the accent colour and its hover shade;
* the background: the MGraphics design (pure CSS, the default), any image from the media library, or a plain colour; the background colour and the light shading;
* the card position on large screens (left, centre or right);
* whether to show the language switcher;
* additional CSS for the login screen only.

Every screen of the login page is styled: log in, lost password, new password, registration, "check your e-mail" confirmations, errors and notices, the administration e-mail check, on desktops and phones.

The MGraphics layout with a photo is three settings: Background → Image from the media library (the photo), Shading on, Card position → Left.

**Only the look changes.** Signing in, password reset, registration, redirects, messages and e-mails are WordPress' own. The plugin works on the login screen wherever it is: `wp-login.php`, or a private address set by MG Stealth or WPS Hide Login.

**Nothing on the public pages.** The plugin prints nothing, adds no styles or scripts and sends no headers on the front end of the site. It makes no external requests: the font (Open Sans) and the logo are bundled, and the default background is drawn in CSS.

= Credits =

* Open Sans by The Open Sans Project Authors, SIL Open Font License 1.1 (assets/fonts/OFL.txt).
* plugin-update-checker by Yahnis Elsts, MIT License (GitHub build only).

= Po polsku =

MG Login nadaje ekranowi logowania WordPressa firmowy wygląd: biała karta na delikatnym tle (domyślnie wzór CSS albo dowolne zdjęcie z biblioteki mediów), logo nad kartą, proste pola z etykietami nad nimi, komunikaty jako zwykły tekst oddzielony cienką linią (bez ramek i kolorowych pasków), link „Zapomniałeś hasła?” pod hasłem, wyraźny przycisk w kolorze akcentu oraz wybór języka przeniesiony z dołu strony do karty, bezpośrednio pod formularz. Domyślnie wygląda jak logowanie MGraphics; w **Ustawienia → MG Login** można dla klienta zmienić logo (z biblioteki mediów), jego szerokość, link i tytuł, nagłówek, kolory akcentu, obraz lub kolor tła, przyciemnienie, położenie karty, wybór języka i dodać własny CSS. Zmienia się tylko wygląd – logowanie, resetowanie hasła, rejestracja i e-maile pozostają w rękach WordPressa. Działa razem z MG Stealth i WPS Hide Login. Na publicznych stronach nic nie dodaje; z zewnętrznym serwerem łączy się tylko wersja z GitHuba, sprawdzając aktualizacje. Wtyczka jest w pełni przetłumaczona na polski.

== Installation ==

1. Download `mg-login-<version>.zip` from https://github.com/MGraphics/mg-login/releases, upload it and activate it (Plugins → Add New Plugin → Upload Plugin). Updates then arrive as normal WordPress updates.
2. Open the login screen: it already has the new look.
3. For a client site, set the logo, colours and background in Settings → MG Login and use "Open the login screen" to check them.

== Frequently Asked Questions ==

= Does it change the login address? =

No. Use MG Stealth or WPS Hide Login for a private login address; MG Login styles the login screen at whatever address it has.

= The language switcher is missing. =

WordPress shows it only when at least one language other than English is installed (Settings → General → Site Language installs one), and only when "Show the language switcher under the form" is ticked.

= Does it work without JavaScript? =

Yes. Without JavaScript the language switcher stays in a separate white box under the card; everything else is CSS.

= My theme also styles the login screen. =

Then both sets of styles load. Switch off the theme's login styling (or use MG Login's additional CSS to adjust) so there is one look.

= Does the plugin send data anywhere? =

No. The GitHub build looks for new versions in the Releases of the public repository https://github.com/MGraphics/mg-login (no token, no data about the site beyond a normal request); the WordPress.org build has no update code of its own.

== Screenshots ==

1. The login screen with the default (MGraphics) look.
2. The same screen on a phone.
3. A client configuration: own logo, colours and background, card in the centre.
4. Settings → MG Login.

== Changelog ==

= 1.0.2 =
* Fields: no icons and no placeholders inside the fields. Every field has its label above it and a plain outlined box, the same on all screens (log in, lost password, new password, registration). Only WordPress' show-password button stays in the password field.
* Messages (logged out, errors, lost and new password, registration, "check your e-mail"): plain text set off from the form by a thin line, instead of a tinted box with a coloured bar on the left. Errors are dark red with a red line. Text contrast is at least 6.5:1, field borders 3.5:1.
* Messages are announced to screen readers: errors as alerts, the others as polite status messages.
* New password screen: the strength is one line of coloured text under the field; "Generate password" and the main button match the rest of the card.
* Fixed: on the "administration email verification" screen the heading was drawn on top of the logo; the screen is now styled like the others.
* Fixed: with a tall card (several errors, registration, new password) the logo could be cut off at the top of the page.
* Fixed: the links under the form ("Log in", "Register") no longer sit beside the button.
* The "Forgot your password?" and privacy policy links are a little darker (contrast at least 4.5:1).
* Phones: 16 px text in the fields, so the browser does not zoom in when a field is tapped.
* Settings are unchanged. The MGraphics layout with a photo: Background → Image from the media library, Shading on, Card position → Left.

= 1.0.1 =
* Fixed: "Check for updates" stopped with a fatal error (Class "Parsedown" not found) when a GitHub release had release notes. The release package now contains the complete update checker, including its Markdown and readme parsers; every build checks this.

= 1.0.0 =
* Published on GitHub: updates come from the GitHub Releases of github.com/MGraphics/mg-login (release asset mg-login-1.0.0.zip).
* The default background is a pure CSS design in the MGraphics look; any photo can be chosen from the media library.
* First release: branded login, lost-password, reset, registration and message screens; language switcher under the form; settings for logo, link, title, heading, colours, background, card position, language switcher and custom CSS; bundled Open Sans font; Polish translation.

== Upgrade Notice ==

= 1.0.2 =
Cleaner fields (labels above, no icons) and calmer messages (plain text, no coloured bars). Settings stay as they are.

= 1.0.1 =
Fixes the fatal error when checking for updates. Install this package by hand once (Plugins → Add New Plugin → Upload Plugin), because 1.0.0 cannot update itself.

= 1.0.0 =
First release.
