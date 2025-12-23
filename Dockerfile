#
#   Dockerfile
#
FROM php:8.2-apache


#
#   docroot
#
RUN sed -i 's,DocumentRoot /var/www/html,DocumentRoot /opt/index/public,g' '/etc/apache2/sites-available/000-default.conf'
RUN printf '%s\n' \
    '<Directory /opt/index/public>' \
    '    Options Indexes FollowSymLinks' \
    '    AllowOverride All' \
    '    Require all granted' \
    '</Directory>' \
    >> /etc/apache2/apache2.conf


#
#   expose
#
EXPOSE 80
