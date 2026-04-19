<?php declare(strict_types=1);

namespace App\Models\Enums;

enum ResponsibilityCategory: string
{
    use \Praust\App\Models\Concerns\EnumToArray;

    case PRODUCT_DEFECT = 'Wada produktu';
    case INCORRECT_INSTALLATION = 'Nieprawidłowy montaż';
    case MISUSE = 'Niewłaściwe użytkowanie';
    case MUTUAL_MISUNDERSTANDING = 'Brak zrozumienia jednej ze stron';
    case UNDETERMINED = 'Nie można ocenić odpowiedzialności';
}
