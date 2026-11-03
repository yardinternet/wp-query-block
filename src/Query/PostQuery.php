<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Query;

use Corcel\Model\Builder\PostBuilder;
use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Post;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Yard\QueryBlock\Block\BlockAttributes;

class PostQuery implements QueryInterface
{
	public function __construct(private readonly BlockAttributes $attributes)
	{
	}

	/**
	 * @return Collection<int, Model>
	 */
	public function get(): Collection
	{
		$query = Post::whereIn('post_status', $this->attributes->postStatus())
			->whereIn('post_type', $this->attributes->postTypes())
			->limit($this->attributes->limit())
			->offset($this->attributes->offset());

		if ($this->isCurrentPostExcluded()) {
			$query->where('ID', '!=', $this->currentPostId());
		}

		if ($this->attributes->hasManualSelection()) {
			$query->whereIn('ID', $this->attributes->manualSelectionPostIDs());
		}

		if ($this->attributes->hasExcludedPosts()) {
			$query->whereNotIn('ID', $this->attributes->excludedPostIDs());
		}

		if ($this->attributes->excludeChildPosts()) {
			$query->where('post_parent', 0);
		}

		if ($this->attributes->onlyChildPostsOfThisParent()) {
			$parentPostID = $this->currentPostId() ?: $this->attributes->parentPostID();
			if (0 === $parentPostID) {
				return collect();
			}
			$query->where('post_parent', $parentPostID);
		}

		if ($this->attributes->onlyChildPostsOfParent()) {
			if ($this->attributes->parentPostID() === 0) {
				return collect();
			}
			$query->where('post_parent', $this->attributes->parentPostID());
		}

		if (true === in_array('tribe_events', $this->attributes->postTypes(), true)) {
			$query = $this->applyEventEndDateFilter($query);
		}

		if (true === in_array('yard-event', $this->attributes->postTypes(), true)) {
			$query = $this->applyYardEventEndDateFilter($query);
		}

		if ($this->attributes->hasTaxonomyFilter()) {
			foreach ($this->attributes->taxonomyTermSlugs() as $taxonomy => $termSlugs) {
				$query->taxonomy($taxonomy, $termSlugs);
			}
		}

		if ($this->attributes->enableConnection()) {
			$query->where(function ($query) {
				$connected = $this->attributes->postConnections();
				foreach ($connected as $connection) {
					$query->orWhere(function ($q) use ($connection) {
						$q->where('post_type', $connection['post_type'])
							->whereHas('meta', function ($metaQuery) use ($connection) {
								$metaQuery
									->where('meta_key', $connection['meta_key'])
									->whereIn('meta_value', $connection['meta_value']);
							});
					});
				}
			});
		}

		$query = $this->order($query);

		/**
		 * Filters the Post Query before it is executed on the database.
		 *
		 * @param  PostBuilder  $query  The query object.
		 * @param  BlockAttributes  $attributes  The block attributes.
		 *
		 * @return PostBuilder The modified query object.
		 */
		$query = apply_filters('yard_query_block_post_query', $query, $this->attributes);

		return $query->get();
	}

	/**
	 * Exclude past tribe events from the query. Allows other post types without '_EventEndDate' filtering.
	 * Recurring events only keep their first occurrence in '_EventEndDate', so TEC's occurrences are used when available.
	 */
	private function applyEventEndDateFilter(PostBuilder $query): PostBuilder
	{
		return $query->where(function ($query) {
			$query->where('post_type', 'tribe_events')
				->when(
					$this->hasTribeOccurrences(),
					fn ($query) => $query->whereExists($this->upcomingTribeOccurrences()),
					fn ($query) => $query->whereHas('meta', function ($metaQuery) {
						$metaQuery->where('meta_key', '_EventEndDate')
							->where('meta_value', '>', (new DateTime())->format('Y-m-d H:i:s'));
					})
				)
				->orWhere('post_type', '!=', 'tribe_events');
		});
	}

	private function hasTribeOccurrences(): bool
	{
		$isActive = ['TEC\Events\Custom_Tables\V1\Provider', 'is_active'];

		return is_callable($isActive) && true === $isActive();
	}

	/**
	 * Occurrences of the current post that have not ended yet. WordPress runs PHP in UTC, hence the UTC columns.
	 */
	private function upcomingTribeOccurrences(): QueryBuilder
	{
		return (new Post())->getConnection()->table('tec_occurrences')
			->whereColumn('tec_occurrences.post_id', 'posts.ID')
			->where('tec_occurrences.end_date_utc', '>', (new DateTime())->format('Y-m-d H:i:s'));
	}

	/**
	 * Exclude past yard events from the query.
	 */
	private function applyYardEventEndDateFilter(PostBuilder $query): PostBuilder
	{
		return $query->where(function ($query) {
			$query->where('post_type', 'yard-event')
				->whereHas('meta', function ($metaQuery) {
					$metaQuery->where('meta_key', 'event_end_date')
						->where('meta_value', '>=', (new DateTime())->format('Y-m-d H:i:s'));
				})
				->orWhere('post_type', '!=', 'yard-event');
		});
	}

	private function order(PostBuilder $query): PostBuilder
	{
		if ($this->attributes->hasManualSelection()
			&& $this->attributes->keepManualSelectionOrder()
			&& $this->attributes->manualSelectionPostIDs()
		) {
			return $query->orderByRaw('FIELD(ID, ' . implode(',', $this->attributes->manualSelectionPostIDs()) . ')');
		}

		if ($this->attributes->hasStickyPost()) {
			$query->orderByRaw('FIELD(ID, ' . $this->attributes->stickyPostID() . ') DESC');
		}

		if ($this->attributes->inRandomOrder()) {
			return $query->inRandomOrder();
		}

		if ('_EventStartDate' === $this->attributes->orderBy() && $this->hasTribeOccurrences()) {
			// Order recurring events by their next occurrence instead of their first.
			return $query->orderBy(
				$this->upcomingTribeOccurrences()->selectRaw('MIN(start_date_utc)'),
				$this->attributes->order()
			);
		}

		if ('_EventStartDate' === $this->attributes->orderBy()) {
			return $query->orderBy(
				PostMeta::select('meta_value')
					->where('meta_key', $this->attributes->orderBy())
					->whereColumn('postmeta.post_id', 'posts.ID'),
				$this->attributes->order()
			);
		}

		if ('_YardEventDate' === $this->attributes->orderBy()) {
			return $query->orderBy(
				PostMeta::select('meta_value')
					->wherein('meta_key', [
						'event_start_date',
						'event_end_date_time',
					])
					->whereColumn('postmeta.post_id', 'posts.ID'),
				$this->attributes->order()
			);
		}

		$query->orderBy($this->attributes->orderBy(), $this->attributes->order());

		if ('menu_order' === $this->attributes->orderBy()) {
			$query->orderBy('post_title', 'ASC'); // Secondary order
		}

		return $query;
	}

	private function isCurrentPostExcluded(): bool
	{
		/**
		 * Filters whether the current post should be excluded from query results.
		 * This applies when the query is not using a manual selection and the current post is not the configured sticky post.
		 */
		$excludeCurrentPost = apply_filters('yard_query_block_exclude_current_post', true);

		if (! $excludeCurrentPost) {
			return false;
		}

		$currentPostId = $this->currentPostId();

		if (0 >= $currentPostId) {
			return false;
		}

		if ($this->attributes->hasManualSelection()) {
			return false;
		}

		if ($this->attributes->hasStickyPost() && $this->attributes->stickyPostID() === $currentPostId) {
			return false;
		}

		return true;
	}

	private function currentPostId(): int
	{
		if (function_exists('get_the_ID') && $id = get_the_ID()) {
			return $id;
		}

		if (function_exists('get_queried_object_id')) {
			return (int) get_queried_object_id();
		}

		return 0;
	}
}
