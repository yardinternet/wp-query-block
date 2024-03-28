<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Models;

use Illuminate\Support\Collection;

class ModelCollection
{
	public function __construct(private Collection $models)
	{
	}

	public function __get($name): ModelInterface
	{
		return $this->models->pluck($name);
	}
}
