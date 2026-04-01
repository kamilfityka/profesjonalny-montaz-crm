<?php namespace App\Models;

class CalendarAttachment extends \Praust\App\Models\PraustActionAttachmentModel
{
	use \Praust\App\Models\Concerns\PraustAttachment;
	use \Praust\App\Models\Concerns\PraustOwner;

	public $fillable = ['calendar_id', 'file', 'language_id'];

    public function hasActive(): bool
    {
        return false;
    }
}
