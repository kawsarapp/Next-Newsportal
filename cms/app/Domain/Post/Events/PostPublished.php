<?php

declare(strict_types=1);

namespace App\Domain\Post\Events;

use App\Domain\Shared\Contracts\DomainEventInterface;

final class PostPublished implements DomainEventInterface
{
    public function __construct(
        public readonly int $postId,
        public readonly int $portalId,
        public readonly int $publishedBy,
        public readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {}

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
