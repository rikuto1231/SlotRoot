FROM php:8.1-apache

# 必要なPHP拡張機能をインストール（MySQLドライバーを追加）
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mysqli

# Apache の rewrite モジュールを有効化
RUN a2enmod rewrite

# プロジェクトファイルをコピー
COPY . /var/www/html/

# Apacheの設定を修正
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom-directory.conf

RUN a2enconf custom-directory

# DocumentRootを設定
RUN sed -i -e "s/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/Web\/my-app/g" /etc/apache2/sites-enabled/000-default.conf

# MySQLの設定を追加
RUN echo "mysql.default_socket=/var/run/mysqld/mysqld.sock" >> /usr/local/etc/php/conf.d/docker-php-ext-mysqli.ini
RUN echo "mysqli.default_socket=/var/run/mysqld/mysqld.sock" >> /usr/local/etc/php/conf.d/docker-php-ext-mysqli.ini

# パーミッションを設定
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]