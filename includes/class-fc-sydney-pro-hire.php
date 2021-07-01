<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.forumcube.com/
 * @since      1.0.0
 *
 * @package    Fc_Sydney_Pro_Hire
 * @subpackage Fc_Sydney_Pro_Hire/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Fc_Sydney_Pro_Hire
 * @subpackage Fc_Sydney_Pro_Hire/includes
 * @author     ForumCube <ammad@karigar.pk>
 */
class Fc_Sydney_Pro_Hire {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Fc_Sydney_Pro_Hire_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'FC_SYDNEY_PRO_HIRE_VERSION' ) ) {
			$this->version = FC_SYDNEY_PRO_HIRE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'fc-sydney-pro-hire';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		add_action( 'woocommerce_email', array($this,'fc_unhook_emails') );
		//add_action( 'init', array( $this, 'load' ), 999 );
		//add_action( 'admin_init', array( $this, 'get_email_template' ), 20 );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Fc_Sydney_Pro_Hire_Loader. Orchestrates the hooks of the plugin.
	 * - Fc_Sydney_Pro_Hire_i18n. Defines internationalization functionality.
	 * - Fc_Sydney_Pro_Hire_Admin. Defines all hooks for the admin area.
	 * - Fc_Sydney_Pro_Hire_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fc-sydney-pro-hire-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fc-sydney-pro-hire-i18n.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fc-sydney-pro-hire-options.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fc-sydney-pro-hire-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fc-sydney-pro-hire-public.php';

		$this->loader = new Fc_Sydney_Pro_Hire_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Fc_Sydney_Pro_Hire_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Fc_Sydney_Pro_Hire_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Fc_Sydney_Pro_Hire_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Fc_Sydney_Pro_Hire_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Fc_Sydney_Pro_Hire_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	function fc_unhook_emails( $email_class ) {

		
	
		// New order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	
		// Processing order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
	
		// Completed order emails
		remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
	
		// Note emails
		remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
	}
	public function load() {

		$page = filter_input( INPUT_GET, 'page' );

		if ( class_exists( 'WC_Emails' ) ) {

			$wc_emails = WC_Emails::instance();
			$emails    = $wc_emails->get_emails();
			if ( ! empty( $emails ) ) {
				//Filtering out booking emails becuase it won't work from this plugin
				//Buy PRO version if you need this capability
				$unset_booking_emails = array(
					'WC_Email_New_Booking',
					'WC_Email_Booking_Reminder',
					'WC_Email_Booking_Confirmed',
					'WC_Email_Booking_Notification',
					'WC_Email_Booking_Cancelled',
					'WC_Email_Admin_Booking_Cancelled',
					'WC_Email_Booking_Pending_Confirmation'
				);

				//Filtering out subscription emails becuase it won't work from this plugin
				//Buy PRO version if you need this capability
				$unset_subscription_emails = array(
					'WCS_Email_New_Renewal_Order',
					'WCS_Email_New_Switch_Order',
					'WCS_Email_Processing_Renewal_Order',
					'WCS_Email_Completed_Renewal_Order',
					'WCS_Email_Completed_Switch_Order',
					'WCS_Email_Customer_Renewal_Invoice',
					'WCS_Email_Cancelled_Subscription',
					'WCS_Email_Expired_Subscription',
					'WCS_Email_On_Hold_Subscription'
				);

				//Filtering out membership emails becuase it won't work from this plugin
				//Buy PRO version if you need this capability
				$unset_membership_emails = array(
					'WC_Memberships_User_Membership_Note_Email',
					'WC_Memberships_User_Membership_Ending_Soon_Email',
					'WC_Memberships_User_Membership_Ended_Email',
					'WC_Memberships_User_Membership_Renewal_Reminder_Email',
				);

				$unset_booking_emails      = apply_filters( 'woo_preview_emails_unset_booking_emails', $unset_booking_emails );
				$unset_subscription_emails = apply_filters( 'woo_preview_emails_unset_subscription_emails', $unset_subscription_emails );
				$unset_membership_emails   = apply_filters( 'woo_preview_emails_unset_memebership_emails', $unset_membership_emails );

				if ( ! empty( $unset_booking_emails ) ) {
					foreach ( $unset_booking_emails as $unset_booking_email ) {
						if ( isset( $emails[ $unset_booking_email ] ) ) {
							unset( $emails[ $unset_booking_email ] );
						}
					}
				}

				if ( ! empty( $unset_subscription_emails ) ) {
					foreach ( $unset_subscription_emails as $unset_subscription_email ) {
						if ( isset( $emails[ $unset_subscription_email ] ) ) {
							unset( $emails[ $unset_subscription_email ] );
						}
					}
				}

				if ( ! empty( $unset_membership_emails ) ) {
					foreach ( $unset_membership_emails as $unset_membership_email ) {
						if ( isset( $emails[ $unset_membership_email ] ) ) {
							unset( $emails[ $unset_membership_email ] );
						}
					}
				}

				$this->emails = $emails;
			}
		}

	}

	function get_email_template(){
		$_POST['orderID'] =  14;
	$_POST['choose_email'] = 'WC_Sydney_Order_Email';
	$_POST['email'] = 'ammad@karigar.pk';
	/*Make Sure serached order is selected */
	$orderID         = absint( ! empty( $_POST['search_order'] ) ? $_POST['search_order'] : $_POST['orderID'] );
	$index           = esc_attr( $_POST['choose_email'] );
	$recipient_email = $_POST['email'];

	if ( is_email( $recipient_email ) ) {
		$this->recipient = $_POST['email'];
	} else {
		$this->recipient = '';
	}

	$current_email = $this->emails[ $index ];
	/*The Woo Way to Do Things Need Exception Handling Edge Cases*/
	add_filter( 'woocommerce_email_recipient_' . $current_email->id, array( $this, 'no_recipient' ) );

	$additional_data = apply_filters( 'woo_preview_additional_orderID', false, $index, $orderID, $current_email );
	if ( $additional_data ) {
		do_action( 'woo_preview_additional_order_trigger', $current_email, $additional_data );
	} else {
		if ( $index === 'WC_Email_Customer_Note' ) {
			/* customer note needs to be added*/
			$customer_note = 'This is some customer note , just some dummy text nothing to see here';
			$args          = array(
				'order_id'      => $orderID,
				'customer_note' => $customer_note
			);
			$current_email->trigger( $args );

		} else if ( $index === 'WC_Email_Customer_New_Account' ) {
			$user_id = get_current_user_id();
			$current_email->trigger( $user_id );
		} else if ( strpos( $index, 'WCS_Email' ) === 0 && class_exists( 'WC_Subscription' ) && is_subclass_of( $current_email, 'WC_Email' ) ) {
			/* Get the subscriptions for the selected order */
			$order_subscriptions = wcs_get_subscriptions_for_order( $orderID );
			if ( ! empty( $order_subscriptions ) && $current_email->id != 'customer_payment_retry' && $current_email->id != 'payment_retry' ) {
				/* Pick the first one as an example */
				$subscription = array_pop( $order_subscriptions );
				$current_email->trigger( $subscription );

			} else {
				$current_email->trigger( $orderID, wc_get_order( $orderID ) );
			}
		} else {
			$current_email->trigger( $orderID );
		}
	}

	$content = $current_email->get_content_html();
	$content = apply_filters( 'woocommerce_mail_content', $current_email->style_inline( $content ) );
	echo $content;
	/*This ends the content for email to be previewed*/
	/*Loading Toolbar to display for multiple email templates*/

	/*The Woo Way to Do Things Need Exception Handling Edge Cases*/
	remove_filter( 'woocommerce_email_recipient_' . $current_email->id, array( $this, 'no_recipient' ) );
	


	}
}
