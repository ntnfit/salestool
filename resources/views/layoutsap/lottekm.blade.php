<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
    <title>Phiếu giao hàng</title>
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
    <style>
        @page {
            size:auto;;
            margin: 1cm;
        }


        @media print {
            .page-break {
                page-break-before: always;
            }

			
			

        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        h5 {
            text-align: center;
            margin-bottom: 30px;
        }

        .content {
            display: flex;
            margin-bottom: 30px;
        }

        .left-content {
            flex: 1;
        }

        .right-content {
            flex: 1;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
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
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
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
                <h1>PHIẾU GIAO HÀNG KHUYẾN MÃI</h1>
                <h5>NGÀY GIAO: {{date("d/m/Y", strtotime($documents->last()->DocDate))  }}</h5>
            </header>
            <main>
                <div class="content">
                    <div class="left-content">
                        <p>Khách hàng: 5965</p>
                        <p>Nhà cung cấp: CTY TNHH Betagen Viet Nam</p>
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
                        @php($totalline = 0)
                        @foreach ($documents as $document)
                        @php($totalline += $document->LineTotal)
                            <tr>
                                <td class="text-center">{{ ++$index }}</td>
                                <td class="text-center">{{ $document->ItemCust }}</td>
                                <td class="text-center">{{ $document->Mavach }}</td>
                                <td>{{ $document->TenHang }}</td>
                                <td class="text-center">{{ $document->CustQC }}</td>
                                <td class="text-center">{{ $document->CustUomQuyDoi }}</td>
                                <td class="text-center">{{ number_format($document->DelQtybyThung,2) }}</td>
                                @if(number_format($document->LineTotal) >0)
                                <td class="text-center">{{ number_format($document->DGThung,2) }}</td>
                                <td class="text-center">{{ number_format($document->LineTotal,2) }}</td>
                                @else
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <th colspan="8"> Tổng </th>
                        @if(number_format($totalline) >0)
                        <th>{{number_format($document->LineTotal,2)}}</th>
                        @else
                        <th> - </th>
                        @endif
                    </tfoot>
                </table>
            </main>
            <footer class="footer">
                <div class="left">
                    <p>{{$documents->last()->CardName}}</p>
                    <p>Người nhận</p>
                </div>
                <div class="right">
                    <p>Nhà cung cấp</p>
                    <p>Người giao</p>
                   
                </div>
            </footer>
        </div>
		
    @endforeach
	

</body>
<script>
	window.addEventListener("load", function () {
    var A4HeightInPx = 1123; // Approximate A4 height in pixels at 96 DPI
    var totalPages = Math.ceil(document.body.scrollHeight / A4HeightInPx);
    var pageNumberElements = document.querySelectorAll(".page-number");

    pageNumberElements.forEach(function (element, index) {
        element.textContent = "Page " + (index + 1) + " of " + totalPages;
    });
});


</script>
</html>
