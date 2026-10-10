<?php

declare(strict_types=1);

namespace App\Domain\Portal\ValueObjects;

final class PortalId
{
    public function __construct(
        private readonly int $value
    ) {
        if ($value <= 0) {
            throw new \InvalidArgumentException('PortalId must be a positive integer.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
