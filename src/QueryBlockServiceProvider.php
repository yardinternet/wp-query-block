<?php

declare(strict_types=1);

namespace Yard\QueryBlock;

use Yard\QueryBlock\Block\Block;
use Illuminate\Support\ServiceProvider;

class QueryBlockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'yard-query-block',
        );

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/yard-query-block'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

		(new Block())->register();
    }
}
