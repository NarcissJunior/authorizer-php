FROM php:7.4-cli

COPY . .
WORKDIR /usr/src

RUN apt-get update && apt-get install -y