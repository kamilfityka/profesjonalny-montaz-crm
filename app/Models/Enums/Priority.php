<?php

namespace App\Models\Enums;

enum Priority: string
{
    use \Praust\App\Models\Concerns\EnumToArray;

    case PRIORITY_LOW = 'Niski';
    case PRIORITY_NORMAL = 'Normalny';
    case PRIORITY_HIGH = 'Pilny';
}
