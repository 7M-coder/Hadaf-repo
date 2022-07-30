<?php
session_start();

if(isset($_SESSION["user_id_session"])) {

    include "init.php";

    if(isset($_GET["name"])) {

        $currentPage = $_GET["name"];

    } else {

        $currentPage = "main";
    }

    if($currentPage == "main") { // the stats main page ?>

        <?php 
        $get_stats = $con->prepare("SELECT * FROM stats");
        $get_stats->execute(); 
        $stats = $get_stats->fetchAll(); 
        ?>

        <div class="container d-flex flex-wrap justify-content-center stats">
            <?php 
                if($get_stats) {


                    if(!$stats) {

                        echo "<div class='msg normal-msg w-100 mt-3'>لم تتم إضافة احصائية بعد!</div>";
                    }

                    foreach($stats as $stat): ?>
        
                    <div class="card stat-card d-flex flex-column mx-2 mb-3 w-25">
                        <img src="uploads/stats/<?php echo $stat["image"]; ?>" class="card-img-top player-img mt-3" alt="...">
                        <div class="card-body d-flex flex-column h-100">
                            <h1 class="stat-name text-center mb-3"><?php echo $stat["stat_name"]?></h1>
                            <h3 class="player-name text-center mb-3"><?php echo $stat["player"] ?></h3>
                            <h5 class="text-center mb-3">
                                عدد المساهمات:
                                <?php echo $stat["count"] ?>
                            </h5>
                            <div class="delete-btn-container d-flex" style='flex-grow:2'>
                            <a href="?name=delete&id=<?php echo $stat["stat_id"]; ?>" class="btn btn-danger mt-3 w-100 fw-bold delete-stat-btn">
                            حذف
                            </a>
                            </div>

                            <input type="hidden" name="id" value="<?php echo $stat["stat_id"]; ?>">

                        </div>
                    </div> 
                
                    <?php endforeach;
                } else {

                    echo "<div class='alert alert-danger w-100'>حدث خطأ ما! يرجى المحاولة مجددا</div>";
                    header("refresh:3;url=index.php");
                    exit;
                }
            ?>
            <div class="w-100 d-flex justify-content-center"> 
                <button class="btn btn-primary mt-3 p-2 add-stat-btn">
                    <a href="?name=add-from" class="text-white text-decoration-none">
                        <strong>إضافة احصائية جديدة</strong>
                    </a>
                </button>
            </div>
            
        </div>
       
    <?php }


    else if($currentPage == "delete") {

        if(is_numeric($_GET["id"])) {

            $id_exist = checkIf("stat_id", "stats", "stat_id", $_GET["id"]);

            if($id_exist) { // delete

                $query = "DELETE FROM stats WHERE stat_id = ?";
                $delete = $con->prepare($query);
                $delete->execute([$_GET["id"]]);
                $delete = $delete->rowCount();
                if($delete) {

                    echo "<div class='alert alert-success w-100'>تم حذف الاحصائية بنجاح</div>";
                }
                else {

                    echo "<div class='alert alert-danger w-100'>حدث خطأ ما! يرجى المحاولة مجددا</div>";
                }

            } else {

                header("Location:index.php");
                exit;
            }
        }
    }

    else if($currentPage == "add-from") { ?>

        <form class="add-stat-form" action="?name=insert" method='post' enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label"><strong>نوع الإحصائية</strong></label>
                <input type="text" class="form-control" name="stat_name" maxlength="30" id="">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label"><strong>اسم اللاعب</strong></label>
                <input type="text" class="form-control" name="player_name"  maxlength="15" id="exampleInputPassword1">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label"><strong>عدد المساهمات</strong></label>
                <input type="text" class="form-control" name="stat_count"  maxlength="2" id="exampleInputPassword1">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label"><strong>صورة اللاعب</strong></label>
                <input type="file" class="form-control" name="player_img"  id="exampleInputPassword1">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label"><strong>اسم فريق اللاعب</strong></label>
                <input type="text" class="form-control" name="team_name"  maxlength="20" id="exampleInputPassword1">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label"><strong>شعار فريق اللاعب</strong></label>
                <input type="file" class="form-control" name="team_badge"  id="exampleInputPassword1">
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">نشر</button>
        </form>
    <?php }

    else if($currentPage == "insert") {

        if($_SERVER["REQUEST_METHOD"] == "POST") {

            $errors = [];

            $stat_name = $_POST["stat_name"];
            $player_name = $_POST["player_name"];
            $stat_count = $_POST["stat_count"];
            $team_name = $_POST["team_name"];

            if(empty($stat_name)) {

                $errors[] = "<div class='alert alert-danger w-100'>اختر اسم للإحصائية</div>";
            } else if(strlen($stat_name) > 30) {

                "<div class='alert alert-danger w-100'>اسم الإحصائية يحتوي على أكثر من 30 حرفا</div>";
            }

            if(empty($player_name)) {

                $errors[] = "<div class='alert alert-danger w-100'>حدد اسم اللاعب</div>";
            } else if(strlen($stat_name) > 15) {

                "<div class='alert alert-danger w-100'>اسم اللاعب يحتوي على أكثر من 15 حرفا</div>";
            }

            if(empty($stat_count)) {

                $errors[] = "<div class='alert alert-danger w-100'>اختر عددا للمساهمات</div>";
            } else if(strlen($stat_count) > 2) {

                "<div class='alert alert-danger w-100'>عدد المساهمات فوق الحد المعقول</div>";
            }

            if(empty($team_name)) {

                $errors[] = "<div class='alert alert-danger w-100'>حدد اسم فريق اللاعب</div>";
            } else if(strlen($stat_name) > 20) {

                "<div class='alert alert-danger w-100'>اسم فريق اللاعب يحتوى على اكثر من 20 حرفا</div>";
            }

            // player image
            $player_img_name = $_FILES["player_img"]["name"];
            $player_img_extension = substr($_FILES["player_img"]["type"], strpos($_FILES["player_img"]["type"],"/") + 1) ;        
            $player_img_tmp = $_FILES["player_img"]["tmp_name"];


            $valid_img_extensions = ["jpg", "jpeg", "png"];
            if(empty($player_img_name)) {

                $errors[] = "<div class='alert alert-danger w-100'>اختر صورة للاعب</div>";
 
            } else if(!empty($player_img_name) && !in_array($player_img_extension, $valid_img_extensions)) {

                $errors[] = "<div class='alert alert-danger w-100'>امتداد الصورة غير صالح</div>";

            }
            // team image
            $team_badge_name = $_FILES["team_badge"]["name"];
            $team_badge_extension = substr($_FILES["team_badge"]["type"], strpos($_FILES["team_badge"]["type"],"/") + 1) ;        
            $team_badge_tmp = $_FILES["team_badge"]["tmp_name"];

            if(empty($team_badge_name)) {

                $errors[] = "<div class='alert alert-danger w-100'>اختر صورة فريق اللاعب</div>";
 
            } else if(!empty($team_badge_name) && !in_array($team_badge_extension, $valid_img_extensions)) {

                $errors[] = "<div class='alert alert-danger w-100'>امتداد الصورة غير صالح</div>";

            }

            if(!empty($errors)) { // print errors

                foreach($errors as $error) {

                    echo $error;
                }

            } else { // insert


                $player_img_name = rand(1,100000) . "_" . $player_img_name;
                $team_badge_name = rand(1,100000) . "_" . $team_badge_name;
                $move1 = move_uploaded_file($player_img_tmp, "uploads/stats/" . $player_img_name);
                $move2 = move_uploaded_file($team_badge_tmp, "uploads/stats/" . $team_badge_name);

                $insert = $con->prepare(
                    "
                    INSERT INTO stats(stat_name, count, player, image, team_name, team_badge)
                     VALUES(:xsname, :xcount, :xplayer, :ximg, :xtname, :xtbadge)
                    "
                    );
                
                $insert = $insert->execute([
                    ':xsname' => $stat_name,
                    ':xcount' => $stat_count,
                    ':xplayer' => $player_name,
                    ':ximg' => $player_img_name,
                    ':xtname' => $team_name,
                    ':xtbadge' => $team_badge_name,
                ]);

                if($insert) {

                    echo "<div class='alert alert-success'>تم نشر الاحصائية بنجاح</div>";
                }
                else {

                    echo "<div class='alert alert-danger'>حدث خطا ما! يرجى المحاولة مجددا</div>";
                }
            }
        }
    }

    include $tpl . "footer.php";
}

else {

    header("index.php");
    exit;
}