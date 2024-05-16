<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Query;

use Illuminate\Support\Collection;
use Yard\QueryBlock\Block\BlockAttributes;

interface QueryInterface
{
    public function __construct(BlockAttributes $attributes);
    public function get(): Collection;
}
