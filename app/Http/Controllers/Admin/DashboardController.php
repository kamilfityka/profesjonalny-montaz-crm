<?php namespace App\Http\Controllers\Admin;

use Praust\App\Http\Controllers\Admin\PraustDashboardController;

class DashboardController extends PraustDashboardController
{
    public string $module_name = 'Kalendarz';

    public static function getWidgets(): array
    {
        return [];
    }
}
