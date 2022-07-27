<?php 

session_start();
// to get all sessions that we have

$name = 'profile';

include 'init.php';

if(isset($_SESSION['user_id_session'])) { 
	// if the user is loged in

$user_id = $_SESSION['user_id_session'];

$user_info = connect("*", "users", "WHERE user_id = {$user_id}", "", "", "get");

?>
	
<section class="my-profile">
	<div class="container">
		<div class="row">
			<h2 class="">الصفحة الشخصية</h2>
			<div class="col-xs-12">
			<div class="contain">
				<div class="box">
					<div class="profile d-flex justify-content-center flex-wrap">
						<div class="w-100 d-flex justify-content-center">
							<img src="uploads/avatars/<?php echo $user_info['avatar']; ?>" alt="avatar" class="img-responsive">
						</div>
						<h3 class="w-100"><?php echo $user_info['name']; ?></h3>
						<h5 class="w-100">@<?php echo $user_info['username']; ?></h5>
					</div>
					<div class="setting">
						<a class="account" href="users.php?name=edit"><i class="glyphicon glyphicon-user"></i><span>معلومات الحساب</span><i class="fas fa-angle-left"></i></a>
						<a class="account" href="posts.php?name=main&userid=<?php echo $user_info['user_id']; ?>"><i class="glyphicon glyphicon-picture"></i><span>منشوراتي</span><i class="fas fa-angle-left"></i></a>
						<a class="account" href="logout.php"><i class="glyphicon glyphicon-log-out"></i><span>تسجيل الخروج</span><i class="fas fa-angle-left"></i></a>
						<a class="account" href="#"><i class="glyphicon glyphicon-calendar"></i><span>تاريخ التسجيل: <?php echo $user_info['rigester_date']; ?></span></a>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</section>

<?php } else {

	header('Location: index.php');
}

?>

<?php include $tpl . 'footer.php' ?>