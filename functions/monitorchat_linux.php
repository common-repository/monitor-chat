<?php
/**
 * Linux Functions for Monitor.chat plugin
 */


// No direct calls to this script
if ( strpos($_SERVER['PHP_SELF'], basename(__FILE__) )) {
        die('No direct calls allowed!');
}


// ########## INSTANT MESSAGE ON FILE SYSTEM FREE SPACE ##########

function monitorchat_send_xmpp_on_file_system_free_space() {

  if(!monitorchat_is_enabled('monitorchat_file_system_free_space_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_file_system_free_space_recipient');
  $message=monitorchat_format_message_for_file_system_free_space();
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR FILE SYSTEM FREE SPACE ##########
function monitorchat_format_message_for_file_system_free_space(){
  $replacethis=array('<THRESHOLD>');
  $withthis=array(get_option('monitorchat_file_system_free_space_pct'));
  $emb=str_replace($replacethis,$withthis,'#!/bin/bash
PATH=/bin:/usr/bin:$PATH;
JJ=`df -h | sed -e /^Filesystem/d | awk \'0+$5 >= <THRESHOLD> {print "<POINTRIGHT> " $6 " has " $4 " of free space out of " $2 " total."}\'`
if [ -z "$JJ" ]; then
  printf "There is plenty of free disk space.";
else
  KK=`printf  "Free Disk Space:\n$JJ"`;
  printf "$KK";
fi');

  $result=shell_exec("$emb 2>/dev/null");

  $message=get_option('monitorchat_file_system_free_space_message');
  $replacethis=array('<FILESYSTEMREPORT>');
  $withthis=array($result);
  $message=str_replace($replacethis,$withthis,$message);

  return monitorchat_sanitize($message);
}

// ########## INSTANT MESSAGE ON FREE MEMORY ##########

function monitorchat_send_xmpp_on_memory() {

  if(!monitorchat_is_enabled('monitorchat_memory_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_memory_recipient');
  $message=monitorchat_format_message_for_memory();
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR FREE MEMORY ##########
function monitorchat_format_message_for_memory(){
  $emb='#!/bin/bash
PATH=/bin:/usr/bin:$PATH;
MEM1=`free -m |awk "NR==2"|awk \'{print $2}\'`
MEM2=`free -m |awk "NR==2"|awk \'{print $3}\'`
MEM3=`free -m |awk "NR==2"|awk \'{print $4}\'`
MEM4=`free -m |awk "NR==2"|awk \'{print $5}\'`
MEM5=`free -m |awk "NR==2"|awk \'{print $6}\'`
MEM6=`free -m |awk "NR==2"|awk \'{print $7}\'`
MEM7=`free -m |awk "NR==3"|awk \'{print $2}\'`
MEM8=`free -m |awk "NR==3"|awk \'{print $3}\'`
MEM9=`free -m |awk "NR==3"|awk \'{print $4}\'`

KK=`printf "Wordpress Server Memory Report
Total Memory: $MEM1 MB
Used Memory: $MEM2 MB
Free Memory: $MEM3 MB
Shared Memory: $MEM4 MB
Buffer Cache: $MEM5 MB
Available Memory: $MEM6 MB
Total Swap: $MEM7 MB
Used Swap: $MEM8 MB
Free Swap: $MEM9 MB"`

if [ -z "$KK" ]; then
  printf "free command failed?"
else
  printf "$KK"
fi';

  $result=shell_exec("$emb 2>&1");

  $message=get_option('monitorchat_memory_message');
  $replacethis=array('<MEMORYREPORT>');
  $withthis=array($result);
  $message=str_replace($replacethis,$withthis,$message);

  return monitorchat_sanitize($message);
}


// ########## INITIALIZE ##########
  add_action( 'monitorchat_file_system_free_space','monitorchat_send_xmpp_on_file_system_free_space');
  add_action( 'monitorchat_memory','monitorchat_send_xmpp_on_memory');
