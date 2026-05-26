<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products', name: 'app_product_')]
class ProductController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(ProductRepository $productRepo, CategoryRepository $categoryRepo): Response
    {
        return $this->render('product/index.html.twig', [
            'products'   => $productRepo->findAll(),
            'categories' => $categoryRepo->findAll(),
        ]);
    }

    #[Route('/category/{slug}', name: 'by_category')]
    public function byCategory(string $slug, ProductRepository $productRepo, CategoryRepository $categoryRepo): Response
    {
        $category = $categoryRepo->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        return $this->render('product/index.html.twig', [
            'products'        => $productRepo->findByCategory($category->getId()),
            'categories'      => $categoryRepo->findAll(),
            'activeCategory'  => $category,
        ]);
    }

    #[Route('/{slug}', name: 'show')]
    public function show(string $slug, ProductRepository $productRepo): Response
    {
        $product = $productRepo->findOneBy(['slug' => $slug]);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
