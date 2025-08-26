<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;

use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * @template TData of BaseData
 */
class IntCast implements Cast
{
	/**
	 * @param array<string> $properties
	 * @param CreationContext<TData> $context
	 */
	public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): int|Uncastable
	{
		try {
			Assert::integerish($value);
		} catch (Throwable $e) {
			return Uncastable::create();
		}

		return (int) $value;
	}
}
