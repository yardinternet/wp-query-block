<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/yard/query-block/assets/js/index', function () {
    return response()->file(__DIR__.'/../../public/index.js', ['Content-Type' => 'application/javascript']);
});

Route::get('yard/query-block/assets/php/index', function () {
    return response()->file(__DIR__.'/../../public/index.asset.php', ['Content-Type' => 'application/x-httpd-php']);
});

Route::get('yard/query-block/assets/css/index', function () {
    return response()->file(__DIR__.'/../../public/index.css', ['Content-Type' => 'text/css']);
});

Route::get('yard/query-block/settings/templates', function () {
    $path = resource_path('views/vendor/yard-query-block/templates');
    $files = scandir($path);
    $templates = [];

    foreach ($files as $file) {
        if (strpos($file, '.blade.php') === false) {
            continue;
        }

        $content = file_get_contents($path . '/' . $file);
        preg_match('/Template: (.*)\n/', $content, $matches);
        $templateName = $matches[1] ?? $file;

        $templates[] = [
            'label' => $templateName,
            'value' => str_replace('.blade.php', '', $file),
        ];
    }

    return response()->json([
        'templates' => $templates,
    ]);
});
