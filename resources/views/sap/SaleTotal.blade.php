<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css">
    <title>Sale tools</title>
  </head>
  <body>
    <div id="spinner-div" class="pt-5">
    <div class="spinner-border text-primary" role="status">
    </div>
  </div>
  <form>
    <div class="container shadow min-vh-100 py-2">
        <h5>Sales stock total</h5>
        <div class="row justify-content-center">
            <div class="col-lg-3 col-sm-6">
                <label for="startDate"><h6>From Date</6></label>
                <input id="startDate" class="form-control" type="date" name="fromdate" />
                <span id="startDateSelected"></span>
            </div>
            <div class="col-lg-3 col-sm-6">
                <label for="endDate"><h6>To Date</6></label>
                <input id="endDate" class="form-control" type="date" name="todate" />
                <span id="endDateSelected"></span>
            </div>
            
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-3 col-sm-6">
            <label for="Warehouse"><h6>Warehouse</h6></label>
            <select class="selectpicker" multiple data-live-search="true" id="whscode" name="whscode">
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
                <option value="4">Four</option>
            </select>  
            </div>
            <div class="col-lg-3 col-sm-6">
            <label for="team"><h6>Team</6></label>
            <select class="selectpicker" multiple data-live-search="true" id="team" name="team">
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
                <option value="4">Four</option>
                <option value="5">Four</option>
                <option value="6">Four</option>
            </select>  
            </div>
        </div>
        <div class="row justify-content-center">
        <div class="col-lg-6 col-sm-12">
            <button type="" class="form-control btn btn-primary" id="export-excel">Export Excel</button>
            </div>
        </div>
    </div> 
</form>
<div id="wrapper"></div>
<div class="d-flex align-items-center invisible">
  <strong>Loading...</strong>
  <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
</div>

<script>
  $(document).ready(function() {
  
    let startDate = document.getElementById('startDate')
    let endDate = document.getElementById('endDate')

    startDate.addEventListener('change',(e)=>{
    let startDateVal = e.target.value
   
    })

    endDate.addEventListener('change',(e)=>{
    let endDateVal = e.target.value
    })  
  });
  function sCustomer() {
    $("#spinner-div").show();
    var e = document.getElementById("channel").value;
    
   if(e==0)
   {
        $.ajax({
		// the URL for the request
		url:"https://115.84.182.179:50000/b1s/v1/sml.svc/CUSTOMERDATA",
        xhrFields: {
            withCredentials: true
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
			$("div#wrapper").Grid({
                search: true,
                pagination: true,
                sort: true,
                with:'fix-content',
                columns: ['CardCode', 'CardName', 'ShortName','storeID','Street','TaxCode','Channel','Route','PGCode','PGName','SalSupCode',
              'SalSupName','TeamLeaderCode','TeamLeaderName','KA_ASM_Code','KA_ASM_Name','SalManagerCode','SaLManager_Name','Group','DefaultWhs'],
             data:response.value
            });
           
            $("#spinner-div").hide();
        filename='reports.xlsx';
         data=response.value;
         var ws = XLSX.utils.json_to_sheet(data);
         var wb = XLSX.utils.book_new();
         XLSX.utils.book_append_sheet(wb, ws, "People");
         XLSX.writeFile(wb,filename);
         $("#spinner-div").hide();
		},

		// code to run if the request fails; the raw request and
		// status codes are passed to the function
		error: function( xhr, status, errorThrown ) {
			$('#connectedError').modal('show');
			console.log( "Error: " + errorThrown );
			console.log( "Status: " + status );
			console.dir( xhr );
			connected = false;
		},

		// code to run regardless of success or failure
		complete: function( xhr, status ) {
			//alert(complete);
			// Nothing for now.
		}
	});
   
   }
   
}



</script>
<!-- MDB -->

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
  </body>
</html>