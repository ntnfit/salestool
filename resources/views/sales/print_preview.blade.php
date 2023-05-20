<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <style>
        table, th, td {
  border: 1px solid;
}
    </style>
<table>
    <thead>
        <tr>
            <th>STT</th>
            <th>ItemCode</th>
            <th colspan="2">ItemName</th>
            <th>Type Name</th>
            @foreach ($distinctLots as $lot)
                <th>LOT{{ $lot }}</th>
            @endforeach
            <th>Total stock out</th>
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
                $consolidatedKey = $result['ItemCode'].'_' . $result['TypePrd'];
                if (!isset($consolidatedData[$consolidatedKey])) {
                    $consolidatedData[$consolidatedKey] = [
                        'ItemCode' => $result['ItemCode'],
                        'ItemName' => $result['ItemName'],
                        'TypePrd' => $result['TypePrd'],
                        
                        'Quantity' => array_fill_keys($distinctLots, 0),
                    ];
                }
                $consolidatedData[$consolidatedKey]['Quantity'][$result['LotNo']] += $result['Quantity'];
                $totalStockOuts[$result['LotNo']] += $result['Quantity'];
            @endphp
        @endforeach

        @foreach ($consolidatedData as $key => $result)
            <tr class="{{ $result['Quantity'] > 0 ? 'has-stockout' : '' }}">
                <td>{{ $loop->iteration }}</td>
                <td class="ItemCode">{{ $result['ItemCode'] }}</td>
                <td class="ItemName" colspan="2">{{ $result['ItemName'] }}</td>    
                <td class="ItemName">@if($result['TypePrd']=="001") Bán @endif @if($result['TypePrd']=="002") Khuyến mãi @endif</td>      
                @foreach ($distinctLots as $lot)
                   
                    <td style="text-align:center;">
                        @if($result['Quantity'][$lot]>0)
                        {{ $result['Quantity'][$lot] }}    
                        @endif           
                    </td>
                @endforeach

                <td style="text-align:center;"> {{ array_sum($result['Quantity']) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th ></th>
            <th ></th>
            <th colspan="3">Total Quantity</th>
            @foreach ($distinctLots as $lot)
                @php
                    $totalQuantity = 0;
                    $totalOut = 0;
                    foreach ($results as $result) {
                        if ($result['LotNo'] == $lot) {
                            
                            $totalOut += $result['Quantity'];
                        }
                    }
                @endphp
                <th>{{ $totalOut }}</th>
              
                
            @endforeach


            <th style="">{{ array_sum($totalStockOuts) }}</th>
        </tr>
    </tfoot>
</table>

</body>
</html>