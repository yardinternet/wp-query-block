<?php

declare(strict_types=1);

namespace Services;

namespace Yard\QueryBlock\Services;

use Illuminate\Support\Collection;
use Yard\QueryBlock\Block\BlockAttributes;
use Yard\QueryBlock\Services\WPQuery\QueryBuilder;

class WPQuerySourceFetcher implements QuerySourceInterface
{
    public function __construct(private BlockAttributes $attributes)
    {

    }

    public function getResults(): Collection
    {
        return (new QueryBuilder())
            ->postType($this->attributes->postTypes())
            ->limit($this->attributes->limit())
            ->collect();

    }
}
