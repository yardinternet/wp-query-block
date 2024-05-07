<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services;

use Corcel\Model\Post;
use Illuminate\Support\Collection;
use Yard\QueryBlock\Block\BlockAttributes;

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

        if ($this->attributes->inRandomOrder()) {
            $query->inRandomOrder();
        } else {
            $query->orderBy($this->attributes->orderBy(), $this->attributes->order());
        }

        $posts = $query->get();

        return collect($posts);
    }
}
