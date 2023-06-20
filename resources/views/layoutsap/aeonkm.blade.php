<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    <title>Phiếu giao hàng Aeon</title>

</head>

<body>
    <style>
        <style>@page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }

            .header {
                text-align: center;
                margin-left: auto;
                margin-right: auto;
            }

            #tbhead {
                margin: 0 auto;
            }

            .master {
                height: 288mm;
                width: 100%;
            }

            .page {
                position: relative;
                right: 0;
                float: right;
            }
            .footer {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                margin: 0 100px 0 150px;
                margin-top: 10px
            }
            .row {
                display: flex;
                justify-content: space-between;
                width: 100%;
                height: 50px;
            }
            .date {
                text-align: left;
            }
            .sign {
                text-align: right;
            }
        }

        .header {
            text-align: center;
            line-height: 10px;
            margin-left: auto;
            margin-right: auto;
        }
        #tbhead {
                margin: 0 auto;
            }



        .header th,
        tr,
        td {
            padding-left: 10px;
        }

        table,
        th,
        tr,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            font-weight: normal;
            height: 30px;
            font-size: 15px;
            text-align: left;
            line-height: 17px;

        }

        #table-main th {
            font-weight: bold;
            text-align: center;
        }

        #table-main {
            margin-top: 10px;
            margin-left: 50px;
            margin-right: 50px;
        }

        .footer {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                margin: 0 100px 0 150px;
                margin-top: 10px
            }
            .row {
                display: flex;
                justify-content: space-between;
                width: 100%;
                height: 50px;
            }
            .date {
                text-align: left;
            }
            .sign {
                text-align: right;
            }


        .break {
            page-break-after: always;

        }

        .page-break {
            margin-bottom: 140px;
        }

        .master {
            height: 288mm;
            width: 100%;
        }

        .page {
            position: relative;
            right: 0;
            float: right;
        }
    </style>
    @php
        $pagenumber = 0;
        $totalPage = count($groupedDocuments);
    @endphp
    @foreach ($groupedDocuments as $docentry => $documents)
        <div class="page-break">
            <div class="master">
                <img src="{{ asset('images/aeonlogo.png') }}" width="200" height="100">
                <div class="header">
                    <h2 style="font-weight: bold;">PHIẾU GIAO HÀNG KHUYẾN MÃI</h2>
                    <h3 style="font-weight: normal;">PROMOTIONDELIVERY NOTE</h3>
                    <table id="tbhead">
                        <tr>
                            <th style="width: 120px;">Mã bộ phận: <br> Department Code</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th style="width: 120px;">Mã nhà cung cấp: <br> Supplier No </th>
                            <td>374</td>
                        </tr>
                        <tr>
                            <th style="width: 120px;">Số đặt hàng: </th>
                            <td>{{ $documents->last()->OrderNo }}</td>
                        </tr>
                        <tr>
                            <th style="width: 120px;">Tên nhà cung cấp: <br>Supplier Name</th>
                            <td>CTY TNHH Betagen Việt Nam</td>
                        </tr>
                    </table>
                </div>

                <div id="table-main">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 200px;"> MÃ HÀNG <br> Item No</th>
                                <th style="width: 600px;"> TÊN HÀNG<br> Item Description </th>
                                <th style="width: 200px;"> SỐ LƯỢNG <br> Quantity </th>
                                <th style="width: 200px;"> ĐVT<br> UoM </th>
                                <th style="width: 400px;"> GHI CHÚ <br>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($index = 0)

                            @foreach ($documents as $document)
                                <tr>

                                    <td>{{ $document->MaHang }}</td>
                                    <td>{{ $document->TenHang }}</td>
                                    <td>{{ number_format($document->Quantity) }}</td>
                                    <td>{{ $document->CustUomQuyDoi }}</td>
                                    <td>{{ $document->GhiChu }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="footer">
                    <div class="row">
                        <div class="date" id="date1">
                            <span>Ngày .....</span>
                            <span>Tháng .....</span>
                            <span>Năm .....</span> <br>
                            <span>Date .....</span>
                            <span>Month .....</span>
                            <span>Year .....</span>
                        </div>
                        <div class="date" id="date2">
                            <span>Ngày .....</span>
                            <span>Tháng .....</span>
                            <span>Năm .....</span><br>
                            <span>Date .....</span>
                            <span>Month .....</span>
                            <span>Year .....</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="sign" id="sign1">
                            <p>Phòng giao nhận hàng hóa</p>
                        </div>
                        <div class="sign" id="sign2">
                            <p>Ngành hàng</p>
                        </div>
                    </div>
                </div>


            </div>
        <div class="page">
            {{ ++$pagenumber . '/' . $totalPage }}
        </div>
        </div>
    @endforeach

</body>

</html>
