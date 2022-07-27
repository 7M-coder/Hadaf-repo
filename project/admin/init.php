<?php 

include 'connect.php';
// template source
$tpl = 'includes/templates/';

//design
$js 	= 'design/js/';
$css	= 'design/css/';

//includes
$func	= 'includes/functions/';
include $func . 'admin_functions.php';

// header file is included here
include $tpl . 'header.php';
// ander <head> </head> <body>the navbar comes 
include $tpl . 'navbar.php';

?>