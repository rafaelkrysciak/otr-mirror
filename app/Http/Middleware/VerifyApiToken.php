<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Guard;

class VerifyApiToken
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;


    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest() || !$this->auth->user()->isAdmin()) {
            $xAuth = $request->header('X-Auth');

            if ($xAuth != config('auth.api')) {
                return response('Unauthorized.', 401);
            }
        }

        return $next($request);
    }

}
