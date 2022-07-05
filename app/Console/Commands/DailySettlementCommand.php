<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class DailySettlementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:earnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update earnings every day and settle users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $users = User::whereRelation('userRole', 'name', 'Vendor')
                        ->orWhereRelation('userRole', 'name', 'Logistic')
                        ->where('pending_balance', '>', 0)->get();

        $users->map(function($user){
        $user->main_balance += $user->pending_balance;
        $user->pending_balance = 0;
        $user->save();
        });

        $notificationService = new NotificationService();
        $notificationService->subject("Your Wallet Balance has been updated")
                    ->text("Your earnings have been added to your Wallet balance and is now available for access.")
                    ->text('You can now spend your available funds on the '.env('APP_NAME').' platform or withdraw your funds.')
                    ->text('Thank you for staying with us!')
                    ->send($users, ['mail']);
    }
}
