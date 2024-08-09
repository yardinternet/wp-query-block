<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Query;

use Corcel\Model\Builder\PostBuilder;
use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Yard\QueryBlock\Block\BlockAttributes;

class PostQuery implements QueryInterface
{
    public function __construct(private readonly BlockAttributes $attributes)
    {
    }

    /**
     * @return Collection<int, Model>
     */
    public function get(): Collection
    {
        $query = Post::published()
            ->whereIn('post_type', $this->attributes->postTypes())
            ->limit($this->attributes->limit())
            ->offset($this->attributes->offset());

        if ($this->attributes->hasManualSelection()) {
            $query->whereIn('ID', $this->attributes->manualSelectionPostIDs());
        }

        if ($this->attributes->hasExcludedPosts()) {
            $query->whereNotIn('ID', $this->attributes->excludedPostIDs());
        }

        if ($this->attributes->excludeChildPosts()) {
            $query->where('post_parent', 0);
        }

        if ($this->attributes->onlyChildPostsOfParent()) {
            if ($this->attributes->parentPostID() === 0) {
                return collect();
            }
            $query->where('post_parent', $this->attributes->parentPostID());
        }

        if ($this->attributes->hasTaxonomyFilter()) {
            foreach ($this->attributes->taxonomyTermSlugs() as $taxonomy => $termSlugs) {
                $query->taxonomy($taxonomy, $termSlugs);
            }
        }

        $query = $this->order($query);

        /**
         * Filters the Post Query before it is executed on the database.
         *
         * @param PostBuilder $query The query object.
         * @param BlockAttributes $attributes The block attributes.
         *
         * @return PostBuilder The modified query object.
         */
        $query = apply_filters('yard_query_block_post_query', $query, $this->attributes);

        return $query->get();
    }

    private function order(PostBuilder $query): PostBuilder
    {
        if ($this->attributes->hasManualSelection()
        && $this->attributes->keepManualSelectionOrder()
        && $this->attributes->manualSelectionPostIDs()
        ) {
            return $query->orderByRaw('FIELD(ID, ' . implode(',', $this->attributes->manualSelectionPostIDs()) . ')');
        }

        if ($this->attributes->hasStickyPost()) {
            $query->orderByRaw('FIELD(ID, ' . $this->attributes->stickyPostID() . ') DESC');
        }

        if ($this->attributes->inRandomOrder()) {
            return $query->inRandomOrder();
        }

        return $query->orderBy($this->attributes->orderBy(), $this->attributes->order());
    }
}
