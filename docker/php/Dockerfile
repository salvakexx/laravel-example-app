FROM php:8.3-fpm-bullseye

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libghc-postgresql-libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libpq-dev \
    librabbitmq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Расширяем PHP модули
RUN docker-php-ext-install \
    bcmath \
    mbstring \
    pdo_mysql \
    pdo \
    pdo_pgsql \
    opcache \
    xml \
    sockets \
    pcntl \
    && pecl install \
    redis \
    amqp \
    && docker-php-ext-enable redis amqp pcntl sockets

# Конфигурация PHP
COPY ./docker/php/conf.d/* /usr/local/etc/php/conf.d/

# Создаём директорию для работы с кодом
WORKDIR /var/www/html

# Копируем .env файл (если существует)
COPY --chown=www-data:www-data .env.example /var/www/html/.env

# Делаем проект доступным для пользователя www-data
RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]
