<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services\WPQuery;

use Fusion\Models\ModelInterface;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use stdClass;
use WP_Post;
use WP_Query;

class QueryBuilder implements QueryBuilderInterface
{
	use Macroable;
	use ForwardsCalls;
	use Traits\BuildsMetaQueryArguments,
		Traits\BuildsTaxonomyQueryArguments,
		Traits\BuildsConnectionQueryArguments;

	protected $model = WP_Post::class;

	protected array $parameters = [
		'post_status' => [],
		'post_type' => [],
	];

	// Order Directions
	const DESC = 'DESC';
	const ASC = 'ASC';

	// Field Types
	const NUMERIC = 'numeric';

	// Logical Operators
	const OR = 'OR';
	const AND = 'AND';

	public function __construct(array $parameters = [])
	{
		$this->parameters = array_merge($this->parameters, $parameters);
		$this->model = $parameters['model'] ?? $this->model;
	}

	/**
	 * Dynamically handle calls into the query instance.
	 *
	 * @return mixed
	 */
	public function __call(string $method, array $parameters)
	{
		$this->forwardCallTo($this->getModel(), $method, $parameters);

		return $this;
	}

	/**
	 * Dynamically handle calls into the query instance.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 *
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic($method, $parameters)
	{
		return static::throwBadMethodCallException($method);
	}

	public function as($model): self
	{
		$this->model = $model;

		return $this;
	}

	/**
	 * Create a new instance of the WP_Query object with the build query arguments.
	 */
	public function get(): WP_Query
	{
		return (new WP_Query($this->getParameters()));
	}

	/**
	 * Get a Collection object of all the posts.
	 */
	public function collect(): Collection
	{
		$posts = $this->get()->posts ?? [];
		// if ($this->hasModelAssociated($this->getModel())) {
		//     $posts = array_map(function ($item) {
		//         return $this->getModel()::makeFrom($item);
		//     }, $posts);
		// }

		return new Collection($posts);
	}

	protected function hasModelAssociated($model): bool
	{
		return (class_exists($model) && (new \ReflectionClass($model))->implementsInterface(ModelInterface::class));
	}

	public function limit(int $limit): self
	{
		$this->parameters['posts_per_page'] = $limit;

		return $this;
	}

	public function orderBy($orderBy, string $order = QueryBuilder::ASC): self
	{
		$order = strtoupper($order);

		$this->parameters['orderby'] = $orderBy;
		$this->parameters['order'] = $order;

		return $this;
	}

	public function orderByMeta($metaKey, string $order = self::ASC, string $type = null): self
	{
		$order = strtoupper($order);

		$this->parameters['orderby'] = (self::NUMERIC === $type ? true : false) ? 'meta_value_num' : 'meta_value';
		$this->parameters['order'] = $order;
		$this->parameters['meta_key'] = $metaKey;

		return $this;
	}

	public function first()
	{
		$this->limit(1);
		$post = $this->get()->post ?? [];
		if ($this->hasModelAssociated($this->getModel()) && (! empty($post))) {
			$post = $this->getModel()::makeFrom($post);
		}

		return $post;
	}

	/**
	 * @throws \Illuminate\Database\RecordsNotFoundException
	 */
	public function firstOrFail()
	{
		$first = $this->first();
		if (empty($first)) {
			throw new RecordsNotFoundException();
		}

		return $first;
	}

	protected function getModel(): string
	{
		return $this->model;
	}

	/**
	 * Get a collection of all posts and their metadata.
	 */
	public function collectWithMeta(): Collection
	{
		return $this->collect()->each(function ($item) {
			$item->meta = $this->getPostMetaObject($item->ID);
		});
	}

	/**
	 * Get post count for current query
	 */
	public function count(): int
	{
		return $this->get()->post_count;
	}

	public function whereIdIn(array $ids): self
	{
		$this->parameters['post__in'] = $ids;

		return $this;
	}

	public function whereIdNotIn(array $ids): self
	{
		$this->parameters['post__not_in'] = $ids;

		return $this;
	}

	/**
	 * Get number of pages for current query
	 */
	public function pageCount(): int
	{
		return $this->get()->max_num_pages;
	}

	/**
	 * Stores the post its meta data in an object.
	 */
	protected function getPostMetaObject(int $id): stdClass
	{
		$class = new stdClass;

		collect(\get_post_meta($id))->each(function ($item, $key) use ($class) {
			if (1 == count($item)) {
				$class->$key = $item[0];

				return;
			}
			$class->$key = $item;
		});

		return $class;
	}

	/**
	 * Get the array of arguments to build the WP_Query object.
	 */
	public function getQueryArguments(): array
	{
		return $this->getParameters();
	}

	public function getParameters(): array
	{
		if (empty($this->parameters['post_type'])) {
			$this->postType('any');
		}

		if (empty($this->parameters['post_status'])) {
			$this->published();
		}

		return $this->parameters;
	}

	public function clone(): self
	{
		$clone = clone $this;

		return $clone;
	}

	/**
	 * Set a key on the querybuilder.
	 */
	public function setQueryArgument($key, $value): self
	{
		$this->parameters[$key] = $value;

		return $this;
	}

	/**
	 * Builds up a meta query.
	 *
	 * @param        $key
	 * @param null   $operator
	 * @param null   $value
	 */
	public function meta($key, $operator = null, $value = null, string $type = 'CHAR'): self
	{
		$this->parameters['meta_query'][] = $this->buildMetaQueryArgument(...func_get_args());

		return $this;
	}

	/**
	 * Sets the relation of the meta query to OR, meaning that only
	 * one of the arguments should be true.
	 */
	public function metaNeedsOneMatch(): self
	{
		$this->parameters['meta_query']['relation'] = 'OR';

		return $this;
	}

	/**
	 * Builds a taxonomy query.
	 *
	 * @param int|string|array $terms
	 */
	public function taxonomy(string $taxonomy, string $field = 'term_id', string $operator = 'IN', $terms = null, bool $children = true): self
	{
		$this->parameters['tax_query'][] = $this->buildTaxonomyQueryArgument(...func_get_args());

		return $this;
	}

	/**
	 * Query a Posts 2 Posts connection.
	 *
	 * @param       $from
	 * @param null  $to
	 */
	public function connection($from, $to = null, array $meta = []): self
	{
		$arguments = call_user_func_array([ $this, 'buildConnectionQueryArgument' ], func_get_args());

		collect($arguments)->each(function ($value, $argument) {
			$this->parameters[$argument] = $value;
		});

		return $this;
	}

	/**
	 * Query a connection of a specific post.
	 *
	 * @param       $post
	 * @param       $from
	 * @param null  $to
	 */
	public function connectionOf($post, $from, $to = null, array $meta = []): self
	{
		$arguments = $this->buildConnectionQueryArgument($from, $to, $meta, $post);

		collect($arguments)->each(function ($value, $argument) {
			$this->parameters[$argument] = $value;
		});

		return $this;
	}

	/**
	 * Add custom arguments to the query.
	 */
	public function custom(array $arguments): self
	{
		foreach ($arguments as $key => $argument) {
			$this->parameters[$key] = $argument;
		}

		return $this;
	}

	/**
	 * Query by post id.
	 *
	 * @param string|int $id
	 */
	public function find($id): self
	{
		$this->parameters['p'] = (int) $id;

		return $this;
	}

	/**
	 * Query by page id.
	 *
	 * @param string|int $id
	 */
	public function findPage($id): self
	{
		$this->parameters['page_id'] = (int) $id;

		return $this;
	}

	/**
	 * Query posts and pages by author ID.
	 *
	 * @param string|int $id
	 */
	public function findAuthor($id): self
	{
		$this->parameters['author'] = (string) $id;

		return $this;
	}

	/**
	 * Query posts/pages by author name.
	 */
	public function author(string $name): self
	{
		$this->parameters['author_name'] = $name;

		return $this;
	}

	/**
	 * Query posts by parent id.
	 *
	 * @param string|int $id
	 */
	public function parent($id): self
	{
		$this->parameters['post_parent'] = (string) $id;

		return $this;
	}

	/**
	 * Query post by slug.
	 */
	public function name(string $slug): self
	{
		$this->parameters['name'] = $slug;

		return $this;
	}

	/**
	 * Query page by slug.
	 */
	public function page(string $slug): self
	{
		$this->parameters['pagename'] = $slug;

		return $this;
	}

	/**
	 * Query certain post types.
	 */
	public function postType(string|array $types): self
	{
		if (! is_array($types)) {
			$this->parameters['post_type'][] = strtolower($types);

			return $this;
		}

		foreach ($types as $type) {
			$this->parameters['post_type'][] = strtolower($type);
		}

		return $this;
	}

	/**
	 * Query published posts.
	 */
	public function published(): self
	{
		$this->parameters['post_status'][] = 'publish';

		return $this;
	}

	/**
	 * Query future posts.
	 */
	public function future(): self
	{
		$this->parameters['post_status'][] = 'future';

		return $this;
	}

	/**
	 * Query drafts.
	 */
	public function isDraft(): self
	{
		$this->parameters['post_status'][] = 'draft';

		return $this;
	}

	/**
	 * Query pending posts.
	 */
	public function pending(): self
	{
		$this->parameters['post_status'][] = 'pending';

		return $this;
	}

	/**
	 * Query private posts.
	 */
	public function isPrivate(): self
	{
		$this->parameters['post_status'][] = 'private';

		return $this;
	}

	/**
	 * Query trashed posts.
	 */
	public function trashed(): self
	{
		$this->parameters['post_status'][] = 'trash';

		return $this;
	}

	/**
	 * Query a custom post status.
	 */
	public function status(string $status): self
	{
		$status = func_get_args();

		if (count($status) === 0) {
			throw new \InvalidArgumentException('`whereStatus` must be called with at least one argument');
		}

		$this->parameters['post_status'][] = count($status) > 1 ? $status : $status[0];

		return $this;
	}

	/**
	 * Query posts/pages with a password.
	 */
	public function hasPassword(bool $bool): self
	{
		$this->parameters['has_password'] = $bool;

		return $this;
	}

	/**
	 * Query posts/pages which have a particular password.
	 */
	public function wherePassword(string $password): self
	{
		$this->hasPassword(true);
		$this->parameters['post_password'] = $password;

		return $this;
	}

	public function offset(int $offset): self
	{
		$this->parameters['offset'] = $offset;

		return $this;
	}

	/**
	 * Search through posts and pages.
	 *
	 * @param $query
	 */
	public function search($query): self
	{
		$this->parameters['s'] = $query;

		return $this;
	}

	/**
	 * Query posts/pages and paginate them by a certain amount, with an optional offset.
	 *
	 * @param int|string      $amount
	 * @param null|int|string $offset
	 */
	public function paginate($amount, $offset = null): self
	{
		$this->parameters['posts_per_page'] = (string) $amount;
		$this->parameters['paged'] = \get_query_var('paged');

		if (! is_null($offset)) {
			$this->parameters['offset'] = (int) $offset;
		}

		return $this;
	}
}
