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
        $employees = DB::table('employees')
                     ->where('id', '=', $request->id)
                     ->where('password', '=', $request->password)
                     ->get();
        $patients  = DB::table('patients')
                     ->where('id', '=', $request->id)
                     ->where('password', '=', $request->password)
                     ->get();

        if (count($employees)){
            Session::put('user',$employees[0]);
            return Redirect::to('/');
        }
        
        if (count($patients)){
            Session::put('user',$patients[0]);
            return Redirect::to('/');
        }

        if (count($employees) == 0)
            return Redirect::to('/login')->withErrors('ID or Password is incorrect');
        
        if (count($patients) == 0)
            return Redirect::to('/login')->withErrors('ID or Password is incorrect');
        


        
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
        
        //Session::flush();
        return view('login');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
            $data  = Session::get('user');
            $check  = property_exists($data, 'designation');
            if ($check == 0)
                return view('pat.profile')->withData($data);

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
