<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailySpecial\DailySpecial;
use App\Traits\ReturnTemplate;
use Carbon\Carbon;

class TrackDailySpecials extends Command
{
    use ReturnTemplate;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updates:daily_special';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command checks for daily special that is up to 24 hours and updates the status to expired';

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
        $this->updateDailySpecialStatus();
    }

    public function updateDailySpecialStatus(){
        $dailySpecial = DailySpecial::where('status', 'inprogress')->get();
        if(count($dailySpecial) > 0){
            foreach($dailySpecial as $each_specials){
                //chack if the time keeper + 24hours is over
                if($each_specials->created_at->diffInHours(now()) >= $this->dailySpecial){
                    $each_specials->status = 'expired';
                    $each_specials->save();
                }
            }
        }
    }
}
