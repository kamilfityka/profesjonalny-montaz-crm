<?php namespace App\Models;

use Praust\App\Models\Fields\TextName;
use Praust\App\Models\Fields\Tinymce;

/**
 * @property mixed $text
 */
class DocumentType extends \Praust\App\Models\PraustActionModel
{
	public array $image = [];
	public $fillable = [];

    public function fields(bool $construct = false): array
    {
        $arr = parent::fields($construct);
        $arr[] = TextName::make("name")->validate("required");
        $arr[] = Tinymce::make("text");
        return $arr;
    }
}
