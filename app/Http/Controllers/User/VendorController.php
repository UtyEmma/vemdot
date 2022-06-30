<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendors\VendorLogistic;
use Illuminate\Http\Request;

class VendorController extends Controller{

    function addCompany($company_id){
        $company = User::findOrFail($company_id);
        if($company->userRole->exists() && $company->userRole->name !=  'Logistic')
                return $this->returnMessageTemplate(false, "The Selected user is not a Logistics Company");

        $user = $this->user();

        $is_company = $user->logisticCompany()->where([
            'vendor_id' => $user->unique_id,
            'company_id' => $company_id
        ])->exists();

        if($is_company) return $this->returnMessageTemplate(false, "This company already exists in your list");

        VendorLogistic::create([
            'unique_id' => $this->createUniqueId('vendor_logistics'),
            'vendor_id' => $user->unique_id,
            'company_id' => $company->unique_id
        ]);

        $companies = $user->logisticCompany()->with(['company'])->get();

        return $this->returnMessageTemplate(true, 'Logistic Company added to your list', [
            'logistics' => $companies
        ]);
    }

    function listCompanies ($vendor_id = null){
        $vendor = !!$vendor_id ? User::findOrFail($vendor_id) : $this->user();
        $companies = $vendor->logisticCompany()->with(['company'])->get();
        return $this->returnMessageTemplate(true, '', $companies);
    }

    // function list (){}
}
