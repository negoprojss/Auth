<?php

namespace App\Http\Middleware;

use App\Models\App;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppAccessMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $slug
    ): Response {

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // El administrador tiene acceso a todo.
        if ($user->isAdmin()) {
            return $next($request);
        }

        $hasAccess = $user->apps()
            ->where('slug', $slug)
            ->where('active', true)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'No tienes acceso a esta aplicación.');
        }

        return $next($request);
    }
}
