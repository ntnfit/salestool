@extends('adminlte::page')

@section('title', 'Truck information')
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
        $config = ['format' => 'DD/MM/yyy'];
    @endphp
    <h5> Truck Information</h5>
    @php
        $configss = [
            'title' => 'Select data',
            'liveSearch' => true,
            'liveSearchPlaceholder' => 'Search...',
            'showTick' => true,
            'actionsBox' => true,
        ];
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

                <x-adminlte-select-bs label="Truck Code" :config="$configss" igroup-size="sm"
                    label-class="text-lightblue truckcode" name="truckcode" id="truckcode" igroup-size="sm"
                    fgroup-class="col-md-3" style="margin-left: 150px" enable-old-support>
                    <option value=""></option>
                    @foreach ($results as $result)
                        <option value="{{ $result->Code }}">
                            {{ 'Code:' . $result->Code}}
                        </option>
                    @endforeach
                    <option value="">null</option>
                </x-adminlte-select-bs>
                <x-adminlte-button class="btn" id="apply"
                    style="float: right;margin-top: 30px;font-size: small;height: 31px;margin-left:10px;" type="button"
                    label="Apply" theme="success" />


            </div>


    </div>
    <input type="text" id="filter-text-box" placeholder="Filter..." oninput="onFilterTextBoxChanged()">
    <!-- form gird -->
    <div id="MyGrid" class="ag-theme-alpine" style="height: 50%">
    </div>
    <div id="subgird" class="ag-theme-alpine" style="height: 30%">
    </div>


    </form>
    <x-adminlte-button class="btn-flat" id="rmdo" style="float: right;  margin-right: 20px;" type="button"
    label="remove DO" theme="danger" />
    <x-adminlte-button class="btn-flat" id="print" style="float: right;  margin-right: 20px;" type="button"
        label="Print" theme="success" />
    <x-adminlte-button class="btn-flat" id="stockout" style="float: right;  margin-right: 20px;" type="button"
        label="Print stock out" theme="success" />
    <a href="{{ route('logistic.lock') }}">
        <x-adminlte-button class="btn-flat" id="lock" style="float: right;  margin-right: 20px;" type="button"
            label="Lock/ Unlock Vehicle" theme="success" />
    </a>


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

        const columnDefs = [{
                headerName: '',
                field: '',
                maxWidth: 50,
                headerCheckboxSelection: true,
                checkboxSelection: true,
                pinned: 'left',
            },
            {
                headerName: "Truck Info",
                field: "U_TruckInfo",
                rowGroup: true,
                enableRowGroup: true,
                sort: 'asc',
                hide: true,
                cellStyle: {color: 'red', 'background-color': 'green'}
            },
           
            {
                headerName: 'Due Date',
                field: 'DocDueDate',
                valueFormatter: (params) => {
                    if (!params.value || isNaN(Date.parse(params.value))) {
                    return ''; // Return an empty string for empty or invalid date values
                    }
                var date = new Date(params.value);
                var day = date.getDate().toString().padStart(2, '0');
                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                var year = date.getFullYear().toString();
                return day + '/' + month + '/' + year;
            }
            },
            {
                headerName: 'Warehouse',
                field: 'WhsName',
            },
            {
                headerName: 'Type',
                field: 'TypeName',
            },
            {
                headerName: 'Truck Info',
                field: 'U_TruckInfo',
            },
            {
                headerName: 'Weight',
                field: 'Weight',
                aggFunc: (params) => {
                    let sum = 0;
                    params.values.forEach((value) => (sum += parseFloat(value)));
                    return sum.toFixed(3);
                }
            },
            {
                headerName: 'Route Name',
                field: 'U_RouteName',
            },
            {
                headerName: 'Card Code',
                field: 'CardCode',
            },
            {
                headerName: 'Card Name',
                field: 'CardName',
            },
            {
                headerName: 'Delivery_Addr',
                field: 'Delivery_Addr',
                
            },

            {
                headerName: 'Del No',
                field: 'U_DelNo',
                editable: true
            },
            {
                headerName: 'Doc Entry',
                field: 'DocEntry',
                hide: true,
            },
            {
                headerName: 'Doc No',
                field: 'DocNum',
                hide: true,
            },
                 
            {
                headerName: 'Tax Date',
                field: 'TaxDate',
                hide: true,
            },
            {
                headerName: 'So Phieu',
                field: 'U_SoPhieu',
            },
            {
                headerName: 'Route',
                field: 'U_Route',
                hide: true,
            },
           
           
            {
                headerName: 'Quantity',
                field: 'Quantity',
            },

            {
                headerName: 'Truck Type',
                field: 'TruckType',
                hide: true,
            },
            {
                headerName: 'Capacity',
                field: 'Capacity',
                hide: true,
            },
            {
                headerName: 'Truck Driver',
                field: 'TruckDriver',
            },
            {
                headerName: 'Full Name',
                field: 'FullName',
            },
            {
                headerName: 'Phone',
                field: 'Phone',
            },
            {
                headerName: 'Truck Driver 1',
                field: 'TruckDriver1',
            },
            {
                headerName: 'Full Name 1',
                field: 'FullName1',
            },
            {
                headerName: 'Phone 1',
                field: 'Phone1',
            },
            {
                headerName: 'Delivery Status',
                field: 'U_DeliveryStatus',
                hide: true,
            },
            {
                headerName: 'Status',
                field: 'StatusIDName',
                hide: true,
            },

            {
                headerName: 'Truck Weight',
                field: 'TruckWeight',
            },
            {
                headerName: 'Truck Time',
                field: 'TruckTime',
            },

        ];


        let updatedData = [];
        const gridOptions = {
            columnDefs: columnDefs,
            pagination: true,
            defaultColDef: {
                flex: 1,
                minWidth: 150,
                filter: true,
                resizable: true,
            },
            groupDefaultExpanded: 1,
            rowGroupPanelShow: 'always',
            animateRows: true,
            pagination: true,
            rowSelection: 'multiple',
            rowGroupColDef: {
                headerName: 'Truck Info',
                field: 'U_TruckInfo',
                keyCreator: function(params) {
                    if (params.value == null) {
                        return '(Blanks)';
                    } else {
                        return params.value;
                    }
                },
            },
            checkboxSelection: true,
            headerCheckboxSelectionFilteredOnly: true,
            headerCheckboxSelection: function(params) {
                return (
                    params.columnApi
                    .getRowGroupColumns()
                    .some((c) => c.getId() === params.column.getId()) &&
                    params.nodes
                    .filter((n) => !n.group)
                    .some((n) => n.selectable)
                );
            },
            headerCheckboxSelectionParams: {
                suppressCount: true,
                selectAllOnMiniFilter: true,
            },
            onCellEditingStopped: function (event) {
                // Store updated data in the array
                const rowNode = event.node;
                const data = rowNode.data;
                if (updatedData.indexOf(data) === -1) {
                updatedData.push(data);
                }
            },
            enableRangeSelection: true,
            allowContextMenuWithControlKey: true,
            getContextMenuItems: getContextMenuItems,
            groupSelectsChildren: true,
            suppressRowClickSelection: true,
            onRowClicked: onSelectionChanged,
            cacheQuickFilter:true
        };
        function onFilterTextBoxChanged() {
        gridOptions.api.setQuickFilter(
            document.getElementById('filter-text-box').value
        );
        }
        function onBtExport() {
            gridOptions.api.exportDataAsExcel();
        }
        function onSelectionChanged(event) {
            const docEntry = event.data.DocNum;
            const Type = event.data.TypeName;
            loadData_Detail(docEntry,Type)
        //console.log(docEntry+Type);
        }
        function loadInitialData() {
            // Make an API call to abc.com to retrieve 100 records

            // Update the grid with the retrieved data
            gridOptions.api.setRowData({!!$listData!!});
            gridOptionssUB.api.setRowData([]);
        }
       
        function loadFilteredData() {

            $.ajax({
                type: 'GET',
                url: '{{ route('truck.get') }}',
                data: filterData,
                dataType: 'json',
                success: function(data) {
                    gridOptions.api.setRowData(data);
                    updatedData = [];
                    loadingModal.style.display = "none";

                }
            });

        }
        function getContextMenuItems(params) {
            var result = [
                // Other menu items...
                {
                name: 'Choose All DO',
                action: function () {
                    selectSameU_DelNo(params);
                },
                },
                'export',
            ];

            return result;
            }
            function selectSameU_DelNo() {
            const selectedRows = gridOptions.api.getSelectedRows();

            // If there is at least one row selected, retrieve the U_DelNo value
            if (selectedRows.length > 0) {
                const selectedDelNo = selectedRows[0]?.U_DelNo;

                gridOptions.api.forEachNode(function (node) {
                if (node.data && node.data.U_DelNo === selectedDelNo) {
                    node.setSelected(true);
                }
                });
            }
            }



        let filterData = {};
        // setup the grid after the page has finished loading
        document.addEventListener('DOMContentLoaded', function() {

            var gridDiv = document.querySelector('#myGrid');
            var gridDivSub = document.querySelector('#subgird');
            new agGrid.Grid(gridDiv, gridOptions);
            new agGrid.Grid(gridDivSub, gridOptionssUB);

            const filterButton = document.querySelector('#search');
            filterButton.addEventListener('click', function() {
               
              
                console.log("okay");
                const submitBtn = document.getElementById("search");
                const loadingModal = document.getElementById("loadingModal");
                // Get the filter values from the input fields
                const filterInput1 = document.querySelector('#fromDate');
                const filterInput2 = document.querySelector('#toDate');
                filterData.fromDate = filterInput1.value.split('/').reverse().join('');
                filterData.toDate = filterInput2.value.split('/').reverse().join('');
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
            const collectButton = document.querySelector('#rmdo');
            collectButton.addEventListener('click', () => {
                if (updatedData.length === 0) {
                    alert("No rows have been updated.");
                    return;
                } else {
                    console.log(updatedData);
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url:  '{{ route('logistic.removeDo') }}',
                    type: 'POST',
                    data: { dataNo:updatedData},
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        updatedData=[];
                        alert("save data suceess!");
                        loadFilteredData();
                    },
                    error: function(xhr, status, error) {
                        console.log("save failed");
                        alert("save data fail!");
                    }
                });
                }
                
            });
            loadInitialData();

            document.querySelector("#apply").addEventListener("click", function() {
                const selectedRows = gridOptions.api.getSelectedRows().filter(row => row.DocNum);
                const selectedProIds = selectedRows.map((row) =>  ({ DocNum: row.DocNum, DocType: row.TypeName }));
                const TruckCode = document.getElementById('truckcode').value;
                const loadingModal = document.getElementById("loadingModal");
                const submitBtn = document.getElementById("apply");

                if (selectedProIds.length === 0) {
                    alert("Please choose DocNum!")
                } else {
                    console.log(selectedProIds);
                    console.log(TruckCode);

                    loadingModal.style.display = "block";

                    // Disable the submit button
                    submitBtn.disabled = true;

                    $.ajax({
                        type: 'GET',
                        url: '{{ route('logistic.TruckApply') }}',
                        data: {
                            TruckCode: TruckCode,
                            No: selectedProIds
                        },
                        dataType: 'json',
                        success: function(data) {
                            alert("đã apply thành công!")
                            loadFilteredData();
                            submitBtn.disabled = false;
                        },
                        error: function() {
                            alert("đã apply thất bại!, vui lòng kiếm tra dữ liệu!");
                            loadingModal.style.display = "none";

                            // Enable the submit button
                            submitBtn.disabled = false;
                        }
                    })
                }

            })
             });
        
            document.querySelector("#print").addEventListener("click", function() {
                const selectedRows = gridOptions.api.getSelectedRows().filter(row => row.DocNum);
                const selectedProIds = selectedRows.map((row) => row.DocNum);
                const selectPramaDoc = selectedRows.map((row) => row.DocNum + '-' + row.TypeName);

                // Get distinct values of TruckInfor
                const truckInforSet = new Set(selectedRows.map((row) => row.U_TruckInfo).filter(Boolean));
                const truckInforArray = Array.from(truckInforSet);

                // Get distinct values of U_DelNo
                const uDelNoSet = new Set(selectedRows.map((row) => row.U_DelNo).filter(Boolean));
                const uDelNoArray = Array.from(uDelNoSet);

                // Check if any selected row has null or empty TruckInfor
                const hasNullTruckInfor = selectedRows.some(row => !row.U_TruckInfo);

                if (hasNullTruckInfor) {
                    alert('Please apply the truck code before printing!');
                    return;
                }

                if (selectedProIds.length === 0) {
                    alert("Please choose DocNum!");
                    return;
                }
                if (uDelNoArray.length > 1) {
                    alert("Cannot select more than 2 Do No!");
                    return;
                }
                // if (truckInforArray.length > 1) {
                //     alert("Cannot select more than 2 TruckCode!!");
                //     return;
                // }
                console.log("TruckInfor:" + truckInforArray);
                console.log("DelNo:" + uDelNoArray);
                console.log("Prama" + selectPramaDoc);
                const url = '{{ route('print-do') }}' + '?type=print' + '&pra=' + encodeURIComponent(selectPramaDoc);
                // redirect to the new URL
                window.open(url, '_blank');
            });

            document.querySelector("#stockout").addEventListener("click", function() {
                const selectedRows = gridOptions.api.getSelectedRows().filter(row => row.DocNum);
                const selectedProIds = selectedRows.map((row) => row.DocNum);
                const selectPramaDoc = selectedRows.map((row) => row.DocNum + '-' + row.TypeName);

                // Get distinct values of TruckInfor
                const truckInforSet = new Set(selectedRows.map((row) => row.U_TruckInfo).filter(Boolean));
                const truckInforArray = Array.from(truckInforSet);

                // Get distinct values of U_DelNo
                const uDelNoSet = new Set(selectedRows.map((row) => row.U_DelNo).filter(Boolean));
                const uDelNoArray = Array.from(uDelNoSet);

                // Check if any selected row has null or empty TruckInfor
                const hasNullTruckInfor = selectedRows.some(row => !row.U_TruckInfo);

                if (hasNullTruckInfor) {
                    alert('Please apply the truck code before printing!');
                    return;
                }

                // if (truckInforArray.length > 1) {
                //     alert("Cannot select more than 2 TruckCode!!");
                //     return;
                // }
                if (selectedProIds.length === 0) {
                    alert("Please choose DocNum!");
                    return;
                }

                if (uDelNoArray.length > 1) {
                    alert("Cannot select more than 2 DO No!");
                    return;
                }
                const loadingModal = document.getElementById("loadingModal");
                const submitBtn = document.getElementById("apply");
                console.log("TruckInfor:" + truckInforArray);
                console.log("DelNo:" + uDelNoArray);
                console.log("Prama" + selectPramaDoc);
                loadingModal.style.display = "block";

                // Disable the submit button
                submitBtn.disabled = true;

                $.ajax({
                    type: 'GET',
                    url: '{{ route('applyDo') }}',
                    data: {
                        delno: uDelNoArray,
                        Prama: selectPramaDoc,
                        No: selectedProIds,
                        type: "01"
                    },
                    dataType: 'json',
                    success: function(data) {
                        loadFilteredData();
                        const url = '{{ route('print-do') }}' + '?type=stockout' + '&pra=' +
                            encodeURIComponent(selectPramaDoc);

                            window.open(url, '_blank','noopener');
                    

                    },
                    error: function() {
                        alert("sorry, It happen error please contact administrator!");
                        loadingModal.style.display = "none";

                        // Enable the submit button
                        submitBtn.disabled = false;
                    }
                })

            });

            function selectGroupCallback(params) {
                const selectedValue = params.api.getValue('selected', params.node);

                // Get all the child nodes of the group node
                const childNodes = params.api.getChildrenOfNode(params.node);

                // Set the selected value for each child node
                childNodes.forEach((childNode) => {
                    params.api.setValue('selected', !selectedValue, childNode);
                });

                // Refresh the grid to reflect the changes
                params.api.refreshCells();
            }
    </script>
    <script>
         const columnDefsSub = [
                
            {
                headerName: "ItemCode",
                field: "ItemCode",
            },
            {
                headerName: 'ItemName',
                field: 'Dscription',
            },
            {
                headerName: 'Quantity',
                field: 'Quantity',
            },
            {
                headerName: 'Delivery Qty',
                field: 'DeliverQty',
            },
            {
                headerName: 'Weight',
                field: 'Weight',
            },
            {
                headerName: 'Uom',
                field: 'UomName',
            },
            {
                headerName: 'price before',
                field: 'PriceBefDi',
            },
            {
                headerName: 'price after',
                field: 'Price',
            },
            {
                headerName: 'Line Total',
                field: 'LineTotal',
            },
        ]
        const gridOptionssUB = {
            columnDefs: columnDefsSub,
            pagination: true,
            defaultColDef: {
                flex: 1,
                minWidth: 150,
                filter: true,
                resizable: true,
            },
           
            animateRows: true,
            pagination: true,
        };
        function loadData_Detail(DocEntry,Type)
        {
            $.ajax({
                type: 'GET',
                url: '{{ route('truck-detail') }}',
                data: {DocEntry:DocEntry,Type:Type},
                dataType: 'json',
                success: function(data) {
                     gridOptionssUB.api.setRowData(data);
                }
            });

        }
    </script>
@endpush
