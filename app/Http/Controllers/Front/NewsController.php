<?php namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\Request;

class NewsController extends FrontController
{
    public static int $page_id = 7;

    public function getSingle(Request $request)
    {
        $page = Page::with('translation')->find(self::$page_id);
        $news = (new News())->newQuery()->whereHas('translation', fn($query) => $query->where('slug', 'like', $request->route('slug')))->active()->firstOrFail();
        $news->createVisitLog($request->user());

        $view = view('theme.news.single')->with('page', $page)->with('news', $news);
        if ($request->has('fragment') && $request->input('fragment')) {
            $view = $view->fragmentIf($request->ajax(), $request->input('fragment'));
        }
        return $view;
    }
}
