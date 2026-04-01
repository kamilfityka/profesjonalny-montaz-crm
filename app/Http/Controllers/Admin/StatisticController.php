<?php namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Praust\App\Http\Controllers\Admin\PraustActionController;

class StatisticController extends PraustActionController
{
    public string $module_name = 'Statystyki';

    public function getIndex(Request $request): mixed
    {
        $range = $request->input('range', 'year');
        if($range == 'range') {
            $date = Carbon::parse($request->input('date', Carbon::today()->subMonth()));
            $date_end = Carbon::parse($request->input('date_end', Carbon::today()));
        } else {
            $date = Carbon::parse($request->input('date', Carbon::today()));
            $date_end = Carbon::parse($request->input('date_end', Carbon::today()->addYear()));
        }

        return view('admin.statistic.index')->with(compact('date', 'date_end', 'range'));
    }

    public function getPDF(Request $request)
    {
        $range = $request->input('range', 'year');
        if($range == 'range') {
            $date = Carbon::parse($request->input('date', Carbon::today()->subMonth()));
            $date_end = Carbon::parse($request->input('date_end', Carbon::today()));
        } else {
            $date = Carbon::parse($request->input('date', Carbon::today()));
            $date_end = Carbon::parse($request->input('date_end', Carbon::today()->addYear()));
        }

        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('admin.statistic.pdf', compact('date', 'date_end', 'range'));
        $pdf->setOptions(['defaultFont' => 'Maisonneue', 'font_height_ratio' => 1, 'enable_remote' => true]);
        $pdf->setWarnings(true);
        return $pdf->stream();
    }
}
