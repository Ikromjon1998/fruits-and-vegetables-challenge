<?php

namespace App\DTO;

use App\Config\ItemType;
use App\Config\ItemUnit;
use Symfony\Component\Validator\Constraints as Assert;

class ItemData
{
    #[Assert\NotBlank]
    public string $name;

    #[Assert\Choice(choices: ItemType::VALUES, message: 'Choose a valid item type.')]
    public string $type;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $quantity;

    #[Assert\Choice(choices: ItemUnit::VALUES, message: 'Choose a valid item unit.')]
    public string $unit;
}
