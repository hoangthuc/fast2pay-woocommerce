<?php
$data = json_decode(file_get_contents('php://input'), true);
if( !isset( $data['transaction_id'] ) )exit(0);
$orders  = wc_get_orders( array(
    'limit'        => 1, // Query all orders
    'orderby'      => 'date',
    'order'        => 'DESC',
    'meta_query' => array(
        array(
            'key' => 'transaction_id',
            'compare' => '=',
            'value' => $data['transaction_id']
        )
    )
));
if( !isset($orders[0]->id) || $data['payment']['status'] != 'SUCCESS' ) exit(0);
$order = $orders[0];
$order_status = get_option( 'order_status', 'completed' );
$domain = 'fast2pay_payment';
$status = 'wc-' === substr( $order_status, 0, 3 ) ? substr( $order_status, 3 ) : $order_status;
// Set order status
$order->update_status( $status, __( 'Checkout with fast2pay payment. ', $domain ) );

// or call the Payment complete
// $order->payment_complete();

// Reduce stock levels
$order->reduce_order_stock();

// Return thankyou redirect
echo json_encode(
    array(
        'result'    => 'success',
        'data'  => $data
    )
);