<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    // 重写isReading
    // api.php里的方法（GET、POST）都需要验证csrf，
    // web.php总是返回index页面
    public function isReading($request)
    {
        $path = $request->getPathInfo();

        // 以/api/请求的路径需要验证，
        // 导出Excel表格的路径不需要验证
        if ('local' === env('APP_ENV') ||
            ! preg_match('#^\/api\/#', $path) ||
            preg_match('#^\/api\/auth\/doLogin#', $path) ||
            preg_match('#^\/api\/getUploadSign#', $path) ||
            preg_match('#\/export#', $path)) {
            return true;
        }

        return in_array($request->method(), ['HEAD', 'OPTIONS'], true);
    }
}
