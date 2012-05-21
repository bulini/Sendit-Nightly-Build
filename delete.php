<?php

include("../../../wp-blog-header.php"); 
status_header(200);
nocache_headers();
/*
  QUESTO FILE CANCELLA L'INDIRIZZO EMAIL DALLA LISTA
 */
function CancellaEmail() {
	
	
    global $_GET;
	global $wpdb;
	
    $table_email = $wpdb->prefix . "nl_email";
    
    if($_GET['action']=="delete"):   
    	
    	$user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where magic_string ='$_GET[c]';");
    	
    		if($user_count<1) :
    			echo "<div class=\"error\">".__('email addresso not present or something is going wrong?!', 'sendit')."</div>";
    		else :
				
	 			$wpdb->query("UPDATE $table_email set accepted='d' where magic_string = '$_GET[c]'");				
				$table_liste = $wpdb->prefix . "nl_liste";
				
					 $templaterow=$wpdb->get_row("SELECT * from $table_liste where id_lista = '$_GET[lista]' ");
					
					
					//utile anzi fondamentale
					$plugindir   = "sendit/";
					$sendit_root = get_option('siteurl') . '/wp-content/plugins/'.$plugindir;
			
			
			
			/*
			 * QUI potete ridisegnare il vs TEMA
			 */		
				
				
				get_header();
					 
					 	echo '<div id=\"content\">';
							echo '<div id="message" class="updated fade"><p><strong>'.__("Your email address was deleted suffesfully from our mailing list!", "sendit").'</strong></p></div>';
						echo '</div>';
					echo '</div>';
				
				get_footer();
						
			endif;	
    
    endif;


}






CancellaEmail();


?>
