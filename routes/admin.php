<?php

use App\Enums\Permission;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'auth.session', 'active', 'can:access-admin'])
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::get('/perfil', Admin\ProfileController::class)->name('profile');

        Route::middleware('can:'.Permission::ManagePages->value)->group(function () {
            Route::get('texts', [Admin\SiteTextController::class, 'edit'])->name('texts.edit');
            Route::put('texts', [Admin\SiteTextController::class, 'update'])->name('texts.update');
            Route::resource('pages', Admin\PageController::class)->except('show');
            Route::post('pages/{page}/restore', [Admin\PageController::class, 'restore'])->name('pages.restore');
        });

        Route::middleware('can:'.Permission::ManageServices->value)->group(function () {
            Route::resource('services', Admin\ServiceController::class)->except('show');
            Route::post('services/{service}/restore', [Admin\ServiceController::class, 'restore'])->name('services.restore');
        });

        Route::middleware('can:'.Permission::ManageProjects->value)->group(function () {
            Route::resource('projects', Admin\ProjectController::class)->except('show');
            Route::post('projects/{project}/restore', [Admin\ProjectController::class, 'restore'])->name('projects.restore');
        });

        Route::middleware('can:'.Permission::ManageClients->value)->group(function () {
            Route::post('clients/reorder', [Admin\ClientController::class, 'reorder'])->name('clients.reorder');
            Route::resource('clients', Admin\ClientController::class)->except('show');
        });

        Route::resource('posts', Admin\PostController::class)->except('show');
        Route::post('posts/{post}/restore', [Admin\PostController::class, 'restore'])->name('posts.restore');
        Route::get('posts/{post}/preview', [Admin\PostController::class, 'preview'])->name('posts.preview');

        Route::middleware('can:'.Permission::ManageTaxonomies->value)
            ->prefix('categories')
            ->name('categories.')
            ->whereIn('type', array_keys(Admin\CategoryController::TYPES))
            ->whereNumber('id')
            ->group(function () {
                Route::get('/{type?}', [Admin\CategoryController::class, 'index'])->name('index');
                Route::post('/{type}', [Admin\CategoryController::class, 'store'])->name('store');
                Route::put('/{type}/{id}', [Admin\CategoryController::class, 'update'])->name('update');
                Route::delete('/{type}/{id}', [Admin\CategoryController::class, 'destroy'])->name('destroy');
            });

        Route::middleware('can:'.Permission::ManageMedia->value)->group(function () {
            Route::get('media', [Admin\MediaController::class, 'index'])->name('media.index');
            Route::post('media', [Admin\MediaController::class, 'store'])->middleware('throttle:uploads')->name('media.store');
            Route::put('media/{media}', [Admin\MediaController::class, 'update'])->name('media.update');
            Route::delete('media/{media}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');
        });

        Route::middleware('can:'.Permission::ManageLeads->value)->group(function () {
            Route::get('contact-messages/export', [Admin\ContactMessageController::class, 'export'])->name('contact-messages.export');
            Route::resource('contact-messages', Admin\ContactMessageController::class)->only(['index', 'show', 'update', 'destroy']);
            Route::get('quote-requests/export', [Admin\QuoteRequestController::class, 'export'])->name('quote-requests.export');
            Route::resource('quote-requests', Admin\QuoteRequestController::class)->only(['index', 'show', 'update', 'destroy']);
        });

        Route::middleware('can:'.Permission::ManageNewsletter->value)->group(function () {
            Route::get('newsletter', [Admin\NewsletterSubscriberController::class, 'index'])->name('newsletter.index');
            Route::get('newsletter/export', [Admin\NewsletterSubscriberController::class, 'export'])->name('newsletter.export');
            Route::delete('newsletter/{subscriber}', [Admin\NewsletterSubscriberController::class, 'destroy'])->name('newsletter.destroy');
        });

        Route::middleware('can:'.Permission::ManageUsers->value)->group(function () {
            Route::resource('users', Admin\UserController::class)->except('show');
            Route::post('users/{user}/reset-link', [Admin\UserController::class, 'sendResetLink'])->name('users.reset-link');
        });

        Route::middleware('can:'.Permission::ManageSettings->value)->group(function () {
            Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
        });

        Route::get('activity', Admin\ActivityLogController::class)
            ->middleware('can:'.Permission::ViewActivity->value)
            ->name('activity.index');
    });
