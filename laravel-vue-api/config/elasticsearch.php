<?php 
return [
    'index' => env('ELASTICSEARCH_INDEX', 'your_index_name'),
    'hosts' => [
        env('ELASTICSEARCH_HOST', 'localhost') . ':' . env('ELASTICSEARCH_PORT', 9200),
    ],
    'connection' => [
        'username' => env('ELASTICSEARCH_USERNAME', null),
        'password' => env('ELASTICSEARCH_PASSWORD', null),
        'ssl_verification' => env('ELASTICSEARCH_SSL_VERIFICATION', true),
    ],
];
