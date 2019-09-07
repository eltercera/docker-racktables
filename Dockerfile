# This file is part of docker-racktables.
#
# docker-racktables is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# docker-racktables is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Foobar.  If not, see <https://www.gnu.org/licenses/>.

FROM alpine:3.9
MAINTAINER Rómulo Rodríguez <rjrodrig@ucab.edu.ve>

RUN apk --no-cache update \
    && apk --no-cache upgrade \
    && apk --no-cache add \
        apache2 \
        ca-certificates \
        gettext \
        libxml2 \
        openssh-client \
        perl \
        perl-net-openssh \
        perl-net-telnet \
        php7 \
        php7-apache2 \
        php7-bcmath \
        php7-curl \
        php7-dom \
        php7-fileinfo \
        php7-gd \
        php7-gmp \
        php7-json \
        php7-phar \
        php7-ldap \
        php7-mbstring \
        php7-openssl \
        php7-pcntl \
        php7-pdo \
        php7-pdo_mysql \
        php7-pecl-mcrypt \
        php7-pecl-yaml \
        php7-session \
        php7-snmp \
        php7-sockets \
        php7-xml \
        php7-xmlreader \
        php7-xmlrpc \
        py2-pip \
        python \
    && pip install ucsmsdk \
    && mkdir -p /run/apache2 \
    && ln -sfT /dev/stderr /var/log/apache2/error.log \
    && ln -sfT /dev/stdout /var/log/apache2/access.log \
    && php -v

ARG RACKTABLES_VERSION
ENV RACKTABLES_PATH /racktables
RUN wget -q https://github.com/RackTables/racktables/archive/RackTables-$RACKTABLES_VERSION.tar.gz \
	&& tar -xzf RackTables-$RACKTABLES_VERSION.tar.gz \
	&& rm RackTables-$RACKTABLES_VERSION.tar.gz \
	&& mv racktables-RackTables-$RACKTABLES_VERSION $RACKTABLES_PATH

ADD httpd.conf.template /etc/apache2/httpd.conf.template
ADD docker-entrypoint.sh make_racktables_secret.php init_racktables_db.php utils.php /

# Arbitrary user suport
RUN mkdir -p /var/log/apache2 \
    && chmod g+w \
        /run/apache2 \
        /etc/apache2/httpd.conf \
        /var/log/apache2 \
        $RACKTABLES_PATH/plugins \
        $RACKTABLES_PATH/scripts \
        $RACKTABLES_PATH/gateways \
        $RACKTABLES_PATH/wwwroot/inc \
    && chgrp root \
        /run/apache2 \
        /var/log/apache2 \
        /etc/apache2/httpd.conf \
        $RACKTABLES_PATH/plugins \
        $RACKTABLES_PATH/scripts \
        $RACKTABLES_PATH/gateways \
        $RACKTABLES_PATH/wwwroot/inc \
    && chmod +x /docker-entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["/docker-entrypoint.sh"]


