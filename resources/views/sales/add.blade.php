@extends('adminlte::page')

@section('title', 'Add Stock Out Request - Sales Order')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@section('plugins.select2', true)
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@section('content')
    @if (session()->has('message'))
        <div id="success-alert" class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        <script>
            setTimeout(function() {
                $('#success-alert').fadeOut('slow');
            }, 3000); // close after 3 seconds
        </script>
    @endif
    @php
        $config = ['format' => 'L', 'format' => 'DD/MM/yyy'];
        $configsodate = ['autoclose' => true, 'format' => 'DD/MM/yyy', 'immediateUpdates' => true, 'todayBtn' => true, 'todayHighlight' => true, 'setDate' => 0];

    @endphp
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h3>Add Stock Out Request - Sales Order</h3>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('sales.list') }}"> Back</a>
            </div>
        </div>
    </div>
    <form action="{{ route('sales.store') }}" method="post" id="addorder">
        @csrf
        <!-- header input  -->
        <div class="row">
            <x-adminlte-select label="Order type" label-class="text-lightblue" igroup-size="sm" name="ordertype"
                id="ordertype" fgroup-class="col-md-2" enable-old-support>
                <option value=""></option>
                @foreach ($orderTypes as $orderType)
                    <option value="{{ $orderType->Code }}">{{ $orderType->Name }}</option>
                @endforeach
            </x-adminlte-select>
            @php
                $configss = [
                    'title' => 'Select data',
                    'liveSearch' => true,
                    'liveSearchPlaceholder' => 'Search...',
                    'showTick' => true,
                    'actionsBox' => true,
                ];
            @endphp
            <x-adminlte-select-bs label="Customer Code" :config="$configss" label-class="text-lightblue" igroup-size="sm"
                name="cuscode" id="cuscode" fgroup-class="col-md-3 cuscode" enable-old-support>
                <option value=""></option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer['CardCode'] }}">
                        {{ 'StoreId: ' . $customer['U_SID'] . '--' . $customer['CardCode'] . '--' . $customer['CardName'] . '--' . $customer['GroupName'] . '--' . $customer['ChannelName'] . '--' . $customer['RouteName'] . '--' . $customer['LocationName'] }}
                    </option>
                @endforeach
            </x-adminlte-select-bs>
            <x-adminlte-select label="Support OrderNo" label-class="text-lightblue" igroup-size="sm" name="sporderno"
                id="sporderno" fgroup-class="col-md-2" enable-old-support>
            </x-adminlte-select>
            <x-adminlte-select-bs label="Warehouse" label-class="text-lightblue" :config="$configss" igroup-size="sm"
                name="WhsCode" id="WhsCode" fgroup-class="col-md-2" enable-old-support>
                <option value=""></option>
                @foreach ($whsCodes as $whsCode)
                    <option value="{{ $whsCode->WhsCode }}">{{ $whsCode->WhsCode . '--' . $whsCode->WhsName }}</option>
                @endforeach
            </x-adminlte-select-bs>
            <x-adminlte-select-bs label="Team" label-class="text-lightblue" :config="$configss" igroup-size="sm"
                name="bincode" id="bincode" fgroup-class="col-md-2" enable-old-support>
                <option value=""></option>
            </x-adminlte-select-bs>

        </div>
        <div class="row">
            <x-adminlte-input label="PO ID" label-class="text-lightblue" name="pono" id="pono" type="text"
                placeholder="" igroup-size="sm" fgroup-class="col-md-2">
            </x-adminlte-input>

            <div class="form-group col-md-2">
                <label for="podate" class="text-lightblue">PO Date:</label>
                <div class="input-group date" id="podate" data-target-input="nearest">
                    <input type="text" name="podate" class="form-control datetimepicker-input" data-target="#podate"
                        placeholder="Select Date" />
                    <div class="input-group-append" data-target="#podate" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                    </div>
                </div>
            </div>
           <div class="form-group col-md-2">
            <label for="sodate">SO Date:</label>
            <div class="input-group date" id="sodate" data-target-input="nearest">
                <input type="text" name="date" class="form-control datetimepicker-input" data-target="#sodate"
                    placeholder="Select Date" />
                <div class="input-group-append" data-target="#sodate" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                </div>
            </div>
        </div>


            <x-adminlte-button class="btn" id="search"
                style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button" label="load item"
                theme="success" icon="fas fa-filter" />


        </div>
        <input type="text" id="searchInput" placeholder="Search...">
        <div class="row">
            <div style="height:auto;max-height: 600px; overflow: auto;" id="tabledata">

            </div>
            <div class=" table-responsive py-2">

                <div>

                    <label for="note" style="margin-right: 30px; margin-top: 100px;"> Note:</label>
                    <input type="text" id="note" name="note">

                </div>
                <x-adminlte-button class="btn-flat" style="float: left; margin-left: 20px;" id="save"
                    type="submit" label="Save" theme="success" icon="fas fa-lg fa-save" />
                <x-adminlte-button class="btn-flat" id="promotion" style="float: right; margin-right: 20px;"
                    type="button" label="Get Promotion" theme="success" disabled />

            </div>

        </div>

        <input type="text" name="custname" id="custname" value="" hidden>
        <input type="text" name="frmwhsname" id="frmwhsname" value="" hidden>
        <input type="text" name="teams" id="teams" value="" hidden>

    </form>
    <div id="loadingModal" class="modal">
        <div class="modal-content">
            <div class="loader"></div>
            <p>Please wait...</p>
        </div>
    </div>
@stop


@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <style>
        .btn-flat {
            font-size: small;
            padding: 8px 24px;
            margin-top: 70px;
        }

        #note {
            width: 400px;
            height: 80px;
            padding: 12px 20px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid lightgrey;
            border-radius: 4px;
        }

        table#tableadd {
            border-collapse: collapse;
            max-width: 75%;
            zoom: 81%
        }

        thead {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #ddd;
        }

        table#tableadd th,
        table#tableadd td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table#tableadd th {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #ddd;
        }


        table#tableadd td:first-child,
        table#tableadd th:first-child {
            text-align: left;
        }

        table#tableadd td:first-child,
        table#tableadd td:nth-child(2),
        table#tableadd td:nth-child(3) {
            position: sticky;
            left: 0;

            background-color: #ddd;
            /* ensure that the fixed columns have the same background color as the table */
        }

        table#tableadd td:first-child {
            /* text-align: left; */
            width: 50px;
            min-width: 50px;
            max-width: 50px;
            left: 0px !important;
        }

        table#tableadd td:nth-child(2) {
            width: 100px;
            min-width: 100px;
            max-width: 100px;
            left: 50px;
        }

        table#tableadd td:nth-child(3) {
            width: 150px;
            min-width: 150px;
            max-width: 150px;
            left: 150px;
        }

        .orange {
            color: #3adf92;

        }

        button,
        input {
            /* background: coral; */
            overflow: visible;
            border: none;
            color: #3adf92;
            font-weight: bold;
        }

        .inlot {
            font-weight: bold;
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

        #searchInput {
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input.Qtyout {
            max-width: 60px;
        }



        input[type="number"] {
            width: 60.4px;
        }

        .dropdown-menu.show {
            max-width: 500px;
        }
    </style>
@stop
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script>
        $(document).ready(function() {
            $('#WhsCode').change(function() {
                var selectedWhsCode = $(this).val(); // Get the value of the selected WhsCode
                $.ajax({
                    url: '{{ route('bincode') }}', // Replace this with the actual route for the bincode API
                    type: 'GET',
                    dataType: "json",
                    data: {
                        WhsCode: selectedWhsCode
                    },
                    success: function(data) {
                        var select = $('#bincode');
                        select.empty();
                        console.log(data);
                        $.each(data, function(index, option) {
                            select.append($('<option>', {
                                value: option.AbsEntry,
                                text: option.BinCode
                            }));
                        });
                        // // Re-initialize the selectpicker
                        select.selectpicker('refresh');


                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Handle any errors here
                    }
                });
            });
        });
    </script>

    <script>
        $('#search').click(function() {
            var ordertype = document.getElementById("ordertype").value;
            var custcode = document.getElementById("cuscode").value;
            var whscode = document.getElementById("WhsCode").value;
            var team = document.getElementById("bincode").value;
            var sodate = document.getElementById("sodate").value;
            var Podate = document.getElementById("podate").value;
            var support = document.getElementById("sporderno").value;
            if (!ordertype) {
                alert("Order Type is missing");
            } else if (!custcode) {
                alert("Customer Code is missing");
            } else if (!whscode) {
                alert("Warehouse Code is missing");
            } else if (!team) {
                alert("Team is missing");
            } else {
                $(this).prepend(
                    '<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
                // Disable button
                $(this).prop('disabled', true);
                $('#tabledata').empty();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('filllot-items') }}',
                    data: {
                        ordertype: ordertype,
                        custcode: custcode,
                        whscode: whscode,
                        team: team,
                        sodate: sodate,
                        Podate: Podate,
                        sporderno: support
                    },
                    success: function(data) {
                        // Remove spinner icon
                        $('#search .spinner-grow').remove();
                        // Re-enable button
                        $('#search').prop('disabled', false);
                        document.getElementById("tabledata").innerHTML = data;
                        if (ordertype === "001") {
                            $('#promotion').prop('disabled', false);
                        } else {
                            $('#promotion').prop('disabled', true);
                        }

                        // Listen for changes to input fields in the rendered table
                        $('#tabledata').on('input', 'input.Qtyout', function() {
                            //clear promotion
                            var trs = document.querySelectorAll('tr');
                            trs.forEach(function(tr) {
                                var bgColor = tr.style.backgroundColor;
                                if (bgColor === 'rgb(223, 240, 216)') {
                                    tr.parentNode.removeChild(tr);
                                }
                            });
                            if (ordertype === "001") {
                                $('#promotion').prop('disabled', false);
                            } else {
                                $('#promotion').prop('disabled', true);
                            }
                            $("#tableadd tbody tr").each(function(index) {
                                $(this).find("td:first-child").text(index +
                                    1); // Update the "STT" (serial number)
                            });
                            //next process
                            var sum = 0;
                            var $row = $(this).closest('tr');
                            $row.find('input.Qtyout').each(function() {
                                var inputValue = parseInt($(this).val());
                                if (!isNaN(inputValue)) {
                                    sum += inputValue;
                                }
                            });
                            var prototal = $row.find('.totalrow').val(sum);
                            var sumcol = 0;
                            var columnIndex = $(this).parent().index();
                            $('#tabledata tr:not(:first):not(:last)').each(function() {

                                var cellValue = parseInt($(this).find('td:eq(' +
                                    columnIndex + ') input.Qtyout').val());
                                if (isNaN(cellValue)) {
                                    cellValue = 0; // Set value to 0 if NaN
                                }
                                sumcol += cellValue;
                            });
                            $('tfoot tr th').eq(columnIndex - 2).text(sumcol || 0);


                            let total = 0;
                            const totalRowElements = document.querySelectorAll(
                                'input.totalrow');
                            totalRowElements.forEach((element) => {
                                const value = parseFloat(element.value);
                                if (isNaN(value)) {
                                    total += 0; // Set value to 0 if NaN
                                } else {
                                    total += value;
                                }
                            });

                            document.querySelector('th.totalstockout').textContent = total;

                            if (ordertype !== "001") {
                                var sumpro = 0;
                                var $row = $(this).closest('tr');
                                $row.find('input.Qtyout').each(function() {
                                    var inputValue = parseInt($(this).val());
                                    if (!isNaN(inputValue)) {
                                        sumpro += inputValue;
                                    }
                                });
                                console.log(sumpro);
                                var prototal = parseInt($row.find('.openqtyrow').text(), 10);
                                console.log(sumpro);
                                if (sumpro > prototal) {
                                    alert('Quantity exceeds open quantity' + prototal);
                                    $row.find('input.Qtyout').val('');
                                }
                            }

                        });
                        $('#tabledata').on('input', 'input.qtypro', function() {
                            var sumpro = 0;
                            var $row = $(this).closest('tr');
                            $row.find('input.qtypro').each(function() {
                                var inputValue = parseInt($(this).val());
                                if (!isNaN(inputValue)) {
                                    sumpro += inputValue;
                                }
                            });
                            console.log(sumpro);
                            var prototal = $row.find('.totalpro').val();
                            console.log(sumpro);
                            if (sumpro > prototal) {
                                alert('Quantity exceeds promotion quantity:' + prototal);
                                $row.find('input.qtypro').val('');
                            }
                        });
                        $('#tableadd th').click(function() {
                            var table = $(this).parents('table').eq(0)
                            var tbody = table.find('tbody').eq(0)
                            var rows = tbody.find('tr').toArray().sort(comparer($(this)
                                .index()))
                            this.asc = !this.asc
                            if (!this.asc) {
                                rows = rows.reverse()
                            }
                            for (var i = 0; i < rows.length; i++) {
                                tbody.append(rows[i])
                            }
                        })

                        function comparer(index) {
                            return function(a, b) {
                                var valA = getCellValue(a, index),
                                    valB = getCellValue(b, index)
                                return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA
                                    .toString().localeCompare(valB)
                            }
                        };

                        function getCellValue(row, index) {
                            return $(row).children('td').eq(index).text()
                        };
                        const searchInput = document.getElementById('searchInput');
                        const rows = document.querySelectorAll('tbody tr');
                        searchInput.addEventListener('keyup', function(event) {
                            const query = event.target.value.toLowerCase();
                            rows.forEach(function(row) {
                                const name = row.querySelector('td:nth-child(1)')
                                    .textContent.toLowerCase();
                                const age = row.querySelector('td:nth-child(2)')
                                    .textContent.toLowerCase();
                                const city = row.querySelector('td:nth-child(3)')
                                    .textContent.toLowerCase();
                                const match = name.indexOf(query) > -1 || age.indexOf(
                                    query) > -1 || city.indexOf(query) > -1;
                                if (match) {
                                    row.style.display = '';
                                } else {
                                    row.style.display = 'none';
                                }
                            });
                        });
                    }
                })
            }
        })
    </script>
    <script>
        // Assume the "Load Promotion" button has an ID of "loadPromotionBtn"
        $("#promotion").on("click", function() {
            $(this).prepend(
                '<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
            // Disable button
            $(this).prop('disabled', true);
            var trs = document.querySelectorAll('tr');

            trs.forEach(function(tr) {
                var bgColor = tr.style.backgroundColor;
                if (bgColor === 'rgb(223, 240, 216)') {
                    tr.parentNode.removeChild(tr);
                }
            });

            var stockOutsInputs = document.querySelectorAll('input[name^="stockOuts"]', 'input[name^="sotype"]');
            var stockOutsValues = [];
            for (var i = 0; i < stockOutsInputs.length; i++) {
                var stockOutsInput = stockOutsInputs[i];
                var stockOutsName = stockOutsInput.getAttribute('name');
                var stockOutsValue = stockOutsInput.value;
                // Include only non-null and greater than zero values
                if (stockOutsValue !== null && parseFloat(stockOutsValue) > 0) {
                    // Extract dynamic parts from name attribute
                    var dynamicParts = stockOutsName.match(/\[(.*?)\]/g).map(function(part) {
                        return part.replace(/\[|\]/g, '');
                    });
                    // Rearrange dynamic parts and concatenate with value
                    var result = dynamicParts[0] + '-' + stockOutsValue + '-' + dynamicParts[1];
                    stockOutsValues.push(result);
                }
            }
            // Convert array to two separate strings
            var ItemLot = stockOutsValues.join(',');
            var ItemList = stockOutsValues.map(function(value) {
                return value.split('-').slice(0, 2).join('-');
            }).join(',');
            console.log(ItemLot);
            console.log(ItemList);
            var promotions = {};
            var custcodes = document.getElementById("cuscode").value;
            var whscodes = document.getElementById("WhsCode").value;
            var dates = document.getElementById("sodate").value;
            var whscodes = document.getElementById("WhsCode").value;
            var sodate = document.getElementById("sodate").value.replace(/\//g, '')
            $.ajax({
                type: 'GET',
                url: "{{ route('promotion.click') }}",
                data: {
                    custcodes: custcodes,
                    whscodes: whscodes,
                    dates: sodate,
                    itemlists: ItemList,
                    itemlots: ItemLot
                },

                datatype: "json",
                success: function(data) {
                    $('#promotion .spinner-grow').remove();
                    // Re-enable button
                    $('#promotion').prop('disabled', false);
                    //  console.log('data: ', data);
                    promotions = data.promotiodt;
                    if (Object.keys(promotions).length == 0) {
                        alert('promotion empty!')
                        $('#promotion').attr('disabled', 'disabled');
                    } else {
                        // Loop through each row in the table with ID "tableadd"
                        $("#tableadd tbody tr").each(function(index) {
                            var itemCode = $(this).find("td.ItemCode").text()
                                .trim(); // Get the value in the "ItemCode" column of the current row
                            // Check if the value in "ItemCode" column is found in the list of promotions
                            if (promotions.hasOwnProperty(itemCode)) {
                                var promotionQty = promotions[
                                    itemCode
                                ]; // Get the promotion quantity for the current item code
                                var newQty =
                                    promotionQty; // Calculate the new quantity by adding the promotion quantity
                                // Clone the current row, update the "Total Qty" input field with the new quantity, and append it to the table
                                var newRow = $(this).clone(true, true);
                                newRow.css('background-color', '#DFF0D8');
                                newRow.find(".sotype").val('KM');
                                newRow.find(".totalrow").val(
                                    newQty
                                ); // Update the "Total Qty" input field with the new quantity
                                newRow.find(".Qtyout").val("");
                                newRow.find(".Qtyout").removeClass('Qtyout').addClass('qtypro');
                                newRow.find(".totalrow").removeClass('totalrow').addClass(
                                    'totalpro').attr('name', 'totalprorow[]');
                                newRow.find("input[name^='stockOuts']").attr("name", function(
                                    index,
                                    name) {
                                    return name.replace(/^stockOuts/, "proout");
                                });
                                // Append the cloned row to the table
                                $(this).after(newRow);
                                // Remove the "STT" (serial number) for the cloned row
                                newRow.find("td:first-child").text("");
                            }
                        });
                        // Add new rows for items in the promotions list that are not in the table
                        $.each(promotions, function(itemCode, promotionQty) {
                            var itemname = data.ItemName;
                            var nameItem = itemname[itemCode];
                            var found = false;
                            $("#tableadd tbody tr").each(function() {
                                if ($(this).find(".ItemCode").text().trim() ==
                                    itemCode) {
                                    found = true;
                                    return false;
                                }
                            });
                            if (!found) {
                                var lastRow = $(
                                    "#tableadd tbody tr:last"); // Get the last row of the table
                                var secondLastRow = lastRow
                                    .prev(); // Get the second last row of the table
                                var newRow = secondLastRow.clone(true,
                                    true); // Clone the second last row
                                newRow.css('background-color', '#DFF0D8');
                                newRow.find(".ItemCode").text(itemCode);
                                newRow.find(".ItemName").text(nameItem);
                                newRow.find(".inlot").text("");
                                newRow.find(".sotype").val('KM');
                                newRow.find(".Qtyout").remove();
                                newRow.find(".Qtyout").val("");
                                newRow.find(".totalrow").val(promotionQty);
                                newRow.find(".totalrow").removeClass('totalrow').addClass(
                                    'totalpro').attr('name', 'totalprorow[]');
                                newRow.find("input[name^='stockOuts']").attr("name", function(
                                    index,
                                    name) {
                                    return name.replace(/^stockOuts/, "proout");
                                });
                                $("#tableadd tbody").append(newRow);
                            }
                        });
                        // Refresh "STT" (serial number) and "Total Qty" in the table
                        $("#tableadd tbody tr").each(function(index) {
                            $(this).find("td:first-child").text(index +
                                1); // Update the "STT" (serial number)
                        });
                        $('#promotion').attr('disabled', 'disabled');
                    }

                },
                error: function(data) {
                    $('#promotion .spinner-grow').remove();
                    // Re-enable button
                    $('#promotion').prop('disabled', false);
                    console.log('data: ', data);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#cuscode').change(function() {
                var CustName = document.getElementById("cuscode").selectedOptions[0].text.split("--")[2];
                var cuscode = document.getElementById("cuscode").selectedOptions[0].value;
                console.log();
                document.getElementById("custname").value = CustName;
                $.ajax({
                    type: 'GET',
                    url: '{{ route('getwhsdf') }}',
                    data: {
                        customer: cuscode
                    },
                    dataType: "json",
                    async: false,
                    success: function(data) {
                        var defaultWhs = data.DefaultWhs;
                        console.log(defaultWhs);
                        if (defaultWhs) {
                            // set the selected option of the select element
                            $('#WhsCode').selectpicker('val', defaultWhs);
                            getBinCodeOptions(defaultWhs)
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Error: " + error);
                    }
                });


            })
            $('#WhsCode').change(function() {
                var FromWhsName = document.getElementById("WhsCode").selectedOptions[0].text.split("--")[1];
                document.getElementById("frmwhsname").value = FromWhsName;
            })
            $('#bincode').change(function() {
                var BinCode = document.getElementById("bincode").selectedOptions[0].text;
                document.getElementById("teams").value = BinCode;
            })
        })

        function getBinCodeOptions(whscode) {
            $.ajax({
                url: '{{ route('bincode') }}', // Replace this with the actual route for the bincode API
                type: 'GET',
                dataType: "json",
                data: {
                    WhsCode: whscode
                },
                success: function(data) {
                    var select = $('#bincode');
                    select.empty();
                    console.log(data);
                    $.each(data, function(index, option) {
                        select.append($('<option>', {
                            value: option.AbsEntry,
                            text: option.BinCode
                        }));
                    });
                    // // Re-initialize the selectpicker
                    select.selectpicker('refresh');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle any errors here
                }
            });
        }

        function getproiteminput() {
            // get stockagain
            var stockOutsInputs = document.querySelectorAll('input[name^="proout"]');
            if (stockOutsInputs.length > 0) {
                var total = 0;
                for (var i = 0; i < stockOutsInputs.length; i++) {
                    var stockOutsInput = stockOutsInputs[i];
                    var stockOutsValue = stockOutsInput.value;
                    // If the value is null, set it to 0
                    if (stockOutsValue === null || stockOutsValue === '') {
                        stockOutsValue = 0;
                    }
                    // Sum the values
                    total += parseFloat(stockOutsValue);
                }
                return total;
            } else {
                return true
            }

        }

        function getprototal() {
            // get stockagain
            var stockOutsInputs = document.querySelectorAll('input[name^="totalprorow"]');
            if (stockOutsInputs.length > 0) {
                var total = 0;
                for (var i = 0; i < stockOutsInputs.length; i++) {
                    var stockOutsInput = stockOutsInputs[i];
                    var stockOutsValue = stockOutsInput.value;
                    // If the value is null, set it to 0
                    if (stockOutsValue === null || stockOutsValue === '') {
                        stockOutsValue = 0;
                    }
                    // Sum the values
                    total += parseFloat(stockOutsValue);
                }
                return total;
            } else {
                return true
            }

        }

        function getItemInput() {
            // get stockagain
            var stockOutsInputs = document.querySelectorAll('input[name^="stockOuts"]');
            if (stockOutsInputs.length > 0) {
                var stockOutsValues = [];
                for (var i = 0; i < stockOutsInputs.length; i++) {
                    var stockOutsInput = stockOutsInputs[i];
                    var stockOutsName = stockOutsInput.getAttribute('name');
                    var stockOutsValue = stockOutsInput.value;
                    // Include only non-null and greater than zero values
                    if (stockOutsValue !== null && parseFloat(stockOutsValue) > 0) {
                        // Extract dynamic parts from name attribute
                        var dynamicParts = stockOutsName.match(/\[(.*?)\]/g).map(function(part) {
                            return part.replace(/\[|\]/g, '');
                        });
                        // Rearrange dynamic parts and concatenate with value
                        var result = dynamicParts[0] + '-' + stockOutsValue + '-' + dynamicParts[1];
                        stockOutsValues.push(result);
                    }
                }
                // Convert array to two separate strings
                var ItemLot = stockOutsValues.join(',');
                if (ItemLot.length > 0) {
                    return true;
                } else {
                    return false
                }
            } else {
                return false
            }

        }

        const form = document.getElementById("addorder");

        const submitBtn = document.getElementById("save");
        const loadingModal = document.getElementById("loadingModal");
        form.addEventListener("submit", function(event) {
            // Prevent the form from submitting normally
            event.preventDefault();
            // Check if the "promotion" button is disabled
            const promotionBtn = document.getElementById("promotion");
            const ordertype = document.getElementById("ordertype").value;
            const itempro = getproiteminput();
            const totalpro = getprototal();
            const itemdata = getItemInput();
            const confirmMsg_promotion =
                "Would you like to proceed when the quantity promotion is not entered or the quantity promotion is less than the total quantity promotion?";
            if (promotionBtn.disabled) {
                // If the button is disabled, simply validate and submit the form

                if (!ValidatePOID()) {
                    alert("The POID has already in system, Please check again!")
                    return false; // Cancel the form submission if validation fails
                }
                if (itemdata == false) {
                    alert("You not input data item")
                    return false; // Cancel the form submission if validation fails
                } else {

                    if ((itempro === true && totalpro === true) || totalpro === itempro) {
                        // alert("You pass")
                        // console.log(itemdata);
                        // console.log(ordertype);
                        // Show the loading modal
                        loadingModal.style.display = "block";
                        submitBtn.disabled = true;
                        // Submit the form after a brief delay to allow the modal to show
                        setTimeout(function() {
                            form.submit();
                        }, 1000);
                    } else {
                        alert("Promotion not match or promotion not input!")
                        return false; // Ca
                        // Cancel the form submission if validation fails
                    }


                }
                // Show the loading modal
                loadingModal.style.display = "block";
                submitBtn.disabled = true;
                // Submit the form after a brief delay to allow the modal to show
                setTimeout(function() {
                    form.submit();
                }, 1000);
                return;
            }
            // If the button is not disabled, prompt the user to confirm
            const confirmMsg = "Do you want to continue without the promotion?";
            if (confirm(confirmMsg)) {
                // If the user confirms, validate and submit the form
                if (itemdata == false) {
                    alert("You not input data item")
                    return false; // Cancel the form submission if validation fails
                }
                if (!ValidatePOID()) {
                    alert("The POID has already in system, Please check again!")
                    return false; // Cancel the form submission if validation fails

                }
                // Show the loading modal
                loadingModal.style.display = "block";
                submitBtn.disabled = true;
                // Submit the form after a brief delay to allow the modal to show
                setTimeout(function() {
                    form.submit();
                }, 1000);
            } else {
                // If the user cancels, show an alert and enable the submit button

                submitBtn.disabled = false;
            }
        });

        function ValidatePOID() {
            const nameInput = document.getElementById("pono");
            if (nameInput.value.trim() !== "") {
                let isValid = true;
                $.ajax({
                    type: 'GET',
                    url: '{{ route('checkPOID') }}',
                    data: {
                        po: nameInput.value.trim()
                    },
                    async: false,
                    success: function(data) {
                        console.log(data);
                        if (data.data === 1) {
                            isValid = false;
                        }
                    },
                    error: function(data) {
                        isValid = false;
                        alert("Internal error: " + data);
                        return false; // Cancel submission
                    }
                });
                return isValid;
            } else {
                return true;
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $(function() {
                $('#ordertype').change(function() {
                    if ($(this).val() === '001') {
                        $('#sporderno').empty();
                        $('#search').prop('disabled', false);


                    } else {
                        $('#search').prop('disabled', true);

                        const ordertype = $('#ordertype').val();
                        const custcode = $('#cuscode').val();
                        if (custcode) {
                            let type = "";
                            if (ordertype == "002") {
                                type = "01"; //support
                            } else if (ordertype == "003") {
                                type = "02"; //Sampling
                            } else {
                                type = "03"; //DA
                            }
                            console.log("loadata");
                            $.ajax({
                                url: '{{ route('GetSupportOrder') }}',
                                datatype: 'json',
                                method: 'get',
                                async: false,
                                data: {
                                    type: type,
                                    custcode: custcode
                                },
                                success: function(response) {
                                    console.log(response);
                                    const select = $('#sporderno');
                                    select.empty();
                                    select.append(`<option value=""></option>`);
                                    for (const option of response) {
                                        select.append(
                                            `<option value="${option.AbsID}">${option.AbsID}</option>`
                                        );
                                    }
                                    select.off('change').on('change', function() {
                                        if ($(this).val()) {
                                            $('#search').prop('disabled',
                                                false);
                                        } else {
                                            $('#search').prop('disabled', true);
                                        }
                                    });
                                    $('#search').prop('disabled', true);
                                },
                                error: function(error) {
                                    // Handle error here
                                }
                            });
                        }
                    }
                });
            });

            $(function() {
                $('#cuscode').change(function() {
                    const ordertype = $('#ordertype').val();
                    const custcode = $('#cuscode').val();
                    if (ordertype && ordertype !== '001') {
                        if (custcode) {
                            let type = "";
                            if (ordertype == "002") {
                                type = "01"; //support
                            } else if (ordertype == "003") {
                                type = "02"; //Sampling
                            } else {
                                type = "03"; //DA
                            }
                            // Make AJAX call here
                            $.ajax({
                                url: '{{ route('GetSupportOrder') }}',
                                datatype: 'json',
                                method: 'get',
                                async: false,
                                data: {
                                    type: type,
                                    custcode: custcode
                                },
                                success: function(response) {
                                    console.log(response);
                                    const select = $('#sporderno');
                                    select.empty();
                                    select.append(`<option value=""></option>`);
                                    for (const option of response) {
                                        select.append(
                                            `<option value="${option.AbsID}">${option.AbsID}</option>`
                                        );
                                    }
                                    select.off('change').on('change', function() {
                                        if ($(this).val()) {
                                            $('#search').prop('disabled',
                                                false);
                                        } else {
                                            $('#search').prop('disabled', true);
                                        }
                                    });
                                    $('#search').prop('disabled', true);
                                },
                                error: function(error) {
                                    // Handle error here
                                }
                            });
                        } else {
                            alert('Please choose a customer code');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
                $('#sodate').datetimepicker({
                    format: 'DD/MM/YYYY',
                    useCurrent: false,
                    keepOpen: false,
                    defaultDate: new Date()

                });

                $('#sodate').on('click', function() {
                    $(this).datetimepicker('show');
                });

                $('#sodate').on('blur', function() {
                    $(this).datetimepicker('hide');
                });
                $('#podate').datetimepicker({
                    format: 'DD/MM/YYYY',
                    useCurrent: false,
                    keepOpen: false,


                });

                $('#podate').on('click', function() {
                    $(this).datetimepicker('show');
                });

                $('#podate').on('blur', function() {
                    $(this).datetimepicker('hide');
                });
            });

        document.onkeydown = function(e) {
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    var activeElement = document.activeElement;
                    var currentRow = activeElement.closest('tr');
                    var nextRow = currentRow.nextElementSibling;

                    if (nextRow && nextRow.tagName === 'TR') {
                        var currentInputIndex = Array.from(currentRow.querySelectorAll('.Qtyout, .qtypro')).indexOf(
                            activeElement);
                        var inputsInNextRow = nextRow.querySelectorAll('.Qtyout, .qtypro');
                        if (inputsInNextRow[currentInputIndex]) {
                            inputsInNextRow[currentInputIndex].focus();
                        }
                    }
                    break;

                case 'ArrowUp':
                    e.preventDefault();
                    var activeElement = document.activeElement;
                    var currentCell = activeElement.closest('td');
                    var currentRowIndex = Array.from(currentCell.parentNode.parentNode.children).indexOf(currentCell
                        .parentNode);
                    var currentColumnIndex = Array.from(currentCell.parentNode.children).indexOf(currentCell);
                    var rows = Array.from(currentCell.closest('table').querySelectorAll('tr'));

                    if (currentRowIndex > 0) {
                        var prevRow = rows[currentRowIndex];
                        var prevCell = prevRow.children[currentColumnIndex];
                        var inputInPrevCell = prevCell.querySelector('.Qtyout, .qtypro');
                        if (inputInPrevCell) {
                            inputInPrevCell.focus();
                        }
                    }
                    break;

                case 'ArrowRight':
                    e.preventDefault();
                    var activeElement = document.activeElement;
                    var currentRow = activeElement.closest('tr');
                    var inputsInCurrentRow = Array.from(currentRow.querySelectorAll('.Qtyout, .qtypro'));
                    var currentIndex = inputsInCurrentRow.indexOf(activeElement);

                    if (inputsInCurrentRow[currentIndex + 1]) {
                        inputsInCurrentRow[currentIndex + 1].focus();
                    }
                    break;

                case 'ArrowLeft':
                    e.preventDefault();
                    var activeElement = document.activeElement;
                    var currentRow = activeElement.closest('tr');
                    var inputsInCurrentRow = Array.from(currentRow.querySelectorAll('.Qtyout, .qtypro'));
                    var currentIndex = inputsInCurrentRow.indexOf(activeElement);

                    if (inputsInCurrentRow[currentIndex - 1]) {
                        inputsInCurrentRow[currentIndex - 1].focus();
                    }
                    break;
            }
        };
    </script>
@endpush
