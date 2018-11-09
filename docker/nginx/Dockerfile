FROM rnburn/nginx-opentracing:0.2.1

ADD https://github.com/Yelp/dumb-init/releases/download/v1.2.0/dumb-init_1.2.0_amd64 /usr/local/bin/dumb-init
RUN chmod +x /usr/local/bin/dumb-init

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        geoip-database \
        nginx-module-geoip=1.13.8-1~stretch


COPY docker/nginx/default_dev.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/nginx_dev.conf /etc/nginx/nginx.conf

RUN mkdir -p /var/www/
ADD https://github.com/perftools/xhgui/archive/0.8.1.tar.gz /var/www/
RUN cd /var/www/ && tar -zxvf /var/www/0.8.1.tar.gz && mv /var/www/xhgui-0.8.1 /var/www/xhgui

COPY ./public /usr/src/app/public

ADD https://raw.githubusercontent.com/rlerdorf/opcache-status/master/opcache.php /usr/src/app/public

# Rewrite signal. Kubernetes always sends SIGTERM signal, nginx use SIGQUIT for graceful shutdown.
# http://nginx.org/ru/docs/control.html
ENTRYPOINT ["dumb-init", "--rewrite", "15:3"]

CMD ["nginx", "-g", "daemon off;"]
