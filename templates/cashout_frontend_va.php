<?php
$banks = [
    ["symbol"=>"PVBANK", "name"=>"Vietnam Public Joint Stock Commercial Bank","img"=>"assets/images/pv_bank_logo.png"],
    ["symbol"=>"WOORIBANK", "name"=>"Woori Bank Vietnam Limited","img"=>"assets/images/woori_bank_logo.png"],
    ["symbol"=>"VIETCAPITALBANK", "name"=>"Viet Capital Commercial Joint Stock Bank","img"=>"assets/images/vietcapital_bank_logo.png"]
];
 $fast2pay = WC()->payment_gateways()->payment_gateways()['fast2pay'];
if( isset( $_POST['bank'] ) ):
$fee_percent = 0;
$transaction_id =  uniqid();
$payee_pay_fee = true;
$amount = ($_POST['amount'])?$_POST['amount']:0;
$amount = $amount / (1 - $fee_percent);
 $payment_url = wp_sprintf( __('https://bank.fast2pays.com/v1/partner/getVirtualAccount?app_id=%s&channel_id=%s&user_id=%s&transaction_id=%s&amount=%d&bank=%s&account_name=%s&hash=%s&callback_uri='.site_url()) ,
 $fast2pay->app_id, 
 $fast2pay->channel_id,
1,
 $transaction_id,
 $amount,
 $_POST['bank'],
 $_POST['account_name'],
 sha1($fast2pay->app_id.":".$fast2pay->channel_id.":".$transaction_id.":".$amount.":".$_POST['bank']."::".$_POST['account_name'].":".$fast2pay->app_secret)
);   

$response = wp_remote_get( $payment_url );
 if ( is_array( $response ) && ! is_wp_error( $response ) ) {
       $headers = $response['headers']; // array of http header lines
       $body    = json_decode($response['body']); // use the content
       if( isset($body->error_code) && $body->error_code )echo '<ul class="woocommerce-error" role="alert"><li>'.$body->message.'</li></ul>';
       if( isset($body->status) && $body->payment_url ){
        echo '<ul class="woocommerce-message" role="alert"><li>'.$body->bank_account->bank_name.'</li></ul>';
        echo '<meta http-equiv="refresh" content="0;url='.$body->payment_url.'">';
       }
 }
endif;
?>
<div id="fast2pay_cashout">
    <div class="list-bank">
        <?php foreach($banks as $bank): ?>
            <a title="<?= $bank['name'] ?>" data-symbol="<?= $bank['symbol'] ?>" onclick="selectFast2Pay(this,'#fast2pay_cashout','bank')" ><img src ="<?= F2P_URL.'/'.$bank['img'] ?>" /></a>
            <?php endforeach; ?> 
        </div>
    <form method="post">
        <input type="hidden" name="bank" value=""/>
        <p class="form-row address-field validate-required form-row-wide">
            <label>Account Name</label>
            <span class="woocommerce-input-wrapper">
                <input type="text" class="input-text " name="account_name" placeholder="Enter name of payee" value="<?= isset($_POST['account_name'])?$_POST['account_name']:'' ?>">
            </span>
        </p>
        <p class="form-row address-field validate-required form-row-wide">
            <label>Amount</label>
            <span class="woocommerce-input-wrapper">
                <input type="number" class="input-text " name="amount" placeholder="" value="<?= isset($_POST['amount'])?$_POST['amount']:'' ?>">
            </span>
        </p>
        <p>
        <button type="submit" class="button alt">Submit</button>
        </p>
</form>
</div>

