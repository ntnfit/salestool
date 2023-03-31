@extends('adminlte::page')
@section('title', 'List Stock Out Request - Sales Order')
@section('plugins.Datatables', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('content_header')
    <h5>List Stock Out Request - Sales Order</h5>
@stop

<script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
@section('content')

    @php
        $config = ['format' => 'L'];
    @endphp

    <p style="float:right"><a href="{{ route('sales.add') }}">
            <x-adminlte-button label="Add New" theme="primary" icon="fas fa-plus" />
        </a> </p>
    {{-- Setup data for datatables --}}
    <form>
        <div class="form-row">
            <x-adminlte-input-date name="" id="fromDate" label="FromDate" :config="$config" label-class="text-lightblue"
                igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-input-date name=""  id="toDate" label="ToDate" :config="$config" label-class="text-lightblue"
                igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-button class="btn" id="filterButton"
                style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button" label="Load Item"
                theme="success" icon="fas fa-filter" />

        </div>
        <div id="myGrid" class="ag-theme-alpine" style="height: 70%">
        </div>
        <x-adminlte-button class="btn-flat" id="apply" style="float: right;margin-right: 20px;" type="button"
            label="Apply SAP" theme="success" />
        <x-adminlte-button class="btn-flat" id="confirm" style="float: right; margin-right: 20px; " type="button"
            label="Confirm" theme="success" />
    </form>


@stop

@section('css')
    <style media="only screen">
      
        .btn-flat {
            font-size: small;
            padding: 8px 24px;
            margin-top: 30px;
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
             headerName: 'Doc No',
                field: 'StockNo',
                filter: 'agNumberColumnFilter'
            },
            {
                headerName: 'Doc Date',
                field: 'StockDate',
                maxWidth:150
            },
            {
                headerName: 'StoreID',
                field: 'U_SID',
                maxWidth:150
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
        function loadInitialData() {
        // Make an API call to abc.com to retrieve 100 records
        
        // Update the grid with the retrieved data
        gridOptions.api.setRowData({!!$results!!});
        }
        function  loadFilteredData()
        {
            $.ajax({ 
            type: 'GET', 
            url: '{{route('sales.list')}}', 
            data: filterData, 
            dataType: 'json',
            success: function (data) { 
                gridOptions.api.setRowData(data);
            }
        });
           
        }
        let filterData={};
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

            // Load the filtered data from the API
            loadFilteredData();
        });
            loadInitialData();
        });
    </script>
@stop
