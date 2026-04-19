<?php declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CheckWarrantyDeadline;
use App\Actions\Dto\WarrantyStatusDto;
use App\Models\Reclamation;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class CheckWarrantyDeadlineTest extends TestCase
{
    public function testReturnsUnknownWhenPurchaseDateIsNull(): void
    {
        $reclamation = new Reclamation();
        $reclamation->purchase_date = null;

        $result = (new CheckWarrantyDeadline())->execute($reclamation);

        $this->assertSame(WarrantyStatusDto::STATUS_UNKNOWN, $result->status);
    }

    public function testReturnsActiveWhenWithin18Months(): void
    {
        $reclamation = new Reclamation();
        $reclamation->purchase_date = CarbonImmutable::parse('2026-01-01');
        $now = CarbonImmutable::parse('2026-06-01');

        $result = (new CheckWarrantyDeadline())->execute($reclamation, $now);

        $this->assertSame(WarrantyStatusDto::STATUS_ACTIVE, $result->status);
        $this->assertGreaterThan(0, $result->remainingDays);
    }

    public function testReturnsExpiredWhenPast18Months(): void
    {
        $reclamation = new Reclamation();
        $reclamation->purchase_date = CarbonImmutable::parse('2024-01-01');
        $now = CarbonImmutable::parse('2026-04-19');

        $result = (new CheckWarrantyDeadline())->execute($reclamation, $now);

        $this->assertSame(WarrantyStatusDto::STATUS_EXPIRED, $result->status);
        $this->assertGreaterThan(0, $result->overdueDays);
    }

    public function testBoundaryAtExactly18Months(): void
    {
        $reclamation = new Reclamation();
        $reclamation->purchase_date = CarbonImmutable::parse('2024-10-19');
        $now = CarbonImmutable::parse('2026-04-19');

        $result = (new CheckWarrantyDeadline())->execute($reclamation, $now);

        $this->assertSame(WarrantyStatusDto::STATUS_ACTIVE, $result->status);
    }
}
