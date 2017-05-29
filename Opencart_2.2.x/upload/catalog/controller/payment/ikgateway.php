<?php
/**
 * @name Интеркасса 2.0
 * @description Модуль разработан в компании GateOn предназначен для CMS Opencart 2.0.x - 2.2.x
 * @author www.gateon.net
 * @email www@smartbyte.pro
 * @last_update 29.05.2017
 * @version 1.2
 */
class ControllerPaymentIkgateway extends Controller {
    private $order;
    private $log;
    private $shoputils = 2907;
    private static $LOG_OFF = 0;
    private static $LOG_SHORT = 1;
    private static $LOG_FULL = 2;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->language->load('payment/ikgateway');
    }

    public function index()
    {

        $data['text_confirm_title'] = $this->language->get('text_confirm_title');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('checkout/success', '', 'SSL');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['action'] = 'https://sci.interkassa.com';

        $form = array();

        $form['ik_co_id'] = $this->config->get('ikgateway_shop_id');
        $form['ik_am'] = number_format($this->currency->format($order_info['total'], $this->config->get('ikgateway_currency'), $this->currency->getValue($this->config->get('ikgateway_currency')), FALSE), 2, '.', '');
        $form['ik_cur'] = $this->config->get('ikgateway_currency');
        $form['ik_pm_no'] = $this->session->data['order_id'];
        $form['ik_desc'] = sprintf($this->language->get('text_ik_payment_desc'), $this->session->data['order_id']);
        $form['ik_loc'] = $this->language->get('code');
        $form['ik_suc_u']  = HTTP_SERVER  . 'index.php?route=payment/ikgateway/success';
        $form['ik_fal_u']     = HTTP_SERVER  . 'index.php?route=payment/ikgateway/fail';
        $form['ik_pnd_u']  = HTTP_SERVER  . 'index.php?route=payment/ikgateway/pending';
        $form['ik_ia_u']   = HTTP_SERVER  . 'index.php?route=payment/ikgateway/status';

        if($this->config->get('ikgateway_test_mode')){
            $form['ik_pw_via']   = 'test_interkassa_test_xts';
        }

        $form['ik_sign'] = $this->IkSignFormation($form, $this->config->get('ikgateway_sign_hash'));

        $data = array_merge($data,$form);

        $this->id = 'payment';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ikgateway.tpl'))
        {
            return $this->load->view($this->config->get('config_template') . '/template/payment/ikgateway.tpl', $data);
        }
        else
        {
            return $this->load->view('payment/ikgateway.tpl', $data);
        }
    }

    public function status()
    {
        $this->logWrite('StatusURL: ', self::$LOG_FULL);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);

        if ($this->validate(true)) {
            $this->load->model('checkout/order');

            $this->model_checkout_order->addOrderHistory($this->request->post['ik_pm_no'], $this->config->get('ikgateway_order_status_id'));
            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
            header('OK');
        }
    }

    public function success()
    {
        $this->logWrite('SuccessURL', self::$LOG_FULL);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);

        $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));

        return true;
    }

    public function pending()
    {
        $this->logWrite('PendingURL', self::$LOG_FULL);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory($this->request->post['ik_pm_no'], $this->config->get('ikgateway_pending_order_status_id'));
        $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));

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
        $this->logWrite('validation', self::$LOG_FULL);
        if (count($_POST) && $this->checkIP() && isset($_POST['ik_sign'])) {

            $this->logWrite('POST received, IP Ok', self::$LOG_FULL);

            if (isset($_POST['ik_pw_via']) && $_POST['ik_pw_via'] == 'test_interkassa_test_xts') {
                $secret_key = $this->config->get('ikgateway_sign_test_key');
            } else {
                $secret_key = $this->config->get('ikgateway_sign_hash');
            }


            if ($_POST['ik_sign'] == $this->IkSignFormation($_POST, $secret_key)) {
                $this->logWrite('Sign is ok', self::$LOG_FULL);
                return true;
            } else {
                $this->logWrite('Sign ne ok', self::$LOG_FULL);
                return false;
            }
        }else{
            $this->logWrite('IP ne Ok', self::$LOG_FULL);
            return false;

        }


    }

    public function view()
    {
        echo $this->shoputils;
    }

    private function sendForbidden($error)
    {
        $this->logWrite('ERROR: ' . $error, self::$LOG_SHORT);

        header('HTTP/1.1 403 Forbidden');
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

    public function checkIP()
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
}
?>
