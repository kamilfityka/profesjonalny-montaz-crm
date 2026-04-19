<?php declare(strict_types=1);

namespace App\Actions;

use App\Models\Enums\ResponsibilityCategory;
use App\Models\Reclamation;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class GenerateReclamationProtocolPdf
{
    public function execute(Reclamation $reclamation): Response
    {
        $responsibilityLabel = null;
        if ($reclamation->responsibility_category) {
            $case = ResponsibilityCategory::tryFrom($reclamation->responsibility_category);
            $responsibilityLabel = $case?->value;
        }

        $pdf = Pdf::loadView('admin.reclamation.pdf.protocol', [
            'reclamation' => $reclamation,
            'responsibilityLabel' => $responsibilityLabel,
        ]);

        $pdf->setOptions([
            'defaultFont' => 'Maisonneue',
            'font_height_ratio' => 1,
            'enable_remote' => true,
        ]);

        return $pdf->download("protokol-reklamacji-{$reclamation->getKey()}.pdf");
    }
}
