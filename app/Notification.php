<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    public $timestamps = false;
    protected $table = 'notifications';
    protected $fillable = array('campaign_no','msg');
}
