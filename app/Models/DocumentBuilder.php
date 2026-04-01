<?php namespace App\Models;

/**
 * @property mixed $text
 */
class DocumentBuilder extends \Praust\App\Models\PraustActionBuilderModel
{
	use \Praust\App\Models\Concerns\PraustBuilder;
	use \Praust\App\Models\Concerns\PraustChildrens;

	public $fillable = ['document_builder_id'];


    public function fields(bool $construct = false): array
    {
        return array_merge_recursive(parent::fields($construct), [
            \Praust\App\Models\Fields\Textarea::make("text")
        ]);
    }
}
