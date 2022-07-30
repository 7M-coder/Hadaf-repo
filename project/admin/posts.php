<?php 

session_start();

$name = 'posts';

include 'init.php';

if(isset($_SESSION['user_id_session'])) {
	// if you loged in
	if(isset($_GET['name'])) {
		//if name=value save it in $pageName
		$pageName = $_GET['name'];

	} else {
		// if name=anthig not [main,add,delete] make it main
		$pageName = 'main';
	}

	if($pageName == 'main') {//main page ?>

		<section class="posts">
		<div class="container">

			<!--start all posts-->
			<h2 class="">كافة المنشورات</h2>
			<div class="row">
				<?php 
				$allPosts = $con->prepare("

					SELECT * FROM posts
					JOIN users
					ON users.user_id = posts.user_id
					WHERE posts.category_id = 1
					");

				$allPosts->execute();

				$record = $allPosts->fetchAll();

				$count = $allPosts->rowCount();


				if($count > 0) {

				foreach($record as $result) { ?>

				<div class="col-xs-12">
					<div class="box">
						<div class="right-side">
							<div class="profile-info">
							<img src='uploads/avatars/<?php echo $result["avatar"]; ?>' alt='avatar' class='avatar'>
							<span class="name"><?php echo $result['name']; ?></span>
							<span class="username">@<?php echo $result['username']; ?></span>
							<a href="users.php?name=ban&userid=<?php echo $result['user_id']; ?>" title="حظر المستخدم"><i class="fas fa-ban"></i></a>
							</div>
							<div class="desc">
								<p><?php echo $result['description']; ?></p>
							</div>	
						</div>
						
						<div class="left-side">
							<div class="image">
								<?php 

								if(!empty($result['image'])) {

									echo "<img src='uploads/posts/" . $result['image'] . "' alt='image' class=''>";
								}

								?>
							</div>
						</div>
						

					</div>
					<div class="comments">
						<?php 

						$comments = $con->prepare("

							SELECT comment.*,
							users.username
							FROM comment
							INNER JOIN users
							ON users.user_id = comment.user_id
							WHERE post_id = ?
						");

						$comments->execute(array($result['post_id']));

						$record = $comments->fetchAll();

						foreach($record as $comment) { ?>

							<div class="comment-box">
								<h4>@<?php echo $comment['username'];  ?></h4>
								<p><?php echo $comment['comment']; ?></p>
							</div>

						<?php }

						?>
					</div>
				</div>
				<!--end all posts-->
				<?php }

				} else {

					echo "<div class='msg-box normal-msg w-100'>لا يوجد منشور لعرضه</div>";
				}

				?>	
			</div>
			
			<div class="d-flex justify-content-center w-100">
				<a href="?name=add" class="add-post btn mt-3">
					إضافة منشور
				</a>
			</div>


		
		</div>
	</section>
	<?php } 

	elseif($pageName == 'add') { ?>
				
		<div class='container'>
			<form action="?name=insert" method="post" enctype="multipart/form-data">

				<h3 class="text-center">إضافة منشور جديد</h3>

				<input type="hidden" value="<?php echo $_SESSION['user_id_session']; ?>" name="useridHtml">

				<input type="text" name="title" class="form-control title" placeholder="العنوان">

				<textarea placeholder="الوصف" name="desHtml" class="form-control" required></textarea>

				<input type="file" name="imageHtml" class="form-control">

				<select class="form-select mb-2" name="categoryId">
					<?php 
					$categories = connect("*", "categories", "", "", "ORDER BY category_id LIMIT 2", "all");

					foreach($categories as $cat) {

						echo "<option value='" . $cat['category_id'] . "'>" . $cat['category_name'] . "</option>";
					}
					?>
				</select>

				<input type="submit" value="حفظ" class="btn btn-primary w-100">

			</form>
		</div>
	<?php }

	elseif($pageName == 'insert') {

		if($_SERVER['REQUEST_METHOD'] == 'POST') {

			$title = filter_var($_POST["title"], FILTER_SANITIZE_STRING);
			$desc 	= filter_var($_POST['desHtml'], FILTER_SANITIZE_STRING);
			$catid 	= intval($_POST['categoryId']); //we got it as a numeric val, so convert it to integer
			//var_dump($catid); 
			$userid = $_POST['useridHtml'];

			//image 
			$image_name 	= $_FILES['imageHtml']['name'];
			$image_tmp  	= $_FILES['imageHtml']['tmp_name'];
			$image_error 	= $_FILES['imageHtml']['error'];
			$image_size		= $_FILES['imageHtml']['size'];
			$image_type 	= $_FILES['imageHtml']['type'];

			//image types
			$types = array("jpeg", "jpg", "png", "jfif");

			//valid types
			$ex = explode('.', $image_name);
			//array to every [image name] and its [.type]
			$valid_type = strtolower(end($ex));

			
			// takes end of every image array [.type]
			//form errors
			$formErrors = array();

			if(!empty($image_name) && !in_array($valid_type, $types)) {

				$formErrors[] = "<div class='msg-box error-msg'>امتداد الصورة غير صالح</div>";
			}

			if($catid === 2) { // اذا نزلت خبر

				if(empty($title) === true) { // وما اخترت عنوان للخبر

					$formErrors[] = "<div class='msg-box error-msg'>اختر عنوانا للخبر</div>";
				}
			}

			if(empty($desc)) {

				$formErrors[] = "<div class='msg-box error-msg'>اكتب شيئا..</div>";
			}

			if(strlen($desc) > 200) {

				$formErrors[] =  "<div class='msg-box error-msg'>يمكنك كتابة 200 حرف كحد اقصى</div>";
			}

			// if there is no error
			if(empty($formErrors)) {

				// cases of image file 
				if(empty($image_name)) {
					//if no image uploaded
					$uniq_name = ''; 				

				} else {

					$uniq_name = rand(0,1000000) . "_" . $image_name;
					move_uploaded_file($image_tmp, 'uploads/posts/' . $uniq_name);
					//left: temporary folder to save by local server, right: real folder to save
				}


				$insert = $con->prepare("

					INSERT INTO posts(description, image, category_id, user_id)
					VALUES(:xdesc, :ximg, :xcatid, :xuserid)

					");

				$insert->execute(array(

					'xdesc' 	=> $desc,
					'ximg'  	=> $uniq_name,
					'xcatid' 	=> $catid,
					'xuserid'	=> $userid

					));

				$insertd = $insert->rowCount();

				if($insertd > 0) {

					echo "<div class='msg-box success-msg'>تمت إضافة المنشور بنجاح</div>";
					redirect($destnition = 'back');

				}

			} else {

				foreach($formErrors as $error) {

					echo $error;
				}
			}
		}
	}

	elseif($pageName == 'delete') {

		if(isset($_GET['postid']) && is_numeric($_GET['postid'])) {

			//got here by [get post id] & is numeric
			$post_id = intval($_GET['postid']);

		} else {

			//got here without [get post id] and isnt numeric
			header("Location: index.php");
			exit();
		}

		// check if the post id is exsist
		$check = checkIf("post_id", "posts", "post_id", $post_id);

		if($check > 0) {

			$delete = $con->prepare("DELETE FROM posts WHERE post_id = ?");

			$delete->execute(array($post_id));

			$deleted = $delete->rowCount();

			if($deleted > 0) {

				echo "<div class='msg-box error-msg'>تم حذف المنشور بنجاح</div>";
				redirect($dest = 'back');
			}

		} else {
			// if the post id isnt exsist
			redirect($dest = 'back');
		}
	}

	else {

		// you have session but the page name is wrong [name=anything]
		header('Location: index.php');
		exit();
	}

} else {

	//if you have no session
	header('Location: index.php');
	exit();
}
	
include $tpl . 'footer.php';

?>