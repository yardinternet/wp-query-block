<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Block;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use WP_REST_Response;
use Yard\Data\PostData;
use Yard\QueryBlock\Query\PostQuery;
use Yard\QueryBlock\Traits\Helpers;
use Yard\QueryBlock\Traits\VersionRetriever;

class Block
{
	use VersionRetriever;
	use Helpers;

	private const SCRIPT_HANDLE = 'yard-query-block-editor-script';
	private const STYLE_HANDLE = 'yard-query-block-editor-style';

	public function __construct(protected Application $app)
	{
	}

	public function register(): void
	{
		add_filter('block_categories_all', $this->addBlockCategory(...));
		add_action('rest_api_init', $this->registerSettingsRoute(...));
		add_action('admin_enqueue_scripts', $this->enqueueAssets(...));
		add_action('init', $this->registerBlock(...));
	}

	public function registerSettingsRoute(): void
	{
		register_rest_route('yard/query-block/v1', '/settings', [
			'methods' => 'GET',
			'callback' => $this->blockSettings(...),
			'permission_callback' => '__return_true',
		]);
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

		return array_merge($categories, [
			[
				'slug' => 'yard',
				'title' => 'Yard',
			],
		]);
	}

	public function enqueueAssets(): void
	{
		$deps = require __DIR__.'/../../public/index.asset.php';

		wp_register_script(
			handle: self::SCRIPT_HANDLE,
			src: $this->appendToBaseUrl('/yard/query-block/assets/js/index'),
			deps: $deps['dependencies'],
			ver: $this->getVersion(),
		);

		wp_register_style(
			handle: self::STYLE_HANDLE,
			src: $this->appendToBaseUrl('/yard/query-block/assets/css/index'),
			ver: $this->getVersion()
		);
	}

	public function registerBlock(): void
	{
		register_block_type(__DIR__ . '/../../public/block.json', [
			'render_callback' => $this->renderBlock(...),
			'editor_script_handles' => [self::SCRIPT_HANDLE],
			'editor_style_handles' => [self::STYLE_HANDLE],
		]);
	}

	public function blockSettings(): WP_REST_Response
	{
		$path = resource_path('views/vendor/yard-query-block/templates');
		$files = scandir($path);
		$templates = [];

		if (false === $files) {
			return new WP_REST_Response([
				'templates' => [],
			]);
		}

		foreach ($files as $file) {
			if (! str_ends_with($file, '.blade.php')) {
				continue;
			}

			$content = file_get_contents($path . '/' . $file);

			if (false === $content) {
				continue;
			}

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
	 * @param array{} $attributes
	 */
	public function renderBlock(array $attributes): View
	{
		try {
			return $this->render($attributes);
		} catch (Exception) {
			return view('yard-query-block::error');
		}
	}

	/**
	 * @param array{} $attributes
	 */
	private function render(array $attributes): View
	{
		$attributes = BlockAttributes::from($attributes);
		$results = (new PostQuery($attributes))->get();
		$postDataCollection = PostData::collect($results);
		$view = 'yard-query-block::templates.' . $attributes->template();

		return view(view()->exists($view) ? $view : 'yard-query-block::templates.default', [
			'postDataCollection' => $postDataCollection,
			'attributes' => $attributes,
		]);
	}
}
