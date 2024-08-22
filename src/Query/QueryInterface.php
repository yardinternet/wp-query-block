<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Query;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Yard\QueryBlock\Block\BlockAttributes;

interface QueryInterface
{
    public function __construct(BlockAttributes $attributes);
    /**
     * @return Collection<int, Model>
     */
    public function get(): Collection;
}
