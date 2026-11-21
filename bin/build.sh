#!/usr/bin/env bash
# Builds the release packages into build/:
#   mg-login-<version>.zip         GitHub release asset (with the update checker, Update URI header)
#   mg-login-<version>-wporg.zip   WordPress.org (no update checker, no Update URI)
# The GitHub package is unpacked and checked with tests/release/update-check.php.
# Both unpack to a folder named mg-login/.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
VERSION="$(sed -n 's/^ \* Version: *//p' "$ROOT/mg-login.php" | head -1 | tr -d '[:space:]')"
STABLE="$(sed -n 's/^Stable tag: *//p' "$ROOT/readme.txt" | head -1 | tr -d '[:space:]')"
CONST="$(sed -n "s/^define( 'MG_LOGIN_VERSION', '\([^']*\)' );/\1/p" "$ROOT/mg-login.php")"
if [ "$VERSION" != "$STABLE" ] || [ "$VERSION" != "$CONST" ]; then
	echo "Version mismatch: header $VERSION, readme Stable tag $STABLE, MG_LOGIN_VERSION $CONST" >&2
	exit 1
fi
OUT="$ROOT/build"
rm -rf "$OUT"
mkdir -p "$OUT/github" "$OUT/wporg"

rsync -a --exclude-from="$ROOT/.distignore" "$ROOT/" "$OUT/github/mg-login/"
rsync -a "$OUT/github/mg-login/" "$OUT/wporg/mg-login/"

# WordPress.org: updates come from WordPress.org itself.
WP="$OUT/wporg/mg-login"
rm -rf "$WP/lib" "$WP/includes/class-updater.php"
sed -i.bak -e '/^ \* Update URI:/d' -e '/github-build:start/,/github-build:end/d' "$WP/mg-login.php"
rm -f "$WP/mg-login.php.bak"
if grep -R -n -i -E 'update uri|plugin-update-checker|PucFactory|github-build' "$WP" --include='*.php'; then
	echo "The WordPress.org build still contains update-checker code." >&2
	exit 1
fi

( cd "$OUT/github" && zip -qr "../mg-login-$VERSION.zip" mg-login )
( cd "$OUT/wporg" && zip -qr "../mg-login-$VERSION-wporg.zip" mg-login )

# The GitHub package must carry the whole update checker (lib/plugin-update-checker/vendor:
# Parsedown, readme parser): unpack it and run an update check against a release fixture.
CHECK="$(mktemp -d)"
trap 'rm -rf "$CHECK"' EXIT
unzip -q "$OUT/mg-login-$VERSION.zip" -d "$CHECK"
php "$ROOT/tests/release/update-check.php" "$CHECK/mg-login"
echo "$OUT/mg-login-$VERSION.zip"
echo "$OUT/mg-login-$VERSION-wporg.zip"
