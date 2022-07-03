<?php

namespace App\Services;

use App\Models\Transaction\Transaction;

class Transactions {

    function getTotalAmount($status, $type){
        $amt = 0;
        $row = Transaction::where('status', $status)
            ->where('type', $type)
            ->get();
        foreach($row as $each_row){
            $amt += $each_row->amount;
        }
        return $amt;       
    }

    function deleteTransaction($request){
        $transaction = Transaction::where('unique_id', $request->unique_id)->first();
        if($transaction == null)
            return false;
        $transaction->delete();
        return true;
    }
}