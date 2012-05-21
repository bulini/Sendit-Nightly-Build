<?php
include("../../../wp-blog-header.php");
status_header(200);
nocache_headers();
require_once 'libs/actions.php';
global $wpdb;
global $_POST;
$id= $_POST['id_email'];


//let me parse

//serializer update
$subscriber_info=urldecode(json_encode($_POST));
$update=$wpdb->query("update ".SENDIT_EMAIL_TABLE." set subscriber_info = '$subscriber_info' where id_email = $id");	   	
echo 'info updated succesfully';

   	
?>