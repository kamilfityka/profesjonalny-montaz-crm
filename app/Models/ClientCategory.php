<?php namespace App\Models;

use Praust\App\Models\Fields\TextName;
use Praust\App\Models\Fields\YesNo;

class ClientCategory extends \Praust\App\Models\PraustActionCategoryModel
{
	use \Praust\App\Models\Concerns\PraustCategoryChildrens;

	public array $image = [];
	public $fillable = [];

    public function fields(bool $construct = false): array
    {
        $arr = parent::fields($construct);
        $arr[] = TextName::make("name")->validate("required");
        $arr[] = YesNo::make("sale")->label("Do szans sprzedaży");
        return $arr;
    }
}
