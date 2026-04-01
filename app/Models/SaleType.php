<?php namespace App\Models;

use Praust\App\Models\Fields\TextName;

class SaleType extends \Praust\App\Models\PraustActionModel
{
	public array $image = [];
	public $fillable = [];

    public function fields(bool $construct = false): array
    {
        $arr = parent::fields($construct);
        $arr[] = TextName::make("name")->validate("required");
        return $arr;
    }
}
