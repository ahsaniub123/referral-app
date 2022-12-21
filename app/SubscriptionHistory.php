<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    protected $dates = [
        'started_at', 'ended_at'
    ];

    public function subscription_plan() {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
