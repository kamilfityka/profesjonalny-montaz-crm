<?php namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Models\News;
use App\Models\Page;
use App\Models\Reference;
use Illuminate\Http\Request;

class ReferenceController extends FrontController
{
    public static int $page_id = 2;

    public function getSingle(Request $request)
    {
        $page = Page::with('translation')->find(self::$page_id);
        $reference = (new Reference())->newQuery()->whereHas('translation', fn($query) => $query->where('slug', 'like', $request->route('slug')))->active()->firstOrFail();
        $reference->createVisitLog($request->user());

        $view = view('theme.reference.single')->with('page', $page)->with('reference', $reference);
        if ($request->has('fragment') && $request->input('fragment')) {
            $view = $view->fragmentIf($request->ajax(), $request->input('fragment'));
        }
        return $view;
    }
}
