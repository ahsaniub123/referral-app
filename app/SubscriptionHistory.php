<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    protected $dates = [
        'started_at', 'ended_at'
    ];
}
