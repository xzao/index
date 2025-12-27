#
#   Dockerfile
#
FROM php:8.2-apache


#
#   dependencies
#
RUN apt-get update && apt-get install -y \
    git \
    make \
    && rm -rf /var/lib/apt/lists/*


#
#   copy application
#
WORKDIR /opt/index
COPY . /opt/index


#
#   install vendor dependencies
#
RUN make install


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
