<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. A "local" driver, as well as a variety of cloud
    | based drivers are available for your choosing. Just store away!
    |
    | Supported: "local", "ftp", "s3", "rackspace"
    |
    */

    'default' => env('FILESYSTEM_TYPE','local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    */

    'disks' => [

        'ftp' => [
            'driver'   => 'ftp',
            'host'     => env('FTP_HOST', '0.0.0.0'),
            'username' => env('FTP_USERNAME', 'username'),
            'password' => env('FTP_PASSWORD', 'password'),

            // Optional FTP Settings...
            // 'port'     => env('FTP_PORT', 21),
            'root'         => env('FTP_ROOT', '/'),
            'passive'      => true,
            'ssl'          => true,
            'cdn_url'      => env('CDN_URL', 'https://cdn.voten.co/'),
            // 'timeout'  => 30,
        ],

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => 'your-key',
            'secret' => 'your-secret',
            'region' => 'your-region',
            'bucket' => 'your-bucket',
        ],
        'oss' => [
            'driver'        => 'oss',
            'access_id'     => env('OSS_ACCESS_KEY_ID',''),
            'access_key'    => env('OSS_ACCESS_KEY_SECRET',''),
            'bucket'        => env('OSS_BUCKET'),
            'endpoint'      => env('OSS_ENDPOINT'),
            'endpoint_internal' => env('OSS_ENDPOINT_INTERNAL',''),
            'cdnDomain'     => env('OSS_CDN_DOMAIN',''),
            'ssl'           => env('OSS_SSL',false),
            'isCName'       => env('OSS_ISCNAME'),
            'debug'         => env('OSS_DEBUGT')
        ],

    ],

];
