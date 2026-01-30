FROM invoiceninja/invoiceninja:5

# Copy custom Nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy custom Supervisor configuration  
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80
