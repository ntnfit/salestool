@extends('adminlte::page')
@section('title', 'Lock Vehicle')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.BsCustomFileInput', true)
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@section('content')
<h3> Lock/ Unlock Vehicle </h3>
<div class="content">
    <div id="MyGrid" class="ag-theme-alpine" style="height: 80%">
    </div>
     
    <x-adminlte-button class="btn-flat" id="lockall" style="float: left;font-size: small; margin-top:30px;padding: 8px 24px;" type="button" label="Lock All" theme="success"/>
    <x-adminlte-button class="btn-flat" id="unall" style="float: left;font-size: small;margin-top:30px; margin-left: 20px;padding: 8px 24px;" type="button" label="Unlock All" theme="success"/>
    <a href="{{route('logistic.truckinfor')}}"><x-adminlte-button class="btn-flat" id="back" style="float: right;font-size: small;margin-top:30px;padding: 8px 24px;" type="button" label="Back" theme="success"/></a>
    <x-adminlte-button class="btn-flat" id="export" style="float: right;font-size: small;margin-top:30px; margin-right: 20px;padding: 8px 24px;" type="button" label="Export Excel" theme="success"/>
</div>
@php

@endphp




@endsection


@section('css')
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
    { field: 'Truck Code'},
    { field: 'Truck Driver'},
    { field: 'Truck Type'},
    { field: 'Capacity'},
    { field: 'Phone'},
    { field: 'Status'},
    { field: 'Lock',
        },
    { field: 'Unlock'},

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

