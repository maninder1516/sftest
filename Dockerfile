FROM php:latest

RUN go get github.com/maninder1516/sftest

RUN apt-get update && apt-get install -y git libpng12-dev

RUN docker-php-ext-install zip && docker-php-ext-enable zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD . /app

WORKDIR /app

