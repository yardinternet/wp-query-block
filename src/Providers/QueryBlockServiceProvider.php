<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Providers;

use Illuminate\Support\ServiceProvider;
use Yard\Data\PostData;
use Yard\QueryBlock\Block\BlockAttributes;
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
        if (in_array('yard', array_column($categories, 'slug'))) {
            return $categories;
        }

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

    public function renderBlock(array $attributes, $content)
    {
        $attributes = new BlockAttributes($attributes);

        $queryService = QuerySourceManager::getService($attributes);

        $results = $queryService->getResults();

        $postDataCollection = PostData::collect($results);

        $template = $attributes->template();

        if (! view()->exists("vendor.yard-query-block.templates.{$template}") && ! view()->exists("QueryBlock::templates.{$template}")) {
            throw new \Exception("Template {$template} not found.");
        }

        return view()->first(["vendor.yard-query-block.templates.{$template}", "QueryBlock::templates.{$template}"], [
            'postDataCollection' => $postDataCollection,
            'attributes' => $attributes,
        ]);
    }
}
