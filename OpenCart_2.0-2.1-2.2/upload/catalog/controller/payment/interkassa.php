<?php

class ControllerPaymentInterkassa extends Controller
{
    private $order;
    private $log;
    private $data = array();
    private static $ikUrlSCI = 'https://sci.interkassa.com';
    private static $ikUrlAPI = 'https://api.interkassa.com/v1/';
    private static $LOG_OFF = 0;
    private static $LOG_SHORT = 1;
    private static $LOG_FULL = 2;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->language('payment/interkassa');
        $this->ip_stack = array(
            '151.80.190.97',
            '35.233.69.55'
        );
    }

    public function index()
    {
        $this->logWrite('index', self::$LOG_SHORT);
        $data = array();
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['action'] = self::$ikUrlSCI;
        if ($this->config->get('interkassa_api_enable')) {
            $data['text'] = $this->load->language('payment/interkassa');
            $data['payment_systems'] = $this->getIkPaymentSystems(
                $this->config->get('interkassa_cashbox_id'),
                $this->config->get('interkassa_api_id'),
                $this->config->get('interkassa_api_key')
            );
        }
        $data['formData'] = $this->makeForm();
        return $this->load->view('default/template/payment/interkassa.tpl', $data);
    }

    public function callback()
    {
        $this->logWrite('callback StatusURL: ', self::$LOG_SHORT);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);

        if ($this->checkIP() && $this->request->server['REQUEST_METHOD'] == 'POST') {
            $ik_response = $this->request->post;
            $order_id = (int)$ik_response['ik_pm_no'];
            $this->load->model('checkout/order');
            $order = $this->model_checkout_order->getOrder($order_id);
            if (!$order) {
                $this->sendForbidden(sprintf($this->language->get('text_error_order_not_found'), $order_id));
                return false;
            }
            if ($this->config->get('interkassa_test_mode'))
                $key = $this->config->get('interkassa_test_key');
            else
                $key = $this->config->get('interkassa_secret_key');
            $ik_sign = $this->IkSignFormation($ik_response, $key);
            $this->logWrite('  $key:' . var_export($key, true), self::$LOG_FULL);
            $this->logWrite('  $ik_sign:' . var_export($ik_sign, true), self::$LOG_FULL);
            $this->logWrite('  post-ik_sign:' . var_export($this->request->post['ik_sign'], true), self::$LOG_FULL);

            if ($this->request->post['ik_sign'] == $ik_sign && ($ik_response['ik_co_id'] == $this->config->get('interkassa_cashbox_id'))) {
                if ($ik_response['ik_inv_st'] == 'success') {
                    $this->model_checkout_order->addOrderHistory($order_id,
                        $this->config->get('interkassa_order_status_success'),
                        sprintf($this->language->get('text_comment'), $this->request->post['ik_pw_via'], $this->request->post['ik_am']),
                        true);
                    echo 'OK';
                    header("HTTP/1.1 200 OK");
                } elseif ($ik_response['ik_inv_st'] == 'fail') {
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('interkassa_order_status_fail'));
                }
            } else {
                $this->sendForbidden($this->language->get('text_error_ik_sign_hash'));
            }
        }
    }

    public function success()
    {
        $this->logWrite('SuccessURL', self::$LOG_SHORT);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);
        if (!isset($this->session->data['order_id'])) {
            $this->session->data['order_id'] = $this->order['order_id']; //Добавляем в сессию номер заказа на случай, если в checkout/success на экран пользователю выводится номер заказа
        }
        $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
    }

    public function fail()
    {
        $this->logWrite('FailURL', self::$LOG_SHORT);
        $this->logWrite('  POST:' . var_export($this->request->post, true), self::$LOG_FULL);
        $this->logWrite('  GET:' . var_export($this->request->get, true), self::$LOG_FULL);

        if (!empty($this->session->data['order_id']) && $this->config->get('interkassa_order_status_confirm') && ($this->session->data['payment_method']['code'] == 'interkassa')) {
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('interkassa_order_status_fail'),'',true);
            $this->response->redirect($this->url->link('checkout/failure', '', 'SSL'));
        }
    }

    public function confirm()
    {
        $this->logWrite('confirm ' . $this->session->data['order_id'], self::$LOG_SHORT);

        if ($this->request->post['flag'] == 1) {
            $this->logWrite('order_id ' . $this->session->data['order_id'], self::$LOG_SHORT);
            $this->logWrite('flag ', self::$LOG_SHORT);

            if (!empty($this->session->data['order_id']) && $this->config->get('interkassa_order_status_confirm') && ($this->session->data['payment_method']['code'] == 'interkassa')) {
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('interkassa_order_status_confirm'),'',true);
                $json = '';
            }
        } else $json = 'error';
        $this->response->setOutput(json_encode($json));
    }

    protected function makeForm($order_id = false)
    {
        $this->load->model('checkout/order');
        if (!$order_id) {
            if (isset($this->session->data['order_id'])) {
                $order_id = $this->session->data['order_id'];
            } else {
                $this->logWrite('Error: Unsupported Checkout Extension', self::$LOG_SHORT);
                die($this->language->get('error_fail_checkout_extension'));
            }
        }
        $order_info = $this->model_checkout_order->getOrder($order_id);
        $ikCurrencyCode = $this->config->get('interkassa_currency');
        if (!$this->currency->has($ikCurrencyCode)) {
            die(sprintf('Currency code (for code: %s) not found', $ikCurrencyCode));
        }
        
        $interkassa_payment_amount = number_format($this->currency->convert($order_info['total'], $this->config->get('config_currency'), $ikCurrencyCode), 2, '.', '');
        $interkassa_cashbox_id = $this->config->get('interkassa_cashbox_id');
        $interkassa_success_url = HTTPS_SERVER . 'index.php?route=payment/interkassa/success';
        $interkassa_fail_url = HTTPS_SERVER . 'index.php?route=payment/interkassa/fail';
        $interkassa_pending_url = HTTPS_SERVER . 'index.php?route=payment/interkassa/success';
        $interkassa_callback_url = HTTPS_SERVER . 'index.php?route=payment/interkassa/callback';

        $params = array(
            'ik_am' => $interkassa_payment_amount,
            'ik_co_id' => $interkassa_cashbox_id,
            'ik_cur' => $ikCurrencyCode,
            'ik_desc' => "#$order_id",
            'ik_pm_no' => $order_info['order_id'],
            'ik_ia_u' => $interkassa_callback_url,
            'ik_suc_u' => $interkassa_success_url,
            'ik_fal_u' => $interkassa_fail_url,
            'ik_pnd_u' => $interkassa_pending_url,
        );

        if ($this->config->get('interkassa_test_mode'))
            $params['ik_pw_via'] = 'test_interkassa_test_xts';

        $params['ik_sign'] = $this->IkSignFormation($params, $this->config->get('interkassa_secret_key'));

        $this->logWrite('Make payment form: ', self::$LOG_SHORT);
        $this->logWrite('  DATA: ' . var_export($params, true), self::$LOG_FULL);

        return $params;
    }

    protected function sendForbidden($error)
    {
        $this->logWrite('ERROR: ' . $error, self::$LOG_SHORT);
        $this->response->addHeader('HTTP/1.1 403 Forbidden');
        //header('HTTP/1.1 403 Forbidden');
        echo "<html>
                <head>
                   <title>403 Forbidden</title>
                </head>
                <body>
                    <p>$error.</p>
                </body>
        </html>";
    }

    public function selectPaySys()
    {
        header("Pragma: no-cache");
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        header("Content-type: text/plain");
        $request = $_POST;
        if (isset($_POST['ik_act']) && $_POST['ik_act'] == 'process') {
            $request['ik_sign'] = $this->IkSignFormation($request, $this->config->get('interkassa_secret_key'));
            $data = $this->getAnswerFromAPI($request);
        } else
            $data = $this->IkSignFormation($request, $this->config->get('interkassa_secret_key'));
        echo $data;
        exit;
    }

    public function getAnswerFromAPI($data)
    {
        $ch = curl_init(self::$ikUrlSCI);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        return $result;
    }

    private function IkSignFormation($data, $secret_key)
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
        $this->logWrite('IkSignFormation: ' . $ik_sign . ' = md5(' . $arg . ')', self::$LOG_SHORT);
        return $ik_sign;
    }

    public function getIkPaymentSystems($ik_cashbox_id, $ik_api_id, $ik_api_key)
    {
        $this->logWrite('  getIkPaymentSystems', self::$LOG_FULL);

        $username = $ik_api_id;
        $password = $ik_api_key;
        $remote_url = self::$ikUrlAPI . 'paysystem-input-payway?checkoutId=' . $ik_cashbox_id;

        $businessAcc = $this->getIkBusinessAcc($username, $password);
        $this->logWrite('  $businessAcc=' . $businessAcc, self::$LOG_FULL);

        $ikHeaders = [];
//        $ikHeaders[] = "GET";
        $ikHeaders[] = "Authorization: Basic " . base64_encode("$username:$password");
        if (!empty($businessAcc)) {
            $ikHeaders[] = "Ik-Api-Account-Id: " . $businessAcc;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $ikHeaders);
        $response = curl_exec($ch);
        $json_data = json_decode($response);

        // Create a stream
//        $opts = array(
//            'http' => array(
//                'method' => "GET",
//                'header' => "Authorization: Basic " . base64_encode("$username:$password")
//            )
//        );
//        $context = stream_context_create($opts);
//        $response = file_get_contents($remote_url, false, $context);
//        $json_data = json_decode($response);
//        file_put_contents(dirname(DIR_APPLICATION) . '/temp.log', json_encode(array(
//            '$remote_url' => $remote_url,
//            '$username' => $username,
//            '$password' => $password,
//            '$opts' => $opts,
//        ), JSON_PRETTY_PRINT));

        if (empty($response))
            return '<strong style="color:red;">Error!!! System response empty!</strong>';
        if ($json_data->status != 'error') {
            $payment_systems = array();
            if (!empty($json_data->data)) {
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
            }
            return !empty($payment_systems) ? $payment_systems : '<strong style="color:red;">API connection error or system response empty!</strong>';

        } else {
            if (!empty($json_data->message))
                return '<strong style="color:red;">API connection error!<br>' . $json_data->message . '</strong>';
            else
                return '<strong style="color:red;">API connection error or system response empty!</strong>';
        }
    }

    public function getIkBusinessAcc($username = '', $password = '')
    {
        $tmpLocationFile = __DIR__ . '/tmpLocalStorageBusinessAcc.ini';
        $dataBusinessAcc = function_exists('file_get_contents') && file_exists($tmpLocationFile) ? file_get_contents($tmpLocationFile) : '{}';
        $dataBusinessAcc = json_decode($dataBusinessAcc, 1);
        $businessAcc = !empty($dataBusinessAcc['businessAcc']) && is_string($dataBusinessAcc['businessAcc']) ? trim($dataBusinessAcc['businessAcc']) : '';
        if (empty($businessAcc) || sha1($username . $password) !== $dataBusinessAcc['hash']) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, self::$ikUrlAPI . 'account');
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Basic " . base64_encode("$username:$password")]);
            $response = curl_exec($curl);
            if (!empty($response['data'])) {
                foreach ($response['data'] as $id => $data) {
                    if ($data['tp'] == 'b') {
                        $businessAcc = $id;
                        break;
                    }
                }
            }
            if (function_exists('file_put_contents')) {
                $updData = [
                    'businessAcc' => $businessAcc,
                    'hash' => sha1($username . $password)
                ];
                file_put_contents($tmpLocationFile, json_encode($updData, JSON_PRETTY_PRINT));
            }
            return $businessAcc;
        }
        return $dataBusinessAcc['businessAcc'];
    }

    public function checkIP()
    {
        $this->logWrite('  server: ' . var_export($this->request->server['REMOTE_ADDR'], true), self::$LOG_FULL);

        $ip_callback = ip2long($this->request->server['REMOTE_ADDR']) ? ip2long($this->request->server['REMOTE_ADDR']) : !ip2long($this->request->server['REMOTE_ADDR']);

        if ($ip_callback == ip2long($this->ip_stack[0]) || $ip_callback == ip2long($this->ip_stack[1])) {
            return true;
        } else {
            $this->sendForbidden(sprintf($this->language->get('text_error_allowed_range_ip'), $this->request->server['REMOTE_ADDR']));
            return false;
        }
    }
    
    protected function logWrite($message, $type)
    {
        switch ($this->config->get('shoputils_ik_log')) {
            case self::$LOG_OFF:
                return;
            case self::$LOG_SHORT:
                if ($type == self::$LOG_FULL) {
                    return;
                }
        }
        if (!$this->log) {
            $this->log = new Log($this->config->get('shoputils_ik_log_filename'));
        }
        $this->log->Write($message);
    }
}

?>