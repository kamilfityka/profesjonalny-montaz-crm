<?php

namespace App\Models\Enums;

enum CalendarType: string
{
    use \Praust\App\Models\Concerns\EnumToArray;

    case TYPE_NOTE = 'Notatka';
    case TYPE_TASK = 'Zadanie';
}
