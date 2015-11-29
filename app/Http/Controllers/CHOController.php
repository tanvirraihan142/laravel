<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Patient;
use App\VaccRecord;
use App\CampaignCenter;
use App\Campaign;
use App\Notification;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use DB;
use Auth;
use Validator, Input, Redirect;
use Session;


class CHOController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreateCampaign()
    {
        return view('cho.createCampaign');
    }
    
    public function getSetCenter()
    {
        $temp = DB::table('campaigns')
                     ->where('cho_id', '=', Session::get('user')->id)
                     ->get();
        return view('cho.setCenter')->with('temp',$temp);
    }
    public function getAssignHO(){
        $temp = DB::table('campaigns')
                     ->where('cho_id', '=', Session::get('user')->id)
                     ->get();
        return view('cho.assignHO')->with('temp',$temp);
    }
    public function getAssignHO2($campaign_no){
        $temp2 = DB::select('select B.*,
            (select CONCAT(first_name," ",last_name) 
            from employees where emp_no = A.ho_id) "health_officer" 
            from campaigncenters as A,centers as B
            where B.center_no = A.center_no and
            A.campaign_no = :somevariable',array('somevariable' => $campaign_no));
        $data = $temp2;
        
        Session::put('centers3',$temp2);
        Session::put('cmpgn_id',$campaign_no);

        return Redirect::to('/assignHO2'); 
        
    }
    public function getAssignHO4($center_no){
        $cmpgn = Session::get('cmpgn_id');
        $data = DB::select('select emp_no,CONCAT(first_name," ",last_name) 
            "name" from employees where designation = "Health Officer"
            and emp_no not in (select CAST(ho_id AS UNSIGNED) "emp_no" 
                from campaigncenters where campaign_no = :somevariable)',array('somevariable'=>$cmpgn));
        Session::put('cntr',$center_no);
        Session::put('hos',$data);
        return Redirect::to('/assignHO3'); 
    }
    public function getAssignHO5(){
        $temp = Session::get('hos');
        return view('cho.assignHO3')->withTemp($temp);
    }
    public function getAssignHO6($emp_id){
        $center_no = Session::get('cntr');
        $campaign_no = Session::get('cmpgn_id');
        DB::update('update campaigncenters set ho_id = :var3 WHERE center_no = :var1
            and campaign_no = :var2',array('var1' => $center_no, 'var2' => $campaign_no , 'var3'=>$emp_id ));

        $temp2 = DB::select('select B.*,
            (select CONCAT(first_name," ",last_name) 
            from employees where emp_no = A.ho_id) "health_officer" 
            from campaigncenters as A,centers as B
            where B.center_no = A.center_no and
            A.campaign_no = :somevariable',array('somevariable' => $campaign_no));
        Session::put('centers3',$temp2);
        return Redirect::to('/assignHO2'); 
    }

    public function getAssignHO3(){
        $temp1 = Session::get('centers3');
        $data = $temp1;
        return view('cho.assignHO2')->withData($data);

    }
    public function postAssignHO3(Request $request){
        $center_no = Session::get('cntr');
        $campaign_no = Session::get('cmpgn_id');
        $emp_id = '';
        DB::update('update campaigncenters set ho_id = :var3 WHERE center_no = :var1
            and campaign_no = :var2',array('var1' => $center_no, 'var2' => $campaign_no , 'var3'=>$emp_id ));

        $temp2 = DB::select('select B.*,
            (select CONCAT(first_name," ",last_name) 
            from employees where emp_no = A.ho_id) "health_officer" 
            from campaigncenters as A,centers as B
            where B.center_no = A.center_no and
            A.campaign_no = :somevariable',array('somevariable' => $campaign_no));
        Session::put('centers3',$temp2);
        return Redirect::to('/assignHO2');
    }
    public function getSetCenter2($campaign_no)
    {
        $temp = DB::select('select * from centers where not exists(select * from 
            campaigncenters where center_no = centers.center_no 
            and campaign_no = :somevariable)',array('somevariable' => $campaign_no));

        $temp2 = DB::select('select * from centers where exists(select * from 
            campaigncenters where center_no = centers.center_no 
            and campaign_no = :somevariable)',array('somevariable' => $campaign_no));

        Session::put('cmpgn_id',$campaign_no);
        Session::put('centers',$temp);
        Session::put('centers2',$temp2);
        return Redirect::to('/setCenter2');        
    }
    public function getSetCenter21()
    {
        $data=array(Session::get('centers'),Session::get('centers2'));
        return view('cho.setCenter2')->withData($data);        
    }
    public function getSetCenter22($center_no){
        $temp1 = Session::get('centers');
        $temp2 = Session::get('centers2');
        $item = DB::select('select * from centers where center_no = :somevariable',array('somevariable' => $center_no));
        array_push($temp2,$item[0]);
        $item2 = array();

        foreach ($temp1 as $value) {
            if ($value->center_no != $center_no)
               array_push($item2,$value);
        } 
        Session::put('centers',$item2);
        Session::put('centers2',$temp2);
        return Redirect::to('/setCenter2'); 
    }
    public function getSetCenter23($center_no){
        $temp1 = Session::get('centers');
        $temp2 = Session::get('centers2');
        $item = DB::select('select * from centers where center_no = :somevariable',array('somevariable' => $center_no));
        array_push($temp1,$item[0]);
        $item2 = array();

        foreach ($temp2 as $value) {
            if ($value->center_no != $center_no)
               array_push($item2,$value);
        } 
        Session::put('centers',$temp1);
        Session::put('centers2',$item2);
        return Redirect::to('/setCenter2'); 
    }
    public function postSetCenter2(Request $request){
        $temp = Session::get('centers2');
        $temp2 = DB::table('campaigncenters')->get();
        
        if ( $temp2 != null )
            DB::table('campaigncenters')->
            where('campaign_no', '=', Session::get('cmpgn_id'))->delete();



        foreach ($temp as $value) {
            $new = new CampaignCenter;
            $new->campaign_no = Session::get('cmpgn_id');
            $new->center_no = $value->center_no;
            $new->save();
        }
        return Redirect::to('/'); 
    }
    public function postCreateCampaign(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'campaign_name' => 'required',
            'vaccine_name'=>'required',
            'campaign_date'=> 'required|date|unique:campaigns,campaign_date', 
            'start_age'=>'required|int',
            'end_age'=>'required|int',
        
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::to('/createCampaign')->withErrors($validator);
         }
         else{
            $cmpgn = array($request->campaign_name,$request->vaccine_name,$request->campaign_date,
                $request->start_age,$request->end_age);
            $new = new Campaign;
            $new->campaign_name = $cmpgn[0];
            $new->cho_id = Session::get('user')->id;
            $new->vaccine_name = $cmpgn[1];
            $new->setDate($cmpgn[2]);
            $new->start_age = (int)$cmpgn[3];
            $new->end_age = (int)$cmpgn[4];
            $new->save();
            return Redirect::to('/createCampaign');
            
         }
    }
    public function getNotifications(){
        $temp = DB::table('campaigns')
                     ->where('cho_id', '=', Session::get('user')->id)
                     ->get();
        return view('cho.sendNotifications')->with('temp',$temp);
    }
    public function getNotifications2($campaign_no){
        $temp = DB::table('campaigns')
                     ->where('campaign_no', '=', $campaign_no)
                     ->get();

        $temp2 = DB::select('select A.* from centers as A,campaigncenters as B
            where B.campaign_no = :somevariable 
            and A.center_no = B.center_no',array('somevariable'=> $campaign_no));
        $data = array($temp,$temp2);
        Session::put('data',$data);
        return Redirect::to('/notify2');
    }
    public function getNotifications3(){
        $data = Session::get('data');
        return view('cho.sendNotifications2')->withData($data);
    }

    public function getNotifications4(Request $request){
       $data = Session::get('data');
       $campaign = $data[0][0]->campaign_no;
       $validator = Validator::make($request->all(),[
            'noti' => 'required',
        ]);
       if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::to('/createCampaign')->withErrors($validator);
         }
        else{
            $new = new Notification;
            $new->campaign_no = $campaign;
            $new->msg = $request->noti;
            $new->save();
        }
        return Redirect::to('/');
    }
}
