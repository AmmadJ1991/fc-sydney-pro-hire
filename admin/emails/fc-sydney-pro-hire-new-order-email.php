<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A custom Sydney Order WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Sydney_Order_Email extends WC_Email {


	/**
	 * Set email defaults
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// set ID, this simply needs to be a unique name
		$this->id = 'wc_sydney_order';

		// this is the title in WooCommerce Email settings
		$this->title = 'Your Sydney Prop Hire Quote';

		// this is the description in WooCommerce email settings
		$this->description = 'Sydney Order Notification emails are sent when a customer places an order';

		// these are the default heading and subject lines that can be overridden using the settings
		$this->heading = 'Your Sydney Prop Hire Quote';
		$this->subject = 'Your Sydney Prop Hire Quote';

		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->template_html  = 'emails/admin-new-order.php';
		$this->template_plain = 'emails/plain/admin-new-order.php';

		// Trigger on new paid orders
		add_action( 'woocommerce_order_status_quote-sent', array( $this, 'trigger' ), 10, 2 );
		

		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();

		// this sets the recipient to the settings defined below in init_form_fields()
		$this->recipient = $this->get_option( 'recipient' );

		// if none was entered, just use the WP admin email as a fallback
		if ( ! $this->recipient )
			$this->recipient = get_option( 'admin_email' );
	}


	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @since 0.1
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {

		// bail if no order ID is present
		if ( ! $order_id )
			return;

		// setup order object
		$this->object = new WC_Order( $order_id );

		

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

		// woohoo, send the email!
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}



	public function get_content_html() {
		return wc_get_template_html(
			$this->template_html,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => true,
				'plain_text'         => false,
				'email'              => $this,
			)
		);
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_plain,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => true,
				'plain_text'         => true,
				'email'              => $this,
			)
		);
	}

	/**
	 * Initialize Settings Form Fields
	 *
	 * @since 2.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'    => array(
				'title'   => 'Enable/Disable',
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes'
			),
			'recipient'  => array(
				'title'       => 'Recipient(s)',
				'type'        => 'text',
				'description' => sprintf( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => ''
			),
			'subject'    => array(
				'title'       => 'Subject',
				'type'        => 'text',
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => 'Email Heading',
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => 'Email type',
				'type'        => 'select',
				'description' => 'Choose which format of email to send.',
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'	    => __( 'Plain text', 'woocommerce' ),
					'html' 	    => __( 'HTML', 'woocommerce' ),
					'multipart' => __( 'Multipart', 'woocommerce' ),
				)
			)
		);
	}


} // end \WC_Sydney_Order_Email class
