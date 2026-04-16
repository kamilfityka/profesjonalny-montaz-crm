<?php

namespace App\Models\Enums;

enum Urgency: string
{
    use \Praust\App\Models\Concerns\EnumToArray;

    case URGENT = 'Pilne';
    case NOT_URGENT = 'Niepilne';
}
