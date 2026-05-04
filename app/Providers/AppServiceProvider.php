<?php
namespace App\Providers;

use App\Models\BahanMasuk;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {}

    public function boot(): void
    {
        View::composer('components.layouts.admin', function ($view) {
            if (auth()->check() && auth()->user()->hasRole('admin')) {
                $expiredCount = BahanMasuk::whereNotNull('tanggal_kadaluarsa')
                    ->where(function ($q) {
                        $q->whereDate('tanggal_kadaluarsa', '<', today())
                            ->orWhere(function ($q2) {
                                $q2->whereDate('tanggal_kadaluarsa', '>=', today())
                                    ->whereDate('tanggal_kadaluarsa', '<=', today()->addDays(7));
                            });
                    })
                    ->count();

                $view->with('expiredCount', $expiredCount);
            } else {
                $view->with('expiredCount', 0);
            }
        });
    }
}
