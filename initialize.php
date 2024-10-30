<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

$schedules=wp_get_schedules();

// ########## ADD OR REMOVE SCHEDULED EVENTS BASED ON ENABLED OR DISABLED ##########
   if ( ! wp_next_scheduled('monitorchat_heartbeat')&&(monitorchat_is_enabled('monitorchat_heartbeat_enabled'))){
        wp_schedule_event( time()+30, 'monitorchat_heartbeat_interval', 'monitorchat_heartbeat' );
    }

   if((wp_next_scheduled('monitorchat_heartbeat'))&&(!monitorchat_is_enabled('monitorchat_heartbeat_enabled'))){
       wp_clear_scheduled_hook('monitorchat_heartbeat');
   }


if (is_plugin_active('woocommerce/woocommerce.php')) {
   if ( ! wp_next_scheduled( 'monitorchat_woocommerce_orders_summary' )&&(monitorchat_is_enabled('monitorchat_woocommerce_orders_summary_enabled'))) {
      wp_schedule_event( monitorchat_distribute_scheduled_hooks($schedules['monitorchat_woocommerce_orders_summary_interval']['interval']), 
                         'monitorchat_woocommerce_orders_summary_interval', 
                         'monitorchat_woocommerce_orders_summary' );
    }
}

  if ((!is_plugin_active('woocommerce/woocommerce.php'))&&(wp_next_scheduled( 'monitorchat_woocommerce_orders_summary' ))) {
       wp_clear_scheduled_hook('monitorchat_woocommerce_orders_summary');
   }

   if((wp_next_scheduled( 'monitorchat_woocommerce_orders_summary' ))&&(!monitorchat_is_enabled('monitorchat_woocommerce_orders_summary_enabled'))){
       wp_clear_scheduled_hook('monitorchat_woocommerce_orders_summary');
   }



   if ( ! wp_next_scheduled( 'monitorchat_wp_core_update_check' )&&(monitorchat_is_enabled('monitorchat_wp_core_update_check_enabled'))) {
       wp_schedule_event( monitorchat_distribute_scheduled_hooks($schedules['monitorchat_wp_core_update_check_interval']['interval']), 
                          'monitorchat_wp_core_update_check_interval', 
                          'monitorchat_wp_core_update_check' );
    }

   if((wp_next_scheduled( 'monitorchat_wp_core_update_check' ))&&(!monitorchat_is_enabled('monitorchat_wp_core_update_check_enabled'))){
       wp_clear_scheduled_hook('monitorchat_wp_core_update_check');
   }

   if ( ! wp_next_scheduled( 'monitorchat_plugins_update_check' )&&(monitorchat_is_enabled('monitorchat_plugins_update_check_enabled'))) {
      wp_schedule_event( monitorchat_distribute_scheduled_hooks($schedules['monitorchat_plugins_update_check_interval']['interval']), 
                         'monitorchat_plugins_update_check_interval', 
                         'monitorchat_plugins_update_check' );
    }

   if((wp_next_scheduled( 'monitorchat_plugins_update_check' ))&&(!monitorchat_is_enabled('monitorchat_plugins_update_check_enabled'))){
       wp_clear_scheduled_hook('monitorchat_plugins_update_check');
   }

   if ( ! wp_next_scheduled( 'monitorchat_themes_update_check' )&&(monitorchat_is_enabled('monitorchat_themes_update_check_enabled'))) {
      wp_schedule_event( monitorchat_distribute_scheduled_hooks($schedules['monitorchat_themes_update_check_interval']['interval']),
                         'monitorchat_themes_update_check_interval', 
                         'monitorchat_themes_update_check' );
    }

   if((wp_next_scheduled( 'monitorchat_themes_update_check' ))&&(!monitorchat_is_enabled('monitorchat_themes_update_check_enabled'))){
       wp_clear_scheduled_hook('monitorchat_themes_update_check');
   }

   if ( ! wp_next_scheduled( 'monitorchat_file_system_free_space' )&&(monitorchat_is_enabled('monitorchat_file_system_free_space_enabled'))) {
      wp_schedule_event( monitorchat_distribute_scheduled_hooks($schedules['monitorchat_file_system_free_space_interval']['interval']), 
                         'monitorchat_file_system_free_space_interval', 
                         'monitorchat_file_system_free_space' );
    }

   if((wp_next_scheduled( 'monitorchat_file_system_free_space' ))&&(!monitorchat_is_enabled('monitorchat_file_system_free_space_enabled'))){
       wp_clear_scheduled_hook('monitorchat_file_system_free_space');
   }

   if ( ! wp_next_scheduled( 'monitorchat_memory' )&&(monitorchat_is_enabled('monitorchat_memory_enabled'))) {
      wp_schedule_event( monitorchat_distribute_scheduled_hooks($schedules['monitorchat_memory_interval']['interval']), 
                         'monitorchat_memory_interval', 
                         'monitorchat_memory' );
    }

   if((wp_next_scheduled( 'monitorchat_memory' ))&&(!monitorchat_is_enabled('monitorchat_memory_enabled'))){
       wp_clear_scheduled_hook('monitorchat_memory');
   }


if (is_plugin_active('wp-statistics/wp-statistics.php')){

   if ( ! wp_next_scheduled( 'monitorchat_wp_statistics_visitors' )&&(monitorchat_is_enabled('monitorchat_wp_statistics_visitors_enabled'))) {
      wp_schedule_event( monitorchat_distribute_scheduled_hooks($schedules['monitorchat_wp_statistics_visitors_interval']['interval']), 
                         'monitorchat_wp_statistics_visitors_interval', 
                         'monitorchat_wp_statistics_visitors' );
    }
   if ( ! wp_next_scheduled( 'monitorchat_wp_statistics_referrals' )&&(monitorchat_is_enabled('monitorchat_wp_statistics_referrals_enabled'))) {
      wp_schedule_event( monitorchat_distribute_scheduled_hooks($schedules['monitorchat_wp_statistics_referrals_interval']['interval']), 
                         'monitorchat_wp_statistics_referrals_interval', 
                         'monitorchat_wp_statistics_referrals' );
    }
   if ( ! wp_next_scheduled( 'monitorchat_wp_statistics_pages' )&&(monitorchat_is_enabled('monitorchat_wp_statistics_pages_enabled'))) {
      wp_schedule_event( monitorchat_distribute_scheduled_hooks($schedules['monitorchat_wp_statistics_pages_interval']['interval']), 
                         'monitorchat_wp_statistics_pages_interval', 
                         'monitorchat_wp_statistics_pages' );
    }
}

if (!is_plugin_active('wp-statistics/wp-statistics.php')){

   if(wp_next_scheduled( 'monitorchat_wp_statistics_visitors' )){
       wp_clear_scheduled_hook('monitorchat_wp_statistics_visitors');}
   if(wp_next_scheduled( 'monitorchat_wp_statistics_referrals' )){
       wp_clear_scheduled_hook('monitorchat_wp_statistics_referrals');}
   if(wp_next_scheduled( 'monitorchat_wp_statistics_pages' )){
       wp_clear_scheduled_hook('monitorchat_wp_statistics_pages');}
}

   if((wp_next_scheduled( 'monitorchat_wp_statistics_visitors' ))&&(!monitorchat_is_enabled('monitorchat_wp_statistics_visitors_enabled'))){
       wp_clear_scheduled_hook('monitorchat_wp_statistics_visitors');
   }
   if((wp_next_scheduled( 'monitorchat_wp_statistics_referrals' ))&&(!monitorchat_is_enabled('monitorchat_wp_statistics_referrals_enabled'))){
       wp_clear_scheduled_hook('monitorchat_wp_statistics_referrals');
   }
   if((wp_next_scheduled( 'monitorchat_wp_statistics_pages' ))&&(!monitorchat_is_enabled('monitorchat_wp_statistics_pages_enabled'))){
       wp_clear_scheduled_hook('monitorchat_wp_statistics_pages');
   }




?>
