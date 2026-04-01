<?php namespace App\Models;

use Praust\App\Models\Fields\Group;
use Praust\App\Models\Fields\TextName;

class SaleCategory extends \Praust\App\Models\PraustActionCategoryModel
{
	use \Praust\App\Models\Concerns\PraustCategoryChildrens;

	public array $image = [];
	public $fillable = [];

    public function fields(bool $construct = false): array
    {
        $arr = parent::fields($construct);
        $arr[] = TextName::make("name")->validate("required");
        $arr[] = Group::make("bgColor")->prefix('#');
        return $arr;
    }

    public function getDefaultListView(): string
    {
        return 'kanban';
    }
}
