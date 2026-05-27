<?php

namespace App\Twig;

use App\Entity\User;
use App\Repository\CartRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class CartExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private CartRepository $cartRepository,
        private Security $security,
    ) {}

    public function getGlobals(): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return ['cart_count' => 0];
        }

        $cart = $this->cartRepository->findByUser($user);

        return ['cart_count' => $cart ? $cart->getTotalItems() : 0];
    }
}
