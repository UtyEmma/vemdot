<?php

namespace App\Http\Middleware;

use App\Models\Role\AccountRole;
use App\Traits\ReturnTemplate;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CheckUserRole{
    use ReturnTemplate;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role){
        $user = $request->user();
        $accountRole = AccountRole::find($user->role);

        if($accountRole->name === $role) return $next($request);

        abort(401, "You are not authorized to make this request");
    }
}
