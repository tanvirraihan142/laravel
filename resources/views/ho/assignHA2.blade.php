@extends('layout.ho')

@section('content')
                <div class="col-lg-12 text-center">
                
                   <div class="container">
                      <div class="row">
                          <div class="col-sm-6 col-md-4 col-md-offset-4">
                              <h2>
      Select Health Asst.
    </h2>
 <h2 class = "brand-after">
                                <small> Select Health Assistants from below </small>
                              </h2>
                              <hr class="tagline-divider">
  <table class="table">
    <tr style="color:White;background-color:#507CD1;font-weight:bold;">
      <th scope="col">&nbsp;</th><th scope="col">NAME OF HEALTH ASSISTANS</th>

    @if ($data[1]!=null)
  @foreach ($data[1] as $line)
       <tr >
           <td><a href="assignHA2/add/{{ $line->emp_no }}" style="color:blue">Select</a></td><td>{{ $line->name}}</td>
       </tr>              
  @endforeach
  @endif
  </table>
<hr class="tagline-divider">
 <h2 class = "brand-after">
                                <small> Selected Centers </small>
                              </h2>
  <table class="table">
    <tr style="color:White;background-color:#507CD1;font-weight:bold;">
      <th scope="col">&nbsp;</th><th scope="col">NAME OF HEALTH ASSISTANS</th>

   @if ($data[0]!=null)
  @foreach ($data[0] as $line)
       <tr >
           <td><a href="assignHA2/del/{{ $line->emp_no }}" style="color:blue">Deselect</a></td><td>{{ $line->name}}</td>
       </tr>              
  @endforeach
  @endif

  </table>
  <div class="col-sm-offset-1 col-sm-10">
         <button type="submit" class="btn btn-default">Confirm</button>
      </div>
                          </div>
                      </div>
                    </div>
                </div>
            @endsection