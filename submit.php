<?php 
include("../../../wp-blog-header.php");
status_header(200);
nocache_headers();
//require_once 'libs/actions.php';
require_once 'libs/shared/lists-core.php';
//$sendit=new Actions();

//$sendit->NewSubscriber();
sendit_new_subscriber($_POST['email_add'],$_POST['lista']);
?>