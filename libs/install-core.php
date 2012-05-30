<?php 
/*******************************
Installation new core
*******************************/
require('constants.php');
global $sendit_db_version;
global $wpdb;


add_action( 'init', 'register_cpt_sendit_subscriber' );

function register_cpt_sendit_subscriber() {

    $labels = array( 
        'name' => _x( 'Subscribers', 'sendit_subscriber' ),
        'singular_name' => _x( 'Subscriber', 'sendit_subscriber' ),
        'add_new' => _x( 'Add New', 'sendit_subscriber' ),
        'add_new_item' => _x( 'Add New Subscriber', 'sendit_subscriber' ),
        'edit_item' => _x( 'Edit Subscriber', 'sendit_subscriber' ),
        'new_item' => _x( 'New Subscriber', 'sendit_subscriber' ),
        'view_item' => _x( 'View Subscriber', 'sendit_subscriber' ),
        'search_items' => _x( 'Search Subscribers', 'sendit_subscriber' ),
        'not_found' => _x( 'No subscribers found', 'sendit_subscriber' ),
        'not_found_in_trash' => _x( 'No subscribers found in Trash', 'sendit_subscriber' ),
        'parent_item_colon' => _x( 'Parent Subscriber:', 'sendit_subscriber' ),
        'menu_name' => _x( 'Subscribers', 'sendit_subscriber' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'custom-fields' ),
        'taxonomies' => array( 'mailing_lists' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => false,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => false,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'sendit_subscriber', $args );
}

add_action( 'init', 'register_cpt_sendit_template' );

function register_cpt_sendit_template() {

    $labels = array( 
        'name' => _x( 'Newsletter Template', 'sendit_template' ),
        'singular_name' => _x( 'Template', 'sendit_template' ),
        'add_new' => _x( 'Add New', 'sendit_subscriber' ),
        'add_new_item' => _x( 'Add New Template', 'sendit_template' ),
        'edit_item' => _x( 'Edit Template', 'sendit_template' ),
        'new_item' => _x( 'New Template', 'sendit_template' ),
        'view_item' => _x( 'View Template', 'sendit_template' ),
        'search_items' => _x( 'Search Template', 'sendit_template' ),
        'not_found' => _x( 'No Templates found', 'sendit_template' ),
        'not_found_in_trash' => _x( 'No Templates found in Trash', 'sendit_template' ),
        'parent_item_colon' => _x( 'Parent Tenplate:', 'sendit_template' ),
        'menu_name' => _x( 'Templates', 'sendit_template' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'custom-fields'),

        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post',
    	'register_meta_box_cb' => 'sendit_add_custom_box'
    	);
    	
    


    register_post_type( 'sendit_template', $args );
}

add_action( 'init', 'create_sendit_list_taxonomies', 0 );


function create_sendit_list_taxonomies() 
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name' => _x( 'Mailing Lists', 'mailing_list' ),
    'singular_name' => _x( 'Mailing list', 'mailing_list' ),
    'search_items' =>  __( 'Search Mailing lists' ),
    'all_items' => __( 'All Mailing lists' ),
    'parent_item' => __( 'Parent Mailing List' ),
    'parent_item_colon' => __( 'Parent Mailing List:' ),
    'edit_item' => __( 'Edit Mailing List' ), 
    'update_item' => __( 'Update Mailing List' ),
    'add_new_item' => __( 'Add New Mailing List' ),
    'new_item_name' => __( 'New Mailing List' ),
    'menu_name' => __( 'Mailing List' ),
  ); 	

  register_taxonomy('mailing_lists',array('sendit_subscriber'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => false,
    'rewrite' => array( 'slug' => 'mailing-list' ),
  ));

}


function sendit_custom_post_type_init() 
{
	/***************************************************
	+++ custom post type: newsletter extract from Sendit Pro
	***************************************************/

  $labels = array(
    'name' => _x('Newsletters', 'post type general name'),
    'singular_name' => _x('Newsletter', 'post type singular name'),
    'add_new' => _x('Add New', 'newsletter'),
    'add_new_item' => __('Add New newsletter'),
    'edit_item' => __('Edit newsletter'),
    'new_item' => __('New newsletter'),
    'view_item' => __('View newsletter'),
    'search_items' => __('Search newsletter'),
    'not_found' =>  __('No newsletters found'),
    'not_found_in_trash' => __('No newsletters found in Trash'), 
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
    'rewrite' => false,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => 20,
    'supports' => array('title','editor','thumbnail','custom-fields'),
	'rewrite' => array(
    'slug' => 'newsletter',
    'with_front' => FALSE

  ),
	'register_meta_box_cb' => 'sendit_add_custom_box'


  ); 
  register_post_type('newsletter',$args);

}

add_filter('post_updated_messages', 'newsletter_updated_messages');


function newsletter_updated_messages( $messages ) {
	global $_POST;

	if($_POST['send_now']==1):
		$msgok=__('Newsletter Sent Now','sendit');
	elseif($_POST['send_now']==2):
		$msgok=__('Newsletter Scheduled it will be sent automatically','sendit');
	else:
		$msgok=__('Newsletter Saved succesfully','sendit');		
	endif;

  $messages['newsletter'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => $msgok,
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Newsletter updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Newsletter restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => $msgok,
    //6 => sprintf( __('Newsletter published. <a href="%s">View newsletter</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Newsletter saved.'),
    8 => sprintf( __('Newsletter submitted. <a target="_blank" href="%s">Preview newsletter</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Newsletter scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview newsletter</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Newsletter draft updated. <a target="_blank" href="%s">Preview newsletter</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

add_filter( 'gettext', 'sendit_change_publish_button', 10, 2 );

function sendit_change_publish_button( $translation, $text ) {
if ( 'newsletter' == get_post_type())
if ( $text == 'Publish' || $text == 'Update')
    return 'Save or Send Newsletter';

return $translation;
}




//display contextual help for Newsletters
add_action( 'contextual_help', 'add_help_text', 10, 3 );

function add_help_text($contextual_help, $screen_id, $screen) { 
$contextual_help =  ''; //var_dump($screen); // use this to help determine $screen->id
  if ('newsletter' == $screen->id ) {
    $contextual_help =
      '<p>' . __('Very important notices for a better use:','sendit') . '</p>' .
      '<ul>' .
      '<li>' . __('Insert your favorite content to send using the editor exactly in the same way you edit post, remember this content will be sent so be careful.','sendit') . '</li>' .
      '<li>' . __('Specify the mailing list from the radio men&ugrave; at the bottom of edit','sendit') . '</li>' .
      '</ul>' .
      '<p>' . __('If you want to schedule immediatly the newsletter check YES:','sendit') . '</p>' .
      '<ul>' .
      '<li>' . __('Under the Publish module, click on the Edit link next to Publish.','sendit') . '</li>' .
      '<li>' . __('Newsletter will be scheduled to be sent with your favorite settings.','sendit') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('For more information:') . '</strong></p>' .
      '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>','sendit') . '</p>' .
      '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>','sendit') . '</p>' ;
  } elseif ( 'edit-newsletter' == $screen->id ) {
    $contextual_help = 
      '<p>' . __('This is the help screen displaying the table of Newsletter system.','sendit') . '</p>' ;
  }
  return $contextual_help;
}



function sendit_edit_subscriber_columns( $columns ) {


	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Email' ),
		'date' => __( 'Subscription Date' )

	);	
	$sendit_morefields=get_option('sendit_dynamic_settings');



 	$arr=json_decode($sendit_morefields);
	//print_r($arr);
	if(!empty($arr)): 
	
		$morecolums[]=array();
 		foreach($arr as $k=>$v):		
			$morecolums[]=$v->name;
			//$allcolumns[]=array_merge($columns,$morecolums);
		endforeach;
	endif;





	$allcolumns=array_merge($columns,$morecolums);
	return $columns;
	
}





function sendit_install() {
   
   
    

   $init_html='<!-- Start Sendit Subscription form -->
     <div class="sendit">
<h4>Subscribe to our newsletter</h4>
     	<form name="theform" id="senditform">
<!-- the shortcode to generate subscription fields -->
        {sendit_morefields}
    	<p><label for="email_add">Your email</label>
       		<input id="email_add" class="validate[email]" type="text" placeholder="email here" name="email_add"/>
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



?>