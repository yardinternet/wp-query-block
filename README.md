# WP Query Block

[![Code Style](https://github.com/yardinternet/yard-query-block/actions/workflows/format-php.yml/badge.svg?no-cache)](https://github.com/yardinternet/yard-query-block/actions/workflows/format-php.yml)
[![PHPStan](https://github.com/yardinternet/yard-query-block/actions/workflows/phpstan.yml/badge.svg?no-cache)](https://github.com/yardinternet/yard-query-block/actions/workflows/phpstan.yml)
[![Tests](https://github.com/yardinternet/yard-query-block/actions/workflows/run-tests.yml/badge.svg?no-cache)](https://github.com/yardinternet/yard-query-block/actions/workflows/run-tests.yml)
[![Code Coverage Badge](https://github.com/yardinternet/yard-query-block/blob/badges/coverage.svg)](https://github.com/yardinternet/yard-query-block/actions/workflows/badges.yml)
[![Lines of Code Badge](https://github.com/yardinternet/yard-query-block/blob/badges/lines-of-code.svg)](https://github.com/yardinternet/yard-query-block/actions/workflows/badges.yml)

An acorn package providing a "Query Block" for the Gutenberg editor.

## Requirements

- [Sage](https://github.com/roots/sage) >= 10.0
- [Acorn](https://github.com/roots/acorn) >= 4.0

## Installation

1. Install this package with Composer:

    ```sh
    composer require yard/query-block
    ```

2. Run the Acorn WP-CLI command to discover this package:

    ```shell
    wp acorn package:discover
    ```

## Usage

When this package is installed, you can insert the “Berichtenlijst” block in the Gutenberg editor. This block lists a series of posts based on different filter settings in the admin.

### Templates

#### Override Default Template

A default template is included in the package. You can publish the template to your project with:

```shell
wp acorn vendor:publish --provider="Yard\QueryBlock\QueryBlockServiceProvider"
```

This will copy the view default.php from this package into your project at `/sage/resources/views/vendor/yard-query-block/templates/default.blade.php`. You can now modify the default template as desired.

#### Create Aditional Templates

You can create additional templates by placing a template file in the same directory. For example:

`/sage/resources/views/vendor/yard-query-block/templates/horizontal.blade.php`

Add the template name as a comment at the top of this template file like this:

```php
@php
/**
 * Template: Horizontal
 *
 * @var Illuminate\Support\Collection|Yard\Data\PostData $postDataCollection
 * @var Yard\QueryBlock\Block\BlockAttributes $attributes
 */
@endphp
```

Now, you will be able to select the template from the editor. The name of the template is displayed using the value in your docblock.

## Hooks

### JavaScript filters

#### `yard.query-inspector-config`

Customize which controls are displayed in the block's inspector panel.

```js
import { addFilter } from '@wordpress/hooks';

addFilter(
    'yard.query-inspector-config',
    'yard.query-inspector-config',
    ( config, attributes ) => {
        return {
            ...config,
            showPostTypeSelectControl: false, 
            showNumberOfPostsRangeControl: false,
        };
    }
);
```

#### `yard.query-exclude-post-types`

Exclude specific post types from the list of available post types.

```js
import { addFilter } from '@wordpress/hooks';

addFilter(
    'yard.query-exclude-post-types',
    'yard.query-exclude-post-types',
    ( excludedPostTypes ) => {
        return [
            ...excludedPostTypes,
            'healthcare-provider',
            'location',
            'page',
        ];
    }
);
```

#### `yard.query-exclude-taxonomies`

Exclude specific taxonomies from the list of available taxonomies.

```js
import { addFilter } from '@wordpress/hooks';

addFilter(
    'yard.query-exclude-taxonomies',
    'yard.query-exclude-taxonomies',
    ( excludedTaxonomies ) => {
        return [ 'category', 'post_tag' ]; 
    }
);
```

#### `yard.query-min-number-of-posts` and `yard.query-max-number-of-posts`

Customize the minimum and maximum value for the posts per page range.

```js
import { addFilter } from '@wordpress/hooks';

addFilter(
    'yard.query-max-number-of-posts',
    'yard.query-max-number-of-posts',
        (defaultMax, attributes) => {
        const postTypeValues = attributes.postTypes?.map((type) => type.value) || [];
        
        if (postTypeValues.includes('news')) {
            return 5;
        }

        if (postTypeValues.includes('healthcare-provider')) {
            return 2;
        }

        return defaultMax;
    }
);
```

#### `yard.query-post-type-select-control-is-multi`

Change the post type select control from multi to single select.

```js
import { addFilter } from '@wordpress/hooks';

addFilter(
    'yard.query-post-type-select-control-is-multi',
    'yard.query-post-type-select-control-is-multi',
    () => false
);
```

### PHP filters

#### `yard_query_block_post_query`

Filters the Post Query before it is executed on the database.

| Parameters  | Type                                   | Description          |
|-------------|----------------------------------------|----------------------|
| $query      | \Corcel\Model\Builder\PostBuilder      | The query object     |
| $attributes | \Yard\QueryBlock\Block\BlockAttributes | The block attributes |

| Return | Type                              | Description      |
|--------|-----------------------------------|------------------|
| $query | \Corcel\Model\Builder\PostBuilder | The query object |

Example:

```php
add_filter('yard_query_block_post_query', function ($query, $attributes) {
    if (is_user_logged_in()) {
        return $query;
    }

    return $query->hasMeta('post_is_public', 'yes');
}, 10, 2);
```

## About us

[![banner](https://raw.githubusercontent.com/yardinternet/.github/refs/heads/main/profile/assets/small-banner-github.svg)](https://www.yard.nl/werken-bij/)
