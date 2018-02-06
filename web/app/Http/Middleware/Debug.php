<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/23
 * Time: 17:10
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class Debug
{
    public static $beginTime = 0;
    /**
     * Handle an incoming request.
     * __debug
     * b 00000001111
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $debug = $request->input('__debug');
        if ($debug & 0x1) {
            self::$beginTime = microtime(1);
        }

        if ($debug & 0x2) {
            DB::connection()->enableQueryLog();
        }

        return $next($request);
    }
}