@extends('adminlte::page')

@section('title', 'Add Stock Out Request - Sales Order')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
@section('content')

<div style="height: 600px; overflow: auto;">
<table>
    <thead>
        <tr>
            <th>STT</th>
            <th>ItemCode</th>
            @foreach ($distinctLots as $lot)
                <th>LOT{{ $lot }}</th>
                <th class="orange">Stock Out</th>
            @endforeach
            <th>Total Qty Lot</th>
            <th>Total Stock Out</th>
        </tr>
    </thead>
    <tbody>
        @php
            $consolidatedData = array();
            $totalQuantityIns = array_fill_keys($distinctLots, 0);
            $totalStockOuts = array_fill_keys($distinctLots, 0);
        @endphp
        @foreach ($results as $key => $result)
            @php
                $consolidatedKey = $result['ItemCode'];
                if (!isset($consolidatedData[$consolidatedKey])) {
                    $consolidatedData[$consolidatedKey] = array(
                        'ItemCode' => $result['ItemCode'],
                        'QuantityIn' => array_fill_keys($distinctLots, 0),
                        'QuantityOut' => array_fill_keys($distinctLots, 0)
                    );
                }
                $consolidatedData[$consolidatedKey]['QuantityIn'][$result['LotNo']] += $result['QuantityIn'];
                $consolidatedData[$consolidatedKey]['QuantityOut'][$result['LotNo']] += $result['QuantityOut'];
                $totalQuantityIns[$result['LotNo']] += $result['QuantityIn'];
                $totalStockOuts[$result['LotNo']] += $result['QuantityOut'];
            @endphp
        @endforeach

        @foreach ($consolidatedData as $key => $result)
            <tr class="{{ $result['QuantityOut'] > 0 ? 'has-stockout' : '' }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $result['ItemCode'] }}</td>
                @foreach ($distinctLots as $lot)
                    <td>{{ $result['QuantityIn'][$lot] }}</td>
                   
                    <td class="{{ $result['QuantityOut'][$lot] > 0 ? 'orange' : '' }}">
                        @if (1 == 1)
                            <input type="number" name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}]" value="{{ $result['QuantityOut'][$lot] }}">
                        @else
                            <span>{{ $result['QuantityOut'][$lot] }}</span>
                        @endif
                    </td>
                @endforeach
                <td>{{ array_sum($result['QuantityIn']) }}</td>
                <td>{{ array_sum($result['QuantityOut']) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total Quantity</th>
            @foreach ($distinctLots as $lot)
                @php
                    $totalQuantity = 0;
                    $totalOut=0;
                    foreach ($results as $result) {
                        if ($result['LotNo'] == $lot) {
                            $totalQuantity += $result['QuantityIn'];
                            $totalOut += $result['QuantityOut'];
                        }
                    }
                @endphp
                <th>{{ $totalQuantity }}</th>
                <th>{{ $totalOut}}</th>
            @endforeach
           
            <th>{{ array_sum($totalQuantityIns) }}</th>
            <th>{{ array_sum($totalStockOuts) }}</th>
        </tr>
    </tfoot>
</table>
</div>
@stop
@section('css')
<style>
    table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
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

td:first-child, th:first-child {
    text-align: left;
}

.orange {
    color: orange;
}
</style>
@stop
@push('js')
@endpush