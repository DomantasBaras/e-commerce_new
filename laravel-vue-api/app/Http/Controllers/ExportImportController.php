<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class ExportImportController extends Controller
{

    public function exportUsers()
    {
        $filePath = User::exportToCsv('exports/users.csv');
        return response()->download(storage_path('app/' . $filePath)); // Download from the correct storage path

    }

    public function importUsers(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $path = $request->file('file')->store('imports');

        try {
            User::importFromCsv($path);
            return back()->with('success', 'Users imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function exportProducts()
    {
        try {
            $filePath = $this->exportToCsv('products.csv');
            return response()->download(storage_path('app/' . $filePath));
        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting products: ' . $e->getMessage());
        }
    }

    public static function exportToCsv(string $fileName = 'export.csv'): string
    {
        $columns = Schema::getColumnListing((new static)->getTable());
        $filePath = 'exports/' . $fileName;

        // Open a writable stream to storage
        $handle = fopen(storage_path("app/{$filePath}"), 'w');

        // Write the header row
        fputcsv($handle, $columns);

        // Fetch data in chunks
        static::chunk(1000, function ($models) use ($handle, $columns) {
            foreach ($models as $model) {
                $row = [];
                foreach ($columns as $column) {
                    $row[] = $model->{$column};
                }
                fputcsv($handle, $row);
            }
        });

        // Close the file handle
        fclose($handle);

        return $filePath;
    }


    public function importProducts(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $path = $request->file('file')->store('imports');

        try {
            Product::importFromCsv($path);
            return back()->with('success', 'Products imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
