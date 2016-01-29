<?php

include("header.php");?>

<div class="container">

	<h1><?php the_title();?></h1>
	
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
		<!----- M A P ---->
		<?php echo do_shortcode('[google_map_easy id="1"]'); ?>
	</div>
	
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
		<h3 class="method">BY EMAIL&nbsp;<a class="glyphicon glyphicon-chevron-down toggle" href="#email" data-toggle="collapse"></a></h3>
		<div id="email" class="collapse">
			<?php echo do_shortcode('[formidable id=3]'); ?>
		</div>
		
		<h3 class="method">BY PHONE&nbsp;<a class="glyphicon glyphicon-chevron-down toggle" href="#phone" data-toggle="collapse"></a></h3>
		<div id="phone" class="collapse">
			<?php if(get_field('phone_method')) {
				echo get_field('phone_method');
			}?>
		</div>	

		<h3 class="method">BY MAIL&nbsp;<a class="glyphicon glyphicon-chevron-down toggle" href="#mail" data-toggle="collapse"></a></h3>
		<div id="mail" class="collapse">
			<?php if(get_field('mail_method')) {
				echo get_field('mail_method');
			}?>
		</div>
	</div>
</div>

<?php include("footer.php"); ?>