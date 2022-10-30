<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationsController extends Controller
{
    public function show(string $code) : View
    {
        $invitation = Invitation::findByCode($code);

        abort_if($invitation->hasBeenUsed(), 404);

        return view('invitations.show', [
            'invitation' => $invitation,
        ]);
    }
}
