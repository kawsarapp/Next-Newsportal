<?php

declare(strict_types=1);

namespace App\Domain\Portal\Contracts;

interface PortalRepositoryInterface
{
    public function findById(int $id): ?object;

    public function findByDomain(string $domain): ?object;

    public function findByApiKeyHash(string $hash): ?object;

    public function all(): \Illuminate\Support\Collection;

    public function create(array $data): object;

    public function update(int $id, array $data): object;

    public function rotateApiKey(int $portalId): array;

    public function getSetting(int $portalId, string $key, mixed $default = null): mixed;

    public function updateSetting(int $portalId, string $key, mixed $value): bool;

    public function getSettings(int $portalId): array;

    public function getDesignTokens(int $portalId): array;

    public function updateDesignToken(int $portalId, string $key, string $value): bool;

    public function isModuleEnabled(int $portalId, string $module): bool;
}
