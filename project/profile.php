<?php 

session_start();

if(isset($_SESSION["state"]) && $_SESSION["state"]) {

    $siteName = "الصفحة الشخصية";
    include "init_front.php";

        
        // if profile.php?username= username
        if(isset($_GET["username"])) { // your profile

            //check if the username [the user himself] exist

            $username = $_GET["username"];

            $check = checkIf("username", "users", "username", $username);

            // if the user exist in database
            if($check > 0) {

                $get_user_info = $con->prepare("SELECT * FROM users WHERE username = ?");

                $get_user_info->execute([$username]);

                $success = $get_user_info->rowCount();

                if($success > 0) { // if the SELECT executed successfuly
                    $infos = $get_user_info->fetchAll();
                    $my_userid = $infos[0]["user_id"];
                    ?>
                    

                <div class="profile-section">

                    <header class="header">
                        <img src="design/images/PageHeader.jpg" class="header-img img-responsive" alt="">
                    </header>
                    <div class="container">
                        <div class="row">
                            <div class="right-side col-lg-4 col-md-4 col-sm-12 col-xs-12">

                                <div class="avatar">
                                    <img class="img-responsive" src="admin\uploads\avatars\<?php echo isset($infos[0]['avatar']) ? $infos[0]['avatar'] : "blank.png";?>" alt="">
                                </div>

                                <div class="profile-btn">
                                    <?php if($username !== $_SESSION["username"]):  ?>
                                    <button class="follow" data-action="follow">متابعة</button>
                                    <?php endif; ?>
                                </div>

                                <div class="info d-flex">
                                    <h3 class="main-name"><?php echo $infos[0]["name"] ?></h3>
                                    <h4 class="main-username" id="username"><?php echo $infos[0]["username"]?></h4>
                                </div>
                                <div class="detailes d-flex">
                                    <span class="birithday"><i class="fas fa-birthday-cake"></i><?php echo $infos[0]["birthday"] ?></span>
                                    <span class="rigester"><i class="fas fa-clock"></i><?php echo $infos[0]["rigester_date"] ?></span>
                                    <span class="location text-uppercase"><i class="fas fa-compass"></i><?php echo $infos[0]["country"] ?></span>
                                </div>
                                <div class="bio d-flex">
                                    <p class="mx-3"><?php echo $infos[0]["bio"];?></p>
                                </div>

                                <div class="numbers flex">
                                    <span class="followers_nums">
                                        <h3>المتابَعين</h3>
                                        <h4>
                                            <?php 
                                            $userid = get_user_id($username);
                                            $followings = get_data("followers_ids", "users");
                                            $counter = 0;

                                            // print_r($followings);

                                            foreach($followings as $index => $f):
                                                if(strpos($f, "," . $userid)) {
                                                    $counter++;
                                                }
                                            endforeach;

                                            echo $counter;
                                            
                                            ?>
                                        </h4>
                                    </span>
                                    <span class="following_nums">
                                        <h3>المتابِعون</h3>
                                        <h4>
                                            <?php 
                                            $clinet_id = get_user_id($username);
                                            echo get_user_data("followers", $clinet_id)["followers"];
                                            
                                            ?>
                                        </h4>
                                    </span>
                                </div>
                                <?php 
                                
                                $get_user_id = $con->prepare("SELECT user_id FROM users WHERE username = ?");
                                $get_user_id->execute([$username]);
                                $success = $get_user_id->rowCount();

                                if($success) {

                                    $userid = $get_user_id->fetchAll()[0]["user_id"];

                                } else {

                                     redirectToIndex();
                                }



                                $query = "SELECT * FROM posts WHERE user_id = ? ORDER BY post_id DESC LIMIT 6";
                                $get_posts = $con->prepare($query);
                                $get_posts->execute(array($userid));
                                $success = $get_posts->rowCount();
                                if($success) {

                                    $all_posts = $get_posts->fetchAll();
                                
                                ?>
                                <div class="media">
                                    <h3>الوسائط</h3>
                                    <div class="row">
                                        <?php foreach($all_posts as $index => $post): ?>
                                        <?php if($post["image"]): ?>
                                        <div class="col-4 media-box">
                                            <img src="admin/uploads/posts/<?php echo $post["image"]; ?>" class="rounded float-start" alt="...">
                                        </div>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php } else {

                                    echo "<div class='alert alert-dark m-3 text-white' style='background:mediumpurple'>لا توجد وسائط لعرضها!</div>";
                                } ?>
                                <div class="suggest">
                                    <?php 
                                    
                                    $query = "SELECT * FROM `users` ORDER BY rand() LIMIT 3";

                                    $get_rand_users = $con->prepare($query);
                                    $get_rand_users->execute();
                                    $success = $get_rand_users->rowCount();

                                    if($success) { //got the random users successfully ?>

                                       <?php $rand_users = $get_rand_users->fetchAll(); ?>
                                        <h3>اقتراحات المتابعة</h3>
                                        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                    <div class="row row-cols-4 d-flex justify-content-center">
                                                        <?php foreach($rand_users as $user): ?>
                                                        <div class="col mx-lg-1">
                                                            <a href="profile.php?username=<?php echo $user["username"] ?>">
                                                                <div class="card user-container">
                                                                    <h5 class="text-center"><?php echo $user["name"] ?></h5>
                                                                    <h6 class="text-center"><?php echo $user["username"] ?>@</h6>
                                                                    <img src="admin/uploads/avatars/<?php echo $user["avatar"]?>" class="w-50 m-auto" alt="...">
                                                                </div>
                                                            </a>

                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>                                     
                                                </div>
                                                
                                            </div>
                                        </div>
                                    <?php }
                                    
                                    ?>
                                </div>
                        </div>

                        <section class="left-side col-md-8 col-12">

                            <ul class="nav nav-tabs mt-2" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" aria-controls="home" aria-selected="true">المنشورات</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#news" type="button" role="tab" aria-controls="profile" aria-selected="false">الأخبار</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#likes" type="button" role="tab" aria-controls="contact" aria-selected="false">الإعجابات</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#comments" type="button" role="tab" aria-controls="contact" aria-selected="false">التعليقات</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent">

                                <div class="tab-pane fade show active" id="posts" role="tabpanel" aria-labelledby="home-tab">
                                    <?php 
                                    
                                    $get_posts = $con->prepare("
                                        SELECT posts.*,
                                        users.username,
                                        users.name,
                                        users.avatar
                                        FROM posts
                                        INNER JOIN users
                                        ON users.user_id = posts.user_id
                                        WHERE posts.user_id = ?
                                        AND category_id = 1
                                    ");

                                    $get_posts->execute(array($my_userid));

                                    if($get_posts->rowCount() > 0) {
                                        
                                        $result = $get_posts->fetchAll();
                                    
                                    ?>
                                    <!--my posts-->
                                    <?php 
                                    foreach($result as $post) { ?>

                                    <div class="profile-posts posts-container">
                                    <input type="hidden" class="client-uid" value="<?php echo get_user_id($_SESSION["username"]); ?>">

                                    <input type="hidden" class="post_id" value="<?php echo $post["post_id"]; ?>">

                                    <div class="text-container">

                                    <div class="profile-info">

                                        <a class="avatar" href="profile.php?username=<?php echo $post["username"]; ?>">   
                                            <img src="admin\uploads\avatars\<?php echo $post["avatar"]; ?>" alt="avatar">
                                        </a>

                                        <div class="username">
                                            <h4><?php echo $post["name"]; ?></h4>
                                            <h4><?php echo $post["username"]; ?>@</h4>
                                            <?php 
					                    	$post_timestamp = strtotime(date("Y/m/d H:i:s" ,strtotime($post["post_date"] . " " . $post["post_time"])));
						 
						                    ?>
						                    <p class="post-date"><?php echo date("M d", strtotime($post["post_date"])); ?></p>
						                    <p class="post-time"><?php echo calc_time($post_timestamp) ?></p>
                                        </div>
                                    </div>

                                    <div class="post-description">
                                            <p><?php echo $post["description"]; ?></p>
                                    </div>

                                    <div class='likes'>
                                        <button class="like" type="submit" value="<?php echo $post["post_id"]; ?>"><i class="fa-solid fa-heart" data-color='#d1d1d1'></i></button>
                                        <button class="likers">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-heart" viewBox="0 0 16 16">
                                            <path d="M9 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 8c0 1 1 1 1 1h10s1 0 1-1-1-4-6-4-6 3-6 4Zm13.5-8.09c1.387-1.425 4.855 1.07 0 4.277-4.854-3.207-1.387-5.702 0-4.276Z"/>
                                        </svg>
                                        </button>
                                        <button class="comments">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-fill" viewBox="0 0 16 16">
                                                <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
                                            </svg>
                                        </button>
                                    </div>

                                    </div>
                                    <?php 
                                            
                                        if(empty($post["image"]) === false) { ?>
                                        
                                        <div class="img-container">
                                            <img class="rounded" src="admin\uploads\posts\<?php echo $post['image'] ?> ">                                        
                                        </div>
                                        <?php }
                                            
                                    ?>

                                    </div>
                                    <?php } 
                                    } else { 

                                        echo "<div class='alert alert-light'>لا توجد منشورات بعد!</div>";
                                    }

                                    ?>

                                </div>
                                
                                <!--my news-->
                                <div class="tab-pane fade" id="news" role="tabpanel" aria-labelledby="profile-tab">
                                <?php 
                                    
                                    $get_news = $con->prepare("
                                        SELECT posts.*,
                                        users.username,
                                        users.name,
                                        users.avatar
                                        FROM posts
                                        INNER JOIN users
                                        ON users.user_id = posts.user_id
                                        WHERE posts.user_id = ?
                                        AND category_id = 2
                                    ");

                                    $get_news->execute(array($my_userid));

                                    if($get_news->rowCount() > 0) {
                                        
                                        $result = $get_news->fetchAll();
                                    
                                    ?>
                                <?php
                                foreach($result as $news) { ?>
                                <div class="profile-posts news-container">
                                <input type="hidden" value="<?php echo $news["post_id"]; ?>">
                                <div class="text-container">

                                <div class="profile-info">

                                    <a class="avatar" href="profile.php?username=<?php echo $news["username"]; ?>">   
                                        <img src="admin\uploads\avatars\<?php echo $news["avatar"]; ?>" alt="avatar">
                                    </a>

                                    <div class="username">
                                        <h4><?php echo $news["name"]; ?></h4>
                                        <h4><?php echo $news["username"]; ?>@</h4>
                                    </div>
                                </div>

                                <div class="post-description">
                                        <p><?php echo $news["description"]; ?></p>
                                </div>

                                </div>
                                <?php 
                                        
                                    if(empty($news["image"]) === false) { ?>
                                    
                                    <div class="img-container">
                                        <img src="admin\uploads\posts\<?php echo $news['image'] ?> ">                                        
                                    </div>
                                    <?php }
                                        
                                ?>

                                </div>

                                <?php } 
                                
                                } else { 

                                    echo "<div class='alert alert-light'>لا توجد أخبار بعد!</div>";
                                }
                                ?>
                                </div>
                                
                                <div class="tab-pane fade" id="likes" role="tabpanel" aria-labelledby="contact-tab">
                                <?php 
                                    $userid = get_user_id($username);
                                    $get_liked_posts = $con->prepare("SELECT * FROM posts JOIN users ON users.user_id = posts.user_id WHERE likes LIKE '%$userid%'");
                                    $get_liked_posts->execute(); 
                                    $success = $get_liked_posts->rowCount();
                                    if($success) { 
                                        $posts = $get_liked_posts->fetchAll();
                                        foreach($posts as $post):
                                    ?>

                                    <div class="profile-posts posts-container">
                                    <input type="hidden" class="client-uid" value="<?php echo get_user_id($_SESSION["username"]); ?>">

                                    <input type="hidden" class="post_id" value="<?php echo $post["post_id"]; ?>">

                                    <div class="text-container">

                                    <div class="profile-info">

                                        <a class="avatar" href="profile.php?username=<?php echo $post["username"]; ?>">   
                                            <img src="admin\uploads\avatars\<?php echo $post["avatar"]; ?>" alt="avatar">
                                        </a>

                                        <div class="username">
                                            <h4><?php echo $post["name"]; ?></h4>
                                            <h4><?php echo $post["username"]; ?>@</h4>
                                            <?php 
					                    	$post_timestamp = strtotime(date("Y/m/d H:i:s" ,strtotime($post["post_date"] . " " . $post["post_time"])));
						 
						                    ?>
						                    <p class="post-date"><?php echo date("M d", strtotime($post["post_date"])); ?></p>
						                    <p class="post-time"><?php echo calc_time($post_timestamp) ?></p>
                                        </div>
                                    </div>

                                    <div class="post-description">
                                            <p><?php echo $post["description"]; ?></p>
                                    </div>

                                    <div class='likes'>
                                        <button class="like" type="submit" value="<?php echo $post["post_id"]; ?>"><i class="fa-solid fa-heart" data-color='#d1d1d1'></i></button>
                                        <button class="likers">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-heart" viewBox="0 0 16 16">
                                            <path d="M9 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 8c0 1 1 1 1 1h10s1 0 1-1-1-4-6-4-6 3-6 4Zm13.5-8.09c1.387-1.425 4.855 1.07 0 4.277-4.854-3.207-1.387-5.702 0-4.276Z"/>
                                        </svg>
                                        </button>
                                        <button class="comments">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-fill" viewBox="0 0 16 16">
                                                <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
                                            </svg>
                                        </button>
                                    </div>

                                    </div>
                                    <?php 
                                            
                                        if(empty($post["image"]) === false) { ?>
                                        
                                        <div class="img-container">
                                            <img class="rounded" src="admin\uploads\posts\<?php echo $post['image'] ?> ">                                        
                                        </div>
                                        <?php }
                                            
                                    ?>

                                    </div>

                                    <?php 
                                    endforeach; 

                                    } else {

                                        echo "<div class='alert alert-light'>لا توجد إعجابات بعد</div>";
                                    }
                                    
                                ?>

                                
                                    
                                </div>

                                <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="contact-tab">
                                    <?php 
                                    
                                    $comments = get_comments($_SESSION["username"]); 
                                    
                                    ?>
                                    <section class="show-comment mt-0">
                                    <?php if(is_array($comments)): ?>
			                        <?php foreach($comments as $index => $comment): ?>
                                    
			                        <div class="comment-box comments-container">
                                        <input type="hidden" value="<?php echo $comment["post_id"] ?>">
                                        <div class="text-container justify-content-center">
                                            <div class="profile-info">
                                                <a class="avatar" href="profile.php?username=<?php echo $comment["username"]; ?>">   
                                                    <img src="admin\uploads\avatars\<?php echo $comment["avatar"]; ?>" alt="avatar">
                                                </a>

                                                <div class="username justify-content-start" >
                                                    <h4><?php echo $comment["name"]; ?></h4>
                                                    <h4 id="comment_username" class="mx-2"><?php echo $comment["username"]; ?>@</h4>
                                                    <?php 
                                                    $comment_timestamp = strtotime(date("Y/m/d H:i:s" ,strtotime($comment["date"] . " " . $comment["time"])));
                                                    
                                                    ?>
                                                    <p class="comment-time"><?php echo calc_time($comment_timestamp) ?></p>
                                                    <p class="comment-date"><?php echo date("M d", strtotime($comment["date"])); ?></p>

                                                </div>
                                            </div>

                                            <div class="comment-description">
                                                    <p class=""><?php echo $comment["comment"]; ?></p>
                                            </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php else: echo "<div class='alert alert-light'>لا توجد تعليقات بعد</div>" ?>
                                <?php endif; ?>
                                <!--end of show comments section-->
		                        </section>                                        
                                        
                                <!--end of tab pane-->
                                </div>

                                <!--end of tab content-->
                            </div>

                            <!--end of left side-->
                        </section>

                        <?php include "add-modal.php"; ?>
                    </div>      
                </div>

                    
                <?php } else { // if getting user data is failed

                    echo "<div class='alert alert-danger'>حدث خطأ ما! يرجى المحاولة مجددا</div>";
                } 
                

            } else {
                    
                  // if the user isn't exist in database
                  redirectToIndex();
            }

        } else {    // if profile.php?not username

            header("Location: index.php");
            exit();
        }

    include $tpl . "footer.php";

} else {
    header("Location:index.php");
}

