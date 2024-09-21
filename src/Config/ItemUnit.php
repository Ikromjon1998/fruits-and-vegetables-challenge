<?php

namespace App\Config;

enum ItemUnit: string
{
    case KG = 'kg';
    case G = 'g';

    public const VALUES = [
        self::KG->value,
        self::G->value,
    ];
}