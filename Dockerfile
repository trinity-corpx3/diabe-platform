FROM invoiceninja/invoiceninja:5

# Copy only customizations (not the entire app)
COPY --chown=www-data:www-data lang/es_ES/texts.php /var/www/app/lang/es_ES/texts.php
COPY --chown=www-data:www-data app/Console/Commands/ConfigureConstructionCompany.php /var/www/app/app/Console/Commands/ConfigureConstructionCompany.php

# Copy custom configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

# Use the original entrypoint from the base image
CMD ["/usr/local/bin/docker-entrypoint"]
