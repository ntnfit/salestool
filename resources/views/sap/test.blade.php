@extends('adminlte::page')

@section('title', 'Customer data')
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script>
@section('content')

  <form>
    <div class="form-row">
        <div class="form-group col-sx-4">Sale manager</div>
        <div class="form-group col-md-4">
        <select id="channel" class="form-control">
            <option value="0">All</option>
            <option value="DTH" selected>Doan Thi Hong</option>
            <option value="GT">GT Chanel</option>
        </select>
        </div>
    <div class="form-group col-md-2">
   
      <button type="button" class="form-control btn btn-primary" id="search" onclick="sCustomer()">Search</button>
    </div>
    <div class="form-group col-md-2">
      
      <button type="button" class="form-control btn btn-primary" id="export-excel" onclick="onBtExport()">Excel</button>
    </div>
    </div>
</form>
		<div id="myGrid" class="ag-theme-alpine" style="height: 30%">
		</div>


    <style media="only screen">
            html, body {
                height: 100%;
                width: 100%;
                margin: 0;
                box-sizing: border-box;
                -webkit-overflow-scrolling: touch;
            }

            html {
                position: absolute;
                top: 0;
                left: 0;
                padding: 0;
                overflow: auto;
            }

            body {
                padding: 1rem;
                overflow: auto;
            }
            
        </style>

		<script>var __basePath = './';</script>
		<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@28.2.1/dist/ag-grid-community.min.js"> 
		</script>
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@28.2.1/dist/ag-grid-enterprise.min.js">
      </script>
		<script>
function BtnCellRenderer() {}

BtnCellRenderer.prototype.init = function (params) {
  this.params = params;

  this.eGui = document.createElement('button');
  this.eGui.innerHTML = 'Delete';
  this.eGui.classList.add('delete-button'); // Add CSS class

  this.btnClickedHandler = this.btnClickedHandler.bind(this);
  this.eGui.addEventListener('click', this.btnClickedHandler);
};

BtnCellRenderer.prototype.getGui = function () {
  return this.eGui;
};

BtnCellRenderer.prototype.btnClickedHandler = function (event) {
  const selectedRow = this.params.node;
  const gridApi = this.params.api;
  gridApi.applyTransaction({ remove: [selectedRow.data] });
};

BtnCellRenderer.prototype.destroy = function () {
  this.eGui.removeEventListener('click', this.btnClickedHandler);
};

const columnDefs = [
  { field: 'CardCode' },
  { field: 'CardName' },
  { field: 'GroupName' },
  { field: 'ChannelName'},
  { field: 'RouteName' },
  { field: 'LocationName' },
  {
    headerName: 'PGCode',
    maxWidth: 100 ,
    cellRenderer: BtnCellRenderer
  }
];

const gridOptions = {
  columnDefs: columnDefs,
  pagination: true,
  defaultColDef: {
    flex: 1,
    minWidth: 150,
    filter: true,
    resizable: true,
  }
};

document.addEventListener('DOMContentLoaded', function () {
  var gridDiv = document.querySelector('#myGrid');
  new agGrid.Grid(gridDiv, gridOptions);
  gridOptions.api.setRowData({!!$custdata!!});
});



		</script>
@stop