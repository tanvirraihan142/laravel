<?php

namespace App;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class VaccRecord extends Model
{
    //
    public $timestamps = false;
    protected $table = 'vacc_record';
    protected $fillable = array('patient_id','healthasst_id','center','vacc_date','vaccine');
    
    public function setDate($value){
       $this->attributes['vacc_date'] = Carbon::createFromFormat('d/m/Y', $value);
   	}
}
