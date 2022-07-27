<?php 

session_start();

if(isset($_SESSION["state"]) && $_SESSION["state"]) {

    if($_SERVER["REQUEST_METHOD"] == "POST") {

        require "functions.php";
    
        $username = $_SESSION["username"];
    
        $userid = get_user_id($username);
    
        // input errors
        $errors = [];
    
        $comment = strip_tags($_POST["add-comment"]);
        $catid = $_POST["catid"];
        $postid = $_POST["postid"];
        
        if(!$comment) {
    
            $errors[] = "لا يمكن ترك حقل التعليق فارغا";
        }
    
    
        if(!empty($errors)) {
    
            foreach($errors as $error) {
    
                echo fail_alert($error);
            }
    
        } else {
    
            // insert

            date_default_timezone_set("Asia/Riyadh");
            $columns = array_column(get_columns("comment"), "COLUMN_NAME");
            unset($columns[0]);
            $columns = implode(",",$columns);
    
            $insert = insertData(
                "comment", 
                $columns,
                [
                    ":xcomment" => $comment, 
                    ":xspam" => 0, 
                    ":xuid" => $userid, 
                    ":xpostid" => $postid, 
                    ":xcatid" => $catid, 
                    ":xdate" => date("Y-m-d"), 
                    ":xtime" => date("H:i:s")]
            );
            
            // return the alerts of success or fail when inserting
            echo $insert;     
        }
    }
    
    else {
    
        redirectToIndex();
    }


} else {

    redirectToIndex();
}


?>