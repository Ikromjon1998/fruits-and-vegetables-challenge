<?php

namespace App\Service;

use App\Entity\Fruit;

class FruitCollectionManager extends AbstractCollectionManger
{
    protected function getEntityClass(): string
    {
        return Fruit::class;
    }
}
