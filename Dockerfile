FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Redis
RUN pecl install redis && docker-php-ext-enable redis

# Configurações PHP otimizadas para performance
RUN echo "upload_max_filesize=40M" >> /usr/local/etc/php/conf.d/performance.ini \
    && echo "post_max_size=40M" >> /usr/local/etc/php/conf.d/performance.ini \
    && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/performance.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/performance.ini \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/performance.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/performance.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/performance.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/performance.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/performance.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/performance.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar composer files e instalar dependências
COPY composer*.json ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Copiar código da aplicação
COPY . .

# Finalizar instalação do Composer
RUN composer dump-autoload --optimize

# Definir permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Expor porta 8000
EXPOSE 8000

# Comando padrão
CMD php artisan serve --host=0.0.0.0 --port=8000
