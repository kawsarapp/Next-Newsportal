<?php

declare(strict_types=1);

namespace App\Domain\Post\Events;

use App\Domain\Shared\Contracts\DomainEventInterface;

final class PostStatusChanged implements DomainEventInterface
{
    public function __construct(
        public readonly int    $postId,
        public readonly int    $portalId,
        public readonly string $fromStatus,
        public readonly string $toStatus,
        public readonly int    $changedBy,
        public readonly ?string $note = null,
        public readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {}

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
