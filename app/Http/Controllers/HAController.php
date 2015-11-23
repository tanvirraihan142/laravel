<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Patient;
use App\VaccRecord;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use DB;
use Auth;
use Validator, Input, Redirect;
use Session;


class HAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegPatient()
    {
        //
        return view('ha.regpatient');
    }
    public function getUpdatePatient()
    {
        //
        return view('ha.updatepatient');
    }
    public function getUpdatePatient2()
    {
        //
        $patient = Session::get('patient');
        $history = DB::table('vacc_record')
                     ->where('patient_id', '=', $patient->id)
                     ->get();

        if (count($history)){
            foreach ($history as $value) {
                $temp2 = DB::table('employees')
                     ->where('id', '=', $value->healthasst_id)
                     ->get();
                $temp3 = $temp2[0]->first_name." ".$temp2[0]->last_name;
                $value->healthasst_id = $temp3;;
            }
        }

        $data = array($patient->id,$patient->first_name." ".$patient->last_name,$history);
        return view('ha.updatepatient2')->withData($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegPatient(Request $request)
    {
        //
        $validator = Validator::make($request->all(),[
            'id' => 'required|unique:patients,id',
            'password'=>'required',
            'password_retype' =>'required|same:password',
            'first_name'=>'required',
            'last_name'=>'required',
            'father_name'=>'required',
            'mother_name'=>'required',
            'mobile_no'=>'required',
            'address'=>'required',
            'date_of_birth'=> 'required|date|before:today',       // required and has to match the password field
        ]);
        if ($validator->fails()) {

            // get the error messages from the validator
            $messages = $validator->messages();
            // redirect our user back to the form with the errors from the validator
            return Redirect::to('/registerpatient')->withErrors($validator);
         }
         else{
            $employee = new Patient;
            $employee->id           = $request->id;
            $employee->password     = $request->password;
            $employee->first_name   = $request->first_name;
            $employee->last_name    = $request->last_name;
            $employee->father_name  = $request->father_name;
            $employee->mother_name  = $request->mother_name;
            $employee->gender       = $request->gender;
            $employee->setDobAttribute($request->date_of_birth);
            $employee->address      = $request->address;
            $employee->mobile_no    = $request->mobile_no;

            $employee->save();

            return Redirect::to('/registerpatient');
    }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postUpdatePatient(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::to('/updatepatient')->withErrors($validator);
         }
         $patients  = DB::table('patients')
                     ->where('id', '=', $request->id)
                     ->get();

        if (count($patients) == 0)
            return Redirect::to('/updatepatient')->withErrors('There is no patient matching that ID');
        
        if (count($patients)){
            $updatingPatient = $patients[0];
            Session::put('patient',$updatingPatient);
            return Redirect::to('/updatepatient2');
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postUpdatePatient2(Request $request)
    {
        //
        $validator = Validator::make($request->all(),[
            'vaccine'=>'required',
            'CenterName'=>'required',
            'vaccine_date'=> 'required|date',       // required and has to match the password field
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return Redirect::to('/updatepatient2')->withErrors($validator);
         }
         $record = new VaccRecord;
         $record->patient_id = Session::get('patient')->id;
         $record->healthasst_id = Session::get('user')->id;
         $record->center = $request->CenterName;
         $record->setDate($request->vaccine_date);
         $record->vaccine = $request->vaccine;
         $record->save();

        return Redirect::to('/updatepatient2');


    } 

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
