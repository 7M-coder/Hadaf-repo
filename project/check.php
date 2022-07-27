<?php 

session_start();

if(isset($_SESSION["state"]) && $_SESSION["state"]) {
    require "functions.php";
    if($_SERVER["REQUEST_METHOD"] === "POST") {
        
        $userid = get_user_id($_SESSION["username"]);
        $client_userid = get_user_id($_COOKIE["username"]);
        $userdata = get_user_data("followers_ids", $client_userid);
        $followers_ids = explode(",", $userdata["followers_ids"]);
        if(in_array($userid, $followers_ids)) {

            echo true;
        }
    
    }
}