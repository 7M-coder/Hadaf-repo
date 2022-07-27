<?php 

$get_avatar = $con->prepare("SELECT avatar FROM users WHERE username = :username");
$get_avatar->bindParam(":username",$_SESSION["username"]);
$get_avatar->execute();
$result = $get_avatar->fetch();
if($get_avatar->rowCount() > 0) {

  $avatarName = $result[0];
}
?>
<nav class="navbar">
    <ul>
        <span class="profile">
          <li title="صفحتي الشخصية"><a href="profile.php?username=<?php echo $_SESSION["username"] ?>"><img src="admin/uploads/avatars/<?php echo $avatarName ?>" alt="profile"></a></li>
        </span>
        
        <span class="main-options">
          <li class="home-list" title="الهداف - الصفحة الرئيسية"><a href="homepage.php" class="active"><i class="fas fa-home"></i></a></li>
          <li class="post-list" title="المنشورات"><a href="posts.php"><i class="fa-regular fa-hashtag"></i></a></li>
          <li class="news-list" title="الأخبار"><a href="news.php"><i class="far fa-newspaper"></i></a></li>
          <li class="stat-list" title="الإحصائيات"><a href="stats.php"><i class="far fa-chart-bar"></i></a></li>
        </span>
        
        <span class="off">
          <li><a href="logout.php"><i class="fas fa-power-off"></a></i></li>
        </span>
        
    </ul>
</nav>