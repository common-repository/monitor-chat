<?php
require_once  ABSPATH . 'wp-admin/includes/plugin.php';
/**
 * Settings class file.
 *
 * @package WordPress Plugin Template/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 */
class monitorchat_Settings {

	/**
	 * The single instance of monitorchat_Settings.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; 

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

        /**
         * Default test message.
         *
         * @var     string
         * @access  public
         * @since   1.0.0
         */
        public $message ='<SUNGLASSES> This is a test message sent from Wordpress.';

        /**
         * Default test recipient.
         *
         * @var     string
         * @access  public
         * @since   1.0.0
         */
        public $recipient ='reco';

	/**
	 * Constructor function.
	 *
	 * @param object $parent Parent object.
	 */
	public function __construct( $parent ) {

        if(isset($_POST['monitorchat_test_message'])){
          $this->message=monitorchat_sanitize($_POST['monitorchat_test_message']);}
        if(isset($_POST['monitorchat_test_recipient'])){
          $this->recipient=sanitize_text_field($_POST['monitorchat_test_recipient']);}
          if(!in_array($this->recipient,array('yourchatroom','reco','both','all','test'))){$this->recipient='reco';}

		$this->parent = $parent;

		$this->base = 'monitorchat_';

		// Initialise settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu.
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page.
		add_filter(
			'plugin_action_links_' . plugin_basename( $this->parent->file ),
			array(
				$this,
				'add_settings_link',
			)
		);

		// Configure placement of plugin settings page. See readme for implementation.
		add_filter( $this->base . 'menu_settings', array( $this, 'configure_settings' ) );
	}

	/**
	 * Initialise settings
	 *
	 * @return void
	 */
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item() {

		$args = $this->menu_settings();

		// Do nothing if wrong location key is set.
		if ( is_array( $args ) && isset( $args['location'] ) && function_exists( 'add_' . $args['location'] . '_page' ) ) {
			switch ( $args['location'] ) {
				case 'options':
				case 'submenu':
					$page = add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'] );
					break;
				case 'menu':
					$page = add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position'] );
					break;
				default:
					return;
			}
			add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
		}
	}

	/**
	 * Prepare default settings page arguments
	 *
	 * @return mixed|void
	 */
	private function menu_settings() {
		return apply_filters(
			$this->base . 'menu_settings',
			array(
				'location'    => 'options', // Possible settings: options, menu, submenu.
				'parent_slug' => 'options-general.php',
				'page_title'  => __( 'Monitor.chat Settings', 'monitorchat' ),
				'menu_title'  => __( 'Monitor.chat', 'monitorchat' ),
				'capability'  => 'manage_options',
				'menu_slug'   => $this->parent->_token . '_settings',
				'function'    => array( $this, 'settings_page' ),
				'icon_url'    => '',
				'position'    => null,
			)
		);
	}

	/**
	 * Container for settings page arguments
	 *
	 * @param array $settings Settings array.
	 *
	 * @return array
	 */
	public function configure_settings( $settings = array() ) {
		return $settings;
	}

	/**
	 * Load settings JS & CSS
	 *
	 * @return void
	 */
	public function settings_assets() {}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'monitorchat' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

   // EDWARD - get an array of WP roles, add "none"
   global $wp_roles;
   if ( !isset( $wp_roles ) ) $wp_roles = new WP_Roles();
   $available_roles = array();
   $available_roles = $wp_roles->get_names();
   $add_none = array('none' => 'none');
   $available_roles = ($available_roles+$add_none);

      $ready=monitorchat_is_enabled('monitorchat_global_enabled');
      $all_recipients=array('yourchatroom'=>'your chatroom','reco'=>'recovery contact','both'=>'both recovery contact and your chatroom','all'=>'all recipients','test'=>'test chatroom');
      $what_is_this='<a href="https://monitor.chat/documentation/command_line/#the-second-parameter" target="_blank">What is this?</a>';
if (get_option('monitorchat_about_hidden')!='on'){
                $settings['about'] = array(
                        'title'       => __( 'About', 'monitorchat' ),
                        'description' => __( 'About Monitor.chat.', 'monitorchat' ),
                        'shortcodes' => __( 'none' ),
                        'fields' => array(),

                );
}
if (get_option('monitorchat_api_key_hidden')!='on'){
		$settings['apikey'] = array(
			'title'       => __( 'API Key', 'monitorchat' ),
			'description' => __( 'Global Settings for the Monitor.chat Plugin.<br>'
                            .monitorchat_populate_apikey_report()
                            .monitorchat_display_apikey_report_link()
                            .monitorchat_display_apikey_report(), 'monitorchat' ),
                        'shortcodes' => __( 'none' ),
			'fields'      => array(
				array(
					'id'          => 'global_api_key',
					'label'       => __( 'Your Monitor.chat API Key', 'monitorchat' ),
					'description' => __( __('<br><ul style="list-style-type:disc; margin-left: 2rem;"><li>If you are using the <strong>monitor.chat.sh script</strong> option, this value will be ignored.</li></ul>'), 'monitorchat' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( '', 'monitorchat' ),
                                        'callback' =>'monitorchat_is_apikey_valid',
				),
                                array(
                                        'id'          => 'global_shell_script',
                                        'label'       => __( 'Full path and file name of monitor.chat.sh', 'monitorchat' ),
                                        'description' => __( __('<br><ul style="list-style-type:disc; margin-left: 2rem;"><li>If you are <strong>not</strong> using the <strong>monitor.chat.sh script</strong> option, this value will be ignored.</li><li>To change this value, the file basename must be <strong>monitor.chat.sh</strong>.</li><li>To change this value, the script file must exist, be executable, and be accessible to the web server application.</li></ul>', 'monitorchat' )),
                                        'type'        => 'text',
                                        'default'     => '/usr/local/bin/monitor.chat.sh',
                                        'placeholder' => __( '', 'monitorchat' ),
                                        'callback'    => 'monitorchat_validate_shell_script'
                                ),
				array(
					'id'          => 'global_send_using',
					'label'       => __( 'Send Messages Using', 'monitorchat' ),
					'description' => __( '', 'monitorchat' ),
					'type'        => 'select',
                                        'callback'    => 'monitorchat_is_send_using_ok',
					'options'     => array(
						'script'     => 'monitor.chat.sh script',
                                                'embed'      => 'embedded bash script',
                                                'wppost'     => 'Wordpress remote post',
					),
					'default'     => 'embed',
				),
                                array(
                                        'id'          => 'global_distribute_scheduled',
                                        'label'       => __( 'Seperate Scheduled by', 'monitorchat' ),
                                        'description' => __( 'Initialize scheduled messages with this interval between them.', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => array(
                                                '2100'    => '35 minutes',
                                                '3900'    => '1 hour, 5 minutes',
                                                '7500'    => '2 hours, 5 minutes',
                                                '11100'   => '3 hours, 5 minutes',
                                                '14700'   => '4 hours, 5 minutes',
                                        ),
                                        'default'     => '7500',
                                ),
                                array(
                                        'id'          => 'global_enabled',
                                        'label'       => __( 'Enabled', 'monitorchat' ),
                                        'description' => __( 'Uncheck this box to disable all messages from this plugin.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                               array(
                                        'id'          => 'global_mask_pii',
                                        'label'       => __( 'Mask PII', 'monitorchat' ),
                                        'description' => __( 'Mask personally identifiable information (PII). Masking cannot be disabled when using a trial API key.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                        'callback'    => 'monitorchat_can_pii_be_disabled',
                                ),
                               array(
                                        'id'          => 'global_hostname',
                                        'label'       => __( 'Hostname', 'monitorchat' ),
                                        'description' => __( __('<br><ul style="list-style-type:disc; margin-left: 2rem;"><li>If you are using the <strong>monitor.chat.sh script</strong> option, this value will be ignored.</li><li>Hostname will appear in brackets at the end of each message to identify where the message originated.</li></ul>', 'monitorchat' )),
                                        'type'        => 'text',
                                        'default'     => monitorchat_gethostname(),
                                        'placeholder' => __( '', 'monitorchat' ),
                                        'callback'    => 'monitorchat_validate_hostname'
                                ),

			),
		);
}

if (get_option('monitorchat_test_hidden')!='on'){

                $settings['test_message'] = array(
                        'title'       => __( 'Test', 'monitorchat' ),
                        'description' => __( 'Send a test message to verify the configuration.', 'monitorchat' ),
                        'shortcodes' => ( 'EMOJI,userattribs' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'test_message',
                                        'label'       => __( 'Your Test Message', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => $this->message,
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'test_recipient',
                                        'label'       => __( 'Send Your Test Message to:', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => $this->recipient,
                                ),
                        ),
                );
}
                $settings['enable'] = array(
                        'title'       => __( 'Enable', 'monitorchat' ),
                        'description' => __( 'Enable messages triggered by human interactions with Wordpress.', 'monitorchat' ),
                        'shortcodes' => __( 'none' ),
                        'fields'      => array(
                                                       array(
                                        'id'          => 'logins_enabled',
                                        'label'       => __( 'Logins', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when someone logs in to your Wordpress website.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                                                        array(
                                        'id'          => 'logouts_enabled',
                                        'label'       => __( 'Logouts', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when someone logs out of your Wordpress website.', '
monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                                                        array(
                                        'id'          => 'failed_logins_enabled',
                                        'label'       => __( 'Login Failures', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when someone attempts to login but fails.', 'monitor
chat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
							array(
					'id'          => 'user_register_enabled',
					'label'       => __( 'Registers', 'monitorchat' ),
					'description' => __( 'Receive an instant message when someone registers as a new user.', 'monitorchat' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
							array(
					'id'          => 'profile_update_enabled',
					'label'       => __( 'Profile Updates', 'monitorchat' ),
					'description' => __( 'Receive an instant message when someone updates his profile.', 'monitorchat' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
							array(
					'id'          => 'publish_post_enabled',
					'label'       => __( 'Publish Posts', 'monitorchat' ),
					'description' => __( 'Receive an instant message when someone publishes a post.', 'monitorchat' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
                                                        array(
                                        'id'          => 'pending_post_enabled',
                                        'label'       => __( 'Pending Posts', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when someone creates a post that is pending approval.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                                                        array(
                                        'id'          => 'future_post_enabled',
                                        'label'       => __( 'Future Posts', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when someone creates a post that is scheduled to be published.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                           ),
                                                        array(
                                        'id'          => 'publish_page_enabled',
                                        'label'       => __( 'Publish Pages', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when someone publishes a page.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                                                        array(
                                        'id'          => 'create_term_enabled',
                                        'label'       => __( 'Create Terms', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when someone creates a new term.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
							array(
					'id'          => 'comment_post_enabled',
					'label'       => __( 'Comments', 'monitorchat' ),
					'description' => __( 'Receive an instant message when someone comments on a post.', 'monitorchat' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
                                                        array(
                                        'id'          => 'attachment_upload_enabled',
                                        'label'       => __( 'Uploads', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when someone uploads a media file or attachment.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                                                       array(
                                        'id'          => 'upgrader_process_complete_enabled',
                                        'label'       => __( 'Update', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when any Wordpress software element is updated.', 'monit
orchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                ),

                                                        array(
                                        'id'=>'break1',
                                        'description' => __( 'Enable messages sent on a schedule.', 'monitorchat'),
                                        'type'        => 'table_break',
                                ),

                                                      array(
                                        'id'          => 'heartbeat_enabled',
                                        'label'       => __( 'Heartbeats', 'monitorchat' ),
                                        'description' => __( 'Receive instant messages at regular intervals.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                                                       array(
                                        'id'          => 'wp_core_update_check_enabled',
                                        'label'       => __( 'WP Core Updates', 'monitorchat' ),
                                        'description' => __( 'Receive instant messages informing you whether Wordpress core software is up to date.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                                                       array(
                                        'id'          => 'plugins_update_check_enabled',
                                        'label'       => __( 'Plugin Updates', 'monitorchat' ),
                                        'description' => __( 'Receive instant messages informing you whether your plugins are up to date.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                ),
 
                                                       array(
                                        'id'          => 'themes_update_check_enabled',
                                        'label'       => __( 'Theme Updates', 'monitorchat' ),
                                        'description' => __( 'Receive instant messages informing you whether your themes are up to date.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                        ),
                );

if((strtoupper(substr(PHP_OS,0,3))!='WIN')&&(monitorchat_shell_exec_enabled())){
$settings['enable']['fields'][90]=   array(
                                        'id'          => 'file_system_free_space_enabled',
                                        'label'       => __( 'File System Space', 'monitorchat' ),
                                        'description' => __( 'Receive instant messages informing you of available space on file system.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                );
$settings['enable']['fields'][91]=   array(
                                        'id'          => 'memory_enabled',
                                        'label'       => __( 'Memory', 'monitorchat' ),
                                        'description' => __( 'Receive instant messages informing you of memory usage on your wordpress server.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                );
}



if ((is_plugin_active('updraftplus/updraftplus.php')) ||
   ( is_plugin_active('gwolle-gb/gwolle-gb.php')) ||
   ( is_plugin_active('woocommerce/woocommerce.php')) ||
   ( is_plugin_active('wp-statistics/wp-statistics.php'))||
   ( is_plugin_active('akismet/akismet.php') )) {
$settings['enable']['fields'][100]=
                                                        array(
                                        'id'=>'break2',
                                        'description' => __( 'Enable messages that depend on other plugins.', 'monitorchat'),
                                        'type'        => 'table_break',
                                );

}

if ( is_plugin_active('akismet/akismet.php') ) {
    $settings['enable']['fields'][101]=
                                                      array(
                                        'id'          => 'akismet_spam_caught_enabled',
                                        'label'       => __( 'Akismet Spam Caught', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when Akismet catches spam.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                );
}

if ( is_plugin_active('updraftplus/updraftplus.php') ) {
    $settings['enable']['fields'][102]=  
                                                      array(
                                        'id'          => 'updraft_backups_enabled',
                                        'label'       => __( 'Updraft Backups', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when Updraft completes a backup.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                );
}
if(is_plugin_active('gwolle-gb/gwolle-gb.php')){
    $settings['enable']['fields'][103]=
                                                      array(
                                        'id'          => 'gwolle_guestbook_enabled',
                                        'label'       => __( 'Gwolle Guestbook', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when someone leaves a new Gwolle guestbook entry.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                );
}
if (is_plugin_active('woocommerce/woocommerce.php')) {
    $settings['enable']['fields'][104]=
                                                      array(
                                        'id'          => 'woocommerce_new_product_enabled',
                                        'label'       => __( 'New Product', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when a Woocommerce product is published.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                );

    $settings['enable']['fields'][105]=
                                                      array(
                                        'id'          => 'woocommerce_order_status_change_enabled',
                                        'label'       => __( 'Order Status Changed', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when a Woocommerce order is created or has a change in its status.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                );


    $settings['enable']['fields'][107]=
                                                      array(
                                        'id'          => 'woocommerce_applied_coupon_enabled',
                                        'label'       => __( 'Coupon Applied', 'monitorchat' ),
                                        'description' => __( 'Receive an instant message when Woocommerce coupon is applied to an order.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                );
    $settings['enable']['fields'][108]=
                                                      array(
                                        'id'          => 'woocommerce_orders_summary_enabled',
                                        'label'       => __( 'Orders Summary', 'monitorchat' ),
                                        'description' => __( 'Receive instant messages informing you of products recently ordered.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                );
}
if(is_plugin_active('wp-statistics/wp-statistics.php')){
    $settings['enable']['fields'][111]=
                                                      array(
                                        'id'          => 'wp_statistics_visitors_enabled',
                                        'label'       => __( 'Visitor Report', 'monitorchat' ),
                                        'description' => __( 'Receive an instant messages informing you of visitors to your website.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                );
    $settings['enable']['fields'][112]=
                                                      array(
                                        'id'          => 'wp_statistics_referrals_enabled',
                                        'label'       => __( 'Referrals Report', 'monitorchat' ),
                                        'description' => __( 'Receive an instant messages informing you of search engine referrals.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                );
    $settings['enable']['fields'][113]=
                                                      array(
                                        'id'          => 'wp_statistics_pages_enabled',
                                        'label'       => __( 'Pages Report', 'monitorchat' ),
                                        'description' => __( 'Receive an instant messages informing you of the top visited pages.', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                );
}

// once a user has  a permanent API key, allow him to hide the setup tabs
if ((monitorchat_validate_apikey(get_option('monitorchat_global_api_key'))=='PERM')||
    (get_option('monitorchat_test_hidden')=='on')||
    (get_option('monitorchat_api_key_hidden')=='on')||
    (get_option('monitorchat_about_hidden')=='on')){
$settings['enable']['fields'][300]=   array(
                                        'id'=>'break3',
                                        'description' => __( 'Hide the tabs related to the initial setup.', 'monitorchat'),
                                        'type'        => 'table_break',
                                );
$settings['enable']['fields'][301]=     array(
                                        'id'          => 'about_hidden',
                                        'label'       => __( 'Hide About Tab', 'monitorchat' ),
                                        'description' => __( 'Do not display the About tab.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                );
$settings['enable']['fields'][302]=     array(
                                        'id'          => 'api_key_hidden',
                                        'label'       => __( 'Hide API Key Tab', 'monitorchat' ),
                                        'description' => __( 'Do not display the API Key tab.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                );
$settings['enable']['fields'][303]=     array(
                                        'id'          => 'test_hidden',
                                        'label'       => __( 'Hide Test Tab', 'monitorchat' ),
                                        'description' => __( 'Do not display the Test tab.', 'monitorchat' ),
                                       'type'        => 'checkbox',
                                        'default'     => '',
                                );								
}

if (get_option('monitorchat_logins_enabled')=='on'){
		$settings['logins'] = array(
			'title'       => __( 'Login', 'monitorchat' ),
			'description' => __( 'Receive an instant message when someone logs in to your Wordpress website.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,userattribs' ),
			'fields'      => array(
				array(
					'id'          => 'logins_message',
					'label'       => __( 'Message On Login', 'monitorchat' ),
					'description' => __( '', 'monitorchat' ),
					'type'        => 'textarea',
					'default'     => '',
					'placeholder' => __( '', 'monitorchat' ),
				),
				array(
					'id'          => 'logins_recipient',
					'label'       => __( 'Send Messages to', 'monitorchat' ),
					'description' => __( $what_is_this, 'monitorchat' ),
					'type'        => 'select',
					'options'     => $all_recipients,
					'default'     => 'yourchatroom',
				),
                                array(
                                        'id'          => 'logins_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),

			),
		);
}
if (get_option('monitorchat_logouts_enabled')=='on'){
		$settings['logouts'] = array(
			'title'       => __( 'Logout', 'monitorchat' ),
			'description' => __( 'Receive an instant message when someone logs out of your Wordpress website.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,USERIPADDRESS,USERAGENT' ),
			'fields'      => array(
				array(
					'id'          => 'logouts_message',
					'label'       => __( 'Message On Logout', 'monitorchat' ),
					'description' => __( '', 'monitorchat' ),
					'type'        => 'textarea',
					'default'     => '',
					'placeholder' => __( '', 'monitorchat' ),
				),
				array(
					'id'          => 'logouts_recipient',
					'label'       => __( 'Send Messages to', 'monitorchat' ),
					'description' => __( $what_is_this, 'monitorchat' ),
					'type'        => 'select',
					'options'     => $all_recipients,
					'default'     => 'yourchatroom',
				),
			),
		);
}
if (get_option('monitorchat_failed_logins_enabled')=='on'){
		$settings['failed_logins'] = array(
			'title'       => __( 'Fail', 'monitorchat' ),
			'description' => __( 'Receive an instant message when someone attempts to login but fails.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,USERIPADDRESS,USERAGENT' ),
			'fields'      => array(
				array(
					'id'          => 'failed_logins_message',
					'label'       => __( 'Message On Login Failure', 'monitorchat' ),
					'description' => __( '', 'monitorchat' ),
					'type'        => 'textarea',
					'default'     => '',
					'placeholder' => __( '', 'monitorchat' ),
				),
				array(
					'id'          => 'failed_logins_recipient',
					'label'       => __( 'Send Messages to', 'monitorchat' ),
					'description' => __( $what_is_this, 'monitorchat' ),
					'type'        => 'select',
					'options'     => $all_recipients,
					'default'     => 'yourchatroom',
				),
			),
		);
}
if (get_option('monitorchat_user_register_enabled')=='on'){
		$settings['user_register'] = array(
			'title'       => __( 'Register', 'monitorchat' ),
			'description' => __( 'Receive an instant message when someone registers as a new user.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,USERIPADDRESS,USERAGENT' ),
			'fields'      => array(
				array(
					'id'          => 'user_register_message',
					'label'       => __( 'Message On New User Register', 'monitorchat' ),
					'description' => __( '', 'monitorchat' ),
					'type'        => 'textarea',
					'default'     => '',
					'placeholder' => __( '', 'monitorchat' ),
				),
				array(
					'id'          => 'user_register_recipient',
					'label'       => __( 'Send Messages to', 'monitorchat' ),
					'description' => __( $what_is_this, 'monitorchat' ),
					'type'        => 'select',
					'options'     => $all_recipients,
					'default'     => 'yourchatroom',
				),
			),
		);
}
if (get_option('monitorchat_profile_update_enabled')=='on'){
		$settings['profile_update'] = array(
			'title'       => __( 'Profile', 'monitorchat' ),
			'description' => __( 'Receive an instant message when someone updates his profile.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,userattribs' ),
			'fields'      => array(
				array(
					'id'          => 'profile_update_message',
					'label'       => __( 'Message On Profile Update', 'monitorchat' ),
					'description' => __( '', 'monitorchat' ),
					'type'        => 'textarea',
					'default'     => '',
					'placeholder' => __( '', 'monitorchat' ),
				),
				array(
					'id'          => 'profile_update_recipient',
					'label'       => __( 'Send Messages to', 'monitorchat' ),
					'description' => __( $what_is_this, 'monitorchat' ),
					'type'        => 'select',
					'options'     => $all_recipients,
					'default'     => 'yourchatroom',
				),
                                array(
                                        'id'          => 'profile_update_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),
			),
		);
}
if (get_option('monitorchat_publish_post_enabled')=='on'){
		$settings['publish_post'] = array(
			'title'       => __( 'Post', 'monitorchat' ),
			'description' => __( 'Receive an instant message when someone publishes a post.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,post' ),
			'fields'      => array(
				array(
					'id'          => 'publish_post_message',
					'label'       => __( 'Message On Publish of Post', 'monitorchat' ),
					'description' => __( '', 'monitorchat' ),
					'type'        => 'textarea',
					'default'     => '',
					'placeholder' => __( '', 'monitorchat' ),
                                        'rows'        => '6',
				),
				array(
					'id'          => 'publish_post_recipient',
					'label'       => __( 'Send Messages to', 'monitorchat' ),
					'description' => __( $what_is_this, 'monitorchat' ),
					'type'        => 'select',
					'options'     => $all_recipients,
					'default'     => 'yourchatroom',
				),
                                array(
                                        'id'          => 'publish_post_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),
			),
		);
}
if (get_option('monitorchat_pending_post_enabled')=='on'){
                $settings['pending_post'] = array(
                        'title'       => __( 'Pending', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when someone creates a post that is pending approval.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,post' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'pending_post_message',
                                        'label'       => __( 'Message On New Pending Post', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                        'rows'        => '6',
                                ),
                                array(
                                        'id'          => 'pending_post_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                                array(
                                        'id'          => 'pending_post_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),
                        ),
                );
}
if (get_option('monitorchat_future_post_enabled')=='on'){
                $settings['future_post'] = array(
                        'title'       => __( 'Future', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when someone creates a post that is scheduled to be published.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,post' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'future_post_message',
                                        'label'       => __( 'Message On Future Post', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                        'rows'        => '6',
                                ),
                                array(
                                        'id'          => 'future_post_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                                array(
                                        'id'          => 'future_post_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),
                        ),
                );
}
if (get_option('monitorchat_publish_page_enabled')=='on'){
                $settings['publish_page'] = array(
                        'title'       => __( 'Page', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when someone publishes a page.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,pubpage' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'publish_page_message',
                                        'label'       => __( 'Message On Publish of Page', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                        'rows'        => '5',
                                ),
                                array(
                                        'id'          => 'publish_page_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                                array(
                                        'id'          => 'publish_page_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),
                        ),
                );
}

if (get_option('monitorchat_create_term_enabled')=='on'){
                $settings['create_term'] = array(
                        'title'       => __( 'Term', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when someone creates a new term. A term is an item in a taxonomy, such as a category or tag.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,USERNAME,term' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'create_term_message',
                                        'label'       => __( 'Message On Creation of term', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                        'rows'        => '5',
                                ),
                                array(
                                        'id'          => 'create_term_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                                array(
                                        'id'          => 'create_term_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),
                        ),
                );
}
if (get_option('monitorchat_comment_post_enabled')=='on'){
		$settings['comment_post'] = array(
			'title'       => __( 'Comment', 'monitorchat' ),
			'description' => __( 'Receive an instant message when someone comments on a post.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,comment' ),
			'fields'      => array(
				array(
					'id'          => 'comment_post_message',
					'label'       => __( 'Message On Comment of Post', 'monitorchat' ),
					'description' => __( '', 'monitorchat' ),
					'type'        => 'textarea',
					'default'     => '',
					'placeholder' => __( '', 'monitorchat' ),
                                        'rows'        => '4',
				),
				array(
					'id'          => 'comment_post_recipient',
					'label'       => __( 'Send Messages to', 'monitorchat' ),
					'description' => __( $what_is_this, 'monitorchat' ),
					'type'        => 'select',
					'options'     => $all_recipients,
					'default'     => 'yourchatroom',
				),
                                array(
                                        'id'          => 'comment_post_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),
			),
		);
}
if (get_option('monitorchat_attachment_upload_enabled')=='on'){
                $settings['attachment_upload'] = array(
                        'title'       => __( 'Upload', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when someone uploads a media file or attachment.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,attachment' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'attachment_upload_message',
                                        'label'       => __( 'Message On Attachment Upload', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'attachment_upload_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                                array(
                                        'id'          => 'attachment_upload_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),
                        ),
                );
}
if (get_option('monitorchat_upgrader_process_complete_enabled')=='on'){
                $settings['upgrader_process_complete'] = array(
                        'title'       => __( 'Updated', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when any Wordpress software element is updated.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,updatereport' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'upgrader_process_complete_message',
                                        'label'       => __( 'Message On Software Update', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'upgrader_process_complete_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
}
if (get_option('monitorchat_heartbeat_enabled')=='on'){

  $next_heartbeat=monitorchat_next_scheduled_hook('monitorchat_heartbeat');
  $description = sprintf(__( 'Receive instant messages at regular intervals.<br><strong>The recommended interval for the heartbeat is once every 10 minutes.</strong><br>The next Monitor.chat heartbeat will occur in %1$s.', 'monitorchat' ),$next_heartbeat);
  if(!$ready){$description = __( 'Receive instant messages at regular intervals.<br><strong>The recommended interval for the heartbeat is once every 10 minutes.</strong>', 'monitorchat' );}

                $settings['heartbeat'] = array(
                        'title'       => __( 'Heartbeat', 'monitorchat' ),
                        'description' => $description,
                        'shortcodes' => __( 'EMOJI' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'heartbeat_interval',
                                        'label'       => __( 'Minutes between heartbeats', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '30',
                                        'min'         => '2',
                                        'max'         => '60',
                                        'units'       => 'Minutes',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'heartbeat_message',
                                        'label'       => __( 'Message On Heartbeat', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'heartbeat_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
}
if (get_option('monitorchat_wp_core_update_check_enabled')=='on'){

  $next_core=monitorchat_next_scheduled_hook('monitorchat_wp_core_update_check');
  $description=sprintf(__( 'Receive instant messages informing you whether Wordpress Core software is up to date.<br>The next Monitor.chat Wordpress core update check is in %1$s.', 'monitorchat' ),$next_core);
  if(!$ready){$description = __( 'Receive instant messages informing you whether Wordpress Core software is up to date.', 'monitorchat' );}

                $settings['wp_core_update_check'] = array(
                        'title'       => __( 'Core', 'monitorchat' ),
                        'description' => $description,
                        'shortcodes' => __( 'EMOJI,core' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'wp_core_update_check_interval',
                                        'label'       => __( 'Hours between checks for updates', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '12',
                                        'min'         => '1',
                                        'max'         => '24',
                                        'units'       => 'Hours',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'wp_core_update_check_message',
                                        'label'       => __( 'Message when WP Core is not up to date', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'wp_core_update_check_alt_message',
                                        'label'       => __( 'Message when WP Core is up to date', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),

                                array(
                                        'id'          => 'wp_core_update_check_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
}
if (get_option('monitorchat_plugins_update_check_enabled')=='on'){

  $next_plugins=monitorchat_next_scheduled_hook('monitorchat_plugins_update_check');
  $description = sprintf(__( 'Receive instant messages informing you whether your plugins are up to date.<br>The next Monitor.chat plugins update check is  in %1$s.', 'monitorchat' ),$next_plugins);
  if(!$ready){$description = __( 'Receive instant messages informing you whether your plugins are up to date.', 'monitorchat' );}

                $settings['plugins_update_check'] = array(
                        'title'       => __( 'Plugins', 'monitorchat' ),
                        'description' =>  $description,
                        'shortcodes'  => __( 'EMOJI,plugins' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'plugins_update_check_interval',
                                        'label'       => __( 'Hours between checks for updates', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '12',
                                        'min'         => '1',
                                        'max'         => '24',
                                        'units'       => 'hours',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'plugins_update_check_message',
                                        'label'       => __( 'Message when plugins are not up to date', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'plugins_update_check_alt_message',
                                        'label'       => __( 'Message when plugins are up to date', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'plugins_update_check_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
			),
                );
}

if (get_option('monitorchat_themes_update_check_enabled')=='on'){

  $next_themes=monitorchat_next_scheduled_hook('monitorchat_themes_update_check');
  $description = sprintf(__( 'Receive instant messages informing you whether your themes are up to date.<br>The next Monitor.chat themes update check is  in %1$s.', 'monitorchat' ),$next_themes);
  if(!$ready){$description = __( 'Receive instant messages informing you whether your themes are up to date.', 'monitorchat' );}

                $settings['themes_update_check'] = array(
                        'title'       => __( 'Themes', 'monitorchat' ),
                        'description' => $description,
                        'shortcodes'  => __( 'EMOJI,themes' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'themes_update_check_interval',
                                        'label'       => __( 'Hours between checks for updates', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '12',
                                        'min'         => '1',
                                        'max'         => '24',
                                        'units'       => 'hours',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'themes_update_check_message',
                                        'label'       => __( 'Message when themes are not up to date', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'themes_update_check_alt_message',
                                        'label'       => __( 'Message when themes are up to date', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'themes_update_check_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
			),
                );
}
if((monitorchat_shell_exec_enabled())&&(strtoupper(substr(PHP_OS,0,3))!='WIN')){
  if(get_option('monitorchat_file_system_free_space_enabled')=='on'){

  $next_space=monitorchat_next_scheduled_hook('monitorchat_file_system_free_space');
  $description = sprintf(__( 'Receive instant messages informing you of available space on the file system of your Wordpress server.<br>The next Monitor.chat file system free space check is  in %1$s.', 'monitorchat' ),$next_space);
  if(!$ready){$description = __( 'Receive instant messages informing you of available space on the file system of your Wordpress server.', 'monitorchat' );}

                $settings['files'] = array(
                        'title'       => __( 'Space', 'monitorchat' ),
                        'description' => $description,
                        'shortcodes' => __( 'EMOJI,filesystemreport' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'file_system_free_space_interval',
                                        'label'       => __( 'Hours between checks for free space', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '24',
                                        'min'         => '1',
                                        'max'         => '48',
                                        'units'       => 'Hours',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'file_system_free_space_message',
                                        'label'       => __( 'Message on check for free space', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'file_system_free_space_pct',
                                        'label'       => __( 'Used Space Threshold', 'monitorchat' ),
                                        'description' => __( 'Report on mountpoints that have at least this much used space.', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => array(
                                                '0'      => '0% used',
                                                '10'     => '10% used',
                                                '30'     => '30% used',
                                                '50'     => '50% used',
                                                '70'     => '70% used',
                                                '90'     => '90% used',
                                        ),
                                        'default'     => '50',
                                ),
                                array(
                                        'id'          => 'file_system_free_space_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
  }
  if(get_option('monitorchat_memory_enabled')=='on'){

  $next_memory=monitorchat_next_scheduled_hook('monitorchat_memory');
  $description = sprintf(__( 'Receive instant messages informing you of memory usage on your Wordpress server.<br>The next Monitor.chat memory report is in %1$s.', 'monitorchat' ),$next_memory);
  if(!$ready){$description = __( 'Receive instant messages informing you of memory usage on your Wordpress server.', 'monitorchat' );}

                $settings['memory'] = array(
                        'title'       => __( 'Memory', 'monitorchat' ),
                        'description' => $description,
                        'shortcodes' => __( 'EMOJI,memoryreport' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'memory_interval',
                                        'label'       => __( 'Hours between checks', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '24',
                                        'min'         => '1',
                                        'max'         => '48',
                                        'units'       => 'Hours',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'memory_message',
                                        'label'       => __( 'Message on check for free space', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'memory_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
  }
}
if ( is_plugin_active('akismet/akismet.php') ) {
  if (get_option('monitorchat_akismet_spam_caught_enabled')=='on'){
                $settings['akismet_spam'] = array(
                        'title'       => __( 'Spam', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when Akismet catches spam.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'akismet_spam_caught_message',
                                        'label'       => __( 'Message On Akismet catching spam', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'akismet_spam_caught_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
  }
}

if ( is_plugin_active('updraftplus/updraftplus.php') ) {
  if (get_option('monitorchat_updraft_backups_enabled')=='on'){
                $settings['updraft_backups'] = array(
                        'title'       => __( 'Updraft', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when Updraft completes a backup of your Wordpress server.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,updraft' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'updraft_backup_message',
                                        'label'       => __( 'Message On Completion of Updraft Backup', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'updraft_backup_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
  }
}

if ( is_plugin_active('gwolle-gb/gwolle-gb.php') ) {
  if (get_option('monitorchat_gwolle_guestbook_enabled')=='on'){
                $settings['gwolle_guestbook'] = array(
                        'title'       => __( 'Guestbook', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when someone leaves a new Gwolle guestbook entry.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,gwolle' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'gwolle_guestbook_message',
                                        'label'       => __( 'Message On Entry in Gwolle Guestbook', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'gwolle_guestbook_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
  }
}

if ( is_plugin_active('woocommerce/woocommerce.php') ) {

  if (get_option('monitorchat_woocommerce_new_product_enabled')=='on'){
                $settings['woocommerce_new_product'] = array(
                        'title'       => __( 'Product', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when a Woocommerce product is published.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,USERNAME,product' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'woocommerce_new_product_message',
                                        'label'       => __( 'Message On Publish of Product', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                        'rows'   => '5',
                                ),
                                array(
                                        'id'          => 'woocommerce_new_product_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                                array(
                                        'id'          => 'woocommerce_new_product_exclude',
                                        'label'       => __( 'Exclude users with role:', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $available_roles,
                                        'default'     => 'none',
                                ),
                        ),
                );
  }

  if (get_option('monitorchat_woocommerce_order_status_change_enabled')=='on'){
                $settings['woocommerce_order_status_changed'] = array(
                        'title'       => __( 'Order', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when a Woocommerce order is created or has a change in its status.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,order' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'woocommerce_order_status_change_message',
                                        'label'       => __( 'Message On Status Change.', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'rows'        => '6',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'woocommerce_order_status_change_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                       array(
                                        'id'          => 'woocommerce_order_status_change_pending',
                                        'label'       => __( 'Pending', 'monitorchat' ),
                                        'description' => __( 'Enable for change to "pending"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),

                       array(
                                        'id'          => 'woocommerce_order_status_change_processing',
                                        'label'       => __( 'Processing', 'monitorchat' ),
                                        'description' => __( 'Enable for change to "processing"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                       array(
                                        'id'          => 'woocommerce_order_status_change_on-hold',
                                        'label'       => __( 'On-Hold', 'monitorchat' ),
                                        'description' => __( 'Enable for change to "on-hold"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),

                       array(
                                        'id'          => 'woocommerce_order_status_change_completed',
                                        'label'       => __( 'Completed', 'monitorchat' ),
                                        'description' => __( 'Enable for change to "completed"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                       array(
                                        'id'          => 'woocommerce_order_status_change_cancelled',
                                        'label'       => __( 'Cancelled', 'monitorchat' ),
                                        'description' => __( 'Enable for change to "cancelled"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),

                       array(
                                        'id'          => 'woocommerce_order_status_change_refunded',
                                        'label'       => __( 'Refunded', 'monitorchat' ),
                                        'description' => __( 'Enable for change to "refunded"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),

                        ),
                );
  }

  if (get_option('monitorchat_woocommerce_applied_coupon_enabled')=='on'){
                $settings['woocommerce_coupon'] = array(
                        'title'       => __( 'Coupon', 'monitorchat' ),
                        'description' => __( 'Receive an instant message when Woocommerce coupon is applied to an order.', 'monitorchat' ),
                        'shortcodes' => __( 'EMOJI,coupon' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'woocommerce_applied_coupon_message',
                                        'label'       => __( 'Message On Application of Coupon', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'woocommerce_applied_coupon_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',

                                     ),
                        ),
                );
  }


  if (get_option('monitorchat_woocommerce_orders_summary_enabled')=='on'){

  $next_summary=monitorchat_next_scheduled_hook('monitorchat_woocommerce_orders_summary');
  $description = sprintf(__( 'Receive instant messages informing you of products recently ordered.<br>The next Monitor.chat orders summary is in %1$s.', 'monitorchat' ),$next_summary);
  if(!$ready){$description = __( 'Receive instant messages informing you of products recently ordered.', 'monitorchat' );}

                $settings['woocommerce_orders_summary'] = array(
                        'title'       => __( 'Summary', 'monitorchat' ),
                        'description' => $description,
                        'shortcodes' => __( 'EMOJI,summary' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'woocommerce_orders_summary_interval',
                                        'label'       => __( 'Hours between summaries', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '4',
                                        'min'         => '1',
                                        'max'         => '48',
                                        'units'       => 'Hours',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'woocommerce_orders_summary_message',
                                        'label'       => __( 'Message', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                       array(
                                        'id'          => 'woocommerce_orders_summary_pending',
                                        'label'       => __( 'Pending', 'monitorchat' ),
                                        'description' => __( 'Include orders with status "pending"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),

                       array(
                                        'id'          => 'woocommerce_orders_summary_processing',
                                        'label'       => __( 'Processing', 'monitorchat' ),
                                        'description' => __( 'Include orders with status "processing"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                       array(
                                        'id'          => 'woocommerce_orders_summary_on-hold',
                                        'label'       => __( 'On-Hold', 'monitorchat' ),
                                        'description' => __( 'Include orders with status "on-hold"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),

                       array(
                                        'id'          => 'woocommerce_orders_summary_completed',
                                        'label'       => __( 'Completed', 'monitorchat' ),
                                        'description' => __( 'Include orders with status "completed"', 'monitorchat' ),
                                        'type'        => 'checkbox',
                                        'default'     => '',
                                ),
                               array(
                                        'id'          => 'woocommerce_orders_summary_order_by',
                                        'label'       => __( 'Order By', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => array(
                                                'gross_after_discount'     => 'Gross value after discounts',
                                                'quantity'      => 'Quantity',
                                        ),
                                        'default'     => 'gross_after_discount',
                                ),
                               array(
                                        'id'          => 'woocommerce_orders_summary_limit',
                                        'label'       => __( 'Limit Results to the top', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => array(
                                                '5'   => '5',
                                                '7'   => '7',
                                                '10'  => '10',
                                                '12'  => '12',
                                                '15'  => '15',
                                        ),
                                        'default'     => '10',
                                ),
                                array(
                                        'id'          => 'woocommerce_orders_summary_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',

                                     ),
                        ),
                );
  }
}

if(is_plugin_active('wp-statistics/wp-statistics.php')){

  if(get_option('monitorchat_wp_statistics_visitors_enabled')=='on'){

  $next_visitors=monitorchat_next_scheduled_hook('monitorchat_wp_statistics_visitors');
  $description = sprintf(__( 'Receive an instant messages informing you of visitors to your website.<br>The next Monitor.chat visitors report is in %1$s.', 'monitorchat' ),$next_visitors);
  if(!$ready){$description = __( 'Receive an instant messages informing you of visitors to your website.', 'monitorchat' );}

                $settings['wp_statistics_visitors'] = array(
                        'title'       => __( 'Visitors', 'monitorchat' ),
                        'description' => $description,
                        'shortcodes' => __( 'EMOJI,visitors' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'wp_statistics_visitors_interval',
                                        'label'       => __( 'Minutes between checks', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '30',
                                        'min'         => '10',
                                        'max'         => '360',
                                        'step'        => '10',
                                        'units'       => 'Minutes',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'wp_statistics_visitors_message',
                                        'label'       => __( 'Message on check for free space', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'wp_statistics_visitors_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
  }

  if(get_option('monitorchat_wp_statistics_referrals_enabled')=='on'){

  $next_referrals=monitorchat_next_scheduled_hook('monitorchat_wp_statistics_referrals');
  $description = sprintf(__( 'Receive an instant messages informing you of search engine referrals.<br>The next Monitor.chat referrals report is in %1$s.', 'monitorchat' ),$next_referrals);
  if(!$ready){$description = __( 'Receive an instant messages informing you of search engine referrals.', 'monitorchat' );}

                $settings['wp_statistics_referrals'] = array(
                        'title'       => __( 'Referrals', 'monitorchat' ),
                        'description' => $description,
                        'shortcodes' => __( 'EMOJI,referrals' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'wp_statistics_referrals_interval',
                                        'label'       => __( 'Hours between checks', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '4',
                                        'min'         => '1',
                                        'max'         => '48',
                                        'units'       => 'Hours',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'wp_statistics_referrals_message',
                                        'label'       => __( 'Message on check for free space', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'wp_statistics_referrals_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
  }

  if(get_option('monitorchat_wp_statistics_pages_enabled')=='on'){

  $next_pages=monitorchat_next_scheduled_hook('monitorchat_wp_statistics_pages');
  $description = sprintf(__( 'Receive an instant messages informing you of the top visited pages.<br>The next Monitor.chat top pages report is in %1$s.', 'monitorchat' ),$next_pages);
  if(!$ready){$description = __( 'Receive an instant messages informing you of the top visited pages.', 'monitorchat' );}

                $settings['wp_statistics_pages'] = array(
                        'title'       => __( 'Top Pages', 'monitorchat' ),
                        'description' => $description,
                        'shortcodes' => __( 'EMOJI,toppages' ),
                        'fields'      => array(
                                array(
                                        'id'          => 'wp_statistics_pages_interval',
                                        'label'       => __( 'Hours between checks', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'range',
                                        'default'     => '2',
                                        'min'         => '1',
                                        'max'         => '48',
                                        'units'       => 'Hours',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ), 
                                array(
                                        'id'          => 'wp_statistics_pages_message',
                                        'label'       => __( 'Message on check for free space', 'monitorchat' ),
                                        'description' => __( '', 'monitorchat' ),
                                        'type'        => 'textarea',
                                        'default'     => '',
                                        'placeholder' => __( '', 'monitorchat' ),
                                ),
                                array(
                                        'id'          => 'wp_statistics_pages_recipient',
                                        'label'       => __( 'Send Messages to', 'monitorchat' ),
                                        'description' => __( $what_is_this, 'monitorchat' ),
                                        'type'        => 'select',
                                        'options'     => $all_recipients,
                                        'default'     => 'yourchatroom',
                                ),
                        ),
                );
  }

}
// #######################################

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 *
	 * @return void
	 */
	public function register_settings() {

		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = sanitize_text_field($_GET['tab']);
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						array( $this->parent->admin, 'display_field' ),
						$this->parent->_token . '_settings',
						$section,
						array(
							'field'  => $field,
							'prefix' => $this->base,
						)
					);
				}

				if ( ! $current_section ) {
					break;
				}
			}
		}
	}

	/**
	 * Settings section.
	 *
	 * @param array $section Array of section ids.
	 * @return void
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html; 
	}

public function f_display_send_message_success($exito){
?>
<div data-dismissible="disable-done-notice-forever" class="notice notice-success is-dismissible" style="margin-top:1rem;margin-bottom:0.5rem;"><p><?php echo $exito; ?></p></div>
<?php
}

public function f_display_send_message_error($err){
?>
<div data-dismissible="disable-done-notice-forever" class="notice notice-error is-dismissible" style="margin-top:1rem;margin-bottom:0.5rem;"><p><?php echo $err; ?></p></div>
<?php
}

public function f_send_test_message() {
  if(get_option('monitorchat_global_enabled') != 'on'){
       $this->f_display_send_message_error(__('All messages are currently disabled in the Monitor.chat plugin.')); 
       return;}
  $message=monitorchat_sanitize($_POST['monitorchat_test_message']); 
  $message=monitorchat_format_message_for_user($message,null);
  $recipient=sanitize_text_field($_POST['monitorchat_test_recipient']);
  $send_using=get_option('monitorchat_global_send_using');
  $apikey=get_option('monitorchat_global_api_key');
  $validate=monitorchat_validate_apikey($apikey);
  $script=get_option('monitorchat_global_shell_script');
  if(!in_array($recipient,array('yourchatroom','reco','both','all','test'))){
       $this->f_display_send_message_error(__('Invalid Recipient.')); return;}
  if(strlen($message)=='0'){$this->f_display_send_message_error(__('Invalid Message.')); return;}

  if($send_using=='wppost'){
    if($validate=='INVALID'){$this->f_display_send_message_error(__('Invalid API Key.')); return;}
    if(get_option('monitorchat_global_api_key') != get_option('monitorchat_global_last_valid_apikey')){
      $this->f_display_send_message_error('Invalid API Key.'); return;}
    if(monitorchat_is_enabled('monitorchat_global_enabled')){
        monitorchat_remote_post_message($recipient,$message); 
        $this->f_display_send_message_success(__('Message sent by Wordpress remote post. Check your XMPP client to see if you received the instant message!'));
      }
    return;
  }

  if($send_using=='embed'){
    if($validate=='INVALID'){$this->f_display_send_message_error(__('Invalid API Key.')); return;}
    if(!monitorchat_shell_exec_enabled()){$this->f_display_send_message_error(__('Shell_exec is not enabled.')); return;}
    if(strtoupper(substr(PHP_OS,0,3))=='WIN'){$this->f_display_send_message_error(__('Windows Operating System not supported for embedded bash script. Please try the Wordpress remote post option.')); return;}
      if(get_option('monitorchat_global_api_key') != get_option('monitorchat_global_last_valid_apikey')){
        $this->f_display_send_message_error('Invalid API Key.'); return;}
    if(monitorchat_is_enabled('monitorchat_global_enabled')){
      monitorchat_embed_message($recipient,$message);
      $this->f_display_send_message_success(__('Message sent by embedded bash script. Check your XMPP client to see if you received the instant message!'));
    }
    return;
  }

  if($send_using=='script'){
    if(!monitorchat_shell_exec_enabled()){$this->f_display_send_message_error(__('Shell_exec is not enabled.')); return;}
    if(!file_exists($script)){$this->f_display_send_message_error(__('The shell script '.$script.' does not exist.')); return;}
    if(!is_executable($script)){$this->f_display_send_message_error(__('The shell script '.$script.' is not executable and accessible to the web server application.')); return;}
    if(monitorchat_is_enabled('monitorchat_global_enabled')){
      monitorchat_shell_message($recipient,$message);
      $this->f_display_send_message_success(__('Message sent by shell script. Check your XMPP client to see if you received the instant message!'));
    }
    return;
  }

}
	public function settings_page() {
//EDWARD TEST_MESSAGE
  if(($_GET['tab']=='test_message')||($_POST['tab']=='test_message')){$test_message=true;}else{$test_message=false;}
  if(($_GET['tab']=='apikey')||($_POST['tab']=='apikey')){
    add_thickbox();
//    monitorchat_populate_apikey_report();
  }

    if( $_POST['send_test_message'] === 'true' ){
        $this->f_send_test_message();
    }
		// Build page HTML.
		$html      = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
		$tab = '';
		
		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab .= sanitize_text_field($_GET['tab']);
		}

		// Show page tabs.
		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

			$html .= '<h2 class="nav-tab-wrapper">' . "\n";

			$c = 0;
			foreach ( $this->settings as $section => $data ) {

				// Set tab class.
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) {
					if ( 0 === $c ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
						$class .= ' nav-tab-active';
					}
				}

				// Set tab link.
				$tab_link = add_query_arg( array( 'tab' => $section ) );
				if ( isset( $_GET['settings-updated'] ) ) { 
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				// Output tab.
				$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

				++$c;
			}

			$html .= '</h2>' . "\n";
		}
if(empty($tab)){$tab='about';}

 if($tab != 'about'){
$html .='<div style="display: flex; margin-top:1.5rem;"><a style="margin: 0; font-family: open sans,-apple-system,BlinkMacSystemFont,segoe ui,Roboto,helvetica neue,Arial,sans-serif,apple color emoji,segoe ui emoji,segoe ui symbol; font-weight: 400; text-align: left; background-color: #fff; font-size: 2.5rem!important; white-space: nowrap; color: #ef0c01; text-shadow: 3px 2px 1px rgba(0,0,0,.3); -ms-transform: skew(0,-6.7deg); -webkit-transform: skew(0,-6.7deg); -o-transform: skew(0,-6.7deg); transform: skew(0,-6.7deg); margin-left: 0rem; margin-top: 0.5rem; margin-bottom: 1.5rem; line-height: inherit; text-decoration: none; background-color: transparent; box-sizing: border-box;" href="https://monitor.chat" target="_blank"><span style="text-align: left; line-height: inherit; font-weight: 700!important;box-sizing: border-box; text-transform: none; font-size: 2rem!important; white-space: nowrap; color: #007bff; text-shadow: 2px 1px 1px rgba(0,0,0,.3); -ms-transform: skew(0,-6.7deg); -webkit-transform: skew(0,-6.7deg); -o-transform: skew(0,-6.7deg); transform: skew(0,-6.7deg); font-family: open sans,-apple-system,BlinkMacSystemFont,segoe ui,Roboto,helvetica neue,Arial,sans-serif,apple color emoji,segoe ui emoji,segoe ui symbol;">monitor.chat</span></a></div>'. "\n";

}

    if($tab != 'about'){
     if($test_message){
                        $html .= '<form method="post" enctype="multipart/form-data">' . "\n";
     }else{
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";
     }

if(!monitorchat_is_enabled('monitorchat_global_enabled')){
  $html .=$this->f_display_send_message_error(__('Plugin is disabled or not properly configured to send messages.'));
}

				// Get settings fields.
				ob_start();
				settings_fields( $this->parent->_token . '_settings' ); 
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean(); 

				$html     .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
if($test_message){
                                        $html .= '<input type="hidden" name="send_test_message" value="true" />' . "\n";
                                        $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Send Message', 'monitor
chat' ) ) . '" />' . "\n";
}else{
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings', 'monitorchat' ) ) . '" />' . "\n";

}
				$html     .= '</p>' . "\n";
			$html         .= '</form>' . "\n";
		$html             .= '</div>' . "\n";
}
		echo $html;

monitorchat_shortcodes_table($this->settings[$tab]['title'],$this->settings[$tab]['shortcodes']);
 



if($tab == 'about'){
   require (MONITORCHAT__PLUGIN_DIR . 'about.php');
}

echo '<p style="margin-top: 0.5rem;">Monitor.chat plugin version: '.MONITORCHAT_VERSION.'<p>';	
}
	/**
	 * Main monitorchat_Settings Instance
	 *
	 * Ensures only one instance of monitorchat_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see monitorchat()
	 * @param object $parent Object instance.
	 * @return object monitorchat_Settings instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of monitorchat_API is forbidden.' ) ), esc_attr( $this->parent->_version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of monitorchat_API is forbidden.' ) ), esc_attr( $this->parent->_version ) );
	} // End __wakeup()

}
