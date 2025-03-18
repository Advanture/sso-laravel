# Запуск проекта #

Есть два способа запустить проект, рекомендую использовать упрощенный вариант, так как в полноценном запуске проекта в докере локально нет смысла

## Упрощенный вариант

1. Скопировать `.env.example` в файл `.env`, заполнить переменные `NATS_HOST`, `NATS_PORT`, `NATS_NKEY`
2. Запустить MariaDB + Redis: `docker-compose up -d`
3. Сгенерировать ключи: `php artisan key:generate`
4. Запустить laravel сервер: `php artisan serve`

## Запуск через Laradock

1. Скопировать `.env.example` в файл `.env`, заполнить переменные `NATS_HOST`, `NATS_PORT`, `NATS_NKEY`
2. В каталоге **laradock** `.env.example` скопировать в файл `.env`
3. Запустить проект: `cd laradock && docker-compose up -d`
