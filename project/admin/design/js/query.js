$(function() {

	//post image
	$(".image img").on("click", function() {

		$(this).parent().attr("class", "test");

		$("body").append("<span class='cover'>this is a cover</span>");

		$(".cover").css({

			position: "fixed",
			top: "0",
			left: "0",
			width: "100%",
			height: "100%",
			background: "#80adf094",
			zIndex: "1111",
			display: "flex",
			alignItems: "center",
			justifyContent: "center"
		});

		$(".cover").append($(this));

		$(this).css({

			position: "absolute",
			zIndex: "9999",
			width:$(this).naturalWidth ,
			height: $(this).naturalHeight 
		});


		// حطيناه هنا لانه غصبا عنك تضغط على الكفر اذا كبرت الصورة فقط
		$(".cover").on("click", function() {

			var theImage = $($(this).find("img"));

			$(".test").append(theImage);

			$(this).remove();

			theImage.css({
				float: "left",
			    width:"280px",
			    height: "280px",
			    borderRadius: "3px",
			    cursor: "pointer",
			    position: "static"
			});

			theImage.parent().removeClass("test");
		});
	});

	var title = $("input.title");
	if($("select").children(":selected").text() == "المنشورات") {
		//اذا دخلت الصفحة وكان المختار هو اوبشن المنشورات

		title.attr("disabled", "disabled");
	}

	$("select").change(function(){
		// اذا غيرت قيمة السيليكت من اوبشن لثانية
		//console.log($(":selected").text());
		if($(":selected").text() == "المنشورات") {
			// اذا غير قيمة السيليكت واخترت اوبشن المنشورات

			title.attr("disabled", "disabled")

		} else {
			
			// اذا اخترت اي اوبشن غير المنشورات
			title.removeAttr("disabled")
			title.attr("enabled", "enabled")
		}

	})

	// stats 
	$(".add-stat-form").submit(function(event) {

		event.preventDefault();

		let formData = new FormData(this);

		$.ajax({

			url: "stats.php?name=insert",
			type: "POST",
			data: formData,
			success: function(data) {

				// get the insert page document
				let getDoc =  new DOMParser();
				const doc = getDoc.parseFromString(data, "text/html");
				
				let failure = doc.querySelectorAll(".alert-danger");
				let success = doc.querySelector(".alert-success");

				console.log(success);
				console.log(failure);

				failure.forEach(function(element) {

					$(".add-stat-form").append(element);
					setInterval(function() {

						$(element).fadeOut()

					}, 5000)
				})

				$(".add-stat-form").append(success);
				setInterval(function() {

					$(success).fadeOut()

				}, 5000)

			},
			cache: false,
			contentType: false,
			processData: false
		})
	})

	$(".delete-stat-btn").on("click", function(event) {

		event.preventDefault();

		$.ajax({

			url: $(this).attr("href"),
			data: $(this).siblings("input").attr("value"),
			success: function(data) {

				// get the insert page document
				let getDoc =  new DOMParser();
				const doc = getDoc.parseFromString(data, "text/html");
				
				let failure = doc.querySelector(".alert-danger");
				let success = doc.querySelector(".alert-success");

				console.log(success);
				console.log(failure);

				$(".stats").prepend(success);
				$(".stats").prepend(failure);
				

				setInterval(function() {

					$(success).fadeOut()

				}, 5000)

				setInterval(function() {

					$(failure).fadeOut()

				}, 5000)
			},
			cache: false,
			contentType: false,
			processData: false
		})
	})
})