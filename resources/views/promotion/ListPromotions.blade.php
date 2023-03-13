@extends('adminlte::page')

@section('title', 'List promotion')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>List promotions</h1>
@stop

@section('content')
    <p style="float:right"><a href="{{route('add-promotions')}}"><x-adminlte-button label="add new" theme="primary" icon="fas fa-plus"/> </a> </p>
    {{-- Setup data for datatables --}}
    
   
    @php

$heads = [
    'ID',
    ['label' => 'Promotion Type', 'width' => 28],
    ['label' => 'Promotion Name','width'=>20],
    ['label' => 'From Date','width'=>10],
    ['label' => 'To Date', 'width'=>10],
    ['label' => 'Quantity','width'=>5 ],
    ['label' => 'Total Amount', 'width' => 5],
    ['label' => 'Discount Percent', 'width' => 5],
    ['label' => 'DiscountAmt', 'width' => 5],
    ['label' => 'Special', 'width' => 5],
    ['label' => 'Rounding', 'width' => 5],
    ['label' => 'Date create', 'width' => 5],
    ['label' => 'Date update', 'width' => 5],
    ['label' => 'Action', 'width' => 5],
];



        $items = array();
        foreach($Promotionlist as $key) {
            $btnEdit = '<a href="">
                <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                <i class="fa fa-lg fa-fw fa-pen"></i>
            </button> </a>';
$btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                  <i class="fa fa-lg fa-fw fa-trash"></i>
              </button>';
$btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                   <i class="fa fa-lg fa-fw fa-eye"></i>
               </button>';
               $btnlink = '<a href="'.$key->ProId.'" >'.
               $key->ProId
            .'</button> </a>';
            $id=++$i;
        $items[] = [ '<nobr>'.$btnlink .'</nobr>',$key->ProtypeName, $key->PromotionName,$key->Fromdate,$key->ToDate,
        
        $key->Quantity,$key->TotalAmount,$key->DiscountPercent,$key->DiscountAmt,
        $key->Special,$key->Rouding,$key->DateCreate,$key->DateUpdate,'<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'];
        }
 
$config = [
   'data' => $items,
    'order' => [[0, 'desc']],
    'searching'=>true,      

    'columns' => [
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false],
        ['orderable' => false]
    ],
];
@endphp
<x-adminlte-datatable id="table2" :heads="$heads" head-theme="dark" :config="$config"
    striped hoverable bordered compressed/>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop