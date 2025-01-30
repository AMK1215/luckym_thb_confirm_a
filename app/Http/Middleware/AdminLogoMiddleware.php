<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AdminLogoMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $logoFilename = $user->agent_logo;
            if ($user->hasRole('Agent') || $user->hasRole('Sub Agent')) {
                $siteName = 'Agent Dashboard';
            } else {
                $siteName = $user->site_name ?? 'LuckyM';
            }

            $adminLogo = $logoFilename
                ? asset('assets/img/logo/'.$logoFilename)
                : asset('assets/img/main_logo.png');

            View::share([
                'adminLogo' => $adminLogo,
                'siteName' => $siteName,
            ]);
        }

        return $next($request);
    }
}
