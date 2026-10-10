<?php

declare(strict_types=1);

namespace App\Domain\Post\Contracts;

use App\Domain\Post\ValueObjects\PostStatus;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    public function findById(int $id): ?object;

    public function findBySlug(string $slug, int $portalId): ?object;

    public function paginate(
        int $portalId,
        int $perPage = 15,
        array $filters = [],
    ): LengthAwarePaginator;

    public function getPublished(
        int $portalId,
        int $limit = 10,
        ?int $categoryId = null,
    ): \Illuminate\Support\Collection;

    public function getBreaking(int $portalId, int $limit = 5): \Illuminate\Support\Collection;

    public function getFeatured(int $portalId, int $limit = 10): \Illuminate\Support\Collection;

    public function getByStatus(int $portalId, PostStatus $status): \Illuminate\Support\Collection;

    public function create(array $data): object;

    public function update(int $id, array $data): object;

    public function updateStatus(int $id, PostStatus $status, int $byUserId): bool;

    public function softDelete(int $id): bool;

    public function restore(int $id): bool;

    public function forceDelete(int $id): bool;

    public function countByStatus(int $portalId): array;

    public function getScheduledDue(): \Illuminate\Support\Collection;
}
