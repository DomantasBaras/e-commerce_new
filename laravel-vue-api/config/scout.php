<?php

return [

    'driver' => env('SCOUT_DRIVER', 'elasticsearch'),

    // Configuration for Elasticsearch
    'elasticsearch' => [
        'hosts' => [
            env('ELASTICSEARCH_HOST', 'localhost:9200'),
        ],
        'retries' => 2,
        'update_mapping' => true,
        'refresh_documents' => true,
        'index' => env('ELASTICSEARCH_INDEX', 'laravel'),
    ],

    // Other Scout configuration options...
    'prefix' => env('SCOUT_PREFIX', ''),
    'queue' => env('SCOUT_QUEUE', true),
    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    'soft_delete' => false,

    'mappings' => [
        'date_detection' => false,
        'dynamic_date_formats' => ['yyyy-MM-dd HH:mm:ss', 'yyyy-MM-dd'],
        'dynamic_templates' => [
            [
                'strings' => [
                    'match_mapping_type' => 'string',
                    'mapping' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
        ],
    ],

];
