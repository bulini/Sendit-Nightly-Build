<?php 
include("../../../wp-blog-header.php");
status_header(200);
nocache_headers();
require_once 'libs/actions.php';

$sendit=new Actions();

$sendit->NewSubscriber();

?>