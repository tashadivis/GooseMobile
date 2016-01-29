<?php

include("header.php");?>



<div class="container">
	
	<div class="cart col-lg-8 col-md-8 col-sm-10">
		<h1 class="page-title"><?php the_title(); ?></h1>
		<table id="shoppingcart"><?php $test = do_shortcode('[always_show_wp_shopping_cart]'); echo $test; ?></table>
	

	<?php if($test == "") { ?>
	<div class="cart-empty">
		<h2>Your cart is empty!</h2>
		<h2><img src="http://localhost/goosemobile/wp-content/plugins/wordpress-simple-paypal-shopping-cart/images/shopping_cart_icon.png" alt="shopping cart icon"></h2>
	</div>
	</div>
	<?php } ?>
	
</div>

<?php include("footer.php"); ?>