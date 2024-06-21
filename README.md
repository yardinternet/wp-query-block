# Yard Query Block

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

1. Add the following to the `repositories` section of your `composer.json`:

    ```json
    {
      "type": "vcs",
      "url": "git@github.com:yardinternet/yard-query-block"
    }
    ```

2. Install this package with Composer:

    ```sh
    composer require yard/query-block
    ```

3. Run the Acorn WP-CLI command to discover this package:

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
