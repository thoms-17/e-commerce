.PHONY: up down build install require console migrate cc tailwind

## Démarrer les containers
up:
	docker compose up -d

## Arrêter les containers
down:
	docker compose down

## Rebuilder l'image et redémarrer
build:
	docker compose build --no-cache
	docker compose up -d

## Installer les dépendances Composer dans le container
install:
	docker compose exec app composer install

## Ajouter un package Composer, puis rebuilder l'image
## Usage : make require pkg="symfony/something"
require:
	docker compose exec app composer require $(pkg)
	docker compose build
	docker compose up -d

## Lancer une commande Symfony
## Usage : make console cmd="debug:router"
console:
	docker compose exec app php bin/console $(cmd)

## Créer et appliquer les migrations
migrate:
	docker compose exec app php bin/console make:migration
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

## Vider le cache
cc:
	docker compose exec app php bin/console cache:clear

## Compiler Tailwind
tailwind:
	docker compose exec app php bin/console tailwind:build
