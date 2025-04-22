<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Block;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Yard\QueryBlock\Casts\IntCast;

/**
 * @phpstan-type LabelValueArray array{
 * 		label: string,
 * 		value: string
 * }
 */
class BlockAttributes extends Data
{
    /**
     * @param array{LabelValueArray}|array{} $postTypes
     * @param LabelValueArray|array{} $manualSelectionPosts
     * @param LabelValueArray|array{} $stickyPost
     * @param LabelValueArray|array{} $excludePosts
     * @param LabelValueArray|array{} $postParent
     * @param array{string: list<LabelValueArray>}|array{} $taxonomyTerms
     */
    public function __construct(
        public array $postTypes = [],
        #[WithCast(IntCast::class)]
        public int $postsPerPage = 3,
        #[WithCast(IntCast::class)]
        public int $offset = 0,
        public string $orderBy = 'date',
        public bool $enableManualSelection = false,
        public array $manualSelectionPosts = [],
        public bool $keepManualSelectionOrder = false,
        public bool $enableStickyPost = false,
        public array $stickyPost = [],
        public bool $enableExcludePosts = false,
        public array $excludePosts = [],
        public bool $enablePostParent = false,
        public string $postParentOption = 'only-parents',
        public array $postParent = [],
        public bool $enableTaxonomies = false,
        public array $taxonomyTerms = [],
        public string $taxonomyRelation = 'AND',
        public string $order = 'desc',
        public bool $displayImage = false,
        public bool $displayDate = false,
        public bool $displayExcerpt = false,
        public bool $displayLabel = false,
        public string $align = '',
        public string $className = '',
        public string $template = 'default',
    ) {
    }

    public function template(): string
    {
        return $this->template;
    }

    /**
     * @return list<string>
     */
    public function postTypes(): array
    {
        return collect($this->postTypes)
            ->map(fn (array $postType): string => $postType['value'])
            ->all();
    }

    public function limit(): int
    {
        return $this->postsPerPage;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function orderBy(): string
    {
        return match ($this->orderBy) {
            'title' => 'post_title',
            'menu_order' => 'menu_order',
            'event_date' => '_EventStartDate',
            default => 'post_date',
        };
    }

    public function inRandomOrder(): bool
    {
        return 'rand' === $this->orderBy;
    }

    public function hasManualSelection(): bool
    {
        return $this->enableManualSelection;
    }

    /**
     * @return list<int>
     */
    public function manualSelectionPostIDs(): array
    {
        if (empty($this->manualSelectionPosts)) {
            return [];
        }

        return array_column($this->manualSelectionPosts, 'value');
    }

    public function keepManualSelectionOrder(): bool
    {
        return $this->keepManualSelectionOrder;
    }

    public function hasStickyPost(): bool
    {
        return $this->enableStickyPost && 0 !== $this->stickyPostID();
    }

    public function stickyPostID(): int
    {
        if (empty($this->stickyPost)) {
            return 0;
        }

        return (int) $this->stickyPost['value'];
    }

    public function hasExcludedPosts(): bool
    {
        return $this->enableExcludePosts && ! empty($this->excludedPostIds());
    }

    /**
     * @return list<int>
     */
    public function excludedPostIds(): array
    {
        if (empty($this->excludePosts)) {
            return [];
        }

        return array_column($this->excludePosts, 'value');
    }

    public function excludeChildPosts(): bool
    {
        return $this->enablePostParent && "only-parents" === $this->postParentOption;
    }

    public function onlyChildPostsOfThisParent(): bool
    {
        return $this->enablePostParent && "current-post-as-parent" === $this->postParentOption;
    }

    public function onlyChildPostsOfParent(): bool
    {
        return $this->enablePostParent && "specific-parent" === $this->postParentOption;
    }

    public function parentPostID(): int
    {
        if (empty($this->postParent)) {
            return 0;
        }

        return (int) $this->postParent['value'];
    }

    public function hasTaxonomyFilter(): bool
    {
        return $this->enableTaxonomies && ! empty($this->taxonomyTerms);
    }

    /**
     * @return array<list<string>>
     */
    public function taxonomyTermSlugs(): array
    {
        $taxonomyTermSlugs = [];
        foreach ($this->taxonomyTerms as $taxonomy => $terms) {
            $taxonomyTermSlugs[$taxonomy] = array_column($terms, 'value');
        }

        return $taxonomyTermSlugs;
    }

    public function taxonomyRelation(): string
    {
        return $this->taxonomyRelation;
    }

    public function order(): string
    {
        return Str::lower($this->order) ;
    }

    public function displayImage(): bool
    {
        return $this->displayImage;
    }

    public function displayDate(): bool
    {
        return $this->displayDate;
    }

    public function displayExcerpt(): bool
    {
        return $this->displayExcerpt;
    }

    public function displayLabel(): bool
    {
        return $this->displayLabel;
    }

    public function align(): string
    {
        return empty($this->align) ? $this->align : 'align' . $this->align;
    }
}
