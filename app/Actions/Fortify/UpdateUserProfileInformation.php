<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'fname' => ['required', 'string', 'min:4', 'max:45'],
            'lname' => ['required', 'string', 'min:4', 'max:45'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'address' => ['string', 'min:8', 'max:85'],
            'city' => ['string', 'min:8', 'max:45'],
            'state' => ['string', 'min:8', 'max:45'],
            'zip' => ['string', 'min:6', 'max:25'],
        ])->validateWithBag('updateProfileInformation');

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'fname' => $input['fname'],
                'lname' => $input['lname'],
                'email' => $input['email'],
                'address' => $input['address'],
                'state' => $input['state'],
                'city' => $input['city'],
                'zip' => $input['zip'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'fname' => $input['fname'],
            'lname' => $input['lname'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'address' => $input['address'],
            'state' => $input['state'],
            'city' => $input['city'],
            'zip' => $input['zip'],
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
