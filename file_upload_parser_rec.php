<?php
$id = $_REQUEST["id"];

$fileName = $id.".jpg";//.substr($_FILES["file1"]["type"],12,3);//$_FILES["file1"]["name"]; // The file name
	 alert($fileName);

$fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
$fileType = $_FILES["file1"]["type"]; // The type of file it is
$fileSize = $_FILES["file1"]["size"]; // File size in bytes
$fileErrorMsg = $_FILES["file1"]["error"]; // 0 for false... and 1 for true
if (!$fileTmpLoc) { // if file not chosen
    echo "ERROR: Please browse for a file before clicking the upload button.";
    exit();
}
if(move_uploaded_file($fileTmpLoc, "id_fotos/$fileName")){
    echo "$fileName upload is complete";
} else {
    echo "move_uploaded_file function failed";
}
?>
