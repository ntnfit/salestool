@extends('adminlte::page')

@section('title', 'Inventory Request')
@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.select2', true)
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
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
    @if ($errors->any())
    <div class="alert alert-danger" id="error-message">
        <ul>
            <li>{{ $errors->first() }}</li>
        </ul>
    </div>
    <script>
        setTimeout(function() {
            document.getElementById("error-message").remove();
        }, 4000);
    </script>
    @endif
    @php
      $configsodate = ['autoclose' => true, 'format' => 'DD/MM/yyy', 'immediateUpdates' => true, 'todayBtn' => true, 'todayHighlight' => true, 'setDate' => 0];
   
     $configss = [
         'title' => 'Select data',
         'liveSearch' => true,
         'liveSearchPlaceholder' => 'Search...',
         'showTick' => true,
         'actionsBox' => true,
     ];
 @endphp
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h3>Inventory request</h3>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('inv.list') }}"> Back</a>
            </div>
        </div>
    </div>
    <form action="{{ route('inv.store') }}" method="post" id="addorder">
        @csrf
        <!-- header input  -->
        <div class="row">
            <x-adminlte-select-bs label="From Warehouse" label-class="text-lightblue" :config="$configss" igroup-size="sm"
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
        <x-adminlte-select-bs label="To Warehouse" label-class="text-lightblue" :config="$configss" igroup-size="sm"
        name="toWhsCode" id="toWhsCode" fgroup-class="col-md-2" enable-old-support>
        <option value=""></option>
        @foreach ($whsCodes as $whsCode)
            <option value="{{ $whsCode->WhsCode }}">{{ $whsCode->WhsCode . '--' . $whsCode->WhsName }}</option>
        @endforeach
    </x-adminlte-select-bs>
    <x-adminlte-select-bs label="To Team" label-class="text-lightblue" :config="$configss" igroup-size="sm" name="tobincode" id="tobincode"
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
        </div>
        <div class="row">
            <x-adminlte-input name="note" id="note" label="Note" type="text"
            label-class="text-lightblue"  fgroup-class="col-md-3" placeholder="Note here...">
        </x-adminlte-input>
        <x-adminlte-button class="btn" id="search"
        style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button" label="load item"
        theme="success" icon="fas fa-filter" />
   
        </div>
        <input type="text" id="searchInput" placeholder="Search...">
        <div class="row">
            <div style="height: 600px; overflow: auto;" id="tabledata">

            </div>
            <div class=" table-responsive py-2">
                <x-adminlte-button class="btn-flat" style="float: left; margin-left: 20px; margin-top:10px" id="save"
                    type="submit" label="Save" theme="success" icon="fas fa-lg fa-save" />

            </div>

        </div>

        <input type="text" name="frmwhsname" id="frmwhsname" value="" hidden>
        <input type="text" name="teams" id="teams" value="" hidden>
        <input type="text" name="towhsname" id="towhsname" value="" hidden>
        <input type="text" name="toteams" id="toteams" value="" hidden>
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
       
        table#tableadd {
  border-collapse: collapse;
  max-width: 75%;
  
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
 
  background-color: #ddd; /* ensure that the fixed columns have the same background color as the table */
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


input.Qtyout {
    max-width: 60px;
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
            //
            $('#toWhsCode').change(function() {
                var selectedWhsCode = $(this).val(); // Get the value of the selected WhsCode
                $.ajax({
                    url: '{{ route('bincode') }}', // Replace this with the actual route for the bincode API
                    type: 'GET',
                    dataType: "json",
                    data: {
                        WhsCode: selectedWhsCode
                    },
                    success: function(data) {

                        var select = $('#tobincode');
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

            
            var whscode = document.getElementById("WhsCode").value;
            var team = document.getElementById("bincode").value;
            var ToWhsCode = document.getElementById("toWhsCode").value;
            var Toteam = document.getElementById("tobincode").value;
            if (!whscode) {
                alert("Warehouse Code is missing");
            } else if (!team) {
                alert("Team is missing");
            } else if (!ToWhsCode) {
                alert("to Warehouse is missing");
            } else if (!Toteam) {
                alert("to Team is missing");
            } else {
                $(this).prepend(
                    '<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
                // Disable button
                $(this).prop('disabled', true);
                $('#tabledata').empty();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('fill-inv') }}',
                    data: {
                        
                        whscode: whscode,
                        team: team
                       
                    },
                    success: function(data) {
                        // Remove spinner icon
                        $('#search .spinner-grow').remove();
                        // Re-enable button
                        $('#search').prop('disabled', false);
                        document.getElementById("tabledata").innerHTML = data;
                        
                        
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

                            // total stock out total
                            let total = 0;
                                const totalRowElements = document.querySelectorAll('input.totalrow');

                                totalRowElements.forEach((element) => {
                                total += parseFloat(element.value);
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
        $(document).ready(function() {
            $('#WhsCode').change(function() {
                var FromWhsName = document.getElementById("WhsCode").selectedOptions[0].text.split("--")[1];

                document.getElementById("frmwhsname").value = FromWhsName;
            })
            $('#bincode').change(function() {
                var BinCode = document.getElementById("bincode").selectedOptions[0].text;
                document.getElementById("teams").value = BinCode;
            })
            $('#toWhsCode').change(function() {
                var toWhsName = document.getElementById("toWhsCode").selectedOptions[0].text.split("--")[1];

                document.getElementById("towhsname").value = toWhsName;
            })
            $('#tobincode').change(function() {
                var BinCode1 = document.getElementById("tobincode").selectedOptions[0].text;
                document.getElementById("toteams").value = BinCode1;
            })
        });

        const form = document.getElementById("addorder");
        const submitBtn = document.getElementById("save");
        const loadingModal = document.getElementById("loadingModal");
       
        form.addEventListener("submit", function(event) {
            // Prevent the form from submitting normally
            event.preventDefault();
            loadingModal.style.display = "block";
                submitBtn.disabled = true;
                form.submit();
        })

    </script>
 
<script>
  $(document).ready(function() {
    $('#sodate').val(moment().format('DD/MM/YYYY')); // set the value of the input element to the current date
    $('#sodate').datetimepicker(); // initialize the datetimepicker
  });
  document.onkeydown = function (e) {
    switch (e.key) {
      case 'ArrowDown':
        e.preventDefault(); // Prevent the default behavior of the arrow key
        var activeElement = document.activeElement;
        var currentRow = activeElement.closest('tr');
        var nextRow = currentRow.nextElementSibling;
        
        if (nextRow && nextRow.tagName === 'TR') {
          var input = nextRow.querySelector('.Qtyout');
          if (input) {
            input.focus();
          }
        }
        break;
      case 'ArrowUp':
        e.preventDefault(); // Prevent the default behavior of the arrow key
        var activeElement = document.activeElement;
        var currentRow = activeElement.closest('tr');
        var prevRow = currentRow.previousElementSibling;
        
        if (prevRow && prevRow.tagName === 'TR') {
          var input = prevRow.querySelector('.Qtyout');
          if (input) {
            input.focus();
          }
        }
        break;
    }
  };
</script>


@endpush
