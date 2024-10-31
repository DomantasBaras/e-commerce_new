<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ElasticSearchService;
use Inertia\Inertia;

class SearchController extends Controller
{
    protected $elasticsearch;

    public function __construct(ElasticSearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    // Search suggestions for AJAX calls (already handled via JSON response)
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = $this->elasticsearch->search($query);

        return response()->json([
            'suggestions' => $results['suggestions'],
        ]);
    }

    // Full search results using Inertia
    public function showResults(Request $request)
    {
        $query = $request->input('query');
        $results = $this->elasticsearch->search($query);

        return Inertia::render('SearchResults', [
            'results' => $results['results'],
            'query' => $query
        ]);
    }
    
}
