
<html>
	<head>
		<title>Newsletter</title>
			
	</head>
	<body>
		<?php if (have_posts()) : ?>


     <?php while (have_posts()) : the_post(); ?>
       <!-- Do your post header stuff here for single post-->
          <?php the_content() ?>
       <!-- Do your post footer stuff here for single post-->
     <?php endwhile; ?>



<?php else : ?>
     <!-- Stuff to do if there are no posts-->

<?php endif; ?>


	</body>
</html>


