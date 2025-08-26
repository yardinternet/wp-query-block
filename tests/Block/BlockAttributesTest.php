<?php

declare(strict_types=1);

use Yard\QueryBlock\Block\BlockAttributes;

it('returns the default template', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->template())->toBe('default');
});

it('returns the selected template', function () {
	$attributes = BlockAttributes::from(['template' => 'custom']);
	expect($attributes->template())->toBe('custom');
});

it('returns the post types', function () {
	$attributes = BlockAttributes::from(['postTypes' => [['value' => 'post'], ['value' => 'page']]]);
	expect($attributes->postTypes())->toBe(['post', 'page']);
});

it('return an empty array if no post types are selected', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->postTypes())->toBe([]);
});

it('returns the limit', function () {
	$attributes = BlockAttributes::from(['postsPerPage' => 5]);
	expect($attributes->limit())->toBe(5);
});

it('returns the default limit', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->limit())->toBe(3);
});

it('returns the offset', function () {
	$attributes = BlockAttributes::from(['offset' => 10]);
	expect($attributes->offset())->toBe(10);
});

it('returns the default offset', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->offset())->toBe(0);
});

it('returns the order by', function () {
	$attributes = BlockAttributes::from(['orderBy' => 'date']);
	expect($attributes->orderBy())->toBe('post_date');
});

it('returns the default order by', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->orderBy())->toBe('post_date');
});

it('returns in random order', function () {
	$attributes = BlockAttributes::from(['orderBy' => 'rand']);
	expect($attributes->inRandomOrder())->toBeTrue();
});

it('returns not in random order', function () {
	$attributes = BlockAttributes::from(['orderBy' => 'date']);
	expect($attributes->inRandomOrder())->toBeFalse();
});

it('returns if it has manual selection', function () {
	$attributes = BlockAttributes::from(['enableManualSelection' => true]);
	expect($attributes->hasManualSelection())->toBeTrue();
});

it('returns if it has no manual selection', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->hasManualSelection())->toBeFalse();
});

it('returns the manual selection post IDs', function () {
	$attributes = BlockAttributes::from(['manualSelectionPosts' => [['value' => 4], ['value' => 2]]]);
	expect($attributes->manualSelectionPostIDs())->toBe([4, 2]);
});

it('returns an empty array if no manual selection post IDs are set', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->manualSelectionPostIDs())->toBe([]);
});

it('returns if it keeps manual selection order', function () {
	$attributes = BlockAttributes::from(['keepManualSelectionOrder' => true]);
	expect($attributes->keepManualSelectionOrder())->toBeTrue();
});

it('returns if it does not keep manual selection order', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->keepManualSelectionOrder())->toBeFalse();
});

it('returns if it has sticky post', function () {
	$attributes = BlockAttributes::from(['enableStickyPost' => true, 'stickyPost' => ['value' => 1]]);
	expect($attributes->hasStickyPost())->toBeTrue();
});

it('returns if it does not have sticky post', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->hasStickyPost())->toBeFalse();
});

it('returns the sticky post ID', function () {
	$attributes = BlockAttributes::from(['stickyPost' => ['value' => 1]]);
	expect($attributes->stickyPostID())->toBe(1);
});

it('returns 0 if no sticky post ID is set', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->stickyPostID())->toBe(0);
});

it('returns if it has excluded posts', function () {
	$attributes = BlockAttributes::from(['enableExcludePosts' => true, 'excludePosts' => [['value' => 1], ['value' => 2]]]);
	expect($attributes->hasExcludedPosts())->toBeTrue();
});

it('returns if it does not have excluded posts', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->hasExcludedPosts())->toBeFalse();
});

it('returns the excluded post IDs', function () {
	$attributes = BlockAttributes::from(['excludePosts' => [['value' => 1], ['value' => 2]]]);
	expect($attributes->excludedPostIds())->toBe([1, 2]);
});

it('returns an empty array if no excluded post IDs are set', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->excludedPostIds())->toBe([]);
});

it('returns if it excludes child posts', function () {
	$attributes = BlockAttributes::from(['enablePostParent' => true, 'postParentOption' => 'only-parents']);
	expect($attributes->excludeChildPosts())->toBeTrue();
});

it('returns if it does not exclude child posts', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->excludeChildPosts())->toBeFalse();
});

it('returns if it only includes child posts of parent', function () {
	$attributes = BlockAttributes::from(['enablePostParent' => true, 'postParentOption' => 'specific-parent']);
	expect($attributes->onlyChildPostsOfParent())->toBeTrue();
});

it('returns if it only includes child posts of the current post id', function () {
	$attributes = BlockAttributes::from(['enablePostParent' => true, 'postParentOption' => 'current-post-as-parent']);
	expect($attributes->onlyChildPostsOfThisParent())->toBeTrue();
});

it('returns if it does not only include child posts of parent', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->onlyChildPostsOfParent())->toBeFalse();
});

it('returns the parent post ID', function () {
	$attributes = BlockAttributes::from(['postParent' => ['value' => 1]]);
	expect($attributes->parentPostID())->toBe(1);
});

it('returns 0 if no parent post ID is set', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->parentPostID())->toBe(0);
});

it('returns if it has taxonomy filter', function () {
	$attributes = BlockAttributes::from(['enableTaxonomies' => true, 'taxonomyTerms' => ['category' => [['value' => 'news'], ['value' => 'events']]]]);
	expect($attributes->hasTaxonomyFilter())->toBeTrue();
});

it('returns if it does not have taxonomy filter', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->hasTaxonomyFilter())->toBeFalse();
});

it('returns the taxonomy term slugs', function () {
	$attributes = BlockAttributes::from(['taxonomyTerms' => ['category' => [['value' => 'news'], ['value' => 'events']]]]);
	expect($attributes->taxonomyTermSlugs())->toBe(['category' => ['news', 'events']]);
});

it('returns an empty array if no taxonomy term slugs are set', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->taxonomyTermSlugs())->toBe([]);
});

it('returns the taxonomy relation', function () {
	$attributes = BlockAttributes::from(['taxonomyRelation' => 'AND']);
	expect($attributes->taxonomyRelation())->toBe('AND');
});

it('returns the default taxonomy relation', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->taxonomyRelation())->toBe('AND');
});

it('returns the order', function () {
	$attributes = BlockAttributes::from(['order' => 'desc']);
	expect($attributes->order())->toBe('desc');
});

it('returns the default order', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->order())->toBe('desc');
});

it('returns if it displays the image', function () {
	$attributes = BlockAttributes::from(['displayImage' => true]);
	expect($attributes->displayImage())->toBeTrue();
});

it('returns if it does not display the image', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->displayImage())->toBeFalse();
});

it('returns if it displays the date', function () {
	$attributes = BlockAttributes::from(['displayDate' => true]);
	expect($attributes->displayDate())->toBeTrue();
});

it('returns if it does not display the date', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->displayDate())->toBeFalse();
});

it('returns if it displays the excerpt', function () {
	$attributes = BlockAttributes::from(['displayExcerpt' => true]);
	expect($attributes->displayExcerpt())->toBeTrue();
});

it('returns if it does not display the excerpt', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->displayExcerpt())->toBeFalse();
});

it('returns if it displays the label', function () {
	$attributes = BlockAttributes::from(['displayLabel' => true]);
	expect($attributes->displayLabel())->toBeTrue();
});

it('returns if it does not display the label', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->displayLabel())->toBeFalse();
});

it('returns the alignment', function () {
	$attributes = BlockAttributes::from(['align' => 'center']);
	expect($attributes->align())->toBe('aligncenter');
});

it('returns empty string if no alignment is set', function () {
	$attributes = BlockAttributes::from([]);
	expect($attributes->align())->toBe('');
});
