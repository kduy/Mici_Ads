#!/bin/bash
set -e

# Set Apache port from Railway's PORT env (default 80)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Fix Railway MPM conflict: disable event/worker, force prefork
a2dismod mpm_event mpm_worker 2>/dev/null || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Ensure uploads directory exists and is writable by Apache (www-data)
mkdir -p /var/www/html/wp-content/uploads
chown -R www-data:www-data /var/www/html/wp-content/uploads
chmod -R 755 /var/www/html/wp-content/uploads

# Copy ACF plugin from staging dir into the persistent volume.
# This runs every startup so ACF survives volume mounts and rebuilds.
if [ -d /opt/acf-plugin/advanced-custom-fields ]; then
  mkdir -p /var/www/html/wp-content/plugins
  cp -r /opt/acf-plugin/advanced-custom-fields /var/www/html/wp-content/plugins/
  chown -R www-data:www-data /var/www/html/wp-content/plugins/advanced-custom-fields
  echo "[mici] ACF plugin copied to plugins directory"
fi

# Copy theme from image into volume (ensures latest theme code is always used)
if [ -d /opt/mici-theme/mici-ads-theme ]; then
  mkdir -p /var/www/html/wp-content/themes
  cp -r /opt/mici-theme/mici-ads-theme /var/www/html/wp-content/themes/
  chown -R www-data:www-data /var/www/html/wp-content/themes/mici-ads-theme
  echo "[mici] Theme copied to themes directory"
fi

# Delegate to WordPress's original docker-entrypoint.sh
exec docker-entrypoint.sh "$@"
