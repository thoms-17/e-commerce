<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Column(length: 255)]
    private string $productName;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $unitPrice;

    #[ORM\Column]
    private int $quantity;

    public function __construct(Order $order, string $productName, string $unitPrice, int $quantity)
    {
        $this->order       = $order;
        $this->productName = $productName;
        $this->unitPrice   = $unitPrice;
        $this->quantity    = $quantity;
    }

    public function getId(): ?int { return $this->id; }

    public function getOrder(): Order { return $this->order; }

    public function getProductName(): string { return $this->productName; }

    public function getUnitPrice(): string { return $this->unitPrice; }

    public function getQuantity(): int { return $this->quantity; }

    public function getSubtotal(): float { return (float) $this->unitPrice * $this->quantity; }
}
