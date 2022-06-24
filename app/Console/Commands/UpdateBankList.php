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
    public function __construct()
    {
        parent::__construct();
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
            'Authorization' => 'Bearer '.env('PAYSTACK_SECRET_KEY')
        ])->get('https://api.paystack.co/bank', [
            'country' => 'nigeria',
        ]);

        $decoded_response = json_decode($response, true);

        if($decoded_response['status']){
            foreach($decoded_response['data'] as $response){
                $bankList = BankList::where('name', $response['name'])
                ->where('code', $response['code'])
                ->first();
                
                if($bankList == null){
                    BankList::create([
                        'unique_id' => $this->createUniqueId('bank_lists'),
                        'name' => $response['name'],
                        'slug' => $response['slug'],
                        'code' => $response['code'],
                        'longcode' => $response['longcode'],
                        'country' => $response['country'],
                        'currency' => $response['currency'],
                        'type' => $response['type'],
                    ]);
                }else{
                    $bankList->name = $response['name'];
                    $bankList->slug = $response['slug'];
                    $bankList->save();
                }
            }
        }  
    }
}
