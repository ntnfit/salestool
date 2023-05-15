@extends('adminlte::page')

@section('title', 'edit Stock Out Request - Sales Order')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)

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
    <form action="{{ route('sales.update', $so->StockNo) }}" method="post" id="updateorder">
        @csrf
        <!-- header input  -->
        <div class="row">
            <x-adminlte-select label="Order type" label-class="text-lightblue" igroup-size="sm" name="ordertype"
                id="ordertype" fgroup-class="col-md-3" enable-old-support>
                <option value="{{ $so->OrderType }}">{{ $so->Name }}</option>

            </x-adminlte-select>
            <x-adminlte-input label="PO ID" label-class="text-lightblue" name="pono" id="pono" type="text"
                placeholder="" igroup-size="sm" fgroup-class="col-md-2" value="{{ $so->PoCardCode }}" readonly="true">
            </x-adminlte-input>
            <x-adminlte-input-date name="podate" id="podate" label="PoDate" :config="$config"
                label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date..."
                value="{{ \Carbon\Carbon::parse($so->PODate)->format('d/m/Y') }}" readonly="true">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-input label="SO ID" label-class="text-lightblue" name="sono" type="text" placeholder=""
                value="{{ $so->StockNo }}" igroup-size="sm" fgroup-class="col-md-2" readonly="true">
            </x-adminlte-input>
            <x-adminlte-select label="Support OrderNo" label-class="text-lightblue" igroup-size="sm" name="sporderno"
                id="sporderno" fgroup-class="col-md-2" enable-old-support>

            </x-adminlte-select>
        </div>
        <div class="row">
            <x-adminlte-select label="Customer Code" label-class="text-lightblue" igroup-size="sm" name="cuscode"
                id="cuscode" fgroup-class="col-md-2" enable-old-support>

                <option value="{{ $so->CustCode }}" selected>{{ $so->CustCode . '--' . $so->CustName }}</option>
            </x-adminlte-select>
            <x-adminlte-select label="Warehouse" label-class="text-lightblue" igroup-size="sm" name="WhsCode" id="WhsCode"
                fgroup-class="col-md-2" enable-old-support>
                <option value="{{ $so->FromWhsCode }}" selected>{{ $so->FromWhsCode }}</option>

            </x-adminlte-select>
            <x-adminlte-select label="Team" label-class="text-lightblue" igroup-size="sm" name="bincode" id="bincode"
                fgroup-class="col-md-2" enable-old-support>
                <option value="{{ $so->AbsEntry }}" selected>{{ $so->BinCode }}</option>
            </x-adminlte-select>

            <x-adminlte-input-date name="date" id="sodate" label="Date" :config="$configsodate"
                label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date..."
                value="{{ Carbon\Carbon::parse($so->StockDate)->format('d/m/Y') }}" readonly="true">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-button class="btn" id="search"
                style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button" label="load item"
                theme="success" icon="fas fa-filter" />

        </div>
        <input type="text" id="searchInput" placeholder="Search...">
        <div style="max-height:600px; overflow: auto;" id="tabledata">

            <table id="tableadd">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>ItemCode</th>
                        <th colspan="2">ItemName</th>
                        <th hidden>Type</th>
                        @if ($blanket != 0)
                            <th>PlanQty</th>
                            <th>CumQty</th>
                            <th>OpenQty</th>
                        @endif
                        @foreach ($distinctLots as $lot)
                            <th class="orange">Stock Out</th>
                            <th>LOT{{ $lot }}</th>
                        @endforeach
                        <th>Total Stock Out</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $consolidatedData = [];
                        $totalQuantityIns = array_fill_keys($distinctLots, 0);
                        $totalStockOuts = array_fill_keys($distinctLots, 0);
                    @endphp
                    @foreach ($results as $key => $result)
                        @php
                            $consolidatedKey = $result['ItemCode'] . '_' . $result['TypePrd'];
                            if (!isset($consolidatedData[$consolidatedKey])) {
                                $consolidatedData[$consolidatedKey] = [
                                    'ItemCode' => $result['ItemCode'],
                                    'ItemName' => $result['ItemName'],
                                    'TypePrd' => $result['TypePrd'],
                                    'PlanQty' => array_fill_keys($distinctLots, 0),
                                    'CumQty' => array_fill_keys($distinctLots, 0),
                                    'OpenQty' => array_fill_keys($distinctLots, 0),
                                    'QuantityIn' => array_fill_keys($distinctLots, 0),
                                    'QuantityOut' => array_fill_keys($distinctLots, 0),
                                ];
                            }
                            $consolidatedData[$consolidatedKey]['QuantityIn'][$result['LotNo']] += $result['QuantityIn'];
                            $consolidatedData[$consolidatedKey]['QuantityOut'][$result['LotNo']] += $result['QuantityOut'];
                            $consolidatedData[$consolidatedKey]['PlanQty'][$result['LotNo']] += $result['PlanQty'];
                            $consolidatedData[$consolidatedKey]['CumQty'][$result['LotNo']] += $result['CumQty'];
                            $consolidatedData[$consolidatedKey]['OpenQty'][$result['LotNo']] += $result['OpenQty'];
                            
                            $totalStockOuts[$result['LotNo']] += $result['QuantityOut'];
                        @endphp
                    @endforeach

                    @foreach ($consolidatedData as $key => $result)
                        <tr class="{{ $result['QuantityOut'] > 0 ? 'has-stockout' : '' }}"
                            @if ($result['TypePrd'] === '002') style="background-color: rgb(223, 240, 216)" @endif>
                            <td>{{ $loop->iteration }}</td>
                            <td class="ItemCode">{{ $result['ItemCode'] }}</td>
                            <td class="ItemName" colspan="2">{{ $result['ItemName'] }}</td>
                            <td hidden><input type="text" class="sotype" name="sotype[{{ $result['ItemCode'] }}][]"
                                    value=""></td>
                            @if ($blanket != 0)
                                <td style="text-color:orange;">
                                    {{ $result['PlanQty'][$lot] }}
                                </td>
                                <td>
                                    {{ $result['CumQty'][$lot] }}
                                </td>
                                <td>
                                    {{ $result['OpenQty'][$lot] }}
                                </td>
                            @endif
                            @foreach ($distinctLots as $lot)
                                <td class="{{ $result['QuantityOut'][$lot] > 0 ? 'orange' : '' }}">

                                    @if ($blanket != 0)
                                        @if ($result['QuantityIn'][$lot] > 0)
                                            <input type="number" class="Qtyout" style="text-color:orange"
                                                name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                                value="{{ $result['QuantityOut'][$lot] }}"
                                                max="{{ $result['OpenQty'][$lot] }}" min="0">
                                        @else
                                            <input type="number" class="Qtyout" style="text-color:orange"
                                                name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                                value="" readonly="true">
                                        @endif
                                    @elseif ($result['QuantityOut'][$lot] > 0 && $result['TypePrd'] === '001')
                                        @php
                                            $max = 0;
                                            if ($result['QuantityOut'][$lot] > $result['QuantityIn'][$lot] && $result['QuantityIn'][$lot] == 0) {
                                                $max = $result['QuantityOut'][$lot];
                                            } elseif ($result['QuantityOut'][$lot] > $result['QuantityIn'][$lot] && $result['QuantityIn'][$lot] != 0) {
                                                $max = $result['QuantityOut'][$lot] + $result['QuantityIn'][$lot];
                                            } elseif ($result['QuantityOut'][$lot] < $result['QuantityIn'][$lot] && $result['QuantityOut'][$lot] != 0) {
                                                $max = $result['QuantityOut'][$lot] + $result['QuantityIn'][$lot];
                                            } else {
                                                $max = $result['QuantityOut'][$lot];
                                            }
                                        @endphp
                                        <input type="number" class="Qtyout" style="text-color:orange"
                                            name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                            value="{{ $result['QuantityOut'][$lot] }}" max="{{ $max }}"
                                            min="0">
                                    @elseif ($result['QuantityOut'][$lot] > 0 && $result['TypePrd'] == '002')
                                        <input type="number" class="qtypro" style="text-color:orange"
                                            name="proout[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                            value="{{ $result['QuantityOut'][$lot] }}"
                                            max="{{ $result['QuantityOut'][$lot] }}" min="0">
                                    @elseif($result['QuantityIn'][$lot] > 0)
                                        <input type="number" class="Qtyout" style="text-color:orange"
                                            name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                            value="">
                                    @else
                                        <input type="number" class="Qtyout" style="text-color:orange"
                                            name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                            value="" readonly="true">
                                    @endif


                                </td>
                                @if ($result['QuantityIn'][$lot] > 0)
                                    <td class="inlot">{{ $result['QuantityIn'][$lot] }}</td>
                                @else
                                    <td class="inlot"></td>
                                @endif
                            @endforeach

                            <td>
                                @if ($result['TypePrd'] === '001' && array_sum($result['QuantityOut']) > 0)
                                    <input type="number" class="totalrow"
                                        value="{{ array_sum($result['QuantityOut']) }}" readonly="true">
                                @elseif($result['TypePrd'] === '002' && array_sum($result['QuantityOut']) > 0)
                                    <input type="number" name="totalprorow[]" class="totalpro"
                                        value="{{ array_sum($result['QuantityOut']) }}" readonly="true">
                                @else
                                    <input type="number" class="totalrow" value="" readonly="true">
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th colspan="3">Total Quantity</th>
                        @if ($blanket != 0)
                            <th></th>
                            <th></th>
                            <th></th>
                        @endif
                        @foreach ($distinctLots as $lot)
                            @php
                                $totalQuantity = 0;
                                $totalOut = 0;
                                foreach ($results as $result) {
                                    if ($result['LotNo'] == $lot) {
                                        $totalOut += $result['QuantityOut'];
                                        $totalQuantity += $result['QuantityIn'];
                                    }
                                }
                            @endphp
                            <th>{{ $totalOut }}</th>
                            <th>{{ $totalQuantity }}</th>
                        @endforeach


                        <th class="totalstockout">{{ array_sum($totalStockOuts) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class=" table-responsive py-2">

            <div>

                <label for="note" style="margin-right: 30px; margin-top: 20px;"> Note:</label>
                <input type="text" id="note" name="note" value="{{ $so->Note }}"
                    style="width: 400px; height: 40px;">

            </div>
            <x-adminlte-button class="btn-flat" style="float: left; margin-left: 20px;margin-top: 10px;" id="save"
                type="submit" label="Save" theme="success" icon="fas fa-lg fa-save" />
            @if ($so->OrderType == '001')
                <x-adminlte-button class="btn-flat" id="promotion"
                    style="float: right; margin-right: 20px;margin-top: 10px;" type="button" label="Get Promotion"
                    theme="success" />
            @else
                <x-adminlte-button class="btn-flat" id="promotion"
                    style="float: right; margin-right: 20px;margin-top: 10px;" type="button" label="Get Promotion"
                    theme="success" disabled />
            @endif

        </div>

        <input type="text" name="custname" id="custname" value="{{ $so->CustName }}" hidden>
        <input type="text" name="frmwhsname" id="frmwhsname" value="{{ $so->FromWhsName }}" hidden>
        <input type="text" name="teams" id="teams" value="{{ $so->BinCode }}" hidden>
    </form>
    <div id="loadingModal" class="modal">
        <div class="modal-content">
            <div class="loader"></div>
            <p>Please wait...</p>
        </div>
    </div>
@stop
@section('css')
    <style>
        .btn-flat {
            font-size: small;
            padding: 8px 24px;
            margin-top: 70px;
        }

        table#tableadd {
            border-collapse: collapse;
            max-width: 75%;
            zoom: 81%
        }

        thead#tableadd {
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

        table#tableadd tr:nth-child(even) {
            background-color: #f2f2f2;
            max-width: 35%;
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
            color: orange;
        }

        button,
        input {
            /* background: coral; */
            overflow: visible;
            border: none;
            color: orange;
        }

        input[type="number"] {
            width: 60.4px;
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
    <script>
        $(document).ready(function() {
            var status = '{{ $so->StatusSAP }}';
            var canceled = '{{ $so->Canceled }}';
            if (status !== "0") {
                $('#tabledata').css({
                    'pointer-events': 'none'
                });
                const saveButton = document.getElementById('save');
                saveButton.disabled = true;
            }
            if (canceled === "C") {
                const saveButton = document.getElementById('save');
                saveButton.disabled = true;
            }


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
                        sono: document.getElementById("sono").value

                    },
                    success: function(data) {
                        // Remove spinner icon
                        $('#search .spinner-grow').remove();
                        // Re-enable button
                        $('#search').prop('disabled', false);
                        document.getElementById("tabledata").innerHTML = data;
                        $('#promotion').removeAttr('disabled');
                        // Listen for changes to input fields in the rendered table
                        $('#tabledata input').off('input');
                        $('#tabledata input.Qtyout').on('input', function() {
                            trs.forEach(function(tr) {
                                var bgColor = tr.style.backgroundColor;
                                if (bgColor === 'rgb(223, 240, 216)') {
                                    tr.parentNode.removeChild(tr);
                                }
                            });
                            $('#promotion').removeAttr('disabled');
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
                                if (!isNaN(cellValue)) {
                                    sumcol += cellValue;
                                }
                            });
                            $('tfoot tr th').eq(columnIndex - 2).text(sumcol || 0);
                        });
                        $('#tabledata input.qtypro').on('change', function() {
                            console.log("okay");
                            var sum = 0;
                            var $row = $(this).closest('tr');
                            $row.find('input.qtypro').each(function() {
                                var inputValue = parseInt($(this).val());
                                if (!isNaN(inputValue)) {
                                    sum += inputValue;
                                }
                            });
                            console.log(sum);
                            var prototal = $row.find('.totalpro').val();
                            console.log(sum);
                            if (sum > prototal) {
                                alert('Quantity exceeds promotion quantity')
                            }
                        });
                    }
                })
            }
        })
    </script>
    <script>
        // Assume the "Load Promotion" button has an ID of "loadPromotionBtn"
        $("#promotion").on("click", function() {
            $(this).prepend('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
            // Disable button
            $(this).prop('disabled', true);
            var trs = document.querySelectorAll('tr');
            // clear promotion
            trs.forEach(function(tr) {
                var bgColor = tr.style.backgroundColor;
                if (bgColor === 'rgb(223, 240, 216)') {
                    tr.parentNode.removeChild(tr);
                }
            });
            // get stockagain
            var stockOutsInputs = document.querySelectorAll('input[name^="stockOuts"], input[name^="sotype"]');
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
            var sodate = document.getElementById("sodate").value.replace(/\//g, '');
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
                    console.log('data: ', data);
                    promotions = data.promotiodt;
                    // Loop through each row in the table with ID "tableadd"
                    $("#tableadd tbody tr").each(function(index) {
                        var itemCode = $(this).find("td.ItemCode").text()
                    .trim(); // Get the value in the "ItemCode" column of the current row
                        // Check if the value in "ItemCode" column is found in the list of promotions
                        if (promotions.hasOwnProperty(itemCode)) {
                            var promotionQty = promotions[
                            itemCode]; // Get the promotion quantity for the current item code
                            var newQty =
                            promotionQty; // Calculate the new quantity by adding the promotion quantity
                            // Clone the current row, update the "Total Qty" input field with the new quantity, and append it to the table
                            var newRow = $(this).clone(true, true);
                            newRow.css('background-color', '#DFF0D8');
                            newRow.find(".sotype").val('KM');
                            newRow.find(".totalrow").val(
                            newQty); // Update the "Total Qty" input field with the new quantity
                            newRow.find(".Qtyout").val("");
                            newRow.find(".Qtyout").removeClass('Qtyout').addClass('qtypro');
                            newRow.find(".totalrow").removeClass('totalrow').addClass(
                                'totalpro').attr('name', 'totalprorow[]');
                            newRow.find("input[name^='stockOuts']").attr("name", function(index,
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
                            if ($(this).find(".ItemCode").text().trim() == itemCode) {
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
                            newRow.find(".qtypro").val("");
                            newRow.find(".totalrow").val(promotionQty);
                            newRow.find(".totalrow").removeClass('totalrow').addClass(
                                'totalpro').attr('name', 'totalprorow[]');
                            newRow.find("input[name^='stockOuts']").attr("name", function(index,
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
                },
                error: function(data) {
                    console.log('data: ', data);
                }
            });
        });
        $('#tabledata input').off('input');
        $('#tabledata').on('input', 'input.Qtyout', function() {
            //CLEAR PROMOTION
            var trs = document.querySelectorAll('tr');
            // clear promotion
            trs.forEach(function(tr) {
                var bgColor = tr.style.backgroundColor;
                if (bgColor === 'rgb(223, 240, 216)') {
                    tr.parentNode.removeChild(tr);
                }
            });
            $('#promotion').prop('disabled', false);
            //NEXT PROCESS
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
            var prototal = $row.find('input.totalpro').val();
            console.log(sumpro);
            if (sumpro > prototal) {
                alert('Quantity exceeds promotion quantity');
                $row.find('input.qtypro').val('');
            }
        });
    </script>
    <script>
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

        const form = document.getElementById("updateorder");

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
            const confirmMsg_promotion = "Would you like to proceed when the quantity promotion is not entered or the quantity promotion is less than the total quantity promotion?";
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

                    if ((itempro === true && totalpro===true )|| totalpro===itempro ) {
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
@endpush
