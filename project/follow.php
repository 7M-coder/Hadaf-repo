<?php

session_start();

if(isset($_SESSION["state"]) === true && $_SESSION["state"] === true) { 

    include "functions.php";    
    // اللي بيعمل فولو
    $userid = get_user_id($_SESSION["username"]);


    $client_username = $_COOKIE["username"];
    $client_userid = get_user_id($client_username);
    if($client_userid == $userid) {
        exit;
    }
    $user_data = get_user_data("followers, followers_ids", $client_userid);
    $followers_num = $user_data["followers"];
    $followers_ids = $user_data["followers_ids"];
    $followers_ids = explode(",", $followers_ids);


    if($_SERVER["REQUEST_METHOD"] === "POST") {

        if(!in_array($userid, $followers_ids)) { // follow

            $followers_num++;
            $followers_ids[] = $userid;
            $followers_ids = implode(",", $followers_ids);
            $update = update_user($client_userid, ["followers", "followers_ids"], [$followers_num++, $followers_ids, $client_userid]);
            
        } else { // unfollow
            
            $index = implode("",array_keys($followers_ids, $userid));
            unset($followers_ids[$index]);
            $followers_num -= 1;
            $followers_ids = count($followers_ids) == 1 ? implode("",$followers_ids) : implode(",", $followers_ids);
            $update = update_user($client_userid, ["followers", "followers_ids"], [$followers_num, $followers_ids, $client_userid]);

        }
    }

    else {

        header("Location: index.php");
        exit;
    }

} else {
    header("Location: index.php");
    exit();
}

