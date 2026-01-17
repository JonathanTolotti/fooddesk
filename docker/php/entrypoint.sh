#!/bin/sh
# Corrige permissões apenas dentro do container
if [ -d "/var/www/storage" ] && [ -d "/var/www/bootstrap/cache" ]; then
    chmod -R u=rwx,g=rwx,o=rx /var/www/storage /var/www/bootstrap/cache
fi

exec "$@"
