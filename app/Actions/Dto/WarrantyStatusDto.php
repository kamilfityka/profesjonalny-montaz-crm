<?php declare(strict_types=1);

namespace App\Actions\Dto;

final class WarrantyStatusDto
{
    public const STATUS_UNKNOWN = 'unknown';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';

    public function __construct(
        public readonly string $status,
        public readonly ?int $overdueDays = null,
        public readonly ?int $remainingDays = null,
    ) {
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
