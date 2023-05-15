@extends('adminlte::page')

@section('title', 'SaleDetail')

<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>

@section('content')
    <div class="container shadow min-vh-50 py-5">
        <h5>Sales stock detail</h5>
        <div class="row justify-content-center">
            <div class="col-lg-3 col-sm-6">
                <label for="startDate">
                    <h6>From Date</h6>
                </label>
                <input id="startDate" class="form-control" type="date" name="fromdate" />
                <span id="startDateSelected"></span>
            </div>
            <div class="col-lg-3 col-sm-6">
                <label for="endDate">
                    <h6>To Date</h6>
                </label>
                <input id="endDate" class="form-control" type="date" name="todate" />
                <span id="endDateSelected"></span>
            </div>

        </div>
        <div class="row justify-content-center">
          <div class="col-sm-4">
            <label for="Warehouse">
                <h6>WhsCode</h6>
            </label>
            <select class="selectpicker" multiple data-live-search="true" id="whscode" name="whscode">
            </select>
        </div>
        <div class="col-sm-3">
            <label for="team">
                <h6>Team</h6>
            </label>
            <select class="selectpicker" multiple data-live-search="true" id="team" name="team">
            </select>
        </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6 col-sm-12">
                <button type="button" class="form-control btn btn-primary" id="export-excel"
                    onclick="getSelectValues()">Export Excel</button>
            </div>
        </div>
    </div>
    <div id="wrapper"></div>
    <div class="d-flex align-items-center invisible">
        <strong>Loading...</strong>
        <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
    </div>

@stop
@section('css')
    <style>
        #spinner-div {
            position: fixed;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 2;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css">
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script>

    <script>
    

        function getSelectValues() {
    var team = $('#team').val();
    var whscode = $('#whscode').val();
    let startDate = document.getElementById('startDate').value;
    let endDate = document.getElementById('endDate').value;

    if (whscode.length == 0) {
            alert("Please choose one Warehouse!");
    } else if (whscode.length == 1 && team.length == 0) {
            alert("Please choose one team!");
    } else if (startDate == "") {
        alert("Please choose from date!");
    } else if (endDate == "") {
        alert("Please choose to date!");
    } else {
      var whs = whscode.join(',');
      var bincode = '';
        $.ajax({
            type: 'GET',
            url: '{{ route('report.saledetail') }}',
            dataType: "json",
            data: {
                fromdate: startDate,
                todate: endDate,
                whscode: whs,
                bincode: team.join(',')
            },
            success: function(response) {
                filename = whs +'_saledetail.xlsx';
                data = response;
                var ws = XLSX.utils.json_to_sheet(data);
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "People");
                XLSX.writeFile(wb, filename);
            },
            error: function(response) {
                console.log(response);
            }
        });
    }
}

            $(document).ready(function() {
                //get whscode

                $.ajax({
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader("Authorization", "Basic " + '{{ env('BSHeader') }}');
                        xhr.withCredentials = true;
                    },
                    crossDomain: true,
                    url: " https://" + '{{ env('SAP_SERVER') }}' + ":" + '{{ env('SAP_PORT') }}' +
                        "/b1s/v1/Warehouses?$select=WarehouseCode,WarehouseName",
                    xhrFields: {
                        withCredentials: true,
                        rejectUnauthorized: false
                    },
                    // whether this is a POST or GET request
                    type: "get",
                    // the type of data we expect back
                    dataType: "json",
                    headers: {
                        "Prefer": "odata.maxpagesize=all",
                    },
                    // code to run if the request succeeds;
                    // the response is passed to the function
                    success: function(response) {

                        var toAppend = '';
                        $.each(response.value, function(i, o) {
                            toAppend += '<option value="' + o.WarehouseCode + '">' + o
                                .WarehouseCode + '-' + o.WarehouseName + '</option>';
                        });
                        $('#whscode').append(toAppend);
                        $('#whscode').selectpicker('refresh')
                        //   $('#sessions').append(toAppend);
                    },
                    error: function(xhr, status, errorThrown) {
                        console.log("Error: " + errorThrown);
                        console.log("Status: " + status);
                        console.dir(xhr);
                    }
                });
            });
            $('#whscode').on('change', function() {
                var whscode = $('#whscode').val();
                if (whscode.length == 1) {
                    var param = $("#whscode option:selected").val();

                    loadteamdata(param)
                } else {
                    $('#team').empty();
                    $('#team').selectpicker('refresh');

                }
            });

            function loadteamdata(param) {
                $.ajax({
                    url: '{{ route('bincode') }}', // Replace this with the actual route for the bincode API
                    type: 'GET',
                    dataType: "json",
                    data: {
                        WhsCode: param
                    },
                    success: function(response) {

                        var toAppend = '';
                        $.each(response, function(i, o) {
                            toAppend += '<option value="' + o.AbsEntry + '">' + o.BinCode + '</option>';
                        });
                        $('#team').append(toAppend);
                        $('#team').selectpicker('refresh')
                        //   $('#sessions').append(toAppend);
                    },
                    error: function(xhr, status, errorThrown) {

                    }
                });
            }
    </script>
    <!-- MDB -->
@endpush
