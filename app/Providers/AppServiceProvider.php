<?php

namespace App\Providers;

use App\Enums\Permission;
use App\Models\Client;
use App\Models\ContactMessage;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Tag;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\HtmlSanitizer;
use App\Services\SettingsRepository;
use App\View\Composers\SiteNavigationComposer;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsRepository::class);
        $this->app->singleton(HtmlSanitizer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        Relation::enforceMorphMap([
            'user' => User::class,
            'page' => Page::class,
            'service' => Service::class,
            'service_category' => ServiceCategory::class,
            'project' => Project::class,
            'project_category' => ProjectCategory::class,
            'client' => Client::class,
            'post' => Post::class,
            'post_category' => PostCategory::class,
            'tag' => Tag::class,
            'media' => Media::class,
            'contact_message' => ContactMessage::class,
            'quote_request' => QuoteRequest::class,
        ]);

        Password::defaults(fn () => Password::min(10)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->when($this->app->isProduction(), fn (Password $rule) => $rule->uncompromised()));

        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');

        View::composer(['components.site.header', 'components.site.footer'], SiteNavigationComposer::class);

        Blade::directive('richtext', fn (string $expression): string => "<?php echo app(\\App\\Services\\HtmlSanitizer::class)->sanitize({$expression}); ?>");

        $this->configureAuthorization();
        $this->configureRateLimiting();
        $this->configureAuthEvents();
    }

    private function configureAuthorization(): void
    {
        // Super administrators bypass every check except user-account rules (e.g. deleting themselves).
        Gate::before(function (User $user, string $ability, array $arguments): ?bool {
            if (! $user->isSuperAdmin() || ! $user->is_active) {
                return null;
            }

            $target = $arguments[0] ?? null;

            return $target === User::class || $target instanceof User ? null : true;
        });

        foreach (Permission::cases() as $permission) {
            Gate::define($permission->value, fn (User $user): bool => $user->hasPermission($permission));
        }

        Gate::define('access-admin', fn (User $user): bool => $user->is_active);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('forms', fn (Request $request) => [
            Limit::perMinute(5)->by('forms:'.$request->ip()),
            Limit::perDay(50)->by('forms-day:'.$request->ip()),
        ]);

        RateLimiter::for('newsletter', fn (Request $request) => Limit::perMinute(3)->by('newsletter:'.$request->ip()));

        RateLimiter::for('uploads', fn (Request $request) => Limit::perMinute(60)->by('uploads:'.$request->user()?->id));
    }

    private function configureAuthEvents(): void
    {
        Event::listen(function (Login $event): void {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
                app(ActivityLogger::class)->log('login', 'Iniciou sessão', $event->user, user: $event->user);
            }
        });

        Event::listen(function (Logout $event): void {
            if ($event->user instanceof User) {
                app(ActivityLogger::class)->log('logout', 'Terminou sessão', $event->user, user: $event->user);
            }
        });

        Event::listen(function (Failed $event): void {
            app(ActivityLogger::class)->log('login_failed', 'Tentativa de início de sessão falhada', properties: [
                'email' => $event->credentials['email'] ?? null,
            ]);
        });

        Event::listen(function (PasswordReset $event): void {
            if ($event->user instanceof User) {
                app(ActivityLogger::class)->log('password_reset', 'Redefiniu a palavra-passe', $event->user, user: $event->user);
            }
        });
    }
}
