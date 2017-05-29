<?php
$_['heading_title']               = 'Interkassa 2.0';

// Tab
$_['tab_log']                     = 'Log';

// Text
$_['text_payment']                = 'Payment';
$_['text_success']                = 'Settings saved!';
$_['text_shoputils_ik']           = '<a onclick="window.open(\'http://www.interkassa.com/\');"><img src="../image/payment/shoputils_ik.jpg" alt="Interkassa.com" title="Interkassa.com"/></a>';
$_['text_order_status_cart']      = 'Cart';
$_['text_ik_log_off']             = 'Log disabled';
$_['text_ik_log_short']           = 'Partly (Only results)';
$_['text_ik_log_full']            = 'Full (All response)';

$_['text_ik_parameters']          = 'Interkassa Payment settings';
$_['text_ik_urls']                = 'In tab "Settings -> Interface" allow override all fields. Type : POST';


// Entry
$_['entry_ik_log']                = 'Log:';
$_['entry_ik_log_help']           = 'Interkassa Log: system/logs/shoputils_ik.txt';
$_['entry_ik_shop_id']            = 'Purse ID:';
$_['entry_ik_shop_id_help']       = 'Purse Id that you create in your Interkassa account';
$_['entry_ik_sign_hash']          = 'Secret key:';
$_['entry_ik_sign_hash_help']     = 'Settings -> Safety -> Secret key.';
$_['entry_ik_sign_test_key']      = 'Test key:';
$_['entry_ik_sign_test_key_help'] = 'Settings -> Safety -> Test key.';

$_['entry_ik_currency']           = 'Currency:';
$_['entry_ik_currency_help']      = 'Currency that will be send to Interkassa ( RUB, UAH, EUR, USD).';
$_['entry_ik_test_mode']          = 'Test mode:';
$_['entry_ik_test_mode_help']     = 'Test mode allowed to check module performance. You cannot choose another payment system except Test Payment System';
$_['entry_log_file']              = 'Log file:';
$_['entry_log_file_help']         = 'Last %d log rows.';
$_['entry_status']                = 'Status:';
$_['entry_order_status']          = 'Order status after success payment:';
$_['entry_pending_order_status']          = 'Order status after panding payment:';
$_['entry_geo_zone']              = 'Geo zone:';
$_['entry_sort_order']            = 'Sort order:';



// Error
$_['error_permission']            = 'You do not have permissions!';
$_['error_ik_shop_id']            = 'Purse ID - must be filled up';
$_['error_ik_sign_hash']          = 'Secret key - must be filled up';
$_['error_ik_sign_test_key']      = 'Test key - must be filled up';
?>