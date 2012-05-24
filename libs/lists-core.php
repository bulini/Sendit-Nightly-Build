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
    				$subscriber_id = wp_insert_post($post, $wp_error );
    				$terms = array_map('intval', $term_id);
    				
    				wp_set_object_terms($new_post_id, $terms, 'mailing_lists');
    									
					add_post_meta($subscriber_id, 'email', $email);
					add_post_meta($subscriber_id, 'magic_string', '1234');
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
	}
	
	function suca($email,$lists=array())
	{
		return $subscriber;
	}	



?>