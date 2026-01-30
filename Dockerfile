FROM invoiceninja/invoiceninja:5

# Copy application files (customizations)
COPY --chown=www-data:www-data . /var/www/app

# Copy custom configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Ensure correct permissions
RUN chown -R www-data:www-data /var/www/app/storage /var/www/app/bootstrap/cache

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
