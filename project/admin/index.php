<?php 

session_start();

if(isset($_SESSION['username_admin_session'])) {

	// if the admin has loged in before and his session still exsist take him to the home page direct
	header('Location: homepage.php');
	exit();
}

$name = 'Login';

include 'init.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	$name_php 	= $_POST['username'];
	$pass_php 	= $_POST['password'];
	$hash_pass 	= sha1($pass_php);

	GLOBAL $con;

	$connect = $con->prepare("SELECT * FROM users WHERE username = ? AND password = ? AND admin = 1");

	$connect->execute(array($name_php,$hash_pass));

	$record = $connect->fetch();

	$count = $connect->rowCount();

	//here we have 3 cases

	if($count > 0) {

		// [1] user and pass is correct and exsist in db 
		$_SESSION['username_admin_session'] = $name_php;
		$_SESSION['user_id_session'] = $record['user_id'];

		// if everything alright head him to the main page
		header('Location: homepage.php');
		exit();

	} else {

		// [2] user and pass exsist but one of them does not correct
		// [3] user and pass does not exsist
		// [4] user not admin
		
		$errorMsg = '<div class="error-msg">اسم المستخدم او كلمة المرور خاطئة</div>';
	}
	


}

?>
<div class="container">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class='login-form'>
				<h3 class="text-center">Admin Login</h3>
				<input class="form-control" type="text" name="username" placeholder="Username" autocomplete="off">
				<input class="form-control" type="password" name="password" placeholder="Password" autocomplete="new-password">
				<input class="btn btn-danger btn-block w-100" type="submit" value="Login">
		</form>

		<?php 
		if(isset($errorMsg)) { ?>

			<div class="msg-box">
			<?php 

			// error message appears here if there was an error in login process
			echo $errorMsg;

		} ?>
		</div>
</div>

<?php 
/* how this page recognized $tpl var despite of we didn't make this var in this page?
because we included init page and all the things that exsist there is here now
*/
include $tpl . 'footer.php'; 
?>
