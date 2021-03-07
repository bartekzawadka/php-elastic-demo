FROM php:7.3-apache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./*.html /var/www/html
COPY ./*.php /var/www/html
COPY ./composer.json /var/www/html

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

WORKDIR /var/www/html
RUN composer install

ENV ELASTIC_ENDPOINT="http://localhost:9200"