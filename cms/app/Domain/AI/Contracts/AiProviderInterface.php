<?php

declare(strict_types=1);

namespace App\Domain\AI\Contracts;

interface AiProviderInterface
{
    /**
     * Generate multiple headline options from article content.
     *
     * @param  string  $content  Article body (Bangla or English)
     * @param  int     $count    Number of headline options to return
     * @return string[]
     */
    public function generateHeadlines(string $content, int $count = 5): array;

    /**
     * Generate a brief summary / excerpt.
     *
     * @param  string  $content  Full article body
     * @param  int     $maxWords Maximum word count for summary
     */
    public function summarize(string $content, int $maxWords = 50): string;

    /**
     * Generate a platform-specific social media caption.
     *
     * @param  string  $content   Article body or summary
     * @param  string  $platform  facebook|instagram|x|telegram|youtube
     */
    public function generateSocialCaption(string $content, string $platform): string;

    /**
     * Translate content between Bangla and English.
     *
     * @param  string  $text       Source text
     * @param  string  $targetLang 'bn' or 'en'
     */
    public function translate(string $text, string $targetLang): string;

    /**
     * Check Bangla grammar and spelling.
     *
     * @return array{errors: array, suggestions: array, score: int}
     */
    public function checkGrammar(string $banglaText): array;

    /**
     * Suggest SEO improvements for the given content.
     *
     * @return array{keywords: array, suggestions: array, readability_score: int}
     */
    public function suggestSeoImprovements(string $title, string $content): array;

    /**
     * Convert audio/video transcript to a structured news draft.
     */
    public function transcriptToNewsDraft(string $transcript): array;

    /**
     * Return provider name and model for logging.
     */
    public function providerName(): string;
    public function modelName(): string;
}
