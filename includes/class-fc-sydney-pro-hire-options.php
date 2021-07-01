<?php


/**
 *  Class for creating meta boxes using CodeStart Framework
 *
 * @since      0.9.0
 * @package    Fc_sydney-pro-hire_Options
 * @subpackage Fc_sydney-pro-hire_Options/includes
 * @author     ForumCube <ForumCube>
 */
class Fc_Sydney_Pro_Hire_Options {
	private $prefix      = 'fc-sydney-pro-hire-options';
	protected $post_type = 'product';
	private $mem_prefix  = 'fc-sydney-pro-hire-options-m';

	public function __construct( ) {
        
        include_once WP_PLUGIN_DIR .'/woocommerce/woocommerce.php';
		
		$this->admin_init();
		$this->shop_order_admin_init();
        add_action( 'init', array($this,'fc_custom_status'), 20 );
       
        add_filter( 'wc_order_statuses',array($this,'get_fc_custom_status_for_editorder') , 20, 1 );
        add_filter( 'bulk_actions-edit-shop_order',array($this,'get_fc_custom_status_for_orderlist') , 20, 1 );

        add_filter( 'woocommerce_email_actions',array($this,'fc_custom_status_action') , 20, 1 );

        // add_action( 'woocommerce_order_status_wc-quote-request', array( WC(), 'send_transactional_email' ), 10, 1 );
        // add_action('woocommerce_order_status_quote-request', array($this,'fc_order_status_custom_notification'), 20, 2);

        //Trigger on when order is created and show it on thankyou page
        add_action('woocommerce_thankyou', array($this, 'update_order'), 10, 1);
        //Trigger on when order is created
        add_action('woocommerce_review_order_after_submit', array($this, 'update_order'), 10, 1);
        // add_filter( 'wc_order_statuses', array($this,'fc_remove_woo_status') );
        // add_filter( 'bulk_actions-edit-shop_order', array($this,'fc_removebulk_woo_status'),999 );

        add_action('save_post_shop_order', array($this, 'update_on_order'),999);
        add_action( 'woocommerce_order_actions', array($this, 'fc_add_order_meta_box_action') );
        add_action( 'woocommerce_order_action_wc_deposit_inv_action',array($this, 'fc_process_order_wc_deposit_inv_action')   );
        add_action( 'woocommerce_order_action_wc_balance_inv_action',array($this, 'fc_process_order_wc_balance_inv_action')   );
        add_action( 'woocommerce_order_action_wc_custom_inv_action',array($this, 'fc_process_order_wc_custom_inv_action')   );
       // add_action('woocommerce_order_status_changed', array($this, 'after_update_on_order'),999);
        
	}
    /**
     * Method to Update Woo Order status
     *
     * @return void
     */
    public function update_order($order_id)
    {


        if (!$order_id) {
            return;
        }
        if (is_checkout() || !empty(is_wc_endpoint_url('order-received')) || !empty($order_id)) {
            $order = wc_get_order($order_id);


            

            $order->update_status('quote-request');

            $order->save();
        }
    }
    /**
     * Method to Update Woo Order Fees
     *
     * @return void
     */
    public function update_on_order($order_id)
    {


        if (!$order_id) {
            return;
        }
        if (is_admin() && !empty($order_id) && !empty($_POST['fc-sydney-pro-hire-options-shop-order-page']['fc-deposit'])) {
            echo '<pre>';
            
          
            $order = wc_get_order($order_id);
            $fee = new WC_Order_Item_Fee();
            foreach( $order->get_items('fee') as $item_id => $item_fee ){
               
                // The fee name
              echo  $fee_name = $item_fee->get_name();
                if($fee_name =='Deposit '.$_POST['fc-sydney-pro-hire-options-shop-order-page']['fc-deposit'].'%'){

                   
                    return;
                }
               
            }
          

            $fee->set_name('Deposit '.$_POST['fc-sydney-pro-hire-options-shop-order-page']['fc-deposit'].'%');
          
            
            
            $amount = $order->get_total() /100 * $_POST['fc-sydney-pro-hire-options-shop-order-page']['fc-deposit'];
            //Set the Fee
            $fee->set_total(-$amount);
          
            //Add to the Order
            $order->add_item($fee);
          
            //Recalculate the totals. IMPORTANT!
            $order->calculate_totals();
            
            $order->save();

          

            
        }
      
        
    }
    function fc_add_order_meta_box_action( $actions ) {
        global $theorder;
    
        // bail if the order has been paid for or this action has been run
        // if ( ! $theorder->is_paid() || get_post_meta( $theorder->id, '_wc_order_marked_printed_for_packaging', true ) ) {
        //     return $actions;
        // }
    
        // add "mark printed" custom action
        $actions['wc_deposit_inv_action'] = __( '10% Deposit invoice', 'my-textdomain' );
        $actions['wc_balance_inv_action'] = __( 'Balance Invoice', 'my-textdomain' );
        $actions['wc_custom_inv_action'] = __( 'Custom Value Invoice', 'my-textdomain' );
        return $actions;
    }
    function fc_process_order_wc_deposit_inv_action( $order ) {
    
        // add the order note
        // translators: Placeholders: %s is a user's display name
       echo $message = __( '10% Deposit invoice information printed .', 'my-textdomain' );
        $order->add_order_note( $message );
        
        // add the flag
        $this->after_update_on_order($order->id);
        
    }
    function fc_process_order_wc_balance_inv_action( $order ) {
    
        // add the order note
        // translators: Placeholders: %s is a user's display name
       echo $message = __( 'Balance invoice information printed .', 'my-textdomain' );
        $order->add_order_note( $message );
        
        // add the flag
        $this->after_update_on_order($order->id);
        
    }
    function fc_process_order_wc_custom_inv_action( $order ) {
    
        // add the order note
        // translators: Placeholders: %s is a user's display name
       echo $message = __( 'Custom Value invoice information printed .', 'my-textdomain' );
        $order->add_order_note( $message );
        
        // add the flag
        $this->after_update_on_order($order->id);
        
    }
    public function after_update_on_order($order_id)
    {


        if (!$order_id) {
            return;
        }
        if (is_admin() && !empty($order_id)) {
            $order = wc_get_order($order_id);


            

            $order->update_status('quote-sent');

            $order->save();
        }
       
    }
	/**
	 * Method to create admin options
	 *
	 * @return void
	 */
	private function admin_init(){

		// Create options
		CSF::createOptions(
			'fc-sydney-pro-hire-options-admin-page',
			array(
				'menu_title'         => __( 'FC Sydney Pro Hire Settings', 'fc-sydney-pro-hire-options' ),
				'menu_slug'          => 'fc-sydney-pro-hire-options',
				'framework_title'    => __( 'Settings', 'fc-sydney-pro-hire-options' ),
				'menu_position'      => 15,
				'show_search'        => false,
				'show_search'        => false,
				'show_reset_all'     => false,
				'show_reset_section' => false,
				'ajax_save'          => false,
			)
		);

		// Create a section
		CSF::createSection(
			'fc-sydney-pro-hire-options-admin-page',
			array(
				'title'  => __( 'FC Custom Status', 'fc-sydney-pro-hire-options' ),
				'fields' => array(

					array(
                        'id'     => 'fc-cos',
                        'type'   => 'repeater',
                        'title'  => 'Custom Status',
                        'fields' => array(
                      
                          array(
                            'id'    => 'fc-status',
                            'type'  => 'text',
                            'title' => 'Enter Status'
                          ),
                          array(
                            'id'      => 'fc-checkbox',
                            'type'    => 'checkbox',
                            'title'   => 'Is this status for Completing the Order',
                            'label'   => '',
                            'default' => false // or false
                          ),
                        ),
                      ),
				
				),
			)
		);
		CSF::createMetabox( 'fc-sydney-pro-hire-options-admin-page', array(
            'title'     => 'FC Sydney Pro Hire Options',
            'post_type' => 'shop_order',
          ) );
          CSF::createSection( $prefix, array(
            'title'  => 'Options',
            'fields' => array(
        
              //
              // A text field
              array(
                'id'    => 'fc-deposit',
                'type'  => 'text',
                'title' => 'Deposit %',
              ),
        
            )
          ) );
	}
    
	/**
	 * Method to create shop order admin options
	 *
	 * @return void
	 */
	private function shop_order_admin_init(){

	
		CSF::createMetabox( 'fc-sydney-pro-hire-options-shop-order-page', array(
            'title'     => 'FC Sydney Pro Hire Options',
            'post_type' => 'shop_order',
          ) );
          CSF::createSection('fc-sydney-pro-hire-options-shop-order-page', array(
            'title'  => 'Options',
            'fields' => array(
        
              //
              // A text field
              array(
                'id'    => 'fc-deposit',
                'type'  => 'text',
                'title' => 'Deposit %',
              ),
        
            )
          ) );
	}
    
    public function fc_custom_status(){
        $fc_sydney_prohire = get_option('fc-sydney-pro-hire-options-admin-page');
       
        // sanitize_title($status['fc-status']  );
        foreach($fc_sydney_prohire['fc-cos'] as $status){
            
        register_post_status( 'wc-'.sanitize_title($status['fc-status']  ), array(
            'label'                     => _x( $status['fc-status'], 'Order status', 'woocommerce' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( $status['fc-status'].' <span class="count">(%s)</span>', $status['fc-status'].' <span class="count">(%s)</span>', 'woocommerce' )
        ) );
        }
    }
	
    public function get_fc_custom_status_for_editorder( $order_statuses ){
        $fc_sydney_prohire = get_option('fc-sydney-pro-hire-options-admin-page');
        
        // sanitize_title($status['fc-status']  );
        foreach($fc_sydney_prohire['fc-cos'] as $status){
        
        $status_key = 'wc-'.sanitize_title($status['fc-status']);
        $order_statuses[$status_key] = _x( $status['fc-status'], 'Order status', 'woocommerce' );
       
        }
        return $order_statuses;
    }
    
    public function get_fc_custom_status_for_orderlist( $actions ){
        $fc_sydney_prohire = get_option('fc-sydney-pro-hire-options-admin-page');
        
        // sanitize_title($status['fc-status']  );
        foreach($fc_sydney_prohire['fc-cos'] as $status){
        
        $status_key = 'mark_'.sanitize_title($status['fc-status']);
        $actions[$status_key] = __( 'Change Status to '.$status['fc-status'],  'woocommerce' );
       
        }
        return $actions;
    }
    
    public function fc_custom_status_action($action){
        $fc_sydney_prohire = get_option('fc-sydney-pro-hire-options-admin-page');
       
        // sanitize_title($status['fc-status']  );
        foreach($fc_sydney_prohire['fc-cos'] as $status){
            
            $actions[] = 'woocommerce_order_status_'.'wc-'.sanitize_title($status['fc-status']);
        
        }
        return $actions;
    }
    function fc_order_status_custom_notification( $order_id, $order ) {
        // HERE below your settings
        // $heading   = __('Your Awaiting delivery order','woocommerce');
        // $subject   = '[{site_title}] Awaiting delivery order ({order_number}) - {order_date}';
    
        // Getting all WC_emails objects
        $mailer = WC()->mailer()->get_emails();
        
        // Customizing Heading and subject In the WC_email processing Order object
        // $mailer['WC_Sydney_Order_Email']->heading = $heading;
        // $mailer['WC_Sydney_Order_Email']->subject = $subject;
    
        // Sending the customized email
        $mailer['WC_Sydney_Order_Email']->trigger( $order_id );
    }
    
    function fc_remove_woo_status( $statuses ){
        if( isset( $statuses['wc-processing'] ) ){
            unset( $statuses['wc-processing'] );
        }
        
        return $statuses;
    }
    function fc_removebulk_woo_status( $actions  ){
        if( isset( $actions['mark_on-hold'] ) ){
            unset( $actions['mark_on-hold'] );
        }
        if( isset( $actions['mark_processing'] ) ){
            unset( $actions['mark_processing'] );
        }
        
        return $actions ;
    }

}
new Fc_Sydney_Pro_Hire_Options();