<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use Illuminate\Support\Str;

class ImportCategories extends Command
{
    protected $signature = 'import:categories';
    protected $description = 'Import categories from JSON files and rebuild ancestor slugs';

    public function handle()
    {
        // Prevent script timeout for large datasets
        set_time_limit(0);
        $this->info("🚀 Starting import process...");

        // 1. Load JSON Files
        $mainCategories  = $this->loadJson('categories/main_categories.json');
        $allCategories   = $this->loadJson('categories/all_categories.json');
        $allCategories2  = $this->loadJson('categories/all_categories_2.json');

        if (!$mainCategories || !$allCategories || !$allCategories2) {
            $this->error("❌ Import failed: Missing or invalid JSON files.");
            return 1;
        }

        // 2. Pass One: Import/Create Records
        $this->info("📦 Step 1: Importing category records...");
        $this->importItems($mainCategories, true);
        $this->importItems($allCategories, false);
        $this->importItems($allCategories2, false);

        // 3. Pass Two: Rebuild Ancestor Slugs
        $this->info("\n🔗 Step 2: Rebuilding ancestor slugs (hierarchy)...");
        
        $categories = Category::all();
        $bar = $this->output->createProgressBar($categories->count());

        $bar->start();
        foreach ($categories as $category) {
            $category->updateAncestorSlugs();
            $bar->advance();
        }
        $bar->finish();

        $this->info("\n\n✅ Category import and hierarchy generation complete!");
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
        foreach ($items as $cat) {
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
        }
    }
}