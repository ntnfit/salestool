@extends('adminlte::page')

@section('title', 'Add promotions')
@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)
@section('plugins.DateRangePicker', true)
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@section('content')

<div class="content">
    <form action="{{route('prosubmit')}}" method="post">
        @csrf
        <!-- header input  -->
        <div class="row">
        <x-adminlte-select label="Type Promotion" label-class="text-lightblue" name="protype" id="protype" fgroup-class="col-md-3" enable-old-support>
            @foreach($PromTypes as $promtype)
            <option value="{{$promtype->ProtypeID}}">{{$promtype->ProtypeName}}</option>
            @endforeach
        </x-adminlte-select>
        <div class="row col-md-9" style="float:right">
            <x-adminlte-input label="ID"  label-class="text-lightblue" name="ID" type="text" placeholder=""  fgroup-class="col-md-3" disabled>
            </x-adminlte-input>
            <x-adminlte-input label="Promotion name"  label-class="text-lightblue" name="promotionname" type="text"  fgroup-class="col-md-6" enable-old-support>
            </x-adminlte-input>
            <x-adminlte-input name="special" label="Special" type="checkbox" label-class="text-lightblue" value="special"  fgroup-class="col-xs-3" enable-old-support>
            </x-adminlte-input>   

            <x-adminlte-input  name="rouding" style="margin-left: 1.5rem;" label="Rouding" type="checkbox" label-class="text-lightblue" value="rouding"  fgroup-class="col-xs-3 rouding" enable-old-support>
            </x-adminlte-input> 
        </div>
    </div>
    <div class="row">
        <x-adminlte-date-range name="period" label="Period"  label-class="text-lightblue" fgroup-class="col-md-3"/>
        <x-adminlte-input label="Quantity"  label-class="text-lightblue" name="Quantity" type="number"  fgroup-class="col-md-3" enable-old-support>
        </x-adminlte-input>
        <x-adminlte-input label="Amount"  label-class="text-lightblue" name="Amount" type="number"  fgroup-class="col-md-3" enable-old-support>
        </x-adminlte-input>
        <x-adminlte-input label="Dis.Percent"  label-class="text-lightblue" name="dispercent" type="number" min="0" max="100" step="0.01"  fgroup-class="col-md-3" enable-old-support>
        </x-adminlte-input>
    </div>
    <!-- row input -->
    <div class="tab">
    <button class="tablinks" type="button" onclick="openTab(event, 'tab-1')" id="defaultOpen">Item</button>
    <button class="tablinks" type="button" onclick="openTab(event, 'tab-2')">Customer</button>
    </div>
    <div id="tab-1" class="tabcontent" >
    @php
   $configItem = [
       "title" => "Select ItemCode - ItemName",
       "liveSearch" => true,
       "liveSearchPlaceholder" => "Search...",
       "showTick" => true,
       "actionsBox" => true,
      
   ];
@endphp
        <div class="tableFixHead">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th class="header-label">ItemCode</th>   
                        <th class="header-label">Quantity</th>
                        <th class="header-label">UoM Codde</th>
                        <th class="header-label">Base Quantity</th>
                        <th class="header-label">Base UoM Code</th>
                        <th class="header-label">Action</th>
                    </tr>
                </thead>
            
                <tbody >
                    <tr class="Itemrows">
                        <td class="Itemselect"> 
                          <select class="selectpicker" id="Item" name="Item[]"  data-live-search="true"> 
                          <option value="" selected></option>        
                          @foreach($ItemCodes as $ItemCode)
                          <option value="{{$ItemCode->ItemCode}}">{{$ItemCode->ItemCode."--".$ItemCode->ItemName}}</option>
                          @endforeach
                          </select>
                        </td>
                        <td><input type="number" name="Qty[]" class="qty form-control"></td>
                        <td><select class="uom form-control" type="text" name="UomCode[]">
                            <option value="" selected></option>        
                            @foreach($Uoms as $Uom)
                            <option value="{{$Uom->UomEntry}}">{{$Uom->UomName}}</option>
                            @endforeach
                        </select></td>
                        <td><input type="number" name="BaseQty[]" class="form-control"></td>
                        <td><input type="text" name="BaseUom[]" class="form-control"></td>
                        <td><button type="button" class="btn btn-outline-danger" onclick="removeRow(this)">
                        <i class="fa fa-trash" aria-hidden="true"></i> </button></td>
                    </tr>
            </tbody>
            </table>
        </div>
    </div>
    <div id="tab-2" class="tabcontent">
    <div class="row datasearch">
   
   @php
   $config = [
       "title" => "Select data",
       "liveSearch" => true,
       "liveSearchPlaceholder" => "Search...",
       "showTick" => true,
       "actionsBox" => true,
      
   ];
@endphp
   <x-adminlte-select-bs id="fcustomergrp" name="fcustomergrp[]" label="Customer Group"
       label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-2" :config="$config" multiple data-selected-text-format="count > 3">
       <x-slot name="prependSlot">
           <div class="input-group-text bg-gradient-red">
               <i class="fas fa-tag"></i>
           </div>
       </x-slot>
   @foreach($Custgroups as $Custgroup)
   <option value="{{$Custgroup->Code}}">{{$Custgroup->Name}}</option>
   @endforeach
   </x-adminlte-select-bs>
  
   <x-adminlte-select-bs id="channel" name="channels[]" label="Channel"
       label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-2" :config="$config" multiple data-selected-text-format="count > 3">
       <x-slot name="prependSlot">
           <div class="input-group-text bg-gradient-red">
               <i class="fas fa-tag"></i>
           </div>
       </x-slot>
   @foreach($channels as $channel)
   <option value="{{$channel->Code}}">{{$channel->Name}}</option>
   @endforeach
   </x-adminlte-select-bs>
   <x-adminlte-select-bs id="Route" name="Routes[]" label="Route"
       label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-2" :config="$config" multiple data-selected-text-format="count > 3">
       <x-slot name="prependSlot">
           <div class="input-group-text bg-gradient-red">
               <i class="fas fa-tag"></i>
           </div>
       </x-slot>
   @foreach($Routes as $Route)
   <option value="{{$Route->Code}}">{{$Route->Name}}</option>
   @endforeach
   </x-adminlte-select-bs>
   <x-adminlte-select-bs id="Location" name="locations[]" label="Location"
       label-class="text-lightblue" igroup-size="sm" fgroup-class="col-md-2" :config="$config" multiple data-selected-text-format="count > 3">
       <x-slot name="prependSlot">
           <div class="input-group-text bg-gradient-red">
               <i class="fas fa-tag"></i>
           </div>
       </x-slot>
   @foreach($Locations as $Location)
   <option value="{{$Location->Code}}">{{$Location->Name}}</option>
   @endforeach
   </x-adminlte-select-bs>
   <x-adminlte-button class="btn-flat" id="search" style="float: right;margin-top: 34px;font-size: small;height: 31px;" type="button" label="search" theme="success" icon="fas fa-lg fa-save"/>
   </div>
        <div class="tableFixHead">
        <!-- các trường nhập liệu của tab customer -->
            <table class="table table-bordered" id="tablecustomer">
            <thead>
            <tr>
            <th class="header-label">CustomerCode</th>        
            <th class="header-label">Group Code</th>
           
            <th class="header-label">Channel</th>
            <th class="header-label">Route</th>
            <th class="header-label">Location</th>
            <th class="header-label">Action</th>
            </tr>
            </thead>
            <tr class="CustomList">
              <td>
              <x-adminlte-select class="selectpicker"  name="cus[]"  data-live-search="true"> 
                         <option value="" selected></option>        
                        @foreach($Customers as $Customer)
                        <option value="{{$Customer->CardCode}}">{{$Customer->CardCode."--".$Customer->CardName}}</option>
                        @endforeach
                         </x-adminlte-select>
            </td>
            <td><input type="text" name="group[]" class="form-control"></td>
            <td><input type="text" name="channel[]"></td>
            <td><input type="text" name="route[]"></td>
            <td><input type="text" name="location[]"></td>
            <td><button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="fa fa-trash" aria-hidden="true"></i> </button></td>
            </tr>
            </table>
        </div> 
    </div>
    <div class="row"id="promcontent" >
    <label class="text-lightblue" > Promotions Items </lable>
    <div class="tab" style="margin: 7px;"> 
        <div class="tableFixHead">
        <table class="table table-bordered" id="proitems">
        <thead>
        <tr>
            <th class="header-label">ItemCode</th>
           
            <th class="header-label">Quantity</th>
            <th class="header-label">UoM Codde</th>
            <th class="header-label">Base Quantity</th>
            <th class="header-label">Base UoM Code</th>
            <th class="header-label">Action</th>
        </tr>
        </thead>
        <tbody>
            <tr class="prorows">
              <td > 
                        <select class="selectpicker"  name="proitem[]"  data-live-search="true"> 
                         <option value="" selected></option>        
                        @foreach($ItemCodes as $ItemCode)
                        <option value="{{$ItemCode->ItemCode}}">{{$ItemCode->ItemCode."--".$ItemCode->ItemName}}</option>
                        @endforeach
                         </select>
                </td>
               
                <td><input type="number" name="proqty[]" class="form-control"></td>
                <td>
                <select class="form-control" name="prouomcode[]"> 
                    <option value="" selected></option>        
                   @foreach($Uoms as $Uom)
                   <option value="{{$Uom->UomEntry}}">{{$Uom->UomName}}</option>
                   @endforeach
                    </select>
                </td>
                <td><input type="number" name="probaseqty[]" class="form-control"></td>
                <td><x-adminlte-select type="text" name="probaseoum[]"></x-adminlte-select></td>
                <td><button type="button" class="btn btn-outline-danger" onclick="removeRow(this)"><i class="fa fa-trash" aria-hidden="true"></i> </button></td>
            </tr>
        </tbody>
        </table>
    </div>
    </div> 
  </div>
        <x-adminlte-button class="btn-flat" style="float: right; margin-top:10px"  id="submit" type="submit" label="Save" theme="success" icon="fas fa-lg fa-save"/>
    </form>
</div>

<!-- script js -->
<script>


function removeRow(button) {
  var row = button.parentNode.parentNode;
  row.parentNode.removeChild(row);
}
</script>
@stop 
@section('css')
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="{{asset('../css/tabformpromotion.css')}}">
<link rel="stylesheet" href="{{asset('../css/table.css')}}">
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<!-- handle tab -->
<script>
    function openTab(evt, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}

document.getElementById("defaultOpen").click();
</script>
<script>
// Lấy các phần tử HTML cần sử dụng
const typeSelect = document.getElementById('protype');
const myTable = document.getElementById('proitems');
const divcontent =document.getElementById('promcontent');
// Thêm event listener cho select option
typeSelect.addEventListener('change', function() {
  // Lấy giá trị được chọn
  const selectedValue = this.value;
  console.log(selectedValue);
  
  // Kiểm tra giá trị được chọn và ẩn hoặc hiển thị table tương ứng
  if (selectedValue === '2' || selectedValue === '3' ||selectedValue === '4') {
    myTable.style.display = 'none'; // Ẩn table
    divcontent.style.visibility  = 'hidden';
  } else if (selectedValue === '1') {
    myTable.style.display = 'table'; // Hiển thị table
    divcontent.style.visibility= 'visible';
  }
});

</script>
<script src="{{asset('../js/handlePromotion.js')}}"></script>
@endpush