@extends('adminlte::page')

@section('title', 'edit Stock Out Request - Sales Order')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@section('content')
@if(session()->has('message'))
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
        $config = ['format' => 'L', 'format' => 'yyyy/MM/DD'];
        $configsodate = ['autoclose' => true, 'format' => 'yyyy/MM/DD', 'immediateUpdates' => true, 'todayBtn' => true, 'todayHighlight' => true, 'setDate' => 0];
        
    @endphp
     <form action="{{ route('sales.update',$so->StockNo) }}" method="post">
        @csrf
    <!-- header input  -->
    <div class="row">
        <x-adminlte-select label="Order type" label-class="text-lightblue" igroup-size="sm" name="ordertype" id="ordertype"
            fgroup-class="col-md-3" enable-old-support>
            <option value="{{ $so->OrderType }}">{{ $so->Name }}</option>

        </x-adminlte-select>
        <x-adminlte-input label="PO ID" label-class="text-lightblue" name="pono" id="pono" type="text"
            placeholder="" igroup-size="sm" fgroup-class="col-md-2" value="{{ $so->PoCardCode }}" readonly="true">
        </x-adminlte-input>
        <x-adminlte-input-date name="podate" id="podate" label="PoDate" :config="$config" label-class="text-lightblue"
            igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date..." value="{{ $so->PODate }}" readonly="true">
            <x-slot name="appendSlot">
                <div class="input-group-text bg-gradient-danger">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </x-slot>
        </x-adminlte-input-date>
        <x-adminlte-input label="SO ID" label-class="text-lightblue" name="sono" type="text" placeholder="" value="{{$so->StockNo}}"
            igroup-size="sm" fgroup-class="col-md-2" readonly="true">
        </x-adminlte-input>
        <x-adminlte-select label="Support OrderNo" label-class="text-lightblue" igroup-size="sm" name="sporderno"
            id="sporderno" fgroup-class="col-md-2" enable-old-support>

        </x-adminlte-select>
    </div>
    <div class="row">
        <x-adminlte-select label="Customer Code" label-class="text-lightblue" igroup-size="sm" name="cuscode" id="cuscode"
            fgroup-class="col-md-2" enable-old-support>

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

        <x-adminlte-input-date name="date" id="sodate" label="Date" :config="$configsodate" label-class="text-lightblue"
            igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date..." value="{{ $so->StockDate }}" readonly="true">
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
    <div style="height: 450px; overflow: auto;" id="tabledata">
        <table >
            <thead>
                <tr>
                    <th>STT</th>
                    <th>ItemCode</th>
                    <th colspan="2">ItemName</th>
                    <th hidden>Type</th>
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
                        $consolidatedKey = $result['ItemCode'];
                        if (!isset($consolidatedData[$consolidatedKey])) {
                            $consolidatedData[$consolidatedKey] = [
                                'ItemCode' => $result['ItemCode'],
                                'ItemName' => $result['ItemName'],
                                'QuantityIn' => array_fill_keys($distinctLots, 0),
                                'QuantityOut' => array_fill_keys($distinctLots, 0),
                            ];
                        }
                        $consolidatedData[$consolidatedKey]['QuantityIn'][$result['LotNo']] += $result['QuantityIn'];
                        $consolidatedData[$consolidatedKey]['QuantityOut'][$result['LotNo']] += $result['QuantityOut'];
                        
                        $totalStockOuts[$result['LotNo']] += $result['QuantityOut'];
                    @endphp
                @endforeach

                @foreach ($consolidatedData as $key => $result)
                    <tr class="{{ $result['QuantityOut'] > 0 ? 'has-stockout' : '' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td class="ItemCode">{{ $result['ItemCode'] }}</td>
                        <td class="ItemName" colspan="2">{{ $result['ItemName'] }}</td>
                        <td hidden><input type="text" class="sotype" name="sotype[{{ $result['ItemCode'] }}][]"
                                value=""></td>
                        @foreach ($distinctLots as $lot)
                            <td class="{{ $result['QuantityOut'][$lot] > 0 ? 'orange' : '' }}">
                                @if ($result['QuantityIn'][$lot] > 0)
                                    <input type="number" class="Qtyout" style="text-color:orange"
                                        name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                        value="{{ $result['QuantityOut'][$lot] }}" max="{{ $result['QuantityIn'][$lot] }}"
                                        min="0">
                                @else
                                    <input type="number" class="Qtyout" style="text-color:orange"
                                        name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                        value="" readonly="true">
                                @endif
                            </td>
                            <td class="inlot">{{ $result['QuantityIn'][$lot] }}</td>
                        @endforeach

                        <td> <input type="number" class="totalrow" value="{{ array_sum($result['QuantityOut']) }}"
                                readonly="true"></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th ></th>
                    <th colspan="3">Total Quantity</th>
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


                    <th>{{ array_sum($totalStockOuts) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class=" table-responsive py-2">

        <div>

            <label for="note" style="margin-right: 30px; margin-top: 20px;"> Note:</label>
            <input type="text" id="note" name="note" value="{{$so->Note}}" style="width: 400px; height: 40px;">

        </div>
        <x-adminlte-button class="btn-flat" style="float: left; margin-left: 20px;margin-top: 10px;" id="save"
            type="submit" label="Save" theme="success" icon="fas fa-lg fa-save" />
            <x-adminlte-button class="btn-flat" id="promotion" style="float: right; margin-right: 20px;margin-top: 10px;"
            type="button" label="Get Promotion" theme="success" disabled />
        

    </div>

    <input type="text" name="custname" id="custname" value="{{$so->CustName}}" hidden>
    <input type="text" name="frmwhsname" id="frmwhsname" value="{{$so->FromWhsName}}" hidden>
    <input type="text" name="teams" id="teams" value="{{$so->BinCode}}" hidden>
</form>
@stop
@section('css')
    <style>
        .btn-flat {
            font-size: small;
            padding: 8px 24px;
            margin-top: 70px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td:first-child,
        th:first-child {
            text-align: left;
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
            width: 182.4px;
            }
    </style>
@stop
@push('js')
   
    <script>
        $(document).ready(function() {
            var status='{{$so->StatusSAP}}';
            var canceled='{{$so->Canceled}}';
           if(status!=="0")
           {
            $('#tabledata').css({'pointer-events':'none'});
            const saveButton = document.getElementById('save');
            saveButton.disabled = true;
           }
           if (canceled==="C")
           {
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
                    sono:document.getElementById("sono").value
                   
                },
                success: function(data) {
                    // Remove spinner icon
                    $('#search .spinner-grow').remove();
                    // Re-enable button
                    $('#search').prop('disabled', false);
                    document.getElementById("tabledata").innerHTML = data;
                    $('#promotion').removeAttr('disabled');
                    // Listen for changes to input fields in the rendered table
                    $('#tabledata input.Qtyout').on('input', function() {
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
                console.log('data: ', data);
                promotions = data;
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

                        newRow.find(".sotype").val('KM');
                        newRow.find(".totalrow").val(
                        newQty); // Update the "Total Qty" input field with the new quantity
                        newRow.find(".Qtyout").val("");

                        newRow.find(".Qtyout").removeClass('Qtyout').addClass('qtypro');
                        newRow.find(".totalrow").removeClass('totalrow').addClass(
                            'totalpro');
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
                        newRow.find(".ItemCode").text(itemCode);
                        newRow.find(".inlot").text("");
                        newRow.find(".sotype").val('KM');
                        newRow.find(".Qtyout").remove();
                        newRow.find(".Qtyout").val("");
                        newRow.find(".totalrow").val(promotionQty);

                        newRow.find(".totalrow").removeClass('totalrow').addClass(
                            'totalpro');

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
</script>
@endpush
