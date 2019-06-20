FROM php:7.3.6-apache-stretch
COPY \path\to\code\ \var\www\
CMD ["apt install powershell", "pip install pycrypto"]
CMD ["chown -R www-data /var/www/", "chown -R 755 /var/"]
WORKDIR /var/www/