@extends('adminlte::page')

@section('title', 'Add Stock Out Request - Sales Order')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.select2', true)

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
        $config = ['format' => 'L', 'format' => 'yyyy/MM/DD'];
        $configsodate = ['autoclose' => true, 'format' => 'yyyy/MM/DD', 'immediateUpdates' => true, 'todayBtn' => true, 'todayHighlight' => true, 'setDate' => 0];
        
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
                id="ordertype" fgroup-class="col-md-3" enable-old-support>
                <option value=""></option>
                @foreach ($orderTypes as $orderType)
                    <option value="{{ $orderType->Code }}">{{ $orderType->Name }}</option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-input label="PO ID" label-class="text-lightblue" name="pono" id="pono" type="text"
                placeholder="" igroup-size="sm" fgroup-class="col-md-3">
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
                name="cuscode" id="cuscode" fgroup-class="col-md-2" enable-old-support>
                <option value=""></option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->CardCode }}">{{ $customer->CardCode . '--' . $customer->CardName.'--StoreId: ' . $customer->U_SID }}
                    </option>
                @endforeach
            </x-adminlte-select-bs>
            <x-adminlte-select-bs label="Warehouse" label-class="text-lightblue" :config="$configss" igroup-size="sm"
                name="WhsCode" id="WhsCode" fgroup-class="col-md-2" enable-old-support>
                <option value=""></option>
                @foreach ($whsCodes as $whsCode)
                    <option value="{{ $whsCode->WhsCode }}">{{ $whsCode->WhsCode . '--' . $whsCode->WhsName }}</option>
                @endforeach
            </x-adminlte-select-bs>
            <x-adminlte-select-bs label="Team" label-class="text-lightblue" :config="$configss" igroup-size="sm" name="bincode" id="bincode"
                fgroup-class="col-md-2" enable-old-support>
                <option value=""></option>
            </x-adminlte-select-bs>

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
                theme="success" icon="fas fa-filter" />

        </div>
        <div class="row">
            <div style="height: 600px; overflow: auto;" id="tabledata">

            </div>
            <div class=" table-responsive py-2">

                <div>

                    <label for="note" style="margin-right: 30px; margin-top: 100px;"> Note:</label>
                    <input type="text" id="note" name="note" style="width: 400px; height: 80px;">

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

        ,


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

tbody tr.matched {
  background-color: #f0f0f0;
}

    </style>
@stop
@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

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
                        sporderno:support
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
                                console.log("coll")
                                var cellValue = parseInt($(this).find('td:eq(' +
                                    columnIndex + ') input.Qtyout').val());
                                if (!isNaN(cellValue)) {
                                    sumcol += cellValue;
                                }
                            });
                            $('tfoot tr th').eq(columnIndex - 2).text(sumcol || 0);

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
                                alert('Quantity exceeds promotion quantity');
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
                                const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                                const age = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                                const city = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                                const match = name.indexOf(query) > -1 || age.indexOf(query) > -1 || city.indexOf(query) > -1;

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
                                newQty
                            ); // Update the "Total Qty" input field with the new quantity
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

    <script>
        $(document).ready(function() {
            $('#cuscode').change(function() {
                var CustName = document.getElementById("cuscode").selectedOptions[0].text.split("--")[1];
                document.getElementById("custname").value = CustName;
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

        const form = document.getElementById("addorder");
        const submitBtn = document.getElementById("save");
        const loadingModal = document.getElementById("loadingModal");

        form.addEventListener("submit", function(event) {
            // Prevent the form from submitting normally
            event.preventDefault();

            // Check if the "promotion" button is disabled
            const promotionBtn = document.getElementById("promotion");
            if (promotionBtn.disabled) {
                // If the button is disabled, simply validate and submit the form
                
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
                return;
            }

            // If the button is not disabled, prompt the user to confirm
            const confirmMsg = "Do you want to continue without the promotion?";
            if (confirm(confirmMsg)) {
                // If the user confirms, validate and submit the form
              
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
        $('#promotion').prop('disabled', false);
        
      } else {
        $('#search').prop('disabled', true);
        $('#promotion').prop('disabled', true);
        const ordertype = $('#ordertype').val();
        const custcode = $('#cuscode').val();
        if (custcode) {
          let type="";
          if(ordertype=="002")
          {
              type="01";//support
          }
          else if (ordertype=="003")
          {
              type="02";//Sampling
          }
          else
          {
              type="03"; //DA
          }
          console.log("loadata");
          $.ajax({
            url: '{{route('GetSupportOrder')}}',
            datatype:'json',
            method: 'get',
            async:false,
            data: { type: type, custcode: custcode },
            success: function(response) {
              console.log(response);
              const select = $('#sporderno');
              select.empty();
              select.append(`<option value=""></option>`);
              for (const option of response) {
                select.append(`<option value="${option.AbsID}">${option.AbsID}</option>`);
              }
              select.off('change').on('change', function() {
                if ($(this).val()) {
                  $('#search').prop('disabled', false);
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
          let type="";
          if(ordertype=="002")
          {
              type="01";//support
          }
          else if (ordertype=="003")
          {
              type="02";//Sampling
          }
          else
          {
              type="03"; //DA
          }
          // Make AJAX call here
          $.ajax({
            url: '{{route('GetSupportOrder')}}',
            datatype:'json',
            method: 'get',
            async:false,
            data: { type: type, custcode: custcode },
            success: function(response) {
              console.log(response);
              const select = $('#sporderno');
              select.empty();
              select.append(`<option value=""></option>`);
              for (const option of response) {
                select.append(`<option value="${option.AbsID}">${option.AbsID}</option>`);
              }
              select.off('change').on('change', function() {
                if ($(this).val()) {
                  $('#search').prop('disabled', false);
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
    $('#sodate').val(moment().format('YYYY/MM/DD')); // set the value of the input element to the current date
    $('#sodate').datetimepicker(); // initialize the datetimepicker
  });
</script>


@endpush
