//handle filter data
var button = document.getElementById('search');
var customerGrpSelect = document.getElementById('fcustomergrp');
var stateSelect = document.getElementById('channel');
var countrySelect = document.getElementById('Route');
var productSelect = document.getElementById('Location');

button.addEventListener('click', function() {
  var customerGrpSelectedOptions = customerGrpSelect.querySelectorAll('option:checked');
  var customerGrpSelectedValues = [];
  if (customerGrpSelectedOptions.length > 0) {
    for (var i = 0; i < customerGrpSelectedOptions.length; i++) {
      customerGrpSelectedValues.push(customerGrpSelectedOptions[i].value);
    }
  }
  
  var stateSelectedOptions = stateSelect.querySelectorAll('option:checked');
  var stateSelectedValues = [];
  if (stateSelectedOptions.length > 0) {
    for (var i = 0; i < stateSelectedOptions.length; i++) {
      stateSelectedValues.push(stateSelectedOptions[i].value);
    }
  }
  
  var countrySelectedOptions = countrySelect.querySelectorAll('option:checked');
  var countrySelectedValues = [];
  if (countrySelectedOptions.length > 0) {
    for (var i = 0; i < countrySelectedOptions.length; i++) {
      countrySelectedValues.push(countrySelectedOptions[i].value);
    }
  }
  
  var productSelectedOptions = productSelect.querySelectorAll('option:checked');
  var productSelectedValues = [];
  if (productSelectedOptions.length > 0) {
    for (var i = 0; i < productSelectedOptions.length; i++) {
      productSelectedValues.push(productSelectedOptions[i].value);
    }
  }
  
  getPageData(customerGrpSelectedValues.join(', '), stateSelectedValues.join(', '), countrySelectedValues.join(', '), productSelectedValues.join(', '));
});

function getPageData(cusgrp, channel, route, location) {
    var data = {};
    if (cusgrp !== null) {
        data.cusgrp = cusgrp;
      }
    if (channel !== null) {
      data.channel = channel;
    }
    
    if (route !== null) {
      data.route = route;
    }
    
    if (location !== null) {
      data.location = location;
    }
    
    $.ajax({
      dataType: 'json',
      url: "/custmer-filter",
      type: 'GET',
      data: data
    }).done(function(data){
      $('#tablecustomer tbody').prepend(data.cust);
      
      $(".items").select2();
    });    
}
