## Пример приложения написанного на Laravel

Проект изначально выполнен в рамках реализации тестового задания, но планируется поддерживаться как демонстрационное приложение-скелет для старта локальной разработки.

## Технологический стек

PHP 8.3, PostgreSQL, Laravel 13, RabbitMQ, Redis

## Установка

Для запуска потребуется установленный docker и утилита docker-compose

```
git clone https://github.com/salvakexx/laravel-example-app your-app
cd your-app
docker compose up -d
```
При первом запуске возможна 502 ошибка, которая должна автоматически уйти после того как отработает команда composer install (может занять несколько минут). 

## Запуск
После установки приложения будут доступны следующие сервисы
- Web-интерфейс [http://localhost/](http://localhost/)
- Панель управления RabbitMQ [http://localhost:15672/](http://localhost:15672/) (user : example, pass : example)
- База-данных *localhost:5432* (user : example, pass : example) 
- Api-документация [http://localhost/docs/api/](http://localhost/docs/api/) (server-api user : example, pass : example) 

## Тестирование
Запуск тестов
```
php vendor/bin/phpunit
```

### Контакты
- kexxdon@gmail.com
- [Telegram](https://t.me/salvakexx)
