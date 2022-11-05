<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\Helpers\ForceStripeMiddlewareInvokable;
use Tests\Helpers\Invokable;

class ForceStripeAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure|ForceStripeMiddlewareInvokable  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure|ForceStripeMiddlewareInvokable $next)
    {
        if (Auth::user()->stripe_account_id === NULL) {
            return redirect()->route('backstage.stripe-connect.connect');
        }

        return $next($request);
    }
}
