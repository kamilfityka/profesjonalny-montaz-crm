<?php

namespace App\Models\Enums;

enum DocumentFormat: string
{
    use \Praust\App\Models\Concerns\EnumToArray;

    case TYPE_PIONOWY = 'Pionowy';
    case TYPE_POZIOMY = 'Poziomy';
}
