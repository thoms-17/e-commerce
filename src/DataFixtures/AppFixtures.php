<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['name' => 'Électronique',    'slug' => 'electronique'],
            ['name' => 'Vêtements',       'slug' => 'vetements'],
            ['name' => 'Maison & Jardin', 'slug' => 'maison-jardin'],
            ['name' => 'Sport',           'slug' => 'sport'],
        ];

        $categoryEntities = [];
        foreach ($categories as $data) {
            $category = (new Category())
                ->setName($data['name'])
                ->setSlug($data['slug']);
            $manager->persist($category);
            $categoryEntities[$data['slug']] = $category;
        }

        $products = [
            // Électronique
            ['name' => 'MacBook Pro 14"',      'slug' => 'macbook-pro-14',     'price' => '2499.99', 'stock' => 8,  'category' => 'electronique',  'description' => 'Puce M3 Pro, 18 Go RAM, 512 Go SSD. Performances exceptionnelles pour les professionnels.'],
            ['name' => 'iPhone 16',            'slug' => 'iphone-16',          'price' => '999.00',  'stock' => 25, 'category' => 'electronique',  'description' => 'Puce A18, appareil photo 48 Mpx, écran Super Retina XDR 6,1".'],
            ['name' => 'Sony WH-1000XM5',      'slug' => 'sony-wh-1000xm5',   'price' => '349.00',  'stock' => 15, 'category' => 'electronique',  'description' => "Casque sans fil à réduction de bruit leader du marché. 30h d'autonomie."],
            ['name' => 'iPad Air M2',          'slug' => 'ipad-air-m2',        'price' => '799.00',  'stock' => 12, 'category' => 'electronique',  'description' => 'Écran Liquid Retina 11", puce M2, compatible Apple Pencil Pro.'],
            // Vêtements
            ['name' => 'Veste en cuir noir',   'slug' => 'veste-cuir-noir',    'price' => '289.00',  'stock' => 6,  'category' => 'vetements',     'description' => 'Veste en cuir véritable, coupe slim, doublure en soie.'],
            ['name' => 'Jean Slim 501',        'slug' => 'jean-slim-501',      'price' => '89.90',   'stock' => 40, 'category' => 'vetements',     'description' => 'Jean slim taille haute, tissu stretch confortable, coloris indigo.'],
            ['name' => 'Sneakers Runner X',    'slug' => 'sneakers-runner-x',  'price' => '129.00',  'stock' => 20, 'category' => 'vetements',     'description' => 'Chaussures de running légères, semelle amortissante, mesh respirant.'],
            ['name' => 'Pull mérino laine',    'slug' => 'pull-merino-laine',  'price' => '119.00',  'stock' => 18, 'category' => 'vetements',     'description' => 'Pull 100% laine mérinos, doux et chaud, col rond classique.'],
            // Maison & Jardin
            ['name' => 'Canapé 3 places',      'slug' => 'canape-3-places',    'price' => '1199.00', 'stock' => 3,  'category' => 'maison-jardin', 'description' => 'Canapé tissu velours anthracite, pieds bois massif, assise confortable.'],
            ['name' => 'Lampe de bureau LED',  'slug' => 'lampe-bureau-led',   'price' => '49.90',   'stock' => 35, 'category' => 'maison-jardin', 'description' => 'Lampe LED avec contrôle tactile, température de couleur réglable, port USB-C.'],
            ['name' => 'Robot de cuisine',     'slug' => 'robot-cuisine',      'price' => '449.00',  'stock' => 10, 'category' => 'maison-jardin', 'description' => 'Robot multifonction 1500W, 12 accessoires inclus, bol inox 5L.'],
            ['name' => 'Plaid alpaga',         'slug' => 'plaid-alpaga',       'price' => '79.00',   'stock' => 22, 'category' => 'maison-jardin', 'description' => 'Plaid en alpaga naturel, 130x170cm, lavable en machine.'],
            // Sport
            ['name' => 'Vélo de route Carbone','slug' => 'velo-route-carbon',  'price' => '3200.00', 'stock' => 4,  'category' => 'sport',         'description' => 'Cadre carbone T800, groupe Shimano 105, roues aéro 50mm.'],
            ['name' => 'Tapis de yoga Pro',    'slug' => 'tapis-yoga-pro',     'price' => '69.00',   'stock' => 30, 'category' => 'sport',         'description' => 'Tapis antidérapant 6mm, matière naturelle, dimensions 183x61cm.'],
            ['name' => 'Haltères réglables',   'slug' => 'halteres-reglables', 'price' => '199.00',  'stock' => 9,  'category' => 'sport',         'description' => "Paire d'haltères 2-24kg, système de réglage rapide, compact."],
            ['name' => 'Montre GPS sport',     'slug' => 'montre-gps-sport',   'price' => '299.00',  'stock' => 14, 'category' => 'sport',         'description' => 'GPS intégré, suivi cardiaque, 50+ modes sport, autonomie 14 jours.'],
        ];

        foreach ($products as $data) {
            $product = (new Product())
                ->setName($data['name'])
                ->setSlug($data['slug'])
                ->setPrice($data['price'])
                ->setStock($data['stock'])
                ->setDescription($data['description'])
                ->setCategory($categoryEntities[$data['category']]);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
