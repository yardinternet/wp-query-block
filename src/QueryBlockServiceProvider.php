<?php

declare(strict_types=1);

namespace Yard\QueryBlock;

use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use WP_REST_Response;
use Yard\Data\PostData;
use Yard\QueryBlock\Block\BlockAttributes;
use Yard\QueryBlock\Query\PostQuery;
use Yard\QueryBlock\Traits\VersionRetriever;

class QueryBlockServiceProvider extends ServiceProvider
{
    use VersionRetriever;

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'QueryBlock',
        );

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/yard-query-block'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        \add_action('init', [$this, 'registerBlock']);
        \add_filter('block_categories_all', [$this, 'addBlockCategory']);
        \add_action('rest_api_init', function () {
            \register_rest_route('yard/query-block/v1', '/settings', [
                'methods' => 'GET',
                'callback' => $this->blockSettings(...),
                'permission_callback' => '__return_true',
            ]);
        });
    }

    /**
     * Add a custom block category if it doesn't already exist.
     *
     * @param list<array{slug: string, title: string}> $categories
     *
     * @return list<array{slug: string, title: string}>
     */
    public function addBlockCategory(array $categories): array
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

    public function registerBlock(): void
    {
        add_action('admin_enqueue_scripts', function () {
            $deps = require __DIR__.'/../public/index.asset.php';
            wp_register_script('yard-query-block-editor-script', $this->route('/yard/query-block/assets/js/index'), $deps['dependencies'], $this->getVersion(), true);
            wp_register_style('yard-query-block-style', $this->route('/yard/query-block/assets/css/index'), [], $this->getVersion());
        });

        \register_block_type(__DIR__ . '/../public/block.json', [
            'render_callback' => [$this, 'renderBlock'],
            'editor_script_handles' => ['yard-query-block-editor-script'],
            'editor_style_handles' => ['yard-query-block-style'],
        ]);
    }

    public function route(string $path): string
    {
        return config('app.url') . $path;
    }

    public function blockSettings(): WP_REST_Response
    {
        $path = resource_path('views/vendor/yard-query-block/templates');
        $files = scandir($path);
        $templates = [];

        foreach ($files as $file) {
            if (strpos($file, '.blade.php') === false) {
                continue;
            }

            $content = file_get_contents($path . '/' . $file);
            preg_match('/Template: (.*)\n/', $content, $matches);
            $templateName = $matches[1] ?? $file;

            $templates[] = [
                'label' => $templateName,
                'value' => str_replace('.blade.php', '', $file),
            ];
        }

        return new WP_REST_Response([
            'templates' => $templates,
        ]);
    }

    /**
     * @param array<mixed> $attributes
     */
    public function renderBlock(array $attributes): View
    {
        $attributes = new BlockAttributes($attributes);

        $results = (new PostQuery($attributes))->get();

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
