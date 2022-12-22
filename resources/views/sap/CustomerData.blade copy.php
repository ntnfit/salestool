<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://unpkg.com/jquery/dist/jquery.min.js"></script>
<script src="https://unpkg.com/gridjs-jquery/dist/gridjs.production.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script> 
    <title>Sale tools</title>
  </head>
  <body>
    <div id="spinner-div" class="pt-5">
    <div class="spinner-border text-primary" role="status">
    </div>
  </div>
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
      
      <button type="button" class="form-control btn btn-primary" id="export-excel">Excel</button>
    </div>
    </div>
    
</form>
<div id="wrapper"></div>
<div class="d-flex align-items-center invisible">
  <strong>Loading...</strong>
  <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
</div>

<script>
  // $( document ).ready(function() {
  //   $("#spinner-div").show();
  //   $.ajax({
	// 	// the URL for the request
	// 	url:"https://115.84.182.179:50000/b1s/v1/BusinessPartners?$select=CardCode,CardName,CardType&$filter=CardType eq 'cCustomer'&$count=true",
  //       xhrFields: {
  //           withCredentials: true
  //       }, 
	// 	// whether this is a POST or GET request
	// 	type: "get",
	// 	// the type of data we expect back
	// 	dataType : "json",
  //       headers:{
  //           "Prefer": "odata.maxpagesize=all",
  //       },
	// 	// code to run if the request succeeds;
	// 	// the response is passed to the function
	// 	success: function( response ) {
	// 		$("div#wrapper").Grid({
  //               search: true,
  //               pagination: true,
  //               sort: true,
  //               columns: ['CardCode', 'CardName', 'CardType'],
  //            data:response.value
  //           });
           
        
  //       filename='reports.xlsx';
  //        data=response.value;
  //        var ws = XLSX.utils.json_to_sheet(data);
  //        var wb = XLSX.utils.book_new();
  //        XLSX.utils.book_append_sheet(wb, ws, "People");
  //        XLSX.writeFile(wb,filename);
  //        $("#spinner-div").hide();
	// 	},

	// 	// code to run if the request fails; the raw request and
	// 	// status codes are passed to the function
	// 	error: function( xhr, status, errorThrown ) {
	// 		$('#connectedError').modal('show');
	// 		console.log( "Error: " + errorThrown );
	// 		console.log( "Status: " + status );
	// 		console.dir( xhr );
	// 		connected = false;
	// 	},

	// 	// code to run regardless of success or failure
	// 	complete: function( xhr, status ) {
	// 		//alert(complete);
	// 		// Nothing for now.
	// 	}
	// });
   
  // });

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