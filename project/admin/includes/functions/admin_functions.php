<?php 

//page name
function pageName() {

	global $name;

	if(isset($name)) {
		
		echo $name;

	} else {

		echo 'normal';
	} 
}

//redirect function
function redirect($dest = null,$seconds = 3) {

	/*if($dest === null) {

		$dest = 'index.php';

	} else {

		if(isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"])) {

			$dest = $_SERVER["HTTP_REFERER"];

		} else {

			$dest = 'index.php';
		}
	}*/

	if($dest == null) {

		$dest = 'index.php';

	} else {
		
		$dest = $_SERVER["HTTP_REFERER"];
	}

	header("refresh:$seconds; url=$dest");

	exit();
}

//count function 
function getStats($rowName, $table, $whereVal, $value) {

	GLOBAL $con;

	$connect = $con->prepare("SELECT COUNT($rowName) FROM $table WHERE $whereVal = ?");

	$connect->execute(array($value));

	$record = $connect->fetchColumn();

	return $record;
}


//connect function 
function connect($selected, $table, $where = null, $and = null, $ordring = null, $fetch = null) {

	GLOBAL $con;
	// [where] here isn't written cuz we may need it when we call this function or we won't need it
	$connect = $con->prepare("SELECT $selected FROM $table $where $and $ordring");

	$connect->execute();

	$count = $connect->rowCount();

	if($count > 0) {

		if($fetch == 'all') {

			$all = $connect->fetchAll();
	
			return $all;
	
		} else {
	
			$galb = $connect->fetch();
	
			return $galb;
		} 
	}


}


//check if exist function 
function checkIf($selected, $table, $where, $value, $and = null) {

	GLOBAL $con;
	
	$connect = $con->prepare("SELECT $selected FROM $table WHERE $where = ? $and");

	$connect->execute(array($value));

	// this is what make it check if function
	$count = $connect->rowCount();

	return $count;
}

function updateUser($firstVal = null, $secondVal = null, $thirdVal = null, $fourthVal = null, $fifthVal = null, $sixthVal = null, $whereVal) {

	GLOBAL $con;

	$update = $con->prepare("UPDATE users SET $firstVal, $secondVal, $thirdVal, $fourthVal, $fifthVal, $sixthVal $whereVal");
	
	$update->execute(array($firstVal, $secondVal, $thirdVal, $fourthVal, $fifthVal, $sixthVal, $whereVal));

	$count = $update->rowCount();

	return $count;
}	

?>