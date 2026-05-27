<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Cart $cart;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\Column]
    private int $quantity;

    public function __construct(Cart $cart, Product $product, int $quantity = 1)
    {
        $this->cart     = $cart;
        $this->product  = $product;
        $this->quantity = $quantity;
    }

    public function getId(): ?int { return $this->id; }

    public function getCart(): Cart { return $this->cart; }

    public function getProduct(): Product { return $this->product; }

    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getSubtotal(): float
    {
        return (float) $this->product->getPrice() * $this->quantity;
    }
}
