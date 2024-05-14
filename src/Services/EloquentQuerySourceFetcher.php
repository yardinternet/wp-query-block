<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services;

use Corcel\Model\Builder\PostBuilder;
use Illuminate\Support\Collection;
use Yard\QueryBlock\Block\BlockAttributes;
use Yard\QueryBlock\Model\Post;

class EloquentQuerySourceFetcher implements QuerySourceInterface
{
    public function __construct(private BlockAttributes $attributes)
    {
    }

    public function getResults(): Collection
    {
        $query = Post::published()
            ->whereIn('post_type', $this->attributes->postTypes())
            ->limit($this->attributes->limit())
            ->offset($this->attributes->offset());

        if ($this->attributes->isManualSelection()) {
            $query->whereIn('ID', $this->attributes->manualSelectionPostIDs());
        }

        $query = $this->orderQuery($query);

        return $query->get();
    }

    private function orderQuery($query): PostBuilder
    {
        if ($this->attributes->isManualSelection()
        && $this->attributes->keepManualSelectionOrder()
        && $this->attributes->manualSelectionPostIDs()
        ) {
            return $query->orderByRaw('FIELD(ID, ' . implode(',', $this->attributes->manualSelectionPostIDs()) . ')');
        }

        if ($this->attributes->hasStickyPost()
        && $this->attributes->stickyPostID()) {
            $query->orderByRaw('FIELD(ID, ' . $this->attributes->stickyPostID() . ') DESC');
        }


        if ($this->attributes->inRandomOrder()) {
            return $query->inRandomOrder();
        }

        return $query->orderBy($this->attributes->orderBy(), $this->attributes->order());
    }
}
