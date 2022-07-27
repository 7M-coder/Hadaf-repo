<?php 

session_start();

$name = 'users';

include 'init.php';

if(isset($_SESSION['user_id_session'])) {

	if(isset($_GET['name'])) {

		$pageName = $_GET['name'];

	} else {

		$pageName = 'main';
	}

	if($pageName == 'main') { ?>

		<section class="main-user">
			<div class='container d-flex flex-wrap'>
			<h2 class="w-100">الأعضاء</h2>
			<?php 

			$users = connect('*', 'users', 'WHERE admin != 1', '', 'ORDER BY user_id DESC', 'all');

			foreach($users as $user) { ?>

					<div class="box">
						<div class="profile-info">
							<div class="avatar-box">
							<?php 
													
							if(!empty($user['avatar'])) {

								echo "<img class='img-responsive avatar' src='uploads/avatars/" . $user['avatar'] . "' alt='avatar'>";

							} else {

								echo "<img class='avatar' src='03.png' alt='avatar'>";
							}
							
							?>
							</div>
							<div class="name-box">
								<h4><?php echo $user['name']; ?></h4>
								<h5 class="username"><?php echo $user['username']; ?>@</h5>
							</div>
							<div class="control">

								<a href="?name=delete&userid=<?php echo $user['user_id']; ?>"><i class="fas fa-trash-alt" title="حذف المستخدم"></i></a>

								<?php 
								if($user["banned"] == 0) { ?>

								<a href="?name=ban&userid=<?php echo $user['user_id']; ?>"><i class="fas fa-ban" title="حظر المستخدم"></i></a>

								<?php } else { ?>

								<a href="?name=unbanned&userid=<?php echo $user['user_id']; ?>"><i class="fas fa-smile" title="الغاء الحظر عن المستخدم"></i></a>
							
								<?php }
								?>
								
							</div>
						</div>
						<div class="stats-box">
							<?php $userid = $user['user_id']; ?>
							<p>المنشورات: <?php 
							echo getStats("post_id", "posts", "user_id", $userid);
							?>
							</p>
							<p>التعليقات: 35</p>
							<p>تاريخ التسجيل: <?php echo $user['rigester_date']; ?></p>
						</div>
					</div>
				
			<?php }

			?>
				<a href="?name=add" class="main-link w-100 d-flex justify-content-center">
					<button class="normal-btn users-btn">اضافة مستخدم جديد</button>
				</a>
			</div>

		</section>



	<?php }

	elseif($pageName == 'add') { ?>
		
		<div class='container'>
			<form action="?name=insert" method="post" enctype="multipart/form-data">
				<h3 class="text-center">اضافة مستخدم جديد</h3>
				<input pattern=".{4,15}" title="username cannot be larger than 15 letters" type="text" class="form-control" name="usernameHtml"  placeholder="username" autocoplete = 'off' required />
				<input type="password" name="passHtml" class="form-control" placeholder="password"  autocomplete="new-password" required>
				<input type="email" name="emailHtml" class="form-control" placeholder="email" required>
				<input pattern=".{0,15}" title="the name cannot be larger than 15 letters" type="text" class="form-control" name="nameHtml"  placeholder="name" required />
				<select name="adminHtml">
					<option value="not-admin">ادمن؟</option>
					<option value="1">نعم</option>
					<option value="0">لا</option>
				</select>
				<input type="file" class="form-control" name="avatarHtml" required>
				<input type="submit" value="حفظ" class="btn btn-primary w-100">
			</form>
		</div>

	<?php }

	elseif($pageName == 'insert') {

		$username = filter_var($_POST['usernameHtml'], FILTER_SANITIZE_STRING);
		$password = filter_var($_POST['passHtml'], FILTER_SANITIZE_STRING);
		$email    = filter_var($_POST['emailHtml'], FILTER_SANITIZE_EMAIL);
		$name 	  = filter_var($_POST['nameHtml'], FILTER_SANITIZE_STRING);
		$admin    = $_POST['adminHtml'];
		$hashPass = sha1($password);

		//avatar
		$avatar_name  	= $_FILES['avatarHtml']['name'];
		$type  			= $_FILES['avatarHtml']['type'];
		$tmp_name  		= $_FILES['avatarHtml']['tmp_name'];
		$error  		= $_FILES['avatarHtml']['error'];
		$size  			= $_FILES['avatarHtml']['size'];

		//for sure from type
		$types 	= array("jpg","jpeg","png", "jfif");
		$ex = explode('.', $avatar_name);
		$valid_type = strtolower(end($ex));
		$formErrors = array();


		if(empty($username)) {

			$formErrors[] = 'خانة اسم المستخدم لايمكن تركها فارغة';
		}


		if(strlen($username) > 15) {

			$formErrors[] = 'اسم المستخدم لا يمكن ان يتكون بأكثر من 15 حرف';
		}

		if(strlen($username) < 4) {

			$formErrors[] = 'اسم المستخدم لا يمكن أن يتكون بأقل من 4 أحرف';
		}

		if(empty($password)) {

			$formErrors[] = 'اكتب كلمة مرور';
		}

		if(empty($email)) {

			$formErrors[] = 'اكتب بريدك الالكتروني';
		}

		if(empty($name)) {

			$formErrors[] = 'اكتب اسمك';
		}

		if(strlen($name) > 15) {

			$formErrors[] = 'الاسم لا يمكن ان يتجاوز 15 حرف';
		}

		if($admin == 'not-admin') {

			$formErrors[] = 'املء خانة الادمن';
		}

		if(!empty($avatar_name) && !in_array($valid_type, $types)) {

			$formErrors[] = "امتداد الصورة غير صالح";
		}

		if(empty($avatar_name)) {

			$formErrors[] = "اختر صورة عرض";	
		}

		if(empty($formErrors)) {


			//if no errors
			// check if the username is exist

			$check = checkIf("username", "users", "username", $username);

			if($check > 0) {

				//if the username is exist
				echo "<div class='msg-box error-msg'>اسم المستخدم الذي اخترته موجود!! قم باختيار اسم اخر</div>";
				redirect($destnition = 'back');

			} else {

				//upload
				$uniq_name = rand(0,1000000) . "_" . $avatar_name;
				move_uploaded_file($tmp_name, 'uploads/avatars/' . $uniq_name);

				//if the username not exist
				$insert = $con->prepare("

				INSERT INTO users(username, password, email, name, admin, avatar, rigester_date)
				VALUES(:xuname, :xpass, :xemail, :xname, :xadmin, :xavatar, now())

				");

				$insert->execute(array(

					'xuname' 	=> $username,
					'xpass' 	=> $hashPass,
					'xemail'	=> $email,
					'xname'		=> $name,
					'xadmin'	=> $admin,
					'xavatar'	=> $uniq_name

				));

				$inserted = $insert->rowCount();

				if($inserted > 0) {

					echo "<div class='msg-box success-msg'>تم اضافة المستخدم بنجاح</div>"; 
					redirect();
				} 
			}
			

		} else {

			// if there is error
			foreach($formErrors as $error) { ?>

				<div class="msg-box error-msg"><?php echo $error; ?></div>

			<?php }
		} 
	}

	elseif($pageName == 'edit') { // users.php?name=edit

		//if get here by existed userid
		echo "<div class='container'>";

		$userid = intval($_SESSION["user_id_session"]);

		$check = checkIf("user_id", "users", "user_id", $userid, "");

		if($check > 0) { 

			$edit = connect("*", "users", "WHERE user_id = {$userid}", "", "", "");
			// if the userid exist in database
			?>

			<form action="?name=update" method="post" enctype="multipart/form-data">
				<h3 style="text-align: center;">تعديل معلومات الحساب</h3>

				<input type="hidden" name="useridHtml" value="<?php echo $edit['user_id'] ?>">

				<input type="text" class="form-control" name="usernameHtml" placeholder="username" autocoplete = 'off' />
				<input type="hidden" name="oldUsername" value="<?php echo $edit['username']; ?>">

				<input type="password" name="newPass" class="form-control" placeholder="password"  autocomplete="new-password">
				<input type="hidden" name="oldPass" value="<?php echo $edit['password']; ?>">

				<input type="email" name="emailHtml" class="form-control" placeholder="email" value="<?php echo $edit['email'] ?>" >

				<input type="text" class="form-control" name="nameHtml"  placeholder="name" value="<?php echo $edit['name']; ?>"  />

				<select name="adminHtml">
					<option value="not-admin">ادمن؟</option>
					<option value="1" <?php if($edit['admin'] == 1) {echo 'selected';} ?> >yes</option>
					<option value="0" <?php if($edit['admin'] == 0) {echo 'selected';} ?>>no</option>
				</select>

				<input type="file" name="avatarHtml" class="form-control" required="required">
				<input type="hidden" name="oldAvatar" value="<?php echo $edit['avatar'] ?>">
				<input type="submit" value="حفظ" class="btn btn-primary btn-block w-100">
			</form>
			

		<?php } else {

			// if the userid deos not exist in database
			header('Location: index.php');
			exit();
		}

		echo "</div>";

	}

	elseif($pageName == 'update') {

		if($_SERVER['REQUEST_METHOD'] == 'POST') {

			//if you get here by post
			$userid 	= $_POST['useridHtml'];
			$username 	= filter_var($_POST['usernameHtml'], FILTER_SANITIZE_STRING);
			$oldUsername = filter_var($_POST['oldUsername'], FILTER_SANITIZE_STRING);
			$newPass 	= filter_var($_POST['newPass'], FILTER_SANITIZE_STRING);
			$oldPass 	= filter_var($_POST['oldPass'], FILTER_SANITIZE_STRING);
			$email 		= filter_var($_POST['emailHtml'], FILTER_SANITIZE_EMAIL);
			$name 		= filter_var($_POST['nameHtml'], FILTER_SANITIZE_STRING);
			$admin 		= $_POST['adminHtml'];

			if(!empty($newPass)) {

				//if there is new password
				$pass = sha1($newPass);

			} else {

				// if the password has not changed
				$pass = $oldPass;
			}

			//avatar
			$avatar_name  	= $_FILES['avatarHtml']['name'];
			$oldAvatar = $_POST['oldAvatar'];
			$type  			= $_FILES['avatarHtml']['type'];
			$tmp_name  		= $_FILES['avatarHtml']['tmp_name'];
			$error  		= $_FILES['avatarHtml']['error'];
			$size  			= $_FILES['avatarHtml']['size'];

			//for sure from type
			$types 		= array("jpg", "jpeg", "png", "jfif");
			$ex = explode('.', $avatar_name);
			$valid_type = strtolower(end($ex));


			$formErrors = array();

			if(strlen($username) > 15) {

				$formErrors[] = 'لا يمكن ان يزيد اسم المستخدم عن 15 حرف';
			}

			if(empty($email)) {

				$formErrors[] = 'لا يمكن ترك حثل البريد الالكتروني فارغا';
			}

			if(empty($name)) {

				$formErrors[] = 'لا يمكن ترك حقل الاسم فارفا';
			}

			if($admin == 'not-admin') {

				$formErrors[] = 'لا يمكن ترك حثل الادمن فارغا';
			}

			if(!empty($avatar_name) && !in_array($valid_type, $types)) {

				$formErrors[] = "امتداد الصورة غير صالح";
			}

			if(empty($avatar_name)) {

				$formErrors[] = "يجب اختيار صورة العرض";
			}


			if(empty($formErrors)) {

				//if there were no errors

				if(empty($username)) {

					//if you didn't change username and we do not want [username exsist] appear

					$username = $oldUsername;

					//upload
					$uniq_name = rand(0,1000000) . "_" . $avatar_name;
					move_uploaded_file($tmp_name, 'uploads/avatars/' . $uniq_name);
		

					// if username does not exist update the information
					$update = $con->prepare("UPDATE users SET username = ?, password = ?, email = ?, name = ?, admin = ?, avatar = ? WHERE user_id = ?");

					$update->execute(array($username, $pass, $email, $name, $admin, $uniq_name, $userid));

					$updated = $update->rowCount();

					if($updated > 0) {

						//updated
						echo "<div class='msg-box success-msg'> تم تحديث معلوماتك بنجاح</div>";
						redirect();
					}


				} else {

					// if you changed your username check if exsist like it in db

					$uniq_name = rand(0,1000000) . "_" . $avatar_name;
					move_uploaded_file($tmp_name, 'uploads/avatars/' . $uniq_name);				

					// check if username is token
					$check = checkIf("username", "users", "username", $username);

					if($check > 0) {

						//if username exist in database
						echo "<div class='msg-box error-msg'>عذرا! هذا الاسم مستخدم</div>";
						redirect($destnition = 'back');

					} else {

						// if there is no username like the one you choosed in db

						// if username does not exist update the information
						$update = $con->prepare("UPDATE users SET username = ?, password = ?, email = ?, name = ?, admin = ?, avatar = ? WHERE user_id = ?");

						$update->execute(array($username, $pass, $email, $name, $admin, $uniq_name, $userid));

						$updated = $update->rowCount();

						if($updated > 0) {

							//updated
							echo "<div class='msg-box success-msg'> تم تحديث معلوماتك بنجاح</div>";
							redirect();
						}
					}

				}


			} else {

				//if there is errors
				foreach($formErrors as $error) { ?>

					<div class="msg-box error-msg"><?php echo $error ?></div>

				<?php }
			}

		} else {

			//if you get here directly
			header('Location: index.php');
			exit();
		}
	}

	elseif($pageName == 'delete') {

		if(isset($_GET['userid']) && is_numeric($_GET['userid'])) {

			$userid = intval($_GET['userid']);

		} else {

			//if the userid not number
			header('Location: index.php');
			exit();
		}

		//check if the userid is exist

		$check = checkIf("user_id", "users", "user_id", $userid);

		if($check > 0) {

			// if the userid is exist delete it
			$delete = $con->prepare("DELETE FROM users WHERE user_id = ?");

			$delete->execute(array($userid));

			$deleted = $delete->rowCount();

			if($deleted > 0) {

				//deleted
				echo "<div class='msg-box success-msg'>تم حذف المستخدم بنجاج</div>";
				redirect();
			}

		} else {

			// if the user id does not exist
			echo "<div class='msg-box error-msg'>هذا المستخدم غير موجود</div>";
			redirect();			
		}

	} elseif($pageName == "ban") {

		echo "<div class='container'>";

		$userid = $_GET["userid"];

		//check if the client got here by $_GET['userid'] = a numeric user id
		if(isset($_GET["userid"]) && is_numeric($_GET["userid"]) == true)  { 

			//check if the user id [the user himslef] exist in db
			$check_user_exsist = connect("user_id, banned", "users", "WHERE user_id = $userid", null, null, null, false);
			// if user id [the user himslef] exist in db
			if($check_user_exsist > 0) {

				if($check_user_exsist["banned"] == 0) { 

					// banning the user
					$banned = $con->prepare("UPDATE users SET banned = ? WHERE user_id = ?");

					$banned->execute(array(1, $userid));

					$check = $banned->rowCount();

					// check if the ban process is completed successfuly
					if($check > 0) {

						// print success message
						echo "<div class='msg-box success-msg'>تم حظر المستخدم بنجاح</div>";
						header("refresh:3; url=users.php");
						exit();
					}
				} else {
					
					// if the user alreay banned
					echo "<div class='msg-box normal-msg'>هذا المستخدم محظور</div>";
					header("refresh:3; url=users.php");
					
				}

			} else {

				// if the user isnt exist
				echo "<div class='msg-box msg error-msg'>هذا المستخدم غير موجود</div>";
				header("refresh:3; url=users.php");
				exit();
				
			}

		} else {

			header("Location: index.php");
			exit();
		}

		echo "</div>";

	} elseif($pageName == "unbanned") { // users.php?name=unbanned

		// if is there users.php?name=unbanned&userid= the client user id
		if(isset($_GET["userid"]) && is_numeric($_GET["userid"])) {

			//get the user id
			$userid = $_GET["userid"];

			echo "<div class='container'>";

			// check if the user id [the user himself] exist in db
			$check_user_exsist = checkIf("user_id", "users", "user_id", $userid);

			// if he exist 
			if($check_user_exsist > 0) {

				// check if he is already not banned
				$check_if_banned = connect("banned", "users", "WHERE user_id = $userid",null,null,null,false);

				if($check_if_banned["banned"] == 0) {

					// if he is not banned
					echo "<div class='msg-box normal-msg'>هذا المستخدم غير محظور بالفعل</div>";
					header("refresh:3; url=users.php");
					exit();
				} else {

					// if he is banned
					// unbanding the user
					$unbanding = $con->prepare("UPDATE users SET banned = ? WHERE user_id = ?");

					$unbanding->execute([0, $userid]);
					// check if the banding process completed
					$check = $unbanding->rowCount();

					if($check > 0) {
						//if completed
						echo "<div class='msg-box success-msg'> تم الغاء الحظر عن المستخدم بنجاح </div>";
						header("refresh:3; url=users.php");
						exit();
					}
				}

			} else {

				// if the user isnt exist
				echo "<div class='msg-box msg error-msg'>هذا المستخدم غير موجود</div>";
				header("refresh:3; url=users.php");
				exit();
			}

			echo "</div>";

		} else {

			header("index.php");
			exit();
		}

	} else { // if get[name] = anything otherwise the specified above

		header("Location: index.php");
		exit();
	}

} else {

	// if does not session
	header('Location: index.php');
	exit();
}

?>

<?php include $tpl . 'footer.php'; ?>