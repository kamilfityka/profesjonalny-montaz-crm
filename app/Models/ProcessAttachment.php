<?php namespace App\Models;

class ProcessAttachment extends \Praust\App\Models\PraustActionAttachmentModel
{
	use \Praust\App\Models\Concerns\PraustAttachment;
	use \Praust\App\Models\Concerns\PraustOwner;

	public $fillable = ['process_id', 'file', 'language_id'];

    public function hasActive(): bool
    {
        return false;
    }
}
