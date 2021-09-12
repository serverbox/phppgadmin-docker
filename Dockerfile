FROM alpine:3 as Downloader

ARG VERSION=7.13.0
ENV VERSION=${VERSION}
WORKDIR /phppgadmin
RUN set -ex \
  && apk add --no-cache curl \
  && curl -sLo - https://github.com/phppgadmin/phppgadmin/releases/download/REL_$( echo ${VERSION} | sed "s/\./-/g")/phpPgAdmin-${VERSION}.tar.bz2 \
    | tar -xj -C /phppgadmin --strip-components 1

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
  && apk add --no-cache postgresql-client
COPY php-fpm.d/www.conf /usr/local/etc/php-fpm.d/
COPY --from=Downloader --chown=www-data:www-data /phppgadmin/ /phppgadmin/
WORKDIR /phppgadmin/

# We expose phpMyAdmin on port 9000 (php-fpm)
EXPOSE 9000
