<?php

namespace App\Http\Middleware;

use App\repositories\repo_user;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class mw_last_actived_time
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check())  //用户已认证
        {
            $expire_time = 5 * 60; //单位:秒
            Redis::setex('user_online_' . Auth::user()->id, $expire_time, true);
        }
        return $next($request);
    }
}
