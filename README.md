# BaksDev Auth Google

[![Version](https://img.shields.io/badge/version-7.3.5-blue)](https://github.com/baks-dev/auth-google/releases)
![php 8.4+](https://img.shields.io/badge/php-min%208.4-red.svg)
[![packagist](https://img.shields.io/badge/packagist-green)](https://packagist.org/packages/baks-dev/auth-google)

Модуль авторизации пользователя в Google

## Установка

``` bash
$ composer require baks-dev/auth-google
```

## Создание приложения

Перейти по ссылке "Client page" https://console.cloud.google.com/auth/clients

1. Если проект не создан - следует создать "Get started";
2. Если проект создан - добавляем клиента "Create client"

## Заполняем данные OAuth 2.0 Client IDs

1. Указать название приложения, электронную почту службы поддержки пользователей;
2. Выбрать Audience "Internal" (для этого предварительно должен быть настроен Google Workspace). Далее следует указать
   контактный адрес электронной почты, согласиться с пользовательской политикой Google API и нажать на кнопку "Create".
3. В настройках приложения создать OAuth путем нажатия на "Create OAuth client ID", выбрать "Web application", указать
   любое имя для OAuth клиента.
4. В Authorized redirect URIs добавить ссылку для редиректа на своё приложение

```https://<DOMAIN>/google/auth```

Нажать на "Create".

## Настройки проекта

Указать параметры GOOGLE_CLIENT_ID (значение Client ID) и GOOGLE_CLIENT_SECRET (Client secret) в .env.

```
GOOGLE_CLIENT_ID=<ClientID>
GOOGLE_CLIENT_SECRET=<ClientSecret>
```

Документация для интеграции доступна по ссылке https://developers.google.com/identity/protocols/oauth2/web-server

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
