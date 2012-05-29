<?php 
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

add_action('mailing_lists_add_form_fields', 'sendit_list_metadata_add', 10, 1);


function save_mailing_lists_metadata( $term_id ) {
	if ( isset($_POST['email_from']) )
		update_term_meta( $term_id, 'email_from', esc_attr($_POST['email_from']) );

	if ( isset($_POST['email_title']) )
		update_term_meta( $term_id, 'email_title', esc_attr($_POST['email_title']) );
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
    add_meta_box( 'mailinglist_choice', __( 'Choose a mailing list from this box', 'sendit' ), 
                'sendit_newsletter_box', 'newsletter', 'advanced' );

   } 
}

function sendit_newsletter_box($post)
{
	
	$markup='<label>'.__('Choose mailing list','sendit').'</label>';
	
	$args = array(
    'show_option_all'    => false,
    'show_option_none'   => false,
    'orderby'            => 'ID', 
    'order'              => 'ASC',
    'show_count'         => true,
    'hide_empty'         => true, 
    'child_of'           => 0,
    'exclude'            => 0,
    'echo'               => 0,
    'selected'           => 0,
    'hierarchical'       => 0, 
    'name'               => 'sendit_mailing_list',
    'class'              => 'postform',
    'depth'              => 0,
    'tab_index'          => 0,
    'taxonomy'           => 'mailing_lists',
    'hide_if_empty'      => false );
    
     $markup.= wp_dropdown_categories( $args ); 	

	echo $markup;
}


function sendit_custom_box_old($post) {
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



?>