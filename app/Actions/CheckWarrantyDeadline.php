<?php declare(strict_types=1);

namespace App\Actions;

use App\Actions\Dto\WarrantyStatusDto;
use App\Models\Reclamation;
use Carbon\CarbonImmutable;

class CheckWarrantyDeadline
{
    private const FREE_ADJUSTMENT_MONTHS = 18;

    public function execute(Reclamation $reclamation, ?CarbonImmutable $now = null): WarrantyStatusDto
    {
        if ($reclamation->purchase_date === null) {
            return new WarrantyStatusDto(WarrantyStatusDto::STATUS_UNKNOWN);
        }

        $now = $now ?? CarbonImmutable::now();
        $deadline = CarbonImmutable::instance($reclamation->purchase_date)->addMonths(self::FREE_ADJUSTMENT_MONTHS);

        if ($now->greaterThan($deadline)) {
            return new WarrantyStatusDto(
                status: WarrantyStatusDto::STATUS_EXPIRED,
                overdueDays: (int) $deadline->diffInDays($now),
            );
        }

        return new WarrantyStatusDto(
            status: WarrantyStatusDto::STATUS_ACTIVE,
            remainingDays: (int) $now->diffInDays($deadline),
        );
    }
}
