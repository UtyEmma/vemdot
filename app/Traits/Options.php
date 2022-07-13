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

    public $orderProgression = ['paid', 'cancelled', 'declined', 'processing', 'terminated', 'done', 'enroute', 'pickedup', 'failed', 'delivered'];

    public $orderUserActions = [
        'User' => ['cancelled'],
        'Vendor' => ['declined', 'processing', 'done', 'delivered', 'terminated'],
        'Logistic' => ['enroute', 'pickedup', 'failed', 'delivered'],
        'Rider' => ['enroute', 'pickedup', 'delivered'],
    ];
}


