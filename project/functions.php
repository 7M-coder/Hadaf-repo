<?php 

require "connect.php";

// alert
function success_alert($msg) {

    return "<div class='alert alert-success w-100 success'>$msg</div>";
}

function fail_alert($msg) {

    return "<div class='alert alert-danger w-100 fail'>$msg</div>";
}

// user
function get_pdo() {
    GLOBAL $con;
    return $con;
}

function get_user_id($username) {
    $con = get_pdo();
    $query = "SELECT user_id FROM users WHERE username = ?";
    $get_user_id = $con->prepare($query);
    $get_user_id->execute([$username]);

    $success = $get_user_id->rowCount();

    if($success):
        return $get_user_id->fetch()[0];
    endif;
}

function get_user_data($type, $userid) {

    $con = get_pdo();
    $query = "SELECT $type FROM users WHERE user_id = ?";
    $get_user_data = $con->prepare($query);
    $get_user_data->execute([$userid]);

    $success = $get_user_data->rowCount();
    if($success):
        return $get_user_data->fetch(PDO::FETCH_ASSOC);
    endif;
}

function update_user(int $userid, array $update_columns, array $data) {

    $con = get_pdo();

    $columns = array_column(get_columns("users"), "COLUMN_NAME");

    $columns = array_intersect($columns, $update_columns);
    $columns = implode("=?,", $columns);
    $columns .= "=?";
    $query = "UPDATE users SET $columns WHERE user_id = ?";
    $update = $con->prepare($query);
    $update->execute($data);
    $success = $update->rowCount();

    if($success) {

        return true;
    }
}

function get_columns($table) {

    $con = get_pdo();
    $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table'";
    $columns = $con->prepare($query);
    $columns->execute();

    return $columns->fetchAll(PDO::FETCH_ASSOC);
}

function get_data($type, $table, $order = null, $limit = null, $fetch = null) {

    $con = get_pdo();
    $query = "SELECT $type FROM $table $order $limit";
    $get_data = $con->prepare($query);
    $get_data->execute();

    $success = $get_data->rowCount();
    if($success) {

        if($fetch == 'rows') {

            return $get_data->fetchAll();
        }
        return $get_data->fetchAll(PDO::FETCH_COLUMN);
    } else {

        return `<div class="alert alert-danger>حدث خطأ ما! يرجى المحاولة مجددا.</div>"`;     
    }
}
function get_specific_data($type, $table, $condition, $value, $condition2 = null, $value2 = null, $join = null) {

    $con = get_pdo();
    $and = '';
    if($join == null) $join = '';

    if($condition !== null && $value !== null) {

        $condition = "WHERE $condition = ?";
    }

    if($condition2 !== null && $value2 !== null) {
        
        $condition2 = " AND $condition2 = ?";
    }

    $query = "SELECT " . $type . " FROM " . $table . $join . " " . $condition . $condition2;

    $get_data = $con->prepare($query);
    if($condition2 !== null && $value2 !== null) {
        $get_data->execute([$value, $value2]);
    } 
    else if($condition == null && $condition2 == null && $value == null && $value2 == null) {
        $get_data->execute();  
    }
    else {
        $get_data->execute([$value]);
    }
    $success = $get_data->rowCount();

    if($success > 0) {

        return $get_data->fetchAll(PDO::FETCH_ASSOC);
        
    } else {

        $error = "<div class='alert alert-danger' dir='rtl'>حدث خطأ ما! يرجى المحاولة مجددا.</div>";
        echo $error;
    }
}
function checkIf($selected, $table, $where, $value, $and = null) {

	$con = get_pdo();

	$connect = $con->prepare("SELECT $selected FROM $table WHERE $where = ? $and");

	$connect->execute(array($value));

	$count = $connect->rowCount();

	return $count;
}

function insertData($table, $columns, $values) {

    $con = get_pdo();
    $values_str = implode(",",array_keys($values));
    $query = "INSERT INTO $table($columns) VALUES($values_str)";
    $insert = $con->prepare($query);
    $success_alert = success_alert("تم النشر بنجاح!");
    $fail_alert = fail_alert("حدث خطأ ما!");
    $insert->execute($values);

    $success = $insert->rowCount();

    if($success) { // insert success

       return $success_alert;
    } 
    else { // insert error
        
        return $fail_alert;
    }
    
}


// pages
function redirectToIndex() {

    header("Location: index.php");
    exit;
}

function siteName() {

	GLOBAL $siteName;

	if(isset($siteName)) {

		echo $siteName;

	} else {

		echo "الهداف - الصفحة الرئيسية";
	}
}

// posts and news 

function calc_time($timestamp) {

    $current_time = time();

    $hours_passed = round(abs($current_time - $timestamp)/(60 * 60)) + 1;
    if($hours_passed < 24 && $hours_passed > 1) {

        $time_in_hours = $hours_passed;
        return $time_in_hours . "h";

    } else if($hours_passed < 1) { // calculate minutes
        
        $time_in_minutes = round(abs(($timestamp - $current_time) / 60));
        $counter = 0;
        foreach(range($time_in_minutes, 60) as $number) {

            $counter++;
        }

        return $counter . "m";

    } else { // calculate days

        $days = $current_time - $timestamp;
        $days = round($days / (60 * 60 * 24));

        return $days . "d";
    }
    


}