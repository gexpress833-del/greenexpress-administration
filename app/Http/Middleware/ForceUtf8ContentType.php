<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceUtf8ContentType
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response->headers->has('Content-Type')) {
            $response->headers->set('Content-Type', 'text/html; charset=utf-8');
        } elseif (! str_contains($response->headers->get('Content-Type'), 'charset=')) {
            $response->headers->set('Content-Type', $response->headers->get('Content-Type').'; charset=utf-8');
        }

        return $response;
    }
}
