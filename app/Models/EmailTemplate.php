<?php

namespace App\Models;

use Praust\App\Models\Fields\TextName;
use Praust\App\Models\Fields\TextInput;
use Praust\App\Models\Fields\Tinymce;
use Praust\App\Models\PraustActionModel;

class EmailTemplate extends PraustActionModel
{
    public array $image = [];
    public $fillable = [];

    public function fields(bool $construct = false): array
    {
        $arr = parent::fields($construct);
        $arr[] = TextName::make("name")->label('Nazwa szablonu')->validate('required');
        $arr[] = TextInput::make("subject")->label('Temat e-maila')->validate('required');
        $arr[] = Tinymce::make("body")->label('Treść e-maila (dostępne placeholdery: {client_name}, {case_number}, {address}, {phone}, {purchase_date})');
        return $arr;
    }
}
