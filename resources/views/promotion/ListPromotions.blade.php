@extends('adminlte::page')

@section('title', 'List promotion')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script>
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
@section('content')

@if(count($errors) >0)
            <ul>
                @foreach($errors->all() as $error)
                    <li class="text-danger">{{ $error }}</li>
                @endforeach
            </ul>
 @endif 
 @if(session()->has('message'))
 <div class="alert alert-success">
	 {{ session()->get('message') }}
 </div>
@endif

    <div class="form-group col-md-12">
      <a style="float: right" href="{{route('add-promotions')}}"><x-adminlte-button label="add new" theme="primary" icon="fas fa-plus"/> </a>
      <button class ="btn btn-danger" id="getSelectedRowsBtn">Terminate</button>
    </div>
   
		<div id="myGrid" class="ag-theme-alpine" style="height: 100%">
		</div>
    <div id="loadingModal" class="modal">
      <div class="modal-content">
          <div class="loader"></div>
          <p>Please wait...</p>
      </div>
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
            .row-red {
            background-color: #FFABAB !important;
        }
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
    { headerName: '', field: '', maxWidth: 50,  headerCheckboxSelection: true, checkboxSelection: true, headerCheckboxSelectionFilteredOnly: true,},
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
  { field: 'hasTerminate' },
];

const gridOptions = {
  columnDefs: columnDefs,
  pagination: true,
  rowClassRules: {
            // row style function

            'row-red': (params) => {
                return params.data.hasTerminate == '1';
                }
            },
  defaultColDef: {
    flex: 1,
    minWidth: 150,
    filter: true,
    resizable: true,
  },
  onRowDoubleClicked: function(params) {
                var id = params.data.ProId;
                var url = '{{ route("pro.edit", ":id") }}';
                url = url.replace(':id', id);
                window.location.href = url;
            },
    rowSelection: 'multiple'
};
document.querySelector("#getSelectedRowsBtn").addEventListener("click", function () {
      const selectedRows = gridOptions.api.getSelectedRows();
      const selectedProIds = selectedRows.map((row) => row.ProId);
      if (selectedProIds.length === 0) {
                alert("Please choose one document!")

            } else {
                const loadingModal = document.getElementById("loadingModal");
                const submitBtn = document.getElementById("getSelectedRowsBtn");
                loadingModal.style.display = "block";

                // Disable the submit button
                submitBtn.disabled = true;

                $.ajax({
                    type: 'get',
                    url: '{{ route('pro.terminated') }}',
                    data: {
                        SoNo: selectedProIds,
                        protype:"1"
                    },
                    dataType: 'json',
                    success: function(data) {
                        alert("Terminated success!")
                        location.reload();
                    },
                    error: function() {
                        alert("Terminated failed!, Please check again data!");
                        loadingModal.style.display = "none";

                        // Enable the submit button
                        submitBtn.disabled = false;
                    }
                })
            }
    });
// setup the grid after the page has finished loading
document.addEventListener('DOMContentLoaded', function () {
  var gridDiv = document.querySelector('#myGrid');
  new agGrid.Grid(gridDiv, gridOptions);
    
  gridOptions.api.setRowData({!!$Promotionlist!!})

});

		</script>
@endpush