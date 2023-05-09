<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
    <title>Phiếu giao hàng Aeon</title>
</head>

<body>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }

            .infocompany {

                font-size: 12px;
                /* adjust font size as needed */
                line-height: 1.5;
                /* adjust line height as needed */
            }

            .tables-container {
                display: flex;
                justify-content: space-between;
                align-items: flex-start margin-bottom: 20px;
                /* add some spacing between the tables */
                margin-right: 16px;
            }

            #table1,
            #table2,
            #table3 {
                width: 50%;
                /* distribute tables evenly across the container */

                padding: 5px;

            }

            #table1 {
                width: 80%;
                 !important
                /* set width for table 1 */
            }

            #table1 th:nth-child(1) {
                width: 300px;
            }

            #table1 th:nth-child(2) {
                width: 200px;
            }
        }

        .break {
            page-break-after: always;
        }

        #header1 span {
            vertical-align: top;
            line-height: 3px;
        }

        h2 {
            text-align: center;
        }

        .header div {
            display: inline-block;
            width: 100%;
        }


        #header1 {
            margin-left: 0px;
            text-align: left;
            width: 50%
        }

        #header2 {
            margin-right: 10px;
            margin-top: 20px;
            text-align: right;
            width: 250px;
            height: 60px;
            border: 1px solid black;
            vertical-align: top;
            line-height: 4px;
            text-align: left;
            padding-left: 20px;
            float: right;
        }

        .table-header {
            display: inline;
            margin-left: 20px;
        }

        th,
        tr,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        /* #table1 td,th{
            width: 150px;
            height: 15px;
            font-size: 11px;;
        } */

        /* #table2 td,th{
            width: 100px;
            height: 15px;
            font-size: 11px;;
        } */

        table td,
        th {
            height: 15px;
            font-size: 11px;
            font-weight: normal;
        }

        #table4 {
            margin-top: 10px;
            margin-left: 20px;
        }

        #table-main {
            margin-top: 20px;
            margin-left: 20px;
        }

        #note {
            margin-top: 20px;
            margin-right: 500px;
            float: right;
        }

        .table-header {
            display: inline-block;
            margin-right: -16px;
        }
        .page-break{
        margin-bottom: 140px; 
     }   
    </style>
    @foreach ($groupedDocuments as $docentry => $documents)
        <div class="page-break">
            <div class="header">
                <div id="header1">
                    <img src="{{ asset('images/aeonlogo.png') }}" width="200" height="100">
                    <span class="infocompany">
                        <p>Tên công ty: {{ $documents->last()->CardName }}</p>
                        <p> Địa chỉ: {{ $documents->last()->Address }} </p>
                        <p> Mã số thuế: {{ $documents->last()->MST }} </p>
                    </span>
                </div>
                <div id="header2">
                    <p>Số đơn hàng</p>
                    <p>(PO No.): {{ $documents->last()->OrderNo }}</p>
                </div>
            </div>

            <h2>PHIẾU GIAO HÀNG</h2>
            <div class="tables-container">
                <table id="table1" class="table-header" cellspacing="0" cellpadding="5">
                    <thead>
                        <tr>
                            <th style="width: 200px;">Mã Số Nhà Cung Cấp</th>
                            <th style="width: 280px;">Tên Nhà Cung Cấp</th>
                            <th style="width: 100px;">Số Hợp Đồng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>374</td>
                            <td> CTY TNHH Betagen Việt Nam</td>
                            <td>GA00686</td>
                        </tr>
                    </tbody>
                </table>


                <table id="table2" class="table-header" cellspacing="0" cellpadding="5">
                    <thead>
                        <tr>
                            <th style="width: 300px;"colspan="3">Ngày nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <table id="table3" class="table-header" cellspacing="0" cellpadding="5">
                    <thead>
                        <tr>
                            <th style="width: 70px;">In lại</th>
                            <th colspan="3" style="width: 300px;">Ngày in</th>
                            <th style="width: 100px;">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>{{date("Y")}}</td>
                            <td>{{date("m")}}</td>
                            <td>{{date("d")}}</td>
                            <td>{{date('H:i')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <table id="table4" cellspacing="0" cellpadding="5">
                <thead>
                    <tr>
                        <th style="width: 50px;">Loại Phiếu</th>
                        <th style="width: 60px;">Mã Kho</th>
                        <th style="width: 460px;">Tên Kho & Địa Chỉ Kho</th>
                        <th style="width: 115px;">LINE CODE</th>
                        <th style="width: 95px;">DEPT CODE</th>
                        <th colspan="3" style="width: 300px;">Ngày Đặt Hàng</th>
                        <th colspan="3" style="width: 310px;">Ngày Giao Hàng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td>1001</td>
                        <td>{{ $documents->last()->WhsName }}</td>
                        <td>{{ $documents->last()->LineCode }}</td>
                        <td>2027</td>
                        <td>{{date('Y', strtotime($documents->last()->OrderDate))  }}</td>
                        <td>{{date('m', strtotime($documents->last()->OrderDate))   }}</td>
                        <td>{{date('d', strtotime($documents->last()->OrderDate))   }}</td>
                        <td>{{date('Y', strtotime($documents->last()->DeliveryDate))  }}</td>
                        <td>{{date('m', strtotime($documents->last()->DeliveryDate))  }}</td>
                        <td>{{date('d', strtotime($documents->last()->DeliveryDate))  }}</td>
                    </tr>
                </tbody>
            </table>

            <table id="table-main" cellspacing="0" cellpadding="5">
                <thead>
                   

                    <tr>
                        <th rowspan="2" style="width: 50px;">STT</th>
                        <th rowspan="2" style="width: 237px;">TÊN SẢN PHẨM (ITEM DESCRIPTION)</th>
                        <th style="width: 70px;">SỐ LƯỢNG</th>
                        <th style="width: 70px;">ĐVT</th>
                        <th style="width: 120px;">MÃ HÀNG</th>
                        <th rowspan="2" style="width: 100px;">MÃ VẠCH <br>SẢN PHẨM</th>
                        <th rowspan="2" style="width: 70px;">SỐ LƯỢNG ĐẶT HÀNG</th>
                        <th rowspan="2" style="width: 70px;">SỐ LƯỢNG GIAO HÀNG</th>
                        <th rowspan="2" style="width: 50px;">CHIẾT KHẤU (%)</th>
                        <th rowspan="2" style="width: 95px;">GIÁ BÁN</th>
                        <th colspan="2"style="width: 200px;">GIÁ MUA</th>
                        <th colspan="2" style="width: 200px;">GIÁ BÁN</th>

                    </tr>

                    <tr>
                        <th style="width: 50px;">SIZE</th>
                        <th>MÀU SẮC</th>
                        <th>MÃ HÀNG NCC</th>
                        <th>ĐƠN GIÁ</th>
                        <th style="width: 100px;">THÀNH TIỀN</th>
                        <th>ĐƠN GIÁ</th>
                        <th style="width: 100px;">THÀNH TIỀN</th>

                    </tr>
                </thead>
                <tbody>
                    @php($index = 0)
                    @php($sumtotal = 0)
                    @foreach ($documents as $document)
                        <tr>
                            <td>{{++$index}}</td>
                            <td>{{ $document->TenHang }}</td>
                            <td>{{ $document->Size }}</td>
                            <td>{{ $document->Color }}</td>
                            <td>{{ $document->MaHang }}</td>
                            <td>{{ $document->MaHang }}</td>
                            <td>{{ number_format($document->OrderQtybyThung,2) }}</td>
                            <td>{{  number_format($document->OrderQtybyThung,2) }}</td>
                            <td>{{  number_format($document->Discount,2) }}</td>
                            <td></td>
                            <td>{{  number_format($document->DGiaMua,2) }}</td>
                            <td>{{ number_format($document->TTMua,2) }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @php($sumtotal+=$document->TTMua)
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="10" style="font-weight: bold;text-align: right;">TỔNG TIỀN</th>
                        <td colspan="4">{{number_format($sumtotal,2) }}</td>
                        
                    </tr>
                    <tr>
                        <th colspan="10" style="font-weight: bold;text-align: right;">TỔNG CHIẾT KHẤU</th>
                        <td colspan="4">{{number_format($documents->last()->DiscountSum,2) }}</td>
                    </tr>
                    <tr>
                        <th colspan="10" style="font-weight: bold;text-align: right;">NET TOTAL</th>
                        <td colspan="4">{{number_format($documents->last()->DiscountSum+$sumtotal,2) }}</td>
                    </tr>
                    <tr>
                        <th colspan="10" style="font-weight: bold;text-align: right;">VAT 10%</th>
                        <td colspan="4">{{number_format($documents->last()->VAT,2) }}</td>
                    </tr>
                    <tr>
                        <th colspan="10" style="font-weight: bold;text-align: right;">TỔNG TIỀN THANH TOÁN</th>
                        <td colspan="4">{{number_format($documents->last()->DocTotal,2)}}</td>

                    </tr>
                </tfoot>
            </table>

            <div id="note">
                <span>
                    Ghi chú:
                </span>
            </div>
        </div>
    @endforeach
</body>

</html>
