FROM php:apache

# copy fileystem files into place
COPY rootfs /

# Update
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
 && apt install curl libcurl -y \
 && apt-get dist-upgrade -y \
 && apt-get autoremove --purge -y \
 && apt-get autoclean
RUN docker-php-ext-install curl \
 && docker-php-ext-enable curl

# Healthcheck
# HEALTHCHECK CMD curl --fail http://localhost/index.php || exit 1

EXPOSE 8080/tcp 8443/tcp
CMD ["/init"]
ENTRYPOINT ["/bin/sh"]
