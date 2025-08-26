<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Yard\QueryBlock\Traits\Helpers;

beforeEach(function () {
	$this->class = new class() {
		use Helpers;
	};
});

it('can create a url', function () {
	$path = '/test/path';

	Config::set('app.url', 'https://example.com');

	$url = $this->class->appendToBaseUrl($path);

	expect($url)->toBe('https://example.com/test/path');
});

it('removes path from the application url', function () {
	$path = '/test/path';

	Config::set('app.url', 'https://example.com/subsite-1/');

	$url = $this->class->appendToBaseUrl($path);

	expect($url)->toBe('https://example.com/test/path');
});

it('keeps the port if provided', function () {
	$path = '/test/path';

	Config::set('app.url', 'https://example.com:8080');

	$url = $this->class->appendToBaseUrl($path);

	expect($url)->toBe('https://example.com:8080/test/path');
});

it('throws an error when the url is not a string', function () {
	$path = '/test/path';

	Config::set('app.url', false);

	$this->expectException(\InvalidArgumentException::class);

	$this->class->appendToBaseUrl($path);
});

it('it does not fail when url is invalid', function () {
	$path = '/test/path';

	Config::set('app.url', 'not-a-url');

	expect($this->class->appendToBaseUrl($path))->toBe('not-a-url/test/path');
});
