<?php declare(strict_types=1);

namespace App\Actions;

use App\Models\Process;
use App\Models\ProcessAttachment;
use App\Models\Sale;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConvertSaleToProcess
{
    public function execute(Sale $sale, string $winReason): Process
    {
        $sale->win_reason = $winReason;
        $sale->save();

        $process = new Process();
        $process->fill($sale->toArray());
        $process->type()->disassociate();
        $process->category()->associate(1);
        $process->save();

        foreach ($sale->admin_attachments as $attachment) {
            $clone = new ProcessAttachment();
            $clone->fill($attachment->toArray());
            $process->admin_attachments()->save($clone);

            $sourceDir = Storage::path(
                Str::snake($sale->getModelName()) . '/' . $sale->getKey() . '/attachments/' . $attachment->getKey()
            );

            if (File::exists($sourceDir)) {
                $targetDir = Storage::path(
                    Str::snake($process->getModelName()) . '/' . $process->getKey() . '/attachments/' . $clone->getKey()
                );
                File::copyDirectory($sourceDir, $targetDir);
            }
        }

        $process->save();
        $sale->delete();

        return $process;
    }
}
