
<script>
/* Script written by Adam Khoury @ DevelopPHP.com */
/* Video Tutorial: http://www.youtube.com/watch?v=EraNFJiY0Eg */
function _(el){
	return document.getElementById(el);
}

function drag_drop(event) {
    event.preventDefault();
    /*alert(event.dataTransfer.files[0]);*/
    /*alert(event.dataTransfer.files[0].name);*/
    /*alert(event.dataTransfer.files[0].size+" bytes");*/
    /*  This is where to begin uploading the file with Ajax and upload progress bar to PHP script */
    /*   https://www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP */	  
		 
	var file = event.dataTransfer.files[0];
	 //alert(file.name+" | "+file.size+" | "+file.type);
	var formdata = new FormData();
  formdata.append("file1", file);
	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "file_upload_parser.php?id=<?php echo $id; ?>");
	ajax.send(formdata);


}


function uploadFile(){
	var file = _("file1").files[0];
	// alert(file.name+" | "+file.size+" | "+file.type);
	var formdata = new FormData();
	file1 = event.dataTransfer.files[0]
	formdata.append("file1", file);
	//formdata.append("file1", file);
	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "file_upload_parser.php");
	ajax.send(formdata);
}
function progressHandler(event){
	_("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
	var percent = (event.loaded / event.total) * 100;
	_("progressBar").value = Math.round(percent);
	_("status").innerHTML = Math.round(percent)+"% uploaded... please wait";
}
function completeHandler(event){
	_("status").innerHTML = event.target.responseText;
	_("progressBar").value = 0;
}
function errorHandler(event){
	_("status").innerHTML = "Upload Failed";
}
function abortHandler(event){
	_("status").innerHTML = "Upload Aborted";
}
</script>

<style>
#drop_zone {
    background-color: #EEE; 
    border: #999 2px dashed;
    width: 20px; 
    height: 20px;
    padding: 1px;
    font-size: 18px;
}
</style>

<div id="drop_zone" ondrop="drag_drop(event)" ondragover="return false"></div>


