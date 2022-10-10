FROM php:8-fpm

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    apt-get update && \
    apt-get install -y software-properties-common \
    libpq-dev \
    wget curl \
    unzip \
    libzip-dev \
    zip \
    git \
    nginx sudo \
    net-tools vim && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip && \
    docker-php-ext-install calendar && \
    # Install Postgre PDO
    docker-php-ext-install pdo pdo_pgsql && \
    # enable pgsql
    docker-php-ext-enable pdo pdo_pgsql && \
    # node & yarn
    curl -sL https://deb.nodesource.com/setup_14.x | bash - && \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && apt-get install -y nodejs yarn && \
    curl -sS https://getcomposer.org/installer -o composer-setup.php && php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean && rm -rf /var/lib/apt/lists/*
#RUN docker-php-ext-configure mysql -with-mysql=/usr/local/mysql