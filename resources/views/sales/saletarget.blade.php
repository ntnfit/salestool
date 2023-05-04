@extends('adminlte::page')

@section('title', 'Sales Target Management')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@section('content')
@php
$config = ['format' => 'L'];
@endphp
<h3> Sales Target Add New/ Update</h3>
<div class="content">
  <form>
    <div class="row">
      <!-- header input -->
      <x-adminlte-select label="Sales Manager" label-class="text-lightblue"  igroup-size="sm" name="sales" id="sales" fgroup-class="col-md-2" enable-old-support></x-adminlte-select>
      <x-adminlte-select label="KA/ ASM" label-class="text-lightblue"  igroup-size="sm" name="ka" id="ka" fgroup-class="col-md-2" enable-old-support></x-adminlte-select>
      <x-adminlte-select label="Sales Sup." label-class="text-lightblue"  igroup-size="sm" name="sup" id="sup" fgroup-class="col-md-2" enable-old-support></x-adminlte-select>
      <x-adminlte-select label="T. Leader" label-class="text-lightblue"  igroup-size="sm" name="leader" id="leader" fgroup-class="col-md-2" enable-old-support></x-adminlte-select>
      <x-adminlte-button class="btn" id="search" style="float: right;margin-top: 30px;font-size: small;height: 31px;" type="button" label="Search" theme="success" />
      
    </div>
    
    <!-- form gird -->
    <div id="MyGrid" class="ag-theme-alpine" style="height: 70%"></div>

   
    
  </div>
  
  </form>
  
  <div>
  <x-adminlte-button class="btn-flat" style="float: left; margin-right: 20px;"  id="save" type="submit" label="Save" theme="success" icon="fas fa-lg fa-save"/> 
    <x-adminlte-button class="btn-flat" id="copy" style=" margin-right: 20px; float: right;" type="button" label="Copy Target from Sales Manager" theme="success" />
    <x-adminlte-button class="btn-flat" id="approve" style="float: right; margin-right: 20px;" type="button" label="Approve" theme="success" />
    <x-adminlte-button class="btn-flat" id="export" style="float: right;margin-right: 20px;" type="button" label="Export Excel" theme="success" />
    
    
  </div>   
      
</div>   
@endsection
@section('css')
<style>
#search {
  float: right;
  margin-left: 20px;
}
.btn-flat{
    font-size: small;
    padding: 8px 24px;
    margin-top: 30px;
    margin-bottom: 30px;

}   



</style>
@stop
@push('js')
<script>

var __basePath = './';</script>
		<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@28.2.1/dist/ag-grid-community.min.js"> 
		</script>
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@28.2.1/dist/ag-grid-enterprise.min.js">
      </script>
      <script>
      var filterParams = {
  comparator: (filterLocalDateAtMidnight, cellValue) => {
    var dateAsString = cellValue;
    if (dateAsString == null) return -1;
    var dateParts = dateAsString.split('/');
    var cellDate = new Date(
      Number(dateParts[2]),
      Number(dateParts[1]) - 1,
      Number(dateParts[0])
    );

    if (filterLocalDateAtMidnight.getTime() === cellDate.getTime()) {
      return 0;
    }

    if (cellDate < filterLocalDateAtMidnight) {
      return -1;
    }

    if (cellDate > filterLocalDateAtMidnight) {
      return 1;
    }
  },
  browserDatePicker: true,
};

const columnDefs = [
  //
  {
    headerName: 'Customer Information',
    children: [
      { field: 'Code'},
      { field: 'Name'},
      { field: 'Group' },
      { field: 'Sale Manager' },
      { field: 'KA/ASM'},
      { field: 'Sale Sup.'},
      { field: 'Team Leader' },
      { field: 'Order Number'},
      { field: 'Sale Rep EmpID'},
      { field: 'PG/ Sales Rep.'},
    ]
  },

  {
    headerName: 'Target',
    children: [
      { field: 'Sale Sup.'},
      { field: 'KA/ASM'},
      { field: 'Sale Manager' },
      { field: 'General Manager' },
    ]
  },
   
  {
    headerName: 'Target SKU',
    children: [
      { field: 'KA/ASM'},
      { field: 'Sale Manager' },
      { field: 'General Manager' },
    ]
  },

  {
    headerName: 'Actual',
    children: [
      { field: 'Current Month' },
      { field: 'Prev. Month' },
    ]
  },


];



const gridOptions = {
  columnDefs: columnDefs,
  pagination: true,
  defaultColDef: {
    flex: 1,
    minWidth: 150,
    filter: true,
    resizable: true,
  },
};
function onBtExport() {
  gridOptions.api.exportDataAsExcel();
}
// setup the grid after the page has finished loading
document.addEventListener('DOMContentLoaded', function () {
  var gridDiv = document.querySelector('#myGrid');
  new agGrid.Grid(gridDiv, gridOptions);
  gridOptions.api.setRowData("")
    });

</script>

@endpush
