<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu giao hàng Aeon</title>
</head>
<body>
    <style>
    <style>
        @page {
            size: A4 portrait;
            margin: 1cm;
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
        }
    .header{
        text-align: center;
        line-height: 10px;
    }

    .header table{
        width: 500px;
        margin-left: 500px;
    }


    .header th,tr, td{
        padding-left: 10px;
    }

    table, th, tr, td{
            border: 1px solid black;
            border-collapse: collapse ;
            font-weight: normal;
            height: 35px;
            font-size: 15px;
            text-align: left;
            line-height: 17px;
            
        }

    #table-main th{
        font-weight: bold;
        text-align: center;
    }

    #table-main{
        margin-top: 20px;
        margin-left: 50px;
        margin-right: 50px;
    }

    .date {
        display: inline-block;
        margin-top: 20px;
    }
    .sign{
        margin-top: 80px;
        float: left;
        
    }
    #sign1{
        margin-left: 30px;
        float: left;
    }

    #sign2{
        margin-left: 200px;
        float: left;
    }
    #date1{
        float: left;
        margin-left: 120px;
        width: 200px;
    }

    #date2{
        width: 200px;
        float: right;
        margin-right: 80px;
    } 
    .break {
            page-break-after: always;
           
        }
     .page-break{
        margin-bottom: 140px; 
     }   
    </style>
     @foreach ($groupedDocuments as $docentry => $documents)
     <div class="page-break">
    <img src="{{ asset('images/aeonlogo.png') }}" width="200" height="100">
    <div class="header"> 
        <h2 style="font-weight: bold;" >PHIẾU GIAO HÀNG KHUYẾN MÃI</h2>
        <h3 style="font-weight: normal;" >PROMOTIONDELIVERY NOTE</h3>
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
                <td>{{ $documents->last()->VendorName }}</td>
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
                    <th style="width: 400px;"> GHI CHÚ <br>Note</th>
                </tr>
            </thead>
            <tbody>
                @php($index = 0)
                  
                    @foreach ($documents as $document)
                <tr>
                   
                    <td>{{ $document->MaHang }}</td>
                    <td>{{ $document->TenHang }}</td>
                    <td>{{ number_format($document->Quantity,2) }}</td>
                    <td>{{$document->GhiChu }}</td>
                </tr>
              
                @endforeach
        
            </tbody>
        </table>
    </div>
    <div class="footer">

            <div class="date" id="date1">
                <span>Ngày </span> &nbsp;
                <span>Tháng</span> &nbsp;
                <span>Năm</span> &nbsp; <br>
                <span>Date </span> &nbsp;
                <span>Month</span> &nbsp;
                <span>Year</span> &nbsp;
            </div>

            <div class="date" id="date2">
                <span>Ngày</span>  &nbsp;
                <span>Tháng</span> &nbsp;
                <span>Năm</span>  &nbsp;<br>
                <span>Date </span> &nbsp;
                <span>Month</span> &nbsp;
                <span>Year</span> &nbsp;
            </div>
        </div>  
        
            <div class="sign" id="sign1">
            <p>Phòng giao nhận hàng hóa</p>
            </div>

            <div class="sign" id="sign2">
                <p>Ngành hàng</p>
            </div>
        </div>
    <div>
    </div>
    @endforeach

</body>
</html>