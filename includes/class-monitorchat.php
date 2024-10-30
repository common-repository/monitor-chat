<?php
/**
 * Main plugin class file.
 *
 * @package WordPress Plugin Template/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class monitorchat {

	/**
	 * The single instance of monitorchat.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; 

	/**
	 * Local instance of monitorchat_Admin_API
	 *
	 * @var monitorchat_Admin_API|null
	 */
	public $admin = null;

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for JavaScripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor funtion.
	 *
	 * @param string $file File constructor.
	 * @param string $version Plugin version.
	 */
	public function __construct( $file = '', $version = MONITORCHAT_VERSION ) {
		$this->_version = $version;
		$this->_token   = 'monitorchat';

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );
                register_deactivation_hook( $this->file, array( $this, 'deactivate' ) );

		// Load frontend JS & CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load API for generic admin functions.
		if ( is_admin() ) {
			$this->admin = new monitorchat_Admin_API();
		}

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @return void
	 * @since   1.0.0
	 */
	public function enqueue_styles() {
        // Keep the commented code in case we want to add in custom css
	//	wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
	//	wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function enqueue_scripts() {
        // Keep the commented code in case we want to add in custom js
	//	wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
	//	wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Admin enqueue style.
	 *
	 * @param string $hook Hook parameter.
	 *
	 * @return void
	 */
	public function admin_enqueue_styles( $hook = '' ) {
        // Keep the commented code in case we want to add in custom css
	//	wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
	//	wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 *
	 * @access  public
	 *
	 * @param string $hook Hook parameter.
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function admin_enqueue_scripts( $hook = '' ) {
        // Keep the commented code in case we want to add in custom js
	//	wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
	//	wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_localisation() {
        // translate into other languages? 
		load_plugin_textdomain( 'monitorchat', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = 'monitorchat';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main monitorchat Instance
	 *
	 * Ensures only one instance of monitorchat is loaded or can be loaded.
	 *
	 * @param string $file File instance.
	 * @param string $version Version parameter.
	 *
	 * @return Object monitorchat instance
	 * @see monitorchat()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance( $file = '', $version = MONITORCHAT_VERSION ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of monitorchat is forbidden' ) ), esc_attr( $this->_version ) );

	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of monitorchat is forbidden' ) ), esc_attr( $this->_version ) );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function install() {
		$this->_log_version_number();
                $this->installOptions();
	} 

public function deactivate() {
    if(wp_next_scheduled( 'monitorchat_heartbeat' )){
       wp_clear_scheduled_hook('monitorchat_heartbeat');
   }
    if(wp_next_scheduled( 'monitorchat_wp_core_update_check' )){
       wp_clear_scheduled_hook('monitorchat_wp_core_update_check');
   }
    if(wp_next_scheduled( 'monitorchat_plugins_update_check' )){
       wp_clear_scheduled_hook('monitorchat_plugins_update_check');
   }
    if(wp_next_scheduled( 'monitorchat_themes_update_check' )){
       wp_clear_scheduled_hook('monitorchat_themes_update_check');
   }
    if(wp_next_scheduled( 'monitorchat_file_system_free_space' )){
       wp_clear_scheduled_hook('monitorchat_file_system_free_space');
   }
    if(wp_next_scheduled( 'monitorchat_memory' )){
       wp_clear_scheduled_hook('monitorchat_memory');
   }
    if(wp_next_scheduled( 'monitorchat_woocommerce_orders_summary' )){
       wp_clear_scheduled_hook('monitorchat_woocommerce_orders_summary');
   }
    if(wp_next_scheduled( 'monitorchat_wp_statistics_visitors' )){
       wp_clear_scheduled_hook('monitorchat_wp_statistics_visitors');
   }
    if(wp_next_scheduled( 'monitorchat_wp_statistics_referrals' )){
       wp_clear_scheduled_hook('monitorchat_wp_statistics_referrals');
   }
    if(wp_next_scheduled( 'monitorchat_wp_statistics_pages' )){
       wp_clear_scheduled_hook('monitorchat_wp_statistics_pages');
   }
} 


	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	private function _log_version_number() { 
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()


      protected function installOptions(){ 
        // INITIAL VALUES AFTER FRESH INSTALL AND ACTIVATION

        // CRITICAL THAT THESE VALUES EXIST SO THAT 
        // USER CAN SEE EXAMPLES OF WORKING MESSAGES!

        // GLOBAL
        if(!get_option('monitorchat_global_api_key')){
            add_option('monitorchat_global_api_key','Replace_with_your_API_Key');
            }
        if(!get_option('monitorchat_global_send_using')){
            add_option('monitorchat_global_send_using','wppost');
            }
        if(!get_option('monitorchat_global_shell_script')){
            add_option('monitorchat_global_shell_script','/usr/local/bin/monitor.chat.sh');
            }
        if(!get_option('monitorchat_global_enabled')){
            add_option('monitorchat_global_enabled','on');
            }
        if(!get_option('monitorchat_global_last_valid_apikey')){
            add_option('monitorchat_global_last_valid_apikey','');
            }
        if(!get_option('monitorchat_global_last_invalid_apikey')){
            add_option('monitorchat_global_last_invalid_apikey','Replace_with_your_API_Key');
            }
        if(!get_option('monitorchat_global_mask_pii')){
            add_option('monitorchat_global_mask_pii','on');
            }
        if(!get_option('monitorchat_global_distribute_scheduled')){
            add_option('monitorchat_global_distribute_scheduled','7500');
            }
        if(!get_option('monitorchat_global_hostname')){
            add_option('monitorchat_global_hostname',monitorchat_gethostname());
            }
        // LOGIN
        if(!get_option('monitorchat_logins_enabled')){
            add_option('monitorchat_logins_enabled','on');
            }
        if(!get_option('monitorchat_logins_exclude')){
            add_option('monitorchat_logins_exclude','none');
            }
        if(!get_option('monitorchat_logins_message')){
            add_option('monitorchat_logins_message',
                       "<SMILEY> <USERNAME> has logged in.\nRole(s) of this user: <USERROLES>");
            }
        if(!get_option('monitorchat_logins_recipient')){
            add_option('monitorchat_logins_recipient','yourchatroom');
            }
        // LOGOUT
        if(!get_option('monitorchat_logouts_enabled')){
            add_option('monitorchat_logouts_enabled',"on");
            }
        if(!get_option('monitorchat_logouts_message')){
            add_option('monitorchat_logouts_message',"<FLUSHED> Someone has logged out of wordpress.\nIP Address: <USERIPADDRESS>");
            }
        if(!get_option('monitorchat_logouts_recipient')){
            add_option('monitorchat_logouts_recipient',"yourchatroom");
            }
        // FAIL
        if(!get_option('monitorchat_failed_logins_enabled')){
            add_option('monitorchat_failed_logins_enabled',"on");
            }
        if(!get_option('monitorchat_failed_logins_message')){
            add_option('monitorchat_failed_logins_message',"<ASTONISHED> Someone attempted to login but failed!\nIP Address: <USERIPADDRESS>");
            }
        if(!get_option('monitorchat_failed_logins_recipient')){
            add_option('monitorchat_failed_logins_recipient',"yourchatroom");
            }
        // REGISTER
        if(!get_option('monitorchat_user_register_enabled')){
            add_option('monitorchat_user_register_enabled',"on");
            }
        if(!get_option('monitorchat_user_register_message')){
            add_option('monitorchat_user_register_message',"<SUN> A new Wordpress user has been created from IP address <USERIPADDRESS>.");
            }
        if(!get_option('monitorchat_user_register_recipient')){
            add_option('monitorchat_user_register_recipient',"yourchatroom");
            }
        // PROFILE
        if(!get_option('monitorchat_profile_update_enabled')){
            add_option('monitorchat_profile_update_enabled',"");
            }
        if(!get_option('monitorchat_profile_update_message')){
            add_option('monitorchat_profile_update_message',"<DRUM> User profile has been updated by <USERNAME>.");
            }
        if(!get_option('monitorchat_profile_update_recipient')){
            add_option('monitorchat_profile_update_recipient',"yourchatroom");
            }
        if(!get_option('monitorchat_profile_update_exclude')){
            add_option('monitorchat_profile_update_exclude',"none");
            }
        // PUBLISH POST
        if(!get_option('monitorchat_publish_post_enabled')){
            add_option('monitorchat_publish_post_enabled',"");
            }
        if(!get_option('monitorchat_publish_post_message')){
            add_option('monitorchat_publish_post_message',"<OWL> The user <AUTHOR> has published a new post.\nTitle: <TITLE>\nContent: <CONTENT>\nExcerpt: <EXCERPT>\nStatus: <STATUS>");
            }
        if(!get_option('monitorchat_publish_post_recipient')){
            add_option('monitorchat_publish_post_recipient',"yourchatroom");
            }
        if(!get_option('monitorchat_publish_post_exclude')){
            add_option('monitorchat_publish_post_exclude',"none");
            }
        // PENDING POST
        if(!get_option('monitorchat_pending_post_enabled')){
            add_option('monitorchat_pending_post_enabled',"");
            }
        if(!get_option('monitorchat_pending_post_message')){
            add_option('monitorchat_pending_post_message',"<PENGUIN> The user <AUTHOR> has a new post that is pending approval.\nTitle: <TITLE>\nContent: <CONTENT>\nExcerpt: <EXCERPT>\nStatus: <STATUS>");
            }
        if(!get_option('monitorchat_pending_post_recipient')){
            add_option('monitorchat_pending_post_recipient',"yourchatroom");
            }
        if(!get_option('monitorchat_pending_post_exclude')){
            add_option('monitorchat_pending_post_exclude',"none");
            }
        // FUTURE POST
        if(!get_option('monitorchat_future_post_enabled')){
            add_option('monitorchat_future_post_enabled',"");
            }
        if(!get_option('monitorchat_future_post_message')){
            add_option('monitorchat_future_post_message',"<SWAN> The user <AUTHOR> has a new post that is scheduled to publish in the future.\nTitle:  <TITLE>\nContent: <CONTENT>\nExcerpt: <EXCERPT>\nStatus: <STATUS>");
            }
        if(!get_option('monitorchat_future_post_recipient')){
            add_option('monitorchat_future_post_recipient',"yourchatroom");
            }
        if(!get_option('monitorchat_future_post_exclude')){
            add_option('monitorchat_future_post_exclude',"none");
            }
        // PAGE
        if(!get_option('monitorchat_publish_page_enabled')){
            add_option('monitorchat_publish_page_enabled',"");
            }
        if(!get_option('monitorchat_publish_page_message')){
            add_option('monitorchat_publish_page_message',"<DUCK> The user <AUTHOR> has published a new page.\nTitle: <TITLE>\nContent: <CONTENT>\nStatus: <STATUS>");
            }
        if(!get_option('monitorchat_publish_page_recipient')){
            add_option('monitorchat_publish_page_recipient',"yourchatroom");
            }
        if(!get_option('monitorchat_publish_page_exclude')){
            add_option('monitorchat_publish_page_exclude',"none");
            }
        // TERM
        if(!get_option('monitorchat_create_term_enabled')){
            add_option('monitorchat_create_term_enabled',"");
            }
        if(!get_option('monitorchat_create_term_message')){
            add_option('monitorchat_create_term_message',"<BULB> The user <USERNAME> has created a new term.\nTerm name: <TERMNAME>\nSlug: <TERMSLUG>\nDescription: <TERMDESC>");
            }
        if(!get_option('monitorchat_create_term_recipient')){
            add_option('monitorchat_create_term_recipient',"yourchatroom");
            }
        if(!get_option('monitorchat_create_term_exclude')){
            add_option('monitorchat_create_term_exclude',"none");
            }
        // COMMENT
        if(!get_option('monitorchat_comment_post_enabled')){
            add_option('monitorchat_comment_post_enabled',"");
            }
        if(!get_option('monitorchat_comment_post_message')){
            add_option('monitorchat_comment_post_message',"<PEN> Someone has commented on a post using the name <AUTHOR>.\nPost Title: <POSTTITLE>\nComment: <COMMENT>");
            }
        if(!get_option('monitorchat_comment_post_recipient')){
            add_option('monitorchat_comment_post_recipient',"yourchatroom");
            }
        if(!get_option('monitorchat_comment_post_exclude')){
            add_option('monitorchat_comment_post_exclude',"none");
            }
        // UPLOAD
        if(!get_option('monitorchat_attachment_upload_enabled')){
            add_option('monitorchat_attachment_upload_enabled',"");
            }
        if(!get_option('monitorchat_attachment_upload_message')){
            add_option('monitorchat_attachment_upload_message',"<BEETLE> Someone has uploaded a file to Wordpress!\n<URL>");
            }
        if(!get_option('monitorchat_attachment_upload_recipient')){
            add_option('monitorchat_attachment_upload_recipient',"yourchatroom");
            }
        if(!get_option('monitorchat_attachment_upload_exclude')){
            add_option('monitorchat_attachment_upload_exclude',"none");
            }
        // UPDATE SOFTWARE ELEMENT
        if(!get_option('monitorchat_upgrader_process_complete_enabled')){
            add_option('monitorchat_upgrader_process_complete_enabled',"");
            }
        if(!get_option('monitorchat_upgrader_process_complete_message')){
            add_option('monitorchat_upgrader_process_complete_message',"<GEAR> <UPDATED>");
            }
        if(!get_option('monitorchat_upgrader_process_complete_recipient')){
            add_option('monitorchat_upgrader_process_complete_recipient',"yourchatroom");
            }
        // HEARTBEAT
        if(!get_option('monitorchat_heartbeat_enabled')){
            add_option('monitorchat_heartbeat_enabled',"on");
            }
        if(!get_option('monitorchat_heartbeat_interval')){
            add_option('monitorchat_heartbeat_interval',"10");
            }
        if(!get_option('monitorchat_heartbeat_message')){
            add_option('monitorchat_heartbeat_message',"<HEARTBEAT> Wordpress heartbeat.");
            }
        if(!get_option('monitorchat_heartbeat_recipient')){
            add_option('monitorchat_heartbeat_recipient',"yourchatroom");
            }
        // CORE UPDATE CHECK
        if(!get_option('monitorchat_wp_core_update_check_enabled')){
            add_option('monitorchat_wp_core_update_check_enabled',"");
            }
        if(!get_option('monitorchat_wp_core_update_check_interval')){
            add_option('monitorchat_wp_core_update_check_interval',"12");
            }
        if(!get_option('monitorchat_wp_core_update_check_message')){
            add_option('monitorchat_wp_core_update_check_message',"<THINKING> Perhaps it is time to update Wordpress!\n<INSTALLEDVERSION> is the installed Wordpress version.\n<PREFERREDVERSION> is the latest Wordpress version.");
            }
        if(!get_option('monitorchat_wp_core_update_check_recipient')){
            add_option('monitorchat_wp_core_update_check_recipient',"yourchatroom");
            }
        if(!get_option('monitorchat_wp_core_update_check_alt_message')){
            add_option('monitorchat_wp_core_update_check_alt_message',"<RELAXED> Wordpress Core is up to date! The installed version is <INSTALLEDVERSION>.");
            }
         // PLUGINS UPDATE CHECK
         if(!get_option('monitorchat_plugins_update_check_enabled')){
             add_option('monitorchat_plugins_update_check_enabled',"");
             }
         if(!get_option('monitorchat_plugins_update_check_interval')){
             add_option('monitorchat_plugins_update_check_interval',"8");
             }
         if(!get_option('monitorchat_plugins_update_check_message')){
             add_option('monitorchat_plugins_update_check_message',"<OWL> There are Wordpress plugins that you may wish to update:\n<PLUGINSTOUPDATE>");
             }
         if(!get_option('monitorchat_plugins_update_check_recipient')){
             add_option('monitorchat_plugins_update_check_recipient',"yourchatroom");
             }
         if(!get_option('monitorchat_plugins_update_check_preferred_available')){
             add_option('monitorchat_plugins_update_check_preferred_available',"");
             }
         if(!get_option('monitorchat_plugins_update_check_alt_message')){
             add_option('monitorchat_plugins_update_check_alt_message',"<SUN> Wordpress plugins are all up to date.");
             }
         // THEMES UPDATE CHECK
         if(!get_option('monitorchat_themes_update_check_enabled')){
             add_option('monitorchat_themes_update_check_enabled',"");
             }
         if(!get_option('monitorchat_themes_update_check_interval')){
             add_option('monitorchat_themes_update_check_interval',"10");
             }
         if(!get_option('monitorchat_themes_update_check_message')){
             add_option('monitorchat_themes_update_check_message',"<BIRD> There are Wordpress themes that you may wish to update:\n<THEMESTOUPDATE>");
             }
         if(!get_option('monitorchat_themes_update_check_alt_message')){
             add_option('monitorchat_themes_update_check_alt_message',"<GLOBE> All Wordpress themes are up to date.");
             }
         if(!get_option('monitorchat_themes_update_check_recipient')){
             add_option('monitorchat_themes_update_check_recipient',"yourchatroom");
             }
         // FILE SYSTEM SPACE
         if(!get_option('monitorchat_file_system_free_space_enabled')){
             add_option('monitorchat_file_system_free_space_enabled',"");
             }
         if(!get_option('monitorchat_file_system_free_space_interval')){
             add_option('monitorchat_file_system_free_space_interval',"24");
             }
         if(!get_option('monitorchat_file_system_free_space_message')){
             add_option('monitorchat_file_system_free_space_message',"<CD> <FILESYSTEMREPORT>");
             }
         if(!get_option('monitorchat_file_system_free_space_pct')){
             add_option('monitorchat_file_system_free_space_pct',"50");
             }
         if(!get_option('monitorchat_file_system_free_space_recipient')){
             add_option('monitorchat_file_system_free_space_recipient',"yourchatroom");
             }
         // MEMORY
         if(!get_option('monitorchat_memory_enabled')){
             add_option('monitorchat_memory_enabled',"");
             }
         if(!get_option('monitorchat_memory_interval')){
             add_option('monitorchat_memory_interval',"12");
             }
         if(!get_option('monitorchat_memory_message')){
             add_option('monitorchat_memory_message',"<THOUGHT> <MEMORYREPORT>");
             }
         if(!get_option('monitorchat_memory_recipient')){
             add_option('monitorchat_memory_recipient',"yourchatroom");
             }
         // AKISMET SPAM CAUGHT
         if(!get_option('monitorchat_akismet_spam_caught_enabled')){
             add_option('monitorchat_akismet_spam_caught_enabled',"");
             }
         if(!get_option('monitorchat_akismet_spam_caught_message')){
             add_option('monitorchat_akismet_spam_caught_message',"<ASTONISHED> Akismet has caught spam. Look in the spam folder.");
             }
         if(!get_option('monitorchat_akismet_spam_caught_recipient')){
             add_option('monitorchat_akismet_spam_caught_recipient',"yourchatroom");
             }
         // UPDRAFT BACKUP
         if(!get_option('monitorchat_updraft_backups_enabled')){
             add_option('monitorchat_updraft_backups_enabled',"");
             }
         if(!get_option('monitorchat_updraft_backup_message')){
             add_option('monitorchat_updraft_backup_message',"<SEEDLING> Updraft has completed a backup. File list:\n<UPDRAFTFILES>");
             }
         if(!get_option('monitorchat_updraft_backup_recipient')){
             add_option('monitorchat_updraft_backup_recipient',"yourchatroom");
             }
         // GWOLLE GUESTBOOK
         if(!get_option('monitorchat_gwolle_guestbook_enabled')){
             add_option('monitorchat_gwolle_guestbook_enabled',"");
             }
         if(!get_option('monitorchat_gwolle_guestbook_message')){
             add_option('monitorchat_gwolle_guestbook_message',"<NOTEBOOK2> Someone with email <AUTHOREMAIL> has left the following guestbook entry:\n<GWOLLE>");
             }
         if(!get_option('monitorchat_gwolle_guestbook_recipient')){
             add_option('monitorchat_gwolle_guestbook_recipient',"yourchatroom");
             }
          // WOOCOMMERCE NEW PRODUCT
         if(!get_option('monitorchat_woocommerce_new_product_enabled')){
             add_option('monitorchat_woocommerce_new_product_enabled',"");
             }
         if(!get_option('monitorchat_woocommerce_new_product_message')){
             add_option('monitorchat_woocommerce_new_product_message',"<PACKAGE> The user <USERNAME> has published a new product.\nProduct ID: <PRODUCTID>\nProduct Name: <PRODUCTNAME>\nStatus: <PRODUCTSTATUS>\nDescription: <PRODUCTDESC>");
             }
         if(!get_option('monitorchat_woocommerce_new_product_recipient')){
             add_option('monitorchat_woocommerce_new_product_recipient',"yourchatroom");
             }
         if(!get_option('monitorchat_woocommerce_new_product_exclude')){
             add_option('monitorchat_woocommerce_new_product_exclude',"none");
             }
         // WOOCOMMERCE ORDER STATUS CHANGE
         if(!get_option('monitorchat_woocommerce_order_status_changed_enabled')){
             add_option('monitorchat_woocommerce_order_status_changed_enabled',"on");
             }
         if(!get_option('monitorchat_woocommerce_order_status_change_enabled')){
             add_option('monitorchat_woocommerce_order_status_change_enabled',"");
             }
         if(!get_option('monitorchat_woocommerce_order_status_change_message')){
             add_option('monitorchat_woocommerce_order_status_change_message',"<SMILECAT> A Woocommerce order with ID <ORDERID> has a status of <STATUS>.\n<POINTRIGHT> Order's Destination: <DESTINATION>\n<POINTRIGHT> Products:\n<PRODUCTLIST>\nOrder total: <TOTAL>\nOrder currency: <CURRENCY>");
             }
         if(!get_option('monitorchat_woocommerce_order_status_change_recipient')){
             add_option('monitorchat_woocommerce_order_status_change_recipient',"yourchatroom");
             }
         if(!get_option('monitorchat_woocommerce_order_status_change_pending')){
             add_option('monitorchat_woocommerce_order_status_change_pending',"on");
             }
         if(!get_option('monitorchat_woocommerce_order_status_change_processing')){
             add_option('monitorchat_woocommerce_order_status_change_processing',"on");
             }
         if(!get_option('monitorchat_woocommerce_order_status_change_on-hold')){
             add_option('monitorchat_woocommerce_order_status_change_on-hold',"on");
             }
         if(!get_option('monitorchat_woocommerce_order_status_change_completed')){
             add_option('monitorchat_woocommerce_order_status_change_completed',"on");
             }
         if(!get_option('monitorchat_woocommerce_order_status_change_cancelled')){
             add_option('monitorchat_woocommerce_order_status_change_cancelled',"on");
             }
         if(!get_option('monitorchat_woocommerce_order_status_change_refunded')){
             add_option('monitorchat_woocommerce_order_status_change_refunded',"on");
             }
         // WOOCOMMERCE COUPON REDEEMED
         if(!get_option('monitorchat_woocommerce_applied_coupon_enabled')){
             add_option('monitorchat_woocommerce_applied_coupon_enabled',"");
             }
         if(!get_option('monitorchat_woocommerce_applied_coupon_message')){
             add_option('monitorchat_woocommerce_applied_coupon_message',"<TICKET> A coupon called <COUPONCODE> was redeemed.");
             }
         if(!get_option('monitorchat_woocommerce_applied_coupon_recipient')){
             add_option('monitorchat_woocommerce_applied_coupon_recipient',"yourchatroom");
             }
         // WOOCOMMERCE SUMMARY OF RECENT ORDERS
         if(!get_option('monitorchat_woocommerce_orders_summary_enabled')){
             add_option('monitorchat_woocommerce_orders_summary_enabled',"");
             }
         if(!get_option('monitorchat_woocommerce_orders_summary_interval')){
             add_option('monitorchat_woocommerce_orders_summary_interval',"4");
             }
         if(!get_option('monitorchat_woocommerce_orders_summary_message')){
             add_option('monitorchat_woocommerce_orders_summary_message',"<BARCHART> <SUMMARY>");
             }
         if(!get_option('monitorchat_woocommerce_orders_summary_pending')){
             add_option('monitorchat_woocommerce_orders_summary_pending',"on");
             }
         if(!get_option('monitorchat_woocommerce_orders_summary_processing')){
             add_option('monitorchat_woocommerce_orders_summary_processing',"on");
             }
         if(!get_option('monitorchat_woocommerce_orders_summary_completed')){
             add_option('monitorchat_woocommerce_orders_summary_completed',"on");
             }
         if(!get_option('monitorchat_woocommerce_orders_summary_order_by')){
             add_option('monitorchat_woocommerce_orders_summary_order_by',"gross_after_discount");
             }
         if(!get_option('monitorchat_woocommerce_orders_summary_limit')){
             add_option('monitorchat_woocommerce_orders_summary_limit',"10");
             }
         if(!get_option('monitorchat_woocommerce_orders_summary_recipient')){
             add_option('monitorchat_woocommerce_orders_summary_recipient',"yourchatroom");
             }
         if(!get_option('monitorchat_woocommerce_orders_summary_on-hold')){
             add_option('monitorchat_woocommerce_orders_summary_on-hold',"on");
             }
         // WP STATISTICS VISITORS REPORT
         if(!get_option('monitorchat_wp_statistics_visitors_enabled')){
             add_option('monitorchat_wp_statistics_visitors_enabled',"");
             }
         if(!get_option('monitorchat_wp_statistics_visitors_interval')){
             add_option('monitorchat_wp_statistics_visitors_interval',"60");
             }
         if(!get_option('monitorchat_wp_statistics_visitors_message')){
             add_option('monitorchat_wp_statistics_visitors_message',"<FAMILY> <ONLINEVISITORS>\n<VISITORSREPORT>\n<VISITSREPORT>");
             }
         if(!get_option('monitorchat_wp_statistics_visitors_recipient')){
             add_option('monitorchat_wp_statistics_visitors_recipient',"yourchatroom");
             }
         // WP STATISTICS REFERRALS REPORT
         if(!get_option('monitorchat_plugins_update_check_preferred_available')){
             add_option('monitorchat_plugins_update_check_preferred_available',"");
             }
         if(!get_option('monitorchat_wp_statistics_referrals_enabled')){
             add_option('monitorchat_wp_statistics_referrals_enabled',"");
             }
         if(!get_option('monitorchat_wp_statistics_referrals_interval')){
             add_option('monitorchat_wp_statistics_referrals_interval',"12");
             }
         if(!get_option('monitorchat_wp_statistics_referrals_message')){
             add_option('monitorchat_wp_statistics_referrals_message',"<ROCKET> <REFERRALSREPORT>");
             }
         if(!get_option('monitorchat_wp_statistics_referrals_recipient')){
             add_option('monitorchat_wp_statistics_referrals_recipient',"yourchatroom");
             }
         // WP STATISTICS TOP PAGES REPORT
         if(!get_option('monitorchat_wp_statistics_pages_enabled')){
             add_option('monitorchat_wp_statistics_pages_enabled',"");
             }
         if(!get_option('monitorchat_wp_statistics_pages_interval')){
             add_option('monitorchat_wp_statistics_pages_interval',"24");
             }
         if(!get_option('monitorchat_wp_statistics_pages_message')){
             add_option('monitorchat_wp_statistics_pages_message',"<CLIPBOARD> <TOPPAGESREPORT>");
             }
         if(!get_option('monitorchat_wp_statistics_pages_recipient')){
             add_option('monitorchat_wp_statistics_pages_recipient',"yourchatroom");
             }
      }
}
