<?php
include('mgr.functions.php');
//path to storage
$storage = '../assets/incoming/web/';
// clean filename
$cfilename = clean_filename(utf8_decode($_FILES['Filedata']['name']));
//path name of file for storage
$uploadfile = "$storage/" . basename( $cfilename );
//if the file is moved successfully
if ( move_uploaded_file( $_FILES['Filedata']['tmp_name'] , $uploadfile ) ) {
  echo( '1 ' . $_FILES['Filedata']['name']);
 //file failed to move
}else{
  echo( '0');
}
?>
