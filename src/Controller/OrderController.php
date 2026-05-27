<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account', name: 'app_order_')]
#[IsGranted('ROLE_USER')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private CartRepository $cartRepository,
        private EntityManagerInterface $em,
    ) {}

    #[Route('/orders', name: 'history', methods: ['GET'])]
    public function history(): Response
    {
        /** @var \App\Entity\User $user */
        $user   = $this->getUser();
        $orders = $this->orderRepository->findByUserOrderedByDate($user);

        return $this->render('order/history.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/orders/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $order = $this->orderRepository->find($id);

        if (!$order || $order->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/order/place', name: 'place', methods: ['POST'])]
    public function place(Request $request, CartService $cartService): Response
    {
        if (!$this->isCsrfTokenValid('order_place', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_cart_index');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $cart = $this->cartRepository->findByUser($user);

        if (!$cart || $cart->getItems()->isEmpty()) {
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_index');
        }

        // Vérifier le stock disponible pour chaque article
        foreach ($cart->getItems() as $cartItem) {
            $product = $cartItem->getProduct();
            if ($product->getStock() < $cartItem->getQuantity()) {
                $this->addFlash('danger', sprintf(
                    'Stock insuffisant pour "%s" (disponible : %d).',
                    $product->getName(),
                    $product->getStock()
                ));
                return $this->redirectToRoute('app_cart_index');
            }
        }

        // Créer la commande (snapshot : on copie nom + prix au moment de la commande)
        $order = new Order($user, (string) $cart->getTotal());

        foreach ($cart->getItems() as $cartItem) {
            $product   = $cartItem->getProduct();
            $orderItem = new OrderItem(
                $order,
                $product->getName(),
                $product->getPrice(),
                $cartItem->getQuantity()
            );
            $order->addItem($orderItem);

            // Décrémenter le stock
            $product->setStock($product->getStock() - $cartItem->getQuantity());
        }

        $this->em->persist($order);
        $this->em->flush();

        // Vider le panier
        $cartService->clearCart($user);

        $this->addFlash('success', 'Votre commande a bien été passée !');

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }
}
