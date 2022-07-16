<?php

namespace App\Http\Middleware\User;

use App\Models\Role\AccountRole;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use App\Traits\Options;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CheckKycStatus{
    use ReturnTemplate, Options, Generics;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role){
        $user = $request->user();
        $userRole = AccountRole::find($user->role);

        // return Response::json([
        //     'userRole' => $userRole,
        // ]);
        if(in_array($userRole->name, ['Vendor', 'Logistic']) && $user->kyc_status == $this->confirmed && $role == $userRole->name) {
            return $next($request);
        }

        return $this->returnMessageTemplate(false, "Your $userRole->name Account has not been verified");
    }
}
