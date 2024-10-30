<?php
/**
 * Gwolle Guestbook functions for Monitor.chat plugin
 */


// No direct calls to this script
if ( strpos($_SERVER['PHP_SELF'], basename(__FILE__) )) {
        die('No direct calls allowed!');
}

// ########## INSTANT MESSAGE ON GWOLLE GUESTBOOK ENTRY ##########

function monitorchat_send_xmpp_on_gwolle_guestbook_entry($entry){
  if(!monitorchat_is_enabled('monitorchat_gwolle_guestbook_enabled')){return;} // enabled or not?
  $message=monitorchat_format_message_for_gwolle_guestbook_entry($entry);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  $recipient=get_option('monitorchat_updraft_backup_recipient');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR GWOLLE GUESTBOOK ENTRY ##########

function monitorchat_format_message_for_gwolle_guestbook_entry($entry){
 $message=get_option('monitorchat_gwolle_guestbook_message');
 if(file_exists(WP_PLUGIN_DIR.'/gwolle-gb/functions/gb-formatting.php')){
   require_once (WP_PLUGIN_DIR.'/gwolle-gb/functions/gb-formatting.php');
 }
 $content = gwolle_gb_sanitize_output( $entry->get_content(), 'content' );

 $author_email = $entry->get_author_email(); 
 $istrial=monitorchat_validate_apikey(get_option('monitorchat_global_api_key'));

 $replacethis=array('<AUTHOREMAIL>','<GWOLLE>');
 $mask_pii=get_option('monitorchat_global_mask_pii');
 if(($istrial!='PERM')||($mask_pii=='on')){
   $withthis=array(monitorchat_mask_email_address($author_email),
                                                  $content);
 }else{
   $withthis=array($author_email,
                   $content);}
 return monitorchat_sanitize(str_replace($replacethis,$withthis,$message));
}

// ########## INITIALIZE ##########
add_action('gwolle_gb_save_entry_frontend','monitorchat_send_xmpp_on_gwolle_guestbook_entry',99,1);
