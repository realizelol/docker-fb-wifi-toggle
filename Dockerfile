FROM php:apache

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid
ENV FRITZBOX_HOST 192.168.178.1
ENV FRITZBOX_PORT 49000
ENV FRITZBOX_PROT http
ENV FRITZBOX_USER admin
ENV FRITZBOX_PASS SecurePa$$
ENV FRITZBOX_WLAN 3
ENV APACHE_HTTPS_PORT 8443
ENV APACHE_HTTP_PORT 8080

# Update
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
 && apt-get dist-upgrade -y \
 && apt-get autoremove --purge -y \
 && apt-get autoclean

# copy fileystem files into place
COPY rootfs /
#COPY docker-entrypoint /usr/local/bin/docker-entrypoint
#RUN chmod +x /usr/local/bin/docker-entrypoint
RUN chown -R www-data:www-data /var/www/html

# Healthcheck
# HEALTHCHECK CMD curl --fail http://localhost/index.php || exit 1

# Modify Ports
CMD sed -i "s/Listen 443/Listen ${APACHE_HTTPS_PORT:-8443}/g" /etc/apache2/ports.conf
CMD sed -i "s/:443/:${APACHE_HTTPS_PORT:-8443}/g" /etc/apache2/sites-{available,enabled}/*
CMD sed -i "s/Listen 80/Listen ${APACHE_HTTP_PORT:-8080}/g" /etc/apache2/ports.conf
CMD sed -i "s/:80/:${APACHE_HTTP_PORT:-8080}/g" /etc/apache2/sites-{available,enabled}/*
EXPOSE 8080/tcp 8443/tcp

# Start apache2
CMD ["docker-php-entrypoint", "apache2-foreground"]
