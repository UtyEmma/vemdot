<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction\Transaction;
use App\Services\Transactions;
use Carbon\Carbon;

use RealRashid\SweetAlert\Facades\Alert;

class AdsTransaction extends Controller
{
    //
    protected function adsTransactionInterface(Transactions $transaction, $startDate = null, $endDate = null){
        $condition = [
            ['type', 'vendor_subscription']
        ];
        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $condition[] = ['created_at', '>=', $startDate];
            $condition[] = ['created_at', '<', $endDate];
        }

        $transactions = Transaction::where($condition)
            ->orderBy('id', 'desc')
            ->paginate($this->paginate);   
        $payload = [
            'transactions' => $transactions,
            'pendingAmount' => $transaction->getTotalAmount($this->pending, 'vendor_subscription'),
            'failedAmount' => $transaction->getTotalAmount($this->failed, 'vendor_subscription'),
            'confirmedAmount' => $transaction->getTotalAmount($this->confirmed, 'vendor_subscription'),
        ];
        return view('pages.transaction.ads-transaction', $payload);
    }

    protected function getTransactionByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('transaction/ads/interface/'.$startDate.'/'.$endDate);
    }

    protected function getTransactionByType(Request $request){
        $type = $request->user_type;
        return redirect()->to('transaction/ads/interface/'.$type);
    }

    protected function deletetransaction(Transactions $transaction, Request $request){
        $transactions = $transaction->deleteTransaction($request);
        if($transactions == false){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Transaction'));
            return redirect()->back();
        }
        Alert::success('Success', $this->returnSuccessMessage('deleted', 'Transaction'));
        return redirect()->back();
    }
}
