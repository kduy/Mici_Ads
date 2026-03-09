FROM wordpress:6.7-php8.2-apache

# Increase PHP limits for image processing and uploads
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Copy custom theme
COPY mici-ads-theme/ /var/www/html/wp-content/themes/mici-ads-theme/

# Copy entrypoint that handles PORT + MPM fix, then delegates to docker-entrypoint.sh
COPY railway-entrypoint.sh /usr/local/bin/railway-entrypoint.sh
RUN chmod +x /usr/local/bin/railway-entrypoint.sh

ENTRYPOINT ["railway-entrypoint.sh"]
CMD ["apache2-foreground"]
