@extends('adminlte::page')

@section('title', 'Add Stock Out Request - Sales Order')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)


<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>  
    
@section('content')

    @php
        $config = ['format' => 'L',
        'format' => 'yyyy/MM/DD'];
        $configsodate = [ 'autoclose'=> true,
        'format' => 'yyyy/MM/DD',
        'immediateUpdates'=> true,
        'todayBtn'=> true,
        'todayHighlight'=> true,
        'setDate'=>0]; 
       
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
    <form action="{{route('sales.store')}}"  method="post">
        @csrf
        <!-- header input  -->
        <div class="row">
            <x-adminlte-select label="Order type" label-class="text-lightblue" igroup-size="sm" name="ordertype"
                id="ordertype" fgroup-class="col-md-3" enable-old-support>
                <option value=""></option>
                @foreach ($orderTypes as $orderType)
                    <option value="{{ $orderType->Code }}">{{ $orderType->Name }}</option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-input label="PO ID" label-class="text-lightblue" name="pono" id="pono" type="text" placeholder=""
                igroup-size="sm" fgroup-class="col-md-3">
            </x-adminlte-input>
            <x-adminlte-input-date name="podate" id="podate" label="PoDate" :config="$config"
                label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-input label="SO ID" label-class="text-lightblue" name="sono" type="text" placeholder=""
                igroup-size="sm" fgroup-class="col-md-1" disabled>
            </x-adminlte-input>
            <x-adminlte-select label="Support OrderNo" label-class="text-lightblue" igroup-size="sm" name="sporderno"
                id="sporderno" fgroup-class="col-md-2" enable-old-support>

            </x-adminlte-select>
        </div>
        <div class="row">
            <x-adminlte-select label="Customer Code" label-class="text-lightblue" igroup-size="sm" name="cuscode"
                id="cuscode" fgroup-class="col-md-2" enable-old-support>
                <option value=""></option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->CardCode }}">{{ $customer->CardCode . '--' . $customer->CardName }}</option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select label="Warehouse" label-class="text-lightblue" igroup-size="sm" name="WhsCode" id="WhsCode"
                fgroup-class="col-md-2" enable-old-support>
                <option value=""></option>
                @foreach ($whsCodes as $whsCode)
                    <option value="{{ $whsCode->WhsCode }}">{{ $whsCode->WhsCode . '--' . $whsCode->WhsName }}</option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select label="Team" label-class="text-lightblue" igroup-size="sm" name="bincode" id="bincode"
                fgroup-class="col-md-2" enable-old-support>

            </x-adminlte-select>

            <x-adminlte-input-date name="date" id="sodate" label="Date" :config="$configsodate"
                label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date...">
                <x-slot name="appendSlot">
                    <div class="input-group-text bg-gradient-danger">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
            <x-adminlte-button class="btn" id="search"
                style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button" label="load item"
                theme="success" icon="fas fa-filter"/>
                
        </div>
        <div class="row">
            <div style="height: 600px; overflow: auto;" id="tabledata">

            </div>
            <div class=" table-responsive py-2">

                <div>

                    <label for="note" style="margin-right: 30px; margin-top: 100px;"> Note:</label>
                    <input type="text" id="note" name="note" style="width: 400px; height: 80px;">

                </div>
                <x-adminlte-button class="btn-flat" style="float: left; margin-left: 20px;" id="save" type="submit"
                    label="Save" theme="success" icon="fas fa-lg fa-save" />
                <x-adminlte-button class="btn-flat" id="promotion" style="float: right; margin-right: 20px;"
                    type="button" label="Get Promotion" theme="success" disabled />

            </div>

        </div>

        <input type="text" name="custname" id="custname"  value="" hidden>
        <input type="text" name="frmwhsname" id="frmwhsname" value="" hidden>
        <input type="text" name="teams" id="teams" value="" hidden>

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

    </style>
@stop
@push('js')
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
                        // Replace #bincode with the ID of the bincode select element
                        var bincodeSelect = $('#bincode');
                        bincodeSelect.empty(); // Clear any existing options
                        // Check if the data has a length property before looping through it
                        if (data.hasOwnProperty('length')) {
                            $.each(data, function(index, value) {
                                bincodeSelect.append($('<option>').text('').attr(
                                    'value', ''));
                                bincodeSelect.append($('<option>').text(value.BinCode)
                                    .attr('value', value.AbsEntry));
                            });
                        }
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
            if (!ordertype) {
                alert("Order Type is missing");
            } else if (!custcode) {
                alert("Customer Code is missing");
            } else if (!whscode) {
                alert("Warehouse Code is missing");
            } else if (!team) {
                alert("Team is missing");
            } else {
                $(this).prepend('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
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
                        Podate: Podate
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
                            var prototal=$row.find('.totalrow').val(sum);
                            
                           
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
                            var prototal=$row.find('.totalpro').val();
                            console.log(sum);
                            if(sum>prototal)
                            {
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
            
            var stockOutsInputs = document.querySelectorAll('input[name^="stockOuts"]','input[name^="sotype"]');
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

            var promotions = {};
            var custcodes=document.getElementById("cuscode").value;
            var whscodes=document.getElementById("WhsCode").value;
            var dates=document.getElementById("sodate").value;
            var whscodes=document.getElementById("WhsCode").value;
            var sodate=document.getElementById("sodate").value.replace("/", "")
            $.ajax({
            type: 'GET',
            url: "{{route('promotion.click')}}",
            data: {custcodes:custcodes,whscodes:whscodes,dates:sodate,itemlists:ItemList,itemlots:ItemLot},
            datatype:"json",
            success: function (data) {
                console.log('data: ', data);
                promotions=data;
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
                   
                    newRow.find(".sotype").val('KM'); 
                    newRow.find(".totalrow").val(newQty); // Update the "Total Qty" input field with the new quantity
                    newRow.find(".Qtyout").val("");
                   
                    newRow.find(".Qtyout").removeClass('Qtyout').addClass('qtypro');
                    newRow.find(".totalrow").removeClass('totalrow').addClass('totalpro');
                    newRow.find("input[name^='stockOuts']").attr("name", function(index, name) {
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
                    var lastRow = $("#tableadd tbody tr:last"); // Get the last row of the table
                    var secondLastRow = lastRow.prev(); // Get the second last row of the table
                    var newRow = secondLastRow.clone(true, true); // Clone the second last row
                    newRow.find(".ItemCode").text(itemCode);
                    newRow.find(".inlot").text("");
                    newRow.find(".sotype").val('KM');
                    newRow.find(".Qtyout").remove();
                    newRow.find(".Qtyout").val("");
                    newRow.find(".totalrow").val(promotionQty); 
                   
                    newRow.find(".totalrow").removeClass('totalrow').addClass('totalpro');
                    
                    newRow.find("input[name^='stockOuts']").attr("name", function(index, name) {
                        return name.replace(/^stockOuts/, "proout");
                    });

                    $("#tableadd tbody").append(newRow);
                }
            });
            // Refresh "STT" (serial number) and "Total Qty" in the table
            $("#tableadd tbody tr").each(function(index) {
                $(this).find("td:first-child").text(index + 1); // Update the "STT" (serial number)
            });
            $('#promotion').attr('disabled', 'disabled');
            },
            error: function(data) {
                console.log('data: ', data);
            }
            });
          
          
        });
    </script>
    <script>
         $(document).ready(function() {
            $('#cuscode').change(function() {
                var CustName=document.getElementById("cuscode").selectedOptions[0].text.split("--")[1];
                document.getElementById("custname").value = CustName;
            })
            $('#WhsCode').change(function() {
                var FromWhsName=document.getElementById("WhsCode").selectedOptions[0].text.split("--")[1];
              
                document.getElementById("frmwhsname").value = FromWhsName;
            })
            $('#bincode').change(function() {
                var BinCode=document.getElementById("bincode").selectedOptions[0].text;
                document.getElementById("teams").value = BinCode;
            })

        })  
       
    </script>
@endpush
