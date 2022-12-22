var SLServer="https://115.84.182.179:50000/b1s/v1/";


var jData =  JSON.stringify({UserName: 'manager', Password: 'manager', CompanyDB: '01_BTG_SAP_LIVE'});	
$(document).ready(function(){  
		connectSL();

});


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