<?php

namespace App\Http\Middleware;

use App\Facades\Settings;
use Auth;
use Closure;

class PrivacyMode {
    /**
     * Redirects users without an alias to the dA account linking page,
     * and banned users to the ban page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Settings::get('privacy_mode') == 1 && !Auth::user()) {
            return response(view('layouts.privacy'));
        }

        return $next($request);
    }
}
