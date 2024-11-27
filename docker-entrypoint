#!/usr/bin/env sh
if [ "${FRITZBOX_PROT}" = "https" ]; then
  sed -i "s/Listen 443/Listen ${FRITZBOX_PORT:-8443}/g" /etc/apache2/ports.conf
  sed -i "s/:443/:${FRITZBOX_PORT:-8443}/g" /etc/apache2/sites-enabled/*
else
  sed -i "s/Listen 80/Listen ${FRITZBOX_PORT:-8080}/g" /etc/apache2/ports.conf
  sed -i "s/:80/:${FRITZBOX_PORT:-8080}/g" /etc/apache2/sites-enabled/*
fi
apache2-foreground
