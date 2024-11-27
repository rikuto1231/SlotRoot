FROM php:8.1-apache

# 必要なPHP拡張機能をインストール
RUN docker-php-ext-install pdo_mysql

# Apache の rewrite モジュールを有効化
RUN a2enmod rewrite

# プロジェクトファイルをコピー（パスを修正）
COPY . /var/www/html/

# パーミッションを設定
RUN chown -R www-data:www-data /var/www/html

# Apacheのドキュメントルートを設定
RUN sed -i -e "s/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/Web\/my-app/g" /etc/apache2/sites-enabled/000-default.conf

# デバッグ用：ファイル構造を確認
RUN ls -la /var/www/html/Web/my-app/public/G1-1

EXPOSE 80

CMD ["apache2-foreground"]