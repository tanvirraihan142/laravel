<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Patient;
use App\VaccRecord;
use App\Campaign;
use Carbon\Carbon;
use App\Center;
use App\Vaccine;
use App\Event;
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
        if ( $request->total_vials != null )
            $value = $request->total_vials;
        $validator = Validator::make($request->all(),[
            'vaccine_name' => 'required',
            'inventory_name'=>'required',
            'total_vials'=> 'required|int', 
            'available_vials'=>"required|int|between:0,$value",
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
        $temp = DB::select('select A.cc_no,C.campaign_name,DATE_FORMAT(C.campaign_date, "%d-%m-%Y") as "campaign_date",B.* 
            from campaigncenters as A,centers as B,campaigns as C
            where B.center_no = A.center_no
            and A.campaign_no = C.campaign_no
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
    public function getAssignHa6(Request $request){
        $data = Session::get('data2');
        $cc = Session::get('cc');
        var_dump($data[0]);
        var_dump($cc);

        $temp2 = DB::table('events')->get();
        if ( $temp2 != null )
            DB::table('events')->
            where('cc_no', '=', $cc)->delete();

        foreach ($data[0] as $value) {
            $new = new Event;
            $new->cc_no = $cc;
            $new->ha_no = $value->emp_no;
            $new->save();
        }
        return Redirect::to('/'); 

    }
    public function updateVaccine(){
        $temp = DB::select('select vaccine_no,vaccine_name,inventory_name,total_vials,
        available_vials,manufacturer,DATE_FORMAT(mfg_date, "%d-%m-%Y") as "mfg_date",DATE_FORMAT(exp_date, "%d-%m-%Y") as "exp_date"
        ,vfc from vaccines');
        
        return view('ho.updateVaccine')->withTemp($temp);
    }
    public function updateVaccine2($vaccineno){
        $temp = DB::select('select vaccine_no,vaccine_name,inventory_name,total_vials,
        available_vials,manufacturer,DATE_FORMAT(mfg_date, "%d/%m/%Y") as "mfg_date",DATE_FORMAT(exp_date, "%d/%m/%Y") as "exp_date"
        ,vfc from vaccines where vaccine_no = :somevariable',array('somevariable'=>$vaccineno));
        Session::put('vaccine',$temp);
        return Redirect::to('/updateVaccine2');
    }
    public function updateVaccine3(){
        $data2 = Session::get('vaccine');
        $data = $data2[0];
        return view('ho.updateVaccine2')->withData($data);
    }
    public function updateVaccine4(Request $request){
        if ( $request->total_vials != null )
            $value = $request->total_vials;
        $validator = Validator::make($request->all(),[
            'vaccine_name' => 'required',
            'inventory_name'=>'required',
            'total_vials'=> 'required|int', 
            'available_vials'=>"required|int|between:0,$value",
            'manufacturer'=>'required',
            'mfg_date' => 'required|date',
            'exp_date'=>'required|date',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::to('/updateVaccine2')->withErrors($validator);
         }
         else{
             $data2 = Session::get('vaccine');
            $vaccineno = $data2[0]->vaccine_no;
            $date1 = Carbon::createFromFormat('d/m/Y', $request->mfg_date);
            $date2 = Carbon::createFromFormat('d/m/Y', $request->exp_date);
           
            $data = Session::get('vaccine');
            $vaccineno = $data[0]->vaccine_no;
            DB::select("update vaccines
                set vaccine_name = '$request->vaccine_name' , inventory_name = '$request->inventory_name',
                total_vials = $request->total_vials , available_vials = $request->available_vials,
                manufacturer = '$request->manufacturer' , mfg_date = '$date1',
                exp_date = '$date2' , vfc = '$request->DropDownList2'
                where vaccine_no = $vaccineno");
            return Redirect::to('/');

        }
    }
}
