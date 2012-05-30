
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <!-- Facebook sharing information tags -->
        <meta property="og:title" content=" Newsletter" />
        
        <title> Newsletter</title>
		<style type="text/css">
			<?php $css=get_post_meta($post->ID, 'newsletter_css', TRUE); 
				echo $css;
			?>
		</style>

	</head>
		<title>Newsletter</title>
			
	</head>
	<body>
		<?php edit_post_link('edit template'); ?>
		<?php if (have_posts()) : ?>

	
     <?php while (have_posts()) : the_post(); 
     		$header=get_post_meta($post->ID, 'headerhtml', TRUE);
			$footer=get_post_meta($post->ID, 'footerhtml', TRUE);
     
     		echo $header;
     		
     		$dummy_content='<h2>Heading text for your newsletter</h2><h3>H3 Heading and template preview</h3>
     		
     		
     		<p><img alt="" src="http://www.senditplugin.com/wp-content/uploads/2012/04/morefields3-200x200.jpg" title="test" class="alignleft size-thumbnail wp-image-15">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>';
     		
     		
		echo $dummy_content
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


