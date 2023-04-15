@extends('adminlte::page')

@section('title', 'Saletotal')
    
  

    @section('content')
    <div id="spinner-div" class="pt-5">
    <div class="spinner-border text-primary" role="status">
    </div>
  </div>
  <form>
    <div class="container shadow min-vh-100 py-2">
        <h5>Sales stock total</h5>
      
        <div class="row justify-content-center">
            <div class="col-sm-4">
            <label for="Warehouse"><h6>WhsCode</h6></label>
            <select class="selectpicker" multiple data-live-search="true" id="whscode" name="whscode">
            </select>  
            </div>
            <div class="col-sm-3">
            <label for="team"><h6>Team</6></label>
            <select class="selectpicker" multiple data-live-search="true" id="team" name="team">
            </select>  
            </div>
            <div class="col-sm-3">
              <button type="button" class="form-control btn btn-primary" id="export-excel" onclick="getSelectValues()">Export Excel</button>
            </div>
          </div>
        </div>
       
</form>
<div id="wrapper"></div>
<div class="d-flex align-items-center invisible">
  <strong>Loading...</strong>
  <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
</div>
@section('css')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css">
<style>
  #spinner-div {
  position: fixed;
  display: none;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  text-align: center;
  background-color: rgba(255, 255, 255, 0.8);
  z-index: 2;
}
</style>
@endsection

@stop
@push('js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>
   
<script>
function getSelectValues() {
  var team = $('#team').val();
  var whscode = $('#whscode').val();
 if(whscode.length==0)
 {
  alert("Please choose one Warehouse!");
 }
 else if(whscode.length==1 && team.length==0)
 {
  alert("Please choose one team!");
 }
 else {
  var whs=whscode.join(',');
  var bincode='';

  $.ajax({
		type: 'GET',
    url: '{{ route('report.saletotal') }}',
		dataType : "json",
    data:{whscode:whs,bincode:team.join(',')},
		success: function( response ) {
      
      filename='saletotal.xlsx';
         data=response;
         var ws = XLSX.utils.json_to_sheet(data);
         var wb = XLSX.utils.book_new();
         XLSX.utils.book_append_sheet(wb, ws, "People");
         XLSX.writeFile(wb,filename);
    },
    error: function(response) {
      console.log(response)
    }
  });
 
 }
}
  
  $(document).ready(function() {
    //get whscode

    $.ajax({
          beforeSend: function (xhr) {
              xhr.setRequestHeader ("Authorization", "Basic "+'{{env('BSHeader')}}');
          },
		url:" https://"+'{{env('SAP_SERVER')}}'+":"+'{{env('SAP_PORT')}}'+"/b1s/v1/Warehouses?$select=WarehouseCode,WarehouseName",
        xhrFields: {
            withCredentials: true,
            rejectUnauthorized: false
        }, 
		// whether this is a POST or GET request
		type: "get",
		// the type of data we expect back
		dataType : "json",
        headers:{
            "Prefer": "odata.maxpagesize=all",
        },
		// code to run if the request succeeds;
		// the response is passed to the function
		success: function( response ) {
      
      var toAppend = '';
           $.each(response.value,function(i,o){
            toAppend += '<option value="'+o.WarehouseCode+'">'+o.WarehouseCode+'-'+o.WarehouseName+'</option>';
          });
          $('#whscode').append(toAppend);
          $('#whscode').selectpicker('refresh')
      //   $('#sessions').append(toAppend);
    },
    error: function( xhr, status, errorThrown ) {
      console.log( "Error: " + errorThrown );
      console.log( "Status: " + status );
      console.dir( xhr );
    }
  });
  });
</script>
<script>
    $('#whscode').on('change', function() {
      var whscode = $('#whscode').val();
      if(whscode.length==1)
      {
        var param=$( "#whscode option:selected" ).val();
       
        loadteamdata(param)
      }
      else
      {
        $('#team').empty();
        $('#team').selectpicker('refresh');
      
      }
  });
  function loadteamdata(param){
    $.ajax({
    url: '{{ route('bincode') }}', // Replace this with the actual route for the bincode API
    type: 'GET',
    dataType: "json",
    data: { WhsCode: param},
		success: function( response ) {

      var toAppend = '';
           $.each(response,function(i,o){
            toAppend += '<option value="'+o.AbsEntry+'">'+o.BinCode+'</option>';
          });
          $('#team').append(toAppend);
          $('#team').selectpicker('refresh')
      //   $('#sessions').append(toAppend);
    },
    error: function( xhr, status, errorThrown ) {
      
    }
  });
  }
  </script>

@endpush