<?php
var_dump($_REQUEST);
?>
<br><br><br><br><br><br>
<form action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post'> 
    <input type='hidden' name='cmd' value='_xclick'> 
    <input type='hidden' name='business' value='runmlm.com@gmail.com'>
    <input type='hidden' name='amount' value='1'> 
    <input type='hidden' name='item_number' value='member_id'> 
    <input type='hidden' name='custom' value='level_id'> 
    <input type='hidden' name='item_name' value='product'> 
    <input type='hidden' name='currency_code' value='USD'> 
    <input type='hidden' name='quantity' value='1'> 
    <input type='hidden' name='undefined_quantity' value='0'> 
    <input type='hidden' name='no_shipping' value='1'> 
    <input type='hidden' name='rm' value='2'> 
    <input type='hidden' name='notify_url' value='http://<?=$_SERVER['HTTP_HOST']?>/notify/paypal_test.php'>
    <input type='hidden' name='return' value='http://<?=$_SERVER['HTTP_HOST']?>/test_paypal.php'>
    <input type='hidden' name='cancel_return' value='http://<?=$_SERVER['HTTP_HOST']?>/test_paypal.php'>
    <button type='submit' class='btn btn-form'><i class='fa fa-check'></i> test Pay </button> 
</form>