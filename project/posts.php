<?php 

session_start();


if(isset($_SESSION["state"]) && $_SESSION["state"]) { 
	
	$siteName = "المنشورات";
	include 'init_front.php';

	$userid = get_user_id($_SESSION["username"]);
	$posts = get_specific_data("*", "posts", "posts.category_id", 1, null, null, " JOIN users ON users.user_id = posts.user_id");
?>


	<?php if(!isset($_GET["post"])): // all posts view?>

		<?php if(!$posts) { // getting post error

			header("refresh:3;url=index.php");
			exit;
		} ?>
		<div class="container">
			
			<section class="recent-posts">

			<?php foreach($posts as $post): ?>

				<div class="post-box">
					<input type="hidden" class="post_id" value="<?php echo $post["post_id"]; ?>">
					<div class="img-container">
						<?php 
								
							if(empty($result["image"]) === false) { ?>
			
								<img src="admin\uploads\posts\<?php echo $post['image'] ?> ">
							<?php }
								
						?>
					</div>
			
					<div class="text-container">
			
						<div class="profile-info">
			
							<a class="avatar" href="profile.php?username=<?php echo $post["username"]; ?>">   
								<img src="admin\uploads\avatars\<?php echo $post["avatar"]; ?>" alt="avatar">
							</a>
			
							<div class="username">
								<?php 
								$post_timestamp = strtotime(date("Y/m/d H:i:s" ,strtotime($post["post_date"] . " " . $post["post_time"])));
								
								?>
								<p class="post-date"><?php echo date("M d", strtotime($post["post_date"])); ?></p>
								<p class="post-time"><?php echo calc_time($post_timestamp) ?></p>
								<h4 id="post_username">@<?php echo $post["username"]; ?></h4>
								<h4><?php echo $post["name"]; ?></h4>
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
				</div> <!--end of post-box-->

			<?php endforeach ?>

			</section>
		</div>

	<?php else: // single post view ?>

	<?php
	// getting the post data
	$post = get_specific_data("*", "posts", "posts.category_id", 1, "post_id", $_GET["post"], " JOIN users ON users.user_id = posts.user_id");

	if(!$post) { // getting post error

		$post;
		header("refresh:3;url=index.php");
		exit;
	}
	?>
	
	<div class="container recent-posts">
		<div class="post-box">
			<input type="hidden" class="post_id" value="<?php echo $post[0]["post_id"]; ?>">
			<div class="img-container">
				<?php 
						
					if(empty($post["image"]) === false) { ?>
	
						<img src="admin\uploads\posts\<?php echo $post[0]['image'] ?> ">
					<?php }
						
				?>
			</div>
	
			<div class="text-container">
	
				<div class="profile-info">
	
					<a class="avatar" href="profile.php?username=<?php echo $post[0]["username"]; ?>">   
						<img src="admin\uploads\avatars\<?php echo $post[0]["avatar"]; ?>" alt="avatar">
					</a>
	
					<div class="username">
						<?php 
						$post_timestamp = strtotime(date("Y/m/d H:i:s" ,strtotime($post[0]["post_date"] . " " . $post[0]["post_time"])));
						
						?>
						<p class="post-date"><?php echo date("M d", strtotime($post[0]["post_date"])); ?></p>
						<p class="post-time"><?php echo calc_time($post_timestamp) ?></p>
						<h4 id="post_username">@<?php echo $post[0]["username"]; ?></h4>
						<h4><?php echo $post[0]["name"]; ?></h4>
					</div>
				</div>
	
				<div class="post-description">
						<p><?php echo $post[0]["description"]; ?></p>
				</div>
	
				<div class='likes'>
					<button class="like" type="submit" value="<?php echo $post[0]["post_id"]; ?>"><i class="fa-solid fa-heart" data-color='#d1d1d1'></i></button>
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

		<section class="add-comment w-100">
			<form action="" id="comment-form" class="w-100" method="post" dir='rtl'>
				<input type="hidden" value="<?php echo $_GET["post"]; ?>" name="postid">
				<div class="input-group">
					<textarea class='form-control w-100' name="add-comment" placeholder="اكتب تعليقا"></textarea>
				</div>
				<div class="input-group">
					<button type="submit" class="form-control btn rounded my-2 comment-send">نشر</button>
				</div>
			</form>
		</section> 

		<section class="show-comment">
			<?php $comments = get_specific_data("*", "comment", "post_id", $_GET["post"], null, null, " JOIN users ON users.user_id = comment.user_id"); ?>
			<?php if(!$comments): echo "<div class='alert alert-primary w-100' dir='rtl'>لا توجد تعليقات!</div>"; ?>
			<?php else: ?>
			<h3>التعليقات</h3>
			<?php foreach($comments as $index => $comment): ?>
			<div class="comment-box">
				<div class="text-container">
					<div class="profile-info">
		
						<a class="avatar" href="profile.php?username=<?php echo $comment["username"]; ?>">   
							<img src="admin\uploads\avatars\<?php echo $comment["avatar"]; ?>" alt="avatar">
						</a>

						<div class="username">
							<?php 
							$comment_timestamp = strtotime(date("Y/m/d H:i:s" ,strtotime($comment["date"] . " " . $comment["time"])));
							
							?>
							<p class="comment-date"><?php echo date("M d", strtotime($comment["date"])); ?></p>
							<p class="comment-time"><?php echo calc_time($comment_timestamp) ?></p>
							<h4 id="comment_username">@<?php echo $comment["username"]; ?></h4>
							<h4><?php echo $comment["name"]; ?></h4>
						</div>

					</div>

					<div class="comment-description">
							<p class=""><?php echo $comment["comment"]; ?></p>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
			<?php endif; ?>
		</section>
	</div>
	
	<?php endif; ?>
	

<?php 

include $tpl . 'footer.php';

} else {

	redirectToIndex();
}


?>