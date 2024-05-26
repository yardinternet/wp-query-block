<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Traits;

trait VersionRetriever
{
    /**
     * Retrieves the version number from composer.json.
     */
    public function getVersion(): string
    {
        $composerJsonPath = __DIR__ . '/../../composer.json';

        if (file_exists($composerJsonPath)) {
            $content = file_get_contents($composerJsonPath);

            if (false === $content) {
                return 'unknown';
            }

            $composerJson = json_decode($content, true);

            return $composerJson['version'] ?? 'unknown';
        }

        return 'unknown';
    }
}
