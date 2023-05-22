@extends('adminlte::page')
@section('title', 'List Stock Out Request - Sales Order')
@section('plugins.Datatables', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('content_header')
    <h5>List Stock Out Request - Sales Order</h5>
@stop
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
<script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
@section('content')
    @if (count($errors) > 0)
        <ul>
            @foreach ($errors->all() as $error)
                <li class="text-danger">{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    @php
        $config = ['format' => 'L', 'format' => 'DD/MM/yyy'];
    @endphp

    <p style="float:right"><a href="{{ route('sales.add') }}">
            <x-adminlte-button label="Add New" theme="primary" icon="fas fa-plus" />
        </a> </p>
    {{-- Setup data for datatables --}}
    <form>
        <div class="form-row">
            <x-adminlte-input-date name="" id="fromDate" label="FromDate" :config="$config"
                label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-input-date name="" id="toDate" label="ToDate" :config="$config"
                label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-button class="btn" id="filterButton"
                style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button" label="Load Item"
                theme="success" icon="fas fa-filter" />
            <x-adminlte-button class="btn" id="getall"
                style="float: right;margin-top: 34px; margin-left:15px;font-size: small;height: 31px;" type="button"
                label="Load all order" theme="success" icon="fas fa-filter" />

        </div>
        <div id="myGrid" class="ag-theme-alpine" style="height: 70%">
        </div>
        <x-adminlte-button class="btn-flat" id="getSelectedRowsBtn" style="float: right;margin-right: 20px;" type="button"
            label="Apply SAP" theme="success" />
        <x-adminlte-button class="btn-flat" id="printpr" style="float: right;margin-right: 20px;" type="button"
            label="print_preview" theme="success" />
        <x-adminlte-button class="btn-flat" id="cancelSQ" style="float: right;margin-right: 20px;" type="button"
            label="Cancel Order" theme="danger" />
    </form>
    <div id="loadingModal" class="modal">
        <div class="modal-content">
            <div class="loader"></div>
            <p>Please wait...</p>
        </div>
    </div>

@stop

@section('css')
    <style media="only screen">
        .btn-flat {
            font-size: small;
            padding: 8px 24px;
            margin-top: 30px;
        }

        .row-green {
            background-color: #b6f599 !important;
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
@stop

@section('js')
    <script>
        var __basePath = './';
    </script>
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
        const columnDefs = [{
                headerName: '',
                field: '',
                maxWidth: 50,
                headerCheckboxSelection: true,
                checkboxSelection: true,
            },
            {
                headerName: 'Doc No',
                field: 'StockNo'

            },
            {
                headerName: 'Doc Date',
                field: 'StockDate',
                maxWidth: 150
            },
            {
                headerName: 'StoreID',
                field: 'U_SID',
                maxWidth: 150
            },
            {
                field: 'CustCode',

            },
            {
                field: 'CustName',
            },
            {
                field: 'saleSup'
            },
            {
                headerName: 'OrderTypeName',
                field: 'Name'
            },
            {
                headerName: 'SupportOrderNo',
                field: 'AbsID',

            },
            {
                headerName: 'WhsCode',
                field: 'FromWhsCode'
            },
            {
                headerName: 'WhsName',
                field: 'FromWhsName'
            },
            {
                headerName: 'PoNo.',
                field: 'POCardCode',

            },
            {
                headerName: 'Po Date',
                field: 'PODate',

            },
            {
                headerName: 'TeamCode',
                field: 'BinCode'

            },
            {
                field: 'ApplySAP'

            },
            {
                field: 'ApplySAPRemark'

            },
            {
                field: 'Note'

            },
            {
                field: 'SQNO'

            },
            {
                field: 'SONO'

            },
            {
                headerName: 'Delivery No.',
                field: 'DeliveryNO'

            },
            {
                headerName: 'AR.NO',
                field: 'ARNo'
            },
            {
                field: 'DeliveryStatus'

            },
            {
                headerName: 'UserCreate',
                field: 'UserName'
            },
            {
                field: 'DateCreate'

            },
            {
                field: 'DateUpdate'

            },
            {
                field: 'TotalWeight',

            },
            {
                field: 'ApplyStatus',

            },
            {
                field: 'StatusSAP',

            },
            {
                field: 'Canceled',

            },
        ];



        const gridOptions = {
            columnDefs: columnDefs,
            pagination: true,
            rowClassRules: {
                // row style function
                'row-green': (params) => {
                    return params.data.StatusSAP == 1;
                },
                'row-red': (params) => {
                    return params.data.Canceled === 'C';
                }
            },
            defaultColDef: {
                flex: 1,
                minWidth: 150,
                filter: true,
                resizable: true,
            },
            onRowDoubleClicked: function(params) {
                var id = params.data.StockNo;
                var url = '{{ route('sales.edit', ':id') }}';
                url = url.replace(':id', id);
                window.location.href = url;
            },
            rowSelection: 'multiple'
        };


        function onBtExport() {
            gridOptions.api.exportDataAsExcel();
        }

        function loadInitialData() {
            // Make an API call to abc.com to retrieve 100 records

            // Update the grid with the retrieved data
            gridOptions.api.setRowData({!! $results !!});
        }

        function loadFilteredData() {
            const loadingModal = document.getElementById("loadingModal");
            const filter = document.getElementById("filterButton");

            filter.disabled = true;
            loadingModal.style.display = "block";
            $.ajax({
                type: 'GET',
                url: '{{ route('sales.list') }}',
                data: filterData,
                dataType: 'json',
                success: function(data) {
                    gridOptions.api.setRowData(data);
                    loadingModal.style.display = "none";
                    filter.disabled = false;
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                    loadingModal.style.display = "none";
                    filter.disabled = false;
                }
            });

        }

        function loadallData() {
            const loadingModal = document.getElementById("loadingModal");
            const getallBtn = document.getElementById("getall");

            getallBtn.disabled = true;
            loadingModal.style.display = "block";

            $.ajax({
                type: 'GET',
                url: '{{ route('inv.loadall') }}',
                dataType: 'json',
                success: function(data) {
                    gridOptions.api.setRowData(data);
                    loadingModal.style.display = "none";
                    getallBtn.disabled = false;

                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                    loadingModal.style.display = "none";
                    getallBtn.disabled = false;
                }
            });
        }

        let filterData = {};
        // setup the grid after the page has finished loading
        document.addEventListener('DOMContentLoaded', function() {

            var gridDiv = document.querySelector('#myGrid');
            new agGrid.Grid(gridDiv, gridOptions);

            const filterButton = document.querySelector('#filterButton');
            filterButton.addEventListener('click', function() {
                // Get the filter values from the input fields
                const filterInput1 = document.querySelector('#fromDate');
                const filterInput2 = document.querySelector('#toDate');
                filterData.fromdate = filterInput1.value;
                filterData.todate = filterInput2.value;
                if (filterInput1.value == "") {
                    alert("Please choose from date");
                } else if (filterInput2.value == "") {
                    alert("Please choose to date");
                } else {
                    const filter = document.getElementById("filterButton");
                    loadingModal.style.display = "block";
                    // Disable the submit button
                    filter.disabled = true;
                    // Load the filtered data from the API
                    loadFilteredData();
                }

            });


            document.querySelector("#getall").addEventListener("click", function() {
                // Get the filter values from the input fields

                const getallBtn = document.getElementById("getall");
                loadingModal.style.display = "block";

                // Disable the submit button
                getallBtn.disabled = true;
                loadallData();
            });
            loadInitialData();
        });

        document.querySelector("#getSelectedRowsBtn").addEventListener("click", function() {
            const selectedRows = gridOptions.api.getSelectedRows().filter(row => row.StatusSAP == 0 && row
                .Canceled != "C");
            const selectedProIds = selectedRows.map((row) => row.StockNo);
            console.log(selectedProIds);
            if (selectedProIds.length === 0) {
                alert("chứng từ đã chọn đã apply/hoặc bạn chưa chọn chứng từ nào!")

            } else {
                const loadingModal = document.getElementById("loadingModal");
                const submitBtn = document.getElementById("getSelectedRowsBtn");
                loadingModal.style.display = "block";

                // Disable the submit button
                submitBtn.disabled = true;

                $.ajax({
                    type: 'GET',
                    url: '{{ route('sales.apply') }}',
                    data: {
                        SoNo: selectedProIds
                    },
                    dataType: 'json',
                    success: function(data) {
                        alert("đã apply thành công! Vui lòng kiểm tra SO apply")
                        loadFilteredData()
                        submitBtn.disabled = false;
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 500) {
                        alert("Internal Server Error! Vui lòng liện hệ administrator!");
                    } else {
                        var response = JSON.parse(xhr.responseText);
                         var responseData = response.data;
                         alert("apply thất bại! message error:"+responseData);
                    }

                    console.log(error);
                        loadingModal.style.display = "none";

                        // Enable the submit button
                        submitBtn.disabled = false;
                        loadFilteredData()
                    }
                })
            }

        });
        //cancel document
        document.querySelector("#cancelSQ").addEventListener("click", function() {
            const selectedRows = gridOptions.api.getSelectedRows().filter(row => row.StatusSAP == 0 && row
                .Canceled != "C");
            const selectedProIds = selectedRows.map((row) => row.StockNo);
            console.log(selectedProIds);
            if (selectedProIds.length === 0) {
                alert("chứng từ đã chọn đã canceled/hoặc bạn chưa chọn chứng từ nào!")

            } else {
                const loadingModal = document.getElementById("loadingModal");
                const submitBtn = document.getElementById("cancelSQ");

                $.ajax({
                    type: 'GET',
                    url: '{{ route('sales.cancel') }}',
                    data: {
                        SoNo: selectedProIds
                    },
                    dataType: 'json',
                    async: false,
                    beforeSend: function() {
                        // Show the loading modal
                        loadingModal.style.display = "block";
                        // Disable the submit button
                        submitBtn.disabled = true;
                    },
                    success: function(data) {
                        alert("Canceled success!")
                        loadFilteredData()
                        submitBtn.disabled = false;
                    },
                    error: function() {
                        alert("Canceld failed !,Please validate data!");
                        loadingModal.style.display = "none";

                        // Enable the submit button
                        submitBtn.disabled = false;
                        loadFilteredData()
                    }
                })
            }
        })
        document.querySelector("#printpr").addEventListener("click", function() {
            const selectedRows = gridOptions.api.getSelectedRows()
            const selectedProIds = selectedRows.map((row) => row.StockNo);
            console.log(selectedProIds.join(','));
            const url = '{{ route('print-preview') }}'+'?so='+selectedProIds;
                          // redirect to the new URL
                          window.open(url, '_blank');
        });
    </script>
@stop
