<?php namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Models\Fund;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\Request;

class FundController extends FrontController
{
    public static int $page_id = 11;

    public function getSingle(Request $request)
    {
        $page = Page::with('translation')->find(self::$page_id);
        $fund = (new Fund())->newQuery()->whereHas('translation', fn($query) => $query->where('slug', 'like', $request->route('slug')))->active()->firstOrFail();
        $fund->createVisitLog($request->user());

        $view = view('theme.fund.single')->with('page', $page)->with('fund', $fund);
        if ($request->has('fragment') && $request->input('fragment')) {
            $view = $view->fragmentIf($request->ajax(), $request->input('fragment'));
        }
        return $view;
    }
}
