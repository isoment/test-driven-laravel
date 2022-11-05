<?php

namespace Tests\Helpers;

use Illuminate\Http\Request;

/**
 *  This class is an invokable class that we can use for testing the ForceStripeMiddleware
 *  We set a $called property to assert that this class was invoked and we allow a request to be
 *  passed in to assert that the request matches the return value of handle() since that method
 *  returns the $next closure.
 */
class ForceStripeMiddlewareInvokable
{
    public bool $called = false;

    public function __invoke(Request $request)
    {
        $this->called = true;
        return $request;
    }
}