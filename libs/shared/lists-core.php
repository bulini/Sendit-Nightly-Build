<?php

/**
 * Insert and retrieve the id of last subscriber.
 * @params email, lists json_array from sendit_morefields
 * @since 2.2
 * @return $subscriber_id.
 */

	function sendit_new_subscriber($email,$term_id)
	{
					$sendit_morefields=get_option('sendit_dynamic_settings');
					
					$post = array(
    					'post_status' => 'publish', 
    					'post_type' => 'sendit_subscriber',
    					'post_author' => $user_ID,
    					'ping_status' => get_option('default_ping_status'), 
    					'post_parent' => 0,
    					'post_name' => sanitize_title($email),
    					'menu_order' => 0,
    					'to_ping' =>  '',
    					'pinged' => '',
    					'post_title' => $email,
    					'import_id' => 0
    					//'tax_input' => array( 'mailing_lists' => $lista->nomelista)
    					);
    				if(!sendit_subscriber_lexist($email)):
						$lista=esc_attr($term_id); //security hack suggested this summer
						//$lista=(int)$lista;
    					$subscriber_id = wp_insert_post($post, $wp_error );
    					$terms = array_map('intval', array($lista));
    				

    				
    					wp_set_object_terms($new_post_id, $lista, 'mailing_lists');
	                	wp_update_term_count_now($terms,'mailing_lists');
	                	//genero stringa univoca x conferme sicure
	                	$magic_string= md5(uniqid(rand(), true));
    									
						add_post_meta($subscriber_id, 'email', $email);
						add_post_meta($subscriber_id, 'magic_string', $magic_string);
						add_post_meta($subscriber_id, 'confirmed', 'y');
						//add more fields elements
	 					$options=$_POST['options'];

 					 	$arr=json_decode($sendit_morefields);
					 		if(!empty($arr)): 	
	 							foreach($arr as $k=>$v):
	 							//print_r($_POST);	 							
									$field = $_POST[$v->name];
									echo $field;
									add_post_meta($subscriber_id,$v->name, $field);		
	 							endforeach;
 							endif;
		
						return $subscriber_id;
						
						echo '<div class="error">
								<p><strong>'.__('Subscription completed now Check your email and confirm', 'sendit').'</p>
							  </div>';					
					else:
						echo '<div class="error">Subscriber Exists</div>';
					endif;
	}

/**
 * Check if subscriber exists.
 * @params email
 * @since 3.0
 * @return boolean.
 */
	
	function sendit_subscriber_lexist($email) 
	{
		global $wpdb;
		$user_count=$wpdb->get_var("SELECT COUNT(*) FROM wp_posts WHERE post_title = '".$email."'");
		echo $user_count;
		if ($user_count>0):		
			return TRUE;
		else:
			return FALSE;
		endif;
	}
	
	function suca($email,$lists=array())
	{
		return $subscriber;
	}	



?>