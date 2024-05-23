<?php

declare(strict_types=1);

use Yard\QueryBlock\Block\BlockAttributes;

it('returns the default template', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->template())->toBe('default');
});

it('returns the selected template', function () {
    $attributes = new BlockAttributes(['template' => 'custom']);
    expect($attributes->template())->toBe('custom');
});

it('returns the post types', function () {
    $attributes = new BlockAttributes(['postTypes' => [['value' => 'post'], ['value' => 'page']]]);
    expect($attributes->postTypes())->toBe(['post', 'page']);
});

it('return an empty array if no post types are selected', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->postTypes())->toBe([]);
});

it('returns the limit', function () {
    $attributes = new BlockAttributes(['postsPerPage' => 5]);
    expect($attributes->limit())->toBe(5);
});

it('returns the default limit', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->limit())->toBe(3);
});

it('returns the offset', function () {
    $attributes = new BlockAttributes(['offset' => 10]);
    expect($attributes->offset())->toBe(10);
});

it('returns the default offset', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->offset())->toBe(0);
});

it('returns the order by', function () {
    $attributes = new BlockAttributes(['orderBy' => 'date']);
    expect($attributes->orderBy())->toBe('post_date');
});

it('returns the default order by', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->orderBy())->toBe('post_date');
});

it('returns in random order', function () {
    $attributes = new BlockAttributes(['orderBy' => 'rand']);
    expect($attributes->inRandomOrder())->toBeTrue();
});

it('returns not in random order', function () {
    $attributes = new BlockAttributes(['orderBy' => 'date']);
    expect($attributes->inRandomOrder())->toBeFalse();
});

it('returns if it has manual selection', function () {
    $attributes = new BlockAttributes(['enableManualSelection' => true]);
    expect($attributes->hasManualSelection())->toBeTrue();
});

it('returns if it has no manual selection', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->hasManualSelection())->toBeFalse();
});

it('returns the manual selection post IDs', function () {
    $attributes = new BlockAttributes(['manualSelectionPosts' => [['value' => 4], ['value' => 2]]]);
    expect($attributes->manualSelectionPostIDs())->toBe([4, 2]);
});

it('returns an empty array if no manual selection post IDs are set', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->manualSelectionPostIDs())->toBe([]);
});

it('returns if it keeps manual selection order', function () {
    $attributes = new BlockAttributes(['keepManualSelectionOrder' => true]);
    expect($attributes->keepManualSelectionOrder())->toBeTrue();
});

it('returns if it does not keep manual selection order', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->keepManualSelectionOrder())->toBeFalse();
});

it('returns if it has sticky post', function () {
    $attributes = new BlockAttributes(['enableStickyPost' => true, 'stickyPost' => ['value' => 1]]);
    expect($attributes->hasStickyPost())->toBeTrue();
});

it('returns if it does not have sticky post', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->hasStickyPost())->toBeFalse();
});

it('returns the sticky post ID', function () {
    $attributes = new BlockAttributes(['stickyPost' => ['value' => 1]]);
    expect($attributes->stickyPostID())->toBe(1);
});

it('returns 0 if no sticky post ID is set', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->stickyPostID())->toBe(0);
});

it('returns if it has excluded posts', function () {
    $attributes = new BlockAttributes(['enableExcludePosts' => true, 'excludePosts' => [['value' => 1], ['value' => 2]]]);
    expect($attributes->hasExcludedPosts())->toBeTrue();
});

it('returns if it does not have excluded posts', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->hasExcludedPosts())->toBeFalse();
});

it('returns the excluded post IDs', function () {
    $attributes = new BlockAttributes(['excludePosts' => [['value' => 1], ['value' => 2]]]);
    expect($attributes->excludedPostIds())->toBe([1, 2]);
});

it('returns an empty array if no excluded post IDs are set', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->excludedPostIds())->toBe([]);
});

it('returns if it excludes child posts', function () {
    $attributes = new BlockAttributes(['enablePostParent' => true, 'postParentOption' => 'only-parents']);
    expect($attributes->excludeChildPosts())->toBeTrue();
});

it('returns if it does not exclude child posts', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->excludeChildPosts())->toBeFalse();
});

it('returns if it only includes child posts of parent', function () {
    $attributes = new BlockAttributes(['enablePostParent' => true, 'postParentOption' => 'specific-parent']);
    expect($attributes->onlyChildPostsOfParent())->toBeTrue();
});

it('returns if it does not only include child posts of parent', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->onlyChildPostsOfParent())->toBeFalse();
});

it('returns the parent post ID', function () {
    $attributes = new BlockAttributes(['postParent' => ['value' => 1]]);
    expect($attributes->parentPostID())->toBe(1);
});

it('returns 0 if no parent post ID is set', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->parentPostID())->toBe(0);
});

it('returns if it has taxonomy filter', function () {
    $attributes = new BlockAttributes(['enableTaxonomies' => true, 'taxonomyTerms' => ['category' => [['value' => 'news'], ['value' => 'events']]]]);
    expect($attributes->hasTaxonomyFilter())->toBeTrue();
});

it('returns if it does not have taxonomy filter', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->hasTaxonomyFilter())->toBeFalse();
});

it('returns the taxonomy terms', function () {
    $attributes = new BlockAttributes(['taxonomyTerms' => ['category' => [['value' => 'news'], ['value' => 'events']]]]);
    expect($attributes->taxonomyTerms())->toBe(['category' => [['value' => 'news'], ['value' => 'events']]]);
});

it('returns an empty array if no taxonomy terms are set', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->taxonomyTerms())->toBe([]);
});

it('returns the taxonomy term slugs', function () {
    $attributes = new BlockAttributes(['taxonomyTerms' => ['category' => [['value' => 'news'], ['value' => 'events']]]]);
    expect($attributes->taxonomyTermSlugs())->toBe(['category' => ['news', 'events']]);
});

it('returns an empty array if no taxonomy term slugs are set', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->taxonomyTermSlugs())->toBe([]);
});

it('returns the taxonomy relation', function () {
    $attributes = new BlockAttributes(['taxonomyRelation' => 'AND']);
    expect($attributes->taxonomyRelation())->toBe('AND');
});

it('returns the default taxonomy relation', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->taxonomyRelation())->toBe('AND');
});

it('returns the order', function () {
    $attributes = new BlockAttributes(['order' => 'desc']);
    expect($attributes->order())->toBe('desc');
});

it('returns the default order', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->order())->toBe('desc');
});

it('returns if it displays the image', function () {
    $attributes = new BlockAttributes(['displayImage' => true]);
    expect($attributes->displayImage())->toBeTrue();
});

it('returns if it does not display the image', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->displayImage())->toBeFalse();
});

it('returns if it displays the date', function () {
    $attributes = new BlockAttributes(['displayDate' => true]);
    expect($attributes->displayDate())->toBeTrue();
});

it('returns if it does not display the date', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->displayDate())->toBeFalse();
});

it('returns if it displays the excerpt', function () {
    $attributes = new BlockAttributes(['displayExcerpt' => true]);
    expect($attributes->displayExcerpt())->toBeTrue();
});

it('returns if it does not display the excerpt', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->displayExcerpt())->toBeFalse();
});

it('returns if it displays the label', function () {
    $attributes = new BlockAttributes(['displayLabel' => true]);
    expect($attributes->displayLabel())->toBeTrue();
});

it('returns if it does not display the label', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->displayLabel())->toBeFalse();
});

it('returns the alignment', function () {
    $attributes = new BlockAttributes(['align' => 'center']);
    expect($attributes->align())->toBe('aligncenter');
});

it('returns empty string if no alignment is set', function () {
    $attributes = new BlockAttributes([]);
    expect($attributes->align())->toBe('');
});
