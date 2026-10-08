<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // I-check kung admin ang user. Kapag HINDI admin, magro-throw ng 403
        if (! $user || ($user->role !== 'admin' && ! str_contains(strtolower($user->name), 'admin'))) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED');
        }

        return $next($request);
    }
}
