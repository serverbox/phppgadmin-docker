FROM alpine:3 as Downloader

ARG VERSION=7.13.0
ENV VERSION=${VERSION}
RUN set -ex \
  && mkdir /phppgadmin \
  && apk add --no-cache curl \
  && curl -sLo - https://github.com/phppgadmin/phppgadmin/releases/download/REL_$( echo ${VERSION} | sed "s/\./-/g")/phpPgAdmin-${VERSION}.tar.bz2 \
    | tar -xj -C /phppgadmin --strip-components 1 \
  && curl -sLO https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
  && chmod +x php-fpm-healthcheck

FROM php:7.4-fpm-alpine
ARG VERSION=7.13.0
ENV PHPPGADMIN_VERSION=${VERSION}

LABEL maintainer="Serverbox Ltd <containers@serverbox.co.uk>" \
  org.label-schema.name="phppgadmin" \
  org.label-schema.vendor="ServerBox Ltd" \
  org.label-schema.description="phpPgAdmin Docker image, phpPgAdmin is a web-based administration tool for PostgreSQL." \
  org.label-schema.vcs-url="https://github.com/serverbox/phppgadmin-docker" \
  org.label-schema.license="LGPLv3"

RUN set -ex \
  && mkdir /sessions \
  && chown www-data:www-data /sessions \
  && apk add --no-cache --virtual .build-deps \
    bzip2-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxpm-dev \
    libzip-dev \
    libbz2 \
    postgresql-dev \
  && docker-php-ext-configure gd \
  && runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )" \
  && apk add --no-cache --virtual .phpmpgdmin-phpexts-rundeps $runDeps \
  && docker-php-ext-install gd pgsql opcache zip \
  && apk del .build-deps \
  && apk add --no-cache \
    fcgi \
    bzip2 \
    gd \
    libpq \
    libzip \
    postgresql-client
COPY php-fpm.d/www.conf /usr/local/etc/php-fpm.d/
COPY --from=Downloader --chown=www-data:www-data /phppgadmin/ /phppgadmin/
COPY --from=Downloader --chown=www-data:www-data /php-fpm-healthcheck /usr/bin/php-fpm-healthcheck
#USER www-data
WORKDIR /phppgadmin/

# We expose phpMyAdmin on port 9000 (php-fpm)
EXPOSE 9000
