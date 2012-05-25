<?php 





function send_newsletter($post_ID)
{
	//$sendit = new Actions();
	$article = get_post($post_ID);
	$send_now = get_post_meta($post_ID, 'send_now',true);
	$sendit_list = get_post_meta($post_ID, 'sendit_list',true);	
	//$table_liste =  SENDIT_LIST_TABLE;
	
	
	
	$list_detail = sendit_list_detail($sendit_list);
	$subscribers = sendit_list_subscribers($sendit_list); //only confirmed
	
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