<?php

namespace Brackets\AdminAuth\Http\Controllers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class AdminUserController extends Controller
{
    /**
     * Guard used for admin user
     *
     * @var string
     */
    protected mixed $guard = 'api';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->guard = config('admin-auth.defaults.guard');
        $this->middleware('auth:api');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard(): StatefulGuard
    {
        return auth()->guard($this->guard);
    }
}
