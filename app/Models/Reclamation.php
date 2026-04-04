<?php namespace App\Models;

use App\Models\Enums\FaultCategory;
use App\Models\Enums\Priority;
use App\Models\Enums\Urgency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property bool $warranty
 * @property string|null $purchase_date
 * @property string|null $fault_description
 * @property string|null $fault_category
 * @property string $urgency
 * @property bool $warranty_expired
 * @property int|null $warranty_days_overdue
 */
class Reclamation extends PraustActionModel
{
	use PraustCategory;
	use PraustAttachments;
    use \App\Models\Concerns\User;
    use \App\Models\Concerns\Client;

	public array $image = [];
	public $fillable = [];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ReclamationType::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ReclamationNote::class)->orderByDesc('created_at');
    }

    public function getWarrantyExpiredAttribute(): bool
    {
        if (!$this->purchase_date) {
            return false;
        }
        return Carbon::parse($this->purchase_date)->addMonths(18)->isPast();
    }

    public function getWarrantyDaysOverdueAttribute(): ?int
    {
        if (!$this->purchase_date) {
            return null;
        }
        $expiryDate = Carbon::parse($this->purchase_date)->addMonths(18);
        if (!$expiryDate->isPast()) {
            return null;
        }
        return (int) $expiryDate->diffInDays(now());
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
        $arr[] = Tinymce::make("text")->label('Opis wady');
        $arr[] = DateTime::make("closed_at")->label('Data zamknięcia');
        $arr[] = Select::make("type_id")->label('Przyczyna')->options($info)->addSelectText();
        $arr[] = Radio::make("priority")->label('Priorytet')->options(Priority::array());
        $arr[] = Radio::make("urgency")->label('Pilność')->options(Urgency::array());
        $arr[] = YesNo::make("warranty")->label('Objęty gwarancją');
        $arr[] = DateTime::make("purchase_date")->label('Data zakupu');
        $arr[] = Tinymce::make("fault_description")->label('Szczegółowy opis usterki');
        $arr[] = Radio::make("fault_category")->label('Kategoria usterki')->options(FaultCategory::array());
        $arr[] = Select::make("user_id")->label('Przypisz do')->options($users)->addSelectText('Brak')->enableSelect2();
        return $arr;
    }
}
