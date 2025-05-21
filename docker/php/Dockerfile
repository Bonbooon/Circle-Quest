FROM golang:1.15 AS builder
RUN go get -d -v github.com/mailhog/mhsendmail \
  && cd /go/src/github.com/mailhog/mhsendmail/ \
  && GOOS=linux GOARCH=arm64 go build -o mhsendmail .



FROM php:fpm-buster
# タイムゾーン(東京)
ENV TZ Asia/Tokyo
# 色々パッケージとか追加(パッケージの操作管理などに使うツール類？とmysqliパッケージ)
RUN apt-get update \
&& apt-get install -y \
git \
zip \
unzip \
vim \
libpq-dev \
&& docker-php-ext-install pdo_mysql pdo_pgsql mysqli \
&& curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
&& apt-get update \
&& apt-get install -y nodejs
# メール受信用のやつのSMTPサーバーとかポートなどいろいろ設定
COPY --from=builder /go/src/github.com/mailhog/mhsendmail/mhsendmail /usr/local/bin/
RUN chmod +x /usr/local/bin/mhsendmail \
  && echo 'sendmail_path = "/usr/local/bin/mhsendmail --smtp-addr=mailhog:1025"' > /usr/local/etc/php/conf.d/sendmail.ini

COPY src /usr/share/nginx/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /usr/share/nginx/html

# package.jsonとpackage-lock.jsonを先にコピーしてキャッシュを活用
COPY src/package.json src/package-lock.json ./

# npm install (または yarn install)
RUN npm install

# 残りのアプリのコードをコピー
COPY src ./
