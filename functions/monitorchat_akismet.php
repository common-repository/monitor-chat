<?php
/**
 * Akismet Functions for Monitor.chat plugin
 */


// No direct calls to this script
if ( strpos($_SERVER['PHP_SELF'], basename(__FILE__) )) {
        die('No direct calls allowed!');
}

// ########## INSTANT MESSAGE ON AKISMET SPAM CAUGHT ##########

function monitorchat_send_xmpp_on_akismet_spam_caught(){

  if(!monitorchat_is_enabled('monitorchat_akismet_spam_caught_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_akismet_spam_caught_recipient');
  $message=monitorchat_sanitize(get_option('monitorchat_akismet_spam_caught_message'));
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## INITIALIZE ##########
  add_action( 'akismet_spam_caught', 'monitorchat_send_xmpp_on_akismet_spam_caught', 99, 1 );

