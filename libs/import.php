<?php

function ImportWpUsers() {
/*
 *IMPORTAZIONE DA WP-users
 */
 
 /*************************
MASS IMPORT da wp_users
**************************/

    global $_POST;
    global $wpdb;
    
     $table_liste =  $wpdb->prefix . "nl_liste";   
     $liste = $wpdb->get_results("SELECT id_lista, nomelista FROM $table_liste ");

    
    
    //disegno i div
    echo "<div class=\"wrap\"class=\"wrap\"><h2>".__('Import email from Authors (wp_users)', 'sendit')."</h2>";

    echo"<form action='$_SERVER[REQUEST_URI]' method='post' name='importform' id='importform'>
        <table>
            <tr><th scope=\"row\" width=\"600\" align=\"left\">".__('Select destination list and click on Import button to start. All email will be added to your mailing list', 'sendit')."<small><br />".__('(email address already presents will not be added)', 'sendit')."</small></label><th>";
               echo "<td></td></tr>
            <tr>
                <td>".__('Select Destination list', 'sendit')."
                <select name='list_id'>";
                
                foreach ($liste as $lista) {
                    
                    echo "<option value=".$lista->id_lista.">".$lista->nomelista."</option>";
                    
                }
                    
                echo "</select><input class=\"button-primary\" type=\"submit\" name=\"start\" value=\"".__('Import', 'sendit')."\" >
                </td>
            </tr>
            
            </table></form>";
            
            echo '
            <p>'.__("Do you think Sendit it\'s useful? Please send a donation to support our development and i really appreciate!", "sendit").'
            <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="giuseppe@streetlab.it">
<input type="hidden" name="item_name" value="Sendit Wordpress plugin">
<input type="hidden" name="currency_code" value="EUR">
<input type="hidden" name="amount" value="10.00">
<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!">
</form>';

    
    //nome tabella commenti = wp_comments
    $table_users = $wpdb->prefix . "users";
    //tabella email
    $table_email = $wpdb->prefix . "nl_email";
 
   if($_POST['start']) :   
        
        $users_emails = $wpdb->get_results("SELECT distinct user_email FROM $table_users");
        
        foreach ($users_emails as $user_email)
        {
            //verifico che gia non ci siano
            $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where email ='$user_email->user_email' and id_lista = '$_POST[list_id]';");
                        
                if($user_count>0) :
                    echo "<div class=\"error\">".sprintf(__('email %s already present', 'sendit'), $user_email->user_email)."</div>";
                else :
                //genero stringa univoca x conferme e cancellazioni sicure
                    $code = md5(uniqid(rand(), true));
                    $wpdb->query("INSERT INTO $table_email (email,id_lista, magic_string, accepted) VALUES ('$user_email->user_email', '$_POST[list_id]', '$code', 'y')");
                     echo '<div class="updated fade"><p><strong>'.sprintf(__('email %s succesfully added', 'sendit'), $user_email->user_email).'</strong></p></div>';   
                 endif;    
         
         
        
        //echo $comment_email->comment_author_email."<br /></br >";    
            
        }
        
    endif;    
            
        
}

function ImportWpComments() {
/*
 *IMPORTAZIONE DA WP-comments
 */
 
 /*************************
MASS IMPORT da wp_comments
**************************/

    global $_POST;
    global $wpdb;
    
     $table_liste =  $wpdb->prefix . "nl_liste";   
     $liste = $wpdb->get_results("SELECT id_lista, nomelista FROM $table_liste ");

    
    
    //disegno i div
    echo "<div class=\"wrap\"class=\"wrap\"><h2>".__('Import email from Comments approved (wp_comments)', 'sendit')."</h2>";

    echo"<form action='$_SERVER[REQUEST_URI]' method='post' name='importform' id='importform'>
        <table>
            <tr><th scope=\"row\" width=\"600\" align=\"left\">".__('Click on Import button to start. All comments authors email will be added to your mailing list selected', 'sendit')."<small><br />".__('(email address already presents will not be added)', 'sendit')."</small></label><th>";
               echo "<td></td></tr>
            <tr>
                <td>".__('Select Destination list', 'sendit')."
                <select name='list_id'>";
                
                foreach ($liste as $lista) {
                    
                    echo "<option value=".$lista->id_lista.">".$lista->nomelista."</option>";
                    
                }
                    
                echo "</select><input class=\"button-primary\" type=\"submit\" name=\"start\" value=\"".__('Import', 'sendit')."\" >
                </td>
            </tr>
            
            </table></form>";
            
            echo '
            <p>'.__("Do you think Sendit it\'s useful? Please send a donation to support our development and i really appreciate!", "sendit").'
            <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="giuseppe@streetlab.it">
<input type="hidden" name="item_name" value="Sendit Wordpress plugin">
<input type="hidden" name="currency_code" value="EUR">
<input type="hidden" name="amount" value="10.00">
<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!">
</form>';

    
    //nome tabella commenti = wp_comments
    $table_comments = $wpdb->prefix . "comments";
    //tabella email
    $table_email = $wpdb->prefix . "nl_email";
 
   if($_POST['start']) :   
        
        $users_emails = $wpdb->get_results("SELECT distinct comment_author_email FROM $table_comments WHERE comment_approved=1 and comment_author_email!=''");
        
        foreach ($users_emails as $user_email)
        {
            //verifico che gia non ci siano
            $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where email ='$user_email->comment_author_email' and id_lista = '$_POST[list_id]';");
                        
                if($user_count>0) :
                    echo "<div class=\"error\">".sprintf(__('email %s already present', 'sendit'), $user_email->user_email)."</div>";
                else :
                //genero stringa univoca x conferme e cancellazioni sicure
                    $code = md5(uniqid(rand(), true));
                    $wpdb->query("INSERT INTO $table_email (email,id_lista, magic_string, accepted) VALUES ('$user_email->comment_author_email', '$_POST[list_id]', '$code', 'y')");
                     echo '<div class="updated fade"><p><strong>'.sprintf(__('email %s succesfully added', 'sendit'), $user_email->comment_author_email).'</strong></p></div>';   
                 endif;    
         
         
        
        //echo $comment_email->comment_author_email."<br /></br >";    
            
        }
        
    endif;    
            
        
}



/*
 *IMPORTAZIONE DA BB-PRESS
 */
 
 /*************************
MASS IMPORT da BB-PRESS
**************************/
function ImportBbPress() {

    global $_POST;
    global $wpdb;
    
     $table_liste =  $wpdb->prefix . "nl_liste";   
     $liste = $wpdb->get_results("SELECT id_lista, nomelista FROM $table_liste ");

    
    
    //disegno i div
    echo "<div class=\"wrap\"class=\"wrap\"><h2>".__('Import email from BBpress Users (bb_users)', 'sendit')."</h2>";

    echo"<form action='$_SERVER[REQUEST_URI]' method='post' name='importform' id='importform'>
        <table>
            <tr><th scope=\"row\" width=\"600\" align=\"left\">".__('Click on Import button to start. All Authors email will be added to your mailing list ID 1', 'sendit')."<small><br />".__('(email address already presents will not be added)', 'sendit')."</small></label><th>";
               echo "<td><input type=\"submit\" name=\"start\" value=\"".__('Import', 'sendit')."\" ></td></tr>
            <tr>
                <td>".__('Select list', 'sendit')."
                <select name='list_id'>";
                
                foreach ($liste as $lista) {
                    
                    echo "<option value=".$lista->id_lista.">".$lista->nomelista."</option>";
                    
                }
                    
                echo "</select>
                </td>
            </tr>
            
            </table></form>";
            
            echo '
            <p>'.__("Do you think Sendit it\'s useful? Please send a donation to support our development and i really appreciate!", "sendit").'
            <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="giuseppe@streetlab.it">
<input type="hidden" name="item_name" value="Sendit Wordpress plugin">
<input type="hidden" name="currency_code" value="EUR">
<input type="hidden" name="amount" value="10.00">
<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!">
</form>';

    
    //nome tabella commenti = wp_comments
    
    $table_users =  "bb_users";
    
    //tabella email
    $table_email = $wpdb->prefix . "nl_email";
 
   if($_POST['start']) :   
        
        $users_emails = $wpdb->get_results("SELECT distinct user_email FROM $table_users");
        
        foreach ($users_emails as $user_email)
        {
            //verifico che gia non ci siano
            $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_email where email ='$user_email->user_email' and id_lista = '$_POST[list_id]';");
                        
                if($user_count>0) :
                    echo "<div class=\"error\">".sprintf(__('email %s already present', 'sendit'), $user_email->user_email)."</div>";
                else :
                //genero stringa univoca x conferme e cancellazioni sicure
                    $code = md5(uniqid(rand(), true));
                    $wpdb->query("INSERT INTO $table_email (email,id_lista, magic_string, accepted) VALUES ('$user_email->user_email', '$_POST[list_id]', '$code', 'y')");
                     echo '<div class="updated fade"><p><strong>'.sprintf(__('email %s succesfully added', 'sendit'), $user_email->user_email).'</strong></p></div>';   
                 endif;    
         
         
        
        //echo $comment_email->comment_author_email."<br /></br >";    
            
        }
        
    endif;    
            
        
}

?>