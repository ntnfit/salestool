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
      console.log(data.success);
    });
  }
  

// Item row handle
var ItemTB = document.getElementById("myTable");

// add event listener for input changes in last row
ItemTB.addEventListener('input', function(event) {
  var lastRow = ItemTB.rows[ItemTB.rows.length - 1];
  // check if last row input has changed
  if (event.target.parentNode.parentNode == lastRow) {
    // check if last row input is not empty
    if (event.target.value.trim() != "") {
      var r = document.querySelectorAll('tbody .Itemrows')[0];
      var c = r.cloneNode(true);
      // clone selectpicker value and options
      var selectpicker = c.querySelector('.selectpicker');
      var selectOptions = selectpicker.querySelectorAll('option');
      var selectedOptionIndex = selectpicker.selectedIndex;
      selectpicker.innerHTML = '';
      for (var i = 0; i < selectOptions.length; i++) {
        var option = document.createElement('option');
        option.value = selectOptions[i].value;
        option.text = selectOptions[i].text;
        if (i === selectedOptionIndex) {
          option.selected = true;
        }
        selectpicker.appendChild(option);
      }
      // re-initialize the cloned selectpicker
      $(selectpicker).selectpicker();
      $(c).find('button[aria-owns="bs-select-1"]').remove();
      $(c).find('input').val('');
      ItemTB.appendChild(c);
    }
  }
});
// handle Item promtion row

var proitem = document.getElementById("proitems");
// Item row handle

// add event listener for input changes in last row
proitem.addEventListener('input', function(event) {
  var lastRow = proitem.rows[proitem.rows.length - 1];
  // check if last row input has changed
  if (event.target.parentNode.parentNode == lastRow) {
    // check if last row input is not empty
    if (event.target.value.trim() != "") {
      var r = document.querySelectorAll('tbody .prorows')[0];
      var c = r.cloneNode(true);
      // clone selectpicker value and options
      var selectpicker = c.querySelector('.selectpicker');
      var selectOptions = selectpicker.querySelectorAll('option');
      var selectedOptionIndex = selectpicker.selectedIndex;
      selectpicker.innerHTML = '';
      for (var i = 0; i < selectOptions.length; i++) {
        var option = document.createElement('option');
        option.value = selectOptions[i].value;
        option.text = selectOptions[i].text;
        if (i === selectedOptionIndex) {
          option.selected = true;
        }
        selectpicker.appendChild(option);
      }
      // re-initialize the cloned selectpicker
      $(selectpicker).selectpicker();
      $(c).find('button[aria-owns="bs-select-3"]').remove();
      $(c).find('input').val('');
      proitem.appendChild(c);
    }
  }
});
/// customer list
var customerItem = document.getElementById("tablecustomer");
// Item row handle

// add event listener for input changes in last row
customerItem.addEventListener('change', function(event) {
  var lastRow = customerItem.rows[customerItem.rows.length - 1];
  // check if last row input has changed
  if (event.target.parentNode.parentNode == lastRow) {
    // check if last row input is not empty
    if (event.target.value.trim() != "") {
      var r = document.querySelectorAll('tbody .CustomList')[0];
      var c = r.cloneNode(true);
      // clone selectpicker value and options
      var selectpicker = c.querySelector('.selectpicker');
      var selectOptions = selectpicker.querySelectorAll('option');
      var selectedOptionIndex = selectpicker.selectedIndex;
      selectpicker.innerHTML = '';
      for (var i = 0; i < selectOptions.length; i++) {
        var option = document.createElement('option');
        option.value = selectOptions[i].value;
        option.text = selectOptions[i].text;
        if (i === selectedOptionIndex) {
          option.selected = true;
        }
        selectpicker.appendChild(option);
      }
      // re-initialize the cloned selectpicker
      $(selectpicker).selectpicker();
      $(c).find('button[aria-owns="bs-select-2"]').remove();
      $(c).find('input').val('');
      customerItem.appendChild(c);
    }
  }
});

// Handle check UoM and calculation baseUo
// add event listener for input changes in last row
proitem.addEventListener('change', function(event) {
  let selectedValues;
  // Get the parent row element of the input element that triggered the event
  var rowElement = event.target.parentNode.parentNode;
  // Find all the input elements in the same row
  
  var selectUoM = rowElement.querySelectorAll('select[name="prouomcode[]"');
  // Loop through the input elements and log their values to the console
  selectUoM.addEventListener('change', function(event) {
    var inputElements = rowElement.querySelectorAll('select[name="proitem[]"');
    inputElements.forEach(function(inputElement) {
      selectedValues = inputElement.value;
    });
    if(selectedValues=='')
    {
      alert("Please choose ItemCode");
    }
    else 
    {
      console.log(selectedValues)
    }
    });
  
});


