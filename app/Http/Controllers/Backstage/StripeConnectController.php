<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StripeConnectController extends Controller
{
    public function authorizeRedirect()
    {
        $url = vsprintf('%s?%s', [
            'https://connect.stripe.com/oauth/v2/authorize',
            http_build_query([
                'response_type' => 'code',
                'scope' => 'read_write',
                'client_id' => config('services.stripe.client_id')
            ])
        ]);

        return redirect($url);
        // return redirect('https://connect.stripe.com/oauth/v2/authorize?response_type=code&scope=read_write&client_id' . config('services.stripe.client_id'));
    }
}
