<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Phiếu giao hàng</title>
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
    <script src="https://unpkg.com/pagedjs/dist/paged.polyfill.js"></script>
    <style>
      @page {
            size: A4 landscape;
            margin: 0 1 1 0;

            @bottom-right {
                content: counter(page) ' of 'counter(pages);
                margin-bottom: 2px;
            }
        }


        @media print {
            .page-break {
                page-break-before: always;
            }

        }
        .pagedjs_page {
        --pagedjs-margin-bottom: 19px !important;
        --pagedjs-margin-right: 2px !important;
    }

    .pagedjs_pagebox>.pagedjs_area>.pagedjs_page_content {
        margin-top: 10px !important;
        margin-left: 12px !important;
        margin-right: 10px !important;
        width: 98% !important;
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
            margin-top: 16px;
          
        }

        .footer-left {
            font-size: 14px;
            line-height: 1.5;
            border: 1px solid;
        }
        .left{
            border: solid;
         border-width: 1.5px;
         text-align: center;
         width: auto
        }
        .right{
            border: solid;
            border-width: 1.5px;
            text-align: center;
            width: auto
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
        .conteft{
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    @foreach ($groupedDocuments as $docentry => $documents)
        <div class="page-break">
            <header class="header">
                <h1>PHIẾU GIAO HÀNG</h1>
            </header>
            <main>
                <div class="content">
                    <div class="left-content">
                        <p>Mã nhà cung cấp: 110013/2003471</p>
                        <p>Nhà cung cấp: Cong TY TNHH BETAGEN VIET NAM</p>
                       
                        <p>Khách hàng: {{ $documents->last()->CardName }}</p>
                        <p>Kho nhận: {{ $documents->last()->Buyer }}</p>
                    </div>
                    <div class="right-content">
                        <p style="text-align: end;">{{date("d/m/Y", strtotime($documents->last()->DocDate))  }}</p>
                        <p>Số đơn hàng: {{ $documents->last()->OrderNo }}</p>
                        <p>Số Hóa đơn: {{ $documents->last()->InvNo }}</p>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">STT</th>
                            <th class="text-center">Mã Hàng</th>
                            <th class="text-center">Mã Vạch</th>
                            <th>Tên Hàng</th>
                            <th class="text-center">ĐVT</th>
                            <th class="text-center">Số lượng giao </th>
                            <th class="text-center">Số lượng nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($index = 0)
                        @foreach ($documents as $document)
                            <tr>
                                <td class="text-center">{{ ++$index }}</td>
                                <td class="text-center">{{ $document->MaHang }}</td>
                                <td class="text-center">{{ $document->Mavach }}</td>
                                <td>{{ $document->TenHang }}</td>
                                <td class="text-center">{{ $document->unitMsr }}</td>
                                <td class="text-center">{{ number_format($document->Quantity) }}</td>
                                <td class="text-center"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </main>
            <footer class="footer">
                <div class="left">
                    <p>Ngày ............ Tháng ............ Năm ............</p>
                    <p>Người nhận</p>
                </div>
                <div class="right">
                    <p>Ngày ............ Tháng ............ Năm ............</p>
                    <p>Người giao hàng</p>
              
                </div>
                
            </footer>
            <div class="conteft" >
                <p class="inline">Bảo vệ</p>
                <p class="inline">Nhân viên nhận hàng</p>
                <p class="inline">Ngành hàng</p>
            </div>
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
