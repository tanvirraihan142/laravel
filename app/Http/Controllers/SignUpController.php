<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use DB;
use Auth;
use Validator, Input, Redirect;
use Session;
use Carbon\Carbon;


class SignUpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function log(Request $request)
    {
        //
        $this->validate($request,[
            'id'=>'required',
            'password'=>'required',
            ]);
        $patients  = DB::table('patients')
                     ->where('id', '=', $request->id)
                     ->where('password', '=', $request->password)
                     ->get();
        
        if (count($patients)){
            Session::put('user',$patients[0]);
            return Redirect::to('/');
        }
        if (count($patients) == 0)
            return Redirect::to('/login')->withErrors('ID or Password is incorrect');
        


        
    }
    public function log2(Request $request)
    {
        //
        $this->validate($request,[
            'id'=>'required',
            'password'=>'required',
            ]);
        $employees = DB::table('employees')
                     ->where('id', '=', $request->id)
                     ->where('password', '=', $request->password)
                     ->get();
        
        if (count($employees)){
            Session::put('user',$employees[0]);
            return Redirect::to('/');
        }

        if (count($employees) == 0)
            return Redirect::to('/login2')->withErrors('ID or Password is incorrect');
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function main()
    {
        //
        if (Session::has('user')) {
            $name = Session::get('user')->first_name." ".Session::get('user')->last_name;
            $data  = Session::get('user');
            $check  = property_exists($data, 'designation');
            if ($check == 0)
                return view('pat.index')->withName($name);
            

            $value = Session::get('user')->designation;
            if ($value == 'Chief Health Officer')
                return view('cho.index')->withName($name);
            elseif ($value == 'Health Officer')
                return view('ho.index')->withName($name);
            elseif ($value == 'Health Assistant')
                return view('ha.index')->withName($name);
            else
                return view('pat.index')->withName($name);

        }
        else 
        return view('index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        //
        $validator = Validator::make($request->all(),[
        'id' => 'required|unique:employees,id',
        'password'=>'required',
        'password_retype' =>'required|same:password',
        'firstname'=>'required',
        'lastname'=>'required',
        'mobile_no'=>'required',
        'address'=>'required',
        'email'=> 'required|email',       // required and has to match the password field
    ]);
    if ($validator->fails()) {

        // get the error messages from the validator
        $messages = $validator->messages();

        // redirect our user back to the form with the errors from the validator
        return Redirect::to('/signup')
            ->withErrors($validator);

    }
    else{
        $employee = new Employee;
        $employee->id           = $request->id;
        $employee->email        = $request->email;
        $employee->password     = $request->password;
        $employee->first_name   = $request->firstname;
        $employee->last_name    = $request->lastname;
        $employee->gender       = $request->DropDownList1;
        $employee->designation  = $request->DropDownList2;
        $employee->email        = $request->email;
        $employee->address      = $request->address;
        $employee->mobile_no    = $request->mobile_no;

        // save our duck
        $employee->save();

        // redirect ----------------------------------------
        // redirect our user back to the form so they can do it all over again
        return Redirect::to('/signup');
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return view('login');
    }
    public function getLogin2()
    {
        return view('login2');
    }
    
    public function getLogout()
    {
        //
        Session::flush();
        return Redirect::to('/');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        //
          if (Session::has('user')) {
            $data = Session::get('user');
            $check  = property_exists($data, 'designation');
            
            if ($check == 0){
                $patient = $data;
                $history = DB::table('vacc_record')
                     ->where('patient_id', '=', $patient->id)
                     ->get();

                if (count($history)){
                    foreach ($history as $value) {
                        $temp2 = DB::table('employees')
                        ->where('id', '=', $value->healthasst_id)
                        ->get();
                        $temp3 = $temp2[0]->first_name." ".$temp2[0]->last_name;
                        $value->healthasst_id = $temp3;
                    }
                }
                $data = array($patient,$history);
                return view('pat.profile')->withData($data);
            }
            else{
                $value = Session::get('user')->designation;
                if ($value == 'Chief Health Officer')
                    return view('cho.profile')->withData($data);
                elseif ($value == 'Health Officer')
                    return view('ho.profile')->withData($data);
                elseif ($value == 'Health Assistant')
                    return view('ha.profile')->withData($data);
                else
                    return view('pat.profile')->withData($data);
            }
        }
    }
    public function editProfile()
    {
          if (Session::has('user')) {
            $data  = Session::get('user');
            $check  = property_exists($data, 'designation');
            if ($check == 0){
                $id = Session::get('user')->id;
                $temp = DB::select('SELECT patient_no, id, password, first_name, last_name, father_name, mother_name, gender,DATE_FORMAT(date_of_birth, "%d/%m/%Y") 
                    as "date_of_birth", age, mobile_no, address FROM patients where id = :somevariable',array('somevariable' => $id));
                //var_dump(array($id,$data));
                return view('pat.editProfile')->withTemp($temp[0]);
            }
            elseif ($check == 1){
            $value = Session::get('user')->designation;
            if ($value == 'Chief Health Officer')
                return view('cho.editProfile')->withData($data);
            elseif ($value == 'Health Officer')
                return view('ho.editProfile')->withData($data);
            elseif ($value == 'Health Assistant')
                return view('ha.editProfile')->withData($data);
            }

        }
    }
    public function changePassword()
    {
          if (Session::has('user')) {
            $data  = Session::get('user');
            $check  = property_exists($data, 'designation');
            if ($check == 0)
                return view('pat.passwordChange')->withData($data);

            $value = Session::get('user')->designation;
            if ($value == 'Chief Health Officer')
                return view('cho.passwordChange')->withData($data);
            elseif ($value == 'Health Officer')
                return view('ho.passwordChange')->withData($data);
            elseif ($value == 'Health Assistant')
                return view('ha.passwordChange')->withData($data);
            else
                return view('pat.passwordChange')->withData($data);

        }
    }

    public function editProfile2(Request $request)
    {
        if (Session::has('user')) {
            $data  = Session::get('user');
            $check  = property_exists($data, 'designation');
            if ($check == 0){
                $input = array($request->first_name,
                               $request->last_name,
                               $request->father_name,
                               $request->mother_name,
                               Carbon::createFromFormat('d/m/Y', $request->date_of_birth),
                    $request->DropDownList1,$request->mobile_no,$request->address);
                DB::table('patients')->where('id', $data->id)
                ->update(['first_name' => $input[0],
                    'last_name' => $input[1],
                    'father_name'=> $input[2],
                    'mother_name'=>$input[3],
                    'date_of_birth'=> $input[4],
                    'gender' =>$input[5],
                    'mobile_no' => $input[6],
                    'address'=>$input[7]  ]);
                $employees = DB::table('patients')
                     ->where('id', '=', $data->id)
                     ->get();
                Session::put('user',$employees[0]);
                return redirect('profile');
                
            }
            elseif ($check == 1){
                $input = array($request->first_name,$request->last_name,$request->mobile_no,$request->email,$request->address);
                DB::table('employees')->where('id', $data->id)
                ->update(['first_name' => $input[0],
                          'last_name' => $input[1],
                          'mobile_no'=> $input[2],
                          'email'=>$input[3],
                          'address'=> $input[4] ]);
                 $employees = DB::table('employees')
                     ->where('id', '=', $data->id)
                     ->get();
                Session::put('user',$employees[0]);
                return redirect('profile');
            }

        }
    }
    public function changePassword2(Request $request)
    {
        if (Session::has('user')) {
            $data  = Session::get('user');
            $check  = property_exists($data, 'designation');
            

            if ($request->password1 != $data->password)
                    return redirect('changePassword')->withErrors('Old Password does not match');
            $validator = Validator::make($request->all(),[
                    'password'=>'required',
                    'password_retype' =>'required|same:password',]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return Redirect::to('/changePassword')
                        ->withErrors($validator);
            }   
            else{
                if ($check == 0){   
                DB::table('patients')->where('id', $data->id)
                    ->update(['password' => $request->password ]);
                 $employees = DB::table('patients')
                     ->where('id', '=', $data->id)
                     ->get();
                Session::put('user',$employees[0]);
                return redirect('profile');
                }
                elseif ($check == 1){
                    DB::table('employees')->where('id', $data->id)
                    ->update(['password' => $request->password ]);
                     $employees = DB::table('employees')
                     ->where('id', '=', $data->id)
                     ->get();
                Session::put('user',$employees[0]);
                return redirect('profile');
                }
            }
        }
    }
    
}
