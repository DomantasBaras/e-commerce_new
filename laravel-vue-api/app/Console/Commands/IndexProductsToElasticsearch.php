<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\ElasticSearchService;

class IndexProductsToElasticsearch extends Command
{
    protected $signature = 'elasticsearch:index-products';
    protected $description = 'Index all products to Elasticsearch';

    protected $elasticsearch;

    public function __construct(ElasticSearchService $elasticsearch)
    {
        parent::__construct();
        $this->elasticsearch = $elasticsearch;
    }

    public function handle()
    {
        // Fetch products in chunks to avoid memory issues
        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                $this->indexProduct($product);
            }
        });

        $this->info('All products have been indexed.');
    }

    protected function indexProduct($product)
    {
        // Prepare data for Elasticsearch
        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            // Add other fields as necessary
        ];

        // Index the product in Elasticsearch
        $this->elasticsearch->index('products', $product->id, $data);

        $this->info("Indexed product: {$product->name}");
    }
}
