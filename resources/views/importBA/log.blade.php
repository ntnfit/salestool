@extends('adminlte::page')
@section('title', 'Log')
@section('content')
    <div class="container mt-4">
        <h2>Response Data</h2>
        <div class="import-button">
            <a href="{{ route('import.upload') }}" class="btn btn-primary">Import</a>
        </div>
        <table class="table table-bordered response-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>DocNum</th>
                    <th>Status</th>
                    <th>ErrorCode</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datas as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $data->DocNum }}</td>
                        <td>{{ $data->Status }}</td>
                        <td>{{ $data->Error_code }}</td>
                        <td>{{ $data->Message }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop
@section('css')
<style>
    /* CSS for the table */
    .response-table {
         width: 100%;
         border-collapse: collapse;
     }

     .response-table th,
     .response-table td {
         padding: 10px;
         text-align: center;
         border: 1px solid black;
     }

     /* CSS for the import button */
     .import-button {
         margin-bottom: 20px;
     }
 </style>
@stop
