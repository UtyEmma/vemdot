<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction\Transaction;
use App\Services\Transactions;
use Carbon\Carbon;

class WalletFundTransaction extends Controller
{
    // 
    protected function fundWalletTransactionInterface(Transactions $transaction, $startDate = null, $endDate = null){
        $condition = [
            ['type', 'fund_wallet']
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
            'pendingAmount' => $transaction->getTotalAmount($this->pending, 'fund_wallet'),
            'failedAmount' => $transaction->getTotalAmount($this->failed, 'fund_wallet'),
            'confirmedAmount' => $transaction->getTotalAmount($this->confirmed, 'fund_wallet'),
        ];
        return view('pages.transaction.fund-wallet-transaction', $payload);
    }

    protected function getTransactionByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('transaction/fundwallet/interface/'.$startDate.'/'.$endDate);
    }

    protected function getTransactionByType(Request $request){
        $type = $request->user_type;
        return redirect()->to('transaction/fundwallet/interface/'.$type);
    }
}
