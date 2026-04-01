<?php namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Praust\App\Models\Fields\Select;
use Praust\App\Models\Fields\TextInput;
use Praust\App\Models\Fields\TextName;
use Praust\App\Models\Fields\DateTime;
use Praust\App\Models\Fields\YesNo;
use Praust\App\Models\TableColumns\PraustColumn;
use Praust\App\Models\TableColumns\TextColumn;

/**
 * @property mixed|string $company_name
 * @property mixed|string $nip
 * @property mixed|string $function
 * @property mixed|string $street
 * @property int|mixed $discount
 * @property mixed|string $source
 * @property mixed|string $www
 * @property mixed|string $prefix
 * @property mixed|string $phone
 * @property mixed|string $phone2
 * @property mixed|string $email
 * @property mixed|string $city
 * @property mixed|string $postcode
 */
class Client extends \Praust\App\Models\PraustActionModel
{
	use \Praust\App\Models\Concerns\PraustAttachments;
	use \Praust\App\Models\Concerns\PraustCategory;
    use \App\Models\Concerns\User;

	public array $image = [];
	public $fillable = [];

    public static array $sources = ['Polecenie od Klienta', 'Polecenie od budowlańca', 'Teren', 'Biuro', 'Oferteo', 'Fb', 'Opinie'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->trans['created'] = custom_admin_trans('Nowy kontakt');
        $this->trans['create'] = custom_admin_trans('Dodaj kontakt');
        $this->trans['list'] = custom_admin_trans('Lista kontaktów');
        $this->trans['edited'] = custom_admin_trans('Edycja kontaktu');
    }

    public function hasOrder(): bool
    {
        return false;
    }

    public function hasPreview(): bool
    {
        return true;
    }

    public function reclamations(): hasMany
    {
        return $this->hasMany(Reclamation::class);
    }

    public function processes(): hasMany
    {
        return $this->hasMany(Process::class);
    }

    public function sales(): hasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function calendars(): hasMany
    {
        return $this->hasMany(Calendar::class);
    }

    public function documents(): hasMany
    {
        return $this->hasMany(Document::class);
    }

    public function hasDownload(): bool
    {
        return true;
    }

    public function client(): belongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function clients(): hasMany
    {
        return $this->hasMany(Client::class);
    }

    public function fields(bool $construct = false): array
    {
        $clients = [];
        if (!$construct) {
            $db_clients = (new Client())->newQuery()->when(!auth()->user()->hasPermission('user-read'), fn($query) => $query->where('user_id', auth()->id()))->order()->get();
            foreach($db_clients as $client) {
                $clients[$client->getKey()] = ($client->company_name ? '('.$client->company_name.') ' : '').$client->getName();
            }
        }

        $arr = parent::fields($construct);
        $arr[] = TextInput::make("company_name")->label('Nazwa firmy');
        $arr[] = TextInput::make("nip")->label('NIP');
        $arr[] = TextName::make("name")->validate("required")->label('Imię i nazwisko');
        $arr[] = TextInput::make("function")->label('Funkcja');
        $arr[] = TextInput::make("street")->label('Ulica');
        $arr[] = TextInput::make("postcode")->label('Kod');
        $arr[] = TextInput::make("city")->label('Miasto');
        $arr[] = TextInput::make("email")->label('E-mail');
        $arr[] = TextInput::make("phone")->label('Telefon');
        $arr[] = TextInput::make("phone2")->label('Uwagi');
        $arr[] = DateTime::make("www")->label('Data ostatniego kontaktu');
        $arr[] = TextInput::make("prefix")->label('PREFIX');
        $arr[] = Select::make("source")->options(array_combine(self::$sources, self::$sources))->addSelectText()->enableSelect2()->label('Źródło pozyskania');
        $arr[] = Select::make('client_id')->options($clients)->addSelectText()->enableSelect2()->label('Z polecenia od');
        $arr[] = Select::make("discount")->label('Rabat')->options(range(0, 100));
        return $arr;
    }

    public function nameColumn(): ?PraustColumn
    {
        return TextColumn::make('Firma/Osoba', ($this->company_name?:$this->name).($this->function?' <small>('.$this->function.')</small>':''))->fieldName('name')->sortable()->locked()->default();
    }

    public function additionalTableColumnsCenter(): array
    {
        $arr[] = TextColumn::make('Adres', $this->city.'<br>'.$this->street)->setModel($this)->fieldName('address')->default();
        $arr[] = TextColumn::make('Adres email', $this->email)->setModel($this)->fieldName('email')->default();
        $arr[] = TextColumn::make('Telefon', $this->phone)->setModel($this)->fieldName('phone');
        return $arr;
    }
}
