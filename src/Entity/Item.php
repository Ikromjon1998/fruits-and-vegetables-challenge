<?php

namespace App\Entity;

use App\Config\ItemType;
use App\Config\ItemUnit;
use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $name;

    // type is ItemType enumerator
    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ItemType::VALUES, message: 'Choose a valid item type.')]
    private string $type;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?int $quantity = 0;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ItemUnit::VALUES, message: 'Choose a valid item unit.')]
    private ?string $unit = 'g';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }
}
