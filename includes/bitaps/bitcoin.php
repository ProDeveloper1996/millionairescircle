<?php

class Bitcoin
{

    public function printForm($params = null)
    {
        if (empty($params)) return 'Error params';
        $address = $this->createAddress($params);
/*
        $html = "<br><b>Please send exactly (plus miner fee)" . $params['amount_BTC'] . " bitcoins (in one payment) to the address <font color='green'><b>" . $address . "</b></font><br><br><b><font color='red'>Important: If you send any other bitcoin amount, payment system will ignore it !</font></b>
        <br> Your payment will be confirmed within the next 10-30 min
        <br>If you have already made payment click on <button class='some_btn' id='checkPay' value='$address'>Check the Payment</button><div id='checkPayMess' style='color:#f00;'></div>";
*/
        //(plus miner fee) 
        $html = "
Please send exactly <b style='color: #f00;'>{$params['amount_BTC']}</b> bitcoins (in one payment) to the address<br />
<font color='green'><b>" . $address . "</b></font>
<br><br>
Important: If you send any other bitcoin amount, payment system will ignore it !<br />
Your payment will be confirmed within the next 10-30 min<br />
If you have already made payment click on<br />
<div id='checkPayMess' style='color:#f00;'></div>
<button class='btn btn-form' id='checkPay' value='$address'>Check the Payment</button>
<button type=\"submit\" class=\"btn btn-form\" onClick=\"window.location.href='payment_f.php'\"> Cancel </button>
<script type=\"text/javascript\">
    jQuery(document).ready(function ($) {
        $('#checkPay').click(function () {
            $.ajax({
                type: 'post',
                url: '/member/myaccount.php',
                data: {address:$(this).val(),checkPay:1},
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        $('#checkPayMess').html('Paid');
                        location.href = '/member/payment_res.php?res=ok';
                    }
                    if (data.status == 2) $('#checkPayMess').html('Payment was not made yet');
                    if (data.status == 3) $('#checkPayMess').html('Payment was not confirmed');
                    if (data.status == 4) $('#checkPayMess').html('You paid the amount less than needed. Please contact site administrator.');
                },
                error: function (xhr, str) {
                    console.error(xhr.responseText);
                    $('#checkPayMess').html('ERROR');
                }
            });
        });

    });
</script>
            ";

        return $html;
    }

    public function createAddress($params = null)
    {
        $transaction_id = md5(time());

        $payout_address = $params['account_id'];
        $confirmations = BTC_CONFIRM;
        $fee_level = BTC_FEE_LEVEL;
        $callback = urlencode($params['notify'].'?transaction_id='.$transaction_id);
        $url = "https://bitaps.com/api/create/payment/". $payout_address. "/" . $callback . "?confirmations=" . $confirmations . "&fee_level=" . $fee_level;
        $data = file_get_contents($url);
        $respond = json_decode($data,true);

        if ( empty($respond) ) exit('ERROR Bitcoin');

        $address = $respond["address"]; // Bitcoin address to receive payments
        $payment_code = $respond["payment_code"]; //Payment Code
        $invoice = $respond["invoice"]; // Invoice to view payments and transactions

        GLOBAL $db;

        $db->ExecuteSql ("Insert into `bitaps_payments` 
            (txID, address, m_level, member_id, `type`, txDate, amount, amountUSD, payment_code, invoice) Values 
            ('$transaction_id', '$address', '{$params['m_level']}', '{$params['member_id']}', '{$params['type']}', ".time().", '{$params['amount_BTC']}', '{$params['amount']}', '$payment_code', '$invoice' )
        ");
        return $address;
    }

    function setNotify($transaction_id, $transaction_hash, $confirmations, $payout_miner_fee, $payout_service_fee, $dataPost)
    {
        GLOBAL $db;

        $data = $db->GetEntry("Select * From bitaps_payments WHERE txID='$transaction_id'  ");
        if (empty($data['id'])) return false;

        $db->ExecuteSql ("UPDATE `bitaps_payments` SET txConfirmed=$confirmations, txCheckDate = ".time().", txhash = '$transaction_hash', payout_miner_fee='$payout_miner_fee', payout_service_fee='$payout_service_fee', dataPost='$dataPost'  WHERE id='{$data['id']}'
        ");
        return $data;
    }

    function checkPaid($address, $member_id)
    {
        GLOBAL $db;
        //$member_id = 71091;
        $checkPay = $db->GetEntry("Select txConfirmed, payout_miner_fee, dataPost, amount From `bitaps_payments` Where address='$address' and txConfirmed > 0 and member_id = $member_id ORDER BY id DESC LIMIT 1 ", null);
        if (!empty($checkPay['dataPost'])) $amountSent = json_decode($checkPay['dataPost'], true)['amount'];
        else $amountSent = 0;
        $status = 2;
        if ( (int)$checkPay['txConfirmed'] >= BTC_CONFIRM && !empty($checkPay['payout_miner_fee']) ) $status = 1;
        if ( (int)$checkPay['txConfirmed'] >= BTC_CONFIRM && $checkPay['payout_miner_fee']==0 ) $status = 3;
        if ( $status==1 && ($amountSent/100000000) < $checkPay['amount'] ) $status = 4;

        return json_encode(array('status'=>$status));
    }

}

?>