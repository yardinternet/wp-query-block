<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services\WPQuery;

use Illuminate\Support\Collection;
use WP_Query;

interface QueryBuilderInterface
{
	public function get(): WP_Query;

	public function find($id): self;

	public function first();

	public function firstOrFail();

	public function as($model): self;

	public function getParameters(): array;

	public function postType(string|array $postType): QueryBuilder;

	public function limit(int $limit): QueryBuilder;

	public function offset(int $offset): QueryBuilder;

	public function orderBy($orderBy, string $order = QueryBuilder::ASC): QueryBuilder;

	public function orderByMeta($metaKey, string $order = QueryBuilder::ASC, string $type = null): QueryBuilder;

	public function whereIdIn(array $ids): QueryBuilder;

	public function whereIdNotIn(array $ids): QueryBuilder;

	public function status(string $status): QueryBuilder;

	public function collect(): Collection;

	public function clone(): QueryBuilder;
}
