<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class SnakingParameters
{
    public function handle($request, Closure $next)
    {
        $snakedParameters = [];

        foreach ($request->all() as $key => $value) {
            $snakedKey = Str::snake($key);
            $snakedParameters[$snakedKey] = $value;
        }

        $request->merge($snakedParameters);

        return $next($request);
    }
}
