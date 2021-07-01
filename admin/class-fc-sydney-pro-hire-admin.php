<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.forumcube.com/
 * @since      1.0.0
 *
 * @package    Fc_Sydney_Pro_Hire
 * @subpackage Fc_Sydney_Pro_Hire/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fc_Sydney_Pro_Hire
 * @subpackage Fc_Sydney_Pro_Hire/admin
 * @author     ForumCube <ammad@karigar.pk>
 */
class Fc_Sydney_Pro_Hire_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		add_filter( 'woocommerce_email_classes', array($this,'add_sydney_order_woocommerce_email') );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fc_Sydney_Pro_Hire_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fc_Sydney_Pro_Hire_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fc-sydney-pro-hire-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fc_Sydney_Pro_Hire_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fc_Sydney_Pro_Hire_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fc-sydney-pro-hire-admin.js', array( 'jquery' ), $this->version, false );

	}
	function add_sydney_order_woocommerce_email( $email_classes ) {

		// include our custom email class
		require_once plugin_dir_path( __FILE__ ) . 'emails/fc-sydney-pro-hire-new-order-email.php';
	
		// add the email class to the list of email classes that WooCommerce loads
		$email_classes['WC_Sydney_Order_Email'] = new WC_Sydney_Order_Email();
	
		return $email_classes;
	
	}
	
}
