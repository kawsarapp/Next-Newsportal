<?php

declare(strict_types=1);

namespace App\Domain\Portal\Events;

use App\Domain\Shared\Contracts\DomainEventInterface;

final class ApiKeyRotated implements DomainEventInterface
{
    public function __construct(
        public readonly int    $portalId,
        public readonly string $newKeyPrefix,
        public readonly int    $rotatedBy,
        public readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {}

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
