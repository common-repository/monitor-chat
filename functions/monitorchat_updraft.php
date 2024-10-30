<?php
/**
 * Updraft Functions for Monitor.chat plugin
 */


// No direct calls to this script
if ( strpos($_SERVER['PHP_SELF'], basename(__FILE__) )) {
        die('No direct calls allowed!');
}

// ########## INSTANT MESSAGE ON UPDRAFT BACKUP ##########

function monitorchat_send_xmpp_on_backup_with_updraft($our_files){
  if(!monitorchat_is_enabled('monitorchat_updraft_backups_enabled')){return;} // enabled or not?
  $message=monitorchat_format_message_for_backup_with_updraft($our_files);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  $recipient=get_option('monitorchat_updraft_backup_recipient');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR UPDRAFT BACKUP ##########

function monitorchat_format_message_for_backup_with_updraft($our_files){
  $file_list=null;
  foreach((array) $our_files["plugins"] as $k){$file_list.=$k."\n";}
  foreach((array) $our_files["themes"] as $k){$file_list.=$k."\n";}
  foreach((array) $our_files["uploads"] as $k){$file_list.=$k."\n";}
  foreach((array) $our_files["others"] as $k){$file_list.=$k."\n";}
  if(!empty($our_files["db"])){$file_list.=$our_files["db"]."\n";}
  $file_list=substr($file_list, 0, -1);

  $message=get_option('monitorchat_updraft_backup_message');
  $replacethis=array('<UPDRAFTFILES>');
  $withthis=array( $file_list );
  $tt=str_replace($replacethis,$withthis,$message);

  return monitorchat_sanitize($tt);
}


// ########## INITIALIZE ##########
  add_action( 'updraft_final_backup_history','monitorchat_send_xmpp_on_backup_with_updraft',99,1);
