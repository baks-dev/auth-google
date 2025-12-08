# BaksDev Auth Google

[![Version](https://img.shields.io/badge/version-7.3.4-blue)](https://github.com/baks-dev/auth-google/releases)
![php 8.4+](https://img.shields.io/badge/php-min%208.4-red.svg)
[![packagist](https://img.shields.io/badge/packagist-green)](https://packagist.org/packages/baks-dev/auth-google)

Модуль авторизации пользователя в Google

## Установка

``` bash
$ composer require baks-dev/auth-google
```

## Создание приложения
1. Перейти по ссылке "Client page"
   https://console.cloud.google.com/auth/clients
   открыть окно создания нового проекта (кнопка "Get started").
2. Указать название приложения, электронную почту службы поддержки пользователей; выбрать Audience "Internal" (для этого предварительно должен быть настроен Google Workspace). Далее следует указать контактный адрес электронной почты, согласиться с пользовательской политикой Google API и нажать на кнопку "Create".
3. В настройках приложения создать OAuth путем нажатия на "Create OAuth client ID", выбрать "Web application", указать любое имя для OAuth клиента. В Authorized redirect URIs добавить ссылку для редиректа на своё приложение https://.../google/auth (вместо троеточия указать свой домен). Нажать на "Create".
4. Указать параметры GOOGLE_CLIENT_ID (значение Client ID) и GOOGLE_CLIENT_SECRET (Client secret) в .env.
5. Документация для интеграции доступна по ссылке https://developers.google.com/identity/protocols/oauth2/web-server

## Дополнительно

Установка конфигурации и файловых ресурсов:

``` bash
$ php bin/console baks:assets:install
```

Изменения в схеме базы данных с помощью миграции

``` bash
$ php bin/console doctrine:migrations:diff

$ php bin/console doctrine:migrations:migrate
```

## Тестирование

``` bash
$ php bin/phpunit --group=auth-google
```

## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.

