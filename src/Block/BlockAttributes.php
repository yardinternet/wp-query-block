<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Block;

use Illuminate\Support\Str;

class BlockAttributes
{
    public function __construct(private array $attributes)
    {
    }

    public function template(): string
    {
        return $this->attributes['template'] ?? 'default';
    }

    public function selectedSource(): string
    {
        return 'eloquent';
    }

    public function postTypes(): array
    {
        return collect($this->attributes['postTypes'])
            ->map(fn ($postType) => $postType['value'])
            ->toArray();
    }

    public function limit(): int
    {
        return (int) $this->attributes['postsPerPage'] ?? 3;
    }

    public function offset(): int
    {
        return (int) $this->attributes['offset'] ?? 0;
    }

    public function orderBy(): string
    {
        switch ($this->attributes['orderBy']) {
            case 'date':
                return 'post_date';
            case 'title':
                return 'post_title';
            case 'menu_order':
                return 'menu_order';
            default:
                return 'post_date';
        }
    }

    public function inRandomOrder(): bool
    {
        return 'rand' === $this->attributes['orderBy'];
    }

    public function hasManualSelection(): bool
    {
        return $this->attributes['enableManualSelection'] ?? false;
    }

    public function manualSelectionPostIDs(): array
    {
        if (empty($this->attributes['manualSelectionPosts'])) {
            return [];
        }

        return array_column($this->attributes['manualSelectionPosts'], 'value');
    }

    public function keepManualSelectionOrder(): bool
    {
        return $this->attributes['keepManualSelectionOrder'] ?? false;
    }

    public function hasStickyPost(): bool
    {
        return ($this->attributes['enableStickyPost'] ?? false) && 0 !== $this->stickyPostID();
    }

    public function stickyPostID(): int
    {
        if (empty($this->attributes['stickyPost'])) {
            return 0;
        }

        return (int) $this->attributes['stickyPost']['value'];
    }

    public function hasExcludedPosts(): bool
    {
        return ($this->attributes['enableExcludePosts'] ?? false) && ! empty($this->excludedPostIds());
    }

    public function excludedPostIds(): array
    {
        if (empty($this->attributes['excludePosts'])) {
            return [];
        }

        return array_column($this->attributes['excludePosts'], 'value');
    }

    public function order(): string
    {
        return Str::lower($this->attributes['order']) ?? 'desc';
    }

    public function displayImage(): bool
    {
        return $this->attributes['displayImage'] ?? false;
    }

    public function displayDate(): bool
    {
        return $this->attributes['displayDate'] ?? false;
    }

    public function displayExcerpt(): bool
    {
        return $this->attributes['displayExcerpt'] ?? false;
    }

    public function displayLabel(): bool
    {
        return $this->attributes['displayLabel'] ?? false;
    }

    public function align(): string
    {
        return isset($this->attributes['align']) ? 'align' . $this->attributes['align'] : '';
    }
}
