@extends('adminlte::page')

@section('title', 'Add sales stock ')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@section('content')
@php
$config = ['format' => 'L'];
@endphp
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>add stock request -order</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href=""> Back</a>
        </div>
    </div>
</div>
<form action="">
@csrf
        <!-- header input  -->
        <div class="row">
                <x-adminlte-select label="Order type" label-class="text-lightblue"  igroup-size="sm" name="ordertype" id="ordertype" fgroup-class="col-md-3" enable-old-support>
                        
                </x-adminlte-select>
                <x-adminlte-input label="PO ID"  label-class="text-lightblue" name="pono" type="text" placeholder="" igroup-size="sm" fgroup-class="col-md-3" >
                    </x-adminlte-input>
            <x-adminlte-input-date name="idDateOnly" label="PoDate" :config="$config"  label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-input label="SO ID"  label-class="text-lightblue" name="sono" type="text" placeholder="" igroup-size="sm" fgroup-class="col-md-3" disabled>
            </x-adminlte-input>
        </div>
</form>
@stop

@section('css')

@stop
@push('js')
@endpush