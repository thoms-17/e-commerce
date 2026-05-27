# Symfony Shop — Projet e-commerce

Application e-commerce complète construite avec **Symfony 7.2**, développée comme projet portfolio pour apprendre l'écosystème Symfony. Elle couvre l'ensemble du cycle de vie d'une boutique en ligne : catalogue produits, panier persisté en base de données, gestion des commandes et panel d'administration.

## Stack technique

| Couche | Technologie |
|---|---|
| Framework | Symfony 7.2 |
| PHP | 8.5 (FrankenPHP) |
| Base de données | PostgreSQL 16 |
| Serveur | FrankenPHP (`dunglas/frankenphp`) |
| CSS | Tailwind CSS v4 (`symfonycasts/tailwind-bundle`) |
| Conteneurisation | Docker + Docker Compose |
| ORM | Doctrine ORM 3 |
| Templates | Twig 3 |
| Authentification | Symfony Security Bundle |
| Polices | Inter (Google Fonts) |

## Fonctionnalités

### Boutique
- **Catalogue produits** — liste paginée avec filtre par catégorie, page de détail
- **Panier persisté** — stocké en base de données, synchronisé par utilisateur connecté
- **Commandes** — passage de commande avec validation du stock, snapshot des prix au moment de l'achat, historique des commandes

### Authentification
- Inscription et connexion par email / mot de passe
- Hashage bcrypt automatique
- Accès protégé aux routes `/cart`, `/account`

### Panel d'administration (`/admin`)
- **Dashboard** — KPIs : produits, catégories, commandes, utilisateurs, chiffre d'affaires
- **Produits** — CRUD complet avec génération automatique du slug
- **Catégories** — CRUD complet
- **Commandes** — mise à jour du statut (En attente / Confirmée / Expédiée / Livrée / Annulée)
- **Utilisateurs** — gestion des rôles (promouvoir / rétrograder admin)

### Qualité & sécurité
- Tokens CSRF sur tous les formulaires POST sensibles
- Validation `UniqueEntity` sur les slugs (pas de doublon en BDD)
- Pages d'erreur personnalisées (404, 403, fallback générique)
- Accès admin protégé par `ROLE_ADMIN` sur toutes les routes `/admin`

## Prérequis

- [Docker](https://www.docker.com/) et Docker Compose v2
- Aucune installation PHP ou PostgreSQL locale nécessaire

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/thoms-17/e-commerce.git
cd e-commerce
```

### 2. Créer le fichier de variables d'environnement

```bash
cp docker-compose.override.yml.dist docker-compose.override.yml
```

> Si le fichier `.dist` n'existe pas, créer `docker-compose.override.yml` manuellement :

```yaml
services:
  app:
    environment:
      APP_SECRET: "une-chaine-aleatoire-de-32-caracteres"
      DATABASE_URL: "postgresql://app:app@db:5432/ecommerce?serverVersion=16&charset=utf8"
  db:
    environment:
      POSTGRES_DB: ecommerce
      POSTGRES_USER: app
      POSTGRES_PASSWORD: app
```

### 3. Construire et démarrer les conteneurs

```bash
docker compose build
docker compose up -d
```

### 4. Initialiser la base de données

```bash
# Créer le schéma
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

# Charger les données de démonstration
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

### 5. Compiler les assets CSS

```bash
docker compose exec app php bin/console tailwind:build
```

L'application est accessible sur **http://localhost**

## Compte administrateur (fixtures)

| Champ | Valeur |
|---|---|
| Email | `admin@shop.fr` |
| Mot de passe | `admin123` |

## Commandes utiles

```bash
# Démarrer
docker compose up -d

# Arrêter
docker compose down

# Voir les logs
docker compose logs -f app

# Vider le cache Symfony
docker compose exec app php bin/console cache:clear

# Recompiler Tailwind
docker compose exec app php bin/console tailwind:build

# Créer une migration après modification d'entité
docker compose exec app php bin/console make:migration
docker compose exec app php bin/console doctrine:migrations:migrate

# Réinitialiser les fixtures (efface toutes les données)
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction

# Ouvrir un shell dans le conteneur
docker compose exec app sh
```

## Structure du projet

```
src/
├── Controller/
│   ├── Admin/          # DashboardController, ProductController,
│   │                   # CategoryController, OrderController, UserController
│   ├── CartController.php
│   ├── HomeController.php
│   ├── OrderController.php
│   ├── ProductController.php
│   ├── RegistrationController.php
│   └── SecurityController.php
├── Entity/             # User, Product, Category, Cart, CartItem, Order, OrderItem
├── Enum/               # OrderStatus (Pending, Confirmed, Shipped, Delivered, Cancelled)
├── Form/               # ProductType, CategoryType, RegistrationFormType
├── Repository/         # Un repository par entité
├── Service/
│   └── CartService.php # Logique métier du panier
└── Twig/
    └── CartExtension.php  # Variable globale `cart_count` dans tous les templates

templates/
├── admin/              # Layout sidebar + toutes les vues admin
├── bundles/TwigBundle/
│   └── Exception/      # Pages d'erreur 404, 403, générique
├── cart/
├── home/
├── order/
├── product/
├── registration/
└── security/
```

## Modèle de données

```
User ──────────── Cart ────────── CartItem ──── Product ──── Category
  │                                                │
  └─── Order ──── OrderItem                       │
         (snapshot : productName, unitPrice, qty) │
                                                  └── (stock décrémenté à la commande)
```

- `Cart` et `CartItem` sont effacés après passage de commande
- `OrderItem` est un **snapshot** : il stocke le nom et le prix au moment de l'achat, sans clé étrangère vers `Product`. Cela garantit que l'historique reste exact même si un produit est modifié ou supprimé.

## Développement

### Variables d'environnement

| Variable | Description |
|---|---|
| `APP_SECRET` | Clé secrète Symfony (32+ caractères aléatoires) |
| `DATABASE_URL` | URL de connexion PostgreSQL |
| `APP_ENV` | `dev` en développement, `prod` en production |

### Notes de développement

- **Sessions** : stockées dans `var/sessions/dev/` (persistantes entre redémarrages de conteneurs)
- **Emails** : transport `null://null` — aucun email n'est envoyé, l'inscription active directement le compte
- **Profiler Symfony** : accessible sur `/_profiler` en mode `dev`
- **Migrations** : toujours vérifier le contenu généré par `make:migration` avant d'exécuter
