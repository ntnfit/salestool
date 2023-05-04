@extends('adminlte::page')

@section('title', 'Sale by cust Group /Customer/Product')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.select2', true)
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
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
        $config = ['format' => 'yyyy/MM/DD'];
    @endphp
    <h3> Sale by cust Group /Customer/Product</h3>
   
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
                <x-adminlte-select label="channel" igroup-size="sm"
                    label-class="text-lightblue truckcode" name="channel" id="channel" igroup-size="sm"
                    fgroup-class="col-md-3"  enable-old-support>
                    <option value="All" selected>All</option>
                    <option value="MT">MT</option>
                    <option value="GT">GT</option>
                </x-adminlte-select>
                <x-adminlte-button class="btn" id="search"
                    style="float: right;margin-top: 30px;font-size: small;height: 31px;" type="button" label="Search"
                    theme="success" />


            </div>


    </div>
    <!-- form gird -->
    <div id="MyGrid" class="ag-theme-alpine" style="height: 70%">
    </div>


    </form>
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
              
                field: "CustGrpCode",
            },
            {
               
                field: 'CustGroup',
            },
            {
                headerName: 'CardCode',
                field: 'CardCode',
            },
            {
                headerName: 'CardName',
                field: 'CardName',
            },
            {
                headerName: 'TotalQuantity',
                field: 'TotalQuantity',
            },
           

            {
                headerName: 'LineTotal',
                field: 'LineTotal',
            },
            {
                headerName: 'Gtoal',
                field: 'Gtoal',
            },
            {
                headerName: 'Channel',
                field: 'Channel',
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
          
            animateRows: true,
            pagination: true
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
                url: '{{ route('report.salebycust') }}',
                data: filterData,
                dataType: 'json',
                success: function(data) {
                    gridOptions.api.setRowData(data);
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
            filterButton.addEventListener('click', function() {
                console.log("okay");
                const submitBtn = document.getElementById("search");
                const loadingModal = document.getElementById("loadingModal");
                const channel = document.getElementById('channel').value;
                // Get the filter values from the input fields
                const filterInput1 = document.querySelector('#fromDate');
                const filterInput2 = document.querySelector('#toDate');
                filterData.fromDate = filterInput1.value.replace(/\//g, '');
                filterData.toDate = filterInput2.value.replace(/\//g, '');
                filterData.channel=channel;
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

            loadInitialData();

            });

    </script>
@endpush
