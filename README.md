# Hexagone Symfony Blog

## Installation

### Install [Symfony CLI](https://symfony.com/download)

### Install PHP, Composer and required components

Under Fedora:

```bash
sudo dnf install php-cli php-pdo php-mysqlnd composer
```

### Install [Docker](https://www.docker.com)

### Run Database

```bash
docker compose up
```

### Run this in phpMyAdmin (for some reason)

```mysql
CREATE USER 'app4' IDENTIFIED BY 'password';
GRANT ALL ON *.* TO 'app4';
```

### Create Database

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Run Symfony Server

```bash
symfony server:start
```