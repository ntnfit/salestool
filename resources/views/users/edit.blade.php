@extends('adminlte::page')

@section('title', 'Edit user')
@section('plugins.Sweetalert2', true)
@section('plugins.BootstrapSelect', true)
@section('content')

<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Edit New User</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>
        </div>
    </div>
</div>


@if (count($errors) > 0)
  <div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
       @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
       @endforeach
    </ul>
  </div>
@endif


{!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id]]) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <strong>Name:</strong>
            {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <strong>Email:</strong>
            {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
        </div>
    </div>
    <x-adminlte-select label="UserSAP" name="UserID"  fgroup-class="col-md-6" enable-old-support required>
        <option value=""></option>
        @foreach($usersap as $sapid)  
            @if($sapid->USERID==$user->UserID)
            <option value="{{$sapid->USERID}}" selected>{{$sapid->USER_CODE}}</option>
            @else   
            <option value="{{$sapid->USERID}}">{{$sapid->USER_CODE}}</option>
            @endif
        @endforeach
        </x-adminlte-select>
        <x-adminlte-input name="address" label="Address" fgroup-class="col-md-6" label-class="text-lightblue" 
        placeholder="561A Điện Biên Phủ, Bình Thạnh, Hồ Chí Minh" value="{{$user->address}}" enable-old-support>
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
    <div class="col-md-6">
        <div class="form-group">
            <strong>Password:</strong>
            {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <strong>Confirm Password:</strong>
            {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        @php
    $config = [
        "title" => "Select roles options...",
        "liveSearch" => true,
        "liveSearchPlaceholder" => "Search...",
        "showTick" => true,
        "actionsBox" => true,
    ];
@endphp
<x-adminlte-select-bs label="roles" name="roles" fgroup-class="col-md-6" :config="$config" enable-old-support>
<x-slot name="prependSlot">
        <div class="input-group-text bg-gradient-red">
            <i class="fas fa-cannabis"></i>
        </div>
    </x-slot>
    @foreach ($roles as $item)
     <option value="{{ $item}}"{{ in_array($item,$userRole) ? 'selected' : '' }}>{{ $item}}</option>
    @endforeach
</x-adminlte-select-bs>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
{!! Form::close() !!}

@endsection

@section('css')
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

@stop
@section('js')
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
@stop