@extends('layout.pat')

@section('content')

  <div class="col-lg-12 text-center">
              	<div class="col-lg-12 text-center">
                
                   
                      <div class="row">
                          <div class="col-sm-6 col-md-4.5 col-md-offset-3">
                              <h2 class = "brand-name">
                                <medium> Profile </medium>
                              </h2>
                    <div class="table-responsive">
                    <table class="table">
                        
    <tbody>
      <tr>
        <td><b>First Name</b></td><td>{{ $data->first_name }}</td>
       </tr>
      <tr>
        <td><b>Last Name</b></td><td>{{ $data->last_name }}</td>
      </tr>
      <tr>
        <td><b>Fathers Name</b></td><td>{{ $data->father_name}}</td>
      </tr>
      <tr>
        <td><b>Mothers Name</b></td><td>{{ $data->mother_name}}</td>
      </tr>
      <tr>
        <td><b>Gender</b></td><td>{{ $data->gender }}</td>
      </tr>
      <tr>
        <td><b>Mobile No.</b></td><td>{{ $data->mobile_no }}</td>
      </tr>
       <tr>
        <td><b>Date of Birth</b></td><td>{{ $data->date_of_birth }}</td>
      </tr>
      <tr>
        <td><b>Age</b></td><td>{{ $data->age }}</td>
      </tr>
      <tr>
        <td><b>Address</b></td><td>{{ $data->address }}</td>
      </tr>
    </tbody>
                    </table>
                     
                          </div>
                </div>
                </div></div></div>
            


@endsection