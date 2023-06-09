@extends('adminlte::page')

@section('title', 'Invoices Stauts')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.select2', true)
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
<script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@section('content')
    @php
        $config = ['format' =>'DD/MM/yyy'];
    @endphp
    <div class="content">
        <form>
            <div class="row">
                <!-- header input -->
                <x-adminlte-input-date name="fromdate" id="fromdate" label="From Date" :config="$config"
                    label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-gradient-danger">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-date>
                <x-adminlte-input-date name="todate" id="todate" label="To Date" :config="$config"
                    label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-gradient-danger">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-date>

                <x-adminlte-button class="btn" id="search"
                    style="float: right;margin-top: 30px;font-size: small;height: 31px;" type="button" label="Search"
                    theme="success" />
                <x-adminlte-button class="btn" id="searchall"
                    style="float: right;margin-left:10px; margin-top: 30px;font-size: small;height: 31px;" type="button"
                    label="Search all" theme="success" />


            </div>


    </div>
    <!-- form gird -->
    <div id="MyGrid" class="ag-theme-alpine" style="height: 70%">
    </div>


    </form>
    <x-adminlte-button class="btn" id="collectButton"
     type="button"
    label="Save" theme="success" />
    </div>
    <div id="loadingModal" class="modal">
        <div class="modal-content">
            <div class="loader"></div>
            <p>Please wait...</p>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <style>
        #search {
            float: right;
            margin-left: 20px;
        }

        .btn-flat {
            font-size: small;
            padding: 8px 24px;
            margin-top: 30px;
            margin-bottom: 30px;

        }

        .dropdown-menu.show {
            max-width: 550px;
        }

        label.text-lightblue.truckcode {
            margin-left: 150px;
        }

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
    < <script>
        var __basePath = './';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community@28.2.1/dist/ag-grid-community.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@28.2.1/dist/ag-grid-enterprise.min.js"></script>
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
                headerName: 'No',
                field: 'DocNum',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Invoice Number',
                field: 'NumAtCard',
                cellClass: 'InvoiceNos',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'CardCode',
                field: 'CardCode',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'CardName',
                field: 'CardName',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'WhsName',
                field: 'WhsName',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'DocDate',
                field: 'DocDueDate',
            },
            {
                headerName: 'DelDate',
                field: 'TaxDate',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'SQ.No',
                field: 'U_SoPhieu',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Truck Infor',
                field: 'U_TruckInfo',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Driver name',
                field: 'U_DriverName',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Driver infor',
                field: 'U_DriverInfo',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Driver assistant',
                field: 'U_DriverAss',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Driver assistant infor',
                field: 'U_DriverAssInfo',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Cust.Po.No',
                field: 'CustPoNo',
                filter: 'agTextColumnFilter',
            },
            {
                field: 'CustPoDate',
            },
            {
                headerName: 'Quantity',
                field: 'Quantity',
                filter: 'agTextColumnFilter',
            },
            {
                field: 'Weight',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Received',
                field: 'Received',
                cellRenderer: (params) => {
                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.checked = params.value == 1;
                    input.addEventListener('change', () => {
                        if (input.checked) {
                            params.data.Received = 1;
                            const now = new Date();
                            params.data.DateReceived = now.toISOString().slice(0, 19).replace('T', ' ') +
                                '.' + now.getMilliseconds(); // Update the row data
                            try {
                                params.api.refreshCells({
                                    rowNodes: [params.node],
                                    columns: ['DateReceived'],
                                }); // Refresh the cell to display the updated value
                            } catch (e) {
                                console.error(e); // Log any errors
                            }
                        } else {
                            params.data.Received=null;
                            params.data.DateReceived = null; // Clear the row data
                            try {
                                params.api.refreshCells({
                                    rowNodes: [params.node],
                                    columns: ['DateReceived'],
                                }); // Refresh the cell to display the updated value
                            } catch (e) {
                                console.error(e); // Log any errors
                            }
                        }
                    });
                    return input;
                },
            },
            {
                headerName: 'DateReceived',
                field: 'DateReceived',
                cellRenderer: 'DateReceived',
            },


            {
                headerName: 'Send Invoice',
                field: 'sendInvoice',
                cellRenderer: (params) => {
                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.checked = params.value == 1;
                    input.addEventListener('change', () => {
                        if (input.checked) {
                            params.data.sendInvoice = 1;
                            const now = new Date();
                            params.data.DateSend = now.toISOString().slice(0, 19).replace('T', ' ') +
                                '.' + now.getMilliseconds(); // Update the row data
                            try {
                                params.api.refreshCells({
                                    rowNodes: [params.node],
                                    columns: ['DateSend'],
                                }); // Refresh the cell to display the updated value
                            } catch (e) {
                                console.error(e); // Log any errors
                            }
                        } else {
                            params.data.sendInvoice = null;
                            params.data.DateSend = null; // Clear the row data
                            try {
                                params.api.refreshCells({
                                    rowNodes: [params.node],
                                    columns: ['DateSend'],
                                }); // Refresh the cell to display the updated value
                            } catch (e) {
                                console.error(e); // Log any errors
                            }
                        }
                    });
                    return input;
                },
            },
            {
                headerName: 'DateSend',
                field: 'DateSend',
            },
            {
                headerName: 'Receiver',
                field: 'Receiver',
                editable: true,
                filter: 'agTextColumnFilter',
            },
            {

                field: "DocEntry",
                filter: 'agTextColumnFilter',
            },
            {

                field: "KHHD",
                filter: 'agTextColumnFilter',
            },
            {

                field: "Location",
                filter: 'agTextColumnFilter',
            }

        ];

        const excelstyle = [
              
              {
                  id: 'InvoiceNos',
                  dataType: 'String',
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
                floatingFilter: true,
            },

            animateRows: true,
            pagination: true,
            rowSelection: 'multiple',
            onGridReady: () => {
                // Store the initial data for all nodes in the grid
                gridOptions.api.forEachNode((node) => {
                    node.oldData = {
                        ...node.data
                    };
                });
            },
            excelStyles:excelstyle
        };

        function onBtExport() {
            gridOptions.api.exportDataAsExcel();
        }

        function loadInitialData() {
            // Make an API call to abc.com to retrieve 100 records

            // Update the grid with the retrieved data
            gridOptions.api.setRowData([]);
        }

        function loadFilteredData() {


            $.ajax({
                type: 'GET',
                url: '{{ route('sales.arlist') }}',
                data: filterData,
                dataType: 'json',
                success: function(data) {
                    gridOptions.api.setRowData(data);
                    gridOptions.api.forEachNode((node) => {
                        node.oldData = {
                            ...node.data
                        };
                    });
                    loadingModal.style.display = "none";

                }
            });

        }

        function loadAllData() {
            $.ajax({
                type: 'GET',
                url: '{{ route('sales.arlist') }}',
                dataType: 'json',
                success: function(data) {
                    gridOptions.api.setRowData(data);
                    gridOptions.api.forEachNode((node) => {
                        node.oldData = {
                            ...node.data
                        };
                    });
                    loadingModal.style.display = "none";

                }
            });

        }
        let filterData = {};
        // setup the grid after the page has finished loading
        document.addEventListener('DOMContentLoaded', function() {

            var gridDiv = document.querySelector('#myGrid');
            new agGrid.Grid(gridDiv, gridOptions);

            const filterButton = document.querySelector('#search');
            //fileter data
            const allButton = document.querySelector('#searchall');
            filterButton.addEventListener('click', function() {
                console.log("okay");
                const submitBtn = document.getElementById("search");
                const loadingModal = document.getElementById("loadingModal");

                // Get the filter values from the input fields
                const filterInput1 = document.querySelector('#fromDate');
                const filterInput2 = document.querySelector('#toDate');
                filterData.fromDate =filterInput1.value.split('/').reverse().join('');
                filterData.toDate = filterInput2.value.split('/').reverse().join('');
                console.log(filterData);
                if (filterInput1.value == "") {
                    alert("Please choose From date!");
                } else if (filterInput2.value == "") {
                    alert("Please choose To Date!");
                } else {
                    loadingModal.style.display = "block";
                    submitBtn.disabled = true;
                    // Load the filtered data from the API
                    loadFilteredData();
                    submitBtn.disabled = false;
                }

            });
            //get all data
            allButton.addEventListener('click', function() {
                const alldatatBtn = document.getElementById("searchall");
                const loadingModal = document.getElementById("loadingModal");
                loadingModal.style.display = "block";
                alldatatBtn.disabled = true;
                // Load the filtered data from the API
                loadAllData();
                alldatatBtn.disabled = false;
            });
            const collectButton = document.querySelector('#collectButton');
            collectButton.addEventListener('click', () => {
                const updatedRows = [];
                gridOptions.api.forEachNode((node) => {
                    const data = node.data;
                    const oldData = node.oldData || {};
                    if (data.DateReceived != oldData.DateReceived || data.DateSend != oldData
                        .DateSend || data.Receiver != oldData.Receiver) {
                        updatedRows.push(data);
                    }
                });
                if (updatedRows.length === 0) {
                    alert('No data has been changed.');
                    return;
                }
                //console.log(updatedRows);
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url:  '{{ route('sales.updatear') }}',
                    type: 'POST',
                    data: { dataNo:updatedRows},
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        console.log("save suceess");
                        alert("save data suceess!");
                    },
                    error: function(xhr, status, error) {
                        console.log("save failed");
                        alert("save data fail!");
                    }
                });
            });
            loadInitialData();

        });
    </script>
@endpush
