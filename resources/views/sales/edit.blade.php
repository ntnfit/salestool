@extends('adminlte::page')

@section('title', 'edit Stock Out Request - Sales Order')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
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
<!-- header input  -->
<div class="row">
    <x-adminlte-select label="Order type" label-class="text-lightblue" igroup-size="sm" name="ordertype"
        id="ordertype" fgroup-class="col-md-3" enable-old-support>
     <option value="{{ $so->OrderType }}">{{ $so->Name }}</option>
      
    </x-adminlte-select>
    <x-adminlte-input label="PO ID" label-class="text-lightblue" name="pono" id="pono" type="text" placeholder=""
        igroup-size="sm" fgroup-class="col-md-3" value="{{$so->PoCardCode}}" disabled>
    </x-adminlte-input>
    <x-adminlte-input-date name="podate" id="podate" label="PoDate" :config="$config"
        label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date..." value="{{$so->PODate}}" disabled>
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
       
        <option value="{{ $so->CustCode }}">{{ $so->CustCode . '--' . $so->CustName }}</option>
    </x-adminlte-select>
    <x-adminlte-select label="Warehouse" label-class="text-lightblue" igroup-size="sm" name="WhsCode" id="WhsCode"
        fgroup-class="col-md-2" enable-old-support>
        <option value="{{$so->FromWhsCode}}">{{$so->FromWhsCode}}</option>
       
    </x-adminlte-select>
    <x-adminlte-select label="Team" label-class="text-lightblue" igroup-size="sm" name="bincode" id="bincode"
        fgroup-class="col-md-2" enable-old-support >
        <option value="{{$so->AbsEntry}}">{{$so->BinCode}}</option>
    </x-adminlte-select>

    <x-adminlte-input-date name="date" id="sodate" label="Date" :config="$configsodate"
        label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-3" placeholder="Choose a date..." value="{{$so->StockDate}}" disabled>
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
<div style="height: 450px; overflow: auto;">
    <table id="tableadd">
        <thead>
            <tr>
                <th>STT</th>
                <th>ItemCode</th>
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
                    <td hidden><input type="text" class="sotype" name="sotype[{{ $result['ItemCode'] }}][]" value=""></td>
                    @foreach ($distinctLots as $lot)
                        
    
                        <td class="{{ $result['QuantityOut'][$lot] > 0 ? 'orange' : '' }}">
                            @if($result['QuantityIn'][$lot] > 0)
                                <input type="number" class="Qtyout" style="text-color:orange"
                                    name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                    value="{{ $result['QuantityOut'][$lot] }}" max="{{ $result['QuantityIn'][$lot] }}" min="0">
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
                <th colspan="2">Total Quantity</th>
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
@endpush