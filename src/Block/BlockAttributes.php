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

    public function postTypes(): array
    {
        return collect($this->attributes['postTypes'])
            ->map(fn ($postType) => $postType['value'])
            ->toArray();
    }

    public function limit(): int
    {
        return (int) ($this->attributes['postsPerPage'] ?? 3);
    }

    public function offset(): int
    {
        return (int) ($this->attributes['offset'] ?? 0);
    }

    public function orderBy(): string
    {
        return match ($this->attributes['orderBy']) {
            'date' => 'post_date',
            'title' => 'post_title',
            'menu_order' => 'menu_order',
            'event_date' => 'event_date',
            default => 'post_date',
        };
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

    public function excludeChildPosts(): bool
    {
        return ($this->attributes['enablePostParent'] ?? false) && "only-parents" === $this->attributes['postParentOption'];
    }

    public function onlyChildPostsOfParent(): bool
    {
        return ($this->attributes['enablePostParent'] ?? false)
        && "specific-parent" === $this->attributes['postParentOption'];
    }

    public function parentPostID(): int
    {
        if (empty($this->attributes['postParent'])) {
            return 0;
        }

        return (int) $this->attributes['postParent']['value'];
    }

    public function hasTaxonomyFilter(): bool
    {
        return ($this->attributes['enableTaxonomies'] ?? false) && ! empty($this->taxonomyTerms());
    }

    public function taxonomyTerms(): array
    {
        $taxonomyTerms = $this->attributes['taxonomyTerms'] ?? [];

        if (empty($taxonomyTerms)) {
            return [];
        }

        return $taxonomyTerms;
    }

    public function taxonomyTermSlugs(): array
    {
        $taxonomyTermSlugs = [];
        foreach ($this->taxonomyTerms() as $taxonomy => $terms) {
            $taxonomyTermSlugs[$taxonomy] = array_column($terms, 'value');
        }

        return $taxonomyTermSlugs;
    }

    public function taxonomyRelation(): string
    {
        return $this->attributes['taxonomyRelation'] ?? 'AND';
    }

    public function order(): string
    {
        return Str::lower($this->attributes['order'] ?? 'desc') ;
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
