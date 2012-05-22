<?php 
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
        'menu_position' => 25,
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'sendit_subscriber', $args );
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
    'query_var' => true,
    'rewrite' => array( 'slug' => 'mailing-list' ),
  ));

}



/**
 * Add additional fields to the taxonomy add view
 * e.g. /wp-admin/edit-tags.php?taxonomy=category
 */
function sendit_list_metadata_add( $tag ) {
	// Only allow users with capability to publish content
	if ( current_user_can( 'publish_posts' ) ): ?>
	<div class="form-field">
		<label for="email_from"><?php _e('From email'); ?></label>
		<input name="email_from" id="email_from" type="text" value="" size="40" />
		<p class="description"><?php _e('The sender email'); ?></p>
	</div>

	<div class="form-field">
		<label for="email_title"><?php _e('From name'); ?></label>
		<input name="email_title" id="email_title" type="text" value="" size="40" />
		<p class="description"><?php _e('your name or title usually displayed as sender'); ?></p>
	</div>
	<?php endif;
}



add_filter( 'manage_edit-sendit_subscriber_columns', 'sendit_edit_subscriber_columns' ) ;

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
    'menu_position' => null,
    'supports' => array('title','editor','thumbnail'),
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


function extract_posts()
{
	$posts=get_posts();
	return $posts;
}

function sendit_add_custom_box() 
{
  if( function_exists( 'add_meta_box' ))
  {
	add_meta_box( 'content_choice', __( 'Append content from existing posts', 'sendit' ), 
		          'sendit_content_box', 'newsletter', 'advanced','high' );
    add_meta_box( 'mailinglist_choice', __( 'Save and Send', 'sendit' ), 
                'sendit_custom_box', 'newsletter', 'advanced' );

   } 
}


function sendit_custom_box($post) {
	$sendit = new Actions();
	global $wpdb;
	$choosed_list = get_post_meta($post->ID, 'sendit_list', TRUE);
	//echo $choosed_list;
	$table_email =  SENDIT_EMAIL_TABLE;   
	$table_liste =  SENDIT_LIST_TABLE;   
    $liste = $wpdb->get_results("SELECT id_lista, nomelista FROM $table_liste ");
	echo '<label for="send_now">'.__('Action', 'sendit').': </label>';
	
	if(get_post_meta($post->ID, 'send_now', TRUE)=='2'):
		echo '<div class="jobrunning senditmessage"><h5>'.__('Warning newsletter is currently running the job','sendit').'</h5></div>';
	elseif(get_post_meta($post->ID, 'send_now', TRUE)=='4'):
		echo '<div class="jobdone senditmessage"><h5>'.__('Newsletter already Sent','sendit').'</h5></div>';
	else:
		
	endif;	
	
	echo '<select name="send_now" id="send_now">';
	
	if(function_exists('Sendit_tracker_installation')):
		if(get_post_meta($post->ID, 'send_now', TRUE)==2){ $selected=' selected="selected" ';} else { $selected='';}
		echo '<option value="2" '.$selected.'>'.__( 'Schedule with Sendit Pro', 'sendit' ).'</option>';
	endif;
		if(get_post_meta($post->ID, 'send_now', TRUE)==1){ $selected=' selected="selected" ';} else { $selected='';}
		echo '<option value="1" '.$selected.'>'.__( 'Send now', 'sendit' ).'</option>';	

		if(get_post_meta($post->ID, 'send_now', TRUE)==0){ $selected=' selected="selected" ';} else { $selected='';}
		echo '<option value="0" '.$selected.'>'.__( 'Save and send later', 'sendit' ).'</option>';
		
		if(get_post_meta($post->ID, 'send_now', TRUE)==4){ $selected=' selected="selected" ';} else { $selected='';}
		echo '<option value="4" '.$selected.'>'.__( 'Sent with Sendit pro', 'sendit' ).'</option>';	
		
		if(get_post_meta($post->ID, 'send_now', TRUE)==5){ $selected=' selected="selected" ';} else { $selected='';}
		echo '<option value="4" '.$selected.'>'.__( 'Sent with Sendit free', 'sendit' ).'</option>';
				
	echo '</select><br />';
	echo '<h4>'.__('Select List', 'sendit').'</h4>';
	foreach($liste as $lista): 
		$subscribers=count($sendit->GetSubscribers($lista->id_lista));?>
    	<input type="radio" name="sendit_list" value="<?php echo $lista->id_lista; ?>" <?php if ($choosed_list == $lista->id_lista) echo "checked=1";?>> <?php echo $lista->nomelista; ?>  subscribers: <?php echo $subscribers; ?><br/>
	<?php endforeach; ?>


	<input type="hidden" name="sendit_noncename" id="sendit_noncename" value="<?php echo wp_create_nonce( 'sendit_noncename'.$post->ID );?>" />
	
	<?php
}

function sendit_content_box($post) {
	global $post;
	$posts=extract_posts();
	foreach($posts as $post): ?>
	<div class="post_box">
	<table>
		<tr>
			<th style="width:200px; text-align:left;"><?php echo $post->post_title; ?></th><td><a class="button-secondary send_to_editor">Send to Editor &raquo;</a></td>
		</tr>
	</table>
    	<div class="content_to_send" style="display:none;"><h2><a href="<?php echo get_permalink( $post->ID); ?>"><?php echo $post->post_title; ?></a></h2><?php echo apply_filters('the_excerpt',$post->post_content); ?><a href="<?php echo get_permalink($post->ID); ?>">Read more...</a>
    	</div>
    </div>

	<?php endforeach; 
	
}

add_action('save_post', 'sendit_save_postdata');

function sendit_save_postdata( $post_id )
{
 	//print_r($_POST);
	if ( !wp_verify_nonce( $_POST['sendit_noncename'], 'sendit_noncename'.$post_id ))
		return $post_id;
 
 	 if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
	    return $post_id;
 
  	if ( !current_user_can( 'edit_page', $post_id ) )
	    return $post_id;
 
	$post = get_post($post_id);
	if ($post->post_type == 'newsletter') {
		update_post_meta($post_id, 'send_now', $_POST['send_now']);	
		update_post_meta($post_id, 'sendit_list', $_POST['sendit_list']);
		//save scheduler data if exixts
		if(function_exists('Sendit_tracker_installation'))
		{
			update_post_meta($post_id, 'subscribers', get_list_subcribers($_POST['sendit_list']));
			update_post_meta($post_id, 'sendit_scheduled',$_POST['sendit_scheduled']);
		}

		return(esc_attr($_POST));
	}
}



function send_newsletter($post_ID)
{
	$sendit = new Actions();
	$article = get_post($post_ID);
	$send_now = get_post_meta($post_ID, 'send_now',true);
	$sendit_list = get_post_meta($post_ID, 'sendit_list',true);	
	$table_liste =  SENDIT_LIST_TABLE;
	$list_detail = $sendit->GetListDetail($sendit_list);
	$subscribers = $sendit->GetSubscribers($sendit_list); //only confirmed
	/*+++++++++++++++++++ TEMPLATE EMAIL +++++++++++++++++++++++++++++++++++++++++*/
	$header=$list_detail->header;
	$footer=$list_detail->footer;
	$email_from=$list_detail->email_lista;
	
	/*+++++++++++++++++++ HEADERS EMAIL +++++++++++++++++++++++++++++++++++++++++*/
	$email=$email_from;
	$headers= "MIME-Version: 1.0\n" .
	"From: ".$email." <".$email.">\n" .
	"Content-Type: text/html; charset=\"" .
	get_option('blog_charset') . "\"\n";
	/*+++++++++++++++++++ CONTENT EMAIL +++++++++++++++++++++++++++++++++++++++++*/
	$title = $article->post_title;

	

	$readonline = get_permalink($post_ID);

	if($send_now==1):
		foreach($subscribers as $subscriber):
			global $subscriber_info;
			if($subscriber->subscriber_info!='') { $subscriber_info=json_decode($subscriber->subscriber_info); 
			}
			$content = apply_filters('the_content',$article->post_content);
		

			$newsletter_content=$header.$content.$footer;		
			
			if(get_option('sendit_unsubscribe_link')=='yes'):
			
				//aggiungo messaggio con il link di cancelazione che cicla il magic_string..
				$delete_link="
				<center>
	 			-------------------------------------------------------------------------------
				<p>".__('To unsubscribe, please click on the link below', 'sendit')."<br />
				<a href=\"".WP_PLUGIN_URL.'/sendit/'."delete.php?action=delete&c=".$subscriber->magic_string."\">".__('Unsubscribe now', 'sendit')."</a></p>
				</center>";
			else:
				$delete_link='';
			endif;
			//send the newsletter!		
			wp_mail($subscriber->email, $title ,$newsletter_content.$delete_link, $headers, $attachments);		
		endforeach;
		//set to 5 status : sent with classic plugin
		update_post_meta($post_ID, 'send_now', '5');	
	endif;
}



function export_subscribers_screen()
{ ?>
	<div class="wrap">

	<h2><?php echo __('To export Sendit mailing list you need to buy Sendit pro exporter','sendit');?></h2>
		<p><?php echo __('With Sendit pro export tool (available now for only 5 euros) you will be able to export and reimport as CSV files all your Sendit subscribers'); ?></p>
		<a class="button primary" href="http://sendit.wordpressplanet.org/plugin-shop/wordpress-plugin/sendit-pro-csv-list-exporter/"><?php echo __('Buy this plugin Now for 5 euros', 'Sendit'); ?></a>
	
	</div>
<? }

function sendit_morefields_screen()
{ ?>
	<div class="wrap">

	<h2><?php echo __('To add and manage more fields to your subscription form you need to buy Sendit More Fields');?></h2>
		<p><?php echo __('With Sendit More Fields tool (available now for only 5 euros) you will be able to create manage and add additional fields and store as serialized data to your subscriptions. Also you can use to personalize your newsletter with something like dear {Name}'); ?></p>
		<h4><?php echo __('This video show you how much easy is to add fields to your subscription form with Sendit More Fields','sendit'); ?></h4>
		<iframe src="http://player.vimeo.com/video/34833902?title=0&amp;byline=0&amp;portrait=0" width="601" height="338" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		<h4>Take a look to Sendit Plugins shop</h4>
		<a class="button-primary sendit-actions" href="http://sendit.wordpressplanet.org/plugin-shop/wordpress-plugin/sendit-more-fields/">
		<br />
		<?php echo __('Buy this plugin Now for 5 euros', 'Sendit'); ?></a>
	
	</div>
<? }

add_filter("manage_edit-newsletter_columns", "senditfree_newsletter_columns");

function senditfree_newsletter_columns($columns)
{

	global $post;
	$columns = array(
		"cb" => "<input type=\"checkbox\" name=\"post[]\" value=\"".$post->ID."\" />",
		"title" => "Newsletter Title",
		"queued" => "queued",
		"subscribers" => "subscribers",
		"startnum" => "sent",
		"opened" => "opened",
		"next_send" => "Next Job",
		"list" => "Receiver list"				
	);
	return $columns;
}


// Add to admin_init function
add_action('manage_posts_custom_column', 'senditfree_manage_newsletter_columns', 10, 2);

function senditfree_manage_newsletter_columns($column_name, $id) {
	global $wpdb;
	$buymsg='<small>'.__('To use this feature You need to buy Sendit Pro plugin', 'sendit').'</small><br />';
	$buymsg.= '<a href="http://sendit.wordpressplanet.org/plugin-shop/wordpress-plugin/sendit-pro-scheduler/">Buy now</a>';
	switch ($column_name) {
	case 'id':
		echo $id;
	    break;

	case 'queued':
		if(!function_exists('Sendit_tracker_installation'))
		{
		/*
		Buy the extension
		*/
		echo $buymsg;
		} else {
				if(get_post_meta($id, 'send_now', TRUE)=='2'):
					echo '<div class="jobrunning senditmessage"><p>'.__('Warning! newsletter is currently running the job','sendit').'</p></div>';
				elseif(get_post_meta($id, 'send_now', TRUE)=='4'):
					echo '<div class="jobdone senditmessage"><p>'.__('Newsletter Sent','sendit').'</p></div>';
			else:
		
			endif;
		}
	break;
		
	case 'list':
		echo 'List id: '. get_post_meta($id,'sendit_list',TRUE);
		if(!function_exists('Sendit_tracker_installation'))
		{
			/*
			Buy the extension
			*/
			//echo $buymsg;
		} 
		else
		{ 
			get_queued_newsletter();
		}
	
	break;
	
	case 'subscribers':
		echo get_post_meta($id,'subscribers',TRUE);
	break;

	case 'startnum':
		if(!function_exists('Sendit_tracker_installation'))
		{
		/*
		Buy the extension
		*/
			echo $buymsg;
		} 
		else
		{
			echo get_post_meta($id,'startnum',TRUE);
		}
				
	break;

	case 'opened':
		if(!function_exists('Sendit_tracker_installation'))
		{
			/*
			Buy the extension
			*/
			echo $buymsg;
		}
		else
		{	
			//status 5 inviate con invio normale
			if(get_post_meta($id,'send_now',TRUE)==5):
				echo '<small>'.__('Sent traditionally without tracker','sendit').'</small>';		
			else:
				$viewed = $wpdb->get_var($wpdb->prepare("SELECT count(reader_ID) FROM ".TRACKING_TABLE." WHERE newsletter_ID = {$id};"));
				$unique_visitors = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT(reader_ID) FROM ".TRACKING_TABLE." WHERE newsletter_ID = {$id};"));		
			echo '<small>'.__('Opened:','sendit').' '.$viewed. ' '.__('times','sendit').'<br />by: '.count($unique_visitors).' readers</small>';
			

			

			endif;
		}
		
	break;	
	
		
	case 'next_send':
		if(!function_exists('Sendit_tracker_installation'))
		{
			/*
			Buy the extension
			*/
			echo $buymsg;
		}
		else
		{
			if(get_post_meta($id, 'send_now', TRUE)==2):
				echo strftime("%d/%m/%Y/ - %H:%M ",wp_next_scheduled('sendit_five_event'));
			endif;
		}
		
	break;



	
	default:
	break;
	} // end switch
}

	
	// This code is copied, from wp-includes/pluggable.php as at version 2.2.2
	function sendit_init_smtp($phpmailer) {


		
		// Set the mailer type as per config above, this overrides the already called isMail method
		if(get_option('sendit_smtp_host')!='') {
			$phpmailer->Mailer = 'smtp';			
			// If we're sending via SMTP, set the host
			$phpmailer->Host = get_option('sendit_smtp_host');
			// If we're using smtp auth, set the username & password SO WE USE AUTH
			if (get_option('sendit_smtp_username')!='') {
				$phpmailer->SMTPAuth = TRUE;
				$phpmailer->SMTPSecure = 'ssl';
				$phpmailer->Port = get_option('sendit_smtp_port'); 
				$phpmailer->Username = get_option('sendit_smtp_username');
				$phpmailer->Password = get_option('sendit_smtp_password');
			}
		}
		
		// You can add your own options here, see the phpmailer documentation for more info:
		// http://phpmailer.sourceforge.net/docs/
		
		// Stop adding options here.
		
	} // End of phpmailer_init_smtp() function definition
	



add_action('phpmailer_init','sendit_init_smtp');	


?>