<?php
namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function search($query)
    {
        // Construct the Elasticsearch query with fuzzy and prefix matching
        $params = [
            'index' => 'products', // Replace with your actual index name
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            // Prefix match for partial string matches
                            [
                                'prefix' => [
                                    'name' => [
                                        'value' => $query,
                                        'boost' => 2.0 // Boost for prefix matches
                                    ]
                                ]
                            ],
                            // Fuzzy match for minor spelling errors
                            [
                                'fuzzy' => [
                                    'name' => [
                                        'value' => $query,
                                        'fuzziness' => 'AUTO' // Automatically adjusts fuzziness level
                                    ]
                                ]
                            ],
                            // Multi-match for general search
                            [
                                'multi_match' => [
                                    'query' => $query,
                                    'fields' => ['name', 'name.keyword'], // Search in 'name' field and its 'keyword' subfield
                                    'type' => 'best_fields' // Return the best matching fields
                                ]
                            ]
                        ]
                    ]
                ],
                'suggest' => [
                    'text' => $query,
                    'suggestion' => [
                        'term' => [
                            'field' => 'name',
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->client->search($params);

        $suggestions = [];
        $results = [];

        // Check for hits (actual search results)
        if (isset($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $hit) {
                $results[] = $hit['_source'];
            }
        }

        // Check if suggestions exist and handle them
        if (isset($response['suggest']['suggestion'][0]['options'])) {
            foreach ($response['suggest']['suggestion'][0]['options'] as $option) {
                $suggestions[] = [
                    'title' => $option['text'],
                    'id' => $option['_id'] ?? null // Adjust based on your needs
                ];
            }
        } else {
            $suggestions = ['No suggestions found'];
        }

        return [
            'results' => $results,
            'suggestions' => $suggestions
        ];
    }

    public function index($index, $id, $data)
    {
        $params = [
            'index' => $index,
            'id' => $id,
            'body' => $data,
        ];

        return $this->client->index($params);
    }
}
