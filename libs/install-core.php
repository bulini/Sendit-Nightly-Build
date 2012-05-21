<?php 
/*******************************
Installation core
*******************************/
require('constants.php');
global $sendit_db_version;
global $wpdb;



function sendit_install() {
   global $_GET;
   global $wpdb;
   global $sendit_db_version;
   $sendit_db_version = SENDIT_DB_VERSION;
   $installed_version = get_option('sendit_db_version');
	/*
	++++++++++++++++++++++++++++
	Table: wp_nl_email
	++++++++++++++++++++++++++++
	*/   
   $table_email = $wpdb->prefix . "nl_email";
   $table_liste = $wpdb->prefix . "nl_liste";
 
   if( $installed_version != $sendit_db_version ) {

   $sql_email = "CREATE TABLE " . SENDIT_EMAIL_TABLE . " (
	  		  	id_email int(11) NOT NULL AUTO_INCREMENT,
              	id_lista  int(11) default '1',
              	contactname varchar(250) default NULL,
              	email varchar(250) default NULL,
              	subscriber_info text default NULL,
              	magic_string varchar(250) default NULL,
              	accepted varchar(1) default 'n',
              	post_id mediumint(9) NULL,
              	ipaddress VARCHAR(255)   NULL,
            
               PRIMARY KEY  (`id_email`),
                           KEY `id_lista` (`id_lista`)
    );";
     update_option("sendit_db_version", $sendit_db_version);
  
   
	/*
	++++++++++++++++++++++++++++
	Table: wp_nl_liste
	++++++++++++++++++++++++++++
	*/  
    $sql_liste = "CREATE TABLE ".SENDIT_LIST_TABLE." (
                  `id_lista` int(11) NOT NULL auto_increment,                  
                  `nomelista` varchar(250) default NULL,
                  `email_lista` varchar(250) default NULL,
                  `header` mediumtext NULL,
                  `footer` mediumtext NULL,
                   PRIMARY KEY  (`id_lista`)
                 );";

   $sql_alter="ALTER TABLE ". SENDIT_EMAIL_TABLE ." add column subscriber_info text default NULL";


   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_email);
   dbDelta($sql_liste);   
   dbDelta($sql_alter);   
   
   
    

   $init_html='<!-- Start Sendit Subscription form -->
     <div class="sendit">
<h4>Subscribe to our newsletter</h4>
     	<form name="theform" id="senditform">
<!-- the shortcode to generate subscription fields -->
        {sendit_morefields}
    	<p><label for="email_add">Your email</label>
       		<input id="email_add" type="text" placeholder="email here" name="email_add"/>
       		<input type="hidden" name="accepted" id="accepted" value="{accepted}">
       		<input type="hidden" name="lista" id="lista" value="{list_id}"><div id="sendit_wait" style="display:none;"></div>
       	        <input class="button" type="button" id="sendit_subscribe_button" name="agg_email" value="{subscribe_text}"/>
    	</p>
		</form>
<div id="dati"></div>
<small><i>You will receive an email with the confirmation link (check your spam folder if NOT)</i></small><br />


   		<small>Sendit <a href="http://www.giuseppesurace.com" title="Wordpress newsletter plugin">Wordpress newsletter</a></small>
	</div>';


	$init_css='.sendit{
	background:#f9f9f9;
	border-radius: 10px;
	padding:10px 5px 10px 5px;
	border:10px solid #efefef;
	}
	.sendit h3, .sendit h4{
	font-size:1.5em;
	}
	.sendit label{
	color:#444;
	margin-right:10px;
	font-weight: bold;
	display:block;
	}
	/*DO NOT CHANGE THIS ID*/
	#sendit_subscribe_button{margin:5px 0;background:#ff9900;color:#fff;}

	.sendit input, .sendit textarea, .sendit select{
	/*width: 180px;*/
	background:#FFFFFF;
	    border: 1px solid #BBBBBB;
	    border-radius: 2px 2px 2px 2px;
	    margin: 0 5px 0 0;
	    padding: 4px;


	}
	.short{
	width: 100px;
	margin-bottom: 5px;
	}

	.sendit textarea{
	width: 250px;
	height: 150px;
	}

	.boxes{
	width: 1em;
	}

	#submitbutton{

	margin-top: 5px;
	width: 180px;
	}

	.sendit br{
	clear: left;
	}

	.info, .success, .warning, .sendit_error, .validation {
	    border: 1px solid;
	    margin: 5px 0px;
	    padding:10px;

	}
	.info, .notice{
	    color: #FFD324;
	    background-color: #FFF6BF;
	}
	.success {
	    color: #4F8A10;
	    background-color: #DFF2BF;
	}
	.warning {
	    color: #9F6000;
	    background-color: #FEEFB3;
	}
	.sendit_error {
	    color: #D8000C;
	    background-color: #FFBABA;
	}
	.sendit small{font-size:80%;}';
	
	if(get_option('sendit_markup')=='') update_option('sendit_markup', $init_html);
	if(get_option('sendit_css')=='') update_option('sendit_css', $init_css);
	if(get_option('sendit_subscribe_button_text')=='') update_option('sendit_subscribe_button_text', 'subscribe');
	if(get_option('sendit_response_mode')=='') update_option('sendit_response_mode', 'ajax');
	if(get_option('sendit_unsubscribe_link')=='') update_option('sendit_unsubscribe_link', 'yes');
	if(get_option('sendit_gravatar')=='') update_option('sendit_gravatar', 'yes');
	
	if($_GET['upgrade_from_box']==1):
        	echo '<div class="updated"><h2>';
        	printf(__('Your Sendit Database table Structure is succesfully updated to version: '.SENDIT_DB_VERSION.' | <a href="%1$s">Hide this Notice and get started! &raquo;</a>'), admin_url( 'admin.php?page=sendit/libs/admin.php&sendit_ignore=0'));
        	echo "</h2></div>";
  	endif;

  }

}







function sendit_sampledata() {
   	/*
	++++++++++++++++++++++++++++
	inserimento lista 1 di test con dati di prova
	++++++++++++++++++++++++++++
	*/  
    global $wpdb;
    $header_default='<h1>'.get_option('blogname').'</h1>';
    $header_default.='<h2>newsletter</h2>';
    $footer_default='<p><a href="http://sendit.wordpressplanet.org">'.__('Newsletter sent by Sendit Wordpress plugin').'</a></p>';
    
    $rows_affected = $wpdb->insert(SENDIT_LIST_TABLE, array('nomelista' => 'Testing list','email_lista' => get_bloginfo('admin_email'), 'header' =>$header_default, 'footer'=>$footer_default) );
}
?>