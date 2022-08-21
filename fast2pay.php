<?php
/*
Plugin Name: Fast2pay Payment Gateway
Description: Fast2pay payment gateway example
*/
define('DOMAIN_F2P','fast2pay_payment');
define('F2P_URL',plugin_dir_url( __FILE__ ));
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action('plugins_loaded', 'init_fast2pay_gateway_class');
function init_fast2pay_gateway_class(){

   include_once('setting.php');
}

add_filter( 'woocommerce_payment_gateways', 'add_fast2pay_gateway_class' );
function add_fast2pay_gateway_class( $methods ) {
    $methods[] = 'WC_Gateway_Fast2Pay'; 
    return $methods;
}

add_action('woocommerce_checkout_process', 'process_fast2pay_payment');
function process_fast2pay_payment(){

    if($_POST['payment_method'] != 'fast2pay')
        return;
        
     if( !isset($_POST['bank']) || empty($_POST['bank'])  )
         wc_add_notice( __( 'Please add bank', DOMAIN_F2P ), 'error' );  
}

/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'fast2pay_payment_update_order_meta' );
function fast2pay_payment_update_order_meta( $order_id ) {

    if($_POST['payment_method'] != 'fast2pay')
        return;
    update_post_meta( $order_id, 'bank', $_POST['bank'] );
    update_post_meta( $order_id, 'transaction_id', $_POST['transaction_id'] );

}

/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'fast2pay_checkout_field_display_admin_order_meta', 10, 1 );
function fast2pay_checkout_field_display_admin_order_meta($order){
    $method = get_post_meta( $order->id, '_payment_method', true );
    if($method != 'fast2pay')
        return;
}
// URL webhook fast2pay
add_action('template_include', 'fast2pay_webhookwebhook_slug');
function fast2pay_webhookwebhook_slug($template) {
    $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
    $fast2pay = WC()->payment_gateways()->payment_gateways()['fast2pay'];
   if($url_path == $fast2pay->fast2pay_webhook)return plugin_dir_path( __FILE__ ) . 'templates/webhook.php';
    return $template;
}

function fast2pay_library_scripts() {    
    wp_enqueue_style( 'fast2pay-style', F2P_URL . '/assets/css/fast2pay.css', array(), '1.1', 'all');
    wp_enqueue_script( 'fast2pay-script', F2P_URL . '/assets/js/fast2pay.js', array ( ), 1.1, true);
  }
  add_action( 'wp_enqueue_scripts', 'fast2pay_library_scripts' );