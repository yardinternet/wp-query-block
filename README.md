# Yard Query Block

An acorn package providing a "Query Block" for the Gutenberg editor.

## Installation

You can install this package with Composer:

```bash
composer require yard/query-block
```

You can publish the config file with:

```shell
$ wp acorn vendor:publish --provider="Yard\QueryBlock\Providers\QueryBlockServiceProvider"
```

## Usage

From a Blade template:

```blade
@include('Example::example')
```

From WP-CLI:

```shell
$ wp acorn example
```
