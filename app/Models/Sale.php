<?php namespace App\Models;

use App\Models\Enums\Priority;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Praust\App\Models\Concerns\PraustAttachments;
use Praust\App\Models\Concerns\PraustCategory;
use Praust\App\Models\Fields\DateTime;
use Praust\App\Models\Fields\Radio;
use Praust\App\Models\Fields\Select;
use Praust\App\Models\Fields\TextInput;
use Praust\App\Models\Fields\TextName;
use Praust\App\Models\Fields\Tinymce;
use Praust\App\Models\Fields\YesNo;
use Praust\App\Models\PraustActionModel;

/**
 * @property array|mixed|string $value
 * @property array|mixed|string $localization
 * @property \DateTime|mixed $closed_at
 * @property int|mixed|string|null $priority
 * @property array|mixed|string $text
 * @property mixed|string $lose_reason
 * @property mixed|string $win_reason
 */
class Sale extends PraustActionModel
{
	use PraustCategory;
	use PraustAttachments;
    use \App\Models\Concerns\User;
    use \App\Models\Concerns\Client;

	public array $image = [];
	public $fillable = [];

    public static array $win_reasons = ['atrakcyjna cena', 'profesjonalne doradztwo i montaż', 'polecenie'];
    public static array $lose_reasons = ['zbyt wysoka cena', 'brak odpowiedniego kontaktu', 'klient ma ofertę od znajomego', 'zbyt późno pomiar', 'inne'];

    public function type(): belongsTo
    {
        return $this->belongsTo(SaleType::class);
    }

    public function hasActive(): bool
    {
        return false;
    }

    public function fields(bool $construct = false): array
    {
        $users = $clients = [];
        if (!$construct) {
            foreach ((new User())->newQuery()->get() as $user) {
                $users[$user->getKey()] = $user->getAdminName();
            }
            foreach ((new Client())->newQuery()->when(!auth()->user()->hasPermission('user-read'), fn($query) => $query->where('user_id', auth()->id()))->get() as $client) {
                $clients[$client->getKey()] = $client->getAdminName();
            }
        }

        $info = [];
        if (!$construct) {
            foreach ((new SaleType())->newQuery()->get() as $model) {
                $info[$model->getKey()] = $model->getAdminName();
            }
        }

        $arr = parent::fields($construct);
        $arr[] = Select::make("client_id")->label('Wybierz klienta')->options($clients)->addSelectText('Brak')->enableSelect2();
        $arr[] = TextName::make("name")->validate("required");
        $arr[] = TextInput::make("value")->label('Wartość szansy sprzedaży');
        $arr[] = YesNo::make("cash_advance")->label('Wpłacona zaliczka');
        $arr[] = TextInput::make("localization")->label('Lokalizacja');
//        $arr[] = DateTime::make("closed_at")->label('Data zamknięcia');
        $arr[] = Select::make("type_id")->label('Typ')->options($info)->addSelectText();
        $arr[] = Radio::make("priority")->label('Priorytet')->options(Priority::array());
        $arr[] = Select::make("user_id")->label('Przypisz do')->options($users)->addSelectText('Brak')->enableSelect2();
        $arr[] = Tinymce::make("text")->label('Opis');
        return $arr;
    }
}
