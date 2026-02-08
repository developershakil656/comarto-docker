<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Category;


class ImportCategories extends Command
{
    protected $signature = 'import:categories';
    protected $description = 'Import categories from JSON files into the database';

    public function handle()
    {
        set_time_limit(0);
        $this->info("Starting import...");

        // Load both files using helper
        $mainCategories  = $this->loadJson('categories/main_categories.json');
        $allCategories = $this->loadJson('categories/all_categories.json');
        $allCategories2 = $this->loadJson('categories/all_categories_2.json');

        if (!$mainCategories || !$allCategories || !$allCategories2) {
            return 1;
        }

        // Import main categories
        $this->importItems($mainCategories, true);
        $this->info("✔ Main categories imported.");

        // Import subcategories
        $this->importItems($allCategories, false);
        $this->info("✔ Trade subcategories part 1 imported.");

        $this->info("subcategories part 2 importing...");

        $this->importItems($allCategories2, false);
        $this->info("✔ Trade subcategories part 2 imported.");

        $this->info("✔ Category import complete!");
        return 0;
    }

    /**
     * Load JSON file from storage/app/
     */
    private function loadJson($relativePath)
    {
        $fullPath = storage_path('app/' . $relativePath);

        if (!file_exists($fullPath)) {
            $this->error("File not found: $fullPath");
            return null;
        }

        $json = file_get_contents($fullPath);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            $this->error("Invalid JSON format in file: $relativePath");
            return null;
        }

        return $data;
    }

    /**
     * Import categories into database
     */
    private function importItems($items, $isMain = false)
    {
        $total = count($items);
        $this->info("Total items to process: " . $total);

        foreach ($items as $index => $cat) {
            Category::updateOrCreate(
                $isMain ? ['id' => $cat['id']] : ['slug' => $cat['slug']],
                [
                    'name'      => $cat['name'],
                    'slug'      => $cat['slug'] ?? Str::slug($cat['name']),
                    'icon'      => $cat['icon'] ?? null,
                    'parent_id' => $cat['parent_id'] ?? null,
                    'status'    => $cat['status'] ?? 'active',
                ]
            );

            // Show progress every 50 items
            if ($index % 50 === 0) {
                $this->info("Processed $index / $total...");
            }
        }
    }
}
