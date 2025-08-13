#
#   Dockerfile
#
FROM php:8.2-apache


#
#   src
#
ADD src/www/html /var/www/html


#
#   expose
#
EXPOSE 80
