<?php

namespace App\Actions\Fortify;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    // /**
    //  * Validate and create a newly registered user.
    //  *
    //  * @param  array  $input
    //  * @return \App\Models\User
    //  */
    // public function create(array $input)
    // {
    //     Validator::make($input, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => [
    //             'required',
    //             'string',
    //             'email',
    //             'max:255',
    //             Rule::unique(User::class),
    //         ],
    //         'password' => $this->passwordRules(),
    //     ])->validate();

    //     return User::create([
    //         'name' => $input['name'],
    //         'email' => $input['email'],
    //         'password' => Hash::make($input['password']),
    //     ]);
    // }

    /**
     * Validate and create a newly registered user.
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input) : User
    {
        Validator::make($input, [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
        ])->validate();

        $invitation = Invitation::findByCode($input['invitation_code']);

        abort_if($invitation->hasBeenUsed(), 404);

        $user = User::create([
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $invitation->update([
            'user_id' => $user->id
        ]);

        return $user;
    }
}
