<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log as DebugLog;

trait Exportable
{
    /**
     * Export model data to a CSV file.
     *
     * @param string $fileName
     * @return string Path to the CSV file.
     */
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


    /**
     * Import data from a CSV file.
     *
     * @param string $filePath
     * @return bool
     */
    public static function importFromCsv(string $filePath): bool
    {
        if (!Storage::exists($filePath)) {
            throw new \Exception("File not found at: {$filePath}");
        }

        $content = Storage::get($filePath);
        $rows = array_map('str_getcsv', explode("\n", trim($content)));
        $columns = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($columns, $row);
            static::updateOrCreate(['id' => $data['id'] ?? null], $data);
        }

        return true;
    }
}
