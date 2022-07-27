<?php 

session_start();

$name = 'categories';

include 'init.php';

if(isset($_SESSION['user_id_session'])) {
	//if you are loged in
	if(isset($_GET['name'])) {
		// if there were name=value then $pagename=this value
		$pageName = $_GET['name'];

	} else {

		$pageName = 'main';
	}

	if($pageName == 'main') {

		echo "<div class='container'>";

		$allcats = connect("*", "categories", "", "", "ORDER BY category_id DESC", "all");  ?>

		<section class="main-cat">
			<h2 class="">الأقسام</h2>

			<div class="row">
			<?php foreach($allcats as $cat) { 
				//extract every row in category table
				$catid = $cat['category_id'];
			?>

				<div class="col-md-4">
					<div class="box">

						<div class="name-box">
							<a class="text-decoration-none" href="posts.php?name=main&catid=<?php echo $cat['category_id']; ?>"><h4><?php echo $cat['category_name']; ?></h4></a>
						</div>

						<div class="link-box">
							<a href="?name=delete&catid=<?php echo $cat['category_id']; ?>"><i class="fas fa-times-circle"></i></a>
							<a href="?name=edit&catid=<?php echo $cat['category_id']; ?>"><i class="fas fa-edit"></i></a>
						</div>

						<div class="state-box">
							<i class="glyphicon glyphicon-picture"><p>المنشورات:<?php echo getStats('post_id', 'posts', 'category_id', $catid); ?></p></i>
							<i class="glyphicon glyphicon-comment"><p>التعليقات:<?php echo getStats('comment_id', 'comment', 'category_id', $catid); ?></p></i>
							<i class="glyphicon glyphicon-heart"><p>الإعجابات:</p></i>
						</div>
					</div>
				</div>

			<?php } ?>
			</div>
		</section>

		<?php 

		echo "<a class='main-link text-decoration-none' href='?name=add'><button class='cat-btn normal-btn'>إضافة قسم جديد</button></a>";

		echo "</div>";

	}

	elseif($pageName == 'add') { ?>

		<div class="container">
			<div class="row">
				<div class='add-cat'>
					<form action="?name=insert" method="post" enctype="multipart/form-data">
						<h3 class="text-center">إضافة قسم جديد</h3>
						<div class="col-xs-12">
							<input pattern=".{3,10}" title="name of the category caonnot be less than 3 ch & larger than 10 ch" type="text" class="form-control" name="catNameHtml"  placeholder="اسم القسم" autocoplete = 'off' required />
						</div>
						<div class="col-xs-12">
							<input type="submit" value="حفظ" class="btn btn-primary btn-block w-100">
						</div>
					</form>
					</div>
				</div>
			</div>
		</div>


	<?php }

	elseif($pageName == 'insert') {

		if($_SERVER['REQUEST_METHOD'] == 'POST') {

			$formErrors = array();

			$cat_name = filter_var($_POST['catNameHtml'],FILTER_SANITIZE_STRING);

			echo "<div class='container'>";

			if(empty($cat_name)) {

				$formErrors[] = 'اسم القسم لا يمكن أن يكون فارغ';
			}

			if(isset($cat_name) > 10) {

				$formErrors[] = 'اسم القسم لا يمكن أن يزيد عن 10 أحرف';
			}

			if(isset($cat_name) < 3) {

				$formErrors[] = 'اسم القسم لا يمكن أن يقل عن 3 أحرف';
			}

			if(empty($formErrors)) { 

				// if there is no error
				//check if this name is exist [not uniq]
				// select category name value that looks like this category name that we put it in input 
				$check = checkIf("category_name", "categories", "category_name", $cat_name);

				if($check > 0) {

					//if the name exist 
					// if the name we put it in input like the one in database
					echo "<div class='msg-box error-msg'>" . 'هذا الاسم محجوز' . "</div>";
					redirect($destnition = 'back');

				} else {

					//if name isn't [it is uniq] exist insert it 
					$connect = $con->prepare("

					INSERT INTO categories(category_name)
					VALUES(:xname)

					");

					$connect->execute(array(

					'xname' 	=> $cat_name
				));

				
					$count = $connect->rowCount();

					if($count > 0) {

						//added
						$theMsg = "<div class='msg-box success-msg'>تم تسجيل القسم بنجاح </div>";
						echo $theMsg;
						redirect($dest = 'back');

					}

				}

			} else {

				//if not empty form errors
				foreach ($formErrors as $error ) {
			
					echo "<div class='error-msg msg-box'>" . $error . "</div>";
				}
			}
			
			echo "</div>";

		} else {

			//if you get here without post
			header('Location: index.php');
			exit();
		}
	}

	elseif($pageName == 'delete') {


		if($_GET['catid'] && is_numeric($_GET['catid'])) {

			//get here by cat id
			$catid = intval($_GET['catid']);

		} else {

			//if cat id isn't int
			header('Location: homepage.php');
			exit();
		}

		// select all informations[id,name,cover] from category which belongs to this category id we came by it
		$check = checkIf("*", "categories", "category_id", $catid);

		if($check > 0) {

			//if this user exist delete it 
			$delete = $con->prepare("DELETE FROM categories WHERE category_id = ?");

			$delete->execute(array($catid));

			$count = $delete->rowCount();

			if($count > 0) {

					//delete done
					echo "<div class='msg-box success-msg'>تم حذف القسم بنجاح</div>";
					redirect();
				}

			} else {

				//if cat not exist
				echo "<div class='msg-box normal-msg'>القسم الذي تبحث عنه غير موجود</div>";
				redirect();
			}
	}

	elseif($pageName == 'edit') { 

		if(isset($_GET['catid']) && is_numeric($_GET['catid'])) {

			$catid = intval($_GET['catid']);

		} else {

			//if get value of [catid] isn't a numeric
			header('Location: index.php');
			exit();
		}

		//check all infos that belongs to this category id is exsist
		// select all infos from categories that belnogs to this category id we came by
		$check = checkIf("*", "categories", "category_id", $catid);

		if($check > 0) { 
			//if exsist
			//get those ifons whose belongs to this category id to display it here
			$cat_info = connect("*", "categories", "WHERE category_id = {$catid}", "", "", "fetch");

			?>
			<div class='container'>
				<form method="post" action="?name=update">
					<h3 class="text-center">تعديل اسم القسم</h3>
					<!--requierd and pattern -->
					<input type="hidden" class="form-control" name="catidHtml" value="<?php echo $cat_info['category_id']; ?>">
					<div class="col-xs-12">
					<input type="text" pattern=".{3,10}" class="form-control" required name="catNameHtml" value="<?php echo $cat_info['category_name'] ?>" autocomplate="off" >
					</div>
					<div class="col-xs-12">
					<input type="submit" class="btn btn-danger btn-block" value="save">
					</div>
				</form>
			</div>

		<?php } else {

			//if catid not exist in database
			header('Location: index.php');
			exit();
		}
 	}

 	elseif($pageName == 'update') {

 		if($_SERVER['REQUEST_METHOD'] == 'POST') {

 				$formErrors = array();

 				$cat_id = $_POST['catidHtml'];

 				$cat_name = filter_var($_POST['catNameHtml'],FILTER_SANITIZE_STRING);

 				if(empty($cat_name)) {

 					$formErrors[] = 'اختر اسما';
 				}

 				if(strlen($cat_name) > 15) {

 					$formErrors[] = 'اسم القسم لا يمكن ان يجتاز 15 حرف';
 				}

 				if(strlen($cat_name) < 3) {

 					$formErrors[] = 'اسم القسم لا يمكن ان يقل عن 3 احرف';
 				}

 				if(empty($formErrors)) {

 					//if no errors in form
 					$check = checkIf("*", "categories", "category_id", $cat_id);

 					if($check > 0) {

 						//the cat is exist so update it
 						$update = $con->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");

 						$update->execute(array($cat_name,$cat_id));

 						$countUpdate = $update->rowCount();

 						if($countUpdate > 0) {

 							//updated
 							echo "<div class='msg-box success-msg'>تم تحديث اسم القسم بنجاح</div>";
 							redirect();
 						}

 					} else {

 						header("Location: index.php");
 						exit();
 					}


 				} else {

 					//if there are errors
 					foreach ($formErrors as $error) {
 						
 						echo "<div class='msg-box error-msg'>" . $error . "</div>";
 					}
 				}
 				
 			 
 		} else {

 			// if you get here without post
 			header('Location: index.php');
 			exit();
 		}
 	}

 	else {

		//if you have session but the page name didnt exist [name=anything]
		header('Location: index.php');
		exit();
 	}

} else {

	//if you come here without session
	header('Location: index.php');
	exit();
} 

include $tpl . 'footer.php';
 
?>