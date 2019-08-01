FROM nginx:1.13-alpine

RUN rm /etc/nginx/nginx.conf
RUN rm /etc/nginx/conf.d/default.conf

COPY ./nginx/conf/nginx.conf /etc/nginx/nginx.conf
COPY ./nginx/frontend-conf.d/ /etc/nginx/conf.d/