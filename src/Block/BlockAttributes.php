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
