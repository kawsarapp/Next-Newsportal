<?php

declare(strict_types=1);

namespace App\Domain\Post\ValueObjects;

enum PostType: string
{
    case Article  = 'article';
    case Video    = 'video';
    case Gallery  = 'gallery';
    case Live     = 'live';
    case Opinion  = 'opinion';
    case Interview = 'interview';
    case Analysis = 'analysis';

    public function label(): string
    {
        return match ($this) {
            self::Article   => 'সংবাদ',
            self::Video     => 'ভিডিও',
            self::Gallery   => 'গ্যালারি',
            self::Live      => 'লাইভ',
            self::Opinion   => 'মতামত',
            self::Interview => 'সাক্ষাৎকার',
            self::Analysis  => 'বিশ্লেষণ',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Article   => 'newspaper',
            self::Video     => 'play-circle',
            self::Gallery   => 'image',
            self::Live      => 'radio',
            self::Opinion   => 'pencil',
            self::Interview => 'mic',
            self::Analysis  => 'chart-bar',
        };
    }
}
