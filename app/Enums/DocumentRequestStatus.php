<?php

namespace App\Enums;

enum DocumentRequestStatus: string
{
    case Pending = 'pending';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';
    case ForPickup = 'pickup';
    case Completed = 'completed';

    public static function fromStoredValue(string $value): self
    {
        return self::from($value === 'released' ? self::Completed->value : $value);
    }

    public function label(): string
    {
        return $this === self::ForPickup ? 'For Pickup' : ucfirst($this->value);
    }
}
