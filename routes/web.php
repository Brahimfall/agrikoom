<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobOfferController as AdminJobOfferController;
use App\Http\Controllers\Admin\MarketPriceController as AdminMarketPriceController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactSellerController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\TrackContactClickController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ListingController::class, 'index'])->name('home');
Route::get('/annonces', [ListingController::class, 'index'])->name('listings.index');
Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/annonces/{listing}', [ListingController::class, 'show'])->name('listings.show');
Route::post('/annonces/{listing}/track-contact', TrackContactClickController::class)->name('listings.track-contact');
Route::get('/emplois', [JobOfferController::class, 'index'])->name('jobs.index');
Route::get('/emplois/{jobOffer}', [JobOfferController::class, 'show'])->name('jobs.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{article:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/dashboard', function () {
    if (auth()->user()?->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard.short');
    Route::get('/admin/dashboard', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/admin/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('admin.dashboard.stats');
    Route::get('/admin/emplois', [AdminJobOfferController::class, 'index'])->name('admin.jobs.index');
    Route::get('/admin/emplois/ajouter', [AdminJobOfferController::class, 'create'])->name('admin.jobs.create');
    Route::post('/admin/emplois', [AdminJobOfferController::class, 'store'])->name('admin.jobs.store');
    Route::get('/admin/emplois/{jobOffer}/modifier', [AdminJobOfferController::class, 'edit'])->name('admin.jobs.edit');
    Route::patch('/admin/emplois/{jobOffer}', [AdminJobOfferController::class, 'update'])->name('admin.jobs.update');
    Route::delete('/admin/emplois/{jobOffer}', [AdminJobOfferController::class, 'destroy'])->name('admin.jobs.destroy');

    Route::get('/admin/mercuriale', [AdminMarketPriceController::class, 'index'])->name('admin.mercuriale.index');
    Route::get('/admin/mercuriale/ajouter', [AdminMarketPriceController::class, 'create'])->name('admin.mercuriale.create');
    Route::post('/admin/mercuriale', [AdminMarketPriceController::class, 'store'])->name('admin.mercuriale.store');
    Route::get('/admin/mercuriale/{marketPrice}/modifier', [AdminMarketPriceController::class, 'edit'])->name('admin.mercuriale.edit');
    Route::patch('/admin/mercuriale/{marketPrice}', [AdminMarketPriceController::class, 'update'])->name('admin.mercuriale.update');
    Route::patch('/admin/mercuriale/{marketPrice}/toggle', [AdminMarketPriceController::class, 'toggle'])->name('admin.mercuriale.toggle');
    Route::delete('/admin/mercuriale/{marketPrice}', [AdminMarketPriceController::class, 'destroy'])->name('admin.mercuriale.destroy');

    Route::get('/admin/articles', [AdminArticleController::class, 'index'])->name('admin.articles.index');
    Route::get('/admin/articles/creer', [AdminArticleController::class, 'create'])->name('admin.articles.create');
    Route::post('/admin/articles', [AdminArticleController::class, 'store'])->name('admin.articles.store');
    Route::get('/admin/articles/{article}/modifier', [AdminArticleController::class, 'edit'])->name('admin.articles.edit');
    Route::patch('/admin/articles/{article}', [AdminArticleController::class, 'update'])->name('admin.articles.update');
    Route::patch('/admin/articles/{article}/toggle', [AdminArticleController::class, 'toggle'])->name('admin.articles.toggle');
    Route::delete('/admin/articles/{article}', [AdminArticleController::class, 'destroy'])->name('admin.articles.destroy');

    Route::get('/publier', [ListingController::class, 'create'])->name('listings.create');
    Route::get('/mes-annonces', [ListingController::class, 'mine'])->name('listings.mine');
    Route::get('/annonces/{listing}/modifier', [ListingController::class, 'edit'])->name('listings.edit');
    Route::delete('/annonces/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
    Route::patch('/annonces/{listing}/vendue', [ListingController::class, 'markSold'])->name('listings.sold');
    Route::patch('/annonces/{listing}/renouveler', [ListingController::class, 'renew'])->name('listings.renew');
    Route::post('/annonces/{listing}/contact', ContactSellerController::class)->name('listings.contact');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
