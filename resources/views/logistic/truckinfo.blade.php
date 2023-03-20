@extends('adminlte::page')

@section('title', 'Truck information')
@section('plugins.Datatables', true)
@section('plugins.DateRangePicker', true)
@section('plugins.BsCustomFileInput', true)
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@section('content')
@php
$config = ['format' => 'L'];
@endphp
<h3> Truck Information</h3>

<div class="content">
  <form>
    <div class="row">
      <!-- header input -->
      <x-adminlte-input-date name="truckdate" label="From Date" :config="$config"  label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-gradient-danger">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
      </x-adminlte-input-date>
      <x-adminlte-input-date name="truckdate" label="To Date" :config="$config"  label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-gradient-danger">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
      </x-adminlte-input-date>

      <x-adminlte-button class="btn-flat" id="search" style="float: right;margin-top: 30px;font-size: small;height: 31px;" type="button" label="Search" theme="success" />
      <x-adminlte-select label="Truck Code" label-class="text-lightblue" name="truckcode" id="truckcode" fgroup-class="col-md-3" style="float:right" enable-old-support>  
      </x-adminlte-select>
      <x-adminlte-button class="btn-flat" id="apply" style="float: right; margin-top: 30px;font-size: small;height: 31px;" type="button" label="Apply" theme="success" />
      
    </div>
    <!-- form gird -->
    <div id="MyGrid" class="ag-theme-alpine" style="height: 80%">
      </div>
  </form>
      <div class="">
      <x-adminlte-button class="btn-flat" id="save" style="float: left;font-size: small;" type="button" label="Save" theme="success"/>
      <x-adminlte-button class="btn-flat" id="print" style="float: right; margin-top: 30px;font-size: small; padding: 8px 24px;margin-right: 20px;" type="button" label="Print" theme="success" />
      <x-adminlte-button class="btn-flat" id="stockout" style="float: right; margin-top: 30px;font-size: small; padding: 8px 24px;margin-right: 20px;" type="button" label="Print stock out" theme="success" />
      <x-adminlte-button class="btn-flat" id="lock" style="float: right; margin-top: 30px;font-size: small; padding: 8px 24px;margin-right: 20px;" type="button" label="Lock/ Unlock Vehicle" theme="success" />
      </div>
</div>   
@endsection
@section('css')
<style>
#search {
  float: right;
  margin-left: 50px;
}

#save {
  margin-top: 30px;
  margin-left: 20px;
  margin-bottom: 20px;
  padding: 8px 24px;
  display: inline-block;
  font-size: 16px;
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
  { field: 'No'},
  { field: 'Type Name' },
  { field: 'Delivery No' },
  { field: 'Route Name' },
  { field: 'Customer Code'},
  { field: 'Customer Name'},
  { field: 'WhsName', filter: 'agNumberColumnFilter' },
  { field: 'Order Number'},
  { field: 'Doc Date'},
  { field: 'Del. Date'},
  { field: 'Quantity'},
  { field: 'Weight'},
  { field: 'Truck Code'},
  { field: 'Capacity'},
  { field: 'Remaining Capacity'},
  { field: 'Truck Type'},
  { field: 'Truck Driver'},
  { field: 'Driver Info'},
  { field: 'Truck Driver Assistant'},
  { field: 'Driver Assistant Info'},
  { field: 'Delivery Status'},

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
</script>
@endpush
