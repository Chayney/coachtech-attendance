<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::registerView(function () {
            return view('auth.register');
        });     
        Fortify::loginView(function () {
            return view('auth.login');
        });
        Fortify::ignoreRoutes();
        Fortify::authenticateUsing(function ($request) {
            $loginRequest = new LoginRequest();
            $loginRequest->setContainer(app())->validateResolved();
            if ($loginRequest->fails()) {
                return null;
            }
            $credentials = $request->only('email', 'password');
            $user = User::where('email', $credentials['email'])->first(); 
            if ($user && Hash::check($credentials['password'], $user->password)) { 
                return $user; 
            }

            return null;
        });
    }
}
