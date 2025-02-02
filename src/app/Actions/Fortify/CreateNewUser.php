<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $request = new RegisterRequest();
        $rules = $request->rules();
        $messages = $request->messages();
        $validator = Validator::make($input, $rules, $messages);
        try {
            $validated = $validator->validate();
        } catch (ValidationException $e) {
            return redirect('/register')->withErrors($e->validator)->withInput();
        }
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        return $user;
    }
}