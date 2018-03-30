FROM ubuntu:16.04

RUN apt-get update
RUN apt-get upgrade -y

COPY debconf.selections /tmp/
RUN debconf-set-selections /tmp/debconf.selections

RUN apt-get install -y \
	php7.1 \
	php7.1-bz2 \
	php7.1-cgi \
	php7.1-cli \
	php7.1-common \
	php7.1-curl \
	php7.1-dev \
	php7.1-enchant \
	php7.1-fpm \
	php7.1-gd \
	php7.1-gmp \
	php7.1-imap \
	php7.1-interbase \
	php7.1-intl \
	php7.1-json \
	php7.1-ldap \
	php7.1-mcrypt \
	php7.1-mysql \
	php7.1-odbc \
	php7.1-opcache \
	php7.1-pgsql \
	php7.1-phpdbg \
	php7.1-pspell \
	php7.1-readline \
	php7.1-recode \
	php7.1-snmp \
	php7.1-sqlite3 \
	php7.1-sybase \
	php7.1-tidy \
	php7.1-xmlrpc \
	php7.1-xsl
RUN apt-get install apache2 libapache2-mod-php7.1 -y
RUN apt-get install mariadb-common mariadb-server mariadb-client -y
RUN apt-get install postfix -y
RUN apt-get install git nodejs npm composer nano tree vim curl ftp -y
RUN npm install -g bower grunt-cli gulp

ENV LOG_STDOUT **Boolean**
ENV LOG_STDERR **Boolean**
ENV LOG_LEVEL warn
ENV ALLOW_OVERRIDE All
ENV DATE_TIMEZONE UTC
ENV TERM dumb

COPY ./ /var/www/html/
COPY run-lamp.sh /usr/sbin/

RUN a2enmod rewrite
RUN ln -s /usr/bin/nodejs /usr/bin/node
RUN chmod +x /usr/sbin/run-lamp.sh
RUN chown -R www-data:www-data /var/www/html

VOLUME /var/www/html
VOLUME /var/log/httpd
VOLUME /var/lib/mysql
VOLUME /var/log/mysql

EXPOSE 80
EXPOSE 3306

CMD ["/usr/sbin/run-lamp.sh"]