<?php 

//connect 
include 'connect.php';

//design 
$js 	= 'design/js/';
$css 	= 'design/css/';

//functions
include  "functions.php";

//template
$tpl = 'includes/template/';
include $tpl . 'header.php';
if(!isset($navbar)) {
	
	include $tpl . 'navbar.php';
}

?>