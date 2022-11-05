<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeConnectController extends Controller
{
    public function connect()
    {
        return view('backstage.stripe-connect.connect');
    }

    public function authorizeRedirect() : RedirectResponse
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

    public function redirect() : RedirectResponse
    {
        // Make a request to stripe to get the access token
        $accessTokenResponse = Http::asForm()->post('https://connect.stripe.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => request('code'),
            'client_secret' => config('services.stripe.secret')
        ])->json();

        // Store the stripe user id and stripe access token contained in the response above
        Auth::user()->update([
            'stripe_account_id' => $accessTokenResponse['stripe_user_id'],
            'stripe_access_token' => $accessTokenResponse['access_token']
        ]);

        return redirect()->route('backstage.concerts.index');
    }
}
