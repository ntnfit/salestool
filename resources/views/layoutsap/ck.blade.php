<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Phiếu giao hàng</title>
    <script src="https://unpkg.com/pagedjs/dist/paged.polyfill.js"></script>
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
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
            margin-top: 5x;
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

        table th {
            background-color: #dddd;
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
                        <p>Nhà cung cấp: CTY TNHH Betagen Việt Nam</p>
                        <p>Khách hàng: {{ $documents->last()->CardName }}</p>
                        <p>Địa chỉ: {{ $documents->last()->Address }}</p>
                    </div>
                    <div class="right-content">
                        <p style="text-align: end;">{{ date('d/m/Y', strtotime($documents->last()->DocDate)) }}</p>
                        <p>Số đơn hàng: {{ $documents->last()->OrderNo }}</p>
                        <p>Số Hóa đơn: {{ $documents->last()->InvNo }}</p>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">STT</th>
                            <th class="text-center">ItemCode</th>
                            <th class="text-center">BarCode</th>
                            <th>Tên Hàng</th>
                            <th class="text-center">Qui Cách</th>
                            <th class="text-center">ĐVT</th>
                            <th class="text-center">Số Lượng</th>
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
                                <td class="text-center">{{ $document->CustQC }}</td>
                                <td class="text-center">{{ $document->unitMsr }}</td>
                                <td class="text-center" style="text-align: right">
                                    {{ number_format($document->Quantity, 2) }}</td>
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
        </div>
    @endforeach


</body>
<script>
    window.addEventListener("load", function() {
        var A4HeightInPx = 1123; // Approximate A4 height in pixels at 96 DPI
        var totalPages = Math.ceil(document.body.scrollHeight / A4HeightInPx);
        var pageNumberElements = document.querySelectorAll(".page-number");

        pageNumberElements.forEach(function(element, index) {
            element.textContent = "Page " + (index + 1) + " of " + totalPages;
        });
    });
</script>

</html>
