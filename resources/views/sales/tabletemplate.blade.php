<input type="text" id="searchInput" placeholder="Search...">
<table id="tableadd">
    <thead>
        <tr>
            <th>STT</th>
            <th>ItemCode</th>
            <th colspan="2">ItemName</th>
            <th hidden>Type</th>
            @if($blanket!=0)
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
                $consolidatedKey = $result['ItemCode'];
                if (!isset($consolidatedData[$consolidatedKey])) {
                    $consolidatedData[$consolidatedKey] = [
                        'ItemCode' => $result['ItemCode'],
                        'ItemName' => $result['ItemName'],
                        'PlanQty' =>array_fill_keys($distinctLots, 0),
                        'CumQty' =>array_fill_keys($distinctLots, 0),
                        'OpenQty' =>array_fill_keys($distinctLots, 0),
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
            <tr class="{{ $result['QuantityOut'] > 0 ? 'has-stockout' : '' }}">
                <td>{{ $loop->iteration }}</td>
                <td class="ItemCode">{{ $result['ItemCode'] }}</td>
                <td class="ItemName" colspan="2">{{ $result['ItemName'] }}</td>
                
                <td hidden><input type="text" class="sotype" name="sotype[{{ $result['ItemCode'] }}][]" value="{{$ordertype}}"></td>
               
                @foreach ($distinctLots as $lot)
                @if($blanket!=0)
                <td>
                    <input type="number" class="Qtyout" style="text-color:orange;  width: 182.4px;"
                    name="PlanQty[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                    value="{{ $result['PlanQty'][$lot] }}" readonly>
                </td>
                <td>
                    <input type="number" class="Qtyout" style="text-color:orange;  width: 182.4px;"
                    name="CumQty[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                    value="{{ $result['CumQty'][$lot] }}"  readonly>
                </td>  
                <td>
                    <input type="number" class="Qtyout" style="text-color:orange;  width: 182.4px;"
                    name="OpenQty[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                    value="{{ $result['OpenQty'][$lot] }}"  readonly>
                </td>
                              
                @endif
                    <td class="{{ $result['QuantityOut'][$lot] > 0 ? 'orange' : '' }}">
                       @if($blanket!=0)
                       @if($result['QuantityIn'][$lot] > 0)
                            <input type="number" class="Qtyout" style="text-color:orange;  width: 182.4px;"
                                name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                value="{{ $result['QuantityOut'][$lot] }}" max="{{$result['OpenQty'][$lot] }}" min="0">
                        @else
                        <input type="number" class="Qtyout" style="text-color:orange"
                                name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                value="" readonly="true">
                        @endif
                       
                       @else

                        @if($result['QuantityIn'][$lot] > 0)
                            <input type="number" class="Qtyout" style="text-color:orange;  width: 182.4px;"
                                name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                value="{{ $result['QuantityOut'][$lot] }}" max="{{ $result['QuantityIn'][$lot] }}" min="0">
                        @else
                        <input type="number" class="Qtyout" style="text-color:orange"
                                name="stockOuts[{{ $result['ItemCode'] }}][{{ $lot }}][]"
                                value="" readonly="true">
                        @endif
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
            <th ></th>
            <th ></th>
            <th ></th>
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
