<?php

namespace App\Models\Enums;

enum FaultCategory: string
{
    use \Praust\App\Models\Concerns\EnumToArray;

    case PRODUCT_DEFECT = 'Wada produktu';
    case INCORRECT_INSTALLATION = 'Nieprawidłowy montaż';
    case IMPROPER_USE = 'Niewłaściwe użytkowanie';
    case MISUNDERSTANDING = 'Brak zrozumienia jednej ze stron';
    case CANNOT_ASSESS = 'Nie można ocenić odpowiedzialności';
}
