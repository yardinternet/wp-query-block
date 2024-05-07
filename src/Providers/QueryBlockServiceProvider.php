<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Providers;

use Illuminate\Support\ServiceProvider;
use Yard\QueryBlock\Block\Attributes;
use Yard\QueryBlock\Console\ExampleCommand;
use Yard\QueryBlock\Example;
use Yard\QueryBlock\Services\QuerySourceManager;
use Yard\QueryBlock\Traits\VersionRetriever;

class QueryBlockServiceProvider extends ServiceProvider
{
    use VersionRetriever;
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(
            __DIR__.'/../../resources/views',
            'QueryBlock',
        );

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/yard-query-block'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        \add_action('init', [$this, 'registerBlock']);
        \add_filter('block_categories_all', [$this, 'addBlockCategory']);
    }

    /**
     * Add a custom block category if it doesn't already exist.
     */
    public function addBlockCategory(array $categories)
    {
        $categories = array_merge($categories, [
            [
                'slug' => 'yard',
                'title' => 'Yard',
            ],
        ]);

        return $categories;
    }

    public function registerBlock()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_register_script('yard-query-block-editor-script', $this->route('/yard/query-block/assets/js/index'), $this->route('/yard/query-block/assets/php/index'), $this->getVersion(), true);
            wp_register_style('yard-query-block-style', $this->route('/yard/query-block/assets/css/index'), [], $this->getVersion());
        });

        \register_block_type(__DIR__ . '/../../public/block.json', [
            'render_callback' => [$this, 'renderBlock'],
            'editor_script_handles' => ['yard-query-block-editor-script'],
            'editor_style_handles' => ['yard-query-block-style'],
        ]);
    }

    public function route($path)
    {
        return config('app.url') . $path;
    }

	public function renderBlock($attributes, $content)
	{
		$attributes = new Attributes($attributes);

		$queryService = QuerySourceManager::getService($attributes);

		$results = $queryService->getResults();

		return view('Query::templates.default', [
			'posts' => $results,
			'attributes' => $attributes,
		]);
	}
}
