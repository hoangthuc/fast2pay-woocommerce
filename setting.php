<?php
 class WC_Gateway_Fast2Pay extends WC_Payment_Gateway {

    public $domain;

    /**
     * Constructor for the gateway.
     */
    public function __construct() {

        $this->domain = 'fast2pay_payment';

        $this->id                 = 'fast2pay';
        $this->icon               = apply_filters('woocommerce_fast2pay_gateway_icon', '');
        $this->has_fields         = false;
        $this->method_title       = __( 'Fast2Pay', $this->domain );
        $this->method_description = __( 'Allows payments with fast2pay gateway.', $this->domain );

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->title        = $this->get_option( 'title' );
        $this->app_id  = $this->get_option( 'app_id' );
        $this->app_secret = $this->get_option( 'app_secret' );
        $this->callback_uri = $this->get_option( 'callback_uri' );
        $this->channel_id = $this->get_option( 'channel_id' );
        $this->fast2pay_webhook = $this->get_option('fast2pay_webhook');
        $this->currency_text = $this->get_option('currency_text');
        $this->fast2pay_currency_vnd = $this->get_option('fast2pay_currency_vnd');
        $this->fast2pay_sanbox = $this->get_option('fast2pay_sanbox');
        $this->description = $this->get_option('description');
        $this->order_status = $this->get_option( 'order_status', 'completed' );

        // Actions
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );

        // Customer Emails
        add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title'   => __( 'Enable/Disable', $this->domain ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable Fast2Pay Payment', $this->domain ),
                'default' => 'yes'
            ),
            'title' => array(
                'title'       => __( 'Title', $this->domain ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', $this->domain ),
                'default'     => __( 'Fast2Pay Payment', $this->domain ),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __( 'Description', $this->domain ),
                'type'        => 'textarea',
                'description' => __( 'Description Information.', $this->domain ),
                'default'     => __( 'Information', $this->domain ),
                'desc_tip'    => true,
            ),
            'order_status' => array(
                'title'       => __( 'Order Status', $this->domain ),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __( 'Choose whether status you wish after checkout.', $this->domain ),
                'default'     => 'wc-completed',
                'desc_tip'    => true,
                'options'     => wc_get_order_statuses()
            ),
            'app_id' => array(
                'title'       => __( 'App ID', $this->domain ),
                'type'        => 'text',
                'description' => __( 'Enter App ID.', $this->domain ),
                'placeholder'     => __('Payment Information', $this->domain),
                'desc_tip'    => true,
            ),
            'app_secret' => array(
                'title'       => __( 'App Secret', $this->domain ),
                'type'        => 'text',
                'description' => __( 'Enter App Secret.', $this->domain ),
                'placeholder'     => __('Payment Information', $this->domain),
                'desc_tip'    => true,
            ),
            'channel_id' => array(
                'title'       => __( 'Channel ID', $this->domain ),
                'type'        => 'text',
                'description' => __( 'Enter Channel ID.', $this->domain ),
                'placeholder'     => __('Channel Information', $this->domain),
                'desc_tip'    => true,
            ),
            'callback_uri' => array(
                'title'       => __( 'Callback URL', $this->domain ),
                'type'        => 'text',
                'description' => __( 'Enter Callback URL.', $this->domain ),
                'placeholder'     => __('URL', $this->domain),
                'desc_tip'    => true,
            ),
            'fast2pay_webhook' => array(
                'title'       => __( 'Webhook slug', $this->domain ),
                'type'        => 'text',
                'description' => __( 'Enter Callback URL.', $this->domain ),
                'placeholder'     => __('fast2pay_webhook', $this->domain),
                'desc_tip'    => true,
            ),
            'currency_text' => array(
                'title'       => __( 'Currency symbol', $this->domain ),
                'type'        => 'text',
                'description' => __( 'Enter symbol', $this->domain ),
                'placeholder'     => __('USD', $this->domain),
                'desc_tip'    => true,
            ),
            'fast2pay_currency_vnd' => array(
                'title'       => __( 'Exchange rate of VietNam', $this->domain ),
                'type'        => 'text',
                'description' => __( 'Enter number VND', $this->domain ),
                'placeholder'     => __('23000', $this->domain),
                'desc_tip'    => true,
            ),
            'fast2pay_sanbox' => array(
                'title'   => __( 'Sanbox Payment', $this->domain ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable sanbox Payment', $this->domain ),
                'default' => 'yes'
            ),

        );
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page() {
        // Remove cart
        WC()->cart->empty_cart();
    }

    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     */
    public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
        // if ( $this->instructions && ! $sent_to_admin && 'custom' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
        //     echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
        // }
    }

    public function payment_fields(){
        
        include_once('templates/payment_frontend.php');
    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id
     * @return array
     */
    public function process_payment( $order_id ) {

        $order = wc_get_order( $order_id );
        $status = 'wc-' === substr( $this->order_status, 0, 3 ) ? substr( $this->order_status, 3 ) : $this->order_status;
        $fee_percent = 0;
        $account_name = "Fast2Pay";
        $transaction_id =  uniqid();
        $currency_amount = floatval( preg_replace( '#[^\d.]#', '', WC()->cart->total ) );
        $currency_amount = $currency_amount / (1 - $fee_percent);
        $fast2pay = WC()->payment_gateways()->payment_gateways()['fast2pay'];
        $fast2pay_currency_vnd = $fast2pay->fast2pay_currency_vnd;
        $currency_text = $fast2pay->currency_text;
        $amount = $currency_amount*$fast2pay_currency_vnd;

        $currency_amount = ($currency_text=='VND')?'':'&currency_amount='.$currency_amount;

        $payment_url = wp_sprintf( __('https://bank.fast2pays.com/v1/partner/getVirtualAccount?app_id=%s&channel_id=%s&user_id=%s&transaction_id=%s&amount=%d&bank=%s&account_name=%s&hash=%s&callback_uri=%s&currency_text=%s'.$currency_amount) ,
        $fast2pay->app_id, 
        $fast2pay->channel_id,
       1,
        $transaction_id,
        $amount,
        $_POST['bank'],
        $account_name,
        sha1($fast2pay->app_id.":".$fast2pay->channel_id.":".$transaction_id.":".$amount.":".$_POST['bank']."::".$account_name.":".$fast2pay->app_secret),
        $this->get_return_url( $order ),
        $currency_text
       ); 


        if($_POST['payment_type'] == 'getATMCardPaymentURL')
        $payment_url = wp_sprintf( __('https://bank.fast2pays.com/v1/partner/getATMCardPaymentURL?transaction_id=%s&amount=%s&hash=%s&bank=%s&app_id=%s&channel_id=%s&callback_uri=%s&currency_text=%s'.$currency_amount) ,
        $transaction_id, 
        $amount, 
        sha1($fast2pay->app_id.":".$fast2pay->channel_id.":".$transaction_id.":".$this->get_return_url( $order ).":".$amount.":".$_POST['bank']."::".$fast2pay->app_secret) , 
        $_POST['bank'], 
        $fast2pay->app_id, 
        $fast2pay->channel_id, 
        $this->get_return_url( $order ),
        $currency_text
        );

            
        $response = wp_remote_get( $payment_url );
 
        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
              $headers = $response['headers']; // array of http header lines
              $body    = json_decode($response['body']); // use the content
        }
          
        return array(
        "result" => "success",
        "redirect" => $body->payment_url,
        );
        
    }
} 
