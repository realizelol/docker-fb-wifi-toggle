FROM php:apache

# Manually set up the apache environment variables
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_CONFDIR=/etc/apache2
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_LOCK_DIR=/var/lock/apache2
ENV APACHE_PID_FILE=/var/run/apache2.pid
ENV FRITZBOX_HOST=192.168.178.1
ENV FRITZBOX_PORT=49443
ENV FRITZBOX_PROT=https
ENV FRITZBOX_USER=dslf-config
ENV FRITZBOX_PASS=S3cure-Pa55
ENV FRITZBOX_WLAN=3
ENV APACHE_HTTP_PORT=8080
ENV APACHE_HTTPS_PORT=8443
ENV APACHE_SSL_ENABLE=false
ENV APACHE_SSL_KEYFILE=/etc/ssl/certs/key.pem
ENV APACHE_SSL_CERTFILE=/etc/ssl/certs/certificate.pem

# Update system environment
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
 && apt-get dist-upgrade -y \
 && apt-get autoremove --purge -y \
 && apt-get autoclean

# copy fileystem files into place and set permissions
COPY rootfs /
RUN  chown -R "${APACHE_RUN_USER}":"${APACHE_RUN_GROUP}" "/var/www/html" \
       "${APACHE_LOG_DIR}" "${APACHE_LOCK_DIR}"
# copy entrypoint file and set execute permission
COPY docker-entrypoint /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

# Enable security headers.
RUN set -eux; \
  a2enmod headers; \
  { \
    echo '<Directory /var/www/html>'; \
    echo '  Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"'; \
    echo '  Header always set X-Frame-Options "sameorigin"'; \
    echo '  Header setifempty X-Content-Type-Options "nosniff"'; \
    echo '</Directory>'; \
  } > "${APACHE_CONFDIR}/conf-available/headers.conf"; \
  a2enconf headers

# Reduce the Server header to just "Apache" and remove the server signature on error pages.
RUN sed -sie "s/^ServerTokens.*/ServerTokens Prod/g" "${APACHE_CONFDIR}/conf-available/security.conf"
RUN sed -sie "s/^ServerSignature.*/ServerSignature Off/g" "${APACHE_CONFDIR}/conf-available/security.conf"

# Stop sending X-Powered-By.
RUN echo -e 'expose_php=Off;' > /usr/local/etc/php/conf.d/no-expose.ini

# Modify Ports
RUN sed -sie "s/Listen 80/Listen ${APACHE_HTTP_PORT}/g" /etc/apache2/ports.conf
RUN sed -sie "s/Listen 443/Listen ${APACHE_HTTPS_PORT}/g" /etc/apache2/ports.conf
RUN find /etc/apache2 -type f -regex ".*\/sites-\(available\|enabled\)\/.*" -exec \
      sed -sie "s/:80/:${APACHE_HTTP_PORT}/g" {} \;
RUN find /etc/apache2 -type f -regex ".*\/sites-\(available\|enabled\)\/.*" -exec \
      sed -sie "s/:443/:${APACHE_HTTPS_PORT}/g" {} \;
EXPOSE "${APACHE_HTTP_PORT}/tcp" "${APACHE_HTTPS_PORT}/tcp"

# Healthcheck
# HEALTHCHECK CMD curl --fail http://localhost/index.php || exit 1

# Start apache2 with root privileges
USER root
ENTRYPOINT ["/bin/bash"]
CMD ["docker-entrypoint"]
