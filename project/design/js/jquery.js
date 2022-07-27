$(function() {

  "use strict";


  //switch between login & signup
  $(".sign-fade").click(function() {
    $(".left .login-form").addClass("d-none");

    $(".left .sign-in-title").addClass("d-none");


    $(".left .signup-form").addClass("d-block");

    $(".left .sign-up-title").addClass("d-block");

  });


  //read more
  $(".new:first-of-type").find(".text-container").find(".description").addClass("long-text");
  $(".new:not(:first-of-type)").find(".text-container").find(".description").addClass("short-text");

  $(".news .text-container .description").each(function() {

    var textLength = $(this).text().length
    // if the [p] of the second and third .big > 75 letters
    if((textLength > 75) && ($(this).hasClass("short-text") == true)) {

      $(this).text($(this).text().substring(0,74) + " ...")
    }

    if(textLength > 550 && $(this).hasClass("long-text") == true) {

      $(this).text($(this).text().substring(0,549) + " ...")

    }

  });
  
  // following and unfollowing by ajax
  var username = $("#username").text();

  document.cookie = "username=" + username;

  let url = window.location.pathname;
  if(url.includes("profile")) {

    $.post("check.php", function(data, status, xhr) {
      // console.log(data);
      if(data == true) {

        $(".follow").attr("data-action", "unfollow");
        $(".follow").text("الغاء المتابعة");

      }
    })
  }
  
  $(".follow").on("click", function() {

    if($(this).attr("data-action") == "follow") {

      $(".follow").attr("data-action", "unfollow");
      $(".follow").text("الغاء المتابعة");

    } else {
      $(".follow").attr("data-action", "follow");
      $(".follow").text("متابعة");
    }

    $.post("follow.php",function(data, status, xhr) {

      // console.log(data);
    })
  }) 

  var postImg = $(".recent-posts .post-box .img-container img");
  
  $(postImg).on("click",function() {

    console.log($(this).width());

    $("body").prepend("<div class='popup'></div>");
    $(".popup").css({
  
      position: "absolute",
      top: 0,
      left: 0,
      width: $(document).width(),
      height: $(document).height(),
      backgroundColor: "rgb(0 0 0 / 87%)",
      zIndex: "0"

    })

    $(this).clone().prependTo("body").addClass("temp-img");

    $(".temp-img").css({

      position: "fixed",
      top: "50%",
      left: "50%",
      transform: "translate(-50%, -50%)",
      width: "254.25",
      height: "80%",
      objectFit: "contain",
      borderRadius: "0px",
      zIndex: "1"
    })

    $(".popup").click(function() {

      $(this).fadeOut(200);
      $(".temp-img").fadeOut(200);

    })
   
  })


  // likes
  $(".like").click(function() {

    if($(this).find("i").attr("data-color") === '#d1d1d1') {

      $(this).find("i").attr("data-color", "red");
      $(this).find("i").css("color", "red");
    } else {

      $(this).find("i").attr("data-color", "#d1d1d1");
      $(this).find("i").css("color", "#d1d1d1");      
    }

    var post_id = $(this).attr("value");
    var owner_uname = $(this).parent().siblings(".profile-info").find(".username").eq(1).text();
    document.cookie = "post_id=" + post_id;
    document.cookie = "post_username=" + owner_uname; 


    $.post("likes.php?action=like", function(data, status, xhr) {

      console.log(data)
    })
  })
  
  //get the liked posts
  $.post("likes.php?action=liked_posts", function(data, status, xhr) {

    if(status == "success") {

      console.log(data);

        var info = JSON.parse(data);

        var arr = Object.entries(info);
    
        // console.log(arr[0][1]["post_id"])
        for(var i = 0; i < arr.length; i++) {
    
          var currentPost_id = arr[i][1]["post_id"];
    
          // console.log(currentPost_id)
          $(".post-box, .profile-posts").each(function() {
    
            if ($(this).find(".post_id").attr("value") == currentPost_id) {
    
              // console.log("this post have likers:" + currentPost_id)
              $(this).find(".like").find("i").css("color", "red");
              $(this).find(".like").find("i").attr("data-color",  "red");
            }
    
          })
    
        }

        $(".likers").click(function() {

          var theClickedPostId = $(this).siblings(".post_id").attr("value");
          // console.log(theClickedPostId)
          for(var j = 0; j < arr.length; j++) {

            // console.log(arr[j][1]["post_id"])

            if(arr[j][1]["post_id"] == theClickedPostId) {

              $(".post-likers").css("display", "block")

              // $(".post-likers").text(arr[j][1]["likes"])
              
              document.cookie = "this_post_likers=" +  arr[j][1]["likes"]

            } else {

              $(".post-likers").css("display", "block")

              // $(".post-likers").text("no likers for this post")
            }
          }
        })

    }

  })

  $.ajax({
    url: "https://www.thesportsdb.com/api/v1/json/2/lookuptable.php?l=4328&s=2021-2022"
  }).done(function(data) {


    // console.log(data.table[0])
    let show = 10;
    console.log(location.href);

    if(location.href.includes("stats.php")) show = 0

    for(var i = 0; i < data.table.length - show; i++) {

      // console.log(team.strTeamBadge + " " + team.strTeam + " " + team.intRank + " " + team.intPoints + " " + team.intWin + " " + team.intLoss + " " + team.intDraw + " " + team.intPlayed + " " + team.strForm);

      let team = data.table[i];

      let club = `
      <tr class='team-row'>
          <th class='team-rank d-none d-md-flex'> ${team.intRank} </th>
          <td class='badge-box' >
          <img class='team-badge mx-2 mx-md-0' src='${team.strTeamBadge}' />       
          </td>
          <td class='team-name'>${team.strTeam}</td>
          <td class='team-points'>${team.intPoints}</td>
          <td class='team-wins'>${team.intWin}</td>
          <td class='team-losses'>${team.intLoss}</td>
          <td class='team-draws'>${team.intDraw}</td>
          <td class='team-played d-none d-md-flex'>${team.intPlayed}</td>
          <td class='team-form d-none d-md-flex'>${team.strForm}</td>
      </tr>

      `

      $(".league-standing .table-container").append(club)


    }

  })

  $.ajax({
    url: "https://api.statorium.com/api/v1/topplayers/20/?apikey=123_test_token_123&event_id=1&limit=100"
  }).done(function(data) {

    console.log(data)
  })

  // post click

  $(".post-box").on("click", function() {
    
    let postId = $(this).find("input").attr("value");

    location.href = "posts.php?post=" + postId;
  })

  $(".profile-posts").on("click", function() {

    console.log("click")
    let postId = $(this).find("input").attr("value");

    location.href = "posts.php?post=" + postId;

  })

  $(".likes").on("click", function(event) {

    event.stopPropagation();

    console.log("clicked");
  })

  // add news-post 
  $(".type-select").on("change", function(event) {

    let titleInput = `<input class="post-title form-control my-2" dir="rtl" type="text" name="title" placeholder="عنوان الخبر" required>`;
    $(event.currentTarget).find(":selected").not().attr("selected", "selected").siblings().removeAttr("selected")
    if($(":selected").text() == "خبر") {
      $(".type-select").after(titleInput);

    } else {
      $(".modal-body").find("input.post-title").remove();
    }
    if($(this).find("option").eq(1).attr("selected")) {

      $(".post-title").attr("type", "text")
    }
  })

  $("#add-form").submit(function(e) {

    e.preventDefault();    
    var formData = new FormData(this);

    $.ajax({
        url: "add-news-posts.php",
        type: 'POST',
        data: formData,
        success: function (data) {
            
            if(data) { // insert success

              $(".modal-header").prepend(data)
              
              setInterval(function(){

                $(".success").slideUp(200)

                // input error
                $(".fail").slideUp(200)

              }, 2000)
              
            } else { // insert error

              $(".modal-header").prepend(data)
              setInterval(function(){

                $(".fail").slideUp(200)

              }, 2000)

            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
  })

  $("#comment-form").submit(function(e) {

      e.preventDefault();
      var formData = new FormData(this);
      // add the category id of post which is 1
      formData.append("catid", 1);

      console.log("submited");

      $.ajax({
        url: "add-comment.php",
        type: 'POST',
        data: formData,
        success: function (data) {
            
            if(data) { // insert success

              $(".add-comment").append(data)
              
              setInterval(function(){

                $(".success").slideUp(200)

                // input error
                $(".fail").slideUp(200)

              }, 2000)

            } else { // insert error

              $(".add-comment").append(data)
              setInterval(function(){

                $(".fail").slideUp(200)

              }, 2000)            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
  });


  $(".news .card").on("click", function() {

    let postId = $(`.card-header`).find("input").attr("value");
    location.href = `news.php?post=${postId}`;
  })
  $(".news .new").on("click", function() {

    let postId = $(this).find("input").attr("value");
    location.href = `news.php?post=${postId}`;
  })

  // comments
  $(".show-comment").find(".alert-danger").hide();

  // navbar
  let pageTitle = document.querySelector("title").textContent;
  let navElements = document.querySelectorAll(".navbar .main-options li");
  let counter = 0;
  navElements.forEach(function(element, index) {

    if(element.getAttribute("title") == pageTitle) {
      console.log(element.children[0].children[0]);
      element.children[0].children[0].style.padding = "5px";
      element.children[0].children[0].style.backgroundColor = "#fff";
      element.children[0].children[0].style.color = "#f72585";
      element.children[0].children[0].style.borderRadius = "6px";
      element.children[0].children[0].style.width = "100%";

    } else {

      counter++;

      if(counter == 4) {
        document.querySelector(".navbar .profile li a img").style.border = '2px solid #f72585';
      }
      
    }
  })
  
});
