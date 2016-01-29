<?php

/* Template Name: Devices Page - 3 Column */

include("header.php");?>

<?php $args = array(
	'post_type'  => 'device'
);

$device_query = new WP_Query( $args );
?>

	<a href="<?php echo home_url( '/' ); ?>plan"><img src="http://localhost/GooseMobile/wp-content/uploads/2016/01/plan-95-banner.png" alt="plan-95-banner" width="100%" class="plan-95-banner"/></a>
		<?php 
	if ($device_query->have_posts()) {
		//I have some posts so loop through the posts
		while($device_query->have_posts()){ 
		//get the post, the title, the content
			$device_query->the_post();
			if(has_post_thumbnail()) { ?>
				<div class="product col-lg-4 col-md-4 col-sm-4 col-xs-4">
					<a href="<?php the_permalink(); ?>">
					<span class="product-title"><?php the_title();?></span><br/>
					<?php the_post_thumbnail('large'); ?>
					<?php $price = (get_field('price')); ?>
						<span class="price"><?php echo '$'; echo $price; ?></span></a><br/><br/>
				</div>
			 <?php }
			else {
				echo "No thumbnail found.";
			}
		}//endloop
	wp_reset_postdata();
	}
	//no posts exist
	else {
		echo "No content found.";
	}

	?>
	
<?php include("footer.php"); ?>