<?php declare(strict_types=1);

namespace App\Models\Enums;

enum ReclamationSource: string
{
    use \Praust\App\Models\Concerns\EnumToArray;

    case MANUAL = 'manual';
    case WP_FORM = 'wp_form';
}
