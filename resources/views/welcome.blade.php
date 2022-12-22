<script src="https://code.jquery.com/jquery-3.6.2.js" integrity="sha256-pkn2CUZmheSeyssYw3vMp1+xyub4m+e+QK4sQskvuo4=" crossorigin="anonymous"></script>
<script >
    var jData =  JSON.stringify({UserName: 'manager', Password: 'manager', CompanyDB: '01_BTG_SAP_LIVE'});	
     $( document ).ready(function() {
        var SLServer="https://115.84.182.179:50000/b1s/v1/";
        connectSL();
function connectSL(){
  //check connect exits
  //login 
  $.ajax({
    // the URL for the request
    url: "https://115.84.182.179:50000/b1s/v1/Login",

    xhrFields: {
        withCredentials: true
    },
        
    // the data to send (will be converted to a query string)

    data: jData,

    // whether this is a POST or GET request
    type: "POST",

    // the type of data we expect back
    dataType : "json",

    // code to run if the request succeeds;
    // the response is passed to the function
    success: function( json ) {
        //SessionID = 
        //alert("Session ID - "+ json.SessionId);
        console .log("connect sap b1 success")

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
    });
</script>
@extends('adminlte::page', ['iFrameEnabled' => true])
