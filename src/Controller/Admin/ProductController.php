<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/products', name: 'app_admin_product_')]
#[IsGranted('ROLE_ADMIN')]
class ProductController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ProductRepository $repo): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form    = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('app_admin_product_index');
        }

        return $this->render('admin/product/form.html.twig', [
            'form'  => $form,
            'title' => 'Nouveau produit',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, ProductRepository $repo, EntityManagerInterface $em): Response
    {
        $product = $repo->find($id);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Produit modifié.');
            return $this->redirectToRoute('app_admin_product_index');
        }

        return $this->render('admin/product/form.html.twig', [
            'form'    => $form,
            'title'   => 'Modifier : ' . $product->getName(),
            'product' => $product,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(int $id, Request $request, ProductRepository $repo, EntityManagerInterface $em): Response
    {
        $product = $repo->find($id);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        if ($this->isCsrfTokenValid('product_delete_' . $id, $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé.');
        }

        return $this->redirectToRoute('app_admin_product_index');
    }
}
