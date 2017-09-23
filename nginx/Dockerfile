FROM nginx

RUN rm -rf /etc/nginx/conf.d
COPY ssl /etc/nginx/ssl

COPY conf/conf.d /etc/nginx/conf.d
COPY start.sh /etc/init.d/start.sh
