@extends('adminlte::page')

@section('title', 'List promotion')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script>
@section('content')


 
    <div class="form-group col-md-2">
      
      <button type="button" class="form-control btn btn-primary" id="export-excel" onclick="onBtExport()">Excel</button>
      <button id="getSelectedRowsBtn">Get Selected Rows</button>
    </div>
   
		<div id="myGrid" class="ag-theme-alpine" style="height: 100%">
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
@endsection
@push('js')
		<script>var __basePath = './';</script>
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
    { headerName: '', field: '', maxWidth: 50,  headerCheckboxSelection: true, checkboxSelection: true, },
  { headerName:"ProId",field: 'ProId',maxWidth: 100 
    },
  {headerName:"Promotion Name",field: 'PromotionName'},
  {  headerName:"Promotion Type",field: 'ProtypeName' },
  { headerName:"From Date",field: 'Fromdate' },
  {
    headerName:"To Date",field: 'ToDate'
  },
  { field: 'Quatity' },
  { field: 'TotalAmount'},
  { field: 'DiscountAmt' },
  { field: 'Special' },
  { field: 'Rouding' },
  { field: 'DateCreate' },
  { field: 'DateUpdate' },
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
  onRowDoubleClicked: function(params) {
                var id = params.data.ProId;
                window.location.href = "aabc.com/" + id;
            },
    rowSelection: 'multiple'
};
document.querySelector("#getSelectedRowsBtn").addEventListener("click", function () {
      const selectedRows = gridOptions.api.getSelectedRows();
      const selectedProIds = selectedRows.map((row) => row.ProId);
      console.log(selectedProIds);
    });
// setup the grid after the page has finished loading
document.addEventListener('DOMContentLoaded', function () {
  var gridDiv = document.querySelector('#myGrid');
  new agGrid.Grid(gridDiv, gridOptions);
    
  gridOptions.api.setRowData({!!$Promotionlist!!})

});

		</script>
@endpush