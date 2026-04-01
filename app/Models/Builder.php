<?php

namespace App\Models;


use Praust\App\Models\Contracts\PraustBuilderInterface;

class Builder implements PraustBuilderInterface
{
    const BUILDER_TEXT = 0;
    const BUILDER_IMAGE = 1;
    const BUILDER_HR = 2;

    public static function getBuilders()
    {
        return [
            self::BUILDER_TEXT => ['group' => '', 'name' => 'Tekst', 'file' => 'section_text'],
            self::BUILDER_IMAGE => ['group' => '', 'name' => 'Zdjęcie', 'file' => 'section_img'],
            self::BUILDER_HR => ['group' => '', 'name' => 'Wymuszenie podziału strony', 'file' => 'section_hr'],
        ];
    }
}
