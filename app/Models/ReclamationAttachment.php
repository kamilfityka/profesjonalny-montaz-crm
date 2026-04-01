<?php namespace App\Models;

class ReclamationAttachment extends \Praust\App\Models\PraustActionAttachmentModel
{
	use \Praust\App\Models\Concerns\PraustAttachment;
	use \Praust\App\Models\Concerns\PraustOwner;

	public $fillable = ['reclamation_id', 'file', 'language_id'];

    public function hasActive(): bool
    {
        return false;
    }
}
