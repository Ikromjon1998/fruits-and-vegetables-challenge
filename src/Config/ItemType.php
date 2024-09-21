<?php

namespace App\Config;

enum ItemType: string
{
    case FRUIT = 'fruit';
    case VEGETABLE = 'vegetable';

    public const VALUES = [
        self::FRUIT->value,
        self::VEGETABLE->value,
    ];
}