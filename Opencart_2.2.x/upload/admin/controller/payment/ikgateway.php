<?php
/**
 * @name Интеркасса 2.0
 * @description Модуль разработан в компании GateOn предназначен для CMS Opencart 2.0.x - 2.2.x
 * @author interkassa.com
 * @email support@interkassa.com
 * @last_update 29.05.2017
 * @version 1.2
 */

class ControllerPaymentIkgateway extends Controller {
    const MAX_LAST_LOG_LINES = 500;
    private $error = array();

    public function index() {
        $this->load->language('payment/ikgateway');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->load->model('setting/setting');

            $this->model_setting_setting->editSetting('ikgateway', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->makeUrl('extension/payment'));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_pending_order_status'] = $this->language->get('entry_pending_order_status');
        $data['entry_ik_log'] = $this->language->get('entry_ik_log');
        $data['entry_ik_log_help'] = $this->language->get('entry_ik_log_help');
        $data['entry_ik_shop_id'] = $this->language->get('entry_ik_shop_id');
        $data['entry_ik_shop_id_help'] = $this->language->get('entry_ik_shop_id_help');
        $data['entry_ik_sign_hash'] = $this->language->get('entry_ik_sign_hash');
        $data['entry_ik_sign_hash_help'] = $this->language->get('entry_ik_sign_hash_help');
        $data['entry_ik_sign_test_key'] = $this->language->get('entry_ik_sign_test_key');
        $data['entry_ik_sign_test_key_help'] = $this->language->get('entry_ik_sign_test_key_help');
        $data['entry_ik_currency'] = $this->language->get('entry_ik_currency');
        $data['entry_ik_currency_help'] = $this->language->get('entry_ik_currency_help');
        $data['entry_ik_test_mode'] = $this->language->get('entry_ik_test_mode');
        $data['entry_ik_test_mode_help'] = $this->language->get('entry_ik_test_mode_help');
        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_log'] = $this->language->get('tab_log');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_ik_urls'] = $this->language->get('text_ik_urls');
        $data['text_ik_parameters'] = $this->language->get('text_ik_parameters');
        $data['entry_log_file'] = $this->language->get('entry_log_file');
        $data['entry_log_file_help']       = sprintf($this->language->get('entry_log_file_help'), self::MAX_LAST_LOG_LINES);
        $data['action']                    = $this->makeUrl('payment/ikgateway');
        $data['cancel']                    = $this->makeUrl('extension/payment');
        $data['log_lines']                 = $this->readLastLines(DIR_LOGS . 'ikgateway.log', self::MAX_LAST_LOG_LINES);

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['ik_shop_id'])) {
            $data['error_ik_shop_id'] = $this->error['ik_shop_id'];
        } else {
            $data['error_ik_shop_id'] = '';
        }

        if (isset($this->error['ik_sign_hash'])) {
            $data['error_ik_sign_hash'] = $this->error['ik_sign_hash'];
        } else {
            $data['error_ik_sign_hash'] = '';
        }

        if (isset($this->error['ik_sign_test_key'])) {
            $data['error_ik_sign_test_key'] = $this->error['ik_sign_test_key'];
        } else {
            $data['error_ik_sign_test_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'href'      => $this->makeUrl('common/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $data['breadcrumbs'][] = array(
            'href'      => $this->makeUrl('extension/payment'),
            'text'      => $this->language->get('text_payment'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'href'      => $this->makeUrl('payment/ikgateway'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        //переопределяю некотр. перемен., не работает метод _updateData:(
        if (isset($this->request->post['ikgateway_sort_order'])) {
            $data['ikgateway_sort_order'] = $this->request->post['ikgateway_sort_order'];
        } elseif($this->config->get('ikgateway_sort_order')) {
            $data['ikgateway_sort_order'] = $this->config->get('ikgateway_sort_order');
        } else {
            $data['ikgateway_sort_order'] = "0";
        }

        if (isset($this->request->post['ikgateway_shop_id'])) {
            $data['ikgateway_shop_id'] = $this->request->post['ikgateway_shop_id'];
        } elseif($this->config->get('ikgateway_shop_id')) {
            $data['ikgateway_shop_id'] = $this->config->get('ikgateway_shop_id');
        } else {
            $data['ikgateway_shop_id']= "";
        }

        if (isset($this->request->post['ikgateway_sign_hash'])) {
            $data['ikgateway_sign_hash'] = $this->request->post['ikgateway_sign_hash'];
        } elseif($this->config->get('ikgateway_sign_hash')) {
            $data['ikgateway_sign_hash'] = $this->config->get('ikgateway_sign_hash');
        } else {
            $data['ikgateway_sign_hash']= "";
        }

        if (isset($this->request->post['ikgateway_sign_test_key'])) {
            $data['ikgateway_sign_test_key'] = $this->request->post['ikgateway_sign_test_key'];
        } elseif($this->config->get('ikgateway_sign_test_key')) {
            $data['ikgateway_sign_test_key'] = $this->config->get('ikgateway_sign_test_key');
        } else {
            $data['ikgateway_sign_test_key']= "";
        }

        if (isset($this->request->post['ikgateway_currency'])) {
            $data['ikgateway_currency'] = $this->request->post['ikgateway_currency'];
        } elseif($this->config->get('ikgateway_currency')) {
            $data['ikgateway_currency'] = $this->config->get('ikgateway_currency');
        } else {
            $data['ikgateway_currency']= "";
        }

        if (isset($this->request->post['ikgateway_test_mode'])) {
            $data['ikgateway_test_mode'] = $this->request->post['ikgateway_test_mode'];
        } elseif($this->config->get('ikgateway_test_mode')) {
            $data['ikgateway_test_mode'] = $this->config->get('ikgateway_test_mode');
        } else {
            $data['ikgateway_test_mode']= "";
        }

        if (isset($this->request->post['ikgateway_order_status_id'])) {
            $data['ikgateway_order_status_id'] = $this->request->post['ikgateway_order_status_id'];
        } elseif($this->config->get('ikgateway_order_status_id')) {
            $data['ikgateway_order_status_id'] = $this->config->get('ikgateway_order_status_id');
        } else {
            $data['ikgateway_order_status_id']= "";
        }

        if (isset($this->request->post['ikgateway_pending_order_status_id'])) {
            $data['ikgateway_pending_order_status_id'] = $this->request->post['ikgateway_pending_order_status_id'];
        } elseif($this->config->get('ikgateway_pending_order_status_id')) {
            $data['ikgateway_pending_order_status_id'] = $this->config->get('ikgateway_pending_order_status_id');
        } else {
            $data['ikgateway_pending_order_status_id']= "";
        }

        if (isset($this->request->post['ikgateway_geo_zone_id'])) {
            $data['ikgateway_geo_zone_id'] = $this->request->post['ikgateway_geo_zone_id'];
        } elseif($this->config->get('ikgateway_geo_zone_id')) {
            $data['ikgateway_geo_zone_id'] = $this->config->get('ikgateway_geo_zone_id');
        } else {
            $data['ikgateway_geo_zone_id']= "";
        }

        if (isset($this->request->post['ikgateway_status'])) {
            $data['ikgateway_status'] = $this->request->post['ikgateway_status'];
        } elseif($this->config->get('ikgateway_status')) {
            $data['ikgateway_status'] = $this->config->get('ikgateway_status');
        } else {
            $data['ikgateway_status']= "";
        }

        if (isset($this->request->post['ikgateway_log'])) {
            $data['ikgateway_log'] = $this->request->post['ikgateway_log'];
        } elseif($this->config->get('ikgateway_log')) {
            $data['ikgateway_log'] = $this->config->get('ikgateway_log');
        } else {
            $data['ikgateway_log']= "";
        }

        //конец переопределения

        $data['logs'] = array(
            '0' => $this->language->get('text_ik_log_off'),
            '1' => $this->language->get('text_ik_log_short'),
            '2' => $this->language->get('text_ik_log_full'),
        );

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = array_merge(
            array(0 => array(
                'name' => $this->language->get('text_order_status_cart')
            )),
            $this->model_localisation_order_status->getOrderStatuses()
        );

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('setting/store');
        $stores = $this->model_setting_store->getStores();
        foreach ($stores as $store){
            $data['stores'][] = $store['url'];
        }
        $data['stores'][] = $this->config->get('config_url');

        $this->load->model('localisation/currency');

        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/ikgateway.tpl', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/ikgateway')) {
            $this->error['warning'] = $this->language->get('error_permission');
        } else {
            if (!isset($this->request->post['ikgateway_sign_hash']) || !$this->request->post['ikgateway_sign_hash']) {
                $this->error['warning'] = $this->language->get('error_ik_sign_hash');
            }
            if (!isset($this->request->post['ikgateway_sign_test_key']) || !$this->request->post['ikgateway_sign_test_key']) {
                $this->error['warning'] = $this->language->get('error_ik_sign_test_key');
            }
            if (!isset($this->request->post['ikgateway_shop_id']) || !$this->request->post['ikgateway_shop_id']) {
                $this->error['warning'] = $this->language->get('error_ik_shop_id');
            }
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function readLastLines($filename, $lines) {
        if (!is_file($filename)) {
            return array();
        }
        $handle = @fopen($filename, "r");
        if (!$handle) {
            return array();
        }
        $linecounter = $lines;
        $pos = -1;
        $beginning = false;
        $text = array();

        while ($linecounter > 0) {
            $t = " ";

            while ($t != "\n") {
                /* if fseek() returns -1 we need to break the cycle*/
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }

            $linecounter--;

            if ($beginning) {
                rewind($handle);
            }

            $text[$lines - $linecounter - 1] = fgets($handle);

            if ($beginning) {
                break;
            }
        }
        fclose($handle);

        return array_reverse($text);
    }

    function makeUrl($route, $url = '')
    {
        return str_replace('&amp;', '&', $this->url->link($route, $url.'&token=' . $this->session->data['token'], 'SSL'));
    }
}
