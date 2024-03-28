<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services;

use Illuminate\Support\Collection;
use Yard\QueryBlock\Block\Attributes;

interface QuerySourceInterface
{
	public function __construct(Attributes $attributes);
	public function getResults(): Collection;
}
