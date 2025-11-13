<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Query;

use Corcel\Model\Builder\PostBuilder;
use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Post;
use DateTime;
use Illuminate\Database\Eloquent\Model;
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
			if ($this->attributes->parentPostID() === 0) {
				return collect();
			}
			$query->where('post_parent', $this->attributes->parentPostID());
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
	 */
	private function applyEventEndDateFilter(PostBuilder $query): PostBuilder
	{
		return $query->where(function ($query) {
			$query->where('post_type', 'tribe_events')
				->whereHas('meta', function ($metaQuery) {
					$metaQuery->where('meta_key', '_EventEndDate')
						->where('meta_value', '>', (new DateTime())->format('Y-m-d H:i:s'));
				})
				->orWhere('post_type', '!=', 'tribe_events');
		});
	}

	/**
	 * Exclude past yard events from the query. Allows other post types without '_EventEndDate' filtering.
	 */
	private function applyYardEventEndDateFilter(PostBuilder $query): PostBuilder
	{
		return $query->where(function ($query) {
			$query->where('post_type', 'yard-event')
				->whereHas('meta', function ($metaQuery) {
					$metaQuery->where('meta_key', 'event_end_date')
						->where('meta_value', '>', (new DateTime())->format('Y-m-d H:i:s'));
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
}
