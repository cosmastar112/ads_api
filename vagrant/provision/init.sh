#!/usr/bin/env bash

#1. Установить пакеты

#Обновить индекс пакетов
apt-get update
#Установить скрипты для добавления и удаления PPA
apt -y install software-properties-common
#Добавить репозиторий apt для установка PHP
add-apt-repository ppa:ondrej/php
#Ещё раз обновить индекс пакетов
apt-get update

#Установить Apache2
apt -y install apache2
#Установить PHP, расширения для PHP и PHP-модуль для Apache
apt -y install php7.4 php7.4-mbstring php7.4-curl php7.4-xml php7.4-xdebug php7.4-sqlite3 php7.4-mysql libapache2-mod-php7.4
#Установить Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#Установить Git, Mysql и Sqlite3
apt -y install git mysql-server sqlite3

#2. Конфигурирование
#Настроить доступность виртуального хоста по имени
echo '127.0.0.1 ads-api.loc' | tee -a /etc/hosts
echo '127.0.0.1 db' | tee -a /etc/hosts
#Создать в корневой директории сервера символическую ссылку на директорию проекта
ln -s /app/web /var/www/ads-api.loc
#Создать конфиг виртуального хоста
echo '<VirtualHost *:80>
  ServerName ads-api.loc
  ServerAdmin webmaster@ads-api.loc
  DocumentRoot /var/www/ads-api.loc
  ErrorLog ${APACHE_LOG_DIR}/error.log
  CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet' | tee -a /etc/apache2/sites-available/ads-api.loc.conf
#Активировать сайт
a2ensite ads-api.loc

#Настроить PHP: показывать ошибки
sed -i 's/display_errors = Off/display_errors = On/g' /etc/php/7.4/apache2/php.ini
sed -i 's/display_startup_errors = Off/display_startup_errors = On/g' /etc/php/7.4/apache2/php.ini

#Настроить PHP: настроить дебагер
echo '
xdebug.mode=debug
xdebug.client_host=localhost
xdebug.client_port=9003
xdebug.idekey="netbeans-xdebug"' | sudo tee -a /etc/php/7.4/mods-available/xdebug.ini

#Создать БД и пользователя, выдать пользователю права на БД
mysql <<< 'CREATE SCHEMA `ads`;'
mysql <<< "USE ads; CREATE USER 'ad-api'@'localhost' IDENTIFIED BY 'password';"
mysql <<< "USE ads; GRANT ALL ON ads.* TO 'ad-api'@'localhost';"

#Применить миграции
cd /app/scripts
php ./migrate-prod
php ./migrate-test

#Перезапустить Apache
apachectl restart