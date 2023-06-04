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
    <h3>List Delivery Not Printed</h3>

    <div class="content">
        <form>

    </div>
    <!-- form gird -->
    <div id="MyGrid" class="ag-theme-alpine" style="height: 70%">
    </div>


    </form>
    <x-adminlte-button class="btn-flat" id="print" style="float: right;  margin-right: 20px;" type="button"
        label="Print" theme="success" />

    </div>

    <!-- Modal choose layout -->
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Choose layout:</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option1"
                                            value="ck">
                                        <label class="form-check-label" for="option1">
                                            Phieu Giao Hang - CK
                                        </label>
                                    </div>
                                    <!-- Add more radio button options here for the left column -->
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option2"
                                            value="aeon">
                                        <label class="form-check-label" for="option2">
                                            Phieu Giao Hang - AEON
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option3"
                                            value="aeonkm">
                                        <label class="form-check-label" for="option3">
                                            Phieu Giao Hang - AEON khuyen mai
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option4"
                                            value="vin">
                                        <label class="form-check-label" for="option4">
                                            Phieu Giao Hang - VIN
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option5"
                                            value="lotte">
                                        <label class="form-check-label" for="option5">
                                            Phieu Giao Hang - Lotte
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option6"
                                            value="lottekm">
                                        <label class="form-check-label" for="option6">
                                            Phieu Giao Hang - Lotte khuyen mai
                                        </label>
                                    </div>
                                    <!-- Add more radio button options here for the right column -->
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option7"
                                            value="metro">
                                        <label class="form-check-label" for="option7">
                                            Phieu Giao Hang - Metro
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option8"
                                            value="metrokm">
                                        <label class="form-check-label" for="option8">
                                            Phieu Giao Hang - Metro khuyen mai
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option9"
                                            value="betagen">
                                        <label class="form-check-label" for="option9">
                                            Phieu Giao Hang - Betagen
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="options" id="option10"
                                            value="betagenkm">
                                        <label class="form-check-label" for="option10">
                                            Phieu Giao Hang - Betagen khuyen mai
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submit-button">Submit</button>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <style>
        /* Optional CSS styles for the modal */
        .modal-content {
            max-width: 500px;
            margin: auto;
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
                headerName: "Cust.Group Name",
                field: "GroupName",
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
                headerName: 'InvoiceNo',
                field: 'NumAtCard',
                sort: 'desc',
                cellClass: 'InvoiceNos',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Total',
                field: 'DocTotal',
            },
            {
                headerName: 'Date',
                field: 'DocDate',
                cellClass: 'nguyen',
                valueFormatter: (params) => {
                    if (!params.value || isNaN(Date.parse(params.value))) {
                    return ''; // Return an empty string for empty or invalid date values
                    }
                    const date = new Date(params.value);
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear().toString().padStart(4, '0');
        return `${day}/${month}/${year}`;
                },
              
            },
            {
                headerName: 'SQ.No',
                field: 'U_SoPhieu',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'SO_No',
                field: 'U_SONo',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'RouteCode',
                field: 'RouteCode',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'RouteName',
                field: 'RouteName',
                filter: 'agTextColumnFilter',
            },

            {
                headerName: 'TruckInfo',
                field: 'U_TruckInfo',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'WhsCode',
                field: 'WhsCode',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'Whs Name',
                field: 'WhsName',
                filter: 'agTextColumnFilter',
            },
            {
                headerName: 'DocEntry',
                field: 'DocEntry',
                filter: 'agTextColumnFilter',
            },


        ];

            const excelstyle = [
              
                    {
                        id: 'InvoiceNos',
                        dataType: 'String',
                    },
                  {
                    id: 'nguyen',
                    dataType: 'Date',
                    numberFormat: {
                    format: 'dd/mm/yyyy',
                     },
                 },                   
                
            ];

        const gridOptions = {
            columnDefs: columnDefs,
            pagination: true,
            paginationPageSize: 200,
            defaultColDef: {
                flex: 1,
                minWidth: 150,
                filter: true,
                resizable: true,
                sortable: true,
                floatingFilter: true,
            },
            animateRows: true,
            pagination: true,
            rowSelection: 'multiple',
            checkboxSelection: true,
            headerCheckboxSelectionFilteredOnly: true,
            headerCheckboxSelectionParams: {
                suppressCount: true,
                selectAllOnMiniFilter: true,
            },
            excelStyles:excelstyle
           
        };

        function onBtExport() {
            gridOptions.api.exportDataAsExcel();
        }

        function loadInitialData() {
            // Make an API call to abc.com to retrieve 100 records

            // Update the grid with the retrieved data
            var gridDiv = document.querySelector('#myGrid');
            new agGrid.Grid(gridDiv, gridOptions);
            gridOptions.api.setRowData({!! $results !!});
        }
        function loadData_after_print()
        {
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
        loadInitialData();

        $(document).ready(function() {
            document.querySelector("#print").addEventListener("click", function() {
                const selectedRows = gridOptions.api.getSelectedRows().filter(row => row.DocEntry);
                const selectedProIds = selectedRows.map((row) => row.DocEntry);
                // Get distinct values of TruckInfor
            const groupset = new Set(selectedRows.map((row) => row.GroupName).filter(Boolean));
            const groupData = Array.from(groupset);


                if (selectedProIds.length === 0) {
                alert("Please choose DocNum!");
                return;
                }
                // Get the modal
                var modal = document.getElementById("myModal");

                // Get the "Submit" button in the modal
                var submitButton = modal.querySelector("#submit-button");

                // When the modal is shown, log a message to the console
                $(modal).on('shown.bs.modal', function() {
                    console.log('Modal shown');
                })

                // When the user clicks on submit, handle selected radio button and submit form data using Ajax
                submitButton.onclick = function() {
                    var selectedOption = document.querySelector('input[name="options"]:checked').value;
                    console.log(selectedOption);
                    console.log(selectedProIds);
                    const url = '{{ route('printed.layout') }}'+'?so='+selectedProIds+'&layout='+selectedOption+ '&group=' + encodeURIComponent(groupData);
                          // redirect to the new URL
                          window.open(url, '_blank');
                //     // Make an Ajax request here...
                //     $.ajax({
                //     type: 'GET',
                //     url: '{{ route('printed.do') }}',
                //     data: {
                //       layout:selectedOption, 
                //       No:selectedProIds,
                //       groupcode:groupData
                //     },
                //     dataType: 'json',
                //     success: function(data) {
                //         location.reload();
                //         const url = '{{ route('printed.layout') }}'+'?so='+selectedProIds+'&layout='+selectedOption+ '&group=' + encodeURIComponent(groupData);
                //           // redirect to the new URL
                //           window.open(url, '_blank');

                //     },
                //     error: function() {
                //         alert("sorry, It happen error please contact administrator!");
                //         loadingModal.style.display = "none";

                //         // Enable the submit button
                //         submitBtn.disabled = false;
                //     }
                // })

                    // Close the modal
                    $('#myModal').modal('hide');
                }

                // Open the modal
                $('#myModal').modal('show');
            });
        });
    </script>
@endpush
