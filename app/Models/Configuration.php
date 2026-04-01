<?php

namespace App\Models;

use Praust\App\Models\PraustConfiguration;
use Illuminate\Support\Collection;

class Configuration extends PraustConfiguration
{
    public static array $url_pages = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->removeAdminTab(["email", "word", "language", 'dashboard']);
    }

    /**
     * @return Collection
     */
    public static function getAdminTabs(): Collection
    {
        $data = parent::getAdminTabs();
        $data->prepend([
            'group_name' => 'general',
            'name' => 'Kalendarz',
            'class_name' => 'dashboard',
            'childrens' => [],
        ]);
        static::addAdminTab($data, 'project_name', 'Grafik monterów', 'monter');
        static::addAdminTab($data, 'project_name', 'Lista zadań', 'calendar');
        static::addAdminTab($data, 'project_name', 'Baza kontaktów', 'client');
        static::addAdminTab($data, 'project_name', 'Szanse sprzedaży', 'sale');
        static::addAdminTab($data, 'project_name', 'Procesy', 'process');
        static::addAdminTab($data, 'project_name', 'Zgłoszenia', 'reclamation');
        static::addAdminTab($data, 'project_name', 'Dokumenty', 'document');
        static::addAdminTab($data, 'project_name', 'Statystyki', 'statistic');

        static::addAdminTab($data, 'settings', 'Kalendarz', 'calendar-category');
        static::addAdminTab($data, 'settings', 'Procesy', 'process-type');
        static::addAdminTab($data, 'settings', 'Zgłoszenia', 'reclamation-type');
        static::addAdminTab($data, 'settings', 'Szanse sprzedaży', 'sale-type');
        static::addAdminTab($data, 'settings', 'Dokumenty', 'document-type');
        return $data;
    }
}
