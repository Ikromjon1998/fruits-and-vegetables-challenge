<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

abstract class ItemDTO
{
    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $weight;
}
