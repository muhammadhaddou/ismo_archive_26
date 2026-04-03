<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Document;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تعريف صلاحية إدارة المستخدمين
        Gate::define('manage-users', function ($user) {
            return $user->hasRole('admin');
        });

        // Badge: عدد الـ Temp-Out المنتهية (مثال للوثائق من نوع Bac)
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $expiredBacCount = Document::where('type', 'Bac')
                    ->where('status', 'Temp_Out')
                    ->whereHas('movements', function ($q) {
                        $q->where('action_type', 'Sortie')
                          ->whereNotNull('deadline')
                          ->where('deadline', '<', now());
                    })
                    ->count();

                //
                $view->with('expiredBacCount', $expiredBacCount);
            }
        });
    }
}