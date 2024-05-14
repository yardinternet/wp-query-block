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
