<?php

/* Создано в компании www.interkassa.com
 * =================================================================
 * Интеркасса модуль OPENCART 2.3.x ПРИМЕЧАНИЕ ПО ИСПОЛЬЗОВАНИЮ
 * =================================================================
 *  Этот файл предназначен для Opencart 2.3.x
 *  www.interkassa.comне гарантирует правильную работу этого расширения на любой другой
 *  версии Opencart, кроме Opencart 2.3.x
 *  данный продукт не поддерживает программное обеспечение для других
 *  версий Opencart.
 * =================================================================
*/

class ControllerExtensionPaymentIkgateway extends Controller
{
    private $order;
    private $log;
    private $key;
    private $shoputils = 2907;
    private static $LOG_OFF = 0;
    private static $LOG_SHORT = 1;
    private static $LOG_FULL = 2;


    public function index()
    {

        $this->language->load('extension/payment/ikgateway');
        $this->load->model('checkout/order');

        $data['text_confirm_title'] = $this->language->get('text_confirm_title');
        $data['text_select_payment_method'] = $this->language->get('text_select_payment_method');
        $data['text_select_currency'] = $this->language->get('text_select_currency');
        $data['text_press_pay'] = $this->language->get('text_press_pay');
        $data['pay_via'] = $this->language->get('pay_via');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('checkout/success', '', 'SSL');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->key = $this->config->get('ikgateway_sign_hash');

        $ik_option = array(
            'ik_am' => number_format($this->currency->format($order_info['total'], $this->config->get('ikgateway_currency'), $this->currency->getValue($this->config->get('ikgateway_currency')), FALSE), 2, '.', ''),
            'ik_pm_no' => $this->session->data['order_id'],
            'ik_desc' => "Оплата заказа #" . $this->session->data['order_id']. ' в магазине CSGOKnife.net',
            'ik_co_id' => $this->config->get('ikgateway_shop_id'),
            'ik_cur' => $this->config->get('ikgateway_currency'),
            'ik_ia_u' => $this->url->link('extension/payment/ikgateway/status', '', 'SSL'),
            'ik_fal_u' => $this->url->link('extension/payment/ikgateway/fail', '', 'SSL'),
            'ik_pnd_u' => $this->url->link('extension/payment/ikgateway/success', '', 'SSL'),
            'ik_suc_u' => $this->url->link('extension/payment/ikgateway/success', '', 'SSL')
        );

        $data['action'] = 'https://sci.interkassa.com';

        $data['ik_co_id'] = $ik_option['ik_co_id'];
        $data['ik_am'] = $ik_option['ik_am'];
        $data['ik_pm_no'] = $ik_option['ik_pm_no'];
        $data['ik_cur'] = $ik_option['ik_cur'];
        $data['ik_desc'] = $ik_option['ik_desc'];
        $data['ik_fal_u'] = $ik_option['ik_fal_u'];
        $data['ik_pnd_u'] = $ik_option['ik_pnd_u'];
        $data['ik_suc_u'] = $ik_option['ik_suc_u'];
        $data['ik_ia_u'] = $ik_option['ik_ia_u'];


        $data['ik_sign'] = $this->IkSignFormation($ik_option, $this->config->get('ikgateway_sign_hash'));
        unset($ik_option);
        $this->id = 'payment';


        //Новое АПИ
        $api_status = $this->config->get('ikgateway_api_status');
        if ($api_status == 1) {

            if ($this->request->server['HTTPS']) {
                $server = $this->config->get('config_ssl');
            } else {
                $server = $this->config->get('config_url');
            }

            $images = $server . 'image/extension/payment/ikgetaway/paysystems/';

            $data['api_status'] = $api_status;
            $data['ajax_url'] = $this->url->link('extension/payment/ikgateway/asyncSignFormation', '', 'SSL');
            $data['shop_cur'] = $this->config->get('ikgateway_currency');
            $data['images'] = $images;
            $data['payment_systems'] = $this->getIkPaymentSystems(
                $this->config->get('ikgateway_shop_id'),
                $this->config->get('ikgateway_api_id'),
                $this->config->get('ikgateway_api_key')
            );
        }


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/ikgateway.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/extension/payment/ikgateway.tpl', $data);
        } else {
            return $this->load->view('extension/payment/ikgateway.tpl', $data);
        }
    }

    public function status()
    {
        $this->logWrite('StatusURL: ', self::$LOG_FULL);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);

        if (!$this->validate(true)) {
            return;
        }
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory($this->request->post['ik_pm_no'], $this->config->get('ikgateway_order_status_id'));
        if ($this->request->post['ik_inv_st']) { //ik_payment_state
            if ($this->order['order_status_id']) {
                $this->model_checkout_order->update($this->order['order_id'],
                    $this->config->get('ikgateway_order_status_id'),
                    sprintf($this->language->get('text_comment'),
                        $this->request->post['ik_pw_via'],
                        $this->request->post['ik_am']
                    ),
                    true);
            } else {
                $this->model_checkout_order->confirm($this->order['order_id'],
                    $this->config->get('ikgateway_order_status_id'),
                    sprintf($this->language->get('text_comment'),
                        $this->request->post['ik_pw_via'],
                        $this->request->post['ik_am']
                    ));
            }

        }
        $this->sendOk();
    }

    public function success()
    {
        $this->logWrite('SuccessURL', self::$LOG_FULL);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);

        if ($this->validate(false)) {
            $this->load->model('checkout/order');

            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('ikgateway_order_status_id'));
            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        } else {
            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        }
        return true;
    }

    public function fail()
    {
        $this->logWrite('FailURL', self::$LOG_FULL);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);
        $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        return true;
    }

    private function validate($check_sign_hash = true)
    {
        if (!$this->checkIP()) {
            return false;
        }

        $this->load->model('checkout/order');

        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $this->sendForbidden($this->language->get('text_error_post'));
            return false;
        }

        if ($check_sign_hash && isset($sign_ik)) {
            $ik_sign_hash_array = $this->request->post;
            unset($ik_sign_hash_array['ik_sign']);
            ksort($ik_sign_hash_array, SORT_STRING);
            array_push($ik_sign_hash_array, $this->config->get('ikgateway_test_mode') ? $this->config->get('ikgateway_sign_test_key') : $this->config->get('ikgateway_sign_hash')); //$this->config->get('ikgateway_sign_hash');
            $ik_sign_hash_string = implode(':', $ik_sign_hash_array);
            $ik_sign_hash = base64_encode(md5($ik_sign_hash_string, true));
            if ($this->request->post['ik_sign'] != $ik_sign_hash) {  //ik_sign_hash
                $this->sendForbidden($this->language->get('text_error_ik_sign_hash'));

                $this->logWrite($ik_sign_hash . '=md5(' . $ik_sign_hash_string . ')', self::$LOG_SHORT);

                return false;
            }
        }

        $this->order = $this->model_checkout_order->getOrder($this->request->post['ik_pm_no']); //ik_payment_id

        if (!$this->order) {
            $this->sendForbidden(sprintf($this->language->get('text_error_order_not_found'), $this->request->post['ik_pm_no']));  //ik_payment_id
            return false;
        }

        return true;
    }

    public function view()
    {
        echo $this->shoputils;
    }

    private function sendForbidden($error)
    {
        $this->logWrite('ERROR: ' . $error, self::$LOG_SHORT);

        header('HTTP/1.1 403 Forbidden');

        echo "<html>
            <head>
             <title>403 Forbidden</title>
         </head>
         <body>
            <p>$error.</p>
        </body>
        </html>";
    }

    private function sendOk()
    {
        $this->logWrite('OK: ' . http_build_query($this->request->post, '', ','), self::$LOG_SHORT);

        header('HTTP/1.1 200 OK');

        echo "<html><head><title>200 OK</title></head></html>";
    }

    private function logWrite($message, $type)
    {
        switch ($this->config->get('ikgateway_log')) {
            case self::$LOG_OFF:
                return;
            case self::$LOG_SHORT:
                if ($type == self::$LOG_FULL) {
                    return;
                }
        }

        if (!$this->log) {
            $this->log = new Log('ikgateway.log');
        }
        $this->log->Write($message);
    }

    function checkIP()
    {
        $ip_stack = array(
            'ip_begin' => '151.80.190.97',
            'ip_end' => '151.80.190.104'
            );

        if (ip2long($_SERVER['REMOTE_ADDR']) < ip2long($ip_stack['ip_begin']) && ip2long($_SERVER['REMOTE_ADDR']) > ip2long($ip_stack['ip_end']) ) {
            die('Ты мошенник! Пшел вон отсюда!');
        }
        return true;
    }


    public function getIkPaymentSystems($ik_co_id, $ik_api_id, $ik_api_key)
    {
        $username = $ik_api_id;
        $password = $ik_api_key;
        $remote_url = 'https://api.interkassa.com/v1/paysystem-input-payway?checkoutId=' . $ik_co_id;

        // Create a stream
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Authorization: Basic " . base64_encode("$username:$password")
            )
        );

        $context = stream_context_create($opts);
        $file = file_get_contents($remote_url, false, $context);
        $json_data = json_decode($file);

        if($json_data->status != 'error'){
        $payment_systems = array();
        foreach ($json_data->data as $ps => $info) {
            $payment_system = $info->ser;
            if (!array_key_exists($payment_system, $payment_systems)) {
                $payment_systems[$payment_system] = array();
                foreach ($info->name as $name) {
                    if ($name->l == 'en') {
                        $payment_systems[$payment_system]['title'] = ucfirst($name->v);
                    }
                    $payment_systems[$payment_system]['name'][$name->l] = $name->v;

                }
            }
            $payment_systems[$payment_system]['currency'][strtoupper($info->curAls)] = $info->als;

        }
        return $payment_systems;
        }else{
            echo '<strong style="color:red;">API connection error!<br>'.$json_data->message.'</strong>';
        }
    }

    public function IkSignFormation($data, $secret_key)
    {

        if (!empty($data['ik_sign'])) unset($data['ik_sign']);

        $dataSet = array();
        foreach ($data as $key => $value) {
            if (!preg_match('/ik_/', $key)) continue;
            $dataSet[$key] = $value;
        }

        ksort($dataSet, SORT_STRING);
        array_push($dataSet, $secret_key);
        $arg = implode(':', $dataSet);
        $ik_sign = base64_encode(md5($arg, true));

        return $ik_sign;
    }

    public function asyncSignFormation()
    {
        $ik_sign = $this->IkSignFormation($_POST, $this->config->get('ikgateway_sign_hash'));
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($ik_sign));
    }
}

?>
