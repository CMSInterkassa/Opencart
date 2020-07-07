<?php

class ControllerPaymentInterkassa extends Controller
{
    public function index()
    {
        $this->language->load('payment/interkassa');
        $this->load->model('checkout/order');

        $this->data['text_confirm_title'] = $this->language->get('text_confirm_title');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['continue'] = $this->url->link('checkout/success', '', 'SSL');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $ik_cur = $order_info['currency_code'];
        if (strtoupper($ik_cur) == 'RUR') {
            $ik_cur = 'RUB';
        }
        $order_total = $order_info['total'];
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
        $fields = array(
            'ik_co_id' => $this->config->get('interkassa_id'),
            'ik_am' => $this->currency->format($order_total, $ik_cur, $order_info['currency_value'], FALSE),
            'ik_pm_no' => $this->session->data['order_id'],
            'ik_desc' => sprintf($this->language->get('text_interkassa_payment_desc'), $this->session->data['order_id']),
            'ik_cur' => $ik_cur,
            'ik_ia_u' => $protocol . $_SERVER["HTTP_HOST"] . '/index.php?route=payment/interkassa/callback',
            'ik_suc_u' => $protocol . $_SERVER["HTTP_HOST"] . '/index.php?route=payment/interkassa/notification',
            'ik_fal_u' => $protocol . $_SERVER["HTTP_HOST"] . '/index.php?route=payment/interkassa/notification',
            'ik_pnd_u' => $protocol . $_SERVER["HTTP_HOST"] . '/index.php?route=payment/interkassa/notification'
        );
        $fields['ik_sign'] = $this->IkSignFormation($fields, $this->config->get('interkassa_secret_key'));

        $this->data['action'] = 'https://sci.interkassa.com/';

        $this->data['ik_co_id'] = $fields['ik_co_id'];
        $this->data['ik_am'] = $fields['ik_am'];
        $this->data['ik_pm_no'] = $fields['ik_pm_no'];
        $this->data['ik_desc'] = $fields['ik_desc'];
        $this->data['ik_cur'] = $fields['ik_cur'];
        $this->data['ik_ia_u'] = $fields['ik_ia_u'];
        $this->data['ik_suc_u'] = $fields['ik_suc_u'];
        $this->data['ik_fal_u'] = $fields['ik_fal_u'];
        $this->data['ik_pnd_u'] = $fields['ik_pnd_u'];
        $this->data['ik_sign'] = $fields['ik_sign'];

        if ($this->request->get['route'] != 'checkout/guest_step_3') {
            $this->data['cancel_return'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
        } else {
            $this->data['cancel_return'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
        }

        if ($this->request->get['route'] != 'checkout/guest_step_3') {
            $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
        } else {
            $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
        }

        $this->id = 'payment';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/interkassa.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/interkassa.tpl';
        } else {
            $this->template = 'default/template/payment/interkassa.tpl';
        }

        $this->render();
    }

    public function callback()
    {
        if ($this->validate()) {
            if ($this->request->post['ik_inv_st'] == 'success') {
                $order_id = $this->request->post['ik_pm_no'];
                $this->load->model('checkout/order');
                $this->model_checkout_order->confirm($order_id, $this->config->get('interkassa_order_status_id'), $this->language->get('text_title'));
				
				header('HTTP/1.1 200 OK');
				exit;
            }
        }
		
		$this->sendForbidden('Bad Request!!!');
    }
	
	private function sendForbidden($error)
    {
        header('HTTP/1.1 403 Forbidden');

        echo "<html>
                <head>
                   <title>403 Forbidden</title>
                </head>
                <body>
                    <p>$error.</p>
                </body>
        </html>";
		exit;
    }

    public function notification()
    {
        if ($this->request->post['ik_inv_st'] == 'canceled') {
            $this->load->model('checkout/order');
            $this->model_checkout_order->confirm($this->request->post['ik_pm_no'], $this->config->get('interkassa_order_failed_status_id'), $this->language->get('text_title'));
            $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
        }elseif($this->request->post['ik_inv_st'] == 'waitAccept'){
            $this->load->model('checkout/order');
            $this->model_checkout_order->confirm($this->request->post['ik_pm_no'], true, $this->language->get('text_title'));
            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        } else {
            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        }
        return true;
    }

    private function validate()
    {
        if (!empty($this->request->post)) {
            if ($this->request->post['ik_co_id'] == $this->config->get('interkassa_id') && !empty($this->request->post['ik_sign']) && $this->checkIP()
            ) {
                $request = $this->request->post;
                $request_sign = $request['ik_sign'];
                if ($request['ik_pw_via'] == 'test_interkassa_test_xts') {
                    $key = $this->config->get('interkassa_test_key');
                } else {
                    $key = $this->config->get('interkassa_secret_key');
                }
                if ($request_sign == $this->IkSignFormation($request, $key)) {
                    return true;
                }
            }
        }
        return false;
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
            '151.80.190.97',
            '35.233.69.55'
        );
        
		$ip = !empty($_SERVER['HTTP_CF_CONNECTING_IP'])? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
        $ip_callback = ip2long($ip) ? ip2long($ip) : !ip2long($ip);

        if ($ip_callback == ip2long($ip_stack[0]) || $ip_callback == ip2long($ip_stack[1])) {
            return true;
        } else {
			$this->log->write('Interkassa IP ne ok:'.$_SERVER['REMOTE_ADDR']);
            return false;
        }
    }

}

?>
