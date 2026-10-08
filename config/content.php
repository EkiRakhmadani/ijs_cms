<?php

use App\Content\Exporters\ServiceExporter;

return [

    /*
    |--------------------------------------------------------------------------
    | Frontend Path
    |--------------------------------------------------------------------------
    |
    | Where the Next.js site lives. Publishing writes JSON and media straight
    | into it, so the frontend keeps prerendering from local files at build
    | time and never has to call this API at runtime.
    |
    | A sibling directory by default, which is how the two sit on a developer
    | machine. Override with FRONTEND_PATH when that is not true.
    |
    */

    'frontend_path' => env('FRONTEND_PATH', dirname(base_path()).'/ijs_frontend'),

    /*
    |--------------------------------------------------------------------------
    | Output Directories
    |--------------------------------------------------------------------------
    |
    | Both relative to the frontend path. `generated` holds the JSON the
    | frontend's data modules import; `media` holds uploaded files, served
    | same-origin so the site's `img-src 'self'` CSP covers them unchanged.
    |
    */

    'generated_dir' => 'src/data/generated',

    'media_dir' => 'public/media',

    /*
    |--------------------------------------------------------------------------
    | Exporters
    |--------------------------------------------------------------------------
    |
    | Each one owns a single JSON file. Adding a content type means adding its
    | exporter here — nothing else in the publish path needs to know about it.
    |
    */

    'exporters' => [
        ServiceExporter::class,
    ],

];
