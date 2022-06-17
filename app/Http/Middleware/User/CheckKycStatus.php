<?php

namespace App\Http\Middleware\User;

use App\Traits\ReturnTemplate;
use Closure;
use Illuminate\Http\Request;

class CheckKycStatus{
    use ReturnTemplate;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $user = $request->user();
        if(in_array($user->role, ['vendors', 'logistics']) && $user->kyc_status === 'confirmed' ) return $next($request);
        return $this->returnMessageTemplate(false, "Your KYC has not been approved!");
    }
}
