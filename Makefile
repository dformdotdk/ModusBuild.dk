SHELL := /bin/bash

up:
docker compose up -d
docker compose exec app composer install || true

stop:
docker compose down

logs:
docker compose logs -f app

pint:
docker compose exec app ./vendor/bin/pint || true

phpstan:
docker compose exec app ./vendor/bin/phpstan analyse || true

test:
docker compose exec app ./vendor/bin/pest || true

