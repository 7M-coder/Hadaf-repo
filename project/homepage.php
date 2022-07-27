<?php 
session_start();
// You need to load the Composer autoload file somewhere in your code before

if(isset($_SESSION['state']) === true && $_SESSION['state'] === true) {

    $name = "الصفحة الرئيسية";
    include 'init_front.php';

?>
<div class="container">
<div class="box d-flex flex-column align-items-center">

<section class="news">
        <h2>آخر الأخبار</h2>
        <div class="news-container">
        <?php 
        
        $connect = $con->prepare("SELECT * FROM posts WHERE category_id = 2 ORDER BY post_id LIMIT 3");

        $connect->execute();

        $record = $connect->fetchAll();

        $check = $connect->rowCount();

        if($check > 0) {

            foreach($record as $result) { ?>
                
                <div class="new">
                    <input type="hidden" name="postId" value="<?php echo $result["post_id"] ?>">
                    <div class="img-container">
                        <img alt="there is no cover" src="admin/uploads/posts/<?php echo $result["image"] ?>" alt="">
                    </div>

                    <div class="text-container">
                        <h3 class="headline"><?php echo $result["title"]; ?></h3>
                        <p class="description"><?php echo $result["description"]; ?></p>
                    </div>

                </div>

            <?php }

        } else {

            echo "<div class='msg-box error-msg'>لا توجد أخبار لعرضها</div>";
        }
        ?>

        </div>
</section>

<section class="recent-posts">
    <h3>آخر المنشورات</h3>

    <?php 

    $connect = $con->prepare("
    SELECT posts.*,
    users.username,
    users.name,
    users.avatar
    FROM posts
    INNER JOIN users
    ON users.user_id = posts.user_id
    WHERE category_id = 1
    ");
    
    $connect->execute();

    $record = $connect->fetchAll();

    $check = $connect->rowCount();

    if($check > 0) {

        foreach($record as $result)  { ?>
        
        <div class="post-box">
            
        <input type="hidden" class="post_id" value="<?php echo $result["post_id"]; ?>">
        <div class="img-container">
            <?php 
                    
                if(empty($result["image"]) === false) { ?>

                    <img src="admin\uploads\posts\<?php echo $result['image'] ?> ">
                <?php }
                    
            ?>
        </div>

        <div class="text-container">

            <div class="profile-info">

                <a class="avatar" href="profile.php?username=<?php echo $result["username"]; ?>">   
                    <img src="admin\uploads\avatars\<?php echo $result["avatar"]; ?>" alt="avatar">
                </a>

                <div class="username">
                    <?php 
					$post_timestamp = strtotime(date("Y/m/d H:i:s" ,strtotime($result["post_date"] . " " . $result["post_time"])));		 
					?>
					<p class="post-date"><?php echo date("M d", strtotime($result["post_date"])); ?></p>
					<p class="post-time"><?php echo calc_time($post_timestamp) ?></p>
                    <h4 id="post_username">@<?php echo $result["username"]; ?></h4>
                    <h4><?php echo $result["name"]; ?></h4>
                </div>
            </div>

            <div class="post-description">
                    <p><?php echo $result["description"]; ?></p>
            </div>

            <div class='likes'>
                <button class="like" type="submit" value="<?php echo $result["post_id"]; ?>"><i class="fa-solid fa-heart" data-color='#d1d1d1'></i></button>
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

        </div> <!--end of post-box-->
        <?php }

    } else {

        echo "<div class='msg-box error-msg'>لا توجد منشورات لعرضها</div>";
    }

        ?>

        <div class="post-likers">
        <?php 
        

        ?>    
        </div>
        
    </section>

    <section class="league-standing w-100 d-flex flex-column" dir='rtl'>
        <a href="" class="league-standing-title"><h3 class='fw-bold'>ترتيب الدوري</h3></a>
        <div class="table-container w-100">
            <table class="table w-100">
                <thead>
                    <tr>
                        <th class='d-none d-md-flex'>#</th>
                        <th></th>
                        <th>Club Name</th>
                        <th>pts</th>
                        <th class="">W</th>
                        <th class="">L</th>
                        <th class="">D</th>
                        <th class="d-none d-md-flex">PL</th>
                        <th class="d-none d-md-flex">Sequence</th>
                    </tr>
                </thead>
                
                <tbody>

                </tbody>
            </table>
        </div>


    </section>


    <!--add post-news-->
    <?php include "add-modal.php"; ?>

    <!--end of box-->
    </div>
    <!--end of container-->
    </div>

<?php include $tpl . 'footer.php'; ?>


<?php } else {

    header("Location: index.php");
    exit();
}

?>