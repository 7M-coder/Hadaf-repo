<?php

session_start();

if(isset($_SESSION["state"]) && $_SESSION["state"]) {

    if($_SERVER["REQUEST_METHOD"] == "POST") {

        require "functions.php";
    
        $username = $_SESSION["username"];
    
        $userid = get_user_id($username);
    
        // input errors
        $errors = [];
    
        $post_desc = strip_tags($_POST["description"]);
    
        if(array_key_exists("title", $_POST)) {
    
           if(!$_POST["title"]) {
    
                $errors[] = "يرجى اختيار عنوان للخبر";
    
           }  else {
    
                $title = strip_tags($_POST["title"]);
           }
    
        } else {
    
            $title = null;
        }
    
        $category_id = $_POST["type"];
        
        if(!$post_desc) {
    
            $errors[] = "لا يمكن ترك حقل المنشور فارغا";
        }
    
        $img_name = $_FILES["image"]["name"];
        $img_tmp = $_FILES["image"]["tmp_name"];
        $img_size = $_FILES["image"]["size"];
        $img_type = substr( $_FILES["image"]["type"], strpos( $_FILES["image"]["type"], "/") + 1);
        $valid_types = array("jpg", "jpeg", "png", "jfif");
        if($img_name && !in_array($img_type, $valid_types)) {
    
            $errors[] = "امتداد الصورة غير صالح";
        }
    
        if(!empty($errors)) {
    
            foreach($errors as $error) { 

                /* 
                    input errors display
                */
    
                echo fail_alert($error);
            }
    
        } else {
    
            // insert
    
            if($img_name) {
    
                $img_name = rand(0, 1000000) . "_" . $img_name;
                move_uploaded_file($img_tmp, "admin/uploads/posts/" . $img_name);
            } 
            else {
                $img_name = "";
            }
    
    
            date_default_timezone_set("Asia/Riyadh");
            $columns = array_column(get_columns("posts"), "COLUMN_NAME");
            unset($columns[0]);
            $columns = implode(",",$columns);
    
            $insert = insertData(
                "posts", 
                $columns,
                [
                    ":xtitle" => $title, 
                    ":xdesc" => $post_desc, 
                    ":ximg" => $img_name, 
                    ":xlike" => 0, 
                    ":xuid" => $userid, 
                    ":xcatid" => $category_id, 
                    ":xdate" => date("Y-m-d"), 
                    ":xtime" => date("H:i:s")]
            );
    
            echo $insert;
    
    
    
    
            
        }
    }
    
    else {
    
        redirectToIndex();
    }


} else {


    redirectToIndex();
}

