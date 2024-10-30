<?php
/**
 * Woocommerce Functions for Monitor.chat plugin
 */


// No direct calls to this script
if ( strpos($_SERVER['PHP_SELF'], basename(__FILE__) )) {
        die('No direct calls allowed!');
}

// ########## GENERAL FUNCTIONS FOR WOOCOMMERCE ##########

function monitorchat_convert_wc_currency_symbol_to_shortcode($msg){
  if(file_exists(WP_PLUGIN_DIR .'/woocommerce/includes/wc-core-functions.php')){
     require_once (WP_PLUGIN_DIR .'/woocommerce/includes/wc-core-functions.php');}
  $currency = get_woocommerce_currency_symbol();
  if($currency == '&#36;'){$msg=str_replace('<CURRENCY>','<DL>',$msg);}
  if($currency == '&pound;'){$msg=str_replace('<CURRENCY>','<PN>',$msg);}
  if($currency == '&euro;'){$msg=str_replace('<CURRENCY>','<EU>',$msg);}
  if($currency == '&yen;'){$msg=str_replace('<CURRENCY>','<YN>',$msg);}
  if($currency == '&#3647;'){$msg=str_replace('<CURRENCY>','<BTC>',$msg);}
  $msg=str_replace('<CURRENCY>','',$msg);
  return $msg;
}

function monitorchat_woocommerce_currency_symbol(){
  global $woocommerce;
  $currency = get_woocommerce_currency_symbol();
  if($currency == '&#36;'){return '<DL>';}
  if($currency == '&pound;'){return '<PN>';}
  if($currency == '&euro;'){return '<EU>';}
  if($currency == '&yen;'){return '<YN>';}
  if($currency == '&#3647;'){return '<BTC>';}
  return '';
}



// ########## FORMAT MESSAGE FOR NEW WOOCOMMERCE PRODUCT ##########

function monitorchat_format_message_for_woocommerce_new_product($msg,$post){
  $product = wc_get_product( $post->ID );
  $replacethis=array("<PRODUCTID>",
                     "<PRODUCTNAME>",
                     "<PRODUCTSTATUS>",
                     "<PRODUCTDESC>",
  );

  $withthis=array($product->get_id(),
                  $product->get_name(),
                  $product->get_status(),
                  $product->get_description(),
  );
	
  
  $tt=str_replace($replacethis,$withthis,$msg);
  return monitorchat_sanitize($tt);

}

// ########## INSTANT MESSAGE ON WOOCOMMERCE ORDER STATUS CHANGE ##########
// Includes order creation!

function monitorchat_send_xmpp_on_woocommerce_order_status_change($order_id){

  $order = wc_get_order( $order_id );
  $order_status = $order->get_status();

  // can be disabled for any type of change:
  if(($order_status=='pending')&&(!get_option('monitorchat_woocommerce_order_status_change_pending')=='on')){return;}
  if(($order_status=='processing')&&(!get_option('monitorchat_woocommerce_order_status_change_processing')=='on')){return;}
  if(($order_status=='on-hold')&&(!get_option('monitorchat_woocommerce_order_status_change_on-hold')=='on')){return;}
  if(($order_status=='completed')&&(!get_option('monitorchat_woocommerce_order_status_change_completed')=='on')){return;}
  if(($order_status=='cancelled')&&(!get_option('monitorchat_woocommerce_order_status_change_cancelled')=='on')){return;}
  if(($order_status=='refunded')&&(!get_option('monitorchat_woocommerce_order_status_change_refunded')=='on')){return;}
  if(($order_status=='failed')&&(!get_option('monitorchat_woocommerce_order_status_change_failed')=='on')){return;}

  if(!monitorchat_is_enabled('monitorchat_woocommerce_order_status_change_enabled')){return;} // enabled or not?
  $message=monitorchat_format_message_for_woocommerce_order($order_id);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  $recipient=get_option('monitorchat_woocommerce_order_status_change_recipient');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR WOOCOMMERCE ORDER STATUS CHANGE ##########

function monitorchat_format_message_for_woocommerce_order($order_id){
  $message=get_option('monitorchat_woocommerce_order_status_change_message');

  $order = wc_get_order( $order_id );
  $order_data = $order->get_data();
  $discount_total = monitorchat_woocommerce_currency_symbol().$order_data['discount_total'];
  $total = monitorchat_woocommerce_currency_symbol().$order_data['total'];
  $currency = $order_data['currency'];
  if(!empty($order_data['shipping']['city'])){
     $destination = $order_data['shipping']['city'].', '.$order_data['shipping']['state'].', '.$order_data['shipping']['country'];
  }else{
     $destination = $order_data['billing']['city'].', '.$order_data['billing']['state'].', '.$order_data['billing']['country'];
  }
  $status = $order_data['status'];

  $product_list=null;
  foreach ($order->get_items() as $item_key => $item ):
    $item_id = $item->get_id();
    $item_name    = $item->get_name();
    $quantity     = $item->get_quantity();
    $product_list=$product_list.$item_name.' [QTY: '.$quantity.']'."\n";
  endforeach;
  $product_list=substr($product_list, 0, -1);
  $product_list=$product_list;

  $replacethis=array('<ORDERID>','<TOTAL>','<CURRENCY>','<DESTINATION>','<PRODUCTLIST>','<STATUS>');
  $withthis=array( $order_id,$total,$currency,$destination,$product_list,$status);
  $tt=str_replace($replacethis,$withthis,$message);

  return monitorchat_sanitize($tt);
}

// ########## INSTANT MESSAGE ON WOOCOMMERCE COUPON REDEEMED ##########

function monitorchat_send_xmpp_on_woocommerce_applied_coupon($coupon_code){
  if(!monitorchat_is_enabled('monitorchat_woocommerce_applied_coupon_enabled')){return;} // enabled or not?
  $message=monitorchat_format_message_for_woocommerce_applied_coupon($coupon_code);
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  $recipient=get_option('monitorchat_woocommerce_applied_coupon_recipient');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR WOOCOMMERCE COUPON APPLIED ##########

function monitorchat_format_message_for_woocommerce_applied_coupon($coupon_code){

  $message=get_option('monitorchat_woocommerce_applied_coupon_message');
  $replacethis=array('<COUPONCODE>');
  $withthis=array( $coupon_code );
  $tt=str_replace($replacethis,$withthis,$message);

  return monitorchat_sanitize($tt);
}

// ########## INSTANT MESSAGE ON WOOCOMMERCE SUMMARY REPORT ##########

function monitorchat_send_xmpp_on_woocommerce_orders_summary() {

  if(!monitorchat_is_enabled('monitorchat_woocommerce_orders_summary_enabled')){return;} // enabled or not?

  $recipient=get_option('monitorchat_woocommerce_orders_summary_recipient');
  $message=monitorchat_format_message_for_woocommerce_orders_summary();
//$message='test message for woocommerce orders summary';
  if(empty($message)){return;} // if message is empty then leave

  $send_using=get_option('monitorchat_global_send_using');
  if($send_using=='wppost'){monitorchat_remote_post_message($recipient,$message);}
  if($send_using=='script'){monitorchat_shell_message($recipient,$message);}
  if($send_using=='embed'){monitorchat_embed_message($recipient,$message);}
  return;
}

// ########## FORMAT MESSAGE FOR WOOCOMMERCE SUMMARY REPORT ##########

function monitorchat_format_message_for_woocommerce_orders_summary(){
  if(file_exists(WP_PLUGIN_DIR.'/woocommerce/includes/admin/reports/class-wc-admin-report.php')){
    require_once (WP_PLUGIN_DIR.'/woocommerce/includes/admin/reports/class-wc-admin-report.php');}
  $message=get_option('monitorchat_woocommerce_orders_summary_message');
  $interval_hours = get_option('monitorchat_woocommerce_orders_summary_interval');
  $interval_minutes = ($interval_hours * 60);
  $interval_seconds = ($interval_minutes * 60);
  $now=time();
  $start_time=($now-$interval_seconds);
  $order_by=get_option('monitorchat_woocommerce_orders_summary_order_by');
  $limit=get_option('monitorchat_woocommerce_orders_summary_limit');
  $order_status=array();
  if(get_option('monitorchat_woocommerce_orders_summary_completed')=='on'){array_push($order_status,'completed');}
  if(get_option('monitorchat_woocommerce_orders_summary_on-hold')=='on'){array_push($order_status,'on-hold');}
  if(get_option('monitorchat_woocommerce_orders_summary_pending')=='on'){array_push($order_status,'pending');}
  if(get_option('monitorchat_woocommerce_orders_summary_processing')=='on'){array_push($order_status,'processing');}
  if(empty($order_status)){return $message;} // no value? return message as it is

    global $woocommerce, $wpdb, $product;
    $wpdb->query('SET SQL_BIG_SELECTS=1');
    $wc_report = new WC_Admin_Report();
    $wc_report->start_date = $start_time;
    $wc_report->end_date = $now;
    $sold_products = $wc_report->get_order_report_data(array(
        'data' => array(
            '_product_id' => array(
                'type' => 'order_item_meta',
                'order_item_type' => 'line_item',
                'function' => '',
                'name' => 'product_id'
            ),
            '_qty' => array(
                'type' => 'order_item_meta',
                'order_item_type' => 'line_item',
                'function' => 'SUM',
                'name' => 'quantity'
            ),
            '_line_subtotal' => array(
                'type' => 'order_item_meta',
                'order_item_type' => 'line_item',
                'function' => 'SUM',
                'name' => 'gross'
            ),
            '_line_total' => array(
                'type' => 'order_item_meta',
                'order_item_type' => 'line_item',
                'function' => 'SUM',
                'name' => 'gross_after_discount'
            )
        ),
        'query_type' => 'get_results',
        'group_by' => 'product_id',
        'where_meta' => '',
        'order_by' => $order_by.' DESC',
        'order_types' => wc_get_order_types('order_count'),
        'filter_range' => TRUE,
        'limit' => $limit,
        'order_status' => $order_status,
    ));
if(count($sold_products) < 1){$msg='No New Order Activity in the past '.
  ($interval_hours == 1 ? 'hour.': $interval_hours.' hours.');}else{
$msg='Summary of products in orders from the past '.
        ($interval_hours == 1 ? 'hour.': $interval_hours.' hours.')."\n";
foreach ($sold_products as $product) {
    $msg.=' <POINTRIGHT> '.html_entity_decode(get_the_title($product->product_id))."\n";
    $msg.='   Quantity: '.intval($product->quantity)."\n";
    $msg.='   Income: <CURRENCY>'.$product->gross_after_discount."\n";
  }
}
  $msg=monitorchat_convert_wc_currency_symbol_to_shortcode($msg);
 
  $replacethis=array('<SUMMARY>');
  $withthis=array($msg);
  $message=str_replace($replacethis,$withthis,$message);
  return monitorchat_sanitize($message);
}

// ########## INITIALIZE ##########
  add_action( 'woocommerce_applied_coupon','monitorchat_send_xmpp_on_woocommerce_applied_coupon',99,1);
  add_action( 'woocommerce_order_status_completed','monitorchat_send_xmpp_on_woocommerce_order_status_change',99,1);
  add_action( 'woocommerce_order_status_pending','monitorchat_send_xmpp_on_woocommerce_order_status_change',99,1);
  add_action( 'woocommerce_order_status_failed','monitorchat_send_xmpp_on_woocommerce_order_status_change',99,1);
  add_action( 'woocommerce_order_status_on-hold','monitorchat_send_xmpp_on_woocommerce_order_status_change',99,1);
  add_action( 'woocommerce_order_status_processing','monitorchat_send_xmpp_on_woocommerce_order_status_change',99,1);
  add_action( 'woocommerce_order_status_refunded','monitorchat_send_xmpp_on_woocommerce_order_status_change',99,1);
  add_action( 'woocommerce_order_status_cancelled','monitorchat_send_xmpp_on_woocommerce_order_status_change',99,1);
  add_action( 'monitorchat_woocommerce_orders_summary','monitorchat_send_xmpp_on_woocommerce_orders_summary'); // sent by cron


