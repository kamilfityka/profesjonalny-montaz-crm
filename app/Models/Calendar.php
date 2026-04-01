<?php namespace App\Models;

use App\Models\Enums\Priority;
use App\Models\Enums\CalendarType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Praust\App\Models\Fields\DateTime;
use Praust\App\Models\Fields\Radio;
use Praust\App\Models\Fields\Select;
use Praust\App\Models\Fields\TextName;
use Praust\App\Models\Fields\Tinymce;
use Praust\App\Models\TableColumns\DateAuthorColumn;
use Praust\App\Models\TableColumns\TextColumn;

/**
 * @property int|mixed|string|null $priority
 * @property array|mixed|string $text
 */
class Calendar extends \Praust\App\Models\PraustActionModel
{
	use \Praust\App\Models\Concerns\PraustAttachments;
    use \App\Models\Concerns\User;
    use \App\Models\Concerns\Client;

	public array $image = [];
	public $fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->trans['created'] = custom_admin_trans('Nowy event');
        $this->trans['create'] = custom_admin_trans('Dodaj event');
        $this->trans['list'] = custom_admin_trans('Lista eventów');
        $this->trans['edited'] = custom_admin_trans('Edycja eventu');
    }

    public function hasOrder(): bool
    {
        return false;
    }

    public function category(): belongsTo
    {
        return $this->belongsTo(CalendarCategory::class);
    }

    public function fields(bool $construct = false): array
    {
        $info = [];
        if (!$construct) {
            foreach ((new \App\Models\CalendarCategory())->newQuery()->get() as $model) {
                $info[$model->getKey()] = $model->getAdminName();
            }
        }

        $users = $clients = [];
        if (!$construct) {
            foreach ((new User())->newQuery()->get() as $user) {
                $users[$user->getKey()] = $user->getAdminName();
            }
            foreach ((new Client())->newQuery()->when(!auth()->user()->hasPermission('user-read'), fn($query) => $query->where('user_id', auth()->id()))->get() as $client) {
                $clients[$client->getKey()] = $client->getAdminName();
            }
        }

        $arr = parent::fields($construct);
        $arr[] = Select::make("client_id")->label('Wybierz klienta')->options($clients)->addSelectText('Brak')->enableSelect2();
        $arr[] = Select::make("user_id")->label('Przypisz do')->options($users)->addSelectText('Brak')->enableSelect2();
        $arr[] = TextName::make("name")->validate("required")->label('Zadanie do wykonania');
        $arr[] = DateTime::make("created_at")->label('Data')->value(request()->has('created_at') ? Carbon::parse(request()->input('created_at')) : null);
        $arr[] = Radio::make("type")->label('Typ')->options(CalendarType::array());
        $arr[] = Radio::make("priority")->label('Priorytet')->options(Priority::array());
        $arr[] = Select::make("category_id")->label('Kategoria')->options($info)->addSelectText();
        $arr[] = Tinymce::make("text")->label('Opis');
        return $arr;
    }

    public function additionalTableColumnsCenter(): array
    {
        return [TextColumn::make('Data', $this->created_at?->format("d-m-Y H:i:s"))];
    }

    public function authorColumns(): array
    {
        return [
            DateAuthorColumn::make('Stworzono', $this->{$this->getCreatedAtColumn()})->fieldName($this->getCreatedAtColumn())->sortable()->setModel($this)->author($this->created_by_user)->default(),
            DateAuthorColumn::make('Zmieniono', $this->{$this->getUpdatedAtColumn()})->fieldName($this->getUpdatedAtColumn())->sortable()->setModel($this)->author($this->updated_by_user)
        ];
    }
}
