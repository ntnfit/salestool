<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Phiếu giao hàng</title>
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
    <style>
        @page {
            size: auto;
            margin: 0;
        }


        @media print {
            .page-break {
                page-break-before: always;
            }

			
			

        }

        h1 {
            text-align: center;
            margin-bottom: -8px;
            height: 30px;
        }

        h5 {
            text-align: center;
            margin-bottom: 5px;
        }

        .content {
            display: flex;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .left-content {
            flex: 1;
            font-size: 12px;
        }

        .right-content {
            flex: 1;
            text-align: center;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            padding: 4px;
            border: 1px solid #000;
        }

        .text-center {
            text-align: center;
        }

        .footer-block {
            display: flex;
            margin-top: 30px;
        }

        .footer-left,
        .footer-right {
            flex: 1;
            font-size: 12px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            padding: 10px 30px;
        }

        .footer-left {
            font-size: 14px;
            line-height: 1.5;
        }

        .footer-right {
            font-size: 14px;
            line-height: 1.5;
            text-align: right;
        }

        /* Footer right block */
        .footer-right {
            font-size: 14px;
            line-height: 1.5;
            text-align: right;
            margin-right: 30px;
        }

        .page-number:after {
            content: counter(page);
        }

        .break {
            page-break-after: always;
        }

        table th {
            background-color: #dddd;
        }
    </style>
</head>

<body>
    @foreach ($groupedDocuments as $docentry => $documents)
        <div class="page-break">
            <header class="header">
                <h1>PHIẾU GIAO HÀNG</h1>
                <h5>NGÀY GIAO: {{date("d/m/Y", strtotime($documents->last()->DocDate))  }}</h5>
            </header>
            <main>
                <div class="content">
                    <div class="left-content">
                        <p>Nhà cung cấp: 5965</p>
                        <p>Tên nhà cung cấp: CTY TNHH Betagen Việt Nam</p>
                       
                    </div>
                    <div class="right-content">
                       
                        <p>Số đơn hàng: {{ $documents->last()->OrderNo }}</p>
                        <p>Số Hóa đơn: {{ $documents->last()->InvNo }}</p>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">STT</th>
                            <th class="text-center">Mã hàng Lotte</th>
                            <th class="text-center">Mã vạch sản phẩm</th>
                            <th>Tên Hàng</th>
                            <th class="text-center">Qui Cách</th>
                            <th class="text-center">ĐVT</th>
                            <th class="text-center">Số Lượng</th>
                            <th class="text-center">Đơn giá</th>
                            <th class="text-center">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($index = 0)
                        @php($total =0)
                        @foreach ($documents as $document)
                       
                        @php($total +=$document->LineTotal)
                            <tr>
                                <td class="text-center">{{ ++$index }}</td>
                                <td class="text-center">{{ $document->ItemCust }}</td>
                                <td class="text-center">{{ $document->Mavach }}</td>
                                <td>{{ $document->TenHang }}</td>
                                <td class="text-center">{{ $document->CustQC }}</td>
                                <td class="text-center">{{ $document->CustUomQuyDoi }}</td>
                                <td class="text-center">{{ number_format($document->DelQtybyThung,2) }}</td>
                                <td class="text-center">{{ number_format($document->DGThung,2) }}</td>
                                <td class="text-center">{{number_format($document->LineTotal,2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            
                        
                            <td  colspan="8" style="text-align: center;font-weight: bold">Tổng</td>
                           
                            <td class="text-center" style="font-weight: bold">{{number_format($total,2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </main>
            <footer class="footer">
                <div class="left">
                    <p>{{$documents->last()->CardName}}</p>
                    <p>Người nhận</p>
                </div>
                <div class="right">
                    <p>Nhà cung cấp</p>
                    <p>Người giao </p>
                   
                </div>
            </footer>
        </div>
		
    @endforeach
	

</body>

</html>
