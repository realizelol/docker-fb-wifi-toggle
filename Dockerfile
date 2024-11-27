FROM php:apache

# copy fileystem files into place
COPY rootfs /

# Update
RUN apt-get update \
 && DEBIAN_FRONTEND=noninteractive apt-get \
   -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" \
   dist-upgrade -y \
 && apt autoremove --purge -y \
 && apt autoclean \
 && docker-php-ext-configure curl \
 && docker-php-ext-install curl \
 && docker-php-ext-enable curl

# Healthcheck
# HEALTHCHECK CMD curl --fail http://localhost/index.php || exit 1

EXPOSE ["8080", "8443"]
CMD ["/init"]
ENTRYPOINT ["/bin/sh"]
