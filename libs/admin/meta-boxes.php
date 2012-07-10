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

add_action('init', 'sendit_empty_check');
//remove all custom fields empty

add_action('mailing_lists_add_form_fields', 'sendit_list_metadata_add', 10, 1);

function sendit_empty_check() {

	$post_id=$_GET['post'];
	if($post_id):

	    //obtain custom field meta for this post
	     $custom_fields = get_post_custom($post_id);
	
	    foreach($custom_fields as $key=>$values):
	    //$values is an array of values associated with $key - even if there is only one value. 
	    //Filter to remove empty values.
	    //Be warned this will remove anything that casts as false, e.g. 0 or false 
	    //- if you don't want this, specify a callback.
	    //See php documentation on array_filter
	    $nonemptyvalues = array_filter($values);
	
	    //If the $nonemptyvalues doesn't match $values then we removed an empty value(s).
	    if($nonemptyvalues!=$values):
	         //delete key
	         delete_post_meta($post_id,$key);
	
	         //re-add key and insert only non-empty values
	         foreach($nonemptyvalues as $nonemptyvalue){
	             add_post_meta($post_id,$key,$nonemptyvalue);
	         }
	    endif;  
	endforeach;
	endif;


} 


/**
 * Save newsletter meta data
 *
 */

function save_mailing_lists_metadata( $term_id ) {
	if ( isset($_POST['email_from']) )
		update_term_meta( $term_id, 'email_from', esc_attr($_POST['email_from']) );

	if ( isset($_POST['email_title']) )
		update_term_meta( $term_id, 'email_title', esc_attr($_POST['email_title']) );
}

/**
 * Add additional fields to the taxonomy add view
 * e.g. /wp-admin/edit-tags.php?taxonomy=category
 */

function extract_posts()
{
	$posts=get_posts();
	return $posts;
}

function sendit_add_custom_box() 
{
  if( function_exists( 'add_meta_box' ))
  {
	//template metabox header and footer
	add_meta_box( 'template_html', __( 'Edit newsletter template', 'sendit' ),'sendit_html_box', 'sendit_template', 'advanced','high' );
	//content choice send element to editor
	add_meta_box( 'content_choice', __( 'Append content from existing posts', 'sendit' ),'sendit_content_box', 'newsletter', 'advanced','high' );
	//template choice from newsletter
	add_meta_box( 'template_choice', __( 'Select template for newsletter', 'sendit' ),'sendit_template_select', 'newsletter', 'side','high' );
    
   } 
}


function sendit_template_select($post)
{
query_posts('post_type=sendit_template');

 ?>
<select name="template_id" id="template_id">
			<option value="<?php echo get_post_meta($post->ID,'template_id', true);?>" selected="selected"><?php echo get_the_title(get_post_meta($post->ID,'template_id', true));?></option>
			<?php
			  while (have_posts()) : the_post(); ?>
				<option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
				<?php

			  endwhile;
				wp_reset_query();
			?>
		</select>

<?php 
}




function sendit_html_box($post)
{
	
	
	
	
	$css=get_post_meta($post->ID, 'newsletter_css', TRUE);
	$header=get_post_meta($post->ID, 'headerhtml', TRUE);
	$footer=get_post_meta($post->ID, 'footerhtml', TRUE); 
	?>
	<h3><?php _e('Custom Css','sendit'); ?></h3>
	<textarea name="newsletter_css" cols="80" rows="20"><?php echo $css;  ?></textarea>
	<h3><?php _e('Html Header', 'sendit') ?></h3>
	<textarea name="headerhtml" cols="80" rows="20"><?php echo $header;  ?></textarea>
	
	
	
	<?php 
	//wp_editor($header, 'headerhtml', $settings = array() );
	?>
	<h3><?php _e('Html Footer', 'sendit') ?></h3>
	<textarea name="footerhtml" cols="80" rows="20"><?php echo $footer;  ?></textarea>
	<?php 
	//wp_editor($footer, 'footerhtml', $settings = array() );

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
	//if ( !wp_verify_nonce( $_POST['sendit_noncename'], 'sendit_noncename'.$post_id ))
		//return $post_id;
 
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
		//save which template
		update_post_meta($post_id, 'template_id',$_POST['template_id']);
		return(esc_attr($_POST));
	}
}


add_action('save_post', 'sendit_template_postdata');

function sendit_template_postdata( $post_id )
{
 	//print_r($_POST);
	//if ( !wp_verify_nonce( $_POST['sendit_noncename'], 'sendit_noncename'.$post_id )) return $post_id;
 
 	 if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
 
  	if ( !current_user_can( 'edit_page', $post_id )) return $post_id;
 	$post = get_post($post_id);
	//obtain custom field meta for this post
     $custom_fields = get_post_custom($post_id);

    if(!$custom_fields) return;

    foreach($custom_fields as $key=>$custom_field):
        //$custom_field is an array of values associated with $key - even if there is only one value. 
        //Filter to remove empty values.
        //Be warned this will remove anything that casts as false, e.g. 0 or false 
        //- if you don't want this, specify a callback.
        //See php documentation on array_filter
        $values = array_filter($custom_field);

        //After removing 'empty' fields, is array empty?
        if(empty($values)):
            delete_post_meta($post_id,$key); //Remove post's custom field
        endif;
    endforeach; 
	
	
	if ($post->post_type == 'sendit_template') {
			
		update_post_meta($post_id, 'newsletter_css', $_POST['newsletter_css']);	
		update_post_meta($post_id, 'headerhtml', $_POST['headerhtml']);	
		update_post_meta($post_id, 'footerhtml', $_POST['footerhtml']);

		return(esc_attr($_POST));
	}
}



?>