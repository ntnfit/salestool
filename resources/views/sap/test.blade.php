@extends('adminlte::page')

@section('title', 'Customer data')
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>	
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script>
@section('content')

  <form>
    <div class="form-row">
        <div class="form-group col-sx-4">Sale manager</div>
        <div class="form-group col-md-4">
        <select id="channel" class="form-control">
            <option value="0">All</option>
            <option value="DTH" selected>Doan Thi Hong</option>
            <option value="GT">GT Chanel</option>
        </select>
        </div>
    <div class="form-group col-md-2">
   
      <button type="button" class="form-control btn btn-primary" id="search" onclick="sCustomer()">Search</button>
    </div>
    <div class="form-group col-md-2">
      <button type="button" id="addRow" class="form-control btn btn-primary">Add Row</button>
      <button type="button" class="form-control btn btn-primary" id="export-excel" onclick="onBtExport()">Excel</button>
    </div>
    </div>
</form>
		<div id="myGrid" class="ag-theme-alpine" style="height: 100%">
		</div>


    <style media="only screen">
            html, body {
                height: 100%;
                width: 100%;
                margin: 0;
                box-sizing: border-box;
                -webkit-overflow-scrolling: touch;
            }

            html {
                position: absolute;
                top: 0;
                left: 0;
                padding: 0;
                overflow: auto;
            }

            body {
                padding: 1rem;
                overflow: auto;
            }
        </style>

		<script>var __basePath = './';</script>
		<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@28.2.1/dist/ag-grid-community.min.js"> 
		</script>
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@28.2.1/dist/ag-grid-enterprise.min.js">
      </script>
		<script>
      var filterParams = {
  comparator: (filterLocalDateAtMidnight, cellValue) => {
    var dateAsString = cellValue;
    if (dateAsString == null) return -1;
    var dateParts = dateAsString.split('/');
    var cellDate = new Date(
      Number(dateParts[2]),
      Number(dateParts[1]) - 1,
      Number(dateParts[0])
    );

    if (filterLocalDateAtMidnight.getTime() === cellDate.getTime()) {
      return 0;
    }

    if (cellDate < filterLocalDateAtMidnight) {
      return -1;
    }

    if (cellDate > filterLocalDateAtMidnight) {
      return 1;
    }
  },
  browserDatePicker: true,
};
function addNewRow() {
  const newRow = {
    CardCode: '',
    CardName: '',
    ShortName: '',
    storeID: '',
    Street: '',
    TaxCode: '',
    Channel: ''
  };
  const addIndex = gridOptions.api.getDisplayedRowCount();
  gridOptions.api.applyTransaction({
    add: [newRow],
    addIndex: addIndex
  });
}

document.getElementById('addRow').addEventListener('click', addNewRow);

const options = ['Option 1', 'Option 2', 'Option 3'];

const columnDefs = [
  {
    field: 'CardCode',
    cellEditor: 'customRichSelectCellEditor',
          cellEditorPopup: true,
          cellEditorParams: {
            values: options
          },
          editable: true
  },
  { field: 'CardName', filter: 'agNumberColumnFilter', editable: true },
  { field: 'ShortName', filter: 'agNumberColumnFilter', editable: true },
  { field: 'storeID', maxWidth: 100, filter: 'agNumberColumnFilter', editable: true },
  { field: 'Street', filter: 'agDateColumnFilter', filterParams: filterParams, editable: true },
  { field: 'TaxCode', filter: 'agNumberColumnFilter', editable: true },
  { field: 'Channel', filter: 'agNumberColumnFilter', editable: true },
  {
          headerName: 'Actions',
          cellRenderer: 'deleteButtonCellRenderer',
          minWidth: 100,
          maxWidth: 100,
          sortable: false,
          filter: false
        }
];

const gridOptions = {
  columnDefs: columnDefs,
  pagination: true,
  defaultColDef: {
    flex: 1,
    minWidth: 150,
    filter: true,
    resizable: true
  },
  frameworkComponents: {
          deleteButtonCellRenderer: deleteButtonCellRenderer
        }
};

function customRichSelectCellEditor() {
      let eCell = document.createElement('div');
      eCell.innerHTML = `<input type="text" class="live-search-input">`;

      let values = [];
      let selectValue = null;
      let filteredOptions = [];

      function init(params) {
        values = params.values;
        selectValue = params.value;
        filteredOptions = values;

        eCell.querySelector('input').value = selectValue || '';
        eCell.querySelector('input').addEventListener('input', handleInputChange);
        eCell.querySelector('input').addEventListener('keydown', handleKeyDown);

        // Attach the event listener to show the popup when the input field is clicked
        eCell.querySelector('input').addEventListener('click', showPopup);
      }

      function handleInputChange(event) {
        const filterText = event.target.value.toLowerCase();
        filteredOptions = values.filter(option =>
          option.toLowerCase().includes(filterText)
        );
        showPopup();
      }

      function handleKeyDown(event) {
        if (event.key === 'Escape') {
          // Cancel editing
          params.stopEditing();
        } else if (event.key === 'Enter') {
          // Finish editing and update cell value
          params.stopEditing();
          params.setValue(selectValue);
        }
      }

      function showPopup() {
        const eSelect = document.createElement('select');
        eSelect.setAttribute('class', 'ag-cell-edit-input');
        eSelect.innerHTML = filteredOptions
          .map(option => `<option value="${option}">${option}</option>`)
          .join('');

        eSelect.value = selectValue || '';

        // Attach the event listener to update the selectValue when an option is selected
        eSelect.addEventListener('change', () => {
          selectValue = eSelect.value;
        });

        params.api.stopEditing();
        params.eGridCell.innerHTML = '';
        params.eGridCell.appendChild(eSelect);
        eSelect.focus();
      }

      function getValue() {
        return selectValue;
      }

      function isCancelBeforeStart() {
        return false;
      }

      function isCancelAfterEnd() {
        return false;
      }

      return {
        init,
        getGui: () => eCell,
        getValue,
        isCancelBeforeStart,
        isCancelAfterEnd
      };
    }


function onBtExport() {
  gridOptions.api.exportDataAsExcel();
}

// setup the grid after the page has finished loading
document.addEventListener('DOMContentLoaded', function () {
  var gridDiv = document.querySelector('#myGrid');
  new agGrid.Grid(gridDiv, gridOptions);
  gridOptions.api.setRowData({!!$custdata!!});
});


		</script>
@stop