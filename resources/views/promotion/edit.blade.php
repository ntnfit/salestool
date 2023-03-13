@extends('adminlte::page')

@section('title', 'Add promotions')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@section('content')
<div class="content">
<form action="{{route('prosubmit')}}" method="post">
    @csrf
    <div class="tableFixHead">
    <!-- các trường nhập liệu của tab customer -->
        <table class="table table-bordered" id="tablecustomer">
        <thead>
        <tr>
        <th class="header-label">CustomerCode</th>
        <th class="header-label">Customer Name</th>
        <th class="header-label">Group Code</th>
        <th class="header-label">Group Name</th>
        <th class="header-label">Channel</th>
        <th class="header-label">Route</th>
        <th class="header-label">Location</th>
        <th class="header-label">Action</th>
        </tr>
        </thead>
        <tr>
        <td><input type="text" name="cus[]"></td>
        <td><input type="text" name="cusname[]"></td>
        <td><input type="text" name="group[]"></td>
        <td><input type="text" name="grpname[]"></td>
        <td><input type="text" name="channel[]"></td>
        <td><input type="text" name="route[]"></td>
        <td><input type="text" name="location[]"></td>
        <td><button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="fa fa-trash" aria-hidden="true"></i> </button></td>
        </tr>
    </table>
    </div> 
  </div>
  <button  value="submit" type="submit"> </button>
</form>
    <script>
       var tablecus = document.getElementById("tablecustomer");

// add event listener for input changes in last row
tablecus.addEventListener('input', function(event) {
  var lastRow = tablecus.rows[tablecus.rows.length - 1];
  // check if last row input has changed
  if (event.target.parentNode.parentNode == lastRow) {
    // check if last row input is not empty
    if (event.target.value.trim() != "") {
      // add new row to table
      var newRow = tablecus.insertRow(tablecus.rows.length);
      newRow.innerHTML = '<td><input type="text" name="cus[]"></td>'+
    '<td><input type="text" name="cusname[]"></td>'+
    '<td><input type="text" name="group[]"></td>'+
    '<td><input type="text" name="grpname[]"></td>'+
    '<td><input type="text" name="channel[]"></td>'+
    '<td><input type="text" name="route[]"></td>'+
    '<td><input type="text" name="location[]"></td>'+
    '<td><button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="fa fa-trash" aria-hidden="true"></i> </button></td>';
    }
  }
});


    </script>
@stop