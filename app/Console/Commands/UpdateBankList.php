<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bank\BankList;
use App\Traits\Generics;
use Illuminate\Support\Facades\Http;

class UpdateBankList extends Command
{
    use Generics;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updates:bank_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command updates the various bank list';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BankList $bankList)
    {
        parent::__construct();
        $this->bankList = $bankList;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->updateBankList();
    }

    public function updateBankList(){
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.env('FL_KEY')
        ])->get('https://api.flutterwave.com/v3/banks/NG');

        $decoded_response = json_decode($response, true);

        if($decoded_response['status'] == 'success'){
            foreach($decoded_response['data'] as $response){
                $bankList = $this->bankList->getSingleBankList([
                    ['name', $response['name']],
                    ['code', $response['code']],
                ]);
                
                if($bankList === null){
                    $bank = new BankList();
                    $bank->unique_id = $this->createUniqueId('bank_lists', 'unique_id');
                    $bank->code = $response['code'];
                    $bank->name = $response['name'];
                    $bank->save();
                }else{
                    $bankList->name = $response['name'];
                    $bankList->save();
                }
            }
        }  
    }
}
