<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
	protected function getPackageProviders($app)
	{
		return [
			LaravelDataServiceProvider::class,
		];
	}
}
