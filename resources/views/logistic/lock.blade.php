@extends('adminlte::page')
@section('title', 'Lock Vehicle')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.BsCustomFileInput', true)
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@section('content')
<h3> Lock/ Unlock Vehicle </h3>
<div class="content">
    <div id="MyGrid" class="ag-theme-alpine" style="height: 80%">
    </div>
     
    <x-adminlte-button class="btn-flat" id="lock" style="float: left;font-size: small; margin-top:30px;padding: 8px 24px;" type="button" label="Lock" theme="success"/>
    <x-adminlte-button class="btn-flat" id="unlock" style="float: left;font-size: small;margin-top:30px; margin-left: 20px;padding: 8px 24px;" type="button" label="Unlock" theme="success"/>
    <a href="{{route('logistic.truckinfor')}}"><x-adminlte-button class="btn-flat" id="back" style="float: right;font-size: small;margin-top:30px;padding: 8px 24px;" type="button" label="Back" theme="success"/></a>
    <x-adminlte-button class="btn-flat" id="export" onclick="onBtExport()" style="float: right;font-size: small;margin-top:30px; margin-right: 20px;padding: 8px 24px;" type="button" label="Export Excel" theme="success"/>
</div>
<div id="loadingModal" class="modal">
  <div class="modal-content">
      <div class="loader"></div>
      <p>Please wait...</p>
  </div>
</div>




@endsection


@section('css')
<style>
          /* Popup Modal styles */
          .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            border-radius: 5px;
            width: 200px;
            height: 100px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            padding: 20px;
        }

        /* Loading spinner styles */
        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            margin: auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
    {
          headerName: '',
          field: '',
          maxWidth: 50,
          headerCheckboxSelection: true,
          checkboxSelection: true,
          pinned: 'left',
      },
    { field: 'Code'},
    { headerName:'TruckDriver', field: 'U_TruckDriver'},
    { headerName:'Type',field: 'U_Type'},
    { headerName:'Capacity',field: 'U_Capacity'},
    { headerName:'Phone',field: 'U_Tel'},
    { headerName:'Status',field: 'STATUSVH'},

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
  rowSelection: 'multiple'
};
function onBtExport() {
  gridOptions.api.exportDataAsExcel();
}
// setup the grid after the page has finished loading
document.addEventListener('DOMContentLoaded', function () {
  var gridDiv = document.querySelector('#myGrid');
  new agGrid.Grid(gridDiv, gridOptions);
  gridOptions.api.setRowData({!!$results!!})
    });
    document.querySelector("#lock").addEventListener("click", function() {
            const selectedRows = gridOptions.api.getSelectedRows().filter(row => row.STATUSVH=="UnLock");
            const selectedProIds = selectedRows.map((row) => row.Code);
            const loadingModal = document.getElementById("loadingModal");
            const submitBtn = document.getElementById("lock");
            if(selectedProIds.lenght===0)
            {
              alert("TruckCode is empty!")
            }
            else
            {
              
                loadingModal.style.display = "block";

                // Disable the submit button
                submitBtn.disabled = true;

                $.ajax({
                    type: 'GET',
                    url: '{{ route('logistic.applylock') }}',
                    data: {
                      TruckCode: selectedProIds,
                      type:'L'
                    },
                    dataType: 'json',
                    success: function(data) {
                        alert("đã lock thành công!")
                        location.reload();
                    },
                    error: function() {
                        alert("đã lock thất bại!, vui lòng kiếm tra dữ liệu!");
                        loadingModal.style.display = "none";

                        // Enable the submit button
                        submitBtn.disabled = false;
                    }
                })
            }
            
      });
      document.querySelector("#unlock").addEventListener("click", function() {
            const selectedRows = gridOptions.api.getSelectedRows().filter(row => row.STATUSVH=="Lock");
            const selectedProIds = selectedRows.map((row) => row.Code);
            const loadingModal = document.getElementById("loadingModal");
            const submitBtn = document.getElementById("unlock");
            if(selectedProIds.lenght===0)
            {
              alert("TruckCode is empty!")
            }
            else
            {
              
                loadingModal.style.display = "block";

                // Disable the submit button
                submitBtn.disabled = true;

                $.ajax({
                    type: 'GET',
                    url: '{{ route('logistic.applylock') }}',
                    data: {
                      TruckCode: selectedProIds,
                      type:'A'
                    },
                    dataType: 'json',
                    success: function(data) {
                        alert("đã Unlock thành công!")
                        location.reload();
                    },
                    error: function() {
                        alert("đã Unlock thất bại!, vui lòng kiếm tra dữ liệu!");
                        loadingModal.style.display = "none";

                        // Enable the submit button
                        submitBtn.disabled = false;
                    }
                })
            }
            
      });
</script>
@endpush

