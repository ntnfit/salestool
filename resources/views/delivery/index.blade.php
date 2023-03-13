@extends('adminlte::page')

@section('title', 'List delivery')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>List delivery</h1>
@stop

@section('content')
@if(count($errors) >0)
            <ul>
                @foreach($errors->all() as $error)
                    <li class="text-danger">{{ $error }}</li>
                @endforeach
            </ul>
 @endif 


 <form action="" method="get" enctype="multipart/form-data">
        <div class="card">
           
            <div class="drag-area">
                <span class="visible">
                    Drag & drop image here or
                    <span class="select" role="button">Browse</span>
                </span>
                <span class="on-drop">Drop images here</span>
                <input name="file[]" type="file" id="img" accept="image/*" class="file" multiple />
            </div>
            <div class="top">
               
                <button type="submit"  onclick = "uploadFile()" >Save & update status</button>
            </div>
            <!-- IMAGE PREVIEW CONTAINER -->
            <div class="container"></div>
        </div>
    </form>
    <script src="app.js"></script>

@endsection