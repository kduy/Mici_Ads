FROM wordpress:6.7-php8.2-apache

# Increase PHP limits for image processing and uploads
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Install Ghostscript for PDF rasterization (Imagick already bundled in base image)
RUN apt-get update \
 && apt-get install -y --no-install-recommends ghostscript unzip \
 && rm -rf /var/lib/apt/lists/*

# Allow Imagick to process PDFs (ImageMagick 6 blocks PDF by default)
RUN sed -i 's/<policy domain="coder" rights="none" pattern="PDF"/<policy domain="coder" rights="read|write" pattern="PDF"/' /etc/ImageMagick-6/policy.xml

# Download ACF to staging dir (copied to volume at runtime by entrypoint)
RUN curl -sL "https://downloads.wordpress.org/plugin/advanced-custom-fields.latest-stable.zip" \
      -o /tmp/acf.zip \
 && mkdir -p /opt/acf-plugin \
 && unzip -q /tmp/acf.zip -d /opt/acf-plugin/ \
 && rm /tmp/acf.zip

# Copy custom theme to both staging dir (for entrypoint) and default location
COPY mici-ads-theme/ /opt/mici-theme/mici-ads-theme/
COPY mici-ads-theme/ /var/www/html/wp-content/themes/mici-ads-theme/

# Copy entrypoint that handles PORT + MPM fix, then delegates to docker-entrypoint.sh
COPY railway-entrypoint.sh /usr/local/bin/railway-entrypoint.sh
RUN chmod +x /usr/local/bin/railway-entrypoint.sh

ENTRYPOINT ["railway-entrypoint.sh"]
CMD ["apache2-foreground"]
