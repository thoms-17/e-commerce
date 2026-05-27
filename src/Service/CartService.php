<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    public function __construct(
        private CartRepository $cartRepository,
        private EntityManagerInterface $em,
    ) {}

    public function getOrCreateCart(User $user): Cart
    {
        $cart = $this->cartRepository->findByUser($user);

        if (!$cart) {
            $cart = new Cart($user);
            $this->em->persist($cart);
            $this->em->flush();
        }

        return $cart;
    }

    public function addProduct(User $user, Product $product, int $quantity = 1): void
    {
        $cart = $this->getOrCreateCart($user);

        // Si le produit est déjà dans le panier, on incrémente la quantité
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $item->setQuantity($item->getQuantity() + $quantity);
                $cart->setUpdatedAt(new \DateTimeImmutable());
                $this->em->flush();
                return;
            }
        }

        // Sinon on crée un nouvel item
        $item = new CartItem($cart, $product, $quantity);
        $cart->addItem($item);
        $this->em->persist($item);
        $this->em->flush();
    }

    public function removeItem(User $user, int $itemId): void
    {
        $cart = $this->cartRepository->findByUser($user);

        if (!$cart) {
            return;
        }

        foreach ($cart->getItems() as $item) {
            if ($item->getId() === $itemId) {
                $cart->removeItem($item);
                $this->em->remove($item);
                $this->em->flush();
                return;
            }
        }
    }

    public function updateQuantity(User $user, int $itemId, int $quantity): void
    {
        $cart = $this->cartRepository->findByUser($user);

        if (!$cart) {
            return;
        }

        foreach ($cart->getItems() as $item) {
            if ($item->getId() === $itemId) {
                if ($quantity <= 0) {
                    $cart->removeItem($item);
                    $this->em->remove($item);
                } else {
                    $item->setQuantity($quantity);
                    $cart->setUpdatedAt(new \DateTimeImmutable());
                }
                $this->em->flush();
                return;
            }
        }
    }

    public function clearCart(User $user): void
    {
        $cart = $this->cartRepository->findByUser($user);

        if (!$cart) {
            return;
        }

        foreach ($cart->getItems() as $item) {
            $this->em->remove($item);
        }

        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }
}
