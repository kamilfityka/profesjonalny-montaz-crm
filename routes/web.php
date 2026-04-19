<?php

use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ReclamationController;
use App\Http\Controllers\Admin\SaleController;

app('router')->group(['middleware' => 'web'], function () {
    app('router')->group(['prefix' => config('praust.admin_path'), 'middleware' => 'auth'], function () {
        generateDefaultRoute('calendar', function() {
            app('router')->post('/{id}/move', [CalendarController::class, 'postMove'])->name('calendar-move');
        });
        generateDefaultRoute('calendar-category');
        generateDefaultRoute('client');
        generateDefaultRoute('document', function() {
            app('router')->get('/get-pdf/{id}', [DocumentController::class, 'getPdf'])->name('document-pdf');
        });
        generateDefaultRoute('document-type');
        generateDefaultRoute('process');
        generateDefaultRoute('process-type');
        generateDefaultRoute('reclamation', function() {
            app('router')->get('/{id}/protocol-pdf', [ReclamationController::class, 'getProtocolPdf'])->name('reclamation-protocol-pdf');
        });
        generateDefaultRoute('reclamation-type');
        generateDefaultRoute('monter');
        generateDefaultRoute('sale', function() {
            app('router')->get('/win/{id}', [SaleController::class, 'getWin'])->name('sale-win');
            app('router')->get('/lose/{id}', [SaleController::class, 'getLose'])->name('sale-lose');
        });
        generateDefaultRoute('sale-type');
        generateDefaultRoute('statistic', function() {
            app('router')->get('/pdf', 'getPDF')->name('statistic-pdf');
        });
    });
});
