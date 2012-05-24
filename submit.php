<?php 
include("../../../wp-blog-header.php");
status_header(200);
nocache_headers();
require_once 'libs/actions.php';
require_once 'libs/lists-core.php';
//$sendit=new Actions();

//$sendit->NewSubscriber();
sendit_new_subscriber($_POST['email_add'],array($_POST['lista']));
?>