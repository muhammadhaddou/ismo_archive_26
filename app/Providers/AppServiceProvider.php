<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use Bootstrap 4 for Pagination (fixes giant SVG arrows)
        Paginator::useBootstrapFour();

        // صلاحية إدارة المستخدمين
        Gate::define('manage-users', function ($user) {
            return $user->hasRole('admin');
        });

        // Badge عدد الـ Temp-Out المنتهية
        View::composer('*', function ($view) {
            if (Auth::check()) {
                static $expiredBacCount = null;
                
                if ($expiredBacCount === null) {
                    $expiredBacCount = Document::where('type', 'Bac')
                        ->where('status', 'Temp_Out')
                        ->whereHas('movements', function ($q) {
                            $q->where('action_type', 'Sortie')
                              ->whereNotNull('deadline')
                              ->where('deadline', '<', now());
                        })
                        ->count();
                }

                $view->with('expiredBacCount', $expiredBacCount);
            }
        });
    }
}