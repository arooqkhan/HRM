<?php

namespace App\Providers;

use App\Models\AccouncementDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
          
            if (Auth::check()) {
                $user = Auth::user();
    
                
                $documents = AccouncementDocument::with('employee')
                    ->where('status', 0)
                    ->when(
                        !in_array($user->role, ['admin', 'HR', 'Accountant']),
                        fn($query) => $query->where('employee_id', $user->employee_id)
                    )
                    ->get();
    
                // Share data globally with all views
                View::share('headerDocuments', $documents);
            } else {
                View::share('headerDocuments', collect()); // Share empty collection if no user
            }
        });
    }
}



