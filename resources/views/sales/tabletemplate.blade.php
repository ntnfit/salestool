
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
                        value="{{ $ordertype }}"></td>
                        @php
                        $firstNonZeroLot = null;
                        foreach ($result['PlanQty'] as $lot => $qty) {
                            if ($qty != 0) {
                                $firstNonZeroLot = $lot;
                                break;
                            }
                        }
                    @endphp
                @if ($blanket != 0)
                    <td style="text-color:orange;">
                        {{ $result['PlanQty'][$lot] }}
                    </td>
                    <td>
                        {{ $result['CumQty'][$lot] }}
                    </td>
                    <td class="openqtyrow">
                        {{ $result['OpenQty'][$lot] }}
                    </td>

                @endif
                @foreach ($distinctLots as $lot)
                @if ($blanket != 0)
                <td hidden>
        
                    
                    @if ($result['QuantityOut'][$lot] ==0 &&  $result['OpenQty'][$firstNonZeroLot]>0 && $result['QuantityIn'][$lot])
                        <input type="number" class="maxtotal" value="{{ $result['OpenQty'][$lot] }}" hidden>
                    @elseif($result['QuantityOut'][$lot] >0 &&  $result['OpenQty'][$firstNonZeroLot]>0)
                    <input type="number" class="maxtotal" value="{{ $result['OpenQty'][$lot] +$result['QuantityOut'][$firstNonZeroLot] }}" hidden> 
                     @elseif($result['QuantityOut'][$lot] >0 &&  $result['OpenQty'][$firstNonZeroLot]==0)
                    <input type="number" class="maxtotal" value="{{ $result['QuantityOut'][$lot] }}" hidden>
                    @endif
                </td>
                @endif
                    <td class="{{ $result['QuantityOut'][$lot] > 0 ? 'orange' : '' }}">

                        @if ($blanket != 0)
                            @if ($result['QuantityIn'][$lot] > 0)
                                <input type="number" class="Qtyout" style="text-color:orange"
                                    name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                    value="{{ $result['QuantityOut'][$lot] }}" max="{{ $result['OpenQty'][$lot] }}"
                                    min="0">
                            @else
                                <input type="number" class="Qtyout" style="text-color:orange"
                                    name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]" value=""
                                    readonly="true">
                            @endif
                        @elseif($result['QuantityOut'][$lot] > 0 && $result['TypePrd'] == '001')
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
                                value="{{ $result['QuantityOut'][$lot] }}" max="{{ $max }}" min="0">
                        @elseif ($result['QuantityOut'][$lot] > 0 && $result['TypePrd'] == '002')
                            <input type="number" class="qtypro" style="text-color:orange"
                                name="proout[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                value="{{ $result['QuantityOut'][$lot] }}" max="{{ $result['QuantityOut'][$lot] }}"
                                min="0">
                        @elseif($result['QuantityIn'][$lot] > 0)
                            <input type="number" class="Qtyout" style="text-color:orange"
                                name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]" value="">
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
            @if ($result['TypePrd'] === '001' && array_sum($result['QuantityOut'])>0 )
            <input type="number" class="totalrow" value="{{ array_sum($result['QuantityOut']) }}"
                readonly="true">
        @elseif($result['TypePrd'] === '002' && array_sum($result['QuantityOut'])>0)
            <input type="number" name="totalprorow[]" class="totalpro" value="{{ array_sum($result['QuantityOut']) }}"
                readonly="true">
        @else
        <input type="number" class="totalrow" value=""
        readonly="true">
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

            @if(array_sum($totalStockOuts)>0)
            <th class="totalstockout">{{ array_sum($totalStockOuts) }}</th>
            @else
            <th class="totalstockout"></th>
            @endif
            
        </tr>
    </tfoot>
</table>
