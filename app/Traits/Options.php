<?php

namespace App\Traits;

trait Options {

    public $paginate = 12;
    public $dailySpecial = 24;
    public $active = 'active';
    public $pending = 'pending';
    public $confirmed = 'confirmed';
    public $success = 'success';
    public $inprogress = 'inprogress';
    public $verified = 'verified';
    public $suspended = 'suspended';
    public $settled = 'settled';
    public $expired = 'expired';
    public $processing = 'processing';
    public $failed = 'failed';
    public $delivered = 'delivered';
    public $declined = 'declined';
    public $cancelled = 'cancelled';
    public $yes = 'yes';
    public $no = 'no';
    public $paid = 'paid';
    public $unread = 'unread';
    public $read = 'read';

    public $orderProgression = ['paid', 'cancelled', 'declined', 'processing', 'terminated', 'done', 'enroute', 'pickedup', 'returned', 'delivered'];

    public $orderUserActions = [
        'User' => ['cancelled'],
        'Vendor' => ['declined', 'processing', 'done', 'delivered', 'terminated'],
        'Logistic' => ['enroute', 'pickedup', 'returned', 'delivered'],
        'Rider' => ['enroute', 'pickedup', 'returned', 'delivered'],
    ];

    public $orderNotificationReceivers = [
        'User' => ['declined', 'processing', 'terminated', 'enroute', 'pickedup', 'delivered', 'returned'],
        'Vendor' => ['paid', 'enroute', 'delivered'],
        'Rider' => ['paid', 'done', 'terminated']
    ];

    public function formatOrderMessages($order, $user, $vendor, $logistics){
        $messages = [
            'paid' => [
                "You have received a new food order"
            ],
            'cancelled' => ""
        ];
    }
}


