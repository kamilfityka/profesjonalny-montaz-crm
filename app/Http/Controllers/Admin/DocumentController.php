<?php namespace App\Http\Controllers\Admin;

use App\Models\Builder;
use App\Models\Document;
use App\Models\DocumentBuilder;
use App\Models\DocumentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends \Praust\App\Http\Controllers\Admin\PraustActionController
{
	public string $module_name = 'Dokumenty';

    public function getLoadBuilder(Request $request): JsonResponse
    {
        $type = $request->input('type');
        $k = $request->input('k');

        /** @var Document $model */
        $data = $this->model_name_class;
        if ($request->input('data_id')) {
            $data = ($this->model_name_class)->newQuery()->find($request->input('data_id'));
        }

        /** @var Document $model */
        $model = getClass($this->model_name);

        /** @var DocumentBuilder $item */
        $item = $model->admin_builders()->newModelInstance();

        if(Str::startsWith($type, 'DocumentType')) {
            $explodes = explode(":", $type);
            $item->type = Builder::BUILDER_TEXT;

            /** @var DocumentType $document_type */
            $document_type = (new DocumentType())->newQuery()->find($explodes[1]);
            if($document_type) {
                $item->text = $document_type->text;
            }
        } else {
            $item->type = $type;
        }

        $path = 'praust::admin.builders.section_none';
        if (view()->exists('admin.builders.section_none')) {
            $path = 'admin.builders.section_none';
        }

        $generated_class = new $model->attached_builder;
        $builder_class = $generated_class->getBuilders();
        if ($builder_class[$item->type]['file']) {
            $find_path = 'admin.builders.' . ($builder_class[$item->type]['group'] ? $builder_class[$item->type]['group'] . '.' : '') . $builder_class[$item->type]['file'];
            if (view()->exists($find_path)) {
                $path = $find_path;
            } else if (view()->exists('admin.builders.' . $builder_class[$item->type]['file'])) {
                $path = 'admin.builders.' . $builder_class[$item->type]['file'];
            } else if (view()->exists('praust::' . $find_path)) {
                $path = 'praust::' . $find_path;
            }
        }

        $render = view('praust::admin.builders.layout')
            ->with('this_model_name', $this->model_name)
            ->with('languages', self::$languages)
            ->with(compact('k', 'item', 'path', 'data', 'model', 'builder_class'))
            ->render();
        return response()->json(['data' => $render]);
    }

    public function getPreview(Request $request, mixed $id): mixed
    {
        $this->checkPermission($request, 'read');

        /** @var Document $model */
        $model = $this->model_name_class;

        /** @var Document $data */
        $data = $model->newQuery()->findOrFail($id);

        if (!$data->canPreview()) {
            abort(403);
        }

        return redirect()->to(custom_route('document-pdf', ['id' => $data->getKey()]));
    }

    public function getPdf(Request $request, $id)
    {
        $model = $this->model_name_class;

        /** @var Document $data */
        $data = $model->newQuery()->findOrFail($id);

        $pdf = Pdf::loadView('admin.document.pdf', compact('data'));
        $pdf->setOptions(['defaultFont' => 'Maisonneue', 'font_height_ratio' => 1, 'enable_remote' => true]);
        return $pdf->stream();
    }
}
