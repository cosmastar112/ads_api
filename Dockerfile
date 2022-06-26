FROM php:7.4.29-apache
#использовать конфиг PHP для разработки
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
#скрипт для удобной установки PHP-расширений
RUN curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql mysqli

#включить PHP-расширения
RUN docker-php-ext-enable pdo_mysql mysqli

WORKDIR /app

#Создать в корневой директории сервера символическую ссылку на директорию проекта
RUN ln -s /app/web /var/www/ads-api.loc
#Создать конфиг виртуального хоста
COPY docker/apache/ads-api.loc.conf /etc/apache2/sites-available/ads-api.loc.conf
#Активировать сайт
RUN a2ensite ads-api.loc

RUN apachectl restart

EXPOSE 8080