<?php
$banks = [
    ["symbol"=>"VCB", "name"=>"Joint Stock Commercial Bank for Foreign Trade of Vietnam","img"=>"assets/images/vietcom_bank_logo.png"],
    ["symbol"=>"TECHCOMBANK", "name"=>"Vietnam Technological and Commercial Joint Stock Bank","img"=>"assets/images/techcombank_bank_logo.png"],
    ["symbol"=>"TPBANK", "name"=>"TienPhong Commercial Joint Stock Bank","img"=>"assets/images/Tp_bank_logo.png"],
    ["symbol"=>"VIETINBANK", "name"=>"Vietnam Joint Stock Commercial Bank of Industry and Trade","img"=>"assets/images/viettin_bank_logo.png"],
    ["symbol"=>"VIB", "name"=>"Vietnam International Commercial Joint Stock Bank","img"=>"assets/images/vib_bank_logo.png"],
    ["symbol"=>"DAB", "name"=>"DONG A Commercial Joint Stock Bank","img"=>"assets/images/donga_bank_logo.png"],
    ["symbol"=>"HDBANK", "name"=>"Ho Chi Minh City Development Joint Stock Commercial Bank","img"=>"assets/images/hd_bank_logo.png"],
    ["symbol"=>"MB", "name"=>"Military Commercial Joint Stock Bank","img"=>"assets/images/mb_bank_logo.png"],
    ["symbol"=>"VIETABANK", "name"=>"Vietnam Asia Commercial Joint","img"=>"assets/images/viet_a_bank_logo.png"],
    ["symbol"=>"MARITIMEBANK", "name"=>"Vietnam Maritime Commercial Join Stock Bank","img"=>"assets/images/Maritime_bank_logo.png"],
    ["symbol"=>"EXIMBANK", "name"=>"Vietnam Export Import Commercial Joint Stock","img"=>"assets/images/exim_bank_logo.png"],
    ["symbol"=>"SHB", "name"=>"Saigon-Hanoi Commercial Joint Stock Bank","img"=>"assets/images/shb_bank_logo.png"],
    ["symbol"=>"VPBANK", "name"=>"Vietnam Prosperity Joint Stock Commercial Bank","img"=>"assets/images/vp_bank_logo.png"],
    ["symbol"=>"ABBANK", "name"=>"An Binh Commercial Joint Stock Bank","img"=>"assets/images/ab_bank_logo.png"],
    ["symbol"=>"SACOMBANK", "name"=>"Saigon Thuong Tin Commercial Joint Stock Bank","img"=>"assets/images/sacombank_bank_logo.png"],
    ["symbol"=>"NAMA", "name"=>"Nam A Commercial Joint Stock Bank","img"=>"assets/images/nama_bank_logo.png"],
    ["symbol"=>"OCEANBANK", "name"=>"Ocean Commercial One Member Limited Liability Bank","img"=>"assets/images/ocean_bank_logo.png"],
    ["symbol"=>"BIDV", "name"=>"Joint Stock Commercial Bank for Investment and Development of Vietnam","img"=>"assets/images/bidv_bank_logo.png"],
    ["symbol"=>"SEABANK", "name"=>"Southeast Asia Commercial Joint Stock Bank","img"=>"assets/images/sea_bank_logo.png"],
    ["symbol"=>"BACA", "name"=>"BAC A Commercial Joint Stock Bank","img"=>"assets/images/baca_bank_logo.png"],
    ["symbol"=>"NAVIBANK", "name"=>"Navi Bank","img"=>"assets/images/navi_bank_logo.png"],
    ["symbol"=>"AGRIBANK", "name"=>"Vietnam Bank for Agriculture and Rural Development","img"=>"assets/images/agribank_bank_logo.png"],
    ["symbol"=>"SAIGONBANK", "name"=>"Saigon Bank for Industry and Trade","img"=>"assets/images/saigon_bank_logo.png"],
    ["symbol"=>"PVBANK", "name"=>"Vietnam Public Joint Stock Commercial Bank","img"=>"assets/images/pv_bank_logo.png"],
    ["symbol"=>"ACB", "name"=>"Asia Commercial Joint Stock Bank","img"=>"assets/images/acb_bank_logo.png"],
    ["symbol"=>"LPB", "name"=>"Lien Viet Post Joint Stock Commercial Bank","img"=>"assets/images/LVPB_bank_logo.png"],
    ["symbol"=>"BVBANK", "name"=>"Bao Viet Joint Stock commercial Bank","img"=>"assets/images/baoviet_bank_logo.png"],
    ["symbol"=>"OCB", "name"=>"Orient Commercial Joint Stock Bank","img"=>"assets/images/ocb_bank_logo.png"],
    ["symbol"=>"KIENLONGBANK", "name"=>"Kien Long Commercial Joint Stock Bank","img"=>"assets/images/kienlong_bank_logo.png"],
    ["symbol"=>"VRB", "name"=>"Vietnam Russia Joint Venture Bank","img"=>"assets/images/vrb_bank_logo.png"],
    ["symbol"=>"NCB", "name"=>"National Citizen Commercial Joint Stock Bank","img"=>"assets/images/ncb_bank_logo.png"],
    ["symbol"=>"PGBANK", "name"=>"Petrolimex Group Commercial Joint Stock Bank","img"=>"assets/images/pg_bank_logo.png"],
    ["symbol"=>"GPBANK", "name"=>"Global Petro Sole Member Limited Commercial Bank","img"=>"assets/images/gb_bank_logo.png"],
    ["symbol"=>"WOORIBANK", "name"=>"Woori Bank Vietnam Limited","img"=>"assets/images/woori_bank_logo.png"],
    ["symbol"=>"VIETCAPITALBANK", "name"=>"Viet Capital Commercial Joint Stock Bank","img"=>"assets/images/vietcapital_bank_logo.png"],
    ["symbol"=>"VISA", "name"=>"Visa","img"=>"assets/images/visa_logo.png"],
    ["symbol"=>"MASTERCARD", "name"=>"Mastercard","img"=>"assets/images/mastercard_logo.png"],
];
 $fast2pay = WC()->payment_gateways()->payment_gateways()['fast2pay'];
if( isset( $_POST['payee_bank'] ) ):
$fee = 5500;
$transaction_id =  uniqid();
$payee_pay_fee = true;
$amount = ($_POST['amount'])?$_POST['amount']:0;
$amount = round($amount - $fee);
 $payment_url = wp_sprintf( __('https://bank.fast2pays.com/v1/partner/transfer?currency=VND&app_id=%s&channel_id=%s&transaction_id=%s&amount=%d&payee_bank=%s&payee_account=%s&payee_name=%s&hash=%s&message=1&payee_account_type=CARD_NUMBER') ,
 $fast2pay->app_id, 
 $fast2pay->channel_id,
 $transaction_id,
 $amount,
 $_POST['payee_bank'],
 $_POST['payee_account'],
 $_POST['payee_name'],
 sha1($fast2pay->app_id.":".$transaction_id.":".$amount.":".$_POST['payee_bank'].":".$_POST['payee_account'].":CARD_NUMBER:".$_POST['payee_name'].":0:".$fast2pay->channel_id."::VND:1:".$fast2pay->app_secret)
);   

$response = wp_remote_get( $payment_url );
 if ( is_array( $response ) && ! is_wp_error( $response ) ) {
       $headers = $response['headers']; // array of http header lines
       $body    = json_decode($response['body']); // use the content
       if( isset($body->error_code) && $body->error_code )echo '<ul class="woocommerce-error" role="alert"><li>'.$body->message.'</li></ul>';
       if( isset($body->status) && $body->payment->status == "SUCCESS" )echo '<ul class="woocommerce-message" role="alert"><li>'.$body->payment->status.'</li></ul>';
 }

endif;

?>
<div id="fast2pay_cashout">
    <div class="list-bank">
        <?php foreach($banks as $bank): ?>
            <a title="<?= $bank['name'] ?>" data-symbol="<?= $bank['symbol'] ?>" onclick="selectFast2Pay(this,'#fast2pay_cashout','payee_bank')" ><img src ="<?= F2P_URL.'/'.$bank['img'] ?>" /></a>
            <?php endforeach; ?> 
        </div>
    <form method="post">
        <input type="hidden" name="payee_bank" value=""/>
        <p class="form-row address-field validate-required form-row-wide">
            <label>Card number</label>
            <span class="woocommerce-input-wrapper">
                <input type="text" class="input-text " name="payee_account" placeholder="The payee's card number" value="<?= isset($_POST['payee_account'])?$_POST['payee_account']:'' ?>">
            </span>
        </p>
        <p class="form-row address-field validate-required form-row-wide">
            <label>Payee Name</label>
            <span class="woocommerce-input-wrapper">
                <input type="text" class="input-text " name="payee_name" placeholder="Enter name of payee" value="<?= isset($_POST['payee_name'])?$_POST['payee_name']:'' ?>">
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

