<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
  public function handle(Request $request, Closure $next, ...$roles)
{
    if (!auth()->check()) {
        return redirect('/login');
    }

    // Example: auth()->user()->role = 'admin' or 'adviser'
    if (!in_array(auth()->user()->role, $roles)) {
        abort(403, 'Unauthorized access.');
    }

    return $next($request);
}

}
