<?php

class Migrations{

	function GetLists()
	{
		global $wpdb;
    	$table_liste = SENDIT_LIST_TABLE;
		$liste=$wpdb->get_results("select * from $table_liste ");	
		return $liste;
	}

	function GetAllSubscribers()
	{
		global $wpdb;
    	$table_email = SENDIT_EMAIL_TABLE;
		$subscribers=$wpdb->get_results("select * from $table_email");	
		return $subscribers;
	}
	
	function GetSubscribersbyList($id)
	{
		global $wpdb;
    	$table_email = SENDIT_EMAIL_TABLE;
		$subscribers=$wpdb->get_results("select * from $table_email where id_lista=$id");	
		return $subscribers;
	}
	
		
	function MigrateLists()
	{

		$sendit_morefields=get_option('sendit_dynamic_settings');

		$liste = $this->GetLists();
		foreach($liste as $lista):

		
		

			//inserisco il TERM prendendo il nome della lista
			$term_id=wp_insert_term(
				  $lista->email_lista, // the term 
				  'mailing_lists', // the taxonomy
				  array(
				    'description'=> 'A yummy mailing list.',
				    'slug' => sanitize_title($lista->nomelista),
				    'parent'=>0
				  )
				);
				$dummy_content='<h3>H3 Heading and template preview</h3><p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>';
				
				//print_r($term_id);
					//inserisco il post del template
					$post = array(
						'post_status' => 'publish', 
						'post_type' => 'sendit_template',
						'post_author' => $user_ID,
						'ping_status' => get_option('default_ping_status'), 
						'post_parent' => 0,
						'post_content'=>$dummy_content,
						'post_name' => 'Template imported from '.sanitize_title($lista->nomelista),
						'menu_order' => 0,
						'to_ping' =>  '',
						'pinged' => '',
						'post_title' => 'Template imported from '.sanitize_title($lista->nomelista),
						'import_id' => 0
						//'tax_input' => array( 'mailing_lists' => $lista->nomelista)
						);
					$new_template_id = wp_insert_post($post, $wp_error );				
						
					update_post_meta($new_template_id, 'newsletter_css', '');
					update_post_meta($new_template_id, 'headerhtml', $lista->header);
					update_post_meta($new_template_id, 'footerhtml', $lista->footer);
					update_post_meta($new_template_id, 'old_list_id', $lista->id_lista);


			
				
				
			$subscribers=$this->GetSubscribersbyList($lista->id_lista);	
			$count=0;
			
		   	foreach($subscribers as $subscriber):
		   			$count++;

					
					$post = array(
						'post_status' => 'publish', 
						'post_type' => 'sendit_subscriber',
						'post_author' => $user_ID,
						'ping_status' => get_option('default_ping_status'), 
						'post_parent' => 0,
						'post_name' => sanitize_title($subscriber->email),
						'menu_order' => 0,
						'to_ping' =>  '',
						'pinged' => '',
						'post_title' => $subscriber->email,
						'import_id' => 0
						//'tax_input' => array( 'mailing_lists' => $lista->nomelista)
						);
					$new_post_id = wp_insert_post($post, $wp_error );
					$terms = array_map('intval', $term_id);
					
					wp_set_object_terms($new_post_id, $terms, 'mailing_lists');
					

					add_post_meta($new_post_id, 'magic_string', $subscriber->magic_string);
					add_post_meta($new_post_id, 'confirmed', $subscriber->accepted);
					//add more fields elements

 					  $arr=json_decode($sendit_morefields);
					 	if(!empty($arr)): 	
	 						foreach($arr as $k=>$v):
	 							$field = GetSenditField($subscriber->subscriber_info,$v->name);
								add_post_meta($new_post_id, $v->name, $field);		
	 						endforeach;
 						endif;

		

			endforeach;
		   			$terms = array_map('intval', $term_id);
		   			$count_args=array('count'=> $count);
					//wp_update_term($terms, 'mailing_lists', $count_args);
					wp_update_term_count_now($terms,'mailing_lists');
		   			echo 'inseriti '.$count.' nella lista '.$lista->id_lista.'<br />';
		   
		endforeach;
		
	}
	

	function json_field($json,$fieldname)
	{

		$options= urldecode($json->options);
		parse_str($options,$output);
		return $output[$fieldname];
		//print_r($output);
	}
		
	function GetListDetail($id_lista)
	{
		global $wpdb;
    	$table_liste = SENDIT_LIST_TABLE;
		$lista=$wpdb->get_row("select * from $table_liste where id_lista = $id_lista");	
		return $lista;
	}


}
?>