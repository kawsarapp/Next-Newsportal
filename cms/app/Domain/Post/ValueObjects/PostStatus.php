<?php

declare(strict_types=1);

namespace App\Domain\Post\ValueObjects;

enum PostStatus: string
{
    case Draft            = 'draft';
    case PendingReview    = 'pending_review';
    case Approved         = 'approved';
    case Published        = 'published';
    case Scheduled        = 'scheduled';
    case Archived         = 'archived';
    case ReturnedRevision = 'returned_for_revision';
    case Trashed          = 'trashed';

    /**
     * Valid transitions FROM this status.
     * Enforces the editorial workflow state machine.
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft            => [self::PendingReview, self::Approved, self::Published, self::Scheduled, self::Trashed],
            self::PendingReview    => [self::Approved, self::Published, self::ReturnedRevision, self::Trashed],
            self::ReturnedRevision => [self::PendingReview, self::Draft, self::Trashed],
            self::Approved         => [self::Published, self::Scheduled, self::Trashed],
            self::Scheduled        => [self::Published, self::Draft, self::Trashed],
            self::Published        => [self::Draft, self::Archived, self::Trashed],
            self::Archived         => [self::Draft, self::Trashed],
            self::Trashed          => [self::Draft], // can restore to draft
        };

    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft            => 'খসড়া',
            self::PendingReview    => 'পর্যালোচনা অপেক্ষায়',
            self::Approved         => 'অনুমোদিত',
            self::Published        => 'প্রকাশিত',
            self::Scheduled        => 'নির্ধারিত',
            self::Archived         => 'সংরক্ষিত',
            self::ReturnedRevision => 'সংশোধনের জন্য ফেরত',
            self::Trashed          => 'মুছে ফেলা',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft            => 'gray',
            self::PendingReview    => 'yellow',
            self::Approved         => 'blue',
            self::Published        => 'green',
            self::Scheduled        => 'purple',
            self::Archived         => 'orange',
            self::ReturnedRevision => 'red',
            self::Trashed          => 'red',
        };
    }
}
