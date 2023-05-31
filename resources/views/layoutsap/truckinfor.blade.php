<!DOCTYPE html>
<html>

<head>
    @if ($type == 'print')
        <title>Truck Information</title>
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    @else
        <title>Phiếu Xuất Kho</title>
    @endif
    <style>
        @media print {
            .no-print {
                display: none;
            }

            .hidden-print {
                display: none;
            }

        }

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
            font-size: 12px;
        }

        th,
        td {
            padding: 3px;
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

        .cuscode {
            width: 15px;
        }

        .ItemName {
            width: 300px
        }

        .Quantity {
            width: 30px;
        }
    </style>
</head>

<body>
    @php
        $classadd = 0;
    @endphp
    @foreach ($data as $truckInfo => $group)
        @php
            ++$classadd;
        @endphp
        <div class="container">
            <div class="header">
                <div class="logo">
                    <img src="{{ asset('images/betagen.png') }}" alt="Company Logo" class="logo">
                </div>
                <div class="header-info">

                    @if ($type == 'print')
                        <p contentEditable="true">DocDate:
                            {{ (new DateTime($group->last()->last()->DocDate))->format('y/m/d') }}</p>
                    @else
                        <p contentEditable="true">DocDate:
                            {{ (new DateTime($group->last()->DocDate))->format('y/m/d') }}</p>
                    @endif
                    @if ($type == 'print')
                        <p contentEditable="true">TruckNo: {{ $group->first()->first()->U_TruckInfo }}</p>
                    @else
                        <p contentEditable="true">TruckNo: {{ $group->last()->U_TruckInfo }}</p>
                    @endif
                    @if ($type == 'print')
                        <p contentEditable="true">Route Name: {{ $group->first()->first()->Capacity }}</p>
                    @else
                        <p contentEditable="true">Route Name: {{ $group->last()->Capacity }}</p>
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
                        <p contentEditable="true">No: {{ $group->last()->last()->DocList }}</h1>
                        @else
                        <p contentEditable="true">No: {{ $group->last()->DocList }}</p>
                    @endif
                </div>
            </div>
            @if ($type != 'print')
                <button class="table-{{ $classadd }}  hidden-print" onclick="addRow('{{ $classadd }}')">Add
                    Row</button>
            @endif
            <table id="table-{{ $classadd }}">
                <thead>
                    <tr>
                        @if ($type == 'print')
                            <th class="cuscode">Customer Code</th>
                        @endif
                        <th>STT</th>
                        <th>Item Code</th>
                        <th class="ItemName">Item Name</th>
                        <th class="Qty">Quantity</th>
                        <th>UOM</th>
                        <th>Batch No</th>
                        @if ($type == 'print')
                            <th>Invoice Number</th>
                        @else
                            <th>Remark</th>
                        @endif
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $index = 1;
                    @endphp
                    @if ($type == 'print')
                   
                        @foreach ($group as $cardCode => $items)
                            @php
                               $totalQuantity = 0;
                            foreach ($items as $item) {
                                if (number_format(floatval($item->Quantity), 2) ) {
                                    $totalQuantity +=floatval($item->Quantity);
                                }
                            }
                            @endphp
                            @foreach ($items as $index => $item)
                                <tr class="group-{{ $cardCode }}">
                                    @if ($loop->first)
                                        <td contentEditable="true" class="GroupcustCode" rowspan="{{ count($items) }}">
                                            {{ $cardCode . '  ' . $item->CardName }}
                                        </td>
                                    @endif
                                    <td contentEditable="true">{{ $index + 1 }}</td>
                                    <td contentEditable="true">{{ $item->ItemCode }}</td>
                                    <td contentEditable="true">{{ $item->Dscription }}</td>
                                    <td contentEditable="true" style="text-align: right">
                                        {{ number_format(floatval($item->Quantity), 2) }}
                                    </td>
                                    <td contentEditable="true">{{ $item->UomName }}</td>
                                    <td contentEditable="true">{{ $item->U_BatchNo }}</td>
                                    <td contentEditable="true">{{ $item->U_AdmissionDate }}</td>
                                    <td class=" hidden-print">
                                        <button class=" hidden-print" onclick="deleteRow(this)">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                            @php
                                $index = 1;
                            @endphp
                            <tr class="group-{{ $cardCode }}">
                                <td colspan="2"></td>
                                <td colspan="2"></td>
                                <td contentEditable="true" style="text-align: right">
                                    {{ number_format($totalQuantity, 2) }} 
                                </td>
                                <td colspan="2"></td>
                                <td></td>
                                <td class=" hidden-print">

                                </td>
                            </tr>
                        @endforeach
                    @else
                    @php
                    $totalQuantity = 0;
                     @endphp
                        @foreach ($group as $item)
                            <tr>
                               
                                @php
                                    $totalQuantity += floatval($item->Quantity);
                                @endphp
                            
                                <td contentEditable="true">{{ $index++ }}</td>
                                <td contentEditable="true">{{ $item->ItemCode }}</td>
                                <td contentEditable="true">{{ $item->Dscription }}</td>
                                <td contentEditable="true" style="text-align: right">
                                    {{ number_format(floatval($item->Quantity), 2) }}</td>
                                <td contentEditable="true">{{ $item->UomName }}</td>
                                <td contentEditable="true">{{ $item->U_BatchNo }}</td>
                                <td contentEditable="true">{{ $item->Comments }}</td>
                                <td class=" hidden-print">
                                    <button class=" hidden-print" onclick="deleteRowNonPrint(this)">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            @if ($type == 'print')
                                <td colspan="2"></td>
                            @else
                                <td colspan="1"></td>
                            @endif
                            <td contentEditable="true" style="text-align: right">{{ number_format(floatval($totalQuantity), 2) }}
                            </td>
                            <td colspan="2"></td>
                            <td></td>
                            <td class=" hidden-print"></td>
                        </tr>
                    @endif
                </tbody>
            </table>

        </div>
    @endforeach
</body>
<script>
    // Get all the group customer code elements
    var groupCustomerCodes = document.getElementsByClassName('GroupcustCode');

    // Add double click event listeners to each group customer code element
    Array.from(groupCustomerCodes).forEach(function(element) {
        element.addEventListener('dblclick', function() {
            // Get the parent tbody element
            var tbody = element.closest('tbody');

            // Get all the rows within the tbody
            var rows = tbody.getElementsByTagName('tr');

            // Get the current rowspan value
            var rowspan = parseInt(element.getAttribute('rowspan')) || 1;

            // Get the index of the row that contains the clicked group customer code
            var rowIndex;
            for (var i = 0; i < rows.length; i++) {
                if (rows[i].contains(element)) {
                    rowIndex = i;
                    break;
                }
            }

            // Update the rowspan value
            element.setAttribute('rowspan', rowspan + 1);

            // Calculate the position to insert the new row
            var insertIndex = rowIndex + rowspan;

            // Create a new row for the new member
            var newRow = document.createElement('tr');

            // Add the necessary cells with empty data
            var numberOfCells = 7; // Change this number based on the number of cells in your table
            for (var i = 0; i < numberOfCells; i++) {
                var newCell = document.createElement('td');
                newCell.setAttribute('contentEditable', 'true');
                // Add text alignment to the fourth cell
                if (i === 3) {
                    newCell.style.textAlign = 'right';
                }
                newRow.appendChild(newCell);
            }

            // Add a delete button cell
            var deleteButtonCell = document.createElement('td');
            deleteButtonCell.classList.add('hidden-print');
            var deleteButton = document.createElement('button');
            deleteButton.classList.add('hidden-print');
            deleteButton.textContent = 'Delete';
            deleteButton.onclick = function() {
                deleteRow(this);
            };
            deleteButtonCell.appendChild(deleteButton);
            newRow.appendChild(deleteButtonCell);

            // Insert the new row at the calculated position
            tbody.insertBefore(newRow, rows[insertIndex]);
        });
    });

    function deleteRow(button) {
        var row = button.closest('tr');
        var tbody = row.parentNode;

        // Update the rowspan value of the group customer code cell
        var groupCustomerCodeCell = tbody.querySelector('.GroupcustCode');
        var rowspan = parseInt(groupCustomerCodeCell.getAttribute('rowspan')) || 1;
        groupCustomerCodeCell.setAttribute('rowspan', rowspan - 1);

        row.remove();
    }

    function deleteRowNonPrint(button) {
        var row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }

    function addRow(classAdd) {
        var button = document.getElementsByClassName('table-' + classAdd)[0];
        var tableId = button.id;
        var table = document.getElementById('table-' + classAdd);
        var newRow = document.createElement('tr');

        // Add the necessary cells with empty data
        var numberOfCells = 7; // Change this number based on the number of cells in your table
        for (var i = 0; i < numberOfCells; i++) {
            var newCell = document.createElement('td');
            newCell.setAttribute('contentEditable', 'true');
            if (i === 3) {
                newCell.style.textAlign = 'right';
            }
            newRow.appendChild(newCell);
        }

        // Add a delete button cell
        var deleteButtonCell = document.createElement('td');
        deleteButtonCell.classList.add('hidden-print');
        var deleteButton = document.createElement('button');
        deleteButton.classList.add('hidden-print');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = function() {
            deleteRowNonPrint(this);
        };
        deleteButtonCell.appendChild(deleteButton);
        newRow.appendChild(deleteButtonCell);

        // Append the new row to the table
        var tbody = table.getElementsByTagName('tbody')[0];
        tbody.insertBefore(newRow, tbody.lastElementChild);
    }
</script>

</html>
