<?php

namespace App\Service;

use App\Entity\Vegetable;

class VegetableCollectionManager extends AbstractCollectionManger
{
    protected function getEntityClass(): string
    {
        return Vegetable::class;
    }
}
