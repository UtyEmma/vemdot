<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\Subscription;
use App\Models\Plan\SubscriptionPlan;
use App\Models\Meal\Meal;
use App\Traits\Options;
use Carbon\Carbon;

class SubscriptionChecker extends Command
{
    use Options;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updates:subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command fetches all the subscription and comfirms which is still active and not';

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
        $this->handleSubscriptionStatus();
    }

    public function handleSubscriptionStatus(){
        $subscription = Subscription::where('status', $this->inprogress)->get();
        foreach($subscription as $each_subscription){
            $plan = SubscriptionPlan::where('unique_id', $each_subscription->plan_id)
            ->where('status', $this->active)
            ->first();     
            if($plan != null){
                if($subscription->start_date->diffInDays(now()) >= $plan->duration){
                    $each_subscription->update([
                        'status' => $this->expired,
                    ]);
                    foreach($subscription->meal_id as $eachMeal){
                        Meal::where('unique_id', $eachMeal)->update(['promoted' => $this->no]);
                    }
                }
            }       
        }
    }
}
