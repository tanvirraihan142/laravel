<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Patient;
use App\VaccRecord;
use App\Campaign;
use App\Center;
use App\Vaccine;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use DB;
use Auth;
use Validator, Input, Redirect;
use Session;


class HOController extends Controller
{
    
    public function getCenter()
    {
        //
        return view('ho.addcenter');
    }
    public function getVaccine()
    {
        //
        return view('ho.addvaccine');
    }
    public function postCenter(Request $request)
    {
       $validator = Validator::make($request->all(),[
            'center_name' => 'required',
            'location'=>'required',
            'district'=> 'required', 
            'contact_no'=>'required',
        
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::to('/addCenter')->withErrors($validator);
         }
         else{
            $cntr = array($request->center_name,$request->location,$request->district,
                $request->contact_no);
             $new = new Center;
             $new->center_name = $cntr[0];
             $new->location = $cntr[1];
             $new->district = $cntr[2];
             $new->contact_no = $cntr[3];
             $new->save();
             return Redirect::to('/addCenter');
            
         }
    }
    public function postVaccine(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'vaccine_name' => 'required',
            'inventory_name'=>'required',
            'total_vials'=> 'required|int', 
            'available_vials'=>'required|int',
            'manufacturer'=>'required',
            'mfg_date' => 'required|date',
            'exp_date'=>'required|date',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::to('/addVaccine')->withErrors($validator);
         }
         else{
            $vccn = array($request->vaccine_name,$request->inventory_name,$request->total_vials,
                $request->available_vials,$request->manufacturer,$request->mfg_date,$request->exp_date,
                $request->DropDownList2);
            
            $new = new Vaccine;
            $new->vaccine_name = $vccn[0];
            $new->inventory_name = $vccn[1];
            $new->total_vials = $vccn[2];
            $new->available_vials = $vccn[3];
            $new->manufacturer = $vccn[4];
            $new->setExpDate($vccn[5]);
            $new->setMfgDate($vccn[6]);
            $new->vfc = $request->DropDownList2;
            $new->save();
            return Redirect::to('/addVaccine');
         }
        
    }
    public function getAssignHa(){
        $empno = Session::get('user')->emp_no;
        $temp = DB::select('select A.cc_no,B.* 
            from campaigncenters as A,centers as B
            where B.center_no = A.center_no
            and ho_id = :somevariable',array('somevariable'=>$empno));

        return view('ho.assignHA')->withTemp($temp);

    }
    public function getAssignHa2($cc){
        Session::put('cc',$cc);
        $temp = DB::select('select emp_no,CONCAT(first_name," ",last_name) as "name" from employees
            where designation = "Health Assistant" and CONCAT(first_name," ",last_name) in (select (select CONCAT(first_name," ",last_name) 
            from employees where emp_no = A.ha_no)as "name" from events as A where cc_no in (
        select cc_no from campaigncenters where campaign_no = (select campaign_no
        from campaigncenters where cc_no = :var1)))',array('var1'=>$cc));
        
        $temp2 = DB::select('select emp_no,CONCAT(first_name," ",last_name) as "name" from employees
            where designation = "Health Assistant" and CONCAT(first_name," ",last_name) not in (select (select CONCAT(first_name," ",last_name) 
            from employees where emp_no = A.ha_no)as "name" from events as A where cc_no in (
        select cc_no from campaigncenters where campaign_no = (select campaign_no
        from campaigncenters where cc_no = :var1)))',array('var1'=>$cc));

        $data = array($temp,$temp2);
        Session::put('data2',$data);
        return Redirect::to('/assignHA2');
    }
    public function getAssignHa3(){
        $data = Session::get('data2');
        return view('ho.assignHA2')->withData($data);
    }
    public function getAssignHa4($empno){
        $data = Session::get('data2');
        $item = DB::select('select emp_no,CONCAT(first_name," ",last_name) as "name" from employees
            where designation = "Health Assistant" and emp_no = :somevariable',array('somevariable' => $empno));
        
        array_push($data[0],$item[0]);
        $newarray = array();

        foreach ($data[1] as $value) {
            if ($value->emp_no != $item[0]->emp_no)
               array_push($newarray,$value);
        }

        $data2 = array($data[0],$newarray);
        Session::put('data2',$data2);
        return Redirect::to('/assignHA2'); 
    }

    public function getAssignHa5($empno){
        $data = Session::get('data2');
        $item = DB::select('select emp_no,CONCAT(first_name," ",last_name) as "name" from employees
            where designation = "Health Assistant" and emp_no = :somevariable',array('somevariable' => $empno));
        
        array_push($data[1],$item[0]);
        $newarray = array();

        foreach ($data[0] as $value) {
            if ($value->emp_no != $item[0]->emp_no)
               array_push($newarray,$value);
        }
        
        $data2 = array($newarray,$data[1]);
        Session::put('data2',$data2);
        return Redirect::to('/assignHA2'); 
    }

    
}
