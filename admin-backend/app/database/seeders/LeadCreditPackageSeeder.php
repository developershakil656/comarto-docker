<?php

namespace Database\Seeders;

use App\Models\LeadCreditPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadCreditPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        LeadCreditPackage::insert([
            ['name' => 'Quick', 'credits' => 1, 'price' => 26, 'duration_months' => 1],
            ['name' => 'Starter', 'credits' => 15, 'price' => 199, 'duration_months' => 1],
            ['name' => 'Standard', 'credits' => 50, 'price' => 489, 'duration_months' => 2],
            ['name' => 'Premium', 'credits' => 100, 'price' => 879, 'duration_months' => 3],
        ]);
    }
}
