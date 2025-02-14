<?php

namespace Brackets\AdminAuth\Traits;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

trait AuthenticatesUsers
{
    use ThrottlesLogins;

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return RedirectResponse|Response|JsonResponse
     *
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($token = $this->attemptLogin($request)) {
            return $this->sendLoginResponse($request, $token);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param Request $request
     * @return void
     *
     * @throws ValidationException
     */
    protected function validateLogin(Request $request): void
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request): bool
    {
        return $this->guard()->attempt(
            $this->credentials($request)
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request): array
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param  mixed  $user
     * @return void
     */
    protected function authenticated(Request $request, mixed $user): void
    {
        if(Schema::hasColumn($user->getTable(), 'last_login_at')) {
            $user->last_login_at = now();
            $user->save();
        }
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     * @return JsonResponse
     */
    protected function sendLoginResponse(Request $request, string $token): JsonResponse
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $this->respondWithToken($token, 204);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param  int $code
     * @return JsonResponse
     */
    protected function respondWithToken(string $token, int $code): JsonResponse
    {
        $response = [
            'access_token' => $token,
            'token_type' => config('admin-auth.token_type'),
            'expires_in' => $this->calcTokenExpiration()
        ];

        return response()->json($response, $code);
    }

    protected function calcTokenExpiration(): int
    {
        return $this->guard()->factory()->getTTL() * 60;
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request $request
     * @return Response
     *
     */
    protected function sendFailedLoginResponse(Request $request): Response
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username(): string
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function logout(Request $request): Response|RedirectResponse
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return new Response('', 204);
    }

    /**
     * The user has logged out of the application.
     *
     * @param Request $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard(): StatefulGuard
    {
        return Auth::guard($this->guard);
    }
}