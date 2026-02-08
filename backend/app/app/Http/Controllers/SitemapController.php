<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        // Cache sitemap for 6 hours
        $sitemap = Cache::remember('sitemap', 3600 * 6, function () {
            return $this->generateSitemap();
        });

        return response($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    public function count()
    {
        $count = Cache::remember('sitemap_count', 3600 * 6, function () {
            return $this->getUrlCount();
        });

        return response()->json(['count' => $count]);
    }

    private function generateSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Add static pages
        $xml .= $this->addStaticPages();

        // Add categories
        $xml .= $this->addCategories();

        // Add products
        $xml .= $this->addProducts();

        // Add businesses
        $xml .= $this->addBusinesses();

        $xml .= '</urlset>';

        return $xml;
    }

    private function addStaticPages()
    {
        $xml = '';
        $baseUrl = config('app.frontend_url');
        $staticPages = [
            '/' => ['priority' => '1.0', 'changefreq' => 'daily'],
            '/categories' => ['priority' => '0.9', 'changefreq' => 'weekly'],
            '/random/products' => ['priority' => '0.1', 'changefreq' => 'daily'],
            '/free/listing' => ['priority' => '0.3', 'changefreq' => 'yearly'],
            '/privacy-policy' => ['priority' => '0.3', 'changefreq' => 'yearly'],
            '/terms-conditions' => ['priority' => '0.3', 'changefreq' => 'yearly'],
            '/disclaimer' => ['priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $path => $config) {
            $xml .= '<url>';
            $xml .= '<loc>' . $baseUrl . $path . '</loc>';
            $xml .= '<priority>' . $config['priority'] . '</priority>';
            $xml .= '<changefreq>' . $config['changefreq'] . '</changefreq>';
            $xml .= '</url>';
        }

        return $xml;
    }

    private function addCategories()
    {
        $xml = '';
        $baseUrl = config('app.frontend_url');
        
        $categories = Category::where('status', 'active')
            ->select('id', 'slug', 'updated_at')
            ->with('children:id,parent_id,slug,updated_at,status')
            ->get();

        foreach ($categories as $category) {
            // Add main category
            $xml .= '<url>';
            $xml .= '<loc>' . $baseUrl . '/category/' . $category->slug . '</loc>';
            $xml .= '<priority>0.9</priority>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<lastmod>' . $category->updated_at->format('c') . '</lastmod>';
            $xml .= '</url>';

            // Add subcategories
            foreach ($category->children->where('status', 'active') as $subcategory) {
                $xml .= '<url>';
                $xml .= '<loc>' . $baseUrl . '/category/' . $subcategory->slug . '</loc>';
                $xml .= '<priority>0.8</priority>';
                $xml .= '<changefreq>daily</changefreq>';
                $xml .= '<lastmod>' . $subcategory->updated_at->format('c') . '</lastmod>';
                $xml .= '</url>';
            }
        }
        
        return $xml;
    }

    private function addProducts()
    {
        $xml = '';
        $baseUrl = config('app.frontend_url');
        
        // Paginate products to handle large datasets
        $perPage = 1000;
        $page = 1;
        
        do {
            $products = Product::where('status', 'active')
                ->select('id', 'slug', 'updated_at')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            foreach ($products as $product) {
                $xml .= '<url>';
                $xml .= '<loc>' . $baseUrl . '/product/' . $product->slug . '</loc>';
                $xml .= '<priority>0.7</priority>';
                $xml .= '<changefreq>weekly</changefreq>';
                $xml .= '<lastmod>' . $product->updated_at->format('c') . '</lastmod>';
                $xml .= '</url>';
            }

            $page++;
        } while ($products->count() === $perPage);
        
        return $xml;
    }

    private function addBusinesses()
    {
        $xml = '';
        $baseUrl = config('app.frontend_url');
        
        // Paginate businesses to handle large datasets
        $perPage = 1000;
        $page = 1;
        
        do {
            $businesses = Business::where('status', 'active')
                ->select('id', 'slug', 'updated_at')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            foreach ($businesses as $business) {
                $xml .= '<url>';
                $xml .= '<loc>' . $baseUrl . '/' . $business->slug . '</loc>';
                $xml .= '<priority>0.8</priority>';
                $xml .= '<changefreq>weekly</changefreq>';
                $xml .= '<lastmod>' . $business->updated_at->format('c') . '</lastmod>';
                $xml .= '</url>';
            }

            $page++;
        } while ($businesses->count() === $perPage);
        
        return $xml;
    }

    private function getUrlCount()
    {
        $staticCount = 6; // Static pages
        
        $categoryCount = Category::where('status', 'active')->count();
        $subcategoryCount = Category::whereHas('parent', function ($query) {
            $query->where('status', 'active');
        })->where('status', 'active')->count();
        
        $productCount = Product::where('status', 'active')->count();
        $businessCount = Business::where('status', 'active')->count();

        return $staticCount + $categoryCount + $subcategoryCount + $productCount + $businessCount;
    }
}