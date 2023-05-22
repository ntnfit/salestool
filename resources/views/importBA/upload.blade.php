@extends('adminlte::page')
@section('title', 'Upload')
@section('content')
@if (count($errors) > 0)
<ul>
    @foreach ($errors->all() as $error)
        <li class="text-danger">{{ $error }}</li>
    @endforeach
</ul>
@endif
@if (session()->has('message'))
<div class="alert alert-success">
    {{ session()->get('message') }}
</div>
@endif
<div class="container mt-4">
    <div class="form-container">
        <h2>Upload Excel</h2>
        <form action="/upload-excel" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="file">Choose Excel File</label>
            <div class="selected-file">
                <span id="file-name">No file selected</span>
            </div>
            
            <input type="file" name="file" id="file" accept=".xlsx, .xls" onchange="displayFileName(this)">
            <button type="submit" id="upload-btn" onclick="handleUpload()">Upload</button>

        </form>
    </div>
</div>
@stop
@section('css')
<style>
     /* CSS for the form container */
     .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        /* CSS for the form elements */
        .form-container input[type="file"] {
            display: none;
        }

        .form-container label {
            display: inline-block;
            margin-bottom: 10px;
            cursor: pointer;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
        }

        .form-container label:hover {
            background-color: #0056b3;
        }

        .form-container .selected-file {
            margin-bottom: 10px;
        }

        .form-container button[type="submit"] {
            display: block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button[type="submit"]:hover {
            background-color: #218838;
        }
        .form-container button[type="submit"].uploading {
        position: relative;
        overflow: hidden;
    }

    .form-container button[type="submit"].uploading::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 1;
    }

    .form-container button[type="submit"].uploading::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        border: 2px solid #ffffff;
        border-top-color: #28a745;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 2;
    }

    @keyframes spin {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }
        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }
</style>
@stop
 @push('js')
 <script>
    // Function to display the selected file name
    function displayFileName(input) {
        if (input.files && input.files[0]) {
            var fileName = input.files[0].name;
            document.getElementById("file-name").textContent = fileName;
        } else {
            document.getElementById("file-name").textContent = "No file selected";
        }
    }
    function handleUpload() {
        var uploadBtn = document.getElementById('upload-btn');
        uploadBtn.classList.add('uploading');
        uploadBtn.disabled = true;
        setTimeout(function() {
            uploadBtn.closest('form').submit();
        }, 500);
    }
</script>
 @endpush
