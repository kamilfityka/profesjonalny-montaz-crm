<?php namespace App\Models;

use App\Models\Enums\Priority;
use App\Models\Enums\ResponsibilityCategory;
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
 * @property \DateTime|mixed $closed_at
 * @property int|mixed|string|null $priority
 * @property array|mixed|string $text
 * @property mixed $address
 * @property mixed $phone
 * @property \Carbon\Carbon|null $purchase_date
 * @property bool $warranty
 * @property string|null $fault_description
 * @property string|null $responsibility_category
 * @property string $source
 */
class Reclamation extends PraustActionModel
{
	use PraustCategory;
	use PraustAttachments;
    use \App\Models\Concerns\User;
    use \App\Models\Concerns\Client;

	public array $image = [];
	public $fillable = [];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty' => 'boolean',
    ];

    public function type(): belongsTo
    {
        return $this->belongsTo(ReclamationType::class);
    }

    public function hasOrder(): bool
    {
        return false;
    }

    public function hasDownload(): bool
    {
        return true;
    }

    public function getOrderDirection(): string
    {
        return 'asc';
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
            foreach ((new ReclamationType())->newQuery()->get() as $model) {
                $info[$model->getKey()] = $model->getAdminName();
            }
        }

        $arr = parent::fields($construct);
        $arr[] = Select::make("client_id")->label('Wybierz klienta')->options($clients)->addSelectText('Brak')->enableSelect2();
        $arr[] = TextName::make("name")->label('Imię i nazwisko');
        $arr[] = TextName::make("phone")->label('Telefon');
        $arr[] = TextName::make("address")->label('Adres budowy');
        $arr[] = DateTime::make("created_at")->label('Data wprowadzenia reklamacji');
        $arr[] = DateTime::make("purchase_date")->label('Data zakupu');
        $arr[] = YesNo::make("warranty")->label('Gwarancja');
        $arr[] = Tinymce::make("text")->label('Opis wady');
        $arr[] = Tinymce::make("fault_description")->label('Szczegółowy opis usterki');
        $arr[] = Radio::make("responsibility_category")->label('Kategoria odpowiedzialności')->options(ResponsibilityCategory::array());
        $arr[] = DateTime::make("closed_at")->label('Data zamknięcia');
        $arr[] = Select::make("type_id")->label('Przyczyna')->options($info)->addSelectText();
        $arr[] = Radio::make("priority")->label('Priorytet')->options(Priority::array());
        $arr[] = Select::make("user_id")->label('Przypisz do')->options($users)->addSelectText('Brak')->enableSelect2();
        return $arr;
    }
}
