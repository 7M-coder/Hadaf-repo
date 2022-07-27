 <?php 

if(isset($_SESSION['user_id_session'])) { ?>

  <nav class="navbar navbar-expand-lg navbar-light d-flex mb-5"><!--start navbar-->
          <div class="container justify-content-center"><!--start container-->
              
              <span class="navbar-brand w-30 d-flex align-self-start">
                <div class="logo-container">
                  <img src="design/logo/The Lion.svg" class="offside">
                </div>
                <h3 class='site-title align-self-center'>هدف</h3>
              </span>

              <!-- Collect the nav links, forms, and other content for toggling -->
              <div class="collapse navbar-collapse align-self-center w-70" id="ournavbar">
                <ul class="navbar-nav navbar-left d-flex flex-row-reverse w-100">

                  <li class="links mx-2"><a href="comments.php" class="text-decoration-none">التعليقات</a></li>
                  <li class="links mx-2"><a href="users.php" class="text-decoration-none">المستخدمين</a></li>
                  <li class="links mx-2"><a href="stats.php" class="text-decoration-none">الإحصائيات</a></li>
                  <li class="links mx-2"><a href="posts.php" class="text-decoration-none">المنشورات</a></li>
                  <li class="links mx-2"><a href="news.php" class="text-decoration-none">الأخبار</a></li>
                  <li class="links mx-2"><a href="categories.php" class="text-decoration-none">الأقسام</a></li>
                  <li class="links mx-2 active"><a href="homepage.php" class="text-decoration-none">الصفحة الشخصية</a></li>

                </ul>
              </div><!-- /.navbar-collapse -->
          </div><!--end container-->
  </nav> <!--end nav bar-->

<?php }