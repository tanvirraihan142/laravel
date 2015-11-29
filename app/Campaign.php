<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    //
    public $timestamps = false;
    protected $table = 'campaigns';
    protected $fillable = array('campaign_name','vaccine_name','cho_id','campaign_date','start_age','end_age');
    public function setDate($value){
       $this->attributes['campaign_date'] = Carbon::createFromFormat('d/m/Y', $value);
   	}
}
