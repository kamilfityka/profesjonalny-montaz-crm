<?php namespace App\Models;

class SaleAttachment extends \Praust\App\Models\PraustActionAttachmentModel
{
	use \Praust\App\Models\Concerns\PraustAttachment;
	use \Praust\App\Models\Concerns\PraustOwner;

	public $fillable = ['sale_id', 'file', 'language_id'];

    public function hasActive(): bool
    {
        return false;
    }
}
