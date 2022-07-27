<?php

session_start();

if(isset($_SESSION["state"]) && $_SESSION["state"] === true) {

    if($_SERVER["REQUEST_METHOD"] == "POST") {

        include 'connect.php';

        GLOBAL $con;
    
        //getting the user id of the client and the post owner
        $client_id = $_SESSION["userid"];

        if(isset($_COOKIE["post_username"]) && isset($_COOKIE["post_id"])) {
            $post_id = $_COOKIE["post_id"];
            $owner_username = $_COOKIE["post_username"];
            $owner_username = str_replace("@", "", $owner_username);
            $get_owner_id = $con->prepare("SELECT user_id FROM users WHERE username = ?");
            $get_owner_id->execute(array($owner_username));
            if($get_owner_id->rowCount() == 1) {
        
               $owner_id = $get_owner_id->fetchObject()->user_id;
        
            }
            
            if(isset($_GET["action"]) && $_GET["action"] == "like" ) {
                //getting the old likes
                $query = "SELECT likes FROM posts WHERE post_id = ?";
                $get_old_likes = $con->prepare($query);
                $get_old_likes->execute([$post_id]);
                if($get_old_likes->rowCount() == 1) {
    
                    $likers_ids = $get_old_likes->fetchObject()->likes;
                    $likers_ids = explode("+", $likers_ids);
                    // print_r($likers_ids);
                    $did_like = 0;
                    for($i = 0; $i < count($likers_ids); $i++) {
    
                        if($likers_ids[$i] == $client_id) {
    
                            $did_like = 1;
                            break;
    
                        }
                        
                    }
    
                    if($did_like == 0) { // first like for the client
    
                        $user_ids = "";
    
                        for($plus = 0; $plus < count($likers_ids); $plus++) {
    
                            $user_ids .= $likers_ids[$plus] . "+";
                        }
    
                        // putting the like
                        $put_like = $con->prepare("UPDATE posts SET likes = ? WHERE post_id = ?");
                        $put_like->execute([$user_ids . $client_id, $post_id]);
                        if($put_like->rowCount() == 1) {
                    
                            echo "success";
                    
                        } else {
                    
                            echo "falied";
                        }
    
                    } else { // if the client already did like this post then remove like
    
                        $dislike = 0;
                        for($j = 0; $j < count($likers_ids); $j++) {
    
                            if($likers_ids[$j] == $client_id) {
                                unset($likers_ids[$j]);
                                $dislike = 1;
                                break;
                            }
                        }
    
                        for($plus = 0; $plus < count($likers_ids); $plus++) {
    
                            $user_ids = $likers_ids[$plus];
                        }
    
                        if($dislike == 1) {
    
                            $query = "UPDATE posts SET likes = ? WHERE post_id = ?";
                            $remove_like = $con->prepare($query);
                            $remove_like->execute([$user_ids,$post_id]);
                            
                            if($remove_like->rowCount() == 1) {
    
                                echo "disliked successfuly";
                            }
                        }
                  }
                    
    
                }
            }
    
            else if(isset($_GET["action"]) && $_GET["action"] === "liked_posts") {
    
                //get user ids of the people who put like to every single post
                $query = "SELECT likes, post_id FROM posts WHERE likes LIKE '%+%'";
                $get_likes = $con->prepare($query);
                $get_likes->execute();
                if($get_likes->rowCount() > 0) {
    
                     $posts_likers = $get_likes->fetchAll();
                     print_r(json_encode($posts_likers));
                        
                }
    
            }
        }

    
    } else {

        header("Location: index.php");
        exit();
    }
    
} else {

    header("Location: index.php");
    exit();
}