@extends('adminlte::page')

@section('title', 'Edit promotions')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.select2', true)
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@28.2.1/dist/ag-grid-community.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@28.2.1/dist/ag-grid-enterprise.min.js"></script>
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @php
$config = [
    "timePicker" => true,
    "startDate" =>date('d/m/Y', strtotime($header[0]['FromDate'])) ,
    "endDate" => date('d/m/Y', strtotime($header[0]['ToDate'])),
    "locale" => ["format" => "DD/MM/yyyy"],
];
@endphp
@section('content')

    <div class="content">
        <form action="{{ route('pro.update', ['proid' => $header[0]['ProID']]) }}" method="post">
            @csrf
            <!-- header input  -->
            <div class="row">
                <x-adminlte-select label="Type Promotion" label-class="text-lightblue" name="protype" id="protype"
                    fgroup-class="col-md-3" enable-old-support readonly="true">
                    <option value="5">Date promotion</option>
                </x-adminlte-select>
                <div class="row col-md-9" style="float:right">
                    <x-adminlte-input label="ID" label-class="text-lightblue" name="ID" type="text" value="{{$header[0]['ProID']}}"
                        placeholder="" fgroup-class="col-md-3" readonly="true">
                    </x-adminlte-input>
                    <x-adminlte-input label="Promotion name" label-class="text-lightblue" name="promotionname" value="{{$header[0]['ProName']}}"
                        type="text" fgroup-class="col-md-6" enable-old-support>
                    </x-adminlte-input>
        
                    @if($header[0]['Rouding']==1) 
                    <x-adminlte-input name="rouding" style="margin-left: 1.5rem;" label="Rounding" type="checkbox"
                        label-class="text-lightblue" value="rouding" fgroup-class="col-xs-3 rouding" enable-old-support checked>
                    </x-adminlte-input>
                    @else
                    <x-adminlte-input name="rouding" style="margin-left: 1.5rem;" label="Rounding" type="checkbox"
                    label-class="text-lightblue" value="rouding" fgroup-class="col-xs-3 rouding" enable-old-support>
                </x-adminlte-input>
                     @endif
                     @if($header[0]['FixCust']==1)
                    <x-adminlte-input name="fixcus" label="Fix customer" type="checkbox" label-class="text-lightblue"
                        value="fixcus" fgroup-class="col-xs-3" enable-old-support  checked>
                    </x-adminlte-input>
                    @else
                    <x-adminlte-input name="fixcus" label="Fix customer" type="checkbox" label-class="text-lightblue"
                        value="fixcus" fgroup-class="col-xs-3" enable-old-support >
                    </x-adminlte-input>
                    @endif
                </div>
            </div>
            <div class="row">
                <x-adminlte-date-range name="period" label="Period" :config="$config" label-class="text-lightblue" fgroup-class="col-md-3" />
               
            </div>
            <!-- row input -->
            <div class="tab">
                <button class="tablinks" type="button" onclick="openTab(event, 'tab-1')" id="defaultOpen">Item</button>
                <button class="tablinks" type="button" onclick="openTab(event, 'tab-2')">Customer</button>
            </div>
            <div id="tab-1" class="tabcontent">
                @php
                    $configItem = [
                        'title' => 'Select ItemCode - ItemName',
                        'liveSearch' => true,
                        'liveSearchPlaceholder' => 'Search...',
                        'showTick' => true,
                        'actionsBox' => true,
                    ];
                @endphp
                <div class="tableFixHead">
                    <table class="table table-bordered" id="myTable">
                        <thead>
                            <tr>
                                <th class="header-label">ItemCode</th>
                                <th class="header-label">Quantity</th>
                                <th class="header-label">UoM Codde</th>
                                <th class="header-label batch">Batch No.</th>
                                <th class="header-label">Base Quantity</th>
                                <th class="header-label">Base UoM Code</th>
                                <th class="header-label Itemselectdate">ItemPro</th>
                                <th class="header-label proqtydate">Quantity</th>                          
                                <th class="header-label probatchdate">Batch No.</th>
                                <th class="header-label">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($listItems as $listItem)
                            <tr class="tr_clone_itemlist">
                                <td class="Itemselect">
                                    <select class="itemlist" id="Item" name="Item[]" data-placeholder="Select an itemcode">
                                        <option value="" selected></option>
                                        @foreach ($ItemCodes as $ItemCode)
                                          @if($listItem['ItemCode']== $ItemCode->ItemCode)
                                            <option value="{{ $ItemCode->ItemCode }}" selected>
                                                {{ $ItemCode->ItemCode . '--' . $ItemCode->ItemName }}</option>
                                            @else
                                            <option value="{{ $ItemCode->ItemCode }}" >
                                                {{ $ItemCode->ItemCode . '--' . $ItemCode->ItemName }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="Qty[]" class="qty form-control" value="{{number_format($listItem['InputQty'], 0, '', '')}}" step="0.01"></td>
                                <td><select class="uom form-control" name="UomCode[]" >
                                        <option value="" selected></option>
                                        @foreach ($Uoms as $Uom)
                                        @if($listItem['InputUoMCode']== $Uom->UomEntry)
                                            <option value="{{ $Uom->UomEntry }}" selected>{{ $Uom->UomName }}</option>
                                        @else
                                            <option value="{{ $Uom->UomEntry }}">{{ $Uom->UomName }}</option>
                                        @endif
                                        @endforeach
                                    </select></td>
                                <td class="batch"><input type="text" name="Batch[]" value="{{$listItem['BatchNo']}}" class="form-control batch"></td>
                                <td><input type="number" name="BaseQty[]" class="form-control" readonly="true" value="{{number_format($listItem['Quantity'], 0, '', '')}}"></td>
                                <td>
                                    <select class="form-control" name="BaseUom[]" style="max-width:350px" readonly="true">
                                        <option value="" selected></option>
                                        @foreach ($Uoms as $Uom)
                                        @if($listItem['UoMCode'] == $Uom->UomEntry)
                                            <option value="{{ $Uom->UomEntry }}" selected>{{ $Uom->UomName }}</option>
                                            @else
                                            <option value="{{ $Uom->UomEntry }}">{{ $Uom->UomName }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </td>
                                
                                <td class="Itemselectdate" >
                                        <select class="itemlist" id="Itemdate" name="Itemdate[]" data-placeholder="Select an itemcode">
                                            <option value="" selected></option>
                                            @foreach ($ItemCodes as $ItemCode)
                                            @if($listItem['ProItemCode']== $ItemCode->ItemCode)
                                            <option value="{{ $ItemCode->ItemCode }}" selected>
                                                {{ $ItemCode->ItemCode . '--' . $ItemCode->ItemName }}</option>
                                            @else
                                            <option value="{{ $ItemCode->ItemCode }}" >
                                                {{ $ItemCode->ItemCode . '--' . $ItemCode->ItemName }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                </td>
                                <td class="proqtydate"><input type="number" name="ProQtydate[]"  value="{{$listItem['ProQuantity']}}" class="form-control"></td>
                                <td class="probatchdate"><input type="text" name="ProBatchdate[]" value="{{$listItem['ProDate']}}" class="form-control" ></td>
                                <td><button type="button" class="btn btn-outline-danger"
                                        onclick="removeRow(this, '#myTable')"><i class="fa fa-trash"
                                            aria-hidden="true"></i> </button></td>
                            </tr>
                            @endforeach
                            <tr class="tr_clone_itemlist">
                                <td class="Itemselect">
                                    <select class="itemlist" id="Item" name="Item[]" data-placeholder="Select an itemcode">
                                        <option value="" selected></option>
                                        @foreach ($ItemCodes as $ItemCode)
                                            <option value="{{ $ItemCode->ItemCode }}">
                                                {{ $ItemCode->ItemCode . '--' . $ItemCode->ItemName }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="Qty[]" class="qty form-control"></td>
                                <td><select class="uom form-control" name="UomCode[]">
                                        <option value="" selected></option>
                                        @foreach ($Uoms as $Uom)
                                            <option value="{{ $Uom->UomEntry }}">{{ $Uom->UomName }}</option>
                                        @endforeach
                                    </select></td>
                                <td class="batch" ><input type="text" name="Batch[]" class="form-control batch"></td>
                                <td><input type="number" name="BaseQty[]" class="form-control" readonly="true"></td>
                                <td>
                                    <select class="form-control" name="BaseUom[]" style="max-width:350px" readonly="true">
                                        <option value="" selected></option>
                                        @foreach ($Uoms as $Uom)
                                            <option value="{{ $Uom->UomEntry }}">{{ $Uom->UomName }}</option>
                                        @endforeach
                                    </select></td>
                                    <td class="Itemselectdate">
                                        <select class="itemlist" id="Itemdate" name="Itemdate[]" data-placeholder="Select an itemcode">
                                            <option value="" selected></option>
                                            @foreach ($ItemCodes as $ItemCode)
                                                <option value="{{ $ItemCode->ItemCode }}">
                                                    {{ $ItemCode->ItemCode . '--' . $ItemCode->ItemName }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="proqtydate"><input type="number" name="ProQtydate[]" class="form-control"></td>
                                    <td class="probatchdate"><input type="text" name="ProBatchdate[]" class="form-control" ></td>
                                <td><button type="button" class="btn btn-outline-danger"
                                        onclick="removeRow(this, '#myTable')"><i class="fa fa-trash"
                                            aria-hidden="true"></i> </button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="tab-2" class="tabcontent">
                <div class="row datasearch">

                    @php
                        $config = [
                            'title' => 'Select data',
                            'liveSearch' => true,
                            'liveSearchPlaceholder' => 'Search...',
                            'showTick' => true,
                            'actionsBox' => true,
                        ];
                    @endphp
                    <x-adminlte-select-bs id="fcustomergrp" name="fcustomergrp[]" label="Customer Group"
                        label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-2" :config="$config" multiple
                        data-selected-text-format="count > 3">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-red">
                                <i class="fas fa-tag"></i>
                            </div>
                        </x-slot>
                        @foreach ($Custgroups as $Custgroup)
                            <option value="{{ $Custgroup->Code }}">{{ $Custgroup->Name }}</option>
                        @endforeach
                    </x-adminlte-select-bs>

                    <x-adminlte-select-bs id="channel" name="channels[]" label="Channel" label-class="text-lightblue"
                        igroup-size="sm" fgroup-class="col-md-2" :config="$config" multiple
                        data-selected-text-format="count > 3">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-red">
                                <i class="fas fa-tag"></i>
                            </div>
                        </x-slot>
                        @foreach ($channels as $channel)
                            <option value="{{ $channel->Code }}">{{ $channel->Name }}</option>
                        @endforeach
                    </x-adminlte-select-bs>
                    <x-adminlte-select-bs id="Route" name="Routes[]" label="Route" label-class="text-lightblue"
                        igroup-size="sm" fgroup-class="col-md-2" :config="$config" multiple
                        data-selected-text-format="count > 3">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-red">
                                <i class="fas fa-tag"></i>
                            </div>
                        </x-slot>
                        @foreach ($Routes as $Route)
                            <option value="{{ $Route->Code }}">{{ $Route->Name }}</option>
                        @endforeach
                    </x-adminlte-select-bs>
                    <x-adminlte-select-bs id="Location" name="locations[]" label="Location"
                        label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-2" :config="$config" multiple
                        data-selected-text-format="count > 3">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-red">
                                <i class="fas fa-tag"></i>
                            </div>
                        </x-slot>
                        @foreach ($Locations as $Location)
                            <option value="{{ $Location->Code }}">{{ $Location->Name }}</option>
                        @endforeach
                    </x-adminlte-select-bs>
                    <x-adminlte-button class="btn-flat" id="search"
                        style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button"
                        label="search" theme="success" icon="fas fa-lg fa-save" />
                </div>
                <div class="tableFixHead">
                    <div id="myGrid" class="ag-theme-alpine" style="height: 30%">
                    </div>

                </div>
            </div>
           
    </div>
    </div>
    <input type="text" name="customerdata[]" value="{{$customerdt}}" hidden>
    <x-adminlte-button class="btn-flat" style="float: right; margin-top:10px" id="submit" type="submit"
        label="Save" theme="success" icon="fas fa-lg fa-save" />
    </form>
    </div>
    <script>
        $(document).ready(function() {
            // Initialize Select2 on the dropdown
            $("select.items").select2();
            $("select.proitem").select2();
            $("select.itemlist").select2();
            // Clone row and re-initialize Select2
            function cloneRow() {
                $('.items').select2("destroy");
                var $tr = $('.tr_clone:last');
                var $clone = $tr.clone();
                $tr.after($clone);
                $('.items').select2();
                $clone.find('.items').select2('val', '');

                // Disable delete button if table has only one row
                if ($('#tablecustomer > tbody > tr').length == 1) {
                    $clone.find('.btn-outline-danger').prop('disabled', true);
                }
            }
            //## clone listitem
            function cloneRowProitem() {
                $('.proitem').select2("destroy");
                var $tr = $('.tr_clone_proitem:last');
                var $clone = $tr.clone();
                $tr.after($clone);
                $('.proitem').select2();
                $clone.find('.proitem').select2('val', '');
                $clone.find('input').val('');
                // Disable delete button if table has only one row
                if ($('#proitems > tbody > tr').length == 1) {
                    $clone.find('.btn-outline-danger').prop('disabled', true);
                }
            }
            //## clone proitem
            function cloneRowList() {
                $('.itemlist').select2("destroy");
                var $tr = $('.tr_clone_itemlist:last');
                var $clone = $tr.clone();
                $tr.after($clone);
                $('.itemlist').select2();
                $clone.find('.itemlist').select2('val', '');
                $clone.find('input').val('');

                // Disable delete button if table has only one row
                if ($('#myTable > tbody > tr').length == 1) {
                    $clone.find('.btn-outline-danger').prop('disabled', true);
                }
            }


            // Add new row when last row is selected
            $("#tablecustomer").on("select2:select", ".items", function() {
                var $tr = $(this).closest('.tr_clone');
                if ($tr.is(":last-child")) {
                    cloneRow();
                }
            });

            $("#proitems").on("select2:select", ".proitem", function() {
                var $tr = $(this).closest('.tr_clone_proitem');
                if ($tr.is(":last-child")) {
                    cloneRowProitem();
                }
            });
            $("#myTable").on("select2:select", ".itemlist", function() {
                var $tr = $(this).closest('.tr_clone_itemlist');
                if ($tr.is(":last-child")) {
                    cloneRowList();
                }
            });

        });
    </script>
    <!-- script js -->
    <script>
        function removeRow(button, table) {
            console.log(table);
            var row = button.parentNode.parentNode;
            if ($(table + ' > tbody > tr').length == 1) {
                alert("Cannot delete the last row.");
            } else {
                row.parentNode.removeChild(row);
            }
        }
    </script>
@stop
@section('css')
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('../css/tabformpromotion.css') }}">
    <link rel="stylesheet" href="{{ asset('../css/table.css') }}">
    <style>
          .delete-button {
            background-color: #ff4c4c;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .delete-button:hover {
            background-color: #e60000;
        }
    </style>
@stop

@push('js')
<script>
    var __basePath = './';
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('../js/handlePromotion.js') }}"></script>
    <!-- handle tab -->
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
    
        document.getElementById("defaultOpen").click();
    
        const config = [
            {
                typeSelect: 'protype',
                myTable: 'proitems',
                divcontent: 'promcontent'
            }
        ];
    
        config.forEach(({ typeSelect, myTable, divcontent }) => {
            const typeSelectElem = document.getElementById(typeSelect);
            const myTableElem = document.getElementById(myTable);
            const divcontentElem = document.getElementById(divcontent);
    
            typeSelectElem.addEventListener('change', function () {
                const selectedValue = this.value;
                console.log(selectedValue);
    
                if (selectedValue === '2' || selectedValue === '3' || selectedValue === '4') {
                    myTableElem.style.display = 'none';
                    divcontentElem.style.visibility = 'hidden';
                } else if (selectedValue === '1') {
                    myTableElem.style.display = 'table';
                    divcontentElem.style.visibility = 'visible';
                }
            });
        });
    
        $(document).ready(function () {
            const handleInputChange = (config) => {
                const { itemName, qtyName, uomCodeName, baseQtyName, baseUomName } = config;
                const row = $(event.target).closest('tr');
                const itemCode = row.find(`select[name="${itemName}[]"]`).val();
                const quantityInput = row.find(`input[name="${qtyName}[]"]`);
                const quantity = quantityInput.val();
                const uomCode = row.find(`select[name="${uomCodeName}[]"]`).val();
                const baseUomInput = row.find(`select[name="${baseUomName}[]"]`);
    
                if (quantity === '') {
                    quantityInput.val('0');
                    quantity = '0';
                }
    
                if (quantity && uomCode) {
                    $.ajax({
                        url: "{{ route('baseuom') }}",
                        data: {
                            itemcode: itemCode,
                            quantity: quantity,
                            uomcode: uomCode,
                            _token: '{{ csrf_token() }}'
                        },
                        type: 'get'
                    }).done(function (data) {
                        const baseQtyInput = row.find(`input[name="${baseQtyName}[]"]`);
                        baseQtyInput.val(data.baseQuantity);
                        baseUomInput.val(data.baseUomCode);
                    }).fail(function (data) {
                        alert("UomCode invaild!")
                    });
                } else {
                    const baseQtyInput = row.find(`input[name="${baseQtyName}[]"]`);
                    baseQtyInput.val('');
                    baseUomInput.val('');
                }
            };
    
            const configurations = [
                {
                    itemName: 'proitem',
                    qtyName: 'proqty',
                    uomCodeName: 'prouomcode',
                    baseQtyName: 'probaseqty',
                    baseUomName: 'probaseoum'
               
                },
                {
                    itemName: 'Item',
                    qtyName: 'Qty',
                    uomCodeName: 'UomCode',
                    baseQtyName: 'BaseQty',
                    baseUomName: 'BaseUom'
                }
             ];

        configurations.forEach(config => {
            const { itemName, qtyName, uomCodeName } = config;
            const selector = `select[name="${itemName}[]"], input[name="${qtyName}[]"], select[name="${uomCodeName}[]"]`;

            $('#myTable tbody, #proitems tbody').on('change', selector, function (event) {
                handleInputChange(config);
            });
        });
    });
</script>

<script>
    function BtnCellRenderer() {}

BtnCellRenderer.prototype.init = function(params) {
this.params = params;

this.eGui = document.createElement('button');
this.eGui.innerHTML = 'Delete';
this.eGui.classList.add('delete-button'); // Add CSS class

this.btnClickedHandler = this.btnClickedHandler.bind(this);
this.eGui.addEventListener('click', this.btnClickedHandler);
};

BtnCellRenderer.prototype.getGui = function() {
return this.eGui;
};

BtnCellRenderer.prototype.btnClickedHandler = function(event) {
const selectedRow = this.params.node;
const gridApi = this.params.api;
gridApi.applyTransaction({
    remove: [selectedRow.data]
  
});
const remainingData = collectRemainingData();
const customerDataInput = document.querySelector("input[name='customerdata[]']");
customerDataInput.value = JSON.stringify(remainingData);
};

BtnCellRenderer.prototype.destroy = function() {
this.eGui.removeEventListener('click', this.btnClickedHandler);
};

const columnDefs = [{
    header: 'CardCode',
    field:'CustCode'
},
{
    header: 'CardName',
    field: 'CustName'
},
{
    header: 'GroupCode',
    field: 'GroupCode'
},
{
    header: 'ChannelCode',
    field: 'ChannelCode'
},
{
    header: 'RouteCode',
    field: 'RouteCode'
},
{
    header: 'LocationCode',
    field: 'LocationCode'
},
{
    headerName: 'PGCode',
    maxWidth: 100,
    cellRenderer: BtnCellRenderer
}
];

const gridOptions = {
columnDefs: columnDefs,
pagination: true,
defaultColDef: {
    flex: 1,
    minWidth: 150,
    filter: true,
    resizable: true,
}
};

function loadInitialData() {
// Make an API call to abc.com to retrieve 100 records
// Update the grid with the retrieved data
gridOptions.api.setRowData({!!$customerdt!!});
collectRemainingData();
}
document.addEventListener('DOMContentLoaded', function() {
var gridDiv = document.querySelector('#myGrid');
new agGrid.Grid(gridDiv, gridOptions);
loadInitialData();
});
function collectRemainingData() {
const remainingData = [];
gridOptions.api.forEachNodeAfterFilter(node => {
    remainingData.push(node.data);
});
return remainingData;
}
</script>
   
  
@endpush
