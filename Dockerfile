FROM invoiceninja/invoiceninja:5

# Switch to root to install and configure nginx
USER root

# Install nginx
RUN apk add --no-cache nginx

# Create nginx directories with proper permissions
RUN mkdir -p /run/nginx && \
    mkdir -p /var/lib/nginx/tmp/client_body && \
    mkdir -p /var/lib/nginx/tmp/proxy && \
    mkdir -p /var/lib/nginx/tmp/fastcgi && \
    mkdir -p /var/lib/nginx/tmp/uwsgi && \
    mkdir -p /var/lib/nginx/tmp/scgi && \
    mkdir -p /var/lib/nginx/logs && \
    mkdir -p /var/log/nginx && \
    chown -R invoiceninja:invoiceninja /var/lib/nginx && \
    chown -R invoiceninja:invoiceninja /var/log/nginx && \
    chown -R invoiceninja:invoiceninja /run/nginx

# Configure nginx to use stdout/stderr for logs
RUN sed -i 's|error_log.*|error_log /dev/stderr warn;|g' /etc/nginx/nginx.conf && \
    sed -i 's|access_log.*|access_log /dev/stdout main;|g' /etc/nginx/nginx.conf

# Copy nginx configuration
COPY <<EOF /etc/nginx/http.d/default.conf
server {
    listen 80;
    server_name _;
    root /var/www/app/public;
    index index.php;

    access_log /dev/stdout;
    error_log /dev/stderr;

    client_body_temp_path /var/lib/nginx/tmp/client_body;
    proxy_temp_path /var/lib/nginx/tmp/proxy;
    fastcgi_temp_path /var/lib/nginx/tmp/fastcgi;
    uwsgi_temp_path /var/lib/nginx/tmp/uwsgi;
    scgi_temp_path /var/lib/nginx/tmp/scgi;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

# Copy custom views (must be done before switching user back or with correct ownership)
COPY --chown=invoiceninja:invoiceninja resources/views /var/www/app/resources/views
COPY --chown=invoiceninja:invoiceninja public /var/www/app/public


# Update supervisor to include nginx
RUN echo "" >> /etc/supervisord.conf && \
    echo "[program:nginx]" >> /etc/supervisord.conf && \
    echo "command=nginx -g 'daemon off;'" >> /etc/supervisord.conf && \
    echo "autostart=true" >> /etc/supervisord.conf && \
    echo "autorestart=true" >> /etc/supervisord.conf && \
    echo "stdout_logfile=/dev/stdout" >> /etc/supervisord.conf && \
    echo "stdout_logfile_maxbytes=0" >> /etc/supervisord.conf && \
    echo "stderr_logfile=/dev/stderr" >> /etc/supervisord.conf && \
    echo "stderr_logfile_maxbytes=0" >> /etc/supervisord.conf

# Switch back to invoiceninja user
USER invoiceninja

EXPOSE 80
