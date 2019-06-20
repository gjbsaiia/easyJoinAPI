FROM php:7.3.6-apache-stretch
COPY \\NCSMBP01\clo-easyADjoin\api\ \var\www\
COPY \\NCSMBP01\clo-easyADjoin\index.php \var\www\
CMD ["apt install powershell", "pip install pycrypto"]
CMD ["chown -R www-data /var/www/", "chown -R 755 /var/"]
WORKDIR /var/www/