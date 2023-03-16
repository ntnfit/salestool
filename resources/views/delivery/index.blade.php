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
 



 <style>
    @import url('https://fonts.googleapis.com/css2?family=Mulish:wght@400;500;600&display=swap');

*,::after,::before {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    width: 100%;
   
    justify-content: center;
    align-items: center;
    font-size: 14px;
    font-family: 'Mulish', sans-serif;
    background: #dfe3f2;
}

/* MAIN STYLE */

.card {
    width: 400px;
    height: auto;
    padding: 15px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
    border-radius: 5px;
    overflow: hidden;
    background: #fafbff;
}

.card .top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.card p {
    font-size: 0.9rem;
    font-weight: 600;
    color: #878a9a;
}

.card button {
    outline: 0;
    border: 0;
        -webkit-appearence: none;
	background: #5256ad;
	color: #fff;
	border-radius: 4px;
	transition: 0.3s;
	cursor: pointer;
	font-weight: 400;
	box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
	font-size: 0.8rem;
	padding: 8px 13px;
}

.card button:hover {
	opacity: 0.8;
}

.card button:active {
	transform: translateY(5px);
}

.card .drag-area {
	width: 100%;
	height: 160px;
	border-radius: 5px;
	border: 2px dashed #d5d5e1;
	color: #c8c9dd;
	font-size: 0.9rem;
	font-weight: 500;
	position: relative;
	background: #dfe3f259;
	display: flex;
	justify-content: center;
	align-items: center;
	user-select: none;
	-webkit-user-select: none;
	margin-top: 10px;
}

.card .drag-area .visible {
	font-size: 18px;
}
.card .select {
    color: #5256ad;
	margin-left: 5px;
	cursor: pointer;
	transition: 0.4s;
}

.card .select:hover {
	opacity: 0.6;
}

.card .container {
	width: 100%;
	height: auto;
	display: flex;
	justify-content: flex-start;
	align-items: flex-start;
	flex-wrap: wrap;
	max-height: 200px;
	overflow-y: auto;
	margin-top: 10px;
}

.card .container .image {
	width: calc(26% - 19px);
	margin-right: 15px;
	height: 75px;
	position: relative;
	margin-bottom: 8px;
}

.card .container .image img {
	width: 100%;
	height: 100%;
	border-radius: 5px;
}

.card .container .image span {
	position: absolute;
	top: -2px;
	right: 9px;
	font-size: 20px;
	cursor: pointer;
}

/* dragover class will used in drag and drop system */
.card .drag-area.dragover {
	background: rgba(0, 0, 0, 0.4);
}

.card .drag-area.dragover .on-drop {
	display: inline;
	font-size: 28px;
}

.card input,
.card .drag-area .on-drop, 
.card .drag-area.dragover .visible {
	display: none;
}
 </style>

<script>
    let files = [],
dragArea = document.querySelector('.drag-area'),
input = document.querySelector('.drag-area input'),
button = document.querySelector('.card button'),
select = document.querySelector('.drag-area .select'),
container = document.querySelector('.container');

const MAX_FILE_SIZE = 5*1024*1024; // Maximum file size in bytes (5MB)
const MAX_FILES = 5; // Maximum number of files
const ACCEPTED_FILE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp']; // Array of accepted file types

/** CLICK LISTENER */
select.addEventListener('click', () => input.click());

/* INPUT CHANGE EVENT */
input.addEventListener('change', () => {
	let file = input.files;
    let newFiles = input.files;
    // if user select no image
    if (file.length == 0) return;
         
	// Check if new files are valid and not duplicates
    for (let i = 0; i < newFiles.length; i++) {
        if (!isFileValid(newFiles[i])) continue;
        if (files.some(e => e.name == newFiles[i].name)) continue;
        
        files.push(newFiles[i]);
        if (files.length >= MAX_FILES) break;
      }
    
      // Create new FileList object and set it as input files
      let newFileList = new DataTransfer();
      for (let i = 0; i < files.length; i++) {
        newFileList.items.add(files[i]);
      }
      input.files = newFileList.files;

	showImages();
});

/** SHOW IMAGES */
function showImages() {
	container.innerHTML = files.reduce((prev, curr, index) => {
		return `${prev}
		    <div class="image">
			    <span onclick="delImage(${index})">&times;</span>
			    <img src="${URL.createObjectURL(curr)}" />
			</div>`
	}, '');
}

/* DELETE IMAGE */
function delImage(index) {
    var inputFile = document.getElementById('img');
   files.splice(index, 1);
   showImages();
   var newFileList = new DataTransfer();
  files.forEach(function(file) {
    newFileList.items.add(file);
  });
  inputFile.files = newFileList.files;

}

/* DRAG & DROP */
dragArea.addEventListener('dragover', e => {
	e.preventDefault()
	dragArea.classList.add('dragover')
})

/* DRAG LEAVE */
dragArea.addEventListener('dragleave', e => {
	e.preventDefault()
	dragArea.classList.remove('dragover')
});

/* DROP EVENT */
dragArea.addEventListener('drop', e => {
	e.preventDefault()
    dragArea.classList.remove('dragover');

	let file = e.dataTransfer.files;
	for (let i = 0; i < file.length; i++) {
		/** Check selected file is image and valid */
		if (!isFileValid(file[i])) continue;
		
		if (!files.some(e => e.name == file[i].name)) files.push(file[i])
		if (files.length >= MAX_FILES) break;
	}
	showImages();
});

function isFileValid(file) {
	// Check file size
	if (file.size > MAX_FILE_SIZE) {
		alert('The selected file is too large.');
		return false;
	}

	// Check file type
	if (!ACCEPTED_FILE_TYPES.includes(file.type)) {
		alert('The selected file type is not allowed.');
		return false;
	}

	return true;
}

</script>
@endsection