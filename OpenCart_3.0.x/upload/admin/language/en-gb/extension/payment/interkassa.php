<?php
$_['heading_title']           = 'Interkassa 2.0';

// Tab 
$_['tab_settings']            = 'Setting payments';
$_['tab_log']                 = 'Log';
$_['tab_information']         = 'Information';

// Text
$_['text_extension']          = 'Extensions';
$_['text_success']            = 'Module settings "%s" have been updated!';
$_['text_clear_log_success']  = 'The request log of the module has been successfully cleared!';
$_['text_confirm']            = 'You can not undo the log! Are you sure you want to do this?';
$_['text_interkassa']         = '<a style="cursor: pointer;" onclick="window.open(\'https://new.interkassa.com\');"><img src="view/image/payment/interkassa.gif" alt="new.interkassa.com" title="Interkassa"/></a>';
$_['text_log_off']            = 'Off';
$_['text_log_short']          = 'Partial (Transaction results only)';
$_['text_log_full']           = 'Full (All Requests)';

$_['text_currency_auto']      = 'Определять автоматически';
$_['text_currency_rub']       = 'Russian rubles (RUB-643)';
$_['text_currency_uah']       = 'Ukrainian Hryvnia (UAH-980)';
$_['text_currency_usd']       = 'U.S. dollars (USD-840)';
$_['text_currency_eur']       = 'Euro (EUR-978)';
$_['text_info']               =  'After <a href="https://www.interkassa.com/registration" target="_blank"> registration </a> of the online store account in "INTERKASSA"
you need to set up a store to accept payments. You can do this in the <a href="https://www.interkassa.com/login" target="_blank"> personal account </a> under <a href = "https://www.interkassa.com/account / checkout "target =" _ blank "> My Cashier </a> by clicking on the" Settings "
opposite his store. The data you need to enter in the settings of the store:';
$_['text_info_content']       = 'In the section "My cash desks -> Cashier settings -> Payment settings" you need to set the following values: <br />
<b> "Check uniqueness of payments" - incl. <br />
<b> "Transfer the description to the payment system" - on </b> <br />
"Payment time of payment (in minutes)" - incl. "allow override in query" </b> <br /> <br />
In the section "My cash desks -> Cashier settings -> Interface" it is necessary to set the following values ​​everywhere: <br />
<b> "Request Type" - "POST" <br />
everywhere to disable "Allow override in request" </b> <br /> <br />
In the section "My cash desks -> Cashier settings -> Security" you need to set the following values: <br />
<b> "Signature Algorithm" - "MD5" <br />
"Check signature in form of payment request" - on </b>';

// Entry
$_['entry_status']                  = 'Status';
$_['entry_geo_zone']                = 'Geographical area';
$_['entry_sort_order']              = 'Sorting order';
$_['entry_minimal_order']           = 'Minimum Order Value';
$_['entry_order_confirm_status']    = 'Order status after confirmation';
$_['entry_order_status']            = 'Order status after a successful payment';
$_['entry_order_fail_status']       = 'Order status after a failed payment';
$_['entry_title']                   = 'Name';

$_['entry_cashbox_id']  = 'Cashier ID';
$_['entry_secret_key']  = 'Secret key';
$_['entry_test_key']    = 'Test key';
$_['entry_test_mode']   = 'Test mode';
$_['entry_api_enable']  = 'API mode';
$_['entry_api_id']      = 'API identifier';
$_['entry_api_key']     = 'API key';
$_['entry_currency']    = 'Store currency';

$_['entry_log']                     = 'Log';
$_['entry_log_file']                = 'Log file';

$_['entry_success_url']     = 'Successful payment URL';
$_['entry_fail_url']        = 'Unsuccessful payment URL';
$_['entry_callback_url']    = 'Interaction URL';

// Help
$_['help_order_confirm_status'] = 'If you click on the "Confirm" button at the last stage of placing your order, the order will be set to the selected status';
$_['help_order_status']         = 'After successful payment of the order, the order will be set to the selected status.';
$_['help_minimal_order']        = 'If the order amount is less than the specified amount, and the amount is not empty and is not zero, this payment method will not be available when placing an order. <br /> For example: 190.90';
$_['help_order_fail_status']    = 'If the Intercass returns the buyer after a failed payment, the order will be set to the selected status.';
$_['help_title']                = 'Name of the payment method on the order registration page';

$_['help_cashbox_id']   = 'The identifier of the cash register registered in the INTERKASSA system. You can find it in the section "My cash desks". Example: 529a6e08bf4efcae2d1b8488 ';
$_['help_secret_key']   = 'Used by the SCI (Intercassa) when creating a digital signature. Must match the secret key in the section "My cashbox -> Cashier settings -> Security -> Secret key. ';
$_['help_test_key']     = 'Used by the SCI (Intercassa) when creating a digital signature if payment was made through a test payment system. Should coincide with the test key in the section "My cashbox -> Cashier settings -> Security -> Test key. ';
$_['help_test_mode']    = 'In the test mode, you can check the settings of the module and the Intercasse through the test currency of the Intercasse by selecting "Test Payment System" when paying. In this case, a test key is used to sign the digital signature (electronic digital signature). To accept real payments - the test mode must be turned off. ';
$_['help_currency']     = 'The currency in which the store transfers the payment amount to the Intercass payment gateway. The Interbank Service accepts the following currencies: RUB, UAH, EUR, USD. ';
$_['help_log_file']     = 'Last% d lines from the log file.';
$_['help_log']          = 'The log of requests from the Intercash is stored in the file: /system/storage/logs/%s';

// Error
$_['error_permission']   = 'You do not have permission to manage the module "%s"!';
$_['error_clear_log']    = 'You do not have permission to clear the module log!';
$_['error_form']         = 'You must fill in the field "%s" (tab "%s")!';