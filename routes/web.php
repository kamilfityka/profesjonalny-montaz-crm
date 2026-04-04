<?php

use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\HelpController;
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
            app('router')->post('/{id}/note', [ReclamationController::class, 'postNote'])->name('reclamation-note');
            app('router')->get('/pdf/{id}', [ReclamationController::class, 'getPdf'])->name('reclamation-pdf');
            app('router')->get('/{id}/email-templates', [ReclamationController::class, 'getEmailTemplates'])->name('reclamation-email-templates');
            app('router')->post('/{id}/send-email', [ReclamationController::class, 'postSendEmail'])->name('reclamation-send-email');
        });
        generateDefaultRoute('reclamation-type');
        generateDefaultRoute('email-template');
        app('router')->get('/help', [HelpController::class, 'index'])->name('help');
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
