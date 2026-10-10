<?php

declare(strict_types=1);

namespace App\Domain\Portal\ValueObjects;

use Illuminate\Support\Str;

final class ApiKey
{
    private const PREFIX = 'pk_live_';
    private const KEY_LENGTH = 40;

    private function __construct(
        private readonly string $prefix,
        private readonly string $hash,
    ) {}

    /**
     * Generate a new API key.
     * Returns ['raw' => string, 'prefix' => string, 'hash' => string]
     * The 'raw' value is shown ONCE to the user. Never store it.
     */
    public static function generate(): array
    {
        $raw    = self::PREFIX . Str::random(self::KEY_LENGTH);
        $prefix = substr($raw, 0, 12);
        $hash   = hash('sha256', $raw);

        return [
            'raw'    => $raw,
            'prefix' => $prefix,
            'hash'   => $hash,
        ];
    }

    /**
     * Verify a raw API key against its stored hash.
     * Uses hash_equals to prevent timing attacks.
     */
    public static function verify(string $raw, string $storedHash): bool
    {
        return hash_equals($storedHash, hash('sha256', $raw));
    }

    public function prefix(): string
    {
        return $this->prefix;
    }

    public function hash(): string
    {
        return $this->hash;
    }
}
