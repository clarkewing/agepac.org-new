<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'private' => [
            // Local development can point this disk at local storage instead of R2.
            // The root only applies there: on s3 it would become a key prefix.
            'driver' => env('PRIVATE_DISK_DRIVER', 's3'),
            'root' => env('PRIVATE_DISK_DRIVER', 's3') === 'local' ? storage_path('app/private') : '',
            'serve' => true,
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/private',
            'key' => env('R2_ACCESS_KEY_ID'),
            'secret' => env('R2_SECRET_ACCESS_KEY'),
            'region' => 'auto',
            'bucket' => env('R2_PRIVATE_BUCKET'),
            'endpoint' => env('R2_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'visibility' => 'private',
            'throw' => false,
            'report' => true,
        ],

        'cdn' => [
            // Local development can point this disk at local storage instead of R2.
            // The root only applies there: on s3 it would become a key prefix.
            'driver' => env('CDN_DISK_DRIVER', 's3'),
            'root' => env('CDN_DISK_DRIVER', 's3') === 'local' ? storage_path('app/public') : '',
            'key' => env('R2_ACCESS_KEY_ID'),
            'secret' => env('R2_SECRET_ACCESS_KEY'),
            'region' => 'auto',
            'bucket' => env('R2_PUBLIC_BUCKET'),
            'endpoint' => env('R2_ENDPOINT'),
            // R2 buckets are only publicly reachable through a public custom domain, never the S3 endpoint.
            'url' => env('R2_PUBLIC_URL') ?: rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'use_path_style_endpoint' => true,
            'throw' => false,
            'report' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => array_filter([
        public_path('storage') => env('CDN_DISK_DRIVER', 's3') === 'local'
            ? storage_path('app/public')
            : null,
    ]),

];
