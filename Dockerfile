# Start from Ubuntu Base
FROM ubuntu

LABEL Description="IOTEdge License Plate Image Machine Learning"

# configure docker image with proxies
#ENV http_proxy http://proxy:8080
#ENV https_proxy http://proxy:8080
#ENV HTTP_PROXY http://proxy:8080
#ENV HTTPS_PROXY http://proxy:8080

# Setup Apache
# INSTALL  APACHE AND PHP
RUN apt-get update && apt-get install -y software-properties-common

RUN export LANG=C.UTF-8 && add-apt-repository -y ppa:ondrej/php && \
  	apt-get update &&  apt-get install -y supervisor wget git apache2 libapache2-mod-php5.6 php-xdebug php5.6 zip unzip && \
  	echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Enable apache mods.
RUN a2enmod rewrite
RUN a2enmod php5.6

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Expose apache.
EXPOSE 80

RUN apt-get update && apt-get install -y \
	libopencv-dev \
        python-opencv \
	vim \
#       build-base \
    	make \
    	gcc \
    	git \
    	perl \
    	ca-certificates \
    	curl \
    	gcc \
#   	libcurl \
#    	libgcc \
#    	libssh2 \
#    	pcre \
    	perl \
    	make \
    	musl-dev \
        && \
    	apt-get clean && \
    	apt-get autoremove && \
    	rm -rf /var/lib/apt/lists/*


WORKDIR /GitRepoYolo

#COPY Yolo /GitRepoYolo/Yolo


RUN chmod 755 /Yolo/backup
RUN cd /Yolo/backup/ && wget -O  yolo-voc.backup.zip http://www.et-cetera.ru/tmp/yolo-voc.backup.zip  &&  unzip yolo-voc.backup.zip



# Copy this webroot files
#COPY www /var/www/html
#COPY apache/000-default.conf /etc/apache2/sites-available

#RUN chmod 755 /var/www/html && chgrp -R www-data /var/www/html
#RUN cd /var/www/html/ && mkdir yolo && cd yolo && ln -s /app/Yolo/inputimages && ln -s /app/Yolo/outputimages
#RUN chmod 755 /app/Yolo/inputimages && chmod 755 /app/Yolo/outputimages && chgrp -R www-data /app/Yolo && chown -R www-data /app/Yolo


#RUN mkdir -p /var/log/supervisor
#COPY supervisor/apache2.conf /etc/supervisor/conf.d/
#CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]





