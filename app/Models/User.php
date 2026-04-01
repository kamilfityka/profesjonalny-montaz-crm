<?php namespace App\Models;

use Praust\App\Models\Fields\YesNo;

class User extends \Praust\App\Models\PraustUser
{
    public function fields(bool $construct = false): array
    {
        $arr = parent::fields($construct);
        $arr[] = YesNo::make("all_see")->label('Widziany przez wszystkich');
        return $arr;
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function calendars()
    {
        return $this->hasMany(Calendar::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function canDestroy(): bool
    {
        if($this->getKey() == 11) {
            return false;
        }
        return parent::canDestroy();
    }
}
