<?php  

session_start();

$siteName = "الأخبار";
include "init_front.php";

if(isset($_SESSION["state"]) && $_SESSION["state"]) { ?>

<section class="news" dir="rtl">
    <div class="container">
    <?php if(!isset($_GET["post"])){ ?>
    <?php $news = get_specific_data("*", "posts", "category_id", 2, null, null, " JOIN users ON posts.user_id = users.user_id"); ?>
    
    <?php if(!$news) {
         echo $news;
         header("refresh:3;url=index.php"); 
         exit;

    } ?>
    <?php foreach($news as $theNew): ?>

        <div class="card w-100 mb-5 news-card">
            <div class="card-header d-flex">
                <input type="hidden" name="postId" value="<?php echo $theNew["post_id"]; ?>">
                <h5 class="title w-50 text-right text-white"><?php echo $theNew["title"]; ?></h5>
                <div class="author w-50 d-flex justify-content-end">
                    <p class="m-0 mx-2 text-right text-white"><?php echo $theNew["post_date"] ?></p>
                    <p class="m-0 text-right text-white"><?php echo $theNew["username"]; ?>@</p>
                </div>
            </div>
            <div class="image-box w-100">
                <img src="admin/uploads/posts/<?php echo $theNew["image"]; ?>" alt="">
            </div>
            <div class="card-body w-100">
                <p class="card-text d-block" ><?php echo $theNew["description"]; ?></p>
            </div>	
		</div>

    <?php endforeach; ?>
    <?php } // end of if($_GET["post])
    else { // single post
    
        if(is_numeric($_GET["post"])) {?>

        <?php $singlePost = get_specific_data("*", "posts", "post_id", $_GET["post"], null, null, " JOIN users ON posts.user_id = users.user_id"); ?>
        <?php if(!$singlePost) {

            $singlePost;
            header("refresh:3;url=index.php");
            exit;
        } ?>
        <div class="card w-100 mb-5 news-card">
            <div class="card-header d-flex">
                <input type="hidden" name="postId" value="<?php echo $singlePost[0]["post_id"]; ?>">
                <h5 class="title w-50 text-right text-white"><?php echo $singlePost[0]["title"]; ?></h5>
                <div class="author w-50 d-flex justify-content-end">
                    <p class="m-0 mx-2 text-right text-white"><?php echo $singlePost[0]["post_date"] ?></p>
                    <p class="m-0 text-right text-white"><?php echo $singlePost[0]["username"]; ?>@</p>
                </div>
            </div>
            <div class="card-body w-100">
                <p class="card-text d-block" ><?php echo $singlePost[0]["description"]; ?></p>
            </div>
            <div class="image-box w-100 p-2">
                <img src="admin/uploads/posts/<?php echo $singlePost[0]["image"]; ?>" alt="">
            </div>
					
		</div>      
        <?php } else {

        redirectToIndex();

        } ?>

    <?php }?>

    </div>    
</section>
<?php }

else {

    redirectToIndex();
}

include $tpl . "footer.php";
?>