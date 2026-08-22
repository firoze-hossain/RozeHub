<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RozeHub Release Storage
    |--------------------------------------------------------------------------
    |
    | Keep software packages outside the Laravel application directory.
    | The database stores only a relative path such as:
    |
    |   novaos/2026.2.1/novaos-2026.2.1-x64.iso
    |
    | Set ROZEHUB_RELEASE_STORAGE_PATH in .env when deploying to cPanel.
    | By default it is placed beside the Laravel project directory.
    |
    */
    'root' => env('ROZEHUB_RELEASE_STORAGE_PATH') ?: (
        dirname(base_path()) . DIRECTORY_SEPARATOR . 'rozehub-storage'
    ),
];
