<section class="add">
        <?php $userid = get_user_id($_SESSION["username"]); ?>
        <input type="hidden" name="user_id" value="<?php echo $userid; ?>">
        <div class="add-btn-box rounded">
            <!-- Button trigger modal -->
            <button type="button" class="btn add-btn rounded" data-bs-toggle="modal" data-bs-target="#exampleModal">
            +
            </button>
        </div>

        <!-- Modal -->
        <div class="modal modal-lg fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header flex-wrap" style="border:none">

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" id="add-form" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            
                            <select name="type" class="form-select my-2 type-select" dir="rtl">  
                                <option value="1" selected="selected">منشور</option>
                                <option value="2">خبر</option>
                            </select>
                            <textarea class="form-control" placeholder="اكتب..." style="resize:none; direction:rtl" name="description" required></textarea>
                            <input type="file" class="form-control my-2" name="image" >

                        </div>
                        <div class="modal-footer" style="border:none">
                            <button type="submit" class="btn btn-send rounded">نشر</button>
                        </div>

                    </form>


                </div>  
            </div>
        </div>
    </section>