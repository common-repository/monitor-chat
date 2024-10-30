<?php
/**
 * General Functions for Monitor.chat plugin
 */


// No direct calls to this script
if ( strpos($_SERVER['PHP_SELF'], basename(__FILE__) )) {
        die('No direct calls allowed!');
}

// ########## GENERAL FUNCTIONS ##########
function monitorchat_display_apikey_report(){
  if (!current_user_can('activate_plugins')){ return null; }
  $apikey=get_option('monitorchat_global_api_key');
  $last_valid_apikey=get_option('monitorchat_global_last_valid_apikey');
  if($apikey != $last_valid_apikey){return null;} // just leave if not valid api key
  return '<div id="apikey-report" style="display:none;"><pre>'
          .$_SESSION["monitorchat_apikey_report"]
  //        .$_SESSION["monitorchat_last_report"] // uncomment to display timestamp of last report
          .'</pre></div>';
}
function monitorchat_display_apikey_report_link(){
  if (!current_user_can('activate_plugins')){ return null; }
  $apikey=get_option('monitorchat_global_api_key');
  $last_valid_apikey=get_option('monitorchat_global_last_valid_apikey');
  if($apikey != $last_valid_apikey){return null;} // just leave if not valid api key
  return '<a href="#TB_inline?&width=500&height=500&inlineId=apikey-report" class="thickbox">'.__('View API Key Report').'</a>'; 
}

function monitorchat_register_session(){
    if( (!session_id()) && (current_user_can('activate_plugins')) )
        session_start();
}

function monitorchat_validate_hostname($hostname){
if (filter_var($hostname, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)){
    return substr($hostname,0,24);
  }else{
    return monitorchat_gethostname();
  }
}

function monitorchat_gethostname(){
  if(get_option('monitorchat_global_hostname')){
   return get_option('monitorchat_global_hostname');
  }
  $hostname=gethostname(); if(empty($hostname)){$hostname='wordpress';}
  return substr($hostname,0,24);
}

function monitorchat_distribute_scheduled_hooks($interval = 999999){
  $start_in=300; // start in 5 minutes

  if(get_option('monitorchat_global_distribute_scheduled')){
    $not_closer_than=get_option('monitorchat_global_distribute_scheduled');}else{
    $not_closer_than=(35*60);}

     if(($interval < ($not_closer_than * 2))&&($interval > 60)){
       //shrink $not_closer_than for frequently scheduled events
       $not_closer_than = floor($interval/5);
     }
  

  $vals=array();
  if(wp_next_scheduled('monitorchat_wp_core_update_check')){
     array_push($vals,wp_next_scheduled('monitorchat_wp_core_update_check'));}
  if(wp_next_scheduled('monitorchat_plugins_update_check')){
     array_push($vals,wp_next_scheduled('monitorchat_plugins_update_check'));}
  if(wp_next_scheduled('monitorchat_themes_update_check')){
     array_push($vals,wp_next_scheduled('monitorchat_themes_update_check'));}
  if(wp_next_scheduled('monitorchat_file_system_free_space')){
     array_push($vals,wp_next_scheduled('monitorchat_file_system_free_space'));}
  if(wp_next_scheduled('monitorchat_memory')){
     array_push($vals,wp_next_scheduled('monitorchat_memory'));}
  if(wp_next_scheduled('monitorchat_woocommerce_orders_summary')){
     array_push($vals,wp_next_scheduled('monitorchat_woocommerce_orders_summary'));}
  if(wp_next_scheduled('monitorchat_wp_statistics_visitors')){
     array_push($vals,wp_next_scheduled('monitorchat_wp_statistics_visitors'));}
  if(wp_next_scheduled('monitorchat_wp_statistics_referrals')){
     array_push($vals,wp_next_scheduled('monitorchat_wp_statistics_referrals'));}
  if(wp_next_scheduled('monitorchat_wp_statistics_pages')){
     array_push($vals,wp_next_scheduled('monitorchat_wp_statistics_pages'));}

  $start = time() + $start_in;

  if(empty($vals)){return $start;}

  sort($vals);

  foreach($vals as $val){
      $r=range($val-$not_closer_than,$val+$not_closer_than);
      if (in_array($start,$r)){$start=$val+$not_closer_than;}
  }
  return $start;
}

function monitorchat_next_scheduled_hook($hk){
  $next_hook=wp_next_scheduled($hk)-time();
  return monitorchat_seconds_to_time($next_hook);
}

function monitorchat_seconds_to_time($inputSeconds){
    $secondsInAMinute = 60;
    $secondsInAnHour = 60 * $secondsInAMinute;
    $secondsInADay = 24 * $secondsInAnHour;

    $days = floor($inputSeconds / $secondsInADay);

    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    $timeParts = [];
    $sections = [
        'day' => (int)$days,
        'hour' => (int)$hours,
        'minute' => (int)$minutes,
        'second' => (int)$seconds,
    ];

    foreach ($sections as $name => $value){
        if ($value > 0){
            $timeParts[] = $value. ' '.$name.($value == 1 ? '' : 's');
        }
    }
    if(empty($timeParts)){return '0 seconds (now)';}
    return implode(', ', $timeParts);
}

function monitorchat_getpluginbasedir(){
  if(function_exists('wp_normalize_path')){
    if(defined('WP_PLUGIN_DIR')) {return wp_normalize_path(WP_PLUGIN_DIR . '/');}
    return wp_normalize_path(WP_CONTENT_DIR . '/plugins/');
  }else{
    if(defined('WP_PLUGIN_DIR')) {return WP_PLUGIN_DIR . '/';}
    return WP_CONTENT_DIR . '/plugins/';}
}

function monitorchat_can_pii_be_disabled($input){
  $istrial=monitorchat_validate_apikey($_POST["monitorchat_global_api_key"]);
  if($istrial!='PERM'){ return 'on'; }
  return $input;
}

function monitorchat_is_send_using_ok($input){
  if((monitorchat_shell_exec_enabled())&&(monitorchat_curl_exists())){return $input;}

  if($_POST["monitorchat_global_send_using"]=='wppost'){return $input;}

  if($_POST["monitorchat_global_send_using"]=='script'){
    $displaytxt= __( 'Settings saved. To use the monitor.chat.sh script, <a href="https://monitor.chat/documentation/php/" target="_blank>">shell_exec</a> must be enabled and <a href="https://monitor.chat/documentation/overview/#how-does-monitorchat-work" target="_blank">curl</a> must be installed.', 'monitorchat' );}

  if($_POST["monitorchat_global_send_using"]=='embed'){
    $displaytxt= __( 'Settings saved. To use the embedded script, <a href="https://monitor.chat/documentation/php/" target="_blank>">shell_exec</a> must be enabled and <a href="https://monitor.chat/documentation/overview/#how-does-monitorchat-work" target="_blank">curl</a> must be installed.', 'monitorchat' );}
  
  add_settings_error('monitorchat_settings','warn',$displaytxt,'warning');

  return $input;
}

function monitorchat_is_apikey_valid($input){
  $last_valid_apikey=get_option('monitorchat_global_last_valid_apikey');
  $last_invalid_apikey=get_option('monitorchat_global_last_invalid_apikey');
  $displaytxt= __( 'Settings saved. API Key is invalid.', 'monitorchat' );

  if($input==$last_valid_apikey){return $input;}

  if($input==$last_invalid_apikey){
    if($_POST["monitorchat_global_send_using"]!='script'){
      add_settings_error('monitorchat_settings','warn',$displaytxt,'warning');}
    return $input;}

  if(monitorchat_validate_apikey($input)=='INVALID'){
   update_option('monitorchat_global_last_invalid_apikey',$input);
   if($_POST["monitorchat_global_send_using"]!='script'){
     add_settings_error('monitorchat_settings','warn',$displaytxt,'warning');}
   return $input;
  }
  
  $server_check=monitorchat_remote_post_verify_apikey($input);

  if($server_check=='VALID'){
    update_option('monitorchat_global_last_valid_apikey',$input);
    return $input;
  }

  if($server_check=='INVALID'){
   update_option('monitorchat_global_last_invalid_apikey',$input);
   if($_POST["monitorchat_global_send_using"]!='script'){
      add_settings_error('monitorchat_settings','warn',$displaytxt,'warning');}
   return $input;
  }

  // if you have gotten this far, something went wrong! What?
  add_settings_error('monitorchat_settings','warn',$server_check,'error');
  return $input;  
}

function monitorchat_validate_shell_script($input){
$allowable_ending='monitor.chat.sh';
$length = strlen($allowable_ending);
$default='/usr/local/bin/monitor.chat.sh';
$displaybasename= __( 'Settings saved. Basename of shell script must be monitor.chat.sh', 'monitorchat' );
$displaynofile= __( 'Settings saved. File for script does not exist.', 'monitorchat' );
$displaynotexecutable= __( 'Settings saved. File for the monitor.chat.sh script is not executable and accessible to the web server application.', 'monitorchat' );

//only allow basename of "monitor.chat.sh"
if(substr($input, -$length) != $allowable_ending){
  if($_POST["monitorchat_global_send_using"]=='script'){
    add_settings_error('monitorchat_settings','warn',$displaybasename,'warning');}
  return $default;}

// if there is no file on the file system
if(!file_exists($input)){
  if($_POST["monitorchat_global_send_using"]=='script'){
    add_settings_error('moitorchat_settings','warn',$displaynofile,'warning');}
  return $default;}
if(!is_executable($input)){
  if($_POST["monitorchat_global_send_using"]=='script'){
    add_settings_error('monitorchat_settings','warn',$displaynotexecutable,'warning');}
  return $default;}
return $input;

}

function monitorchat_mask_string($str){
    $char_shown_front = 1; $char_shown_back = 1;	
    $len = strlen($str);
    if(($len > 6)&&($len < 11)){$char_shown_front = 2; $char_shown_back = 2;}
    if($len > 10){$char_shown_front = 3; $char_shown_back = 3;}
    if($len < ($char_shown_front + $char_shown_back + 1)){return str_repeat('*',$len);}
	return substr($str, 0, $char_shown_front)
        . str_repeat('*', $len - $char_shown_front - $char_shown_back)
        . substr($str, $len - $char_shown_back, $char_shown_back);
}

function monitorchat_mask_email_address($email) {
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){return 'NOT_A_VALID_EMAIL_ADDRESS';}
    $mail_parts = explode("@", $email);
    $domain_parts = explode('.', $mail_parts[1]);
    $domain_parts[0] = monitorchat_mask_string($domain_parts[0]);
    $mail_parts[0]=monitorchat_mask_string($mail_parts[0]);
    $mail_parts[1] = implode('.', $domain_parts);
    return implode("@", $mail_parts);	
}

function monitorchat_format_visits($visits){
  if ($visits > 1) {return ': '.$visits.' visits';}
  return ': '.$visits.' visit';
}

function monitorchat_is_enabled($trigger){
  if(get_option('monitorchat_global_enabled') != 'on'){return false;}
  if(get_option("$trigger") != 'on'){return false;}
  
  if(get_option('monitorchat_global_send_using') == 'script'){
    if(strtoupper(substr(PHP_OS,0,3))=='WIN'){return false;}
    $script=get_option('monitorchat_global_shell_script');
    if(!monitorchat_shell_exec_enabled()){return false;}
    if(!file_exists($script)){return false;}
    if(!is_executable($script)){return false;}
    if(!monitorchat_curl_exists()){return false;}
  }

  if(get_option('monitorchat_global_send_using') == 'embed'){
    if(strtoupper(substr(PHP_OS,0,3))=='WIN'){return false;}
    if(get_option('monitorchat_global_api_key') != get_option('monitorchat_global_last_valid_apikey')){return false;}
    if(!monitorchat_shell_exec_enabled()){return false;}
    if(!monitorchat_curl_exists()){return false;} 
  }

  if(get_option('monitorchat_global_send_using') == 'wppost'){
    if(get_option('monitorchat_global_api_key') != get_option('monitorchat_global_last_valid_apikey')){return false;}
  }

  return true;
}

function monitorchat_getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function monitorchat_sanitize($str){

  // ensure valid UTF-8 string
  if ( function_exists( 'mb_convert_encoding' ) ) {
    $encoding = mb_detect_encoding( $str, mb_detect_order(), true );
    if ( $encoding ) {
      $str = mb_convert_encoding( $str, 'UTF-8', $encoding );
    } else {
      $str = mb_convert_encoding( $str, 'UTF-8', 'UTF-8' );
    }
  } else {
    $str = wp_check_invalid_utf8( $str, true );
   }

  // convert single quotes, double-quotes, dollar-signs, back-ticks, back-slashes, etc.
  $replacethis=array( "'",'"','$','`',
                      '\\','/','&','*',
                      '#','@','|','!',
                      '%','´','‘','’',
                      '“','”','€','¥',
                      '£','₿');
  $withthis=   array( '<SQ>','<DQ>','<DL>','<GA>',
                      '<BSL>','<FSL>','<AMP>','<AST>',
                      '<OCTO>','<AT>','<PIPE>','<BANG>',
                      '<PCT>','<AA>','<LSQ>','<RSQ>',
                      '<LDQ>','<RDQ>','<EU>','<YN>',
                      '<PN>','<BTC>');
  $txt=str_replace( $replacethis,$withthis,$str);
  $txt=trim($txt); // remove leading and trailing whitespace
  $txt=substr($txt,0,5000); // up to 5,000 characters - more than enough
  return $txt;
}

function monitorchat_validate_apikey($apikey){
  if(!preg_match('/^[a-z][a-z][a-z]-[a-z0-9]{39,39}$/u',$apikey)){return 'INVALID';}
  if(substr($apikey,0,3)=='try'){return 'TRIAL';}
  if(substr($apikey,0,3)=='mon'){return 'PERM';}
  return 'INVALID';
}

function monitorchat_custom_admin_styles() {
$current_section = '';
 if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
       $current_section = sanitize_text_field($_POST['tab']);
 } else {
    if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
      $current_section = sanitize_text_field($_GET['tab']);
    }
 }
  if(($current_section=='enable')||
    ($current_section=='woocommerce_order_status_changed')||
    ($current_section=='woocommerce_orders_summary')){
    echo '<style>th { padding: 4px 10px 4px 10px !important; width: 150px !important; } td {padding: 4px 10px 4px 10px !important;} p.submit{margin-top: 5px !important;} </style>';
  }

if($current_section=='apikey'){echo '<style>th { padding: 6px 10px 6px 10px !important; } td {padding: 6px 10px 6px 10px !important;}</style>';}

}

function monitorchat_curl_exists(){
  $existscurl=shell_exec('which curl');
  if (empty($existscurl)){return false;}
  return true;
}

function monitorchat_shell_exec_enabled() {
  $disabled = explode(',', ini_get('disable_functions'));
  return !in_array('shell_exec', $disabled);
}

function monitorchat_curl_exec_enabled() {
  $disabled = explode(',', ini_get('disable_functions'));
  return !in_array('curl_exec', $disabled);
}

// ########## FUNCTION TO ADD WP CRON SCHEDULES ##########

function monitorchat_filter_cron_schedules( array $scheds ) {

  $heartbeat_interval=get_option('monitorchat_heartbeat_interval');
  if (empty($heartbeat_interval)){$heartbeat_interval=30;} // default 30 minutes
  $wp_core_update_check_interval=get_option('monitorchat_wp_core_update_check_interval');
  if (empty($wp_core_update_check_interval)){$wp_core_update_check_interval=12;} // default 12 hours
  $plugins_update_check_interval=get_option('monitorchat_plugins_update_check_interval');
  if (empty($plugins_update_check_interval)){$plugins_update_check_interval=12;} // default 12 hours
  $themes_update_check_interval=get_option('monitorchat_themes_update_check_interval');
  if (empty($themes_update_check_interval)){$themes_update_check_interval=12;} // default 12 hours
  $file_system_free_space_interval=get_option('monitorchat_file_system_free_space_interval');
  if (empty($file_system_free_space_interval)){$file_system_free_space_interval=24;} // default 24 hours
  $memory_interval=get_option('monitorchat_memory_interval');
  if (empty($memory_interval)){$memory_interval=24;} // default 24 hours
  $wp_statistics_visitors_interval=get_option('monitorchat_wp_statistics_visitors_interval');
  if (empty($wp_statistics_visitors_interval)){$wp_statistics_visitors_interval=60;} // default 60 minutes
  $wp_statistics_referrals_interval=get_option('monitorchat_wp_statistics_referrals_interval');
  if (empty($wp_statistics_referrals_interval)){$wp_statistics_referrals_interval=24;} // default 24 hours
  $wp_statistics_pages_interval=get_option('monitorchat_wp_statistics_pages_interval');
  if (empty($wp_statistics_pages_interval)){$wp_statistics_pages_interval=24;} // default 24 hours
  $woocommerce_orders_summary_interval=get_option('monitorchat_woocommerce_orders_summary_interval');
  if (empty($woocommerce_orders_summary_interval)){$woocommerce_orders_summary_interval=4;} // default 4 hours

    $schedules['monitorchat_heartbeat_interval'] = array(
            'interval'  => ($heartbeat_interval * 60), // convert minutes to seconds
            'display'   => 'Every '.$heartbeat_interval.($heartbeat_interval == 1 ? ' minute.':' minutes.').' (Monitor.chat)'
    );
    $schedules['monitorchat_wp_core_update_check_interval'] = array(
            'interval'  => ($wp_core_update_check_interval * 60 * 60), // convert hours to seconds
            'display'   => 'Every '.$wp_core_update_check_interval.($wp_core_update_check_interval ==1 ? ' hour.':' hours.').' (Monitor.chat)'
    );
    $schedules['monitorchat_plugins_update_check_interval'] = array(
            'interval'  => ($plugins_update_check_interval * 60 * 60), // convert hours to seconds
            'display'   => 'Every '.$plugins_update_check_interval.($plugins_update_check_interval==1 ? ' hour.':' hours.').' (Monitor.chat)'
    );
    $schedules['monitorchat_themes_update_check_interval'] = array(
            'interval'  => ($themes_update_check_interval * 60 * 60), // convert hours to seconds
            'display'   => 'Every '.$themes_update_check_interval.($themes_update_check_interval==1 ? ' hour.':' hours.').' (Monitor.chat)'
    );

  if((strtoupper(substr(PHP_OS,0,3))!='WIN')&&(monitorchat_shell_exec_enabled())){
     $schedules['monitorchat_file_system_free_space_interval'] = array(
            'interval'  => ($file_system_free_space_interval * 60 * 60 ), // convert hours to seconds
            'display'   => 'Every '.$file_system_free_space_interval.($file_system_free_space_interval==1 ? ' hour.':' hours.').' (Monitor.chat)'
     );
     $schedules['monitorchat_memory_interval'] = array(
            'interval'  => ($memory_interval * 60 * 60 ), // convert hours to seconds
            'display'   => 'Every '.$memory_interval.($memory_interval==1 ? ' hour.':' hours.').' (Monitor.chat)'
     );
  }

  if(is_plugin_active('wp-statistics/wp-statistics.php')){
    $schedules['monitorchat_wp_statistics_visitors_interval'] = array(
            'interval'  => ($wp_statistics_visitors_interval * 60  ), // convert minutes to seconds
            'display'   => 'Every '.$wp_statistics_visitors_interval.($wp_statistics_visitors_interval==1 ?' minute.':' minutes.').' (Monitor.chat)'
    );

    $schedules['monitorchat_wp_statistics_referrals_interval'] = array(
            'interval'  => ($wp_statistics_referrals_interval * 60 * 60  ), // convert hours to seconds
            'display'   => 'Every '.$wp_statistics_referrals_interval.($wp_statistics_referrals_interval==1 ?' hour.':' hours.').' (Monitor.chat)'
    );

    $schedules['monitorchat_wp_statistics_pages_interval'] = array(
            'interval'  => ($wp_statistics_pages_interval * 60 * 60 ), // convert hours to seconds
            'display'   => 'Every '.$wp_statistics_pages_interval.($wp_statistics_pages_interval==1 ? ' hour.':' hours.').' (Monitor.chat)'
    );
  }

  if (is_plugin_active('woocommerce/woocommerce.php')) {
    $schedules['monitorchat_woocommerce_orders_summary_interval'] = array(
            'interval'  => ($woocommerce_orders_summary_interval * 60 * 60 ), // convert hours to seconds
            'display'   => 'Every '.$woocommerce_orders_summary_interval.($woocommerce_orders_summary_interval==1 ? ' hour.':' hours.').' (Monitor.chat)'
    );
  }

  return array_merge( $schedules, $scheds );
}

// ########## SEND MESSAGE FUNCTIONS ##########
// ########## EMBEDED SHELL SCRIPT ##########

function monitorchat_embed_message($recipient,$message){
  $apikey=get_option('monitorchat_global_api_key');
  $hostname=monitorchat_gethostname();
  $validate=monitorchat_validate_apikey($apikey);
  if($validate=='TRIAL'){$url='https://ap.monitor.chat/';}
  if($validate=='PERM'){$url='https://monitor.e2e.ee/';}
  if($recipient=='yourchatroom'){$recipient=null;}

$replacethis=array('<APIKEY>','<URL>','<HOSTNAME>','<RECIPIENT>','<MESSAGE>');
$withthis=array($apikey,$url,$hostname,$recipient,$message);

$emb=str_replace($replacethis,$withthis,'#!/bin/bash
PATH=/bin:/usr/bin:$PATH;
APIKEY="<APIKEY>";
URL="<URL>";
HOSTNAME="<HOSTNAME>";
MSG="<MESSAGE>";
MSG=`printf "$MSG" | sed -E \':a;N;$!ba;s/\r{0,1}\n/\\\\\\\\n/g\'`;
JSON_FORMAT=\'{"apikey":"%s","msg":"%s","hostname":"%s","recipient":"%s","display_hostname":"yes"}\';
JSON=`printf "$JSON_FORMAT" "$APIKEY" "$MSG" "$HOSTNAME" "<RECIPIENT>"`;
curl --header "Content-Type: application/json" --request POST --data "$JSON" "$URL";');

shell_exec("$emb 1>/dev/null 2>&1 &");

}

// ########## SHELL SCRIPT ##########

function monitorchat_shell_message($recipient,$message){
  $script=get_option('monitorchat_global_shell_script');
  if($recipient=='yourchatroom'){
    shell_exec($script.' "'.$message.'" 1>/dev/null 2>&1 &');
  }else{
    shell_exec($script.' "'.$message.'" "'.$recipient.'" 1>/dev/null 2>&1 &');
  }
}

// ########## REMOTE POST ##########

function monitorchat_remote_post_message($recipient,$message){
  $apikey=get_option('monitorchat_global_api_key');
  $hostname=monitorchat_gethostname();
  $validate=monitorchat_validate_apikey($apikey);
  if($validate=='TRIAL'){$url='https://ap.monitor.chat/';}
  if($validate=='PERM'){$url='https://monitor.e2e.ee/';}
  if($validate=='INVALID'){return;}
  if($recipient=='yourchatroom'){$recipient=null;}
  $data = array("apikey" => "$apikey",
                "msg" => "$message",
                "hostname" => "$hostname",
                "recipient" => "$recipient",
                "display_hostname"=>"yes");
  $data_string = json_encode($data);
  // blocking => false. Do not wait for a responce from the remote server
  $result = wp_remote_post($url, array(
    'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
    'body'        => $data_string,
    'method'      => 'POST',
    'data_format' => 'body',
    'timeout'     => 2,
    'blocking'    => false,)
    );
}

// ########## VERIFY API KEY BY COMMUNICATION WITH SITE  ##########

function monitorchat_remote_post_verify_apikey($apikey){
  $hostname=monitorchat_gethostname();
  $validate=monitorchat_validate_apikey($apikey);
  if($validate=='TRIAL'){$url='https://ap.monitor.chat/';}
  if($validate=='PERM'){$url='https://monitor.e2e.ee/';}
  if($validate=='INVALID'){return false;}
  $recipient=null;
  $data = array("apikey" => "$apikey",
                "msg" => "REPORT",
                "hostname" => "$hostname",
                "recipient" => "$recipient",
                "display_hostname"=>"yes");
  $data_string = json_encode($data);
  $result = wp_remote_post($url, array(
    'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
    'body'        => $data_string,
    'method'      => 'POST',
    'data_format' => 'body',
	'timeout'     => 4,
    'blocking' => true,)
    );

if(is_wp_error($result)){return $result->get_error_message(); }
if (strpos(trim($result["body"]),'INVALID API KEY') > -1 ){return 'INVALID';}
return 'VALID';

}

// ########## POPULATE THE API KEY REPORT ##########

function monitorchat_populate_apikey_report(){
  if (!current_user_can('activate_plugins')){ return null; } // just leave if not an admin
  $apikey=get_option('monitorchat_global_api_key');
  $last_valid_apikey=get_option('monitorchat_global_last_valid_apikey');
  if($apikey != $last_valid_apikey){return null;} // just leave if not valid api key
  $last_check=$_SESSION["monitorchat_last_report"];
  if((!is_numeric($last_check))||(empty($last_check))){$last_check=0;}
  $seconds_since_last=(time()-$last_check);
  if($seconds_since_last < 3600){return null;} // new report every hour, no more

  $hostname=monitorchat_gethostname();
  $validate=monitorchat_validate_apikey($apikey);
  if($validate=='TRIAL'){$url='https://ap.monitor.chat/';}
  if($validate=='PERM'){$url='https://monitor.e2e.ee/';}
  if($validate=='INVALID'){return null;}
  $recipient=null;
  $data = array("apikey" => "$apikey",
                "msg" => "REPORT",
                "hostname" => "$hostname",
                "recipient" => "$recipient",
                "display_hostname"=>"yes");
  $data_string = json_encode($data);
  $result = wp_remote_post($url, array(
    'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
    'body'        => $data_string,
    'method'      => 'POST',
    'data_format' => 'body',
    'timeout'     => 4,
    'blocking' => true,)
    );

  if(is_wp_error($result)){
    $report = $result->get_error_message(); 
  }else{
    $report = $result["body"];
  }
    $_SESSION["monitorchat_apikey_report"]=$report;
    $_SESSION["monitorchat_last_report"]=time();
    return null;
}

// ########## INSTANT MESSAGE ON GENERIC ACTION - FOR TESTING HOOKS ##########

function monitorchat_send_xmpp_on_generic_action(){

  $recipient='test';
  $message='<FALLENLEAF>Generic test message from Wordpress.';

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## INITIALIZE ##########
  add_action('admin_head', 'monitorchat_custom_admin_styles');
  add_action('init','monitorchat_register_session'); // register a session for admins only
  add_filter( 'cron_schedules', 'monitorchat_filter_cron_schedules' );
