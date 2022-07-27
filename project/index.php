<?php 

session_start();

if(isset($_SESSION['state']) === true && $_SESSION['state'] === true) {

	header("Location: homepage.php");
	exit();
}
$navbar = 'test';
$siteName = 'تسجيل الدخول';
include 'init_front.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	if(isset($_POST['signin'])) {

		$username 	= 	filter_var($_POST['username']);
		$pass 		= 	$_POST['password'];

		//remember to hash the password later
		$hash_pass = sha1($pass);
		
		$connect = $con->prepare("SELECT * FROM users WHERE username = ? AND password = ?");

		$connect->execute(array($username, $hash_pass));	

		$count = $connect->rowCount();

		$record = $connect->fetch();


		if($count > 0) {
			
			/* اذا شلت الكومنت عن هذه حتحصل مشكلة اذا كنت مسجل
			دخول في الادمن والصفحة الرئيسية في نفس الوقت
			المشكلة انه الصفحة الرئيسية راح يسجل خروج منها تلقائيا
			والمشكلة حصلت بسبب ان السيشن اللي هنا واللي في الادمن 
			واخذين نفس القيمة حقت اليوزر اي دي واليوزرنيم وماني متأكد اذا
			استنتاجي صحيح */

	 		$_SESSION['userid'] 	= $record['user_id'];
	 		$_SESSION['username']	= $record['username'];
			$_SESSION['state'] = true;
			header('Location: index.php');
			exit();

		} else {

			$error_msg = "<p class='error-text mt-3'>اسم المستخدم او كلمة  المرور غير  صحيحة</p>";

		}

	} else {

		$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
		$name	  = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
		$email	  = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		$password = $_POST['password'];
		$re_password = $_POST['re-password'];

		$form_errors = array();


		//username error conditions
		if(isset($username)) {

			if(strlen($username) > 15) {

				$form_errors[] = 'username cannot be larger 15';

			} elseif(strlen($username) < 4 AND !empty($username)) {

				$form_errors[] = 'usernme cannot be less than 4';

			} elseif(empty($username)) {

				$form_errors[] = 'username cannot be empty';
			}

		}

		//name errors conditions
		if(isset($name)) {

			if(strlen($name) > 15) {

				$form_errors[] = 'name cannot be larger than 15 characters';

			} elseif(empty($name)) {

				$form_errors[] = 'name cannot be empty';
			}
		}

		//email errors conditions
		if(isset($email)) {

			$true_email = filter_var($email, FILTER_VALIDATE_EMAIL);

			if($true_email != true AND !empty($true_email)) {

				$form_errors[] = 'الايميل غير صحيح';

			} elseif(empty($email)) {

				$form_errors[] = 'قم بانشاء ايميل';
			}
		}


		//pass errors conditions
		if(isset($password)) {

			if($password != $re_password ) {

				$form_errors[] = "كلمات المرور غير متطابقة";

			} else {
				#if equal
				$true_pass = password_hash($password, PASSWORD_DEFAULT);
			}

			if(strlen($password) < 6) {

				$form_errors[] = 'كلمة المرور قصيرة جدا';
			}
		}

		if(empty($form_errors)) {

			$check = checkIf("username", "users", "username", $username);


			if($check > 0) {

				$exsist_msg = "<div class='msg-box error-msg modify'>اسم المستخدم محجوز</div>";

			} else {

				//insert 
				$connect = $con->prepare("

					INSERT INTO users(username, name, password, email, rigester_date, avatar)
					VALUES(:xusername, :xname, :xpass, :xemail, NOW(), :xavatar)

					");

				$connect->execute(array(

					"xusername" => $username,
					"xname" 	=> $name,
					"xpass"		=> $true_pass,
					"xemail"	=> $true_email,
					"xavatar"  	=> "blank.png"
				));

				$count = $connect->rowCount();

				if($count > 0) {

					 $success_msg = "<div class='msg-box success-msg suc-modi'>تم انشاء الحساب بنجاح</div>";
				}
			}
		}

	}

}

?>

<section class="login-sec d-flex">
	<div class="container d-flex justify-content-center align-items-center w-100">
		<div class="login-box d-flex justify-content-center w-100">
			<div class="right">
				<div class="background"></div>
				<div class="content d-flex flex-column">
					<h2 class="fw-bold text-center mt-3">مرحبا بكم في موقع الهداف</h2>
					<p class="text-center mt-3">آخر مستجدات وتحديثات الدوري الانجليزي</p>
					<div class="logo d-flex justify-content-center">
						<img src="admin/design/logo/The Lion.svg" alt="">
					</div>
				</div>
			</div>
			<div class="left d-flex flex-column">
				<h2 class="text-center sign-in-title fw-bold mt-3">تسجيل الدخول</h2>
				<h2 class="text-center sign-up-title fw-bold mt-3">إنشاء حساب</h2>
				<form class="login-form justify-self-center d-flex flex-column align-items-center" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

					<input class="form-control mt-3" type="text" name="username"  placeholder="اسم المستخدم" required />

					<input class="form-control mt-3" type="password" name="password" placeholder="كلمة المرور" required/>

					<input class="btn w-100 mt-3" type="submit" name="signin" value="تسجيل الدخول">

					<p>لا تملك حساب  ؟<a data-sign="signup" class="sign-fade"> انشاء حساب</a></p>

					<?php if(isset($error_msg)) {echo $error_msg;} ?>

				</form>

				<form class="signup-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input class="form-control mt-3" type="text" name="username"  placeholder="اسم المستخدم"  pattern=".{4,15}" title="username can't be larger than 15 or less than 4 ch" required />
					<input class="form-control mt-3" type="text" name="name"  placeholder="الاسم" pattern=".{0,15}" title="name can't be larger than 15 ch" required />
					<input class="form-control mt-3" type="email" name="email"  placeholder="البريد الالكتروني" required>
					<input class="form-control mt-3" type="password" name="password" placeholder="كلمة المرور" pattern=".{6,}" title="the password has to be larger 6 characters" required />
					<input class="form-control mt-3" type="password" name="re-password" placeholder="تأكيد كلمة المرور" pattern=".{6,}" title="the password has to be larger 6 characters" required />
					<input class="btn w-100 mt-3" type="submit" name="" value="انشاء الحساب" name="signup">
				</form>
			</div>
			<?php 

			if(isset($form_errors)) {

				foreach($form_errors as $error) {

					echo "<div class='msg-box error-msg modify'>" . $error . "</div>";
				}
			}

			if(isset($exsist_msg)) {

				echo $exsist_msg;
			}

			if(isset($success_msg)) {
				
				echo $success_msg;
			}

			?>
		</div>
	</div>
</section>

<?php
include $tpl . 'footer.php';
?>