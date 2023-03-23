@extends('adminlte::page')

@section('title', 'Add Stock Out Request - Sales Order')
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
        <h3>Add Stock Out Request - Sales Order</h3>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{route('sales.list')}}"> Back</a>
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
            <x-adminlte-input-date name="podate" label="PoDate" :config="$config"  label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-input label="SO ID"  label-class="text-lightblue" name="sono" type="text" placeholder="" igroup-size="sm" fgroup-class="col-md-1" disabled>
            </x-adminlte-input>
            <x-adminlte-select label="Support OrderNo" label-class="text-lightblue"  igroup-size="sm" name="sporderno" id="sporderno" fgroup-class="col-md-2" enable-old-support>
                            
            </x-adminlte-select>
        </div>
        <div class="row">
            <x-adminlte-select label="Customer Code" label-class="text-lightblue"  igroup-size="sm" name="cuscode" id="cuscode" fgroup-class="col-md-2" enable-old-support>
                                
            </x-adminlte-select>
            <x-adminlte-select label="Ware house" label-class="text-lightblue"  igroup-size="sm" name="cuscode" id="cuscode" fgroup-class="col-md-2" enable-old-support>
                                
            </x-adminlte-select>
            <x-adminlte-select label="Team" label-class="text-lightblue"  igroup-size="sm" name="cuscode" id="cuscode" fgroup-class="col-md-2" enable-old-support>
                                
            </x-adminlte-select>
            
            <x-adminlte-input-date name="date" label="Date" :config="$config"  label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-button class="btn" id="search" style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button" label="load item" theme="success" icon="fas fa-filter"/>
        </div>
        <div class="row">
            <div class=" table-responsive py-2"> 
                <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">ID</th>
                    <th scope="col">DESC</th>
                    <th scope="col">Type</th>
                    <th scope="col">LotNo.....</th>
                    <th scope="col">Intock..... </th>
                    <th scope="col">Total Qty</th>
                    <th scope="col">Price</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <th scope="row">1</th>
                    <td><input type="text" name="ItemName[]" class="form-control"></td>
                    <td><input type="text" name="Type[]" class="form-control"></td>
                    <td><input type="number" placeholder="0.00" step="any" name="qty[]" class="form-control"></td>
                    <td><input type="number" name="instock[]" class="form-control" disabled></td>
                    <td><input type="number"  placeholder="0.00" step="any" name="totalqty[]" class="form-control" disabled></td>
                    <td><input type="number" placeholder="0.00" step="any" name="price[]" class="form-control"></td>
                    <td><input type="number"  placeholder="0.00" step="any" name="amount[]" class="form-control" disabled></td>
                    <td>delete</td>
                   
                 
                </tbody>
                </table>
                <div id="totalQuantity">Total Quantity here</div>
                
                <div>
                
                    <label for="note" style="margin-right: 30px; margin-top: 100px;"> Note:</label>
                    <input type="text" id="note" name="note" style="width: 400px; height: 80px;">
                    
                </div>
                <x-adminlte-button class="btn-flat" style="float: left; margin-left: 20px;"  id="save" type="submit" label="Save" theme="success" icon="fas fa-lg fa-save"/>
                <x-adminlte-button class="btn-flat" id="export" style="float: left; margin-left: 20px;" type="button" label="Export Excel" theme="success"/>

                <x-adminlte-button class="btn-flat" id="add" style=" margin-right: 20px; float: right;background-color: #e7e7e7; color: black;" type="button" label="Add New"  />
                <x-adminlte-button class="btn-flat" id="copy" style="float: right; margin-right: 20px;background-color: #e7e7e7; color: black;" type="button" label="Copy Order"  />
                <x-adminlte-button class="btn-flat" id="apply" style="float: right;margin-right: 20px;background-color: #e7e7e7; color: black;" type="button" label="Apply SAP" />
                <x-adminlte-button class="btn-flat" id="promotion" style="float: right; margin-right: 20px;" type="button" label="Get Promotion" theme="success"  />
        
                
        </div>
       
        
        
        

    </form>
@stop

@section('css')
<style>
.btn-flat{
    font-size: small;
    padding: 8px 24px;
    margin-top: 70px;
}   

</style>
@stop
@push('js')
@endpush