<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Traits;

use Webmozart\Assert\Assert;

trait Helpers
{
    public function appendToBaseUrl(string $path): string
    {
        return $this->baseUrl() . $path;
    }

    private function baseUrl(): string
    {
        $url = config('app.url');

        Assert::string($url);

        $parsed = parse_url($url);

        Assert::isArray($parsed);

        if (! isset($parsed['scheme']) || ! isset($parsed['host'])) {
            return $url;
        }

        $baseUrl = $parsed['scheme'] . '://' . $parsed['host'];

        if (isset($parsed['port'])) {
            $baseUrl .= ':' . $parsed['port'];
        }

        return $baseUrl;
    }
}
