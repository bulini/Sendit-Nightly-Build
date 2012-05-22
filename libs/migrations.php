<?php
require('constants.php');

class Migrations{
	
	
	function ChangeStatus($id,$status)
	{
		global $wpdb;
		$table_request = $wpdb->prefix . "request";
   		$update=$wpdb->query("update $table_request set payment_status = '$request' where id_request = $id"); 
		return true;
	}
	
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
		$liste = $this->GetLists();
		foreach($liste as $lista):
			//wp_insert_term($lista->email_lista, 'mailing_lists');
			//inserisco il TERM prendendo il nome della lista
			$term_id= wp_insert_term(
				  $lista->email_lista, // the term 
				  'mailing_lists', // the taxonomy
				  array(
				    'description'=> 'A yummy mailing list.',
				    'slug' => sanitize_title($lista->nomelista),
				    'parent'=>0
				  )
				);
				print_r($term_id);
			$subscribers=$this->GetSubscribersbyList($lista->id_lista);	
		   	foreach($subscribers as $subscriber):
		   		

					
					$post = array(
						'post_status' => 'pending', 
						'post_type' => 'sendit_subscriber',
						'post_author' => $user_ID,
						'ping_status' => get_option('default_ping_status'), 
						'post_parent' => 0,
						'post_name' => sanitize_title($subscriber->email),
						'menu_order' => 0,
						'to_ping' =>  '',
						'pinged' => '',
						'post_title' => $subscriber->email,
						'import_id' => 0,
						'tax_input' => array( 'mailing_lists' => $lista->nomelista)
						);
					$new_post_id = wp_insert_post($post, $wp_error );
					//wp_set_object_terms($new_post, array($term_id->term_id,0), 'mailing_lists' );
					echo $new_post_id;
					echo '-';
					echo $term_id->term_id;
					echo '<br />';
			endforeach;
		   
		   
		   
		endforeach;
		
	}
	

/*

	function MigrateSubscribers()
	{
		global $wpdb;
    	$table_email = SENDIT_EMAIL_TABLE;
		$subscribers=$wpdb->get_results("select * from $table_email");	
		foreach($subscribers as $subscriber):
			
		endforeach;
	}
	
*/
		
	function GetListDetail($id_lista)
	{
		global $wpdb;
    	$table_liste = SENDIT_LIST_TABLE;
		$lista=$wpdb->get_row("select * from $table_liste where id_lista = $id_lista");	
		return $lista;
	}


}
?>