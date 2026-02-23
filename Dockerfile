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

    # Serve uploaded files (logos, documents) from persistent storage volume
    location /storage {
        alias /var/www/app/storage/app/public;
        try_files \$uri \$uri/ =404;
    }

    # Serve React assets from custom directory to bypass volume masking
    location /react {
        root /var/www/app/custom_public;
        try_files \$uri \$uri/ =404;
    }
    
    # Also serve other static assets if needed
    location /images {
        root /var/www/app/custom_public;
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

# Copy custom views
COPY --chown=invoiceninja:invoiceninja resources/views /var/www/app/resources/views

# Copy custom app logic
COPY --chown=invoiceninja:invoiceninja app /var/www/app/app
COPY --chown=invoiceninja:invoiceninja verify_whitelabel.php /var/www/app/verify_whitelabel.php

# Copy custom routes and migrations
COPY --chown=invoiceninja:invoiceninja routes/api.php /var/www/app/routes/api.php
COPY --chown=invoiceninja:invoiceninja database/migrations /var/www/app/database/migrations

# Copy public assets to custom directory to persist them in image
# IMPORTANT: Force rebuild - v2026.02.03.2200
ARG CACHEBUST=1
RUN echo "Build timestamp: $(date)"
COPY --chown=invoiceninja:invoiceninja public /var/www/app/custom_public

# Verify files were copied correctly (for debugging)
RUN ls -la /var/www/app/custom_public/react/ | head -5 && \
    echo "Total files in react:" && \
    ls /var/www/app/custom_public/react/ | wc -l


# Create storage symlink (Laravel needs public/storage → storage/app/public)
RUN rm -rf /var/www/app/public/storage && \
    ln -sf /var/www/app/storage/app/public /var/www/app/public/storage

# Startup script: ensure storage directories exist in the volume at runtime
RUN printf '#!/bin/sh\nmkdir -p /var/www/app/storage/app/public\nchown -R invoiceninja:invoiceninja /var/www/app/storage/app/public\n' > /usr/local/bin/storage-init.sh && \
    chmod +x /usr/local/bin/storage-init.sh

# Update supervisor to include nginx and storage init
RUN echo "" >> /etc/supervisord.conf && \
    echo "[program:storage-init]" >> /etc/supervisord.conf && \
    echo "command=/usr/local/bin/storage-init.sh" >> /etc/supervisord.conf && \
    echo "autostart=true" >> /etc/supervisord.conf && \
    echo "autorestart=false" >> /etc/supervisord.conf && \
    echo "startsecs=0" >> /etc/supervisord.conf && \
    echo "priority=1" >> /etc/supervisord.conf && \
    echo "stdout_logfile=/dev/stdout" >> /etc/supervisord.conf && \
    echo "stdout_logfile_maxbytes=0" >> /etc/supervisord.conf && \
    echo "stderr_logfile=/dev/stderr" >> /etc/supervisord.conf && \
    echo "stderr_logfile_maxbytes=0" >> /etc/supervisord.conf && \
    echo "" >> /etc/supervisord.conf && \
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
