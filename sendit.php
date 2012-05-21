<?php
/*
Plugin Name: Sendit (Nightly Build)
Plugin URI: http://www.giuseppesurace.com/sendit-wp-newsletter-mailing-list/
Description: Wordpress newsletter plugin Sendit v 2 is totally rebuilt and custom post type based. Multiple ajax management added to 2.1.0. You can extend it and buy scheduler and newsletter tracking tool, more fields tool, export tool and others at http://sendit.wordpressplanet.org. With the new Sendit you can Send also  one of more of your post to your subscribers and manage mailing list in 2 click. New version also include an SMTP configuration and import functions from comments and author emails.
Version: 2.1.0
Author: Giuseppe Surace
Author URI: http://www.giuseppesurace.com
*/

include_once plugin_dir_path( __FILE__ ).'/libs/install-core.php';
include_once plugin_dir_path( __FILE__ ).'/libs/markup.php';
include_once plugin_dir_path( __FILE__ ).'/libs/actions.php';
include_once plugin_dir_path( __FILE__ ).'/libs/admin.php';
include_once plugin_dir_path( __FILE__ ).'/libs/extensions-handler.php';
include_once plugin_dir_path( __FILE__ ).'/libs/import.php';

load_plugin_textdomain('sendit', false, basename(dirname(__FILE__)) . '/languages'); //thanks to Davide http://www.jqueryitalia.org

register_activation_hook( __FILE__, 'sendit_install' );
register_activation_hook( __FILE__, 'sendit_sampledata');

/* Display a notice that can be dismissed */
 
add_action('admin_notices', 'sendit_admin_notice');
 
function sendit_admin_notice() {
   global $sendit_db_version;

   $sendit_db_version = SENDIT_DB_VERSION;
   $installed_version = get_option('sendit_db_version');
    global $current_user ;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */

    if ($sendit_db_version!=$installed_version) {
        echo '<div class="updated"><h2>Warning!</h2>';
        printf(__('You need to run Update of Sendit plugin table structure NOW!! | <a href="admin.php?page=update-sendit&upgrade_from_box=1">Click here to run process &raquo;</a>'), '');
        echo "</p></div>";
    } 
    
    else
    
    {
    	if ( ! get_user_meta($user_id, 'sendit_ignore') ) {
        	echo '<div class="updated"><p>';
        	printf(__('Your Sendit database table structure is currently updated to latest version '.SENDIT_DB_VERSION.' | <a href="%1$s">Hide this Notice</a>'), admin_url( 'admin.php?page=sendit/libs/admin.php&sendit_ignore=0'));
        	echo "</p></div>";
    	}
    }

	




}
 
add_action('admin_init', 'sendit_ignore');
 
function sendit_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['sendit_ignore']) && '0' == $_GET['sendit_ignore'] ) {
             add_user_meta($user_id, 'sendit_ignore', 'true', true);
    }
}




add_action('wp_head', 'sendit_js');
add_action('admin_head', 'sendit_js');
add_action('wp_head', 'sendit_loading_image');
add_action('wp_head', 'sendit_register_head');
add_action('plugins_loaded','DisplayForm');
add_action('admin_menu', 'gestisci_menu');

add_action('admin_head', 'sendit_admin_head');
add_action('admin_head', 'sendit_admin_js');
add_action('init', 'sendit_custom_post_type_init');
add_action('save_post', 'sendit_save_postdata');

add_action('save_post', 'send_newsletter');





?>