<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services;

use Illuminate\Support\Collection;
use Yard\QueryBlock\Block\BlockAttributes;

interface QuerySourceInterface
{
    public function __construct(BlockAttributes $attributes);
    public function getResults(): Collection;
}
