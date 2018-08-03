<?php

namespace App\Http\Middleware;

use Closure;

class VerifyReferer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $scheme_host = $request->getSchemeAndHttpHost();
        $referer = $request->server('HTTP_REFERER') ?? '';
        $path = $request->getPathInfo();

        if (1 !== preg_match('/^'.preg_quote($scheme_host, '/').'/', $referer) &&
            1 !== preg_match('#\/export#', $path)) {
            $data = [
                'code' => 40001,
                'msg' => 'invalid referer!',
            ];

            return response()->json($data);
        }

        return $next($request);
    }
}
