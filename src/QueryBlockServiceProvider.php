<?php

declare(strict_types=1);

namespace Yard\QueryBlock;

use Yard\QueryBlock\Block\Block;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class QueryBlockServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
	{
		$package
			->name('yard-query-block')
			->hasViews()
			->hasRoute('web');
	}

	public function packageRegistered(): void
    {
        $this->app->singleton('Block', fn () => new Block($this->app));
    }

    public function packageBooted(): void
    {
		$this->app->make('Block')->register();
    }
}
