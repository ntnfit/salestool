@extends('adminlte::page')

@section('title', 'edit Inv request')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
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

     <form action="{{ route('inv.update',$so->StockNo) }}" method="post">
        @csrf
    <!-- header input  -->
    <div class="row">
        <x-adminlte-input label="Inv ID" label-class="text-lightblue" name="sono" type="text" placeholder="" value="{{$so->StockNo}}"
            igroup-size="sm" fgroup-class="col-md-2" readonly="true">
        </x-adminlte-input>
        <x-adminlte-select label="Warehouse" label-class="text-lightblue" igroup-size="sm" name="WhsCode" id="WhsCode"
        fgroup-class="col-md-2" enable-old-support>
        <option value="{{ $so->FromWhsCode }}" selected>{{ $so->FromWhsName}}</option>

    </x-adminlte-select>
    <x-adminlte-select label="Team" label-class="text-lightblue" igroup-size="sm" name="bincode" id="bincode"
        fgroup-class="col-md-2" enable-old-support>
        <option value="{{ $so->AbsEntry }}" selected>{{ $so->BinCode }}</option>
    </x-adminlte-select>
    <x-adminlte-select label="To Warehouse" label-class="text-lightblue" igroup-size="sm" name="toWhsCode" id="toWhsCode"
    fgroup-class="col-md-2" enable-old-support>
    <option value="{{ $so->ToWhsCode }}" selected>{{ $so->ToWhsName }}</option>

</x-adminlte-select>
<x-adminlte-select label="To Team" label-class="text-lightblue" igroup-size="sm" name="tobincode" id="tobincode"
    fgroup-class="col-md-2" enable-old-support>
    <option value="{{ $so->AbsEntry1 }}" selected>{{ $so->BinCode1 }}</option>
</x-adminlte-select>
    </div>
    <div class="row">
       
    @php
$config = ['format' => 'L', 'format' => 'DD/MM/yyy'];
$configsodate = ['autoclose' => true, 'format' => 'DD/MM/yyy', 'immediateUpdates' => true, 'todayBtn' => true, 'todayHighlight' => true, 'setDate' => 0];

@endphp
        <x-adminlte-input-date name="date" id="sodate" label="Date" :config="$configsodate" label-class="text-lightblue"
            igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date..." value="{{\Carbon\Carbon::parse($so->StockDate)->format('d/m/Y') }}" readonly="true">
            <x-slot name="appendSlot">
                <div class="input-group-text bg-gradient-danger">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </x-slot>
        </x-adminlte-input-date>
        <x-adminlte-input type="text"  label-class="text-lightblue" 
         label="Note" id="note"    igroup-size="sm" fgroup-class="col-md-3" name="note" value="{{$so->Note}}" >
        </x-adminlte-input>
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
                            
                            @if ($result['QuantityOut'][$lot] > 0 && $result['QuantityIn'][$lot] > 0)
                               
                                <input type="number" class="Qtyout" style="text-color:orange"
                                    name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                    value="{{ $result['QuantityOut'][$lot] }}" max="{{$result['QuantityOut'][$lot]+ $result['QuantityIn'][$lot]}}"
                                    min="0">
        
                            @elseif($result['QuantityIn'][$lot] > 0 && $result['QuantityOut'][$lot]== 0)
                                <input type="number" class="Qtyout" style="text-color:orange"
                                    name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]" max={{$result['QuantityIn'][$lot]}}
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

                        <td>  @if ( array_sum($result['QuantityOut']) > 0)
                           
                            <input type="number" class="totalrow" value="{{ array_sum($result['QuantityOut']) }}"
                                readonly="true"></td>
                                @else
                                <input type="number" class="totalrow" value=""
                                readonly="true"></td>
                                @endif
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


                    <th class="totalstockout">{{ array_sum($totalStockOuts) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class=" table-responsive py-2">

       
        <x-adminlte-button class="btn-flat" style="float: left; margin-left: 20px;margin-top: 10px;" id="save"
            type="submit" label="Save" theme="success" icon="fas fa-lg fa-save" />
        

    </div>
    <input type="text" name="frmwhsname" id="frmwhsname" value="{{$so->FromWhsName}}" hidden>
    <input type="text" name="teams" id="teams" value="{{$so->BinCode}}" hidden>
    <input type="text" name="towhsname" id="towhsname" value="{{$so->ToWhsName}}" hidden>
    <input type="text" name="toteams" id="toteams" value="{{$so->BinCode1}}" hidden>
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
            width: 80.4px;
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
// Select the date picker input field

</script>
<script>
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
