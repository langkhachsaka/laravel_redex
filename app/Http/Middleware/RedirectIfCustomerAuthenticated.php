<?php
namespace App\Http\Middleware;

use Closure;
use Auth;

class RedirectIfCustomerAuthenticated
{

    public function handle($request, Closure $next)
    {
        if (Auth::guard('customer')->check()) {
            return redirect('/customer/order');
        }
        return $next($request);
    }
}