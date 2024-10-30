<?php
/**
 * Wordpress Functions for Monitor.chat plugin
 */


// No direct calls to this script
if ( strpos($_SERVER['PHP_SELF'], basename(__FILE__) )) {
        die('No direct calls allowed!');
}

// ########## FORMAT MESSAGE BASED ON USER OBJECT ##########

function monitorchat_format_message_for_user($msg,$usr){
$current_user = $usr;
if(empty($current_user)){$current_user=wp_get_current_user();}
$replacethis=array("<USERNAME>",
                   "<USEREMAIL>",
                   "<USERLEVEL>",
                   "<USERFIRSTNAME>",
                   "<USERLASTNAME>",
                   "<USERDISPLAYNAME>",
                   "<USERID>",
                   "<USERIPADDRESS>",
                   "<USERROLES>",
                   "<USERAGENT>"
);
$istrial=monitorchat_validate_apikey(get_option('monitorchat_global_api_key'));
$mask_pii=get_option('monitorchat_global_mask_pii');
if(($istrial!='PERM')||($mask_pii=='on')){
$withthis=array(monitorchat_mask_string($current_user->user_login),
                monitorchat_mask_email_address($current_user->user_email),
                $current_user->user_level,
                monitorchat_mask_string($current_user->user_firstname),
                monitorchat_mask_string($current_user->user_lastname),
                monitorchat_mask_string($current_user->display_name),
                $current_user->ID,
                monitorchat_getUserIpAddr(),
                implode(",", (array) $current_user->roles),
                $_SERVER['HTTP_USER_AGENT']);
}else{
$withthis=array($current_user->user_login,
                $current_user->user_email,
                $current_user->user_level,
                $current_user->user_firstname,
                $current_user->user_lastname,
                $current_user->display_name,
                $current_user->ID,
                monitorchat_getUserIpAddr(),
                implode(",", (array) $current_user->roles),
                $_SERVER['HTTP_USER_AGENT']);
}

$tt=str_replace($replacethis,$withthis,$msg);
return monitorchat_sanitize($tt);
}


// ########## INSTANT MESSAGE ON LOGIN ##########

function monitorchat_send_xmpp_on_login($login){

  if(!monitorchat_is_enabled('monitorchat_logins_enabled')){return;} // enabled or not?

  $user = get_user_by('login',$login); 
  $exclude_role=get_option('monitorchat_logins_exclude');
  if ( in_array($exclude_role, (array) $user->roles )) {return;} //user has excluded role or not?

  $recipient=get_option('monitorchat_logins_recipient');
  $message=get_option('monitorchat_logins_message');
  $message=monitorchat_format_message_for_user($message,$user);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
return;
}

// ########## INSTANT MESSAGE ON LOGOUT ##########

function monitorchat_send_xmpp_on_logout(){

  if(!monitorchat_is_enabled('monitorchat_logouts_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_logouts_recipient');
  $message=get_option('monitorchat_logouts_message');
  $message=monitorchat_format_message_for_user($message,null);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
return;
}

// ########## INSTANT MESSAGE ON FAILED LOGIN ##########

function monitorchat_send_xmpp_on_failed_login(){

  if(!monitorchat_is_enabled('monitorchat_failed_logins_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_failed_logins_recipient');
  $message=get_option('monitorchat_failed_logins_message');
  $message=monitorchat_format_message_for_user($message,null);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
return;
}

// ########## INSTANT MESSAGE ON NEW USER REGISTRATION ##########

function monitorchat_send_xmpp_on_user_register(){

  if(!monitorchat_is_enabled('monitorchat_user_register_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_user_register_recipient');
  $message=get_option('monitorchat_user_register_message');
  $message=monitorchat_format_message_for_user($message,null);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
return;
}

// ########## INSTANT MESSAGE ON USER UPDATES PROFILE ##########

function monitorchat_send_xmpp_on_profile_update(){

  if(!monitorchat_is_enabled('monitorchat_profile_update_enabled')){return;} // enabled or not?
  if(!is_user_logged_in()){return;} // We don't want new registrations to trigger this
  $current_user=wp_get_current_user();
  $exclude_role=get_option('monitorchat_profile_update_exclude');
  if ( in_array($exclude_role, (array) $current_user->roles )) {return;} //user has excluded role or not?

  $recipient=get_option('monitorchat_profile_update_recipient');
  $message=get_option('monitorchat_profile_update_message');
  $message=monitorchat_format_message_for_user($message,$current_user);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
return;
}

// ########## INSTANT MESSAGE ON POST, INCLUDING NEW ##########

function monitorchat_send_xmpp_on_post($new_status, $old_status, $post) {  

  if( $new_status=='publish' && $old_status != 'publish' && $post->post_type == 'post') {

    if(!monitorchat_is_enabled('monitorchat_publish_post_enabled')){return;} // enabled or not?
  
    $current_user=wp_get_current_user();
    $exclude_role=get_option('monitorchat_publish_post_exclude');
    if ( in_array($exclude_role, (array) $current_user->roles )) {return;} //user has excluded role or not?

    $recipient=get_option('monitorchat_publish_post_recipient');
    $message=get_option('monitorchat_publish_post_message');
    $message=monitorchat_format_message_from_post($message,$post);
    if(empty($message)){return;} // if message is empty then leave

    $send_using=get_option('monitorchat_global_send_using');
    if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
    if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
    if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
    return;
  }

  if( $new_status=='pending' && $old_status != 'pending' && $post->post_type == 'post') {
    if(!monitorchat_is_enabled('monitorchat_pending_post_enabled')){return;} // enabled or not?

    $current_user=wp_get_current_user();
    $exclude_role=get_option('monitorchat_pending_post_exclude');
    if ( in_array($exclude_role, (array) $current_user->roles )) {return;} //user has excluded role or not?

    $recipient=get_option('monitorchat_pending_post_recipient');
    $message=get_option('monitorchat_pending_post_message');
    $message=monitorchat_format_message_from_post($message,$post);
    if(empty($message)){return;} // if message is empty then leave

    $send_using=get_option('monitorchat_global_send_using');
    if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
    if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
    if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
    return;
  }

  if( $new_status=='publish' && $old_status != 'publish' && $post->post_type == 'page') {

    if(!monitorchat_is_enabled('monitorchat_publish_page_enabled')){return;} // enabled or not?

    $current_user=wp_get_current_user();
    $exclude_role=get_option('monitorchat_publish_page_exclude');
    if ( in_array($exclude_role, (array) $current_user->roles )) {return;} //user has excluded role or not?

    $recipient=get_option('monitorchat_publish_page_recipient');
    $message=get_option('monitorchat_publish_page_message');
    $message=monitorchat_format_message_from_post($message,$post);
    if(empty($message)){return;} // if message is empty then leave

    $send_using=get_option('monitorchat_global_send_using');
    if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
    if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
    if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
    return;
  }

  if( $new_status=='future' && $old_status != 'future' && $post->post_type == 'post') {
    if(!monitorchat_is_enabled('monitorchat_future_post_enabled')){return;} // enabled or not?

    $current_user=wp_get_current_user();
    $exclude_role=get_option('monitorchat_future_post_exclude');
    if ( in_array($exclude_role, (array) $current_user->roles )) {return;} //user has excluded role or not?

    $recipient=get_option('monitorchat_future_post_recipient');
    $message=get_option('monitorchat_future_post_message');
    $message=monitorchat_format_message_from_post($message,$post);
    if(empty($message)){return;} // if message is empty then leave

    $send_using=get_option('monitorchat_global_send_using');
    if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
    if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
    if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
    return;
  }

  if( $new_status=='publish' && 
      $old_status != 'publish' && 
      $post->post_type == 'product' &&
      is_plugin_active('woocommerce/woocommerce.php')) { // WOOCOMMERCE
    if(!monitorchat_is_enabled('monitorchat_woocommerce_new_product_enabled')){return;} // enabled or not?

    $current_user=wp_get_current_user();
    $exclude_role=get_option('monitorchat_woocommerce_new_product_exclude');
    if ( in_array($exclude_role, (array) $current_user->roles )) {return;} //user has excluded role or not?

    $recipient=get_option('monitorchat_woocommerce_new_product_recipient');
    $message=get_option('monitorchat_woocommerce_new_product_message');
    $message=monitorchat_format_message_for_woocommerce_new_product($message,$post);
    $message=monitorchat_format_message_for_user($message,null);
    if(empty($message)){return;} // if message is empty then leave

    $send_using=get_option('monitorchat_global_send_using');
    if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
    if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
    if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
    return;
  }
}



// ########## FORMAT MESSAGE FOR POST, INCLUDING NEW ##########

function monitorchat_format_message_from_post($msg,$post){ 
  $replacethis=array("<AUTHOR>",
                   "<TITLE>",
                   "<CONTENT>",
                   "<EXCERPT>",
                   "<STATUS>"
  );

  $author=(get_user_by('id',$post->post_author));
  $istrial=monitorchat_validate_apikey(get_option('monitorchat_global_api_key'));
  $mask_pii=get_option('monitorchat_global_mask_pii');
  
  if(($istrial!='PERM')||($mask_pii=='on')){
    $withthis=array(monitorchat_mask_string($author->user_login),
                  wp_strip_all_tags($post->post_title),
                  wp_strip_all_tags($post->post_content),
                  wp_strip_all_tags($post->post_excerpt),
                  $post->post_status);
  }else{
    $withthis=array($author->user_login,
                  wp_strip_all_tags($post->post_title),
                  wp_strip_all_tags($post->post_content),
                  wp_strip_all_tags($post->post_excerpt),
                  $post->post_status);
  }

  $tt=str_replace($replacethis,$withthis,$msg);
  return monitorchat_sanitize($tt);
}

// ########## INSTANT MESSAGE ON CREATION OF TERM ##########

function monitorchat_send_xmpp_on_create_term($term_id, $tt_id, $taxonomy) {

  if(!monitorchat_is_enabled('monitorchat_create_term_enabled')){return;} // enabled or not?

  $current_user=wp_get_current_user();
  $exclude_role=get_option('monitorchat_create_term_exclude');
  if ( in_array($exclude_role, (array) $current_user->roles )) {return;} //user has excluded role or not?

  $recipient=get_option('monitorchat_create_term_recipient');
  $message=monitorchat_format_message_for_create_term($term_id);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR CREATE TERM ##########

function monitorchat_format_message_for_create_term($term_id){
  $message=get_option('monitorchat_create_term_message');
  $term = get_term($term_id);
  $term_name = $term->name;
  $term_slug = $term->slug;
  $term_description = $term->description;
  $replacethis=array("<TERMID>",
                     "<TERMNAME>",
                     "<TERMSLUG>",
                     "<TERMDESC>");
  $withthis=array($term_id,
                  $term_name,
                  $term_slug,
                  $term_description);

  $tt=str_replace($replacethis,$withthis,$message);
  $tt=monitorchat_format_message_for_user($tt,null);
  return monitorchat_sanitize($tt);
}


// ########## INSTANT MESSAGE ON COMMENT ##########

function monitorchat_send_xmpp_on_comment_post($comment_ID, $comment_approved, $commentdata ) {

  if(!monitorchat_is_enabled('monitorchat_comment_post_enabled')){return;} // enabled or not?

  $current_user=wp_get_current_user();
  $exclude_role=get_option('monitorchat_publish_post_exclude');
  if ( in_array($exclude_role, (array) $current_user->roles )) {return;} //user has excluded role or not?

  $recipient=get_option('monitorchat_comment_post_recipient');
  $message=get_option('monitorchat_comment_post_message');
  $message=monitorchat_format_message_from_comment($message,$commentdata);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR COMMENT ##########

function monitorchat_format_message_from_comment($msg,$comment){ 

$replacethis=array("<AUTHOR>",
                   "<AUTHOREMAIL>",
                   "<COMMENT>",
                   "<POSTTITLE>");

  $istrial=monitorchat_validate_apikey(get_option('monitorchat_global_api_key'));
  $mask_pii=get_option('monitorchat_global_mask_pii');

  if(($istrial!='PERM')||($mask_pii=='on')){
    $withthis=array(monitorchat_mask_string($comment["comment_author"]),
                    monitorchat_mask_email_address($comment["comment_author_email"]), 
                    $comment["comment_content"],
                    get_the_title($comment['comment_post_ID']));
  }else{
    $withthis=array($comment["comment_author"],
                    $comment["comment_author_email"],
                    $comment["comment_content"],
                    get_the_title($comment['comment_post_ID']));
  }
  $tt=str_replace($replacethis,$withthis,$msg);
  return monitorchat_sanitize($tt);
}

// ########## INSTANT MESSAGE ON ATTACHMENT UPLOAD ##########

function monitorchat_send_xmpp_on_attachment_upload($post_ID) {
  if(!monitorchat_is_enabled('monitorchat_attachment_upload_enabled')){return;} // enabled or not?

  $current_user=wp_get_current_user();
  $exclude_role=get_option('monitorchat_attachment_upload_exclude');
  if ( in_array($exclude_role, (array) $current_user->roles )) {return;} //user has excluded role or not?

  $recipient=get_option('monitorchat_attachment_upload_recipient');
  $message=get_option('monitorchat_attachment_upload_message');
  $message=str_replace('<URL>',wp_get_attachment_url($post_ID),$message);
  $message=monitorchat_sanitize($message);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## INSTANT MESSAGE ON CRON HEARTBEAT ##########

function monitorchat_send_xmpp_on_heartbeat() {

  if(!monitorchat_is_enabled('monitorchat_heartbeat_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_heartbeat_recipient');
  $message=monitorchat_sanitize(get_option('monitorchat_heartbeat_message'));
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## INSTANT MESSAGE ON WP CORE UPDATE CHECK ##########

function monitorchat_send_xmpp_on_wp_core_update_check() {

  if(!monitorchat_is_enabled('monitorchat_wp_core_update_check_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_wp_core_update_check_recipient');
  $message=monitorchat_format_message_for_wp_core_update_check();
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR WP CORE UPDATE CHECK ##########

function monitorchat_format_message_for_wp_core_update_check(){
  if(file_exists(ABSPATH . 'wp-admin/includes/update.php')){
    require_once (ABSPATH . 'wp-admin/includes/update.php');}
  $somethigwentwrong='<ASTONISHED> Something went wrong when checking the installed '.
                     'Wordpress version against the preferred version.';

  $cur = get_preferred_from_update_core();
  if ( ! is_object( $cur ) ) { $cur = new stdClass; }
  if ( ! isset( $cur->current ) ) { return $somethigwentwrong; }
  if ( ! isset( $cur->response ) ) { return $somethigwentwrong; }

  if($cur->response=='upgrade'){ $msg=get_option('monitorchat_wp_core_update_check_message');
    if(empty(trim($msg))){$msg='<THINKING> Perhaps it is time to update Wordpress!'."\n".
                               '<INSTALLEDVERSION> is the installed version.'."\n".
                               '<PREFERREDVERSION> is the preferred version.';}
  }
  if($cur->response=='latest') { $msg=get_option('monitorchat_wp_core_update_check_alt_message');
    if(empty(trim($msg))){$msg='<RELAXED> Wordpress Core is up to date!'."\n".
                               'The installed version is <INSTALLEDVERSION>.';}
  }

  $replacethis=array("<PREFERREDVERSION>",
                     "<INSTALLEDVERSION>");
  $withthis=array($cur->current, // current in this case is the latest version of WP available
                get_bloginfo( 'version', 'display' ));
  $tt=str_replace($replacethis,$withthis,$msg);

  return monitorchat_sanitize($tt);
}

// ########## INSTANT MESSAGE ON PLUGINS UPDATE CHECK ##########

function monitorchat_send_xmpp_on_plugins_update_check() {

  if(!monitorchat_is_enabled('monitorchat_plugins_update_check_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_plugins_update_check_recipient');
  $message=monitorchat_format_message_for_plugin_update_check();
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR PLUGINS UPDATE CHECK ##########

function monitorchat_format_message_for_plugin_update_check(){
  if(file_exists(ABSPATH . 'wp-admin/includes/plugin.php')){
    require_once (ABSPATH . 'wp-admin/includes/plugin.php');}
  if(file_exists(ABSPATH . 'wp-admin/includes/update.php')){
    require_once (ABSPATH . 'wp-admin/includes/update.php');}
  
  $msg=get_option('monitorchat_plugins_update_check_message');
  if(empty(trim($msg))){$msg='<OWL> There are Wordpress plugins that you may wish to update:'."\n".
                      '<PLUGINSTOUPDATE>';}

  $altmsg=get_option('monitorchat_plugins_update_check_alt_message');
  if(empty(trim($altmsg))){$altmsg='<SUN> Wordpress plugins are all up to date.';}
  $altmsg=str_replace('<PLUGINSTOUPDATE>','',$altmsg); // just in case someone puts it in.

  $plugins = get_plugin_updates();
  if ( empty( $plugins ) ) 
       { return monitorchat_sanitize($altmsg); }

  $pluginstoupdate=null; $add_to = null;

  $update_plugins = get_site_transient('update_plugins');
    if ($update_plugins && !empty($update_plugins->response)) {
      foreach ($update_plugins->response as $plugin => $vals) {
             if (!function_exists('get_plugin_data')) {
                 require_once ABSPATH . '/wp-admin/includes/plugin.php';
             }
        $plugin_file = monitorchat_getpluginbasedir().$plugin;
        $data = get_plugin_data($plugin_file);

  $add_to = sprintf(
           /* translators: 1: Plugin version, 2: Plugin name 3: New version. */
           __( '<POINTRIGHT> You have version %1$s of plugin %2$s installed. Version %3$s is available.' ),
           $data["Version"],
           $data["Name"],
           $vals->new_version );

  if(is_plugin_active($plugin)){$add_to=$add_to.' [active]';}
  if(!empty($add_to)){$add_to=$add_to."\n";}
  $pluginstoupdate = $pluginstoupdate . $add_to;
  $add_to = null;
         }
     }

  $pluginstoupdate=substr($pluginstoupdate, 0, -1);
  $replacethis=array("<PLUGINSTOUPDATE>");
  $withthis=array($pluginstoupdate);
  
  $tt=str_replace($replacethis,$withthis,$msg);

return monitorchat_sanitize($tt);
}

// ########## INSTANT MESSAGE ON THEMES UPDATE CHECK ##########

function monitorchat_send_xmpp_on_themes_update_check() {

  if(!monitorchat_is_enabled('monitorchat_themes_update_check_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_themes_update_check_recipient');
  $message=monitorchat_format_message_for_theme_update_check();
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR THEMES UPDATE CHECK ##########

function monitorchat_format_message_for_theme_update_check(){
  if(file_exists(ABSPATH . 'wp-admin/includes/update.php')){
    require_once (ABSPATH . 'wp-admin/includes/update.php');}

  $msg=get_option('monitorchat_themes_update_check_message');
  if(empty(trim($msg))){$msg='<BIRD> There are Wordpress themes that you may wish to update:'."\n".
                      '<THEMESTOUPDATE>';}

  $altmsg=get_option('monitorchat_themes_update_check_alt_message');
  if(empty(trim($altmsg))){$altmsg='<GLOBE> All Wordpress themes are up to date.';}
  $altmsg=str_replace('<THEMESTOUPDATE>','',$altmsg); // just in case someone puts it in.

  $active_theme=wp_get_theme();

  $themes = get_theme_updates();
  if ( empty( $themes ) )
       { return $altmsg; }

  $themestoupdate=null; $add_to = null;

  foreach ( $themes as $stylesheet => $theme ) {
    $add_to = sprintf(
                    /* translators: 1: Theme version, 2: Theme name 3: New version. */
                    __( '<POINTRIGHT> You have version %1$s of theme %2$s installed. Version %3$s is available.' ),
                    $theme->display( 'Version' ),
                    $theme->display( 'Name' ),
                    $theme->update['new_version'] );
    if($active_theme->Name == $theme->display( 'Name' )){$add_to = $add_to.' [active]';}
    if(!empty($add_to)){$add_to=$add_to."\n";}
    // report only the themes that are not up to date:
    if($theme->display( 'Version' ) != $theme->update['new_version']){$themestoupdate = $themestoupdate . $add_to;}
    $add_to = null;
  }

  $replacethis=array("<THEMESTOUPDATE>");
  $withthis=array($themestoupdate);

  $tt=str_replace($replacethis,$withthis,$msg);
  return monitorchat_sanitize($tt);
}

// ########## INSTANT MESSAGE ON UPGRADER PROCESS COMPLETES ##########

function monitorchat_send_xmpp_on_upgrader_process_complete($upgrader_object, $options){
  if(!monitorchat_is_enabled('monitorchat_upgrader_process_complete_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_upgrader_process_complete_recipient');
  $message=monitorchat_format_xmpp_for_upgrader_process_complete($upgrader_object,$options);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR UPGRADER PROCESS COMPLETES ##########

function monitorchat_format_xmpp_for_upgrader_process_complete($upgrader_object, $options){
  if((empty($upgrader_object))||
     (is_wp_error($upgrader_object))){
       return __('<REDFLAG> A Wordpress update process has failed to complete.');}
  
  $message=get_option('monitorchat_upgrader_process_complete_message');

  if( $options['action'] == 'update' && $options['type'] == 'plugin'){
  $name=$upgrader_object->skin->plugin_info["Name"];
  if(empty($name)){
     // inconsistent definition for $options, 
     // sometimes "plugins" (plural) as array, and other times "plugin" (singular)
     if(is_array($options["plugins"])){$name=$options["plugins"][0];}else{$name=$options["plugin"];}
  }
  if(empty($name)){$name='Unknown';} // just in case name is still empty.

    if(is_wp_error($upgrader_object->skin->result)){
     $tt.=__('An error occurred when updating plugin ').$name.".\n";
     $tt.='<REDFLAG> '.$upgrader_object->skin->result->get_error_message();
    }else{
     $tt.= __('The plugin ').$name.__(' has been updated.');
    }
}

  if( $options['action'] == 'update' && $options['type'] == 'theme'){
  $name=$upgrader_object->skin->theme_info["Name"];
  if(empty($name)){
     // inconsistent definition for $options,
     // sometimes "themes" (plural) as array, and other times "theme" (singular)
     if(is_array($options["themes"])){$name=$options["themes"][0];}else{$name=$options["theme"];}
  }
  if(empty($name)){$name='Unknown';} // just in case name is still empty.

    if(is_wp_error($upgrader_object->skin->result)){
     $tt.=__('An error occurred when updating theme ').$name.".\n";
     $tt.='<REDFLAG> '.$upgrader_object->skin->result->get_error_message();
    }else{
     $tt.= __('The theme ').$name.__(' has been updated.');
    }
}

  if( $options['action'] == 'update' && $options['type'] == 'core'){
     if (function_exists('get_core_updates')) {
         $update_wordpress = get_core_updates(array('dismissed' => false));
         if ((!empty($update_wordpress))&& ($update_wordpress[0]->response != 'latest')){
             $tt.=__('An attempt has been made to update Wordpress core.')."\n".
                      __('<REDFLAG> Wordpress Core is not at the latest version.');
             }else{
             $tt.=__('Wordpress core has been updated successfully.');}
     }
  if (empty($message)){
          $tt.= __('An attempt has been made to update Wordpress core.');
  }
 }
  $replacethis=array('<UPDATED>');
  $withthis=array($tt);
  $tt=str_replace($replacethis,$withthis,$message);
  return monitorchat_sanitize($tt);
}

// ########## INITIALIZE ##########
  add_action('wp_login','monitorchat_send_xmpp_on_login',99); // 99= low priority
  add_action('wp_logout','monitorchat_send_xmpp_on_logout',99);
  add_action('wp_login_failed','monitorchat_send_xmpp_on_failed_login',99);
  add_action('user_register','monitorchat_send_xmpp_on_user_register',99);
  add_action('profile_update','monitorchat_send_xmpp_on_profile_update',99);
  add_action('transition_post_status', 'monitorchat_send_xmpp_on_post', 99, 3);
  add_action('comment_post','monitorchat_send_xmpp_on_comment_post',99,3);
  add_action('add_attachment','monitorchat_send_xmpp_on_attachment_upload',99,1);
  add_action('create_term','monitorchat_send_xmpp_on_create_term',99, 3);
  add_action( 'monitorchat_heartbeat', 'monitorchat_send_xmpp_on_heartbeat' );
  add_action( 'monitorchat_wp_core_update_check','monitorchat_send_xmpp_on_wp_core_update_check');
  add_action( 'monitorchat_plugins_update_check','monitorchat_send_xmpp_on_plugins_update_check');
  add_action( 'monitorchat_themes_update_check','monitorchat_send_xmpp_on_themes_update_check');
  add_action( 'upgrader_process_complete', 'monitorchat_send_xmpp_on_upgrader_process_complete', 99, 2 );
