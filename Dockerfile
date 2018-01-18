FROM debian:jessie

LABEL maintainer="Avatar, of UberTweak"

# disable interactive functions. 
ENV DEBIAN_FRONTEND noninteractive

# Install necessary packages, and then clean up after apt.
RUN apt-get update && \
	apt-get install -y apache2 \
	libapache2-mod-php5 \
	php5-gd \
	php-pear \
	php-apc \
	php5-mcrypt \
	php5-json \
	php5-curl \
        php5-sqlite \
        sqlite3 \
        git \
	curl lynx-cur \
	&& rm -rf /var/lib/apt/lists/* \
	&& apt-get clean -y

# Install composer for PHP dependencies
RUN cd /tmp && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Enable apache mods.
RUN a2enmod php5
RUN a2enmod rewrite

# Update the PHP.ini file, enable <? ?> tags and quieten logging.
RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /etc/php5/apache2/php.ini
RUN sed -i "s/error_reporting = .*$/error_reporting = E_ERROR | E_WARNING | E_PARSE/" /etc/php5/apache2/php.ini

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Copy site fom this git checkout.
#RUN git clone -v --depth=1 https://github.com/clamburger/ubersite-lite /var/www
ADD ./ /var/www/html/

# Create the config directory, make it writable
RUN mkdir /var/www/html/config/
RUN chmod o+w /var/www/html/config/

# Make sure www-data owns the web files
RUN chown -hR www-data:www-data /var/www/html/

# Remove the stock Debian welcome home page.
RUN rm /var/www/html/index.html

# Install the composer dependencies
RUN cd /var/www/html/; composer install

# Add apache config
ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

# Load in the questionaire tempate to the database.
RUN echo "If you'd like to load in the pre-filled questionaire template, run this command from your host _after_ you import users to your site."
RUN echo "Note: the './questionaire_template.sql' must be in the cwd of your host shell."
RUN echo "$ docker exec <containername> sh -c '/usr/bin/sqlite3 /var/www/html/config/database.db < /var/www/html/questionaire_tempate.sql' "

# Start Apache
EXPOSE 80
CMD /usr/sbin/apache2ctl -D FOREGROUND
