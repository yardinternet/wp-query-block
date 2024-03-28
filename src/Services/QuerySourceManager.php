<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services;

use Yard\QueryBlock\Block\Attributes;

class QuerySourceManager
{
	public static function getService(Attributes $attributes): QuerySourceInterface
	{
		return match ($attributes->getSelectedSource()) {
			'wp' => new WPQuerySourceFetcher($attributes),
			default => new WPQuerySourceFetcher($attributes),
		};
	}
}
