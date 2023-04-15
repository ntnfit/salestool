@extends('adminlte::page')

@section('title', 'Users')
@section('plugins.Datatables', true)
@section('content_header')
    <h5>create user</h5>
@stop
@section('plugins.Sweetalert2', true);
@section('content')
<div class="container-fluid"> 
<form action="{{route('users.store')}}" method="post">
@csrf
<div class="row">
    <x-adminlte-input label="Full name" label-class="text-lightblue" type="text" name="name" id="" placeholder="Nguyen van A"  fgroup-class="col-md-6" enable-old-support>
    <x-slot name="prependSlot">
        <div class="input-group-text">
            <i class="fas fa-id-card text-lightblue"></i>
        </div>
    </x-slot>
    </x-adminlte-input>
    <x-adminlte-input label="Email"  label-class="text-lightblue" name="email" type="email" placeholder="mail@example.com"  fgroup-class="col-md-6" enable-old-support>
    <x-slot name="prependSlot">
        <div class="input-group-text">
            <i class="fas fa-envelope text-lightblue"></i>
        </div>
    </x-slot>
    </x-adminlte-input>
</div>
<div class="row">
    
    <x-adminlte-select label="UserSAP" name="UserID" fgroup-class="col-md-6" enable-old-support required>
        <option value=""></option>
        @foreach($usersap as $sapid)
             
            <option value="{{$sapid->USERID}}">{{$sapid->USER_CODE}}</option>
            @endforeach
        </x-adminlte-select>
<x-adminlte-input name="password" type="password" label="password" placeholder="password" label-class="text-lightblue"  fgroup-class="col-md-6" >
    <x-slot name="prependSlot">
        <div class="input-group-text">
            <i class="fas fa-lock text-lightblue"></i>
        </div>
    </x-slot>
</x-adminlte-input>
</div>
<div class="row">
<x-adminlte-input name="address" label="Address" fgroup-class="col-md-6" label-class="text-lightblue" placeholder="561A Điện Biên Phủ, Bình Thạnh, Hồ Chí Minh" enable-old-support>
    <x-slot name="prependSlot">
        <div class="input-group-text text-purple">
            <i class="fas fa-address-card"></i>
        </div>
    </x-slot>
    <x-slot name="bottomSlot">
        <span class="text-sm text-gray">
          
        </span>
    </x-slot>
</x-adminlte-input>
<x-adminlte-select label="Role" name="roles" fgroup-class="col-md-6" label-class="text-lightblue" enable-old-support>
    @foreach($roles as $role)
         
        <option value="{{$role}}">{{$role}}</option>
        @endforeach
    </x-adminlte-select>
</div>
<x-adminlte-button class="btn-flat" style="float: right" type="submit" label="save" theme="success" icon="fas fa-lg fa-save"/>

</form>
@stop

@section('css')
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

@stop
@section('js')
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
@stop
@push('js')
<script>
    $(document).ready(function() {
//alert
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        @if(Session::has('success'))
        {
            Swal.fire({
                icon: 'success',
                text: 'Create user success!'
            }
            );
        }
        @endif
  
    
 });
        
  
</script>
@endpush