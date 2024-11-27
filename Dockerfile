FROM php:apache

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Update
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
 && apt-get dist-upgrade -y \
 && apt-get autoremove --purge -y \
 && apt-get autoclean

# copy fileystem files into place
COPY rootfs /
RUN chown -R www-data:www-data /var/www/html

# Healthcheck
# HEALTHCHECK CMD curl --fail http://localhost/index.php || exit 1

EXPOSE 8080/tcp 8443/tcp

# Start apache2
CMD ["apache2-foreground"]
