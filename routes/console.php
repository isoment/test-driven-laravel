<?php

use App\Facades\InvitationCode;
use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('invite-promoter {email}', function($email) {
    $invitation = Invitation::create([
        'email' => $email,
        'code' => InvitationCode::generate(),
    ]);
    $invitation->send();
})->describe('Invite a new promoter to create an account');
