<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class IntCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed // @phpstan-ignore-line
    {
        if ($this->isCastableToInt($value)) {
            return (int) $value;
        }

        return Uncastable::create();
    }

    /** @phpstan-assert-if-true int|string|float $value */
    private function isCastableToInt(mixed $value): bool
    {
        return is_numeric($value) && (int)$value == $value;
    }
}
