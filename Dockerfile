# https://githubprod.prci.com/progressive/clo-cloud-client/blob/master/Dockerfile
FROM php:7.3.6-apache-stretch
LABEL authors="Griffin Saiia"

COPY docker_config/pip.conf /etc/
COPY docker_config/.pydistutils.cfg /root/

RUN  apt-get update && \
apt-get install -y \
less \ 
unzip \
curl \
wget \
ca-certificates \
git \
gnupg2 \
libssl1.1 \
python3-pip \ 
python3-dev \
python3-setuptools \
apt-utils && \
pip3 install requests && \
#pip3 install pyWinAD && \
pip3 install openstacksdk

RUN mkdir /usr/local/share/ca-certificates/Progressive && \
openssl x509 -inform der -in /opt/scripts/PGR.crt -out /usr/local/share/ca-certificates/Progressive/PGR.crt && \
apt-get update && \
apt-get install apt-utils apt-transport-https -y && \
curl https://packages.microsoft.com/keys/microsoft.asc --insecure | apt-key add - && \
echo "deb [arch=amd64] https://packages.microsoft.com/repos/microsoft-debian-stretch-prod stretch main" > /etc/apt/sources.list.d/microsoft.list && \
update-ca-certificates && \
apt-get update && \
apt-get install powershell -y --no-install-recommends

COPY server_side/index.php /var/www/html/
COPY server_side/api/* /var/www/html/api/
COPY server_side/img/* /var/www/html/img/
COPY server_side/fonts/* /var/www/html/fonts/
COPY server_side/css/* /var/www/html/css/
COPY backend/ /var/www/backend/

RUN chmod +x /var/www/backend/join.ps1 && \
chmod +x /var/www/backend/decrypt.py && \
chmod +x /var/www/backend/updateManifest.py && \
chgrp -R root /var/www/ && \
chmod -R g+rw /var/www/

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN sed -i 's/^Listen 80/Listen 8080/g' /etc/apache2/ports.conf

EXPOSE 8080
ENV PORT 8080
