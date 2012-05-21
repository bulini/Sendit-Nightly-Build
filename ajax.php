<?php
include("../../../wp-blog-header.php");
status_header(200);
nocache_headers();
require_once 'libs/actions.php';
global $wpdb;
//print_r($_POST);
//let me parse
foreach($_POST as $k=>$v):
	$id= explode("-", $_POST['id']);
	$id=$id[1];

	if($k!='id'):
	   	if($k!='email'):
	   		//serializer update
	   		$subscriber_info=json_encode($_POST);
			$update=$wpdb->query("update ".SENDIT_EMAIL_TABLE." set $k = '$v' where id_email = $id");	   	
			//$update="update ".SENDIT_EMAIL_TABLE." set subscriber_info = '$subscriber_info' where id_email = $id";
   			if($v=='y') { $confirmed='confirmed'; $style='vertical-align:middle; background:#E4FFCF;'; } 
   			elseif($v=='d') {$confirmed='unsubscribed'; $style='vertical-align:middle; background:#fd919b';} 
   			elseif($v=='n') {$confirmed='not confirmed'; $style='vertical-align:middle; background:#fffbcc;';} 
   			else {}
   			echo '<div style="'.$style.'display:block;height:35px">';
   				echo $confirmed;
	   		echo '</div>';
	   	else:	
			$update=$wpdb->query("update ".SENDIT_EMAIL_TABLE." set $k = '$v' where id_email = $id");
   
   			echo $v;
   		endif;
   	endif;
   	

   	
endforeach;



?>