<html>
	<head>
		<title>Newsletter</title>
			
	</head>
	<body>
		<?php if (have_posts()) : ?>


     <?php while (have_posts()) : the_post(); 
     		$header=get_post_meta($post->ID, 'header_html', TRUE);
			$footer=get_post_meta($post->ID, 'footer_html', TRUE);
     
     		echo $header;
     
     ?>
     
     
       <!-- Do your post header stuff here for single post-->
          <?php the_content() ?>
       <!-- Do your post footer stuff here for single post-->
     <?php endwhile; ?>



<?php else : ?>
     <!-- Stuff to do if there are no posts-->

<?php endif; 
		
		
		echo $footer;

?>
	

	</body>
</html>


