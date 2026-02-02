<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShuffleProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:shuffle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating random keys...');

        // Update all 100k+ rows instantly
        \DB::statement("UPDATE products SET random_sort_key = FLOOR(RAND() * 1000000)");

        // Sync changes to Meilisearch
        // We use chunks to avoid memory exhaustion with 100k records
        \App\Models\Product::chunk(500, function ($products) {
            $products->searchable();
        });

        $this->info('Products shuffled and Meilisearch synced successfully.');
    }
}
