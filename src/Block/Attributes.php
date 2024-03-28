<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Block;

class Attributes
{
	public function __construct(private array $attributes)
	{
	}

	public function getSelectedSource(): string
	{
		return 'wp';
	}

	public function getPostTypes(): array
	{
		return collect($this->attributes['postTypes'])
			->map(fn ($postType) => $postType['value'])
			->toArray();
	}

	public function getLimit(): int
	{
		return (int) $this->attributes['postsPerPage'] ?? 10;
	}
}
