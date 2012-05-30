<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <!-- Facebook sharing information tags -->
        <meta property="og:title" content=" Newsletter" />
        
        <title> Newsletter</title>
        	<style type="text/css">
			<?php   
			$template_id= get_post_meta($post->ID, 'template_id', TRUE); 
			$css=get_post_meta($template_id, 'newsletter_css', TRUE); 
				echo $css;
			?>
		</style>


	</head>
	<body>

	
		<?php if (have_posts()) : ?>


     <?php while (have_posts()) : the_post(); global $post; ?>
       		<?php 
	
	$template_id= get_post_meta($post->ID, 'template_id', TRUE);    		
	$header=get_post_meta($template_id, 'headerhtml', TRUE);
	$footer=get_post_meta($template_id, 'footerhtml', TRUE);
	echo $header;
	?>
          <?php the_content() ?>
       <!-- Do your post footer stuff here for single post-->
     <?php endwhile; ?>



<?php else : ?>
     <!-- Stuff to do if there are no posts-->

<?php endif; ?>
	<?php echo $footer; ?>

	</body>
</html>


