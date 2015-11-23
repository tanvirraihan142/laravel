<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    //
    public $timestamps = false;
    protected $table = 'patients';
    protected $fillable = array('id','password','first_name','last_name','father_name','mother_name','gender','date_of_birth','mobile_no','address');
    protected $hidden = ['password'];

    public function setDobAttribute($value){
       $this->attributes['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $value);
   	}
}
