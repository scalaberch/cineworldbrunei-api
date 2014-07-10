<!DOCTYPE html>
<html>
<head>
	<title>CineWorld Brunei | Data Management Systems </title>

	<link rel='stylesheet' type='stylesheet' href='<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.css' />
	<link rel='stylesheet' type='stylesheet' href='<?php echo base_url(); ?>assets/css/flat-ui.css' />
	<link rel='stylesheet' type='text/css' href='<?php echo base_url(); ?>assets/css/styles.css' />


	<script type='text/javascript' src='<?php echo base_url(); ?>assets/js/jquery-2.0.3.min.js'></script>
	<script type='text/javascript' src='<?php echo base_url(); ?>assets/js/bootstrap.min.js'></script>

	<script type='text/javascript'>
	$(function(){

		// Fix the application height...
		var height = $(window).height();
		$("#main-app-container").css("height", height);


		$('#main-menu-btns a').click(function(e){
			e.preventDefault();
			var id = $(this).attr("href");

			var path = "";
			if (id == "#movies"){ path = "movieListMain"; }
			else if (id == "#cinemas"){ path = "cinemaList"; }

			$('#main-menu-btns a').each(function(){
				if ($(this).parent().hasClass("active")){
					$(this).parent().removeClass("active");
				}
			});
			$(this).parent().addClass('active');


			var request = $.ajax({
				url:path
			});

			request.done(function(data){
				$("#main-app-content").hide(500, function(){
					$('#main-app-content').html(data).show(500);
				});
			});

		});

	});

	</script>
</head>
<body>


<div id='main-app-container' class='container-fluid'>
	<div class='row-fluid'>
		<nav class="navbar navbar-inverse navbar-fixed-top navbar-embossed" role="navigation">
		  <div class="container-fluid">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		      <a class="navbar-brand" href="#">CineWorld Brunei</a>
		    </div>

		    <!-- Collect the nav links, forms, and other content for toggling -->
		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul id='main-menu-btns' class="nav navbar-nav">
		        <li class="active"><a href="#dashboard">Dashboard</a></li>
		        <li><a href="#movies">Movies</a></li>
		        <li><a href="#cinemas">Cinemas</a></li>
		      </ul>
		      <form class="navbar-form navbar-left" role="search">
		        <div class="form-group">
		          <input type="text" class="form-control" placeholder="Search Movie...">
		        </div>
		      </form>
		      <ul class="nav navbar-nav navbar-right">
		        <li><a href="#">Sign Out</a></li>
		      </ul>
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
		</nav>

		<div id='main-app-content' class='container-fluid'>
			<div class='row-fluid'>

			</div>
		</div>

	</div>
</div>

</body>
</html>