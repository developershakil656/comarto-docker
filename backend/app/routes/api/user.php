<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessDetailController;
use App\Http\Controllers\BusinessGalleryController;
use App\Http\Controllers\BusinessLeadController;
use App\Http\Controllers\BusinessNecessaryController;
use App\Http\Controllers\BusinessReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\FavouriteLeadController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\Frontend\FrontendNecessaryController;
use App\Http\Controllers\Frontend\HomePageController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\LeadCreditPackageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductGalleryController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\LastUserActivity;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {

    Route::name('frontend.')->group(function () {
        #Home page routes
        Route::get('top/categories', [HomePageController::class, 'top_categories'])->name('top.categories');
        Route::get('suggested/categories', [HomePageController::class, 'suggested_categories'])->name('suggested.categories');

        Route::get('search', [FrontendNecessaryController::class, 'search'])->name('search');

        Route::get('product/{slug}', [FrontendNecessaryController::class, 'product'])->name('product');
        Route::get('business/products/{business_id}', [FrontendNecessaryController::class, 'business_products'])->name('business.products');
        Route::get('business/{business_slug}', [FrontendNecessaryController::class, 'business'])->name('business');
        Route::get('business/details/{business_id}', [FrontendNecessaryController::class, 'business_details'])->name('business.details');
        Route::get('business/reviews/{business_id}', [FrontendNecessaryController::class, 'business_reviews'])->name('business.reviews');
        Route::get('more/products/from/{business_id}/{except_product_id?}', [FrontendNecessaryController::class, 'more_products_from_seller'])->name('more.products');
        Route::get('related/products/{product_id}', [FrontendNecessaryController::class, 'related_products'])->name('related.products');
        Route::get('related/categories/{product_id}', [FrontendNecessaryController::class, 'related_categories'])->name('related.categories');

        Route::get('all/business/types', [FrontendNecessaryController::class, 'business_types'])->name('business.types');
        Route::post('locations', [FrontendNecessaryController::class, 'locations'])->name('locations');
        Route::get('categories', [FrontendNecessaryController::class, 'categories'])->name('categories');
        Route::get('category/children/{slug}', [FrontendNecessaryController::class, 'category_children'])->name('category.children');
        
    });

    #user login and logout
    Route::prefix('user')->group(function () {
        #user login
        Route::post('mobile/send/otp', [AuthController::class, 'send_otp'])->name('send.otp');
        Route::post('mobile/verify/otp', [AuthController::class, 'verify_otp'])->name('verify.otp');
        Route::post('mobile/login', [AuthController::class, 'mobile_login'])->name('mobile.login');
        Route::post('/google/login', [AuthController::class, 'google_login'])->name('google.login');

        #all authenticate routes
        Route::middleware(['auth:api', 'scope:user'])->group(function () {
            #authenticate user's necessary routes
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

            #profile update
            Route::get('/details', [AuthController::class, 'details'])->name('details');
            Route::put('/profile', [AuthController::class, 'profile_update'])->name('profile.update');
            Route::put('/name', [AuthController::class, 'name_update'])->name('name.update');
            Route::put('/google/update', [AuthController::class, 'google_update'])->name('google.update');
            Route::put('/location', [AuthController::class, 'location_update'])->name('location.update');
            Route::post('/account/verification', [AuthController::class, 'account_verification'])->name('account.verificaion');

            #all resource
            Route::apiResources([
                'my/business' => BusinessController::class,
                // 'business/gallery' => BusinessGalleryController::class,
                'products' => ProductController::class,
                // 'product/gallery' => ProductGalleryController::class,
                'business/review' => BusinessReviewController::class,
                'favourites' => FavouriteController::class,
                'followings' => FollowingController::class,
            ]);

            
            Route::post('business/slug/check', [BusinessController::class, 'checkSlugAvailability'])->name('business.slug.check');

            // Separate ProductGalleryController with unique names
            Route::apiResource('business/gallery', BusinessGalleryController::class)
                ->names('businessgallery');
            Route::apiResource('product/gallery', ProductGalleryController::class)
                ->names('productgallery');

            // --- necessary routes for authenticate seller ---
            Route::prefix('business')->name('business')->group(function () {
                Route::put('profile', [BusinessController::class, 'profile'])->name('.profile');
                Route::get('categories', [BusinessController::class, 'categories'])->name('.categories');

                #business details
                Route::get('details', [BusinessDetailController::class, 'index'])->name('.details.get');
                Route::put('details', [BusinessDetailController::class, 'update'])->name('.details.update');

                #locations authenticate user end
                Route::post('locations', [BusinessNecessaryController::class, 'locations'])->name('.locations');

                #product necessaries for authenticate user end
                Route::post('product/categories', [BusinessNecessaryController::class, 'categories'])->name('.product.categories')->withoutMiddleware(['auth:api', 'scope:user']);
                Route::post('product/units', [BusinessNecessaryController::class, 'product_units'])->name('.product.units')->withoutMiddleware(['auth:api', 'scope:user']);

                Route::name('lead')->group(function () {
                    Route::get('/leads', [BusinessLeadController::class, 'index'])->name('.index'); // list discoverable leads (filterable)
                    Route::post('/leads/{lead}/unlock', [BusinessLeadController::class, 'unlock'])->name('.unlock');
                    Route::get('/my/leads', [BusinessLeadController::class, 'myUnlocked'])->name('.myUnlocked'); // leads this business unlocked

                    Route::get('/lead/credits/packages', [LeadCreditPackageController::class, 'packages'])->name('.packages');
                    Route::post('/lead/credits/purchase', [LeadCreditPackageController::class, 'purchase'])->name('.purchase');
                    Route::get('/lead/credits/purchase', [LeadCreditPackageController::class, 'purchases']);


                    Route::post('/leads/{lead}/favourite', [FavouriteLeadController::class, 'toggle'])->name('.favourite');
                    Route::get('/leads/favourites', [FavouriteLeadController::class, 'index'])->name('.all.favourite');
                });
            });

            Route::name('inquiry')->group(function () {
                Route::get('/inquiries', [InquiryController::class, 'index'])->name('.index');
                Route::post('/inquiries', [InquiryController::class, 'store'])->name('.store');
                Route::post('/inquiries/{id}/close', [InquiryController::class, 'close'])->name('.close');
            });

            # all message/chatting routes
            Route::name('conversation')->group(function () {
                Route::post('/conversations/start', [ChatController::class, 'startConversation'])->name('.start')->withoutMiddleware([LastUserActivity::class]);
                Route::get('/conversations', [ChatController::class, 'conversations'])->name('.get');
                Route::get('/conversations/{id}/messages', [ChatController::class, 'messages'])->name('.messages')->withoutMiddleware([LastUserActivity::class]);
                Route::post('/conversations/{id}/messages', [ChatController::class, 'sendMessage'])->name('.message.store')->withoutMiddleware([LastUserActivity::class]);
            });
        });
    });

    // Sitemap routes
    Route::get('sitemap', [SitemapController::class, 'index'])->name('sitemap.index');
    Route::get('sitemap/count', [SitemapController::class, 'count'])->name('sitemap.count');
});

