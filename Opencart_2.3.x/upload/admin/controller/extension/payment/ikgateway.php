<?php
/* Создано в компании www.gateon.net
 * =================================================================
 * Интеркасса модуль OPENCART 2.3.x ПРИМЕЧАНИЕ ПО ИСПОЛЬЗОВАНИЮ
 * =================================================================
 *  Этот файл предназначен для Opencart 2.3.x
 *  www.gateon.net не гарантирует правильную работу этого расширения на любой другой
 *  версии Opencart, кроме Opencart 2.3.x
 *  данный продукт не поддерживает программное обеспечение для других
 *  версий Opencart.
 * =================================================================
*/
class ControllerExtensionPaymentIkgateway extends Controller {
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
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('checkout/success', '', 'SSL');
        
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->key = $this->config->get('ikgateway_sign_hash');

        $ik_option = array(
            'ik_am' => number_format($this->currency->format($order_info['total'], $this->config->get('ikgateway_currency'), $this->currency->getValue($this->config->get('ikgateway_currency')), FALSE), 2, '.', ''),
            'ik_pm_no' => $this->session->data['order_id'],
            'ik_desc' => "#".$this->session->data['order_id'],
            'ik_co_id' => $this->config->get('ikgateway_shop_id'),
            'ik_cur' => $this->config->get('ikgateway_currency')
        );

        $data['action'] = 'https://sci.interkassa.com';

        $data['ik_co_id'] = $ik_option['ik_co_id'];
        $data['ik_am'] = $ik_option['ik_am'];
        $data['ik_pm_no'] = $ik_option['ik_pm_no'];
        $data['ik_cur'] = $ik_option['ik_cur'];
        $data['ik_desc'] = $ik_option['ik_desc'];
        
        ksort($ik_option, SORT_STRING);
        array_push($ik_option, $this->key);
        $ik_sign = implode(':', $ik_option);
        
        $data['ik_sign'] = base64_encode(md5($ik_sign, true));

        unset($ik_option);

        $this->id = 'payment';
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/ikgateway.tpl'))
        {
            return $this->load->view($this->config->get('config_template') . '/template/extension/payment/ikgateway.tpl', $data);
        }
        else
        {
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

        if ($this->request->post['ik_inv_st']) { //ik_payment_state
            if ($this->order['order_status_id']){
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
        if(!$this->checkIP()){return false;}

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

    public function checkIP(){
        $ip_stack = array(
            'ip_begin'=>'151.80.190.97',
            'ip_end'=>'151.80.190.104'
        );

        if(!ip2long($_SERVER['REMOTE_ADDR'])>=ip2long($ip_stack['ip_begin']) && !ip2long($_SERVER['REMOTE_ADDR'])<=ip2long($ip_stack['ip_end'])){
            return false;
        }
        return true;
    }
}
?>
