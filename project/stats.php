<?php 

session_start();
$siteName = "الإحصائيات";
include "init_front.php";
if($_SESSION["state"] && isset($_SESSION["state"])) { ?>

<section class="stats" dir='rtl'>
    <div class="container">
        <div class="stats-box mb-5 d-flex justify-content-center flex-wrap w-100">

            <?php 
                $get_stats = get_data("*", "stats", null, null, 'rows');
             ?>
            <?php foreach($get_stats as $stat): ?>
            <div class="stat-container d-flex flex-column mt-5 mx-4">
                <h3 class="w-100 fw-bold stat-name"><?php echo $stat["stat_name"]; ?></h3>
                <div class="stat w-100 mt-3 d-flex flex-column">
                    <div class="img-box d-flex">
                        <img class="w-50" src="admin/uploads/stats/<?php echo $stat['image']; ?>" alt="">
                        <div class="team-badge d-flex flex-row-reverse w-50">
                            <img class="m-3" src="admin/uploads/stats/<?php echo $stat["team_badge"] ?>" alt="">
                        </div>
                    </div>
                    <div class="player">
                        <h2 class="mt-2 mx-2 fw-bold"><?php echo $stat["player"]; ?></h2>
                    </div>
                    <div class="count w-100 mt-3">
                        <h1 class="text-center"><?php echo $stat["count"] ?></h1>
                    </div>               
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <h3 class="mb-3 fw-bold league-standing-title">جدول الترتيب</h3>
        <div class="league-standing">
            <div class="table-container w-100">
                <table class="table w-100">
                    <thead>
                        <tr>
                            <th class='d-none d-md-flex'>#</th>
                            <th></th>
                            <th>Club Name</th>
                            <th>pts</th>
                            <th class="">W</th>
                            <th class="">L</th>
                            <th class="">D</th>
                            <th class="d-none d-md-flex">PL</th>
                            <th class="d-none d-md-flex">Sequence</th>
                        </tr>
                    </thead>
                    
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    

</section>

<?php }

else { // if not logged in

    redirectToIndex();
}
include $tpl . "footer.php";