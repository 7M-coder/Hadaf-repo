<?php 

session_start();

$name = 'comments';

include 'init.php';

if(isset($_SESSION['user_id_session'])) {

	if(isset($_GET['name'])) {

		$pageName = $_GET['name'];

	} else {

		$pageName = 'main';
	} 

	if($pageName == 'main') { 

		$comments = $con->prepare("

				SELECT comment.*,
				users.username,
				users.name,
 				users.avatar
				FROM comment
				INNER JOIN 
				users
				ON 
				users.user_id = comment.user_id
				WHERE spam = 0
			");

		$comments->execute();

		$record = $comments->fetchAll();

		$count = $comments->rowCount();

		if($count > 0) { 

			// في حالة في كومنتات عادية ومحظورة اعرضهم كلهم	
		?>
		<section class="main-comment">
			<div class="container">
				<h2 class="text-center">التعليقات<span></span></h2>
				<?php 

				foreach($record as $result) { ?>

				<div class="com-box">
					<div class="profile-filed">
						<?php 

						if(!empty($result['avatar'])) {

							echo "<img src='uploads/avatars/" . $result['avatar'] . "' alt='avatar'>";

						} else {

							echo "<img src='03.png' alt='avatar'>";
						}
						
						?>
						<h4><?php echo $result['name']; ?></h4>
						<h5>@<?php echo $result['username']; ?></h5>
						<p class="date"><?php echo $result['date']; ?></p>
						<span class="spam"><a href="?name=spam&comid=<?php echo $result['comment_id']; ?>">ban</a></span>
					</div>
					<div class="comment-filed">
						<p><?php echo $result['comment']; ?></p>
					</div>
				</div>


				<?php }

				?>
			</div>
		</section>

		<section class="spam-comments">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
					<div class="panel panel-default">
						<div class="panel panel-heading">
							التعليقات المحظورة
						</div>
						<div class="panel panel-body">
							<?php 
							$spamcom = $con->prepare("

								SELECT comment.*,
								users.avatar,
								users.name,
								users.username
								FROM comment 
								INNER JOIN
								users
								ON 
								users.user_id = comment.user_id
								WHERE spam = 1

								");

							$spamcom->execute();

							$comment = $spamcom->fetchAll();

							$count = $spamcom->rowCount();  

							if($count > 0) { ?>	

							<?php 

							foreach($comment as $spam) { ?>

							<div class="com-box">
								<div class="profile-filed">
									<?php 

									if(!empty($spam)) {

										echo "<img src='uploads/avatars/" . $spam['avatar'] . "' alt='avatar'>";

									} else { ?>

										<img src='03.png' alt='avatar' />

									<?php }
										
									?>
									<h4><?php echo $spam['name'] ?></h4>
									<h5>@<?php echo $spam['username'] ?></h5>
									<p class="date"><?php echo $spam['date'] ?></p>
								</div>
								<div class="comment-filed">
									<p><?php echo $spam['comment']; ?></p>
								</div>
								<span class="freedom"><a href="?name=unlock&comid=<?php echo $spam['comment_id']; ?>">رفع الحظر</a></span>
							</div>

							<?php }

							 } else {

								echo '<b style="margin-right:15px">لا توجد تعليقات لعرضها</b>';
							}

							?>
							
						</div>
					</div>
					</div>
				</div>
			</div>
		</section>


		<?php } else { 

			echo "<div class='container'>";
			
			echo "<div class='msg normal-msg mb-3'>لا توجد تعليقات غير محظورة لعرضها</div>";

			echo "</div>";

			// في حالة في كومنتات محظورة فقط اعرضها	
		?>
		<section class="spam-comments">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
					<div class="panel panel-default">
						<div class="panel panel-heading">
							التعليقات المحظورة
						</div>
						<div class="panel panel-body">
							<?php 
							$spamcom = $con->prepare("

								SELECT comment.*,
								users.avatar,
								users.name,
								users.username
								FROM comment 
								INNER JOIN
								users
								ON 
								users.user_id = comment.user_id
								WHERE spam = 1

								");

							$spamcom->execute();

							$comment = $spamcom->fetchAll();

							$count = $spamcom->rowCount();  

							if($count > 0) { ?>	

							<?php 

							foreach($comment as $spam) { ?>

							<div class="com-box">
								<div class="profile-filed">
									<?php 

									if(!empty($spam)) {

										echo "<img src='uploads/avatars/" . $spam['avatar'] . "' alt='avatar'>";

									} else { ?>

										<img src='03.png' alt='avatar' />
										<h4><?php echo $spam['name'];?></h4>
										<h5>@<?php echo $spam['username'] ?></h5>
										<p class="date"><?php echo $spam['date'] ?></p>
									<?php }

									?>
								</div>
								<div class="comment-filed">
									<p><?php echo $spam['comment']; ?></p>
								</div>
								<span class="freedom"><a href="?name=unlock&comid=<?php echo $spam['comment_id']; ?>">unlock</a></span>
							</div>

							<?php }

							 } else {

								echo '<b>لا توجد تعليقات لعرضها</b>';
							}

							?>
							
						</div>
					</div>
					</div>
				</div>
			</div>
		</section>

		<?php }

 }

 elseif($pageName == 'spam') {

 	if(isset($_GET['comid']) && is_numeric($_GET['comid'])) {

 		$comid = intval($_GET['comid']);

 		//check if the id of commetn exist in db
 		$check = checkIf("comment_id", "comment", "comment_id", $comid, "AND spam = 0");

 		if($check > 0) {

 			$spam = $con->prepare("UPDATE comment SET spam = 1 WHERE comment_id = ?");

 			$spam->execute(array($comid));

 			$count = $spam->rowCount();

 			if($count > 0) {

 				echo "<div class='msg-box success-msg'>تم حظر التعليق</div>";
 				redirect($dest = 'back');
 			}

 		} else {

 			redirect($dest = 'back');
 		}

 	} else {

 		header('Location: index.php');
 		exit();
 	}
 }

 elseif($pageName == 'unlock') {

 	if(isset($_GET['comid']) && is_numeric($_GET['comid'])) {

 		$comid = intval($_GET['comid']);

 		$check = checkIf("comment_id", "comment", "comment_id", $comid, "AND spam = 1");

 		if($check > 0) {

 			$unlock = $con->prepare("UPDATE comment SET spam = 0 WHERE comment_id = ?");

 			$unlock->execute(array($comid));

 			$unlocked = $unlock->rowCount();

 			if($unlocked > 0) {

 				echo "<div class='msg-box success-msg'>تم فك الحظر عن التعليق بنجاح</div>";
 				redirect($dets = 'back');
 			}

 		} else {
 			redirect($dest = 'back');
 		}

 	} else {

 		header('Location: index.php');
 		exit();
 	}
 }

} else {

	header('index.php');
	exit();
}

?>

<?php include $tpl . 'footer.php'; ?>