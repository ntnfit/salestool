<!DOCTYPE html>
<html>

<head>
    @if ($type == 'print')
        <title>Truck Information</title>
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
    @else
        <title>Phiếu Xuất Kho</title>
    @endif
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            size: auto;
            height: 297mm;
            width: 210mm;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .header {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            margin-bottom: 30px;
            width: 100%;
        }

        .header-text {
            text-align: center;
            margin-left: 20px;
            flex: 1;
        }

        .header-text h2 {
            margin-top: 0;
            margin-bottom: 10px;
            text-align: center;
            font-size: 18px;
        }

        .header-text p {
            margin: 0;
            text-align: center;
            font-size: 16px;
        }

        .header-info {
            text-align: left;
            margin-top: 30px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header-info p {
            margin-left: 0;
            font-size: 14px;
            line-height: 20px;
            margin: 0;
        }

        .header-info>p:first-child {
            margin-bottom: auto;
        }

        .logo {
            display: block;
            margin-right: auto;
            max-height: 36px;
            max-width: 96%;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @foreach ($data as $truckInfo => $group)
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/betagen.png') }}" alt="Company Logo" class="logo">
            </div>
            <div class="header-info">

                @if ($type == 'print')
                    <p>DocDate: {{(new DateTime($group->last()->last()->DocDate))->format('y/m/d') }}</h1>
                    @else
                    <p>DocDate: {{ (new DateTime($group->last()->DocDate))->format('y/m/d') }}</p>
                @endif
				@if ($type == 'print')
                    <p>TruckNo: {{ $group->first()->first()->U_TruckInfo }}</h1>
                    @else
                    <p>TruckNo: {{ $group->last()->U_TruckInfo }}</p>
                @endif
				@if ($type == 'print')
                    <p>Capacity: {{ $group->first()->first()->Capacity }}</h1>
                    @else
                    <p>Capacity: {{ $group->last()->Capacity }}</p>
                @endif
              
                
            </div>
            <div class="header-text">
                @if ($type == 'print')
                    <h2>TRUCK INFORMATION</h2>
                    <p>THÔNG TIN XE</p>
                @else
                    <h2>STOCKOUT REQUEST</h2>
                    <p>PHIẾU XUẤT KHO</p>
                @endif
            </div>

            <div class="header-info">
				@if ($type == 'print')
				<p>No: {{$group->last()->last()->DocList }}</h1>
				@else
				<p>No: {{ $group->last()->DocList }}</p>
			@endif
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    @if ($type == 'print')
                        <th>Customer Code</th>
                    @endif
                    <th>STT</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>UOM</th>
                    <th>Batch No</th>
                    @if ($type == 'print')
                        <th>Invoice Number</th>
                    @else
                        <th>Remark</th>
                    @endif


                </tr>
            </thead>
            <tbody>
                @php
                    $index = 1;
                @endphp
                @if ($type == 'print')
                @foreach ($group as $cardCode => $items)
                        @php
                            $totalQuantity = $items->sum('Quantity');
                        @endphp
                       @foreach ($items as $item)
                            <tr>
                                @if ($loop->first)
                                <td rowspan="{{ count($items) }}">{{ $cardCode. '  ' . $item->CardName }}</td>
                                @endif
                                <td>{{ $index++ }}</td>
                                <td>{{ $item->ItemCode }}</td>
                                <td>{{ $item->Dscription }}</td>
                                <td style="text-align: right">{{ number_format($item->Quantity,2) }}</td>
                                <td>{{ $item->UomName }}</td>
                                <td>{{ $item->U_BatchNo }}</td>
                                <td>{{ $item->U_AdmissionDate }}</td>
                            </tr>
                        @endforeach
                        @php
                        $index=1;
                        @endphp   
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="2"></td>
                            <td style="text-align: right">{{ number_format($totalQuantity,2) }}</td>
                            <td colspan="2"></td>
                            <td></td>
                        </tr>
                    @endforeach
                @else
                    @php
                        $totalQuantity = 0;
                    @endphp
                    @foreach ($group as $item)
                        <tr>
                            @php
                                
                                $totalQuantity += $item->Quantity;
                            @endphp
                            <td>{{ $index++ }}</td>
                            <td>{{ $item->ItemCode }}</td>
                            <td>{{ $item->Dscription }}</td>
                            <td>{{ number_format($item->Quantity,2) }}</td>
                            <td>{{ $item->UomName }}</td>
                            <td>{{ $item->U_BatchNo }}</td>
                            <td>{{ $item->Comments }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"></td>
                        @if ($type == 'print')
                            <td colspan="2"></td>
                        @else
                            <td colspan="1"></td>
                        @endif
                        <td>{{ number_format($totalQuantity,2) }}</td>
                        <td colspan="2"></td>
                        <td></td>
                    </tr>
                @endif
            </tbody>
        </table>

    </div>
    @endforeach
</body>

</html>
