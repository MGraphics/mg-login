#!/usr/bin/env bash
# Development environment: WordPress (php8.4-apache) + MariaDB in Docker, project "mg-login".
# Site: http://127.0.0.1:8486 · admin credentials: .local/admin.txt (not in git).
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
URL="http://127.0.0.1:8486"
wp() { docker compose -f "$ROOT/docker-compose.yml" run --rm -T cli wp "$@"; }

docker compose -f "$ROOT/docker-compose.yml" up -d --wait db wordpress
for _ in $(seq 1 60); do
	curl -s -o /dev/null "$URL/wp-admin/install.php" && break
	sleep 1
done

mkdir -p "$ROOT/.local"
if ! wp core is-installed 2>/dev/null; then
	PASS="$(openssl rand -hex 12)"
	wp core install --url="$URL" --title="MGraphics" --admin_user=admin --admin_password="$PASS" --admin_email=admin@example.test --skip-email
	printf 'url: %s\nuser: admin\npassword: %s\n' "$URL" "$PASS" >"$ROOT/.local/admin.txt"
	chmod 600 "$ROOT/.local/admin.txt"
	wp rewrite structure '/%postname%/' --hard
	wp language core install pl_PL --activate || true
	# A published privacy policy page, so the login screen shows its link (as on mgraphics.com.pl).
	PRIVACY="$(wp post create --post_type=page --post_status=publish --post_title='Polityka prywatności' --post_name=polityka-prywatnosci --porcelain)"
	wp option update wp_page_for_privacy_policy "$PRIVACY"
	wp plugin activate mg-login
fi
echo "Ready: $URL (credentials in .local/admin.txt)"
