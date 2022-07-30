<?php 

session_start();


$name = "News";

include 'init.php';


if(isset($_GET['name'])) {

	$pagename = $_GET['name'];

} else {

	$pagename = 'main';
} 

if($pagename == 'main') {

	$news = connect("*", "posts", "WHERE category_id = 2", null, "ORDER BY post_id", 'all');

	if(isset($_SESSION['user_id_session'])) { ?>

		<section class="news d-flex flex-column align-items-center">
			<div class="container">
			
			<?php if($news > 0):
				
				foreach($news as $index => $theNew):?>
				<?php $user = connect("username", "users", "WHERE user_id = " . $theNew['user_id']) ?>
				<div class="card w-100 mb-5 news-card">
					<div class="card-header d-flex">
						<h5 class="title w-50"><?php echo $theNew["title"]; ?></h5>
						<div class="author w-50 d-flex justify-content-end">
							<p class="m-0 mx-2"><?php echo $theNew["post_date"] ?></p>
							<p class="m-0"><?php echo $user["username"]; ?>@</p>
						</div>
					</div>
					<div class="card-body w-100">
						<p class="card-text d-block new-text" ><?php echo $theNew["description"]; ?></p>
					</div>
					<div class="image-box w-100 p-2">
						<img src="uploads/posts/<?php echo $theNew["image"]; ?>" alt="">
					</div>
					<div class="news-delete w-100 p-2">
						<a href="?name=delete&nid=<?php echo $theNew["post_id"] ?>" class="btn delete-news w-auto">حذف الخبر</a>
					</div>
					
				</div>

				<?php endforeach; ?>
			<?php else: 
				echo "<div class='msg normal-msg'>لا توجد أخبار لعرضها</div>";
				exit; 
			?>
			<?php endif ?>
			
			</div>

			<a class="btn add-news" href="news.php?name=add">إضافة خبر</a>
		</section>

		
	<?php } 

	else {

		header("Location: index.php");
		exit;
	}


} elseif($pagename == 'add') { 

	if(isset($_SESSION['user_id_session'])) { 

	$userid = $_SESSION['user_id_session'];

	?>
	<div class="container" dir="rtl">
		<form class="w-100" action="?name=insert" method="post" enctype="multipart/form-data">
					
			<input type='hidden' name='userId' value="<?php echo $userid; ?>">
			<input class="form-control w-100" type="text" name="title" placeholder="عنوان الخبر" required pattern=".{,15}" title="not empty & not > 15 characters">
			<input class="form-control w-100" type="text" name="description" placeholder="وصف الخبر" required pattern=".{,200}" title="not empty & not > 200 characters" />
			<input class="form-control w-100" type="file" name="image" required />
			<button type="submit" class="btn btn-primary w-100" type="submit">نشر</button>
		</form>
	</div>

	<?php } else {

		header("Location: index.php");
		exit();
	}

 } elseif($pagename == 'insert') {
 	
 	if(isset($_SESSION['user_id_session'])) {
 		$userid = $_SESSION['user_id_session'];
 		if($_SERVER['REQUEST_METHOD']) {

			$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
			$desc = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

			//image
			$image = $_FILES['image'];
			$image_name = $_FILES['image']['name'];
			$image_tmp = $_FILES['image']['tmp_name'];
			$image_error = $_FILES['image']['error'];
			$image_size = $_FILES['image']['size'];

			// valid types
			$valid = array("JPEG", "PNG", "JPG", "jpeg", "png", "jpg", "jfif");
			$ex = explode(".", $image_name);
			$type = end($ex);
			//errors
			$errors = array();

			//the-news error
			if(isset($desc)) {

				if(strlen($desc) > 200) {

					$errors[] = "<div class='msg-box error-msg'>the letters cannot be larger than 200 </div>";
				}

			}

			if(empty($desc)) {

				$errors[] = "<div class='msg-box error-msg'>you have to write something</div>";
			}

			//image errors

			if(isset($image_name)) {

				if(!in_array($type, $valid) && !empty($image_name)) {

					$errors[] = "<div class='msg-box error-msg'>this type isn't valid</div>";
				}
			}

			if(empty($image_name)) {

				$errors[] = "<div class='msg-box error-msg'>اختر صورة</div>";
			}

			if(empty($errors)) {

				$uniq = rand(0,1000000) . "_" . $image_name;
				move_uploaded_file($image_tmp, "uploads/posts/" . $uniq);

				$insert = $con->prepare("

					INSERT INTO posts(title, description, image, likes, user_id, category_id, post_date, post_time)
					VALUES (:xtitle, :xdesc, :ximg, :xlikes, :xuid, :xcatid, :xdate, :xtime)

					");

				$insert->execute(array(

					'xtitle' => $title,
					'xdesc' => $desc,
					'ximg' => $uniq,
					'xlikes' => 0,
					'xuid' => $userid,
					'xcatid' => 2,
					'xdate' => date("Y-m-d"),
					'xtime' => date("H:i:s")
				));

				$inserted = $insert->rowCount();

				if ($inserted > 0) {

					$successMsg = "<div class='msg-box success-msg'>تم اضافة الخبر بنجاح</div>";
				}

				if(isset($successMsg)) {

					echo $successMsg;
					redirect("back");
				}

			} else {

				foreach($errors as $error) {

					echo "<div class='alert alert-danger'>$error</div>";
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

} elseif($pagename == 'delete') {

	if(isset($_SESSION['user_id_session'])) {

		if(isset($_GET['nid']) && is_numeric($_GET['nid'])) {

			$nid = $_GET['nid'];

			$check = checkIf("post_id", "posts", "post_id", $nid);

			if($check > 0) {

				$delete = $con->prepare("DELETE FROM posts WHERE post_id = ?");

				$delete->execute(array($nid));

				$deleted = $delete->rowCount();

				if ($deleted > 0) {

					$successMsg = "<div class='alert alert-success'>تم حذف الخبر بنجاح</div>";
				}

				while ( isset($successMsg)) {
					
					echo $successMsg;
					redirect("back");

				}

			} else {

				redirect('back');
			}
		}

	} else {

		header("Location: index.php");
		exit();
	}
}

include $tpl . 'footer.php';

?>