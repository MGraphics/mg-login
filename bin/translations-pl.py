#!/usr/bin/env python3
"""Builds languages/mg-login-pl_PL.po from languages/mg-login.pot and the Polish texts below.

Usage: bin/translations-pl.py   (then compile: msgfmt -o languages/mg-login-pl_PL.mo languages/mg-login-pl_PL.po)
Keys are msgids exactly as written in the .pot (with its escapes). Strings missing here stay
untranslated and are listed on stderr.
"""
import pathlib
import re
import sys

ROOT = pathlib.Path(__file__).resolve().parent.parent

PL = {
    "MG Login": "MG Login",
    "https://github.com/MGraphics/mg-login": "https://github.com/MGraphics/mg-login",
    "A branded login screen: your logo, colours and background on a clean card, with the language switcher placed under the form. Styles only the login screen; nothing is added to the public pages.":
        "Firmowy ekran logowania: twoje logo, kolory i tło, przejrzysta karta i wybór języka pod formularzem. Zmienia wyłącznie ekran logowania; na publicznych stronach nic nie dodaje.",
    "MGraphics": "MGraphics",
    "https://mgraphics.com.pl": "https://mgraphics.com.pl",
    "Settings": "Ustawienia",
    "Choose the logo": "Wybierz logo",
    "Choose the background image": "Wybierz obraz tła",
    "Use this image": "Użyj tego obrazu",
    "You are not allowed to do this.": "Nie masz uprawnień do tej operacji.",
    "The look of the login screen. Sign-in itself, the login address and the e-mails are not changed. Leave a field empty to use the automatic value.":
        "Wygląd ekranu logowania. Samo logowanie, adres logowania i e-maile się nie zmieniają. Puste pole oznacza wartość automatyczną.",
    "Open the login screen": "Otwórz ekran logowania",
    "The default settings have been restored.": "Przywrócono ustawienia domyślne.",
    "Logo": "Logo",
    "MGraphics logo (default)": "Logo MGraphics (domyślne)",
    "Image from the media library": "Obraz z biblioteki mediów",
    "No logo": "Bez logo",
    "A PNG with a transparent background works best; the logo is shown above the card.":
        "Najlepiej sprawdza się PNG z przezroczystym tłem; logo jest wyświetlane nad kartą.",
    "Logo width": "Szerokość logo",
    "Default: %d px. The height follows the image; on phones the logo is about a quarter smaller.":
        "Domyślnie: %d px. Wysokość wynika z proporcji obrazu; na telefonach logo jest o około jedną czwartą mniejsze.",
    "Logo link": "Link logo",
    "Empty: the home page of this site.": "Puste: strona główna tej witryny.",
    "Logo title": "Tytuł logo",
    "Text of the logo link, read by screen readers. Empty: the site title.":
        "Tekst linku logo, odczytywany przez czytniki ekranu. Puste: nazwa witryny.",
    "Card": "Karta",
    "Heading": "Nagłówek",
    "Log in": "Logowanie",
    "Shown above the login form. Empty: \\\"Log in\\\" in the language of the login screen.":
        "Wyświetlany nad formularzem logowania. Puste: „Logowanie” w języku ekranu logowania.",
    "Card position": "Położenie karty",
    "Left (default)": "Po lewej (domyślnie)",
    "Centre": "Na środku",
    "Right": "Po prawej",
    "On large screens. On tablets and phones the card is always centred.":
        "Na dużych ekranach. Na tabletach i telefonach karta jest zawsze na środku.",
    "Language switcher": "Wybór języka",
    "Show the language switcher under the form": "Pokazuj wybór języka pod formularzem",
    "WordPress shows it only when at least one language other than English is installed.":
        "WordPress pokazuje go tylko wtedy, gdy zainstalowany jest co najmniej jeden język poza angielskim.",
    "Colours": "Kolory",
    "Accent": "Kolor akcentu",
    "Button, links, field focus and the tick of \\\"Remember Me\\\".":
        "Przycisk, linki, obramowanie aktywnego pola i zaznaczenie „Zapamiętaj mnie”.",
    "Accent on hover": "Akcent po najechaniu",
    "Button and links under the mouse; usually a slightly darker accent.":
        "Przycisk i linki pod kursorem; zwykle nieco ciemniejszy akcent.",
    "Background": "Tło",
    "Background image": "Obraz tła",
    "MGraphics photo (default)": "Zdjęcie MGraphics (domyślne)",
    "No image, colour only": "Bez obrazu, tylko kolor",
    "The image covers the whole screen; about 1920 × 1080 px is enough.":
        "Obraz wypełnia cały ekran; wystarczy około 1920 × 1080 px.",
    "Background colour": "Kolor tła",
    "Shown without an image and while the image loads.": "Widoczny bez obrazu i podczas jego wczytywania.",
    "Shading": "Przyciemnienie",
    "Darken the background slightly towards the right": "Lekko przyciemniaj tło w stronę prawej krawędzi",
    "Custom CSS": "Własny CSS",
    "Additional CSS": "Dodatkowy CSS",
    "Loaded only on the login screen, after the plugin's styles. Example: <code>body.login #login { border-radius: 0; }</code>":
        "Wczytywany tylko na ekranie logowania, po stylach wtyczki. Przykład: <code>body.login #login { border-radius: 0; }</code>",
    "Restore all MG Login settings to the defaults?": "Przywrócić wszystkie ustawienia MG Login do domyślnych?",
    "Restore the defaults": "Przywróć ustawienia domyślne",
    "Choose image": "Wybierz obraz",
    "Remove": "Usuń",
    "Forgot your password?": "Zapomniałeś hasła?",
}


def po_string(text: str) -> str:
    return '"' + text + '"'


def main() -> int:
    pot = (ROOT / "languages" / "mg-login.pot").read_text(encoding="utf-8")
    out = [
        '# Polish translation of MG Login.',
        '# This file is distributed under the GPL-2.0-or-later.',
        'msgid ""',
        'msgstr ""',
        '"Project-Id-Version: MG Login 1.0.3\\n"',
        '"Report-Msgid-Bugs-To: https://github.com/MGraphics/mg-login/issues\\n"',
        '"Last-Translator: MGraphics\\n"',
        '"Language-Team: MGraphics\\n"',
        '"PO-Revision-Date: 2026-11-23 09:00+0100\\n"',
        '"Language: pl_PL\\n"',
        '"MIME-Version: 1.0\\n"',
        '"Content-Type: text/plain; charset=UTF-8\\n"',
        '"Content-Transfer-Encoding: 8bit\\n"',
        '"Plural-Forms: nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2);\\n"',
        '"X-Domain: mg-login\\n"',
        '',
    ]
    missing = []
    for block in re.split(r'\n\n+', pot.strip()):
        m = re.search(r'^msgid ((?:"(?:[^"\\]|\\.)*"\n?)+)msgstr ""', block, re.M)
        if not m:
            continue
        msgid = ''.join(re.findall(r'"((?:[^"\\]|\\.)*)"', m.group(1)))
        if msgid == '':
            continue
        comments = [line for line in block.split('\n') if line.startswith('#')]
        translated = PL.get(msgid)
        if translated is None:
            missing.append(msgid)
            translated = ''
        out.extend(comments)
        out.append('msgid ' + po_string(msgid))
        out.append('msgstr ' + po_string(translated.replace('"', '\\"') if '\\"' not in translated else translated))
        out.append('')
    (ROOT / "languages" / "mg-login-pl_PL.po").write_text('\n'.join(out), encoding="utf-8")
    for msgid in missing:
        print('missing:', msgid, file=sys.stderr)
    return 1 if missing else 0


if __name__ == '__main__':
    sys.exit(main())
