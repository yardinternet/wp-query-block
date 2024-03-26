<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Providers;

use Illuminate\Support\ServiceProvider;
use Yard\QueryBlock\Console\ExampleCommand;
use Yard\QueryBlock\Example;
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
		$this->app->singleton('Example', function () {
			return new Example($this->app);
		});

		$this->mergeConfigFrom(
			__DIR__.'/../../config/example.php',
			'example'
		);
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../config/example.php' => $this->app->configPath('example.php'),
		], 'config');

		$this->loadViewsFrom(
			__DIR__.'/../../resources/views',
			'Example',
		);

		$this->commands([
			ExampleCommand::class,
		]);

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
			wp_register_script('yard-query-block-editor-script', get_template_directory_uri().'/vendor/yard/query-block/public/index.js', get_template_directory_uri().'/vendor/yard/query-block/public/index.asset.php', $this->getVersion(), true);
			wp_register_style('yard-query-block-style', get_template_directory_uri().'/vendor/yard/query-block/public/index.css', [], $this->getVersion(), true);
		});

		\register_block_type(__DIR__ . '/../../public/block.json', [
			'render_callback' => [$this, 'renderBlock'],
			'editor_script_handles' => ['yard-query-block-editor-script'],
			'editor_style_handles' => ['yard-query-block-style'],
		]);
	}


	public function renderBlock($attributes, $content)
	{
		return "Hi there!";
	}
}
