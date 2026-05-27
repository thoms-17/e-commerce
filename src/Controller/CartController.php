<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart', name: 'app_cart_')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(CartRepository $cartRepository): Response
    {
        $cart = $cartRepository->findByUser($this->getUser());

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add/{id}', name: 'add', methods: ['POST'])]
    public function add(int $id, Request $request, ProductRepository $productRepository, CartService $cartService): Response
    {
        if (!$this->isCsrfTokenValid('cart_add', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $cartService->addProduct($this->getUser(), $product, $quantity);

        $this->addFlash('success', sprintf('"%s" ajouté au panier.', $product->getName()));

        return $this->redirect($request->headers->get('referer', $this->generateUrl('app_cart_index')));
    }

    #[Route('/remove/{itemId}', name: 'remove', methods: ['POST'])]
    public function remove(int $itemId, Request $request, CartService $cartService): Response
    {
        if (!$this->isCsrfTokenValid('cart_remove_' . $itemId, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $cartService->removeItem($this->getUser(), $itemId);

        $this->addFlash('success', 'Article retiré du panier.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/update/{itemId}', name: 'update', methods: ['POST'])]
    public function update(int $itemId, Request $request, CartService $cartService): Response
    {
        if (!$this->isCsrfTokenValid('cart_update_' . $itemId, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $quantity = (int) $request->request->get('quantity', 1);
        $cartService->updateQuantity($this->getUser(), $itemId, $quantity);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clear(Request $request, CartService $cartService): Response
    {
        if (!$this->isCsrfTokenValid('cart_clear', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $cartService->clearCart($this->getUser());
        $this->addFlash('success', 'Panier vidé.');

        return $this->redirectToRoute('app_cart_index');
    }
}
