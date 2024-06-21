# Yard Query Block

An acorn package providing a "Query Block" for the Gutenberg editor.

## Requirements

- [Sage](https://github.com/roots/sage) >= 10.0
- [Acorn](https://github.com/roots/acorn) >= 3.0

## Installation

Install this package with Composer:

```bash
composer require yard/query-block
```

Then run the Acorn WP-CLI command to discover this package:

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
 * Template: Horizontaal
 *
 * @var Illuminate\Support\Collection|Yard\Data\PostData $postDataCollection
 * @var Yard\QueryBlock\Block\BlockAttributes $attributes
 */
@endphp
```

Now, you will be able to select the template from the editor. The name of the template is displayed using the value in your docblock.

## Hooks

### Filters

#### yard_query_block_post_query

Filters the Post Query before it is executed on the database.

```php
apply_filters('yard_query_block_post_query', $query, $attributes);
```

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
