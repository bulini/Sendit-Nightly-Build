<?php
function sendit_shortcode($atts) {
     $markup=sendit_markup($atts[id]);    
     return $markup;
}

add_shortcode('newsletter', 'sendit_shortcode');


add_shortcode("quote", "quote");  
function quote( $atts, $content = null ) {  
    return '<div class="right text">"'.$content.'"</div>';  
}
	
if (function_exists('sendit_morefields')) {

	add_action('init', 'add_senditbutton');  
	function add_senditbutton() {  
	   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  
	   {  
	     add_filter('mce_external_plugins', 'add_senditplugin');  
	     add_filter('mce_buttons_3', 'register_senditbutton');  
	   }  
	}  
	function register_senditbutton($buttons) {  
	   array_push($buttons, "quote");  
	   return $buttons;  
	}  
	function add_senditplugin($plugin_array) {  
	   $plugin_array['quote'] = get_bloginfo('siteurl').'/wp-content/plugins/sendit/js/senditcode.js';  
	   return $plugin_array;  
	}


}


function sendit_markup($id,$accepted='n')
{
     /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     	The standard HTML form for all usage (widget shortcode etc)
     +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

	$sendit_markup=get_option('sendit_markup');	
 	$sendit_markup=str_replace("{list_id}",$id, $sendit_markup);
 	$sendit_markup=str_replace("{accepted}",$accepted, $sendit_markup);

 	if(function_exists('sendit_morefields')):
 		$sendit_markup=str_replace("{sendit_morefields}",sendit_morefields(), $sendit_markup);
	else:
 		$sendit_markup=str_replace("{sendit_morefields}",'', $sendit_markup);
	endif;
			
 	$sendit_markup=str_replace("{subscribe_text}", get_option('sendit_subscribe_button_text'), $sendit_markup);
 	if(is_user_logged_in()):
		$sendit_markup.='<small><a href="wp-admin/admin.php?page=sendit_general_settings">'.__('Customize Widget','sendit').'</a></small>';
 	endif;
 	return $sendit_markup;

}

function sendit_js() 
{
  // Spingo js su header (x luca)
  wp_print_scripts( array('jquery' ));
  $sendit_morefields=get_option('sendit_dynamic_settings');
  $arr=json_decode($sendit_morefields);
  // Define custom JavaScript function
?>
		<script type="text/javascript">
		jQuery(document).ready(function(){	
		jQuery('input#email_add').focus(function() {
   			jQuery(this).val('');
		});
		
		
		jQuery('input.req').blur(function() {

   			if (jQuery(this).val() == "") {
  				jQuery(this).after('<span class="sendit_error">Required!</span>');
 			 	valid = false;
			} else {
				  jQuery(this).find('span.sendit_error').hide();
				  valid = true;				
			}

		});


			jQuery('#sendit_subscribe_button').click(function(){
				jQuery.ajax({
				beforeSend: function() {
 jQuery('#sendit_wait').show(); jQuery('#sendit_subscribe_button').hide();},
		        complete: function() { jQuery('#sendit_wait').hide(); jQuery('#sendit_subscribe_button').show(); },
				type: "POST",
		      	//data: ({jQuery("#senditform").serialize()}),
		      	data: ({options : jQuery("#senditform").serialize(),email_add : jQuery('#email_add').val(),lista : jQuery('#lista').val(),accepted: jQuery('#accepted').val()<?php  					  
				if(!empty($arr)): 	
	 				foreach($arr as $k=>$v):?>,<?php echo $v->name; ?>: jQuery('#<?php echo $v->name; ?>').val()<?php endforeach; ?><?php endif; ?>}),
		      	url: '<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/sendit/submit.php',
		  		success: function(data) {
		    	<?php if(get_option('sendit_response_mode')=='alert'): ?>
		   		alert(data);
		   		<?php else: ?>
		    	jQuery('#dati').html(data);
		    	<?php endif; ?>

		   		
		  }
		});
			});
		});
		

	
function checkemail(e){
  var emailfilter = /^w+[+.w-]*@([w-]+.)*w+[w-]*.([a-z]{2,4}|d+)$/i
  return emailfilter.test(e);
}
function checkphone(e) {
 var filter = /[0-9]/
 return filter.test(e);
}
		
		</script>
		

		
<?php
} 


function DisplayForm()
{
    if ( !function_exists('register_sidebar_widget') ){return; }
    register_sidebar_widget('Sendit Widget','JqueryForm');
    register_widget_control('Sendit Widget','Sendit_widget_options', 200, 200);    
}

function JqueryForm($args) {
    global $dcl_global;
    extract($args);
    $lista= get_option('id_lista');
    //before_widget,before_title,after_title,after_widget

    $form_aggiunta=$before_widget."
             ".$before_title.get_option('titolo').$after_title;
  			$form_aggiunta.=sendit_markup($lista);
           // if (!$dcl_global) $form_aggiunta.="<p><small>Sendit <a href=\"http://www.giuseppesurace.com\">Wordpress  newsletter</a></small></p>";
            $form_aggiunta.=$after_widget;
    
    echo $form_aggiunta;
}

function Sendit_widget_options() {
        if ($_POST['id_lista']) {
            $id_lista=$_POST['id_lista'];
            $titolo=$_POST['titolo'];
            update_option('id_lista',$id_lista);
            update_option('titolo',$_POST['titolo']);
        }
        $id_lista = get_option('id_lista');
        $titolo = get_option('titolo');
        //titolo
        echo '<p><label for="titolo">'.__('Newsletter title: ', 'sendit').' <input id="titolo" name="titolo"  type="text" value="'.$titolo.'" /></label></p>';
        //id della mailing list
        echo '<p><label for="id_lista">'.__('Mailing list ID: ', 'sendit').' <input id="id_lista" name="id_lista" type="text" value="'.$id_lista.'" /></label></p>';
        
            
    }


function sendit_loading_image() {
    $siteurl = get_option('siteurl');
    $img_url = $siteurl . '/wp-content/plugins/sendit/images/loading.gif';
    echo '<style type="text/css">#sendit_wait{background:url('.$img_url.') no-repeat; height:40px;margin:10px;display:block;}</style>';    
}

function sendit_register_head() {
    //$siteurl = get_option('siteurl');
    //$url = $siteurl . '/wp-content/plugins/sendit/sendit.css';
    //echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
    echo '<style type="text/css">'.get_option('sendit_css').'</style>';
}

function sendit_admin_js()
{
	?>
	<script type="text/javascript" src="<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/sendit/js/jquery.jeditable.js" ></script>
    <script type="text/javascript">
	jQuery(document).ready(function($) {        
     
               $(".send_to_editor").click( function() {                   
                   var post_title = $(this).closest("div.post_box").find(".content_to_send").html();
                   alert('Article added to your newsletter');
                   send_to_editor(post_title);
              });
              
              $(".editable").editable("<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/sendit/ajax.php", {
              type : "text",
              submit    : "OK",
              name : "email",
      		  cancel    : "<?php echo __('cancel','sendit'); ?>",
      		  tooltip   : "<?php echo __('Click to edit','sendit'); ?>"
              }
              );
              
              
              $(".edit_select").editable("<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/sendit/ajax.php", {
              type : "select",
              data   : "{'n':'<?php echo __('not confirmed','sendit'); ?>','y':'<?php echo __('confirmed','sendit'); ?>','d':'<?php echo __('delete','sendit'); ?>'}",
              submit    : "OK",
              name : "accepted",
      		  cancel    : "<?php echo __('cancel','sendit'); ?>",
      		  tooltip   : "<?php echo __('Click to edit','sendit'); ?>"
              }
              );
              
             /* todo!!!
             $(".buttonsend").click(function(){
              $.post("<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/sendit/ajax.php", { name: "John", time: "2pm" },
   				function(data) {
    			 alert("Data Loaded: " + data);
   				});
   			 });
   			 */
              
              
 });
	</script>
<?php
 }


?>