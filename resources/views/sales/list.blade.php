@extends('adminlte::page')
@section('title', 'List Stock Out Request - Sales Order')
@section('plugins.Datatables', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('content_header')
    <h3>List Stock Out Request - Sales Order</h3>
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
            <x-adminlte-input-date name="podate" label="FromDate" :config="$config" label-class="text-lightblue"
                igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-input-date name="podate" label="ToDate" :config="$config" label-class="text-lightblue"
                igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-button class="btn" id="search"
                style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button" label="Load Item"
                theme="success" icon="fas fa-filter" />

        </div>
        <div id="myGrid" class="ag-theme-alpine" style="height: 100%">
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
                field: 'DocNo'
            },
            {
                field: 'DocDate',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'StoreID'
            },
            {
                field: 'CustCode',
                maxWidth: 100
            },
            {
                field: 'CustomerName',
                filter: 'RouteName',
                filterParams: filterParams,
            },
            {
                field: 'SaleSup'
            },
            {
                field: 'OrderTypeName',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'SupportOrderNo',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'WhsCode',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'WhsName',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'PoNo.',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'Po Date',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'TeamCode',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'ApplySAP',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'Note',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'SQ No',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'SO No.',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'Delivery No.',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'AR.NO',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'DeliveryStatus',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'UserCreate',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'DateCreate',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'DateUpdate',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'TotalWeight',
                filter: 'agNumberColumnFilter'
            },
            {
                field: 'ApplySatus',
                filter: 'agNumberColumnFilter'
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
        // setup the grid after the page has finished loading
        document.addEventListener('DOMContentLoaded', function() {
            var gridDiv = document.querySelector('#myGrid');
            new agGrid.Grid(gridDiv, gridOptions);
            gridOptions.api.setRowData("")
        });
    </script>
@stop
