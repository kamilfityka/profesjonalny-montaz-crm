<?php namespace App\Models;

class ClientAttachment extends \Praust\App\Models\PraustActionAttachmentModel
{
	use \Praust\App\Models\Concerns\PraustAttachment;
	use \Praust\App\Models\Concerns\PraustOwner;

	public $fillable = ['client_id', 'file', 'language_id'];

    public function hasActive(): bool
    {
        return false;
    }
}
