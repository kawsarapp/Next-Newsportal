<?php

declare(strict_types=1);

namespace App\Domain\Post\ValueObjects;

use Illuminate\Support\Str;

final class Slug
{
    private function __construct(
        private readonly string $value
    ) {}

    public static function fromTitle(string $title): self
    {
        // Handle Bangla + English mixed slugs
        $slug = self::transliterate($title);
        $slug = Str::slug($slug, '-');

        if (empty($slug)) {
            $slug = Str::random(8);
        }

        return new self($slug);
    }

    public static function fromString(string $slug): self
    {
        $slug = Str::slug($slug, '-');

        if (empty($slug)) {
            throw new \InvalidArgumentException('Invalid slug value.');
        }

        return new self($slug);
    }

    /**
     * Basic transliteration for Bangla characters.
     * For production, integrate a proper Bangla romanizer library.
     */
    private static function transliterate(string $text): string
    {
        // Strip HTML tags first
        $text = strip_tags($text);

        // If text contains only ASCII, return as-is for Str::slug
        if (mb_detect_encoding($text, 'ASCII', true)) {
            return $text;
        }

        // For Bangla text, we use a timestamp-based fallback
        // Replace this with a proper Bangla romanizer in production
        // Options: https://github.com/niklasbuschmann/bangla-to-latin
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        if (empty(trim($ascii))) {
            // Pure Bangla — use timestamp suffix to ensure uniqueness
            return 'news-' . now()->format('YmdHis');
        }

        return $ascii;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
