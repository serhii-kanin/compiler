FROM php:7.2-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libgmp-dev \
    build-essential \
    apt-utils \
    libtool \
    autoconf \
    pkg-config \
    uuid-dev \
    libzmq3-dev \
    libsodium-dev \
    wget \
    bzip2 \
    git \
    tar \
    zlib1g-dev \
    libxml2-dev \
    librabbitmq-dev \
    ssh-client



RUN git clone git://github.com/mkoppanen/php-zmq.git \
 && cd php-zmq \
 && phpize && ./configure \
 && make \
 && make install \
 && cd .. \
 && rm -fr php-zmq \
 && echo "extension=zmq.so" > /usr/local/etc/php/conf.d/docker-php-ext-zmq.ini

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install gmp \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install zip \
    && rm -rf /tmp/pear

RUN mkdir ~/.ssh
RUN echo 'Host github.com\n\
            StrictHostKeyChecking no\n\
            IdentityFile ~/.ssh-local/id_rsa' > ~/.ssh/config

RUN rm -f /usr/local/etc/www.conf.default && rm -f /usr/local/etc/docker.conf && rm -f /usr/local/etc/zz-docker.conf

RUN pecl install xdebug && docker-php-ext-enable xdebug;
RUN pecl install amqp && docker-php-ext-enable amqp;

COPY docker/fpm/config/php_dev.ini /usr/local/etc/php/php.ini
COPY docker/fpm/config/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY docker/fpm/config/www_dev.conf /usr/local/etc/php-fpm.d/www.conf

#GEOIP
ADD http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz /tmp

RUN cd /tmp && tar xf /tmp/GeoLite2-Country.tar.gz && \
 mkdir -p /usr/share/GeoIP/ && \
 find /tmp/ -type f -name *mmdb -exec mv -t /usr/share/GeoIP/ {} + && \
 chmod 777 /usr/share/GeoIP/GeoLite2-Country.mmdb


WORKDIR /usr/src/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["php-fpm"]
