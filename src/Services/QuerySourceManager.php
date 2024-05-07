<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services;

use Yard\QueryBlock\Block\BlockAttributes;

class QuerySourceManager
{
    public static function getService(BlockAttributes $attributes): QuerySourceInterface
    {
        return match ($attributes->selectedSource()) {
            'wp' => new WPQuerySourceFetcher($attributes),
            'eloquent' => new EloquentQuerySourceFetcher($attributes),
            default => new WPQuerySourceFetcher($attributes),
        };
    }
}
