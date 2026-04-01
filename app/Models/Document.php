<?php namespace App\Models;

use App\Models\Enums\DocumentFormat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Praust\App\Models\Fields\DateTime;
use Praust\App\Models\Fields\Radio;
use Praust\App\Models\Fields\Select;
use Praust\App\Models\Fields\TextName;
use Praust\App\Models\Fields\Tinymce;

/**
 * @property int|mixed|string|null $format
 */
class Document extends \Praust\App\Models\PraustActionModel
{
	use \Praust\App\Models\Concerns\PraustBuilders;
    use \App\Models\Concerns\Client;

	public array $image = [];
	public $fillable = [];

    public function hasPreview(): bool
    {
        return $this->builders->count();
    }

    public function fields(bool $construct = false): array
    {
        $clients = [];
        if (!$construct) {
            foreach ((new Client())->newQuery()->when(!auth()->user()->hasPermission('user-read'), fn($query) => $query->where('user_id', auth()->id()))->get() as $client) {
                $clients[$client->getKey()] = $client->getAdminName();
            }
        }

        $arr = parent::fields($construct);
        $arr[] = TextName::make("name")->validate("required")->label('Numer dokumentu');
        $arr[] = Select::make("client_id")->label('Wybierz klienta')->options($clients)->addSelectText('Brak')->enableSelect2();
        $arr[] = Radio::make("format")->label('Format')->options(DocumentFormat::array());
        return $arr;
    }
}
