@extends('adminlte::page')

@section('title', 'Connection Settings')
@section('plugins.Sweetalert2', true)
@section('content_header')
    <h1>Connection config</h1>
@stop

@section('content')
<div class="container-fluid"> 
<form action="{{route('connect-setup')}}" method="post">
@csrf
<div class="row"> 
    <div class="col-md-6">
    <x-adminlte-input name="servername" id="servername" label="Server Name" placeholder="Domain/IP" type="text" value="{{ env('SAP_SERVER') }}"
    igroup-size="sm" min=1 enable-old-support>
    <x-slot name="appendSlot">
        <div class="input-group-text bg-dark">
            <i class="fas fa-hashtag"></i>
        </div>
    </x-slot>
</x-adminlte-input>
    </div>
    <div class="col-md-6">
    <x-adminlte-input name="port" id="port" label="Port" placeholder="50000" type="number" value="{{ env('SAP_PORT') }}"
    igroup-size="sm" min=1 enable-old-support>
    <x-slot name="appendSlot">
        <div class="input-group-text bg-dark">
            <i class="fas fa-hashtag"></i>
        </div>
    </x-slot>
</x-adminlte-input>
    </div>
</div>
<div class="row"> 
    <div class="col-md-6">
    <x-adminlte-input name="CompanyDB" id="CompanyDB" label="CompanyDB" placeholder="SBODemoAU" type="text" value="{{ env('SAP_DB') }}"
    igroup-size="sm"  enable-old-support>
    <x-slot name="appendSlot">
        <div class="input-group-text bg-dark">
            <i class="fas fa-hashtag"></i>
        </div>
    </x-slot>
</x-adminlte-input>
    </div>
    <div class="col-md-6">
    <x-adminlte-input name="username" id="username" label="User Name" placeholder="manager" type="text" value="{{ env('user_name') }}"
    igroup-size="sm" enable-old-support>
    <x-slot name="appendSlot">
        <div class="input-group-text bg-dark">
            <i class="fas fa-hashtag"></i>
        </div>
    </x-slot>
</x-adminlte-input>
    </div>
</div>

<div class="row"> 
    <div class="col-md-6">
    <x-adminlte-input name="password" id="password" label="Password" placeholder="*****" type="password"  value="{{ env('password') }}"
    igroup-size="sm">
    <x-slot name="appendSlot">
        <div class="input-group-text bg-dark">
            <i class="fas fa-hashtag"></i>
        </div>
    </x-slot>
</x-adminlte-input>
    </div>
    <div class="col-md-6">
       
   
    </div>
</div>
<x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="save"/>
<x-adminlte-button label="test connection"  theme="info" icon="fas fa-info-circle" id="testconnect" onclick="genBasicAuth()"/>
  </form>
  </div>
@stop  

@section('css')
    
@stop

@push('js')
<script>
    var BasicAuth="";
    
$(document).ready(function() {
//alert
      

        @if(Session::has('success'))
        {
            Swal.fire({
                position: 'top-end',
                type: 'success',
                toast: true,
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            }
            );
        }
        @endif
        
    });
    function genBasicAuth() {
        const fields = [
  { id: 'servername', message: 'Please enter server name' },
  { id: 'port', message: 'Please enter port' },
  { id: 'CompanyDB', message: 'Please enter company name' },
  { id: 'username', message: 'Please enter userName' },
  { id: 'password', message: 'Please enter password' }
];

for (const { id, message } of fields) {
  const element = document.getElementById(id);
  if (!element || !element.value) {
    alert(message);
    return;
  }
}
        var nameServer="https://"+document.getElementById('servername').value+":"+document.getElementById('port').value+"/b1s/v1";
     // Define the username and password
        var username ='{"CompanyDB":"'+document.getElementById('CompanyDB').value+'","UserName":"'+document.getElementById('username').value+'"}';
        var password = document.getElementById('password').value;

        // Create a string with the format "username:password"
        var authString = username + ":" + password;

        // Encode the string in base64
        var authToken = btoa(authString);
      
        // Add the authorization token to the request headers

        $.ajax({
          beforeSend: function (xhr) {
              xhr.setRequestHeader ("Authorization", "Basic "+authToken);
          },
          url:nameServer+"/Login",
          xhrFields: {
              withCredentials: true,
              rejectUnauthorized: false
          },              
		// the URL for the request
	
		// whether this is a POST or GET request
		type: "get",
		// the type of data we expect back
		dataType : "json",
		// the response is passed to the function
		success: function( response ) {
            Swal.fire({
                position: 'top-end',
                type: 'success',
                toast: true,
                title: 'connection success!',
                showConfirmButton: false,
                timer: 3000
            })
           
		},
		error: function( response ) {
           const message= response.responseJSON.error.code+"-"+response.responseJSON.error.message.value;
           Swal.fire({
                position: 'top-end',
                type: 'error',
                toast: true,
                title:"Error connect, code:"+ message,
                showConfirmButton: false,
                timer: 3000
            })
		},
        })
    }
         
    </script>
@endpush