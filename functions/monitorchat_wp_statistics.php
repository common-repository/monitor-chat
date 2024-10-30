<?php
/**
 * WP Statistics Functions for Monitor.chat plugin
 */


// No direct calls to this script
if ( strpos($_SERVER['PHP_SELF'], basename(__FILE__) )) {
        die('No direct calls allowed!');
}

// ########## CLASSES TO GET STATS FROM WP-STATISTICS ##########

  class monitorchat_wp_stats_summary
  {
      public static function monitorchat_get_stats($args = array())
      {
          return \WP_STATISTICS\MetaBox\summary::getSummaryHits(array('user-online', 'visitors', 'visits', 'search-engine'));
      }
  }

  class monitorchat_wp_stats_pages
  {
      public static function get($args = array())
      {
        $response = \WP_STATISTICS\Pages::getTop($args);
        if (count($response) < 1) { $response['no_data'] = 1; }
        return $response;
      }
  }

// ########## INSTANT MESSAGE ON WP STATISTICS VISITORS ##########

function monitorchat_send_xmpp_on_wp_statistics_visitors() {

  if(!monitorchat_is_enabled('monitorchat_wp_statistics_visitors_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_wp_statistics_visitors_recipient');
  $message=monitorchat_format_message_for_wp_statistics_visitors();
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}


// ########## FORMAT MESSAGE FOR WP STATISTICS VISITORS ##########
function monitorchat_format_message_for_wp_statistics_visitors(){
	
$sta= new monitorchat_wp_stats_summary();
$stats=$sta->monitorchat_get_stats();
$onlinevisitors.='Visitors Currently Online: '.$stats["user_online"]["value"];
$visitors.='Visitors:'."\n";
$visitors.='  Today: '.$stats["visitors"]["today"]["value"]."\n";
$visitors.='  Yesterday: '.$stats["visitors"]["yesterday"]["value"]."\n";
$visitors.='  Last 7 days: '.$stats["visitors"]["week"]["value"]."\n";
$visitors.='  Last 30 days: '.$stats["visitors"]["week"]["value"]."\n";
$visitors.='  Last 365 days: '.$stats["visitors"]["year"]["value"]."\n";
$visitors.='  Total: '.$stats["visitors"]["total"]["value"];
$visits.='Visits:'."\n";
$visits.='  Today: '.$stats["visits"]["today"]["value"]."\n";
$visits.='  Yesterday: '.$stats["visits"]["yesterday"]["value"]."\n";
$visits.='  Last 7 days: '.$stats["visits"]["week"]["value"]."\n";
$visits.='  Last 30 days: '.$stats["visits"]["month"]["value"]."\n";
$visits.='  Last 365 days: '.$stats["visits"]["year"]["value"]."\n";
$visits.='  Total: '.$stats["visits"]["total"]["value"];

  $message=get_option('monitorchat_wp_statistics_visitors_message');
  $replacethis=array('<ONLINEVISITORS>','<VISITORSREPORT>','<VISITSREPORT>');
  $withthis=array($onlinevisitors,$visitors,$visits);
  $message=str_replace($replacethis,$withthis,$message);

  return monitorchat_sanitize($message);
}

// ########## INSTANT MESSAGE ON WP STATISTICS REFERRALS ##########

function monitorchat_send_xmpp_on_wp_statistics_referrals() {

  if(!monitorchat_is_enabled('monitorchat_wp_statistics_referrals_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_wp_statistics_referrals_recipient');
  $message=monitorchat_format_message_for_wp_statistics_referrals();
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR WP STATISTICS REFERRALS ##########
function monitorchat_format_message_for_wp_statistics_referrals(){
  $stt = new monitorchat_wp_stats_summary();
  $stats=$stt->monitorchat_get_stats();

  $seref.='Search Engine Referrals:'."\n";
  $seref.=' Bing: '.$stats["search-engine"]["bing"]["today"].' today, '.$stats["search-engine"]["bing"]["yesterday"].' yesterday'."\n";
  $seref.=' DuckDuckGo: '.$stats["search-engine"]["duckduckgo"]["today"].' today, '.$stats["search-engine"]["duckduckgo"]["yesterday"].' yesterday'."\n";
  $seref.=' Google: '.$stats["search-engine"]["google"]["today"].' today, '.$stats["search-engine"]["google"]["yesterday"].' yesterday'."\n";
  $seref.=' Yahoo: '.$stats["search-engine"]["yahoo"]["today"].' today, '.$stats["search-engine"]["yahoo"]["yesterday"].' yesterday'."\n";
  $seref.=' Yandex: '.$stats["search-engine"]["yandex"]["today"].' today, '.$stats["search-engine"]["yandex"]["yesterday"].' yesterday';


  $message=get_option('monitorchat_wp_statistics_referrals_message');
  $replacethis=array('<REFERRALSREPORT>');
  $withthis=array($seref);
  $message=str_replace($replacethis,$withthis,$message);

  return monitorchat_sanitize($message);
}

// ########## INSTANT MESSAGE ON WP STATISTICS TOP PAGES REPORT ##########

function monitorchat_send_xmpp_on_wp_statistics_pages() {

  if(!monitorchat_is_enabled('monitorchat_wp_statistics_pages_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_wp_statistics_pages_recipient');
  $message=monitorchat_format_message_for_wp_statistics_pages();
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR WP STATISTICS TOP PAGES REPORT ##########
function monitorchat_format_message_for_wp_statistics_pages(){
$pg = new monitorchat_wp_stats_pages();
$toppages=$pg->get();
$chlimit=25;
$tpages.='All-Time Top Wordpress Pages Report'."\n";	
if($toppages['no_data'] == 1){$tpages.='No Data Found!';}else{
if(!empty($toppages["0"]["title"])){$tpages.='  '.substr($toppages["0"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["0"]["number"])."\n";}
if(!empty($toppages["1"]["title"])){$tpages.='  '.substr($toppages["1"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["1"]["number"])."\n";}
if(!empty($toppages["2"]["title"])){$tpages.='  '.substr($toppages["2"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["2"]["number"])."\n";}
if(!empty($toppages["3"]["title"])){$tpages.='  '.substr($toppages["3"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["3"]["number"])."\n";}
if(!empty($toppages["4"]["title"])){$tpages.='  '.substr($toppages["4"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["4"]["number"])."\n";}
if(!empty($toppages["5"]["title"])){$tpages.='  '.substr($toppages["5"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["5"]["number"])."\n";}
if(!empty($toppages["6"]["title"])){$tpages.='  '.substr($toppages["6"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["6"]["number"])."\n";}
if(!empty($toppages["7"]["title"])){$tpages.='  '.substr($toppages["7"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["7"]["number"])."\n";}
if(!empty($toppages["8"]["title"])){$tpages.='  '.substr($toppages["8"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["8"]["number"])."\n";}
if(!empty($toppages["9"]["title"])){$tpages.='  '.substr($toppages["9"]["title"],0,$chlimit)
      .monitorchat_format_visits($toppages["9"]["number"]);}
}
  $message=get_option('monitorchat_wp_statistics_pages_message');
  $replacethis=array('<TOPPAGESREPORT>');
  $withthis=array($tpages);
  $message=str_replace($replacethis,$withthis,$message);
  return monitorchat_sanitize($message);
}

// ########## INITIALIZE ##########
  add_action( 'monitorchat_wp_statistics_visitors', 'monitorchat_send_xmpp_on_wp_statistics_visitors' );
  add_action( 'monitorchat_wp_statistics_referrals', 'monitorchat_send_xmpp_on_wp_statistics_referrals' );
  add_action( 'monitorchat_wp_statistics_pages', 'monitorchat_send_xmpp_on_wp_statistics_pages' );

