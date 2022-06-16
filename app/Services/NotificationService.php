<?php

namespace App\Services;

use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService {

    private $message = [];

    private function parse($type, $data){
        $this->message[] = [
            'type' => $type,
            'value' => $data
        ];
    }

    function send($receivers, $subject, $channels){
        Notification::send($receivers, new GeneralNotification($subject, $channels, $this->message));
    }

    function text($text){
        $this->parse('text', $text);
        return $this;
    }

    function action($action, $link){
        $this->parse('action', [
            'action' => $action,
            'link' => $link
        ]);
        return $this;
    }

    function image($image){
        $this->parse('image', $image);
        return $this;
    }

    function greeting($greeting){
        $this->parse('greeting', $greeting);
        return $this;
    }
}

