<?php

use App\Http\Controllers\Site;
use Illuminate\Support\Facades\Route;

Route::get('/', Site\HomeController::class)->name('home');
Route::get('/sobre-nos', Site\AboutController::class)->name('about');

Route::get('/servicos', [Site\ServiceController::class, 'index'])->name('services.index');
Route::get('/servicos/{slug}', [Site\ServiceController::class, 'show'])->name('services.show');

Route::get('/portfolio', [Site\PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [Site\PortfolioController::class, 'show'])->name('portfolio.show');

Route::get('/blog', [Site\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/categoria/{slug}', [Site\BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/etiqueta/{slug}', [Site\BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{slug}', [Site\BlogController::class, 'show'])->name('blog.show');

Route::get('/contactos', [Site\ContactController::class, 'show'])->name('contact');

Route::middleware('throttle:forms')->group(function () {
    Route::post('/contactos', [Site\ContactController::class, 'store'])->name('contact.store');
    Route::post('/propostas', [Site\QuoteRequestController::class, 'store'])->name('quote.store');
});

Route::post('/newsletter', [Site\NewsletterController::class, 'store'])->middleware('throttle:newsletter')->name('newsletter.store');
Route::get('/newsletter/cancelar/{token}', [Site\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/sitemap.xml', [Site\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [Site\SitemapController::class, 'robots'])->name('robots');

require __DIR__.'/admin.php';

Route::get('/{slug}', Site\PageController::class)
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('pages.show');
